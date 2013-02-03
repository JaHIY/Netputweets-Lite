<?php
// This is a simple and dirty HTTP proxy for handling images that has simple URL.
// by dword1511 <zhangchi866@gmail.com>
error_reporting(E_ALL ^ E_NOTICE);
$url = !empty($_REQUEST['url']) ? $_GET['url'] : null;

if($url == null) {
  header("HTTP/1.1 200 OK");
  echo "<html>
<head><title>Error</title></head>
<body><h1>URL is Not Specified</h1></body>
</html>";
  return;
}

$fp = fopen($url, "rb");

if($fp == false) {
  header("HTTP/1.1 404 Not Found");
  echo "<html>
<head><title>404 File Not Found</title></head>
<body><h1>404 File Not Found</h1><em>The requested file '".$url."' is not found on this server.</em></body>
</html>";
  return;
}

$mime = "application/octet-stream";
$magic = fread($fp, 4);
if($magic[0] == 'G' && $magic[1] == 'I' && $magic[2] == 'F' && $magic[3] == '8') $mime = 'image/gif';
elseif($magic[0] == "\x89" && $magic[1] == 'P' && $magic[2] == 'N' && $magic[3] == 'G') $mime = 'image/png';
elseif($magic[0] == "\xff" && $magic[1] == "\xd8") $mime = 'image/jpeg';

header("HTTP/1.1 200 OK");
header("Content-type: ".$mime);
echo $magic;
flush();
fpassthru($fp);
flush();
fclose($fp);
exit;
?>

