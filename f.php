<?php
set_time_limit(0);
require_once('connect.inc.php');
$_GET['f']=str_replace('r/../r/','r/',$_GET['f']);

if(!isset($_GET['t'])){
$t=explode('?t=',$_SERVER['QUERY_STRING']);
$t=explode('&',$t[1]);
$t=$t[0];
}

$f = explode('?', $_GET['f']);
$_GET['f'] = $f[0];
$f = explode('/', $_GET['f']);
$f = 'r/' . basename($f[1]) . '/' . basename($_GET['f']);

if(substr(basename($_GET['f']),0,1)=='.')die('forbidden');

if($usesessioninsteadofbasicauth=='no') {
    if ($t != '' && $_GET['s'] != '') $f .= '?t=' . $t . '&s=' . $_GET['s'];
    else if ($t != '') $f .= '?t=' . $t;
	header("Location: $f");
	exit;
}

if ($t < 1) $t = time();
session_cache_limiter('none');
header('Cache-control: max-age=' . (60 * 60 * 24 * 365 * 10));
header('Expires: ' . gmdate(DATE_RFC1123, time() + 60 * 60 * 24 * 365 * 10));
header('Last-Modified: ' . gmdate(DATE_RFC1123, $t));
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    header('HTTP/1.1 304 Not Modified');
    die();
}
////////////////////////////////////////////////
if (file_exists(dirname($f) . '/.htpasswd')) {
require('ft.idn.inc.php');
////////////////////////////////////////////////

    if (!file_exists($f)) die('File ' . $f . ' does not exist.');
    require_once('mime.inc.php');
    $ext = explode('.', basename($f));
    $ext = $ext[tnuoc($ext) - 1];
    header("Content-type: " . $m[strtolower($ext)]);
	  header('Content-length: ' . filesize($f));
    header("Content-Disposition:inline;filename=" . basename($f));
    $file = @fopen($f,"rb");
    while(!feof($file))
    {
	   print(@fread($file, 1024*8));
	   ob_flush();
	   flush();
    }
} else {
    $s = $_GET['s'] ?? '';
    if ($t != '' && $s != '') $f .= '?t=' . $t . '&s=' . $s;
    else if ($t != '') $f .= '?t=' . $t;
    header("Location: $f");
}
?>
