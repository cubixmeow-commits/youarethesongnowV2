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

    public static function coordinate(mixed $lat, mixed $lon): array
    {
        if (!is_numeric($lat)||!is_numeric($lon)||!is_finite((float)$lat)||!is_finite((float)$lon)||abs((float)$lat)>85||abs((float)$lon)>180) throw new \InvalidArgumentException('Choose a valid map location.');
        return [(float)$lat,(float)$lon];
    }

    /** Bounded out-and-back candidates. Source-derived geometry; suitability remains unknown. */
    public function routes(float $lat,float $lon,int $minutes,string $shape): array
    {
        [$lat,$lon]=self::coordinate($lat,$lon);
        $target=max(10,min(30,$minutes))*60; $radius=$target*0.85/2; $routes=[]; $seen=[];
        foreach ([25,145,265] as $index=>$bearing) {
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
                if (!$f||$distance<=0||$duration<=0||$duration>$target||$distance>$target*1.4) continue;
                $geometry=$f['geometry']??null;
                if (!is_array($geometry)||!in_array($geometry['type']??'', ['LineString','MultiLineString'],true)) continue;
                $id='geo-'.substr(hash('sha256',json_encode($geometry)),0,16);if(isset($seen[$id]))continue;$seen[$id]=true;
                $routes[]=['id'=>$id,'name'=>($shape==='short-loop'?'Custom walking circuit':'Custom out-and-back').' '.($index+1),
                    'geometry'=>$geometry,'distanceMeters'=>$distance,'durationSeconds'=>$duration,'distanceLabel'=>number_format($distance/1000,2).' km',
                    'terrain'=>'Surface and hill suitability not verified','shape'=>$shape==='short-loop'?'Circuit candidate':'Out and back',
                    'reasons'=>['Calculated on the pedestrian network.','Within the preview’s estimated time filter.'],
                    'unknowns'=>['current access','closures','sidewalks and crossings','surface','hill suitability','weather'],
                    'source'=>'https://www.geoapify.com/routing-api/','checkedAt'=>time(),'isDemo'=>false,'verified'=>false];
            } catch (\Throwable) { /* One unroutable bearing does not discard other candidates. */ }
        }
        return $routes;
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
