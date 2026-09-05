<?php
declare(strict_types=1);
namespace FirstRuck\Mapping;

final class Geoapify
{
    public function __construct(private string $key, private ?\Closure $transport = null) {}

    public function search(string $text): array
    {
        $json=$this->get('api.geoapify.com','/v1/geocode/search',['text'=>$text,'limit'=>5,'format'=>'json']);
        $places=[];
        foreach ($json['results']??[] as $p) if (isset($p['lat'],$p['lon'])) {
            $places[]=['label'=>(string)($p['formatted']??$p['name']??'Search result'),'latitude'=>(float)$p['lat'],'longitude'=>(float)$p['lon']];
        }
        return $places;
    }

    /** Returns a city-level label so exact device coordinates never need to reach an LLM. */
    public function reverseArea(float $lat, float $lon): string
    {
        [$lat,$lon]=self::coordinate($lat,$lon);
        $json=$this->get('api.geoapify.com','/v1/geocode/reverse',['lat'=>$lat,'lon'=>$lon,'limit'=>1,'format'=>'json']);
        $place=$json['results'][0]??[];
        $parts=array_values(array_unique(array_filter([
            (string)($place['city']??$place['town']??$place['village']??$place['county']??''),
            (string)($place['state_code']??$place['state']??''),
            (string)($place['postcode']??''),
            (string)($place['country']??''),
        ],static fn(string $value):bool=>$value!=='')));
        return implode(', ',array_slice($parts,0,4));
    }

    public static function coordinate(mixed $lat, mixed $lon): array
    {
        if (!is_numeric($lat)||!is_numeric($lon)||!is_finite((float)$lat)||!is_finite((float)$lon)||abs((float)$lat)>85||abs((float)$lon)>180) throw new \InvalidArgumentException('Choose a valid map location.');
        return [(float)$lat,(float)$lon];
    }

    /** Bounded out-and-back candidates. Source-derived geometry; suitability remains unknown. */
    public function routes(float $lat,float $lon,int $minutes,string $shape,array $bearings=[25,145,265]): array
    {
        [$lat,$lon]=self::coordinate($lat,$lon);
        $target=max(10,min(30,$minutes))*60;
        $targetDistance=$target*0.85;
        // Out-and-back covers about two radii. The triangular circuit below
        // covers about 3.64 radii (r + the 110-degree chord + r).
        $radius=$targetDistance/($shape==='short-loop'?3.64:2);
        $routes=[]; $seen=[];
        foreach (array_slice($bearings,0,3) as $index=>$bearing) {
            if(!is_numeric($bearing))continue;
            $bearing=(float)$bearing;
            $a=deg2rad($bearing);
            $endLat=$lat+cos($a)*$radius/111320;
            $endLon=$lon+sin($a)*$radius/(111320*max(.1,cos(deg2rad($lat))));
            $waypoints="$lat,$lon|$endLat,$endLon|$lat,$lon";
            if ($shape==='short-loop') {
                $b=deg2rad($bearing+110);
                $lat2=$lat+cos($b)*$radius/111320;
                $lon2=$lon+sin($b)*$radius/(111320*max(.1,cos(deg2rad($lat))));
                $waypoints="$lat,$lon|$endLat,$endLon|$lat2,$lon2|$lat,$lon";
            }
            try {
                $json=$this->get('api.geoapify.com','/v1/routing',['waypoints'=>$waypoints,'mode'=>'walk','format'=>'geojson']);
                $f=$json['features'][0]??null; $p=$f['properties']??[];$distance=(float)($p['distance']??0);$duration=(float)($p['time']??0);
                if (!$f||$distance<=0||$duration<=0||$duration>$target*1.15||$distance>$target*1.4) continue;
                $geometry=$f['geometry']??null;
                if (!is_array($geometry)||!in_array($geometry['type']??'', ['LineString','MultiLineString'],true)) continue;
                $id='geo-'.substr(hash('sha256',json_encode($geometry)),0,16);if(isset($seen[$id]))continue;$seen[$id]=true;
                $routes[]=['id'=>$id,'name'=>($shape==='short-loop'?'Custom walking circuit':'Custom out-and-back').' '.($index+1),
                    'geometry'=>$geometry,'distanceMeters'=>$distance,'durationSeconds'=>$duration,'distanceLabel'=>number_format($distance/1000,2).' km',
                    'terrain'=>'Surface and hill suitability not verified','shape'=>$shape==='short-loop'?'Circuit candidate':'Out and back','shapeKey'=>$shape,
                    'reasons'=>['Calculated on the pedestrian network.','Within the preview’s estimated time filter.'],
                    'unknowns'=>['current access','closures','sidewalks and crossings','surface','hill suitability','weather'],
                    'source'=>'https://www.geoapify.com/routing-api/','checkedAt'=>time(),'isDemo'=>false,'verified'=>false];
            } catch (\Throwable) { /* One unroutable bearing does not discard other candidates. */ }
        }
        return $routes;
    }

    /**
     * Resolves grounded named-place leads, then calculates one pedestrian
     * candidate near each place. This does not claim to reproduce an official
     * trail alignment.
     */
    public function namedRoutes(array $walks,float $areaLat,float $areaLon,int $minutes,string $shape,array $sources=[]): array
    {
        [$areaLat,$areaLon]=self::coordinate($areaLat,$areaLon);
        $routes=[];
        foreach(array_slice($walks,0,3) as $index=>$walk){
            if(!is_array($walk))continue;
            $name=trim((string)($walk['name']??''));
            $locality=trim((string)($walk['locality']??''));
            if($name===''||strlen($name)>120||strlen($locality)>120)continue;
            try{
                $json=$this->get('api.geoapify.com','/v1/geocode/search',[
                    'text'=>trim($name.' '.$locality),'limit'=>1,'format'=>'json','bias'=>'proximity:'.$areaLon.','.$areaLat,
                ]);
                $place=$json['results'][0]??null;
                if(!is_array($place)||!isset($place['lat'],$place['lon']))continue;
                $resolvedLabel=(string)($place['formatted']??$place['name']??'');
                if(!self::nameMatches($name,$resolvedLabel))continue;
                [$lat,$lon]=self::coordinate($place['lat'],$place['lon']);
                $fromSearch=(int)round(self::distanceMeters($areaLat,$areaLon,$lat,$lon));
                if($fromSearch>25000)continue;
                $candidate=$this->routes($lat,$lon,$minutes,$shape,[25+($index*120)])[0]??null;
                if(!is_array($candidate))continue;
                $candidate['name']='Walking candidate near '.$name;
                $candidate['startLabel']=$resolvedLabel!==''?$resolvedLabel:$name;
                $candidate['discoveryMode']='gemini-search';
                $candidate['discoveryRank']=max(1,(int)($walk['discoveryRank']??$index+1));
                $candidate['discoveryKind']=(string)($walk['kind']??'walking-area');
                $candidate['distanceFromSearchMeters']=$fromSearch;
                $candidate['discoverySources']=array_values(array_slice($sources,0,5));
                $routes[]=$candidate;
            }catch(\Throwable){/* A failed lead must not block the remaining places. */}
        }
        return $routes;
    }

    private static function distanceMeters(float $lat1,float $lon1,float $lat2,float $lon2): float
    {
        $earth=6371000;
        $dLat=deg2rad($lat2-$lat1);$dLon=deg2rad($lon2-$lon1);
        $a=sin($dLat/2)**2+cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLon/2)**2;
        return $earth*2*atan2(sqrt($a),sqrt(max(0,1-$a)));
    }

    private static function nameMatches(string $requested,string $resolved): bool
    {
        $normalize=static fn(string $value):array=>array_values(array_filter(
            preg_split('/[^\pL\pN]+/u',mb_strtolower($value))?:[],
            static fn(string $token):bool=>mb_strlen($token)>=4&&!in_array($token,['park','trail','walk','lake','loop','path'],true)
        ));
        $tokens=$normalize($requested);
        return $tokens!==[]&&array_intersect($tokens,$normalize($resolved))!==[];
    }

    private function get(string $host,string $path,array $query): array
    {
        $query['apiKey']=$this->key;
        if ($this->transport) return ($this->transport)($host,$path,$query);
        $ch=curl_init('https://'.$host.$path.'?'.http_build_query($query));
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>8,CURLOPT_FOLLOWLOCATION=>false]);
        $raw=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE);curl_close($ch);
        if(!is_string($raw)||strlen($raw)>2000000||$status!==200)throw new \RuntimeException('Mapping is temporarily unavailable.');
        return json_decode($raw,true,64,JSON_THROW_ON_ERROR);
    }
}
