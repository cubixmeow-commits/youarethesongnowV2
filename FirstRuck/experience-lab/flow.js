(function(root,factory){if(typeof module==='object'&&module.exports)module.exports=factory();else root.RuckExperience=factory();})(typeof globalThis!=='undefined'?globalThis:this,function(){
'use strict';
const flow=[
['welcome','Begin','welcome'],['meet-kip','Begin','lesson'],
['goal','You','question'],['comfortable-walk','You','question'],['recent-activity','You','question'],['small-start','You','lesson'],
['loaded-experience','Rhythm','question'],['available-time','Rhythm','question'],['weekly-rhythm','Rhythm','question'],['repeatable','Rhythm','lesson'],
['safety-gate','Prepare','question'],['pack-type','Prepare','question'],['load-available','Prepare','question'],['pack-lesson','Prepare','lesson'],
['shoes-socks','Prepare','question'],['surface','Route','question'],['hills','Route','question'],['turnaround','Route','lesson'],
['route-shape','Route','question'],['route-priority','Route','question'],['starting-area','Route','question'],['notice','Route','lesson'],
['plan','Your plan','plan'],['routes','Your plan','routes'],['ready','Your plan','ready'],['paywall','Your next chapter','paywall']
].map(([id,chapter,kind])=>({id,chapter,kind}));
const lessons={
'meet-kip':{kicker:'MEET KIP',title:'Small steps.\nExcellent company.',body:'Your little trail companion. Here for the first pack, the wrong turns, and the walks that become a habit.',quote:'“I packed enthusiasm. And probably too many snacks.”',action:'Let’s find my starting point',note:'Kip is a proposed original wombat character. Encouragement without streak guilt or competitive pressure.'},
'small-start':{kicker:'KIP’S FIELD NOTES · 01',title:'The first win?\nWanting to go again.',body:'Rucking is walking with a loaded pack. Your first outing is a chance to learn how it feels, with plenty left for next time.',quote:'“We’re collecting good walks. Not heroic regrets.”',action:'Find my rhythm',note:'Three questions, then a useful reward. No calorie multipliers or unsupported transformation claims.'},
'repeatable':{kicker:'KIP’S FIELD NOTES · 02',title:'Give your walk\na place in your week.',body:'A familiar time makes getting out the door one less decision. Your first-month plan leaves room for ordinary walks and rest.',quote:'“Same door. Fresh air. Different cloud.”',action:'Get my pack ready',note:'Time and weekly rhythm change the plan. No fake processing delay between the answers and their payoff.'},
'pack-lesson':{kicker:'THE TWO-MINUTE PACK CHECK',title:'Less bounce.\nMore birdsong.',body:'Keep the load close and secure, cushion hard edges, then try a few steps. Adjust anything that rubs or pinches.',quote:'“If your backpack has a drum solo, repack.”',photo:'pack-fit-adjustment.png',action:'Choose my route',note:'Practical preparation using existing gear. Starting with no added weight stays a valid option.'},
turnaround:{kicker:'KIP’S FIELD NOTES · 03',title:'Turning back\nis a route feature.',body:'An out-and-back lets you decide when you have had enough. For the first outing, familiar ground is a good place to begin.',quote:'“The way home deserves a place in the plan.”',action:'Find my kind of path',note:'Route shape has a real purpose. Route facts must come from a mapping source before this becomes navigation.'},
notice:{kicker:'BRING HOME A LITTLE OUTSIDE',title:'Not every highlight\nis a number.',body:'A curious tree. A good view. The face you make when you finish. Your photos can become a small field journal of getting out there.',quote:'“I’m personally hoping for a very good rock.”',photo:'route-choice-greenway.png',action:'Reveal my first ruck',note:'The differentiator: private walk memories and optional shareable postcards. No public location sharing by default.'}
};
function distance(a,b){const r=Math.PI/180;const dlat=(b.latitude-a.latitude)*r,dlon=(b.longitude-a.longitude)*r;const h=Math.sin(dlat/2)**2+Math.cos(a.latitude*r)*Math.cos(b.latitude*r)*Math.sin(dlon/2)**2;return 6371000*2*Math.atan2(Math.sqrt(h),Math.sqrt(1-h));}
function acceptPoint(previous,point){if(!Number.isFinite(point.latitude)||!Number.isFinite(point.longitude)||Math.abs(point.latitude)>90||Math.abs(point.longitude)>180||!Number.isFinite(point.accuracy)||point.accuracy>35||point.accuracy<0||!Number.isFinite(point.timestamp))return false;if(!previous)return true;const dt=(point.timestamp-previous.timestamp)/1000;return dt>0&&distance(previous,point)/dt<3.5;}
function elapsed(record,now){return record.accumulated+(record.running?Math.max(0,now-record.started):0);}
return {flow,lessons,distance,acceptPoint,elapsed};
});
