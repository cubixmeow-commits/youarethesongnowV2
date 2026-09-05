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
$tooLong=new Geoapify('fixture',fn()=>['features'=>[['properties'=>['distance'=>6000,'time'=>6000],'geometry'=>['type'=>'LineString','coordinates'=>[]]]]]);
verify($tooLong->routes(45,-122,10,'out-back')===[],'rejects overlong candidates');
