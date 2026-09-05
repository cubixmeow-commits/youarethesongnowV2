<?php
declare(strict_types=1);
require __DIR__ . '/../src/Coaching/RouteCoach.php';
use FirstRuck\Coaching\RouteCoach;
function check(bool $condition, string $name): void {if (!$condition) throw new RuntimeException($name); echo "PASS $name\n";}
$route=['id'=>'park','verified'=>true,'eligible'=>true,'source'=>'https://example.org/source','checkedAt'=>time(),'reasonCodes'=>['surface_match']];
$good=['routes'=>[['id'=>'park','reasonCodes'=>['surface_match']]]];
$calls=0;$transport=function($url,$payload,$headers)use(&$calls,$good){++$calls;return ['candidates'=>[['content'=>['parts'=>[['text'=>json_encode($good)]]]]]];};
check((new RouteCoach([], $transport))->rank([$route])['mode']==='rules' && $calls===0,'disabled gate makes no calls');
$config=['enabled'=>true,'geminiKey'=>'test-only','geminiModel'=>'test-model'];
$coach=new RouteCoach($config,$transport);
check($coach->rank([$route])['mode']==='gemini','accepts validated Gemini output');
$coach->rank([$route]);$coach->rank([$route]);check($calls===2,'two-call lifetime budget');
$bad=fn()=>['candidates'=>[['content'=>['parts'=>[['text'=>'{"routes":[{"id":"invented","reasonCodes":["safe"]}]}']]]]]];
check((new RouteCoach($config,$bad))->rank([$route])['mode']==='rules','rejects invented route and safety reason');
check((new RouteCoach($config,$transport))->rank([array_merge($route,['verified'=>false])])['routes']===[],'demo route cannot reach provider');
check((new RouteCoach($config,$transport))->rank([array_merge($route,['checkedAt'=>time()-90000])])['routes']===[],'stale candidate excluded');
$groq=new RouteCoach(['enabled'=>true,'groqKey'=>'test-only','groqModel'=>'test-model'],fn()=>['choices'=>[['message'=>['content'=>json_encode($good)]]]]);
check($groq->rank([$route])['mode']==='groq','accepts validated Groq output');
check((new RouteCoach($config,fn()=>throw new RuntimeException('network')))->rank([$route])['mode']==='rules','provider failure retains rules');
