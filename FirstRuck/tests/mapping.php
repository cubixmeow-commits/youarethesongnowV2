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
