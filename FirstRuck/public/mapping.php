<?php
declare(strict_types=1);
require_once dirname(__DIR__).'/src/bootstrap.php';
require_once FIRST_RUCK_ROOT.'/src/Mapping/Geoapify.php';
use FirstRuck\Mapping\Geoapify;
header('Cache-Control: no-store');
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
$config=is_file(FIRST_RUCK_ROOT.'/var/config.php')?require FIRST_RUCK_ROOT.'/var/config.php':[];
$key=(string)($config['geoapify_key']??getenv('FIRST_RUCK_GEOAPIFY_KEY')?:'');
$enabled=$key!==''&&($config['maps_enabled']??false)===true;
$_SESSION['map_csrf']??=bin2hex(random_bytes(24));
function reply(array $body,int $status=200): never {http_response_code($status);echo json_encode($body,JSON_THROW_ON_ERROR|JSON_UNESCAPED_SLASHES);exit;}
function reserve(int $units): void {
    $db=first_ruck_database();
    $db->exec('CREATE TABLE IF NOT EXISTS mapping_budget (day TEXT PRIMARY KEY, units INTEGER NOT NULL)');
    $db->beginTransaction();
    try{$q=$db->prepare('INSERT OR IGNORE INTO mapping_budget(day,units) VALUES (?,0)');$q->execute([gmdate('Y-m-d')]);
        $q=$db->prepare('UPDATE mapping_budget SET units=units+? WHERE day=? AND units+?<=10800');$q->execute([$units,gmdate('Y-m-d'),$units]);
        if($q->rowCount()!==1){$db->rollBack();reply(['ok'=>false,'error'=>'Map allowance reached for today. Your journal and recording still work.'],429);}$db->commit();
    }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
try{
    $action=(string)($_GET['action']??'bootstrap');
    if($action==='bootstrap')reply(['ok'=>true,'enabled'=>$enabled,'csrf'=>$_SESSION['map_csrf'],'provider'=>'Geoapify','message'=>$enabled?'Ready to search.':'Live maps are not connected yet. You can explore example routes.']);
    if(!$enabled)reply(['ok'=>false,'error'=>'Live maps are not connected yet.'],503);
    if($action==='tile'){
        $z=filter_var($_GET['z']??null,FILTER_VALIDATE_INT);$x=filter_var($_GET['x']??null,FILTER_VALIDATE_INT);$y=filter_var($_GET['y']??null,FILTER_VALIDATE_INT);
        if($z===false||$x===false||$y===false||$z<0||$z>18||$x<0||$y<0||$x>=2**$z||$y>=2**$z)reply(['ok'=>false],422);
        $_SESSION['tile_minute']??=['minute'=>intdiv(time(),60),'count'=>0];
        if($_SESSION['tile_minute']['minute']!==intdiv(time(),60))$_SESSION['tile_minute']=['minute'=>intdiv(time(),60),'count'=>0];
        if(++$_SESSION['tile_minute']['count']>180)reply(['ok'=>false],429);
        reserve(1);session_write_close();
        $ch=curl_init("https://maps.geoapify.com/v1/tile/osm-bright-smooth/$z/$x/$y.png?apiKey=".rawurlencode($key));curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>8,CURLOPT_FOLLOWLOCATION=>false]);$raw=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE);curl_close($ch);
        if(!is_string($raw)||$status!==200||strlen($raw)>1000000||!str_starts_with($raw,"\x89PNG"))reply(['ok'=>false],502);
        header('Content-Type: image/png');header('Cache-Control: private, max-age=3600');echo $raw;exit;
    }
    if($_SERVER['REQUEST_METHOD']!=='POST')reply(['ok'=>false,'error'=>'Use a search action.'],405);
    if(!hash_equals($_SESSION['map_csrf'],(string)($_SERVER['HTTP_X_CSRF_TOKEN']??'')))reply(['ok'=>false,'error'=>'Refresh the page and try again.'],419);
    if((int)($_SERVER['CONTENT_LENGTH']??0)>4096)reply(['ok'=>false,'error'=>'Search is too large.'],413);
    $raw=file_get_contents('php://input',false,null,0,4097);if(strlen($raw)>4096)reply(['ok'=>false],413);
    $input=json_decode($raw,true,16,JSON_THROW_ON_ERROR);if(!is_array($input))reply(['ok'=>false],422);
    $now=time();$history=array_filter($_SESSION['map_requests']??[],fn($t)=>$t>$now-60);
    if(count($history)>=6)reply(['ok'=>false,'error'=>'Please wait a minute before searching again.'],429);
    $history[]=$now;$_SESSION['map_requests']=$history;
    $client=new Geoapify($key);
    if($action==='search'){
        $text=trim((string)($input['text']??''));if(strlen($text)<2||strlen($text)>160)reply(['ok'=>false,'error'=>'Enter a town, park, or neighborhood.'],422);
        reserve(4);session_write_close();reply(['ok'=>true,'places'=>$client->search($text)]);
    }
    if($action==='routes'){
        [$lat,$lon]=Geoapify::coordinate($input['latitude']??null,$input['longitude']??null);
        $minutes=max(10,min(30,(int)($input['minutes']??15)));$shape=in_array($input['shape']??'', ['short-loop','out-back'],true)?$input['shape']:'out-back';
        reserve(36);session_write_close();reply(['ok'=>true,'routes'=>$client->routes($lat,$lon,$minutes,$shape),'message'=>'Map-derived candidates. Check access, surface, hills and conditions before choosing.']);
    }
    reply(['ok'=>false,'error'=>'Unknown map action.'],404);
}catch(InvalidArgumentException $e){reply(['ok'=>false,'error'=>$e->getMessage()],422);}catch(Throwable){reply(['ok'=>false,'error'=>'Map search could not finish. Try again later.'],502);}
