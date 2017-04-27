<?php

/*
Syntax is 
'Name|links,bodybg,bodyt,small,odd,even,replyodd,replyeven,menubg,menut,menua',

Assembled in theme_css()
*/

$GLOBALS['colour_schemes'] = array(
    0 => 'Pretty In Pink|c06,fcd,623,c8a,fee,fde,ffa,dd9,c06,fee,fee',
    1 => '#Facebook Blue|3B5998,F7F7F7,000,555,D8DFEA,EEE,FFA,DD9,3B5998,FFF,FFF',
    2 => 'red|d12,F7F7F7,111,555,fff,FFD3D3,ffa,dd9,c12,fff,fff',
    3 => 'Digu Orange|b50,ddd,111,555,fff,eee,ffa,dd9,e81,c40,fff',
    4 => 'Fanfou Blue|13819F,E7F2F5,333,555,fff,E7F2F5,FFA,DD9,00CCFF,333,333',
    5 => 'Colorful|535F74,D1D0B4,000,555,FFEDED,FFD3D3,FFA,DD9,D33D3E,FFF,FFF',
    6 => 'Twitter Blue|1481B1,FFF,333,555,FFF,EEE,FFA,DD9,9AE4E8,333,333',
    7 => 'Whimsical Pink|c06,fcd,623,c8a,fee,fde,ffa,dd9,C06,fee,fee',
    8 => 'Green|293C03,ccc,000,555,fff,eee,CCE691,ACC671,495C23,919C35,fff',
    9 => 'Purple|BAAECB,1F1530,9C8BB5,6D617E,362D45,4C4459,4A423E,5E5750,191432,6D617E,6D617E',
    10 => 'Monokai Python|A6E22E,272822,66D8D4,56564D,3F403A,272822,383933,3A3B35,F92672,fff,fff',
    11 => 'Into the dark|EB6,484848,EEE,CCC,666,484848,676,888,95C90E,fff,fff',
    12 => 'TES|477725,fff,002200,649431,EEFFEE,fff,F1E1F9,FFE9D2,243411,fff,fff',
    13 => 'OS X Graphite|677686,F7F7F5,282828,677686,EBEBEB,F7F7F5,C2D6EB,C2D6EB,677686,fff,fff',
    14 => 'Twitter by jinwen|2674B2,BFDBE6,000,999,F7FCF6,D8ECF7,A9D0DF,8CBED5,002031,88D0DE,88D0DE',
    15 => 'Android|67753A,F0F0F0,2C3342,959F77,F5F5F5,E8F0CD,D1E29C,C5DB80,A4C639,FFF,FFF',
    16 => 'Python Twitter|53BEDF,005E7B,FFF5CA,2F9DBF,01799E,005E7B,01799E,005E7B,2D97BF,FFF,FFF',
);

menu_register(array(
    'settings' => array(
        'callback' => 'settings_page',
    ),
    'reset' => array(
        'hidden' => true,
        'callback' => 'cookie_monster',
    ),
));

function cookie_monster() {
    $cookies = array(
        'browser',
        'settings',
        'utc_offset',
        'search_favourite',
        'USER_AUTH',
        'rl_user',
        'rl_pass',
    );
    $duration = time() - 3600;
    foreach ($cookies as $cookie) {
        setcookie($cookie, NULL, $duration, '/');
        setcookie($cookie, NULL, $duration);
    }
    return theme('page', 'Cookie Monster', '<p>The cookie monster has logged you out and cleared all settings. Try logging in again now.</p>');
}

function setting_fetch($setting, $default = NULL) {
    $settings = (array) unserialize(base64_decode($_COOKIE['settings']));
    if (array_key_exists($setting, $settings)) {
        return $settings[$setting];
    } else {
        return $default;
    }
}

function setcookie_year($name, $value) {
    $duration = time() + (3600 * 24 * 365);
    setcookie($name, $value, $duration, '/');
}

function check_input($value) {
    // 去除斜杠
    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    // 如果不是数字则加引号
    if (!is_numeric($value)) {
        $value = "'" . mysql_real_escape_string($value) . "'";
    }
    return $value;
}

function settings_page($args) {
    if ($args[1] == 'save') {
        $settings['browser'] = $_POST['browser'];
        $settings['gwt'] = $_POST['gwt'];
        $settings['colours'] = $_POST['colours'];
        $settings['reverse'] = $_POST['reverse'];
        $settings['tpp'] = $_POST['tpp'];
        $settings['ltpp'] = $_POST['ltpp'];
        $settings['topuser'] = $_POST['topuser'];
        $settings['tophome'] = $_POST['tophome'];
        $settings['topreplies'] = $_POST['topreplies'];
        $settings['topretweets'] = $_POST['topretweets'];
        $settings['topretweeted'] = $_POST['topretweeted'];
        $settings['topdirects'] = $_POST['topdirects'];
        $settings['toppicture'] = $_POST['toppicture'];
        $settings['topsearch'] = $_POST['topsearch'];

        $settings['replies'] = $_POST['replies'];
        $settings['retweets'] = $_POST['retweets'];
        $settings['retweeted'] = $_POST['retweeted'];
        $settings['directs'] = $_POST['directs'];
        $settings['search'] = $_POST['search'];
        $settings['favourites'] = $_POST['favourites'];
        $settings['lists'] = $_POST['lists'];
        $settings['followers'] = $_POST['followers'];
        $settings['friends'] = $_POST['friends'];
        /*$settings['blockings'] = $_POST['blockings'];*/
        $settings['trends'] = $_POST['trends'];
        $settings['picture'] = $_POST['picture'];
        $settings['about'] = $_POST['about'];
        $settings['ssettings'] = $_POST['ssettings'];
        $settings['slogout'] = $_POST['slogout'];
        $settings['srefresh'] = $_POST['srefresh'];

        $settings['linktrans'] = $_POST['linktrans'];
        $settings['avataro'] = $_POST['avataro'];

        $settings['buttonrl'] = $_POST['buttonrl'];
        $settings['buttonre'] = $_POST['buttonre'];
        $settings['buttonreall'] = $_POST['buttonreall'];
        $settings['buttondm'] = $_POST['buttondm'];
        $settings['buttonfav'] = $_POST['buttonfav'];
        $settings['buttonrt'] = $_POST['buttonrt'];
        $settings['buttondel'] = $_POST['buttondel'];
        $settings['buttonmap'] = $_POST['buttonmap'];
        $settings['buttongeo'] = $_POST['buttongeo'];
        $settings['buttonot'] = $_POST['buttonot'];
        $settings['buttonsearch'] = $_POST['buttonsearch'];
        $settings['buttontime'] = $_POST['buttontime'];
        $settings['buttonfrom'] = $_POST['buttonfrom'];
        $settings['buttonend'] = $_POST['buttonend'];

        $settings['longurl'] = $_POST['longurl'];
        //$settings['showthumbs'] = $_POST['showthumbs'];
        $settings['fixedtagspre'] = $_POST['fixedtagspre'];
        $settings['fixedtagspreo'] = $_POST['fixedtagspreo'];
        $settings['fixedtagspost'] = $_POST['fixedtagspost'];
        $settings['fixedtagsposto'] = $_POST['fixedtagsposto'];
        $settings['rtsyntax'] = $_POST['rtsyntax'];
        $settings['timestamp']   = $_POST['timestamp'];
        $settings['hide_inline'] = $_POST['hide_inline'];
        $settings['utc_offset']  = (float)$_POST['utc_offset'];
        $settings['rl_user'] = $_POST['rl_user'];
        $settings['rl_pass'] = $_POST['rl_pass'];
        $settings['longtext'] = $_POST['longtext'];
        $settings['filtero'] = $_POST['filtero'];
        $settings['filterc'] = $_POST['filterc'];

        // Save a user's oauth details to a MySQL table
        if (ACCESS_USERS == 'MYSQL') {
            if ($newpass = $_POST['newpassword'] || $delpass = $_POST['delpass']) {
                user_is_authenticated();
                $username = strtolower(user_current_username());
                $con = @mysql_connect(MYSQL_URL, MYSQL_USER, MYSQL_PASSWORD) || theme('error', '<p>Error failed to connect your MySQL Database.</p>');
                @mysql_select_db(MYSQL_DB) || theme('error', '<p>Error failed to select your MySQL Database.</p>');
                if ($newpass = $_POST['newpassword']) {
                    list($key, $secret) = explode('|', $GLOBALS['user']['password']);
                    $sql = sprintf("REPLACE INTO user (username, oauth_key, oauth_secret, password) VALUES (%s, %s, %s, MD5(%s))", check_input($username), check_input($key), check_input($secret), check_input($newpass));
                    @mysql_query($sql) || theme('error', '<p>Error failed to save your OAuth Information into your MySQL Database.</p><p>Please check your MySQL Database.</p>');
                }
                if ($delpass = $_POST['delpass']) {
                    user_is_authenticated();
                    $del = "DELETE FROM user WHERE username = ".check_input($username);
                    @mysql_query($del) || theme('error', '<p>Error failed to delete your account.</p>');
                }
            }
        }

        // Save a user's oauth details to a file
        if (ACCESS_USERS == 'FILE') {
            if ($newpass = $_POST['newpassword'] || $delpass = $_POST['delpass']) {
                user_is_authenticated();
                $username = strtolower(user_current_username());
                $token = @glob(CACHE_FLODER.$username.'.*');
                if ($newpass = $_POST['newpassword']) {
                    list($key, $secret) = explode('|', $GLOBALS['user']['password']);
                    $user = array(
                        'password' => md5($newpass),
                        'oauth_key' => $key, 
                        'oauth_secret' => $secret);
                    if(!empty($token)) {
                        $str = $token[0];
                    } else {
                        for ($i=0; $i<15; $i++) {
                            $d=rand(1,30)%2;
                            $suffix .= $d ? chr(rand(65,90)) : chr(rand(48,57));
                        }
                        $str = CACHE_FLODER.$username.'.'.$suffix;
                    }
                    if(@file_put_contents($str,json_encode($user)) === FALSE) {
                        theme('error', '<p>Error failed to write access_token file.</p><p>Please check if you have write permission to cache directory.</p>');
                    }
                }
                if ($delpass = $_POST['delpass']) {
                    user_is_authenticated();
                    if(!empty($token)) {
                        unlink($token[0]);
                    } else {
                        theme('error', '<p>Error failed to delete access_token file.</p>');
                    }
                }
            }
        }

        setcookie_year('settings', base64_encode(serialize($settings)));
        twitter_refresh('');
    }

    $modes = array(
        'mobile' => 'Normal phone',
        'touch' => 'Touch Screen',
        'desktop' => 'PC/Laptop',
        'text' => 'Text only',
        'worksafe' => 'Work Safe',
        'bigtouch' => 'Touch Screen Big',
    );

    $gwt = array(
        'off' => 'direct',
        'on' => 'via GWT',
    );

    $linktrans = array(
        'o' => 'Full URL',
        'd' => 'Domain Only',
        'l' => '[link]',
    );

    $longtext = array(
        'a' => 'Automatic Cut',
        'd' => 'Split into 2+ tweets',
        'r' => 'Return Error',
    );

    $colour_schemes = array();
    foreach ($GLOBALS['colour_schemes'] as $id => $info) {
        list($name, $colours) = explode('|', $info);
        $colour_schemes[$id] = $name;
    }

    $utc_offset = setting_fetch('utc_offset', 0);
/* returning 401 as it calls http://api.twitter.com/1/users/show.json?screen_name= (no username???)     
        if (!$utc_offset) {
                $user = twitter_user_info();
                $utc_offset = $user->utc_offset;
        }
*/
    if ($utc_offset > 0) {
        $utc_offset = '+' . $utc_offset;
    }

    $content .= '<form action="settings/save" method="post">';
    $content .= '<p><label>Colour scheme:<br /><select name="colours">'.theme('options', $colour_schemes, setting_fetch('colours', 1)).'</select></label></p><hr />';
    $content .= '<p><label>Mode:<br /><select name="browser">'.theme('options', $modes, $GLOBALS['current_theme']).'</select></label></p><hr />';
    $content .= '<p>Configure Menu Items<br />';
    $content .= '<span class="texts">Choose what you want to display on the Top Bar.</span><br />';
    $content .= '<label><input type="checkbox" name="topuser" value="yes" '. (setting_fetch('topuser') == 'yes' ? ' checked="checked" ' : '') .' /> User</label><br />';
    $content .= '<label><input type="checkbox" name="tophome" value="yes" '. (setting_fetch('tophome', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Home</label><br />';
    $content .= '<label><input type="checkbox" name="topreplies" value="yes" '. (setting_fetch('topreplies', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Replies</label><br />';
    $content .= '<label><input type="checkbox" name="topretweets" value="yes" '. (setting_fetch('topretweets', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Retweets</label><br />';
    $content .= '<label><input type="checkbox" name="topretweeted" value="yes" '. (setting_fetch('topretweeted') == 'yes' ? ' checked="checked" ' : '') .' /> Retweeted</label><br />';
    $content .= '<label><input type="checkbox" name="topdirects" value="yes" '. (setting_fetch('topdirects', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Directs</label><br />';
    $content .= '<label><input type="checkbox" name="topsearch" value="yes" '. (setting_fetch('topsearch') == 'yes' ? ' checked="checked" ' : '') .' /> Search</label><br />';
    $content .= '<label><input type="checkbox" name="toppicture" value="yes" '. (setting_fetch('toppicture') == 'yes' ? ' checked="checked" ' : '') .' /> Picture</label><br />';
    $content .= '<span class="texts">And Choose what you want to display on the Bottom Bar.</span><br />';
    $content .= '<label><input type="checkbox" name="replies" value="yes" '. (setting_fetch('replies') == 'yes' ? ' checked="checked" ' : '') .' /> Replies</label><br />';
    $content .= '<label><input type="checkbox" name="retweets" value="yes" '. (setting_fetch('retweets') == 'yes' ? ' checked="checked" ' : '') .' /> Retweets</label><br />';
    $content .= '<label><input type="checkbox" name="retweeted" value="yes" '. (setting_fetch('retweeted') == 'yes' ? ' checked="checked" ' : '') .' /> Retweeted</label><br />';
    $content .= '<label><input type="checkbox" name="directs" value="yes" '. (setting_fetch('directs') == 'yes' ? ' checked="checked" ' : '') .' /> Directs</label><br />';
    $content .= '<label><input type="checkbox" name="search" value="yes" '. (setting_fetch('search') == 'yes' ? ' checked="checked" ' : '') .' /> Search</label><br />';
    $content .= '<label><input type="checkbox" name="picture" value="yes" '. (setting_fetch('pictue') == 'yes' ? ' checked="checked" ' : '') .' /> Picture</label><br />';
    $content .= '<label><input type="checkbox" name="favourites" value="yes" '. (setting_fetch('favourites') == 'yes' ? ' checked="checked" ' : '') .' /> Favourites</label><br />';
    $content .= '<label><input type="checkbox" name="lists" value="yes" '. (setting_fetch('lists') == 'yes' ? ' checked="checked" ' : '') .' /> Lists</label><br />';
    $content .= '<label><input type="checkbox" name="followers" value="yes" '. (setting_fetch('followers') == 'yes' ? ' checked="checked" ' : '') .' /> Followers</label><br />';
    $content .= '<label><input type="checkbox" name="friends" value="yes" '. (setting_fetch('friends') == 'yes' ? ' checked="checked" ' : '') .' /> Friends</label><br />';
    /*$content .= '<label><input type="checkbox" name="blockings" value="yes" '. (setting_fetch('blockings') == 'yes' ? ' checked="checked" ' : '') .' /> Blockings</label><br />';*/
    $content .= '<label><input type="checkbox" name="trends" value="yes" '. (setting_fetch('trends') == 'yes' ? ' checked="checked" ' : '') .' /> Trends</label><br />';
    $content .= '<label><input type="checkbox" name="about" value="yes" '. (setting_fetch('about') == 'yes' ? ' checked="checked" ' : '') .' /> About</label><br />';
    $content .= '<label><input type="checkbox" name="ssettings" value="yes" '. (setting_fetch('ssettings', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Settings</label><br />';
    $content .= '<label><input type="checkbox" name="slogout" value="yes" '. (setting_fetch('slogout', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Logout</label><br />';
    $content .= '<label><input type="checkbox" name="srefresh" value="yes" '. (setting_fetch('srefresh') == 'yes' ? ' checked="checked" ' : '') .' /> Refresh</label></p><hr />';
    $content .= '<p><span class="texts">And Choose What you Want to Display On each Status.</span><br />';
    $content .= '<label><input type="checkbox" name="buttonrl" value="yes" '. (setting_fetch('buttonrl', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> RL [Read It Later]</label>';
    $content .= '<label><input type="checkbox" name="buttonre" value="yes" '. (setting_fetch('buttonre', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> AT [@]</label>';
    $content .= '<label><input type="checkbox" name="buttonreall" value="yes" '. (setting_fetch('buttonreall', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> RE [Reply All]</label>';
    $content .= '<label><input type="checkbox" name="buttondm" value="yes" '. (setting_fetch('buttondm') == 'yes' ? ' checked="checked" ' : '') .' /> DM [Direct Messages]</label>';
    $content .= '<label><input type="checkbox" name="buttonfav" value="yes" '. (setting_fetch('buttonfav', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> FAV [Favourite]</label>';
    $content .= '<label><input type="checkbox" name="buttonrt" value="yes" '. (setting_fetch('buttonrt', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> RT [Retweet]</label>';
    $content .= '<label><input type="checkbox" name="buttondel" value="yes" '. (setting_fetch('buttondel', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> DEL [Delete]</label><br />';
    $content .= '<label><input type="checkbox" name="buttonmap" value="yes" '. (setting_fetch('buttonmap', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> MAP [Google Map]</label>';
    $content .= '<label><input type="checkbox" name="buttonot" value="yes" '. (setting_fetch('buttonot', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> OT [Original Tweet]</label>';
    $content .= '<label><input type="checkbox" name="buttonsearch" value="yes" '. (setting_fetch('buttonsearch', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> ? [Search for @ to a user]</label>';
    $content .= '<label><input type="checkbox" name="buttongeo" value="yes" '. (setting_fetch('buttongeo') == 'yes' ? ' checked="checked" ' : '') .' /> GEO [Geolocation]</label><br />';
    $content .= '<label><input type="checkbox" name="buttontime" value="yes" '. (setting_fetch('buttontime', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Status Times</label>';
    $content .= '<label><input type="checkbox" name="buttonfrom" value="yes" '. (setting_fetch('buttonfrom', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Status From</label></p><hr />';
    $content .= '<p><label><input type="checkbox" name="avataro" value="yes" '. (setting_fetch('avataro', 'no') == 'yes' ? ' checked="checked" ' : '') .' /> Disable Avatar</label></p><hr />';
    $content .= '<p><label>Tweets per page (20-200): <input type="text" id="tpp" name="tpp" value="'.setting_fetch('tpp', 20).'" size="3" maxlength="3" class="minput" /></label></p><hr />';

    $content .= '<p><label>List tweets per page (20-200): <input type="text" id="ltpp" name="ltpp" value="'.setting_fetch('ltpp', 20).'" size="3" maxlength="3" class="minput" /></label></p><hr />';

    $content .= '<p><label>External links go:<br /><select name="gwt">';
    $content .= theme('options', $gwt, setting_fetch('gwt', $GLOBALS['current_theme'] == 'text' ? 'on' : 'off'));
    $content .= '</select></label><span class="texts"><br />Google Web Transcoder (GWT) converts third-party sites into small, speedy pages suitable for older phones and people with less bandwidth.</span></p>';
    $content .= '<p><label>Showing URL:<br /><select name="linktrans">'.theme('options', $linktrans, setting_fetch('linktrans', 'd')).'</select></label><br /><span class="texts">Note: Domain Only means change https://twitter.com/JaHIY to [twitter.com]</span></p><hr />';
    $content .= '<p><label>Use Read It Later<br />Email address or username: <input type="text" name="rl_user" value="'. setting_fetch('rl_user', '').'" size="25" class="linput" /></label><br />';
    $content .= '<label>Password, if you have one.: <input type="password" name="rl_pass" value="'. setting_fetch('rl_pass', '').'" size="25" class="linput" /></label></p><hr />';
    if (LONG_URL == 'ON') {
        $content .= '<p><label><input type="checkbox" name="longurl" value="yes" '. (setting_fetch('longurl') == 'yes' ? ' checked="checked" ' : '') .' /> Show Long URL</label></p><hr />';
    }
    //$content .= '<p><label><input type="checkbox" name="showthumbs" value="yes" '. (setting_fetch('showthumbs', 'yes') == 'yes' ? ' checked="checked" ' : '') .' /> Preview Photos In Timelines</label></p><hr />';
    $content .= '<p><label><input type="checkbox" name="fixedtagsposto" value="yes" '. (setting_fetch('fixedtagsposto', 'no') == 'yes' ? ' checked="checked" ' : '') .' /> Tweet Content [At the beginning of your tweets]:</label> <input type="text" id="fixedtagspost" name="fixedtagspost" value="'.setting_fetch('fixedtagspost').'" maxlength="70" size="6" class="sinput" /><br />';
    $content .= '<label><input type="checkbox" name="fixedtagspreo" value="yes" '. (setting_fetch('fixedtagspreo', 'no') == 'yes' ? ' checked="checked" ' : '') .' /> Fixed Tags [At the end of your tweets]:</label> <input type="text" id="fixedtagspre" name="fixedtagspre" value="'.setting_fetch('fixedtagspre').'" maxlength="70" size="6" class="sinput" /><br /><span class="texts">Intro: Add Tags in Your Tweets</span></p><hr />';
    $content .= '<p><label>RT Syntax:<br /><input type="text" id="rtsyntax" name="rtsyntax" value="'.setting_fetch('rtsyntax', 'RT [User]: [Content]').'" maxlength="140" size="25" class="linput" /></label><br /><span class="texts">Default RT Syntax: RT [User]: [Content]</span></p><hr />';
    $content .= '<p><label>When posting a 140+ chars tweet:<br /><select name="longtext">'.theme('options', $longtext, setting_fetch('longtext', 'r')).'</select></label></p><hr />';
    $content .= '<p><label><input type="checkbox" name="filtero" value="yes" '. (setting_fetch('filtero', 'no') == 'yes' ? ' checked="checked" ' : '') .' /> Keyword Filter:</label> <input type="text" id="filterc" name="filterc" value="'.setting_fetch('filterc').'" maxlength="140" size="25" class="linput" /><br /><span class="texts">Note: Separate keywords with space</span></p><hr />';
    $content .= '<p><label><input type="checkbox" name="reverse" value="yes" '. (setting_fetch('reverse') == 'yes' ? ' checked="checked" ' : '') .' /> Attempt to reverse the conversation thread view.</label></p>';
    $content .= '<p><label><input type="checkbox" name="timestamp" value="yes" '. (setting_fetch('timestamp') == 'yes' ? ' checked="checked" ' : '') .' /> Show the timestamp ' . twitter_date('H:i') . ' instead of 25 sec ago</label></p>';
    $content .= '<p><label><input type="checkbox" name="hide_inline" value="yes" '. (setting_fetch('hide_inline') == 'yes' ? ' checked="checked" ' : '') .' /> Hide inline media (eg TwitPic thumbnails)</label></p>';
    $content .= '<p><label>The time in UTC is currently ' . gmdate('H:i') . ', by using an offset of <input type="text" name="utc_offset" value="'. $utc_offset .'" size="3" maxlength="3" class="minput" /> we display the time as ' . twitter_date('H:i') . '.<br />It is worth adjusting this value if the time appears to be wrong.</label></p>';

    // Allow users to choose a Dabr password if accounts are enabled
    if ((ACCESS_USERS == 'MYSQL' || ACCESS_USERS == 'FILE') && user_is_authenticated()) {
        $content .= '<fieldset><legend>Dabr account</legend><span class="texts">If you want to sign in to Dabr without going via Twitter.com in the future, create a password and we\'ll remember you.</span><p><label>Change Dabr password<br /><input type="password" name="newpassword" maxlength="40" size="25" class="linput" /></label><br /><span class="texts">Leave blank if you don\'t want to change it</span><br /><label><input type="checkbox" name="delpass" value="yes" /> Delete my Dabr account, please.</label></p></fieldset>';
    }
    $content .= '<p><button type="submit">Save</button></p></form>';

    $content .= '<hr /><p>Visit <a href="reset">Reset</a> if things go horribly wrong - it will log you out and clear all settings.</p>';

    return theme('page', 'Settings', $content);
}
