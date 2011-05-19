<?php

error_reporting(E_ALL ^ E_NOTICE);

// Twitter's API URL.
define('API_URL','https://api.twitter.com/1/');

// Twitter's Search API URL.
define('APIS_URL','https://search.twitter.com/');

define('NETPUTWEETS_TITLE', 'Netputweets Lite!');

// Cookie encryption key. Max 52 characters
define('ENCRYPTION_KEY', 'Example Key - Change Me!');

// OAuth consumer and secret keys. Available from http://twitter.com/oauth_clients
define('OAUTH_CONSUMER_KEY', 'awGBKfiSSqf1B2iKGsmJQ');
define('OAUTH_CONSUMER_SECRET', 'hym4qJF1F6nyjISzRUCFBU4OQSIr5mrk7074vId3K8');

// bit.ly login and API key for URL shortening
define('BITLY_LOGIN', 'netputer');
define('BITLY_API_KEY', 'R_bd1cb944bb3a85a75e94d18354464d46');

// API key for Read It Later - sign up at http://readitlaterlist.com/api/signup/
define('READ_IT_LATER_API_KEY', 'eWKA2m33d6768ksq40pdrq9NW5T1x1dk');

// Optional: Enable to view page processing and API time
define('DEBUG_MODE', 'OFF');

// Base URL, should point to your website, including a trailing slash
// Can be set manually but the following code tries to work it out automatically.
define('BASE_URL', 'http://t.orzdream.com/');

// MySQL storage of OAuth login details for users
define('ACCESS_USERS', 'OFF'); // set MYSQL for MySQL Mode or set FILE for File Mode

// set the cache floder for File Mode
//define('CACHE_FLODER', 'cache/user');

// or set the mysql information for MySQL mode
//define('MYSQL_URL', 'localhost');
//define('MYSQL_USER', 'username');
//define('MYSQL_PASSWORD', 'password');
//define('MYSQL_DB', 'dabr');

/* The following table is needed to store user login details if you enable MySQL Mode:

CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(64) NOT NULL,
  `oauth_key` varchar(128) NOT NULL,
  `oauth_secret` varchar(128) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`username`)
)

*/

// Google Analytics Mobile tracking code
// You need to download ga.php from the Google Analytics website for this to work
// Copyright 2009 Google Inc. All Rights Reserved.
$GA_ACCOUNT = "";
$GA_PIXEL = "ga.php";

function googleAnalyticsGetImageUrl() {
  global $GA_ACCOUNT, $GA_PIXEL;
  $url = "";
  $url .= $GA_PIXEL . "?";
  $url .= "utmac=" . $GA_ACCOUNT;
  $url .= "&utmn=" . rand(0, 0x7fffffff);
  $referer = $_SERVER["HTTP_REFERER"];
  $query = $_SERVER["QUERY_STRING"];
  $path = $_SERVER["REQUEST_URI"];
  if (empty($referer)) {
    $referer = "-";
  }
  $url .= "&utmr=" . urlencode($referer);
  if (!empty($path)) {
    $url .= "&utmp=" . urlencode($path);
  }
  $url .= "&guid=ON";
  return str_replace("&", "&amp;", $url);
}

?>
