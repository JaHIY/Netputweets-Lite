<?php
require 'Autolink.php';
require 'Extractor.php';
require 'Embedly.php';
date_default_timezone_set('Asia/Shanghai');

menu_register(array(
  '' => array(
    'callback' => 'twitter_home_page',
    'accesskey' => '0',
  ),
  'status' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_status_page',
  ),
  'update' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_update',
  ),
  'retweets' => array(
    'security' => true,
    'callback' => 'twitter_retweets_page',
    'accesskey' => '2',
    'title' => 'Retweets',
  ),
  'twitter-retweet' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_retweet',
  ),
  'replies' => array(
    'security' => true,
    'callback' => 'twitter_replies_page',
    'accesskey' => '1',
  ),
  'favourite' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_mark_favourite_page',
  ),
  'unfavourite' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_mark_favourite_page',
  ),
  'directs' => array(
    'security' => true,
    'callback' => 'twitter_directs_page',
    'accesskey' => '3',
  ),
  'search' => array(
    'security' => true,
    'callback' => 'twitter_search_page',
    'accesskey' => '4',
  ),
  'user' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_user_page',
  ),
  'follow' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_follow_page',
  ),
  'unfollow' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_follow_page',
  ),
  'confirm' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_confirmation_page',
  ),
  'confirmed' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_confirmed_page',
  ),
  'block' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_block_page',
  ),
  'unblock' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_block_page',
  ),
  'spam' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_spam_page',
  ),
  'favourites' => array(
    'security' => true,
    'callback' =>  'twitter_favourites_page',
  ),
  'followers' => array(
    'security' => true,
    'callback' => 'twitter_followers_page',
  ),
  'friends' => array(
    'security' => true,
    'security' => true,
    'callback' => 'twitter_friends_page',
  ),
  'delete' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_delete_page',
  ),
  'deleteDM' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_deleteDM_page',
  ),
  'blockings' => array(
    'security' => true,
    'security' => true,
    'callback' => 'twitter_blockings_page',
  ),
  'retweet' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_retweet_page',
  ),
  'hash' => array(
    'security' => true,
    'hidden' => true,
    'callback' => 'twitter_hashtag_page',
  ),
  'picture' => array(
    'security' => true,
    'callback' => 'twitter_picture_page',
  ),
    'trends' => array(
    'security' => true,
    'callback' => 'twitter_trends_page',
  ),
    'retweeted' => array(
    'security' => true,
    'callback' => 'twitter_retweeted_page',
    'accesskey' => '5',
    'title' => 'Retweeted',
  ),
    'retweeted_by' => array(
    'security' => true,
    'hidden' => true,
    'callback' => 'twitter_retweeters_page',
  ),
    'profile' => array(
    'hidden' => true,
    'security' => true,
    'callback' => 'twitter_profile_page',
  )
));

// Patch in multibyte support
if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $len = '', $encoding="UTF-8"){
    $limit = strlen($str);

    for ($s = 0; $start > 0;--$start) {// found the real start
      if ($s >= $limit)
      break;

      if ($str[$s] <= "\x7F")
      ++$s;
      else {
      ++$s; // skip length

      while ($str[$s] >= "\x80" && $str[$s] <= "\xBF")
    ++$s;
      }
    }

    if ($len == '')
      return substr($str, $s);
    else
      for ($e = $s; $len > 0; --$len) {//found the real end
      if ($e >= $limit)
    break;

      if ($str[$e] <= "\x7F")
    ++$e;
      else {
    ++$e;//skip length

    while ($str[$e] >= "\x80" && $str[$e] <= "\xBF" && $e < $limit)
    ++$e;
      }
      }

    return substr($str, $s, $e - $s);
    }
}

function sysSubStr($String,$Length,$Append = false) {
        if (function_exists('mb_strlen') ? mb_strlen($String,'UTF-8') <= $Length : strlen(utf8_decode($String)) <= $Length) {
            return $String;
        }
        else
        {
            $I = 0;
            $Count = 0;

            while ($Count < $Length)
            {
                $StringTMP = substr($String,$I,1);
                if ( ord($StringTMP) >=224 )
                {
                    $StringTMP = substr($String,$I,3);
                    $I = $I + 3;
                }
                elseif( ord($StringTMP) >=192 )
                {
                    $StringTMP = substr($String,$I,2);
                    $I = $I + 2;
                }
                else
                {
                    $I = $I + 1;
                }
                $Count ++;
                $StringLast[] = $StringTMP;
            }
            if($Append)
                array_pop($StringLast);
            $StringLast = implode("",$StringLast);
            if($Append && $String != $StringLast)
                $StringLast .= urldecode("%E2%80%A6"); //utf-8 code of "..." as a character
            return $StringLast;
        }
}

function long_url($shortURL){
    if (LONG_URL !== 'ON' || setting_fetch('longurl') !== 'yes')
    {
        return $shortURL;
    }
    $url = "http://api.longurl.org/v2/expand?format=json&url=" . $shortURL;
    $url_json = twitter_fetch($url);
    $url_array = json_decode($url_json,true);
    $url_long = $url_array["long-url"];
    if ($url_long == null)
    {
        return $shortURL;
    }
    return $url_long;
}

function friendship_exists($user_a) {
  $request = API_URL.'friendships/show.json?target_screen_name=' . $user_a;
  $following = twitter_process($request);
  
  if ($following->relationship->target->following == 1) {
    return true;
  } else {
    return false;
  }
}

function friendship($user_a) 
{
    $request = API_URL.'friendships/show.json?target_screen_name=' . $user_a;
    return twitter_process($request);
}


function twitter_block_exists($query) 
{
    //http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-blocks-blocking-ids
    //Get an array of all ids the authenticated user is blocking
    $request = API_URL.'blocks/blocking/ids.json';
    $blocked = (array) twitter_process($request);
    
    //bool in_array  ( mixed $needle  , array $haystack  [, bool $strict  ] )  
    //If the authenticate user has blocked $query it will appear in the array
    return in_array($query,$blocked);
}

function twitter_trends_page($query) 
{
  $woeid = $_GET['woeid'];
  if($woeid == '') $woeid = '1'; //worldwide

  //fetch "local" names
  $request = API_URL.'trends/available.json';
  $local = twitter_process($request);
  $header = '<form method="get" action="trends"><select name="woeid">';
  $header .= '<option value="1"' . (($woeid == 1) ? ' selected="selected"' : '') . '>Worldwide</option>';

  //sort the output, going for Country with Towns as children
  foreach($local as $key => $row) {
         $c[$key] = $row->country;
         $t[$key] = $row->placeType->code;
         $n[$key] = $row->name;
  }
  array_multisort($c, SORT_ASC, $t, SORT_DESC, $n, SORT_ASC, $local);

  foreach($local as $l) {
    if($l->woeid != 1) {
      $n = $l->name;
      if($l->placeType->code != 12) $n = '-' . $n;
      $header .= '<option value="' . $l->woeid . '"' . (($l->woeid == $woeid) ? ' selected="selected"' : '') . '>' . $n . '</option>';
    }
  }
  $header .= '</select> <button type="submit">Go</button></form>';
  
  $request = API_URL.'trends/' . $woeid . '.json';
  $trends = twitter_process($request);
  $search_url = 'search?query=';
  foreach($trends[0]->trends as $trend) {
    $row = array('<strong><a href="' . str_replace('http://twitter.com/search/', $search_url, $trend->url) . '">' . $trend->name . '</a></strong>');
    $rows[] = array('data' => $row,  'class' => 'tweet');
  }
  $headers = array($header);
  $content = theme('table', $headers, $rows, array('class' => 'timeline'));
  theme('page', 'Trends', $content);
}

function js_counter($name, $length='140')
{
    $script = '<script type="text/javascript">
<!--
function updateCount() {
var remaining = ' . $length . ' - document.getElementById("' . $name . '").value.length;
document.getElementById("remaining").innerHTML = remaining;
if(remaining < 0) {
 var colour = "#FF0000";
 var weight = "bold";
} else {
 var colour = "";
 var weight = "";
}
document.getElementById("remaining").style.color = colour;
document.getElementById("remaining").style.fontWeight = weight;
setTimeout(updateCount, 400);
}
updateCount();
//-->
</script>';
    return $script;
}

function twitter_picture_page($query) {
    if (user_type() == 'oauth') {
      //V2 of the Twitpic API allows for OAuth
      //http://dev.twitpic.com/docs/2/upload/

      //Has the user submitted an image and message?
      if ($_POST['message'])
      {
    $twitpicURL = 'http://api.twitpic.com/2/upload.json';
      
    //Set the initial headers
    $header = array(
    'X-Auth-Service-Provider: https://api.twitter.com/1/account/verify_credentials.json', 
    'X-Verify-Credentials-Authorization: OAuth realm="http://api.twitter.com/"'
    );
      
    //Using Abraham's OAuth library
    require_once('OAuth.php');

    // instantiating OAuth customer
    $consumer = new OAuthConsumer(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);

    // instantiating signer
    $sha1_method = new OAuthSignatureMethod_HMAC_SHA1();

    // user's token
    list($oauth_token, $oauth_token_secret) = explode('|', $GLOBALS['user']['password']);
    $token = new OAuthConsumer($oauth_token, $oauth_token_secret);

    // Generate all the OAuth parameters needed
    $signingURL = API_URL.'account/verify_credentials.json';
    $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $signingURL, array());
    $request->sign_request($sha1_method, $consumer, $token);

    $header[1] .= ", oauth_consumer_key=\"" . $request->get_parameter('oauth_consumer_key') ."\"";
    $header[1] .= ", oauth_signature_method=\"" . $request->get_parameter('oauth_signature_method') ."\"";
    $header[1] .= ", oauth_token=\"" . $request->get_parameter('oauth_token') ."\"";
    $header[1] .= ", oauth_timestamp=\"" . $request->get_parameter('oauth_timestamp') ."\"";
    $header[1] .= ", oauth_nonce=\"" . $request->get_parameter('oauth_nonce') ."\"";
    $header[1] .= ", oauth_version=\"" . $request->get_parameter('oauth_version') ."\"";
    $header[1] .= ", oauth_signature=\"" . urlencode($request->get_parameter('oauth_signature')) ."\"";

    //open connection
    $ch = curl_init();
      
    //Set paramaters
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL,$twitpicURL);
      
    //TwitPic requires the data to be sent as POST
    $media_data = array(
    'media' => '@'.$_FILES['media']['tmp_name'],
  'message' => ' ' . stripslashes($_POST['message']), //A space is needed because twitpic b0rks if first char is an @
  'key'=>TWITPIC_API_KEY
    );

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$media_data);

    //execute post
    $result = curl_exec($ch);
    $response_info=curl_getinfo($ch);

    //close connection
    curl_close($ch);

    if ($response_info['http_code'] == 200) //Success
    {
      //Decode the response
      $json = json_decode($result);
      $id = $json->id;
      $twitpicURL = $json->url;
      $text = $json->text;
      $message = trim($text) . " " . $twitpicURL;

      //Send the user's message to twitter
      $request = API_URL.'statuses/update.json';

      $post_data = array('source' => 'dabr', 'status' => $message);
      $status = twitter_process($request, $post_data);

      //Back to the timeline
      twitter_refresh("picture/confirm/$id");
    }
    else
    {
      $content = "<p>Twitpic upload failed. No idea why!</p>";
      $content .=  "<pre>";
      $json = json_decode($result);
      $content .= "<br / ><span class='textb'>message</span> " . urlencode($_POST['message']);
      $content .= "<br / ><span class='textb'>json</span> " . print_r($json);
      $content .= "<br / ><span class='textb'>Response</span> " . print_r($response_info);
      $content .= "<br / ><span class='textb'>header</span> " . print_r($header);
      $content .= "<br / ><span class='textb'>media_data</span> " . print_r($media_data);
      $content .= "<br /><span class='textb'>URL was</span> " . $twitpicURL;
      $content .= "<br /><span class='textb'>File uploaded was</span> " . $_FILES['media']['tmp_name'];
      $content .= "</pre>";
    }
      }
      elseif ($query[1] == 'confirm')
      {
    $content = "<p>Upload success. Image posted to Twitter.</p><p><img src='http://twitpic.com/show/thumb/{$query[2]}' alt='' /></p>";
      }
      else
      {
    $content = "<form method='post' action='picture' enctype='multipart/form-data'><div>Image <input type='file' name='media' /><br />Message (optional):<br /><textarea name='message' rows='3' cols='60' id='message'></textarea><br /><button type='submit'>Send</button><span id='remaining'>110</span></div></form>";
    $content .= js_counter("message", "110");
      }

      return theme('page', 'Picture Upload', $content);
    } else {
return theme('page', 'Error', '<p>You can\'t use Picture uploads while accessing Dabr using an non-OAuth login.</p>');
    }
}

function line_united($str) {
    $str = str_replace(array("\r\n", "\r"), "\n", $str);
    return $str;
}

function twitter_profile_page($query) {
    $url = API_URL."account/update_profile.json";
    if ($_POST['name']) {
    $post_data = array(
      'name' => line_united(stripslashes($_POST['name'])),
      'location' => line_united($_POST['location']),
      'url' => line_united($_POST['url']),
      'description' => line_united($_POST['description']),
    );
    $p = twitter_process($url, $post_data);
    $user = user_current_username();
    twitter_refresh("user/{$user}");
    } else {
    $p = twitter_process($url, $post_data);
    $content = "<form method=\"post\" action=\"profile\" enctype=\"multipart/form-data\"><div>Name: <input type=\"text\" name=\"name\" id=\"name\" value=\"{$p->name}\" /> <span id=\"name-remaining\">20</span><br />Location: <input type=\"text\" name=\"location\" id=\"location\" value=\"{$p->location}\" /><span id=\"location-remaining\">30</span><br />Link: <input type=\"text\" name=\"url\" id=\"url\" value=\"{$p->url}\" /> <span id=\"url-remaining\">100</span><br />Bio: <br /><textarea name=\"description\" id=\"description\" rows=\"3\" cols=\"60\">{$p->description}</textarea><br /><button type=\"submit\">Update</button> <span id=\"description-remaining\">160</span></div></form>";
    $content .='<script type="text/javascript">
<!--
function updateCount(id,number) {
var e = id + "-remaining";
var remaining = number - document.getElementById(id).value.length;
document.getElementById(e).innerHTML = remaining;
if(remaining < 0) {
 var colour = "#FF0000";
 var weight = "bold";
} else {
 var colour = "";
 var weight = "";
}
document.getElementById(e).style.color = colour;
document.getElementById(e).style.fontWeight = weight;
}
function bindupdateCount(id,number) {
updateCount(id,number);
document.getElementById(id).onkeydown = function(){updateCount(id,number);}
document.getElementById(id).onkeypress = function(){updateCount(id,number);}
document.getElementById(id).onkeyup = function(){updateCount(id,number);}
}
bindupdateCount("name",20);
bindupdateCount("location",30);
bindupdateCount("url",100);
bindupdateCount("description",160);
//-->
</script>';
    }
    $p = twitter_process($url, $post_data);
    return theme('page', 'Update Profile', $content);
}

function twitter_process($url, $post_data = false)
{
    if ($post_data === true)
    {
  $post_data = array();
    }
    
    if (user_type() == 'oauth' && ( strpos($url, '/twitter.com') !== false || strpos($url, 'api.twitter.com') !== false)) 
    {
  user_oauth_sign($url, $post_data);
    } 

    elseif (strpos($url, 'api.twitter.com') !== false && is_array($post_data)) 
    {
  // Passing $post_data as an array to twitter.com (non-oauth) causes an error :(
  $s = array();
  foreach ($post_data as $name => $value)
  $s[] = $name.'='.urlencode($value);
  $post_data = implode('&', $s);
    }

    $api_start = microtime(1);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    if($post_data !== false && !$_GET['page']) 
    {
  curl_setopt ($ch, CURLOPT_POST, true);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_data);
    }

    if (user_type() != 'oauth' && user_is_authenticated())
    {
  curl_setopt($ch, CURLOPT_USERPWD, user_current_username().':'.$GLOBALS['user']['password']);
    }

    //from  http://github.com/abraham/twitteroauth/blob/master/twitteroauth/twitteroauth.php
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    $response = curl_exec($ch);
    $response_info = curl_getinfo($ch);
    $erno = curl_errno($ch);
    $er = curl_error($ch);
    curl_close($ch);

    global $api_time;
    global $rate_limit;
    //Doesn't bloody work. No idea why!
    $rate_limit = $response_info['X-RateLimit-Limit'];

    $api_time += microtime(1) - $api_start;

    switch( intval( $response_info['http_code'] ) ) 
    {
  case 200:
  case 201:
      $json = json_decode($response);
      if ($json)
      {
    return $json;
      }
      return $response;
  case 401:
      user_logout();
      if (DEBUG_MODE == 'ON') {
        theme('error', "<p>Error: Login credentials incorrect.</p><p>{$response_info['http_code']}: {$response}</p><hr /><p>$url</p>");
      } else {
        theme('error', "<p>Error: Login credentials incorrect.</p><p>{$response_info['http_code']}: {$response}</p>");
      }
  case 0:
      $result = $erno . ":" . $er . "<br />" ;
      /*
      foreach ($response_info as $key => $value) 
      {
    $result .= "Key: $key; Value: $value<br />";
      }
      */
      theme('error', '<h2>Twitter timed out</h2><p>Dabr gave up on waiting for Twitter to respond. They\'re probably overloaded right now, try again in a minute. <br />'. $result . ' </p>');
  default:
      $result = json_decode($response);
      $result = $result->error ? $result->error : $response;
      if (strlen($result) > 500) 
      {
    $result = 'Something broke on Twitter\'s end.';
    /*    
    $result .= $erno . ":" . $er . "<br />" ;
    foreach ($response_info as $key => $value) 
    {
    $result .= "Key: $key; Value: $value<br />";
    }
    */
      }
      if (DEBUG_MODE == 'ON') {
        theme('error', "<h2>An error occured while calling the Twitter API</h2><p>{$response_info['http_code']}: {$result}</p><hr /><p>$url</p>");
      } else {
        theme('error', "<h2>An error occured while calling the Twitter API</h2><p>{$response_info['http_code']}: {$result}</p>");
      }
    }
}

function twitter_url_shorten($text) {
  return preg_replace_callback('#((\w+://|www)[\w\#$%&~/.\-;:=,?@\[\]+]{33,1950})(?<![.,])#is', 'twitter_url_shorten_callback', $text, -1);
}

function twitter_url_shorten_callback($match) {
    if (preg_match('#http://www.flickr.com/photos/[^/]+/(\d+)/#', $match[0], $matches)) {
  return 'http://flic.kr/p/'.flickr_encode($matches[1]);
    }
    switch (setting_fetch('short')) {
        case 'no':
            return $match[0];
            break;
        case 'j.mp':
            if (BITLY_API_KEY == '' || BITLY_LOGIN == '') return $match[0];
            $request = 'https://api-ssl.bitly.com/v3/shorten?login='.BITLY_LOGIN.'&apiKey='.BITLY_API_KEY.'&longUrl='.urlencode($match[0]).'&format=json';
            $json = json_decode(twitter_fetch($request));
            switch ($json->status_code) {
                case 200:
                    return $json->data->url;
                    break;
                default:
                    return $match[0];
                    break;
            }
            break;
        case 'goo.gl':
            $request = 'http://ggl-shortener.appspot.com/?url='.urlencode($match[0]);
            $json = json_decode(twitter_fetch($request));//lzq
            $result = (!isset($json->error_message)) ? $json->short_url : $match[0];
            return $result;
            break;
        default:
            $short_url_mapping = array(
                '8.nf' => 'http://8.nf/api.php?format=simple&action=shorturl&url=',
                'zi.mu' => 'http://zi.mu/api.php?format=simple&action=shorturl&url=',
                'ye.pe' => 'http://ye.pe/api.php?format=simple&action=shorturl&url=',
                'orz.se' => 'http://orz.se/api.php?format=simple&action=shorturl&url=',
                'aa.cx' => 'http://aa.cx/api.php?url=',
                'is.gd' => 'http://is.gd/api.php?longurl=',
                's8.hk' => 'http://s8.hk/api/shorten?longUrl=',
                'tinyurl.com' => 'http://tinyurl.com/api-create.php?url='
            );
            foreach ($short_url_mapping as $setting => $url) {
                if (setting_fetch('short') == $setting) {
                    $request = $url.urlencode($match[0]);
                    $links = twitter_fetch($request);
                    $result = (stripos($links, 'http://'.$setting) == 0) ?  $links : $match[0];
                    return $result;
                } else {
                    return $match[0];
                }
            }
        break;
    }
}

function twitter_fetch($url) {
  global $services_time;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  //$user_agent = "Mozilla/5.0 (compatible; dabr; " . BASE_URL . ")";
  $user_agent = "Mozilla/5.0 (compatible; Netputweets Lite!)";
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $fetch_start = microtime(1);
  $response = curl_exec($ch);
  curl_close($ch);
  $services_time += microtime(1) - $fetch_start;
  return $response;
}

function link_trans($url) {
 switch (setting_fetch('linktrans', 'd')) {
    case 'o':
     $atext = $url;
     break;
    case 'd':
     $url = ((stripos($url,'http://') !== 0) && (stripos($url,'https://') !== 0)) ? 'http://'.$url : $url;
     $urlpara = parse_url($url);
     $atext = "[{$urlpara[host]}]";
     break;
    case 'l':
     $atext = "[link]";
     break;
    }
 return $atext;
}

// http://dev.twitter.com/pages/tweet_entities
function twitter_get_media($status) {
    if($status->entities->media) {
        $media_html = "<a href=\"" . $status->entities->media[0]->media_url_https . "\" rel='external nofollow noreferrer'>";
        $media_html .=  "<img alt='' src=\"" . $status->entities->media[0]->media_url_https . ":thumb\" width=\"" . $status->entities->media[0]->sizes->thumb->w . "\" height=\"" . $status->entities->media[0]->sizes->thumb->h . "\" />";
        $media_html .= "</a><br />";
        return $media_html;
    }
}

//Ugly patch by JahIY 2011.08.10
function twitter_entities_links($entities_urls,$out) {
    foreach($entities_urls as $urls) {
        if($urls->display_url != "") {
            $display_url = $urls->display_url;
        } else {
            $display_url = $urls->url;
        }

        $expanded_url = ($urls->expanded_url) ? $urls->expanded_url : $urls->url;

        $lurl = (setting_fetch('longurl') == 'yes' && LONG_URL == 'ON') ? long_url($expanded_url) : $expanded_url;

        if (setting_fetch('gwt') == 'on') // If the user wants links to go via GWT 
        {
            $encoded = urlencode($lurl);
            $link = "http://google.com/gwt/n?u={$encoded}";
        } else {
            $link = $lurl;
        }
        $atext = link_trans($display_url);
        $link_html = '<a href="' . $link . '" rel="external nofollow noreferrer">' . $atext . '</a>';
        $url = $urls->url;

        // Replace all URLs *UNLESS* they have already been linked (for example to an image)
        $pattern = '#((?<!href\=(\'|\"))'.preg_quote($url,'#').')#i';
        $out = preg_replace($pattern,$link_html, $out);
    }
    return $out;
}

function twitter_parse_tags($input, $entities = false) {

        // Use the Entities to replace hyperlink URLs
        // http://dev.twitter.com/pages/tweet_entities
        if($entities) {
                $out = $input;
                $out = ($entities->urls) ? twitter_entities_links($entities->urls,$out) : $out;
                $out = ($entities->media) ? twitter_entities_links($entities->media,$out) : $out;
        } else {  // If Entities haven't been returned, use Autolink
                // Create an array containing all URLs
                $urls = Twitter_Extractor::create($input)
                                ->extractURLs();

                $out = $input;

        // Hyperlink the URLs 
            if (setting_fetch('gwt') == 'on') // If the user wants links to go via GWT 
            {
                foreach($urls as $url) 
                {
                     
                     if (setting_fetch('longurl') == 'yes' && LONG_URL == 'ON') {
                        $lurl = long_url($url);
                     } else {
                        $lurl = $url;
                     }
                     $encoded = urlencode($lurl);
                     $atext = link_trans($lurl);
                     $out = str_replace($url, "<a href='http://google.com/gwt/n?u={$encoded}' rel='external nofollow noreferrer'>{$atext}</a>", $out);
                }
            } else {
                        $out = Twitter_Autolink::create($out)
                                                ->setTarget('')
                                                ->setTag('')
                                                ->addLinksToURLs();
                        foreach($urls as $url) 
                        {
                            if (setting_fetch('longurl') == 'yes' && LONG_URL == 'ON') {
                                $lurl = long_url($url);
                                $out = str_replace('href="'.$url.'"', 'href="'.$lurl.'"', $out);
                            } else {
                                $lurl = $url;
                            }
                            $atext = link_trans($lurl);
                            $out = str_replace(">{$url}</a>", ">{$atext}</a>", $out);
                        }
            }
        }

        // Hyperlink the @ and lists
        $out = Twitter_Autolink::create($out)
                                ->setTarget('')
                                ->setTag('')
                                ->addLinksToUsernamesAndLists();

        // Hyperlink the #
        $out = Twitter_Autolink::create($out)
                                ->setTarget('')
                                ->addLinksToHashtags();

        //Linebreaks.  Some clients insert \n for formatting.
        $out = nl2br($out);

        //Return the completed string
        return $out;
}

function flickr_decode($num) {
  $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
  $decoded = 0;
  $multi = 1;
  while (strlen($num) > 0) {
    $digit = $num[strlen($num)-1];
    $decoded += $multi * strpos($alphabet, $digit);
    $multi = $multi * strlen($alphabet);
    $num = substr($num, 0, -1);
  }
  return $decoded;
}

function flickr_encode($num) {
  $alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
  $base_count = strlen($alphabet);
  $encoded = '';
  while ($num >= $base_count) {
    $div = $num/$base_count;
    $mod = ($num-($base_count*intval($div)));
    $encoded = $alphabet[$mod] . $encoded;
    $num = intval($div);
  }
  if ($num) $encoded = $alphabet[$num] . $encoded;
  return $encoded;
}

function format_interval($timestamp, $granularity = 2) {
  $units = array(
    'year' => 31536000,
    'day' => 86400,
    'hour' => 3600,
    'min' => 60,
    'sec' => 1
  );
  $output = '';
  foreach ($units as $key => $value) {
    if ($timestamp >= $value) {
      $output .= ($output ? ' ' : ''). pluralise($key, floor($timestamp / $value), true);
      $timestamp %= $value;
      $granularity--;
    }
    if ($granularity == 0) {
      break;
    }
  }
  return $output ? $output : '0 sec';
}

function twitter_status_page($query) {
  $id = (string) $query[1];
  if (is_numeric($id)) {
    $request = API_URL."statuses/show/{$id}.json?include_entities=true";
    $status = twitter_process($request);
    $content = theme('status', $status);
    
  // check for translate flag
    if (strcmp($query[2],'tr')==0) {
  $r = twitter_process("http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=|en&q=".urlencode($status->text));  
  $rz = twitter_process("http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=|zh-CN&q=".urlencode($status->text));  
  $content .= "<p class=\"translate\"><strong>English Translation</strong>: ".
      $r->responseData->translatedText."</p>";
  $content .= "<p class=\"translate\"><strong>Simplified Chinese Translation</strong>: ".
      $rz->responseData->translatedText."</p>";
    }

  // Add Read It Later
    if(strcmp($query[2],'rl')==0){
        $rl_api = "http://readitlaterlist.com/v2/add?username=".setting_fetch('rl_user','')."&password=".setting_fetch('rl_pass','')."&apikey=".READ_IT_LATER_API_KEY;
        $rl_u = "http://twitter.com/".$status->user->screen_name."/status/".$status->id_str;
        $rl_t = "Tweet+from+@".$status->user->screen_name.":+".urlencode($status->text);

        $curl_url = "$rl_api&url=$rl_u&title=$rl_t";

        $ret = twitter_fetch($curl_url);
        switch ($ret) {
        case '200 OK':
            $content .= "Tweet saved. Status: 200 OK";
            break;
        case 100:
            $content .= "X-Limit-User-Limit. Error: 100";
            break;
        case 43:
            $content .= "X-Limit-User-Remaining. Error: 43";
            break;
        case 25:
            $content .= "X-Limit-User-Reset or X-Limit-Key-Reset. Error: 25";
            break;
        case 5000:
            $content .= "X-Limit-Key-Limit. Error: 5000";
            break;
        case 3520:
            $content .= "X-Limit-Key-Remaining. Error: 3520";
            break;
        default:
            $content .= "ERROR: instead of response code 200, we got: $ret.";
            break;
        }
    }

if(strcmp($query[2],'')==0){
      $thread_id = $status->id_str;
      $request = API_URL."related_results/show/{$thread_id}.json";
      $threadstatus = twitter_process($request);
      if ($threadstatus && $threadstatus[0] && $threadstatus[0]->results) {
        $array = array_reverse($threadstatus[0]->results);
        $tl = array();
        foreach ($array as $key=>$value) {
            array_push($tl, $value->value);
            if ($value->value->in_reply_to_status_id_str && $value->value->in_reply_to_status_id_str == $status->id_str) {
                array_push($tl, $status);
            }
        }
        $tl = twitter_standard_timeline($tl, 'replies');
        $content .= '<p>Related results...</p>'.theme('timeline', $tl);
      } elseif (!$status->user->protected) {
      $thread = twitter_thread_timeline($id);
      $content .= '<p>And the experimental conversation view...</p>'.theme('timeline', $thread);
      $content .= "<p>Don't like the thread order? Go to <a href='settings'>settings</a> to reverse it. Either way - the dates/times are not always accurate.</p>";
    }
}

    theme('page', "Status $id", $content);
  }
}

function twitter_thread_timeline($thread_id) {
  $request = APIS_URL."search/thread/{$thread_id}";
  $tl = twitter_standard_timeline(twitter_fetch($request), 'thread');
  return $tl;
}

function twitter_retweet_page($query) {
  $id = (string) $query[1];
  if (is_numeric($id)) {
    $request = API_URL."statuses/show/{$id}.json?include_entities=true";
    $tl = twitter_process($request);
    $content = theme('retweet', $tl);
    theme('page', 'Retweet', $content);
  }
}

function twitter_refresh($page = NULL) {
  if (isset($page)) {
    $page = BASE_URL . $page;
  } else {
    $page = $_SERVER['HTTP_REFERER'];
  }
  header('Location: '. $page);
  exit();
}

function twitter_delete_page($query) {
  twitter_ensure_post_action();
  
  $id = (string) $query[1];
  if (is_numeric($id)) {
    $request = API_URL."statuses/destroy/{$id}.json?page=".intval($_GET['page']);
    $tl = twitter_process($request, true);
    twitter_refresh('user/'.user_current_username());
  }
}

function twitter_deleteDM_page($query) {
    //Deletes a DM
    twitter_ensure_post_action();

    $id = (string) $query[1];
    if (is_numeric($id)) {
        $request = API_URL."direct_messages/destroy/$id.json";
        twitter_process($request, true);
        twitter_refresh('directs/');
    }
}

function twitter_ensure_post_action() {
  // This function is used to make sure the user submitted their action as an HTTP POST request
  // It slightly increases security for actions such as Delete, Block and Spam
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Error: Invalid HTTP request method for this action.');
  }
}

function twitter_follow_page($query) {
  $user = $query[1];
  if ($user) {
    if($query[0] == 'follow'){
      $request = API_URL."friendships/create/{$user}.json";
    } else {
      $request = API_URL."friendships/destroy/{$user}.json";
    }
    twitter_process($request, true);
    twitter_refresh('friends');
  }
}

function twitter_block_page($query) {
  twitter_ensure_post_action();
  $user = $query[1];
  if ($user) {
    if($query[0] == 'block'){
      $request = API_URL."blocks/create/create.json?screen_name={$user}";
      twitter_process($request, true);
      twitter_refresh("confirmed/block/{$user}");
    } else {
      $request = API_URL."blocks/destroy/destroy.json?screen_name={$user}";
      twitter_process($request, true);
      twitter_refresh("confirmed/unblock/{$user}");
    }
    twitter_process($request, true);
    twitter_refresh("user/{$user}");
  }
}

function twitter_spam_page($query) 
{
    //http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-report_spam
    //We need to post this data
    twitter_ensure_post_action();
    $user = $query[1];

    //The data we need to post
    $post_data = array("screen_name" => $user);

    $request = API_URL."report_spam.json";
    twitter_process($request, $post_data);

    //Where should we return the user to?  Back to the user
    twitter_refresh("confirmed/spam/{$user}");
}


function twitter_confirmation_page($query) 
{
    // the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
    $action = $query[1];
    $target = $query[2];    //The name of the user we are doing this action on
    $target_id = $query[3];    //The targets's ID.  Needed to check if they are being blocked.

  switch ($action) {
    case 'block':
      if (twitter_block_exists($target_id)) //Is the target blocked by the user?
      {
  $action = 'unblock';
  $content  = "<p>Are you really sure you want to <strong>Unblock $target</strong>?</p>";
  $content .= '<ul><li>They will see your updates on their home page if they follow you again.</li><li>You <em>can</em> block them again if you want.</li></ul>';  
      }
      else
      {
  $content = "<p>Are you really sure you want to <strong>$action $target</strong>?</p>";
  $content .= "<ul><li>You won't show up in their list of friends</li><li>They won't see your updates on their home page</li><li>They won't be able to follow you</li><li>You <em>can</em> unblock them but you will need to follow them again afterwards</li></ul>";
      }
      break;

    case 'delete':
      $content = '<p>Are you really sure you want to delete your tweet?</p>';
      $content .= "<ul><li>Tweet ID: <strong>$target</strong></li><li>There is no way to undo this action.</li></ul>";
      break;

    case 'deleteDM':
      $content = '<p>Are you really sure you want to delete that DM?</p>';
      $content .= "<ul><li>Tweet ID: <strong>$target</strong></li><li>There is no way to undo this action.</li><li>The DM will be deleted from both the sender's outbox <em>and</em> receiver's inbox.</li></ul>";
      break;

    case 'spam':
      $content  = "<p>Are you really sure you want to report <strong>$target</strong> as a spammer?</p>";
      $content .= "<p>They will also be blocked from following you.</p>";
      break;

  }

  $content .= "<form action='$action/$target' method='post'>
  <div>
      <button type='submit'>Yes please</button>
      </div>
    </form>";
  theme('Page', 'Confirm', $content);
}

function twitter_confirmed_page($query)
{
        // the URL /confirm can be passed parameters like so /confirm/param1/param2/param3 etc.
        $action = $query[1]; // The action. block, unblock, spam
        $target = $query[2]; // The username of the target

        switch ($action) {
                case 'block':
                        $content = "<p>Bye-bye @$target - you are now <strong>blocked</strong>.</p>";
                        break;
                case 'unblock':
                        $content = "<p>Hello again @$target - you have been <strong>unblocked</strong>.</p>";
                        break;
                case 'spam':
                        $content = "<p>Yum! Yum! Yum! Delicious spam! Goodbye @$target.</p>";
                        break;
        }
        theme ('Page', 'Confirmed', $content);
}

function twitter_friends_page($query) {
  $user = $query[1];
  if (!$user) {
    user_ensure_authenticated();
    $user = user_current_username();
  }
  $request = API_URL."statuses/friends/{$user}.xml";
  $tl = lists_paginated_process($request);
  $content = theme('followers', $tl);
  theme('page', 'Friends', $content);
}

function twitter_followers_page($query) {
  $user = $query[1];
  if (!$user) {
    user_ensure_authenticated();
    $user = user_current_username();
  }
  $request = API_URL."statuses/followers/{$user}.xml";
  $tl = lists_paginated_process($request);
  $content = theme('followers', $tl);
  theme('page', 'Followers', $content);
}

function twitter_blockings_page($query) {
  $request = API_URL.'blocks/blocking.json?page='.intval($_GET['page']).'&include_entities=true';
  $tl = twitter_process($request);
  $content = theme('blockings', $tl);
  theme('page', 'Blockings', $content);
}

//  Shows every user who retweeted a specific status
function twitter_retweeters_page($tweet) {
  $id = $tweet[1];
  $request = API_URL."statuses/{$id}/retweeted_by.xml";
  $tl = lists_paginated_process($request);
  $content = theme('retweeters', $tl);
  theme('page', "Everyone who retweeted {$id}", $content);
}

function twitter_update() {
  twitter_ensure_post_action();
  $status = twitter_url_shorten(stripslashes(trim($_POST['status'])));
  $statusArr = array();
  if ($status) {
    $status = line_united($status);
    $length = function_exists('mb_strlen') ? mb_strlen($status, 'utf-8') : strlen(utf8_decode($status));
    if ($length > 140) {
        switch (setting_fetch('longtext', 'r')) {
            case 'a':
                $statusArr[] = sysSubStr($status,140,true);
                break;
            case 'd':
                $num = ceil($length / 100);
                for ($i=0;$i<$num;$i++) {
                    $cnum = $i + 1;
                    $cstart = 100 * $i;
                    $cend = 100 * ($i + 1);
                    $statusArr[] = "($cnum/$num) ".mb_substr($status, $cstart, $cend, 'utf-8');
                }
                arsort($statusArr);
                break;
            case 'r':
                $statusArr[] = $status;
                break;
        }
    } else {
        $statusArr[] = $status;
    }
    $request = API_URL.'statuses/update.json';
    foreach ($statusArr as $status) {
        $post_data = array('source' => 'dabr', 'status' => $status);
        $in_reply_to_id = (string) $_POST['in_reply_to_id'];
        if (is_numeric($in_reply_to_id)) {
            $post_data['in_reply_to_status_id'] = $in_reply_to_id;
        }
        if (setting_fetch('buttongeo') == 'yes') {
            // Geolocation parameters
            list($lat, $long) = explode(',', $_POST['location']);
            $geo = 'N';
            if (is_numeric($lat) && is_numeric($long)) {
                $geo = 'Y';
                $post_data['lat'] = $lat;
                $post_data['long'] = $long;
                // $post_data['display_coordinates'] = 'false';
                // Turns out, we don't need to manually send a place ID
/*              $place_id = twitter_get_place($lat, $long);
                if ($place_id) {
                    // $post_data['place_id'] = $place_id;
                }
*/
            }
        setcookie_year('geo', $geo);
        }
    $b = twitter_process($request, $post_data);
    }
  }
  twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}

function twitter_get_place($lat, $long) {
        // http://dev.twitter.com/doc/get/geo/reverse_geocode
        // http://api.twitter.com/version/geo/reverse_geocode.format 

        // This will look up a place ID based on lat / long.
        // Not needed (Twitter include it automagically
        // Left in just incase we ever need it...
        $request = API_URL.'geo/reverse_geocode.json';
        $request .= '?lat='.$lat.'&long='.$long.'&max_results=1';

        $locations = twitter_process($request);
        $places = $locations->result->places;
        foreach($places as $place)
        {
                if ($place->id) 
                {
                        return $place->id;
                }
        }
        return false;
}

function twitter_retweet($query) {
  twitter_ensure_post_action();
  $id = $query[1];
  if (is_numeric($id)) {
    $request = API_URL.'statuses/retweet/'.$id.'.xml';
    twitter_process($request, true);
  }
  twitter_refresh($_POST['from'] ? $_POST['from'] : '');
}

function twitter_replies_page() {
  $count = setting_fetch('tpp', 20);
  $request = API_URL."statuses/mentions.json?count=$count&page=".intval($_GET['page']).'&include_entities=true';
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'replies');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', 'Replies', $content);
}

function twitter_retweets_page() {
  $count = setting_fetch('tpp', 20);
  $request = API_URL."statuses/retweeted_to_me.json?count=$count&page=".intval($_GET['page']).'&include_entities=true';
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'retweets');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', 'Retweets', $content);
}

function twitter_retweeted_page() {
  $request = API_URL.'statuses/retweets_of_me.json?page='.intval($_GET['page']).'&include_entities=true';
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'retweeted');
  $content = theme('status_form');
  $content .= theme('timeline',$tl);
  theme('page', 'Retweeted', $content);
}

function twitter_directs_page($query) {
  $action = strtolower(trim($query[1]));
  switch ($action) {
    case 'create':
      $to = $query[2];
      $content = theme('directs_form', $to);
      theme('page', 'Create DM', $content);
    
    case 'send':
      twitter_ensure_post_action();
      $to = trim(stripslashes($_POST['to']));
      $message = trim(stripslashes($_POST['message']));
      $request = API_URL.'direct_messages/new.json';
      twitter_process($request, array('user' => $to, 'text' => $message));
      twitter_refresh('directs/sent');
    
    case 'sent':
      $request = API_URL.'direct_messages/sent.json?page='.intval($_GET['page']).'&include_entities=true';
      $tl = twitter_standard_timeline(twitter_process($request), 'directs_sent');
      $content = theme_directs_menu();
      $content .= theme('timeline', $tl);
      theme('page', 'DM Sent', $content);

    case 'inbox':
    default:
      $request = API_URL.'direct_messages.json?page='.intval($_GET['page']).'&include_entities=true';
      $tl = twitter_standard_timeline(twitter_process($request), 'directs_inbox');
      $content = theme_directs_menu();
      $content .= theme('timeline', $tl);
      theme('page', 'DM Inbox', $content);
  }
}

function theme_directs_menu() {
  return '<p><a href="directs/create">Create</a> | <a href="directs/inbox">Inbox</a> | <a href="directs/sent">Sent</a></p>';
}

function theme_directs_form($to) {
  if ($to) {

    if (friendship_exists($to) != 1)
    {
        if (strtolower($to) == strtolower(user_current_username()))
        {
    $html_to = "<em>Warning:</em> <span class='textb'>" . $to . "</span> is yourself. Would you like to send a Direct Message to yourself ? XD<br/>";
        } else {
  $html_to = "<em>Warning:</em> <span class='textb'>" . $to . "</span> is not following you. You cannot send them a Direct Message :-(<br/>";
        }
    }
    $html_to .= "Sending direct message to <span class='textb'>$to</span><input name='to' value='$to' type='hidden' />";
  } else {
    $html_to .= "To: <input name='to' /><br />Message:";
  }
   $content = "<form action='directs/send' method='post'><div>$html_to<br /><textarea name='message' rows='3' cols='60' id='message'></textarea><br /><button type='submit'>Send</button><span id='remaining'>140</span></div></form>";
   $content .= js_counter("message");
   return $content;
}

function twitter_search_page() {
  $search_query = $_GET['query'];
  $content = theme('search_form', $search_query);
  if (isset($_POST['query'])) {
    $duration = time() + (3600 * 24 * 365);
    setcookie('search_favourite', $_POST['query'], $duration, '/');
    twitter_refresh('search');
  }
  if (!isset($search_query) && array_key_exists('search_favourite', $_COOKIE)) {
    $search_query = $_COOKIE['search_favourite'];
  }
  if ($search_query) {
    $tl = twitter_search($search_query);
    if ($search_query !== $_COOKIE['search_favourite']) {
      $content .= '<form action="search/bookmark" method="post"><div><input type="hidden" name="query" value="'.$search_query.'" /><button type="submit">Save as default search</button></div></form>';
    }
    $content .= theme('timeline', $tl);
  }
  theme('page', 'Search', $content);
}

function twitter_search($search_query) {
  $page = (int) $_GET['page'];
  if ($page == 0) $page = 1;
  $request = APIS_URL.'search.json?result_type=recent&q=' . urlencode($search_query).'&page='.$page.'&include_entities=true';
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl->results, 'search');
  return $tl;
}

function twitter_find_tweet_in_timeline($tweet_id, $tl) {
    // Parameter checks
    if (!is_numeric($tweet_id) || !$tl) return;
    
    // Check if the tweet exists in the timeline given
    if (array_key_exists($tweet_id, $tl)) {
  // Found the tweet
  $tweet = $tl[$tweet_id];
    } else {
  // Not found, fetch it specifically from the API
  $request = API_URL."statuses/show/{$tweet_id}.json?include_entities=true";
  $tweet = twitter_process($request);
    }
    return $tweet;
}

function twitter_user_page($query) 
{
    $screen_name = $query[1];
    $subaction = $query[2];
    $in_reply_to_id = (string) $query[3];
    $content = '';
    
    if (!$screen_name) theme('error', 'No username given');
    
    // Load up user profile information and one tweet
    $user = twitter_user_info($screen_name);
    
    // If the user has at least one tweet
    if (isset($user->status)) {
  // Fetch the timeline early, so we can try find the tweet they're replying to
  $request = API_URL."statuses/user_timeline.json?screen_name={$screen_name}&include_rts=true&include_entities=true&page=".intval($_GET['page']);
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'user');
    }
    
    // Build an array of people we're talking to
    $to_users = array($user->screen_name);
    
    // Are we replying to anyone?
    if (is_numeric($in_reply_to_id)) {
  $tweet = twitter_find_tweet_in_timeline($in_reply_to_id, $tl);

    // Create an array containing all URLs
    $urls = Twitter_Extractor::create($tweet->text)
                            ->extractURLs();

    $out = $tweet->text;

    // Hyperlink the URLs (target _blank
    if (setting_fetch('gwt') == 'on') // If the user wants links to go via GWT 
    {
        foreach($urls as $url) 
        {

            if (setting_fetch('longurl') == 'yes' && LONG_URL == 'ON'){
                $lurl = long_url($url);
            } else {
                $lurl = $url;
            }
            $encoded = urlencode($lurl);
            $atext = link_trans($lurl);
            $out = str_replace($url, "<a href='http://google.com/gwt/n?u={$encoded}' rel='external nofollow noreferrer'>{$atext}</a>", $out);
        }
    } else {
        $out = Twitter_Autolink::create($out)
                                        ->setTarget('')
                                        ->setTag('')
                                        ->addLinksToURLs();
        foreach($urls as $url) 
        {
            if (setting_fetch('longurl') == 'yes' && LONG_URL == 'ON'){
                $lurl = long_url($url);
                $out = str_replace('href="'.$url.'"', 'href="'.$lurl.'"', $out);
            } else {
                $lurl = $url;
            }
            $atext = link_trans($lurl);
            $out = str_replace(">{$url}</a>", ">{$atext}</a>", $out);
        }
    }

                // Hyperlink the @ and lists
    $out = Twitter_Autolink::create($out)
                                    ->setTarget('')
                                    ->setTag('')
                                    ->addLinksToUsernamesAndLists();

    // Hyperlink the #
    $out = Twitter_Autolink::create($out)
                                    ->setTarget('')
                                    ->addLinksToHashtags();

    $content .= "<p>In reply to:<br />{$out}</p>";


  if ($subaction == 'replyall') {
      $found = Twitter_Extractor::create($tweet->text)
              ->extractMentionedUsernames();
      $to_users = array_unique(array_merge($to_users, $found));
  }
    }

    // Build a status message to everyone we're talking to
    $status = '';
    foreach ($to_users as $username) {
  if (!user_is_current_user($username)) {
      $status .= "@{$username} ";
  }
    }

    $content .= theme('status_form', $status, $in_reply_to_id);
    $content .= theme('user_header', $user);
    $content .= theme('timeline', $tl);

    theme('page', "User {$screen_name}", $content);
}

function twitter_favourites_page($query) {
  $screen_name = $query[1];
  if (!$screen_name) {
    user_ensure_authenticated();
    $screen_name = user_current_username();
  }
  $request = API_URL."favorites/{$screen_name}.json?page=".intval($_GET['page']).'&include_entities=true';
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'favourites');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', 'Favourites', $content);
}

function twitter_mark_favourite_page($query) {
  $id = (string) $query[1];
  if (!is_numeric($id)) return;
  if ($query[0] == 'unfavourite') {
    $request = API_URL."favorites/destroy/$id.json";
  } else {
    $request = API_URL."favorites/create/$id.json";
  }
  twitter_process($request, true);
  twitter_refresh();
}

function twitter_home_page() {
  user_ensure_authenticated();
  $count = setting_fetch('tpp', 20);
//$request = API_URL."statuses/home_timeline.json?count=$count&include_rts=true&page=".intval($_GET['page']);
  $request = API_URL."statuses/home_timeline.json?count=$count&include_rts=true&include_entities=true";
    if ($_GET['max_id'])
    {
      $request .= '&max_id='.$_GET['max_id'];
    }

    if ($_GET['since_id'])
    {
      $request .= '&since_id='.$_GET['since_id'];
    }
//echo $request;
  $tl = twitter_process($request);
  $tl = twitter_standard_timeline($tl, 'friends');
  $content = theme('status_form');
  $content .= theme('timeline', $tl);
  theme('page', 'Home', $content);
}

function twitter_hashtag_page($query) {
  if (isset($query[1])) {
    $hashtag = '#'.$query[1];
    $content = theme('status_form', $hashtag.' ');
    $tl = twitter_search($hashtag);
    $content .= theme('timeline', $tl);
    theme('page', $hashtag, $content);
  } else {
    theme('page', 'Hashtag', 'Hash hash!');
  }
}

function theme_status_form($text = '', $in_reply_to_id = NULL) {
  if (user_is_authenticated()) {
    $fixedtagspre = setting_fetch('fixedtagspre');
    $fixedtagspost = setting_fetch('fixedtagspost');
    $fixedtagspre = (!empty($fixedtagspre) && (setting_fetch('fixedtagspreo', 'no') == "yes") && ($text == '')) ? $fixedtagspre." " : NULL;
    $fixedtagspost = (!empty($fixedtagspost) && (setting_fetch('fixedtagsposto', 'no') == "yes") && ($text == '')) ? " ".$fixedtagspost : NULL;
    $text = $fixedtagspre.$text.$fixedtagspost;
    $output = "<form method='post' action='update'><fieldset><legend>What's Happening?</legend><div><input name='status' value='{$text}' maxlength='140' /> <input name='in_reply_to_id' value='{$in_reply_to_id}' type='hidden' /><button type='submit'>Tweet</button>";
    if (setting_fetch('buttongeo') == 'yes') {
      $output .= '<div><span id="geo" style="display: none;"><input onclick="goGeo()" type="checkbox" id="geoloc" name="location" /> <label for="geoloc" id="lblGeo"></label></span>
  <script type="text/javascript">
<!--
started = false;
chkbox = document.getElementById("geoloc");
if (navigator.geolocation) {
    geoStatus("Tweet my location");
    if ("'.$_COOKIE['geo'].'"=="Y") {
        chkbox.checked = true;
        goGeo();
    }
}
function goGeo(node) {
    if (started) return;
    started = true;
    geoStatus("Locating...");
    navigator.geolocation.getCurrentPosition(geoSuccess, geoStatus);
}
function geoStatus(msg) {
    document.getElementById("geo").style.display = "inline";
    document.getElementById("lblGeo").innerHTML = msg;
}
function geoSuccess(position) {
    geoStatus("Tweet my <a href=\'http://maps.google.com/maps?q=loc:" + position.coords.latitude + "," + position.coords.longitude + "\' target=\'blank\'>location</a>");
    chkbox.value = position.coords.latitude + "," + position.coords.longitude;
}
//-->
  </script></div>';
    }
  $output .= "</div></fieldset></form>";
    return $output ;
  }
}

function theme_status($status) {
        //32bit int / snowflake patch
        if($status->id_str) $status->id = $status->id_str;

  $time_since = theme('status_time_link', $status);
  $parsed = twitter_parse_tags($status->text, $status->entities);
  $avatar = theme('avatar', theme_get_avatar($status->user), htmlspecialchars($status->user->name, ENT_QUOTES, 'UTF-8'));

  $out = theme('status_form', "@{$status->user->screen_name} ");
  $out .= "<div class='timeline'>\n";
  $out .= "<div class='tweet odd'>\n";
  if ($avatar) {
  $out .= "<span class='avatar'>$avatar</span>\n";
  }
  $out .= "<span class='status shift'><span class='textb'><a href='user/{$status->user->screen_name}'>{$status->user->screen_name}</a></span> $time_since<br />$parsed</span>\n";
  $out .= "</div>\n";
  $out .= "</div>\n";
  if (user_is_current_user($status->user->screen_name)) {
    $out .= "<form action='delete/{$status->id_str}' method='post'><div><button type='submit'>Delete without confirmation</button></div></form>";
  }
  return $out;
}

function theme_retweet($status) 
{
  $rtsyntax = setting_fetch('rtsyntax', 'RT [User]: [Content]');
  $replace = array(
    "[User]" => "@{$status->user->screen_name}",
    "[Content]" => "{$status->text}",
  );
  $text = str_replace(array_keys($replace),array_values($replace),$rtsyntax);

    $length = function_exists('mb_strlen') ? mb_strlen($text,'UTF-8') : strlen(utf8_decode($text));
    $from = substr($_SERVER['HTTP_REFERER'], strlen(BASE_URL));
    
    if($status->user->protected == 0)
    {
  $content.="<p>Twitter's new style retweet:</p>
    <form action='twitter-retweet/{$status->id_str}' method='post'>
    <div>
      <input type='hidden' name='from' value='$from' />
      <button type='submit'>Twitter Retweet</button>
      </div>
    </form>
    <hr />";
    }
    else
    {
  $content.="<p>@{$status->user->screen_name} doesn't allow you to retweet them. You will have to use the  use the old style editable retweet</p>";
    }

    $content .= "<p>Old style editable retweet:</p>
    <form action='update' method='post'>
    <div>
      <input type='hidden' name='from' value='$from' />
      <textarea name='status' rows='3' cols='60' id='status'>$text</textarea>
      <br/>
      <button id='submit' type='submit'>Retweet</button>
      <span id='remaining'>" . (140 - $length) ."</span>
      </div>
    </form>";
    $content .= js_counter("status");  
    if (setting_fetch('browser') == 'desktop') {
    $content .= "<script type='text/javascript'>
    <!--
    document.getElementById('status').onkeydown=function(b){var a=null;a=window.event?window.event:b;a!=null&&a.ctrlKey&&a.keyCode==13&&document.getElementById('submit').click()};
    //-->
    </script>";
    }
    return $content;
}

function twitter_tweets_per_day($user, $rounding = 1) {
  // Helper function to calculate an average count of tweets per day
  $days_on_twitter = (time() - strtotime($user->created_at)) / 86400;
  return round($user->statuses_count / $days_on_twitter, $rounding);
}

function theme_user_header($user) {
    $following = friendship($user->screen_name);
    $followed_by = $following->relationship->target->followed_by; //The $user is followed by the authenticating 
    $following = $following->relationship->target->following;
  $name = theme('full_name', $user);
  $full_avatar = str_replace('_normal.', '.', theme_get_avatar($user));
  $link = (isset($user->url)) ? theme('external_link', $user->url) : $user->url;
  //Some locations have a prefix which should be removed (UbertTwitter and iPhone)
  //Sorry if my PC has converted from UTF-8 with the U (artesea)
  $cleanLocation = str_replace(array("iPhone: ","üT: "),"",$user->location);
  $raw_date_joined = strtotime($user->created_at);
  $date_joined = date('jS M Y', $raw_date_joined);
  $tweets_per_day = twitter_tweets_per_day($user, 1);
  $bio = twitter_parse_tags($user->description);
  $username = user_current_username();
   $out = "<div class='profile'>";
  if (setting_fetch('avataro', 'yes') !== 'yes') {
   $out .= "<span class='avatar'>".theme('external_link', $full_avatar, theme('avatar', theme_get_avatar($user), htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8')), $name)."</span>";
   }
  $out .= "<span class='status shift'><span class='textb'>{$name}</span><br />";
  $out .= "<span class='about'>";
  if ($user->verified == true) {
  $out .= '<strong>Verified Account</strong><br />';
  }
  if ($user->protected == true) {
  $out .= '<strong>Private/Protected Tweets</strong><br />';
  }
  $out .= "Bio: {$bio}<br />";
  $out .= (empty($user->url)) ? "Link: No link to display.<br />" : "Link: {$link}<br />";
  $out .= (empty($user->location)) ? "Location: No location to display.<br />" : "Location: <a href=\"http://maps.google.com/m?q={$cleanLocation}\" rel=\"external nofollow noreferrer\">{$user->location}</a><br />";
  $out .= "Joined: {$date_joined} (~" . pluralise('tweet', $tweets_per_day, true) . " per day)";
  if (strtolower($user->screen_name) !== strtolower(user_current_username())) {
    $out .= "<br /><strong>{$user->screen_name} ";
        if ($following == true) {
            $out .= "follows";
        } else {
            $out .= "does not follow";
        }
    $out .= " {$username}</strong>";
  }
  $out .= "</span></span>";
  $out .= "<div class='features'>";
  $out .= pluralise('tweet', $user->statuses_count, true);

  //If the authenticated user is not following the protected used, the API will return a 401 error when trying to view friends, followers and favourites
  //This is not the case on the Twitter website
  //To avoid the user being logged out, check to see if she is following the protected user. If not, don't create links to friends, followers and favourites
  if ($user->protected == true && $followed_by == false)
  {
     $out .= " | " . pluralise('follower', $user->followers_count, true);
     $out .= " | " . pluralise('friend', $user->friends_count, true);
     $out .= " | " . pluralise('favourite', $user->favourites_count, true);
  }
  else
  {
     $out .= " | <a href='followers/{$user->screen_name}'>" . pluralise('follower', $user->followers_count, true) . "</a>";
     $out .= " | <a href='friends/{$user->screen_name}'>" . pluralise('friend', $user->friends_count, true) . "</a>";
     $out .= " | <a href='favourites/{$user->screen_name}'>" . pluralise('favourite', $user->favourites_count, true) . "</a>";
  }
  
    //NB we can tell if the user can be sent a DM $following->relationship->target->following;
    //Would removing this link confuse users?
    
    //Deprecated http://apiwiki.twitter.com/Twitter-REST-API-Method%3A-users%C2%A0show
    //if ($user->following !== true) 
     if (strtolower($user->screen_name) !== strtolower(user_current_username())) {
    if ($followed_by == false)
    {
  $out .= " | <a href='follow/{$user->screen_name}'>Follow</a>";
    } 
    else 
    {
  $out .= " | <a href='unfollow/{$user->screen_name}'>Unfollow</a>";
    }
  
    //We need to pass the User Name and the User ID.  The Name is presented in the UI, the ID is used in checking
    $out.= " | <a href='confirm/block/{$user->screen_name}/{$user->id}'>Block | Unblock</a>";

    
    /*
    //This should work, but it doesn't. Grrr.
    $blocked = $following->relationship->source->blocking; //The $user is blocked by the authenticating 
    if ($blocked == true)
    {
  $out.= " | <a href='confirm/block/{$user->screen_name}/{$user->id}'>Unblock</a>";
    }
    else
    {
  $out.= " | <a href='confirm/block/{$user->screen_name}/{$user->id}'>Block</a>";
    }
    */
    
    $out.= " | <a href='confirm/spam/{$user->screen_name}/{$user->id}'>Report Spam</a>";
    } else {
    $out .= " | <a href='profile'>Update Profile</a>";
    }
    
    $out .= " | <a href='lists/{$user->screen_name}'>" . pluralise('list', $user->listed_count, true) . "</a>";
    
    if ($following == true && $followed_by == true || strtolower($user->screen_name) == strtolower(user_current_username())) {
    $out .= " | <a href='directs/create/{$user->screen_name}'>Direct Message</a>";
    }
    
    $out .= " | <a href='search?query=%40{$user->screen_name}'>Search @{$user->screen_name}</a>";
    $out .= "</div></div>";
  return $out;
}

function theme_avatar($url, $name='', $force_large = false) {
    if (setting_fetch('avataro', 'yes') !== 'yes') {
    $size = $force_large ? 48 : 24;
    $force_large || $url = str_replace('_normal.', '_mini.', $url);
  return "<img class='shead' alt='$name' src='$url' height='$size' width='$size' />";
    } else {
  return '';
    }
}

function theme_status_time_link($status, $is_link = true) {
  $time = strtotime($status->created_at);
  if ($time > 0) {
    if (twitter_date('dmy') == twitter_date('dmy', $time) && !setting_fetch('timestamp')) {
      $out = format_interval(time() - $time, 1). ' ago';
    } else {
      $out = twitter_date('H:i', $time);
    }
  } else {
    $out = $status->created_at;
  }
  if ($is_link) {
    $out = "<a href='status/{$status->id}' class='time'>$out</a>";
    }
  if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'bigtouch')) {
    return $out;
  } else {
    return strip_tags($out);
  }
}


function twitter_date($format, $timestamp = null) {
/*
  static $offset;
  if (!isset($offset)) {
    if (user_is_authenticated()) {
      if (array_key_exists('utc_offset', $_COOKIE)) {
  $offset = $_COOKIE['utc_offset'];
      } else {
  $user = twitter_user_info();
  $offset = $user->utc_offset;
  setcookie('utc_offset', $offset, time() + 3000000, '/');
      }
    } else {
      $offset = 0;
    }
  }
*/
$offset = setting_fetch('utc_offset', 0) * 3600;
  if (!isset($timestamp)) {
    $timestamp = time();
  }
  return gmdate($format, $timestamp + $offset);
}

function twitter_standard_timeline($feed, $source) {
  $output = array();
  if (!is_array($feed) && $source != 'thread') return $output;

  //32bit int / snowflake patch
  if (is_array($feed)) {
    foreach($feed as $key => $status) {
      if($status->id_str) {
        $feed[$key]->id = $status->id_str;
      }
      if($status->in_reply_to_status_id_str) {
        $feed[$key]->in_reply_to_status_id = $status->in_reply_to_status_id_str;
      }
      if($status->retweeted_status->id_str) {
        $feed[$key]->retweeted_status->id = $status->retweeted_status->id_str;
      }
      // 2011/02/09 Kars adds these codes.
      if($status->retweeted_status->in_reply_to_status_id_str) {
        $feed[$key]->retweeted_status->in_reply_to_status_id = $status->retweeted_status->in_reply_to_status_id_str;
      }
    }
  }

  switch ($source) {
    case 'favourites':
    case 'friends':
    case 'replies':
    case 'retweets':
    case 'retweeted':
    case 'user':
      foreach ($feed as $status) {
  $new = $status;
  if ($new->retweeted_status) {
    $retweet = $new->retweeted_status;
    unset($new->retweeted_status);
    $retweet->retweeted_by = $new;
    $retweet->original_id = $new->id;
    $new = $retweet;
  }
  $new->from = $new->user;
  unset($new->user);
  $output[(string) $new->id] = $new;
      }
      return $output;
    
    case 'search':
      foreach ($feed as $status) {
  $output[(string) $status->id] = (object) array(
    'id' => $status->id,
    'text' => $status->text,
    'source' => strpos($status->source, '&lt;') !== false ? html_entity_decode($status->source) : $status->source,
    'from' => (object) array(
      'id' => $status->from_user_id,
      'screen_name' => $status->from_user,
      'profile_image_url' => theme_get_avatar($status),
    ),
    'to' => (object) array(
      'id' => $status->to_user_id,
      'screen_name' => $status->to_user,
    ),
    'created_at' => $status->created_at,
    'geo' => $status->geo,
  );
      }
      return $output;
    
    case 'directs_sent':
    case 'directs_inbox':
      foreach ($feed as $status) {
  $new = $status;
  if ($source == 'directs_inbox') {
    $new->from = $new->sender;
    $new->to = $new->recipient;
  } else {
    $new->from = $new->recipient;
    $new->to = $new->sender;
  }
  unset($new->sender, $new->recipient);
  $new->is_direct = true;
  $output[$new->id_str] = $new;
      }
      return $output;
    
    case 'thread':
      // First pass: extract tweet info from the HTML
      $html_tweets = explode('</li>', $feed);
      foreach ($html_tweets as $tweet) {
  $id = preg_match_one('#msgtxt(\d*)#', $tweet);
  if (!$id) continue;
  $output[$id] = (object) array(
    'id' => $id,
    'text' => strip_tags(preg_match_one('#</a>: (.*)</span>#', $tweet)),
    'source' => preg_match_one('#>from (.*)</span>#', $tweet),
    'from' => (object) array(
      'id' => preg_match_one('#profile_images/(\d*)#', $tweet),
      'screen_name' => preg_match_one('#twitter.com/([^"]+)#', $tweet),
      'profile_image_url' => preg_match_one('#src="([^"]*)"#' , $tweet),
    ),
    'to' => (object) array(
      'screen_name' => preg_match_one('#@([^<]+)#', $tweet),
    ),
    'created_at' => str_replace('about', '', preg_match_one('#info">\s(.*)#', $tweet)),
  );
      }
      // Second pass: OPTIONALLY attempt to reverse the order of tweets
      if (setting_fetch('reverse') == 'yes') {
  $first = false;
  foreach ($output as $id => $tweet) {
    $date_string = str_replace('later', '', $tweet->created_at);
    if ($first) {
      $attempt = strtotime("+$date_string");
      if ($attempt == 0) $attempt = time();
      $previous = $current = $attempt - time() + $previous;
    } else {
      $previous = $current = $first = strtotime($date_string);
    }
    $output[$id]->created_at = date('r', $current);
  }
  $output = array_reverse($output);
      }
      return $output;

    default:
      echo "<h1>$source</h1><pre>";
      print_r($feed); die();
  }
}

function preg_match_one($pattern, $subject, $flags = NULL) {
  preg_match($pattern, $subject, $matches, $flags);
  return trim($matches[1]);
}

function twitter_user_info($username = null) {
  if (!$username)
  $username = user_current_username();
  $request = API_URL."users/show.json?screen_name=$username&include_entities=true";
  $user = twitter_process($request);
  return $user;
}

function twitter_timeline_filter($input) {
    $filter_keywords = explode(" ",setting_fetch('filterc'));
    foreach ($filter_keywords as $filter_keyword) {
        if (stripos($input, $filter_keyword)) {
            return true;
        }
    }
    return false;
}

function theme_timeline($feed)
{
    if (count($feed) == 0) return theme('no_tweets');
    $rows = array();
    $page = menu_current_page();
    $date_heading = false;
    $first=0;

    // Add the hyperlinks *BEFORE* adding images
    foreach ($feed as &$status)
    {
        $status->text = twitter_parse_tags($status->text, $status->entities);
    }
    unset($status);

    //Add in images
    if (EMBEDLY_KEY !== '') {
        embedly_embed_thumbnails($feed);
    }

    foreach ($feed as $status)
    {
        if ($first==0)
        {
            $since_id = $status->id;
            $first++;
        }
        else
        {
            $max_id =  $status->id;
            if ($status->original_id)
            {
                $max_id =  $status->original_id;
            }
        }
        $time = strtotime($status->created_at);
        if ($time > 0)
        {
            $date = twitter_date('l jS F Y', strtotime($status->created_at));
            if ($date_heading !== $date)
            {
                $date_heading = $date;
                $rows[] = array('data'  => array($date), 'class' => 'date');
            }
        }
        else
        {
            $date = $status->created_at;
        }
        if ((setting_fetch('filtero', 'no') == 'yes') && twitter_timeline_filter($status->text)) {
            $text = "<a href='status/{$status->id}' class='filter'><span class='texts'>[Tweet Filtered]</span></a>";
        } else {
            $text = $status->text;
            setting_fetch('hide_inline') || $media = twitter_get_media($status);
        }
        if (setting_fetch('buttontime', 'yes') == 'yes') {
            $link = theme('status_time_link', $status, !$status->is_direct);
        }
        $actions = theme('action_icons', $status);
        $avatar = theme('avatar', theme_get_avatar($status->from), htmlspecialchars($status->from->name, ENT_QUOTES, 'UTF-8'));
        if (setting_fetch('buttonfrom', 'yes') == 'yes') {
            if ((substr($_GET['q'],0,4) == 'user') || (setting_fetch('browser') == 'touch') || (setting_fetch('browser') == 'desktop') || (setting_fetch('browser') == 'bigtouch')) {
                $source = $status->source ? " via ".str_replace('rel="nofollow"', 'rel="external nofollow noreferrer"', preg_replace('/&(?![a-z][a-z0-9]*;|#[0-9]+;|#x[0-9a-f]+;)/i', '&amp;', $status->source)) : ''; //need to replace & in links with &amps and force new window on links
            } else {
                $source = $status->source ? " via ".strip_tags($status->source) ."" : '';
            }
        } else {
            $source = NULL;
        }
        if ($status->place->name) {
            $source .= " " . $status->place->name . ", " . $status->place->country;
        }
        if ($status->in_reply_to_status_id)
        {
            $source .= " <a href='status/{$status->in_reply_to_status_id_str}'>in reply to {$status->in_reply_to_screen_name}</a>";
        }
        if ($status->retweet_count)     {
            $source .= " <a href='retweeted_by/{$status->id}'>retweeted ";
            switch($status->retweet_count) {
                case(1) : $source .= "once</a>"; break;
                case(2) : $source .= "twice</a>"; break;
                default : $source .= $status->retweet_count . " times</a>";
            }
        }
        if ($status->retweeted_by) {
            $retweeted_by = $status->retweeted_by->user->screen_name;
            $source .= "<br /><a href='retweeted_by/{$status->id}'>retweeted</a> by <a href='user/{$retweeted_by}'>{$retweeted_by}</a>";
        }
        $html = "<span class='textb'><a href='user/{$status->from->screen_name}'>{$status->from->screen_name}</a></span> $actions <span class='texts'>$link</span><br />{$text}<br />$media<span class='texts'>$source</span>";
        unset($row);
        $class = 'status';


        if ($page != 'user' && $avatar)
        {
        $row[] = array('data' => $avatar, 'class' => 'avatar');
        $class .= ' shift';
        }

        $row[] = array('data' => $html, 'class' => $class);

        $class = 'tweet';
        if ($page != 'replies' && twitter_is_reply($status))
        {
            $class .= ' reply';
        }
        $row = array('data' => $row, 'class' => $class);

        $rows[] = $row;
    }
    $content = theme('table', array(), $rows, array('class' => 'timeline'));
    //$content .= theme('pagination');
    if ($page != '')
    {
        $content .= theme('pagination');
    }
    else
    {
      //Doesn't work. since_id returns the most recent tweets up to since_id, not since. Grrr
      //$links[] = "<a href='{$_GET['q']}?since_id=$since_id'>Newer</a>";

        if(is_64bit()) $max_id = intval($max_id) - 1; //stops last tweet appearing as first tweet on next page
        $links[] = "<a href='{$_GET['q']}?max_id=$max_id' accesskey='9'>Older</a> 9";
        $content .= '<p>'.implode(' | ', $links).'</p>';
    }

    return $content;
}

function twitter_is_reply($status) {
        if (!user_is_authenticated()) {
                return false;
        }
        $user = user_current_username();

        // Use Twitter Entities to see if this contains a mention of the user
        if ($status->entities)  // If there are entities
        {
                $entities = $status->entities;
                foreach($entities->user_mentions as $mentions)
                {
                        if ($mentions->screen_name == $user) 
                        {
                                return true;
                        }
                }
                return false;
        }
        
        // If there are no entities (for example on a search) do a simple regex
        $found = Twitter_Extractor::create($status->text)->extractMentionedUsernames();
        foreach($found as $mentions)
        {
                // Case insensitive compare
                if (strcasecmp($mentions, $user) == 0)
                {
                        return true;
                }
        }
        return false;
}

function theme_followers($feed, $hide_pagination = false) {
  $rows = array();
  if (count($feed) == 0 || $feed == '[]') return '<p>No users to display.</p>';

    foreach ($feed->users->user as $user) {

   $name = theme('full_name', $user);
   $tweets_per_day = twitter_tweets_per_day($user);
       $last_tweet = strtotime($user->status->created_at);
    $content = "{$name}<br /><span class='about'>";
    if($user->description != "")
      $content .= "Bio: " . twitter_parse_tags($user->description) . "<br />";
    if($user->location != "")
      $content .= "Location: {$user->location}<br />";
    $content .= "Info: ";
    $content .= pluralise('tweet', $user->statuses_count, true) . ", ";
    $content .= pluralise('friend', $user->friends_count, true) . ", ";
    $content .= pluralise('follower', $user->followers_count, true) . ", ";
    $content .= "~" . pluralise('tweet', $tweets_per_day, true) . " per day<br />";
    $content .= "Last tweet: ";
    if($user->protected == 'true' && $last_tweet == 0)
      $content .= "Private";
    else if($last_tweet == 0)
      $content .= "Never tweeted";
    else
      $content .= twitter_date('l jS F Y', $last_tweet);
    $content .= "</span>";

  if (setting_fetch('avataro', 'yes') !== 'yes') {
    $rows[] = array('data' => array(array('data' => theme('avatar', theme_get_avatar($user), htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8')), 'class' => 'avatar'),
      array('data' => $content, 'class' => 'status shift')),
  'class' => 'tweet');
  } else {
    $rows[] = array('data' => array(array('data' => $content, 'class' => 'status shift')),
  'class' => 'tweet');
  }
      }
        $content = theme('table', array(), $rows, array('class' => 'followers'));
        if (!$hide_pagination)
        $content .= theme('list_pagination', $feed);
        return $content;
}

function theme_blockings($feed, $hide_pagination = false) {
  $rows = array();
  if (count($feed) == 0 || $feed == '[]') return '<p>No users to display.</p>';

    foreach ($feed as $user) {

   $name = theme('full_name', $user);
   $tweets_per_day = twitter_tweets_per_day($user);
       $last_tweet = strtotime($user->status->created_at);
    $content = "{$name}<br /><span class='about'>";
    if($user->description != "")
      $content .= "Bio: " . twitter_parse_tags($user->description) . "<br />";
    if($user->location != "")
      $content .= "Location: {$user->location}<br />";
    $content .= "Info: ";
    $content .= pluralise('tweet', $user->statuses_count, true) . ", ";
    $content .= pluralise('friend', $user->friends_count, true) . ", ";
    $content .= pluralise('follower', $user->followers_count, true) . ", ";
    $content .= "~" . pluralise('tweet', $tweets_per_day, true) . " per day<br />";
    $content .= "Last tweet: ";
    if($user->protected == 'true' && $last_tweet == 0)
      $content .= "Private";
    else if($last_tweet == 0)
      $content .= "Never tweeted";
    else
      $content .= twitter_date('l jS F Y', $last_tweet);
    $content .= "</span>";

  if (setting_fetch('avataro', 'yes') !== 'yes') {
    $rows[] = array('data' => array(array('data' => theme('avatar', theme_get_avatar($user), htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8')), 'class' => 'avatar'),
      array('data' => $content, 'class' => 'status shift')),
  'class' => 'tweet');
  } else {
    $rows[] = array('data' => array(array('data' => $content, 'class' => 'status shift')),
  'class' => 'tweet');
  }
      }
        $content = theme('table', array(), $rows, array('class' => 'followers'));
        if (!$hide_pagination)
        $content .= theme('list_pagination', $feed);
        return $content;
}

// Annoyingly, retweeted_by.xml and followers.xml are subtly different. 
// TODO merge theme_retweeters with theme_followers
function theme_retweeters($feed, $hide_pagination = false) {
        $rows = array();
        if (count($feed) == 0 || $feed == '[]') return '<p>No one has retweeted this status.</p>';

        foreach ($feed->user as $user) {

                $name = theme('full_name', $user);
                $tweets_per_day = twitter_tweets_per_day($user);
                /* $last_tweet = strtotime($user->status->created_at); */
                $content = "{$name}<br /><span class='about'>";
                if($user->description != "")
                        $content .= "Bio: " . twitter_parse_tags($user->description) . "<br />";
                if($user->location != "")
                        $content .= "Location: {$user->location}<br />";
                $content .= "Info: ";
                $content .= pluralise('tweet', $user->statuses_count, true) . ", ";
                $content .= pluralise('friend', $user->friends_count, true) . ", ";
                $content .= pluralise('follower', $user->followers_count, true) . ", ";
                $content .= "~" . pluralise('tweet', $tweets_per_day, true) . " per day<br />";
                $content .= "</span>";

  if (setting_fetch('avataro', 'yes') !== 'yes') {
                $rows[] = array('data' => array(array('data' => theme('avatar', theme_get_avatar($user), htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8')), 'class' => 'avatar'),
                          array('data' => $content, 'class' => 'status shift')),
                                'class' => 'tweet');
  } else {
    $rows[] = array('data' => array(array('data' => $content, 'class' => 'status shift')),
  'class' => 'tweet');
  }

        }

      $content = theme('table', array(), $rows, array('class' => 'followers'));
      if (!$hide_pagination)
   $content .= theme('list_pagination', $feed);
      return $content;
}

function theme_full_name($user) {
  $name = "<a href='user/{$user->screen_name}'>{$user->screen_name}</a>";
  //THIS IF STATEMENT IS RETURNING FALSE EVERYTIME ?!?
  //if ($user->name && $user->name != $user->screen_name) {
  if($user->name != "") {
    $name .= " ({$user->name})";
  }
  return $name;
}

// http://groups.google.com/group/twitter-development-talk/browse_thread/thread/50fd4d953e5b5229#
function theme_get_avatar($object) {
        // Are we calling the HTTPS API?        
        $pos = strpos(API_URL, "https");

        // Not useing HTTPS? Return the normal one
        if ($pos === false) {
                return $object->profile_image_url;
        }

        // Is there a secure image to return?
        if ($object->profile_image_url_https) {
                return $object->profile_image_url_https;
        }

        return $object->profile_image_url;
}

function theme_no_tweets() {
  return '<p>No tweets to display.</p>';
}

function theme_search_results($feed) {
  $rows = array();
  foreach ($feed->results as $status) {
    $text = twitter_parse_tags($status->text, $status->entities);
    $link = theme('status_time_link', $status);
    $actions = theme('action_icons', $status);

  if (setting_fetch('avataro', 'yes') !== 'yes') {
    $row = array(
      theme('avatar', theme_get_avatar($status), htmlspecialchars($status->name, ENT_QUOTES, 'UTF-8')),
      "<a href='user/{$status->from_user}'>{$status->from_user}</a> $actions - {$link}<br />{$text}",
    );
  } else {
    $row = array(
      "<a href='user/{$status->from_user}'>{$status->from_user}</a> $actions - {$link}<br />{$text}",
    );
  }
    if (twitter_is_reply($status)) {
      $row = array('class' => 'reply', 'data' => $row);
    }
    $rows[] = $row;
  }
  $content = theme('table', array(), $rows, array('class' => 'timeline'));
  $content .= theme('pagination');
  return $content;
}

function theme_search_form($query) {
  $query = stripslashes(htmlentities($query,ENT_QUOTES,"UTF-8"));
  return "<form action='search' method='get'><div><input name='query' value=\"$query\" /><button type='submit'>Search</button></div></form>";
}

function theme_external_link($url, $content = null) {
    //Long URL functionality.  Also uncomment function long_url($shortURL)
    if (setting_fetch('longurl') == 'yes' && LONG_URL == 'ON') {
    $lurl = long_url($url);
    } else {
    $lurl = $url;
    }
    if (!$content) {
  //Used to wordwrap long URLs
  //return "<a href='$url' target='_blank'>". wordwrap(long_url($url), 64, "\n", true) ."</a>";
    $atext = link_trans($lurl);
    return "<a href='$lurl' rel='external nofollow noreferrer'>$atext</a>";
    } else {
  return "<a href='$lurl' rel='external nofollow noreferrer'>$content</a>";
    }
}

function theme_pagination() 
{

    $page = intval($_GET['page']);
    if (preg_match('#&q(.*)#', $_SERVER['QUERY_STRING'], $matches)) 
    {
      $query = $matches[0];
    }
    if ($page == 0) $page = 1;
    $links[] = "<a href='{$_GET['q']}?page=".($page+1)."$query' accesskey='9'>Older</a> 9";
    if ($page > 1) $links[] = "<a href='{$_GET['q']}?page=".($page-1)."$query' accesskey='8'>Newer</a> 8";
    return '<p>'.implode(' | ', $links).'</p>';

/*
    if ($_GET['max_id'])
    {
      $id = intval($_GET['max_id']);
    }
    elseif ($_GET['since_id'])
    {
      $id = intval($_GET['since_id']);
    }
    else
    {
      $id = 17090863233;
    }

    $links[] = "<a href='{$_GET['q']}?max_id=$id' accesskey='9'>Older</a> 9";
    $links[] = "<a href='{$_GET['q']}?since_id=$id' accesskey='8'>Newer</a> 8";
    
    return '<p>'.implode(' | ', $links).'</p>';
    */
}

function theme_action_icons($status) {
    $from = $status->from->screen_name;
    $retweeted_by = $status->retweeted_by->user->screen_name;
    $retweeted_id = $status->retweeted_by->id;
    $geo = $status->geo;
    $actions = array();

    if (!$status->is_direct) {
        if (setting_fetch('buttonrl', 'yes') == 'yes' && setting_fetch('rl_user','') !== '' && setting_fetch('rl_pass','') !== '') {
            $actions[] = theme('action_icon', "status/{$status->id}/rl", 'images/instapaper.png', 'RL');
        }
        if (setting_fetch('buttonre', 'yes') == 'yes') {
            $actions[] = theme('action_icon', "user/{$from}/reply/{$status->id}", 'images/reply.png', 'AT');
        }
    }
    //Reply All functionality. 
    if(setting_fetch('buttonreall') == 'yes' && $status->entities->user_mentions)
    {
        $actions[] = theme('action_icon', "user/{$from}/replyall/{$status->id}", 'images/replyall.png', 'RE');
    }
    if (setting_fetch('buttondm') == 'yes') {
        if (!user_is_current_user($from)) {
            $actions[] = theme('action_icon', "directs/create/{$from}", 'images/dm.png', 'DM');
        }
    }
    if (!$status->is_direct) {
        if (setting_fetch('buttonfav', 'yes') == 'yes') {
            if ($status->favorited == '1') {
                $actions[] = theme('action_icon', "unfavourite/{$status->id}", 'images/star.png', 'UNFAV');
            } else {
                $actions[] = theme('action_icon', "favourite/{$status->id}", 'images/star_grey.png', 'FAV');
            }
        }
    if (setting_fetch('buttonrt', 'yes') == 'yes') {
        if ($retweeted_by) // Show a diffrent retweet icon to indicate to the user this is an RT
        {
            $actions[] = theme('action_icon', "retweet/{$status->id}", 'images/retweeted.png', 'RTED');
        } else {
            $actions[] = theme('action_icon', "retweet/{$status->id}", 'images/retweet.png', 'RT');
        }
    }
    if (setting_fetch('buttondel', 'yes') == 'yes') {
        if (user_is_current_user($from))
        {
            $actions[] = theme('action_icon', "confirm/delete/{$status->id}", 'images/trash.gif', 'DEL');
        }
        if ($retweeted_by) //Allow users to delete what they have retweeted
        {
            if (user_is_current_user($retweeted_by))
            {
                $actions[] = theme('action_icon', "confirm/delete/{$retweeted_id}", 'images/trash.gif', 'DEL');
            }
        }
    }
    } else {
        if (setting_fetch('buttondel', 'yes') == 'yes') {
            $actions[] = theme('action_icon', "confirm/deleteDM/{$status->id}", 'images/trash.gif', 'DEL');
        }
    }

    if (setting_fetch('buttonmap', 'yes') == 'yes') {
        if ($geo !== null) 
        {
            $latlong = $geo->coordinates;
            $lat = $latlong[0];
            $long = $latlong[1];
            $actions[] = theme('action_icon', "http://maps.google.com/m?q={$lat},{$long}", 'images/map.png', 'MAP');
        }
    }
    // Added for translation
    if (setting_fetch('buttontr', 'yes') == 'yes') {
        $actions[] = theme('action_icon', "status/{$status->id}/tr", 'images/translate.gif', 'TR');
    }

    if (setting_fetch('buttonot', 'yes') == 'yes') {
        $actions[] = theme('action_icon', "http://twitter.com/{$from}/status/{$status->id}", 'images/original.png', 'OT');
    }

    if (setting_fetch('buttonsearch', 'yes') == 'yes') {
        //Search for @ to a user
        $actions[] = theme('action_icon',"search?query=%40{$from}",'images/q.png','?');
    }

    return implode(' ', $actions);
}

function theme_action_icon($url, $image_url, $text) {
  // alt attribute left off to reduce bandwidth by about 720 bytes per page
  if (preg_match('/MAP|OT/i', $text))
    {
        return "<a href='$url' rel='external nofollow noreferrer'>$text</a>";
    }
return "<a href='$url'>$text</a>";
}

function pluralise($word, $count, $show = FALSE) {
  if($show) $word = "{$count} {$word}";
  return $word . (($count != 1) ? 's' : '');
}

function is_64bit() {
  $int = "9223372036854775807";
  $int = intval($int);
  return ($int == 9223372036854775807);
}

?>