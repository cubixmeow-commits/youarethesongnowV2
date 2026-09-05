<?php
declare(strict_types=1);
require __DIR__.'/../src/Mapping/Geoapify.php';
use FirstRuck\Mapping\Geoapify;
function verify(bool $v,string $label):void{if(!$v)throw new RuntimeException($label);echo "PASS $label\n";}
foreach ([[null,1],[91,0],[0,181],['bad',0]] as [$lat,$lon]){try{Geoapify::coordinate($lat,$lon);throw new RuntimeException('Accepted invalid coordinates');}catch(InvalidArgumentException){}}
verify(true,'invalid coordinates rejected');
$calls=[];$client=new Geoapify('fixture',function($host,$path,$query)use(&$calls){$calls[]=$query;if(str_contains($path,'geocode'))return ['results'=>[['formatted'=>'Example park','lat'=>45,'lon'=>-122]]];return ['features'=>[['properties'=>['distance'=>800,'time'=>600],'geometry'=>['type'=>'LineString','coordinates'=>[[-122,45],[-122.001,45.001],[-122,45]]]]]];});
verify($client->search('Example park')[0]['latitude']===45.0,'normalizes place search');
$routes=$client->routes(45,-122,15,'out-back');verify(count($routes)===1,'duplicate candidate geometry removed');verify(count($calls)===4,'at most three route calls');verify($routes[0]['verified']===false,'geometry is not a suitability guarantee');verify(count($routes[0]['unknowns'])>=5,'unknown suitability fields explicit');verify($calls[1]['mode']==='walk','beginner uses pedestrian not advanced hiking profile');
$loopCalls=[];$loopClient=new Geoapify('fixture',function($host,$path,$query)use(&$loopCalls){$loopCalls[]=$query;return ['features'=>[['properties'=>['distance'=>700,'time'=>540],'geometry'=>['type'=>'MultiLineString','coordinates'=>[[[-122,45],[-122.001,45.001]],[[-122.001,45.001],[-122.002,45]],[[-122.002,45],[-122,45]]]]]]];});
$loopRoutes=$loopClient->routes(45,-122,10,'short-loop');verify(count($loopRoutes)===1,'short-loop candidates use a circuit-sized radius');
$outPoint=explode('|',$calls[1]['waypoints'])[1];$loopPoint=explode('|',$loopCalls[0]['waypoints'])[1];
[$outLat,$outLon]=array_map('floatval',explode(',',$outPoint));[$loopLat,$loopLon]=array_map('floatval',explode(',',$loopPoint));
verify(hypot($loopLat-45,$loopLon+122)<hypot($outLat-45,$outLon+122),'circuit waypoint is closer than out-and-back waypoint');
$tooLong=new Geoapify('fixture',fn()=>['features'=>[['properties'=>['distance'=>6000,'time'=>6000],'geometry'=>['type'=>'LineString','coordinates'=>[]]]]]);
verify($tooLong->routes(45,-122,10,'out-back')===[],'rejects overlong candidates');
$namedCalls=[];$namedClient=new Geoapify('fixture',function($host,$path,$query)use(&$namedCalls){$namedCalls[]=[$path,$query];if($path==='/v1/geocode/reverse')return ['results'=>[['city'=>'Goleta','state_code'=>'CA','postcode'=>'93117','country'=>'United States']]];if($path==='/v1/geocode/search')return ['results'=>[['formatted'=>'Lake Los Carneros, Goleta','lat'=>34.44,'lon'=>-119.84]]];return ['features'=>[['properties'=>['distance'=>800,'time'=>600],'geometry'=>['type'=>'LineString','coordinates'=>[[-119.84,34.44],[-119.841,34.441],[-119.84,34.44]]]]]];});
verify($namedClient->reverseArea(34.43,-119.86)==='Goleta, CA, 93117, United States','reverse lookup returns only a generalized area');
$named=$namedClient->namedRoutes([['name'=>'Lake Los Carneros','locality'=>'Goleta, California','kind'=>'park-loop','discoveryRank'=>1]],34.43,-119.86,15,'out-back',[['title'=>'City source','url'=>'https://example.org/walk']]);
verify(count($named)===1&&$named[0]['discoveryMode']==='gemini-search','grounded named place becomes a mapped candidate');
verify(str_starts_with($named[0]['name'],'Walking candidate near'),'candidate does not claim official trail geometry');
verify($named[0]['distanceFromSearchMeters']>0,'distance from searched area is calculated server-side');
verify(($namedCalls[1][1]['bias']??'')==='proximity:-119.86,34.43','named geocoding is biased to the searched area');
$mismatch=new Geoapify('fixture',fn($host,$path,$query)=>$path==='/v1/geocode/search'?['results'=>[['formatted'=>'Unrelated Community Center','lat'=>34.44,'lon'=>-119.84]]]:[]);
verify($mismatch->namedRoutes([['name'=>'Lake Los Carneros','locality'=>'Goleta']],34.43,-119.86,15,'out-back')===[],'similarly located but mismatched place names are rejected');
