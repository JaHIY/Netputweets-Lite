<?php
function desktop_theme_status_form($text = '', $in_reply_to_id = NULL) {
    if (user_is_authenticated()) {
        $fixedtagspre = setting_fetch('fixedtagspre');
        $fixedtagspost = setting_fetch('fixedtagspost');
        $fixedtagspre = (!empty($fixedtagspre) && (setting_fetch('fixedtagspreo', 'no') == "yes") && ($text == '')) ? $fixedtagspre." " : NULL;
        $fixedtagspost = (!empty($fixedtagspost) && (setting_fetch('fixedtagsposto', 'no') == "yes") && ($text == '')) ? " ".$fixedtagspost : NULL;
        $text = $fixedtagspre.$text.$fixedtagspost;
        // adding ?status=foo will automaticall add "foo" to the text area.
        if ($_GET['status']) {
            $text = $_GET['status'];
        }
        $output = '<form method="post" action="update">
  <fieldset><legend>What\'s Happening?</legend>
  <div><textarea id="status" name="status" rows="4" cols="60">'.$text.'</textarea>
        ';
        if (setting_fetch('buttongeo') == 'yes') {
            $output .= '<br /><span id="geo" style="display: inline;"><input onclick="goGeo()" type="checkbox" id="geoloc" name="location" /> <label for="geoloc" id="lblGeo"></label></span>
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
    navigator.geolocation.getCurrentPosition(geoSuccess, geoStatus, {enableHighAccuracy: true});
}
function geoStatus(msg) {
    document.getElementById("geo").style.display = "inline";
    document.getElementById("lblGeo").innerHTML = msg;
}
function geoSuccess(position) {
    if(typeof position.address !== "undefined")
        geoStatus("Tweet my <a href=\'https://maps.google.com/maps?q=loc:" + position.coords.latitude + "," + position.coords.longitude + "\' target=\'blank\'>location</a>" + " (" + position.address.country + position.address.region + "省" + position.address.city + "市，accuracy: " + position.coords.accuracy + "m)");
    else
        geoStatus("Tweet my <a href=\'https://maps.google.com/maps?q=loc:" + position.coords.latitude + "," + position.coords.longitude + "\' target=\'blank\'>location</a>" + " (accuracy: " + position.coords.accuracy + "m)");
    chkbox.value = position.coords.latitude + "," + position.coords.longitude;
}
//-->
</script>
';
        }
        $output .= '<div><input name="in_reply_to_id" value="'.$in_reply_to_id.'" type="hidden" /><button id="submit" type="submit">Tweet</button><span id="remaining">140</span>';
        $output .= '</div></div></fieldset></form>';
        $output .= js_counter('status');
        if (setting_fetch('browser') == 'desktop') {
        $output .= '<script type="text/javascript">
    <!--
    document.getElementById("status").onkeydown=function(b){var a=null;a=window.event?window.event:b;a!=null&&a.ctrlKey&&a.keyCode==13&&document.getElementById("submit").click()};
    //-->
    </script>';
        }
        return $output;
    }
}

function desktop_theme_search_form($query) {
    $query = stripslashes(htmlentities($query,ENT_QUOTES,"UTF-8"));
    return '
<form action="search" method="get"><input name="query" value="'. $query .'" /><button type="submit">Search</button><br />
<span id="geo" style="display: none; float: right;"><input onclick="goGeo()" type="checkbox" id="geoloc" name="location" /><label for="geoloc" id="lblGeo"></label>
<select name="radius"><option value="1km">1 Km</option><option value="5km">5 Km</option><option value="10km">10 Km</option><option value="50km">50 Km</option></select></span>
<script type="text/javascript">
<!--
started = false;
chkbox = document.getElementById("geoloc");
if (navigator.geolocation) {
    geoStatus("Search near my location");
    if ("'.$_COOKIE['geo'].'"=="Y") {
        chkbox.checked = true;
        goGeo();
    }
}
function goGeo(node) {
    if (started) return;
    started = true;
    geoStatus("Locating...");
    navigator.geolocation.getCurrentPosition(geoSuccess, geoStatus , { enableHighAccuracy: true });
}
function geoStatus(msg) {
    document.getElementById("geo").style.display = "inline";
    document.getElementById("lblGeo").innerHTML = msg;
}
function geoSuccess(position) {
    geoStatus("Search near my <a href=\'https://maps.google.com/m?q=" + position.coords.latitude + "," + position.coords.longitude + "\' rel=\'external nofollow noreferrer\'>location</a>");
    chkbox.value = position.coords.latitude + "," + position.coords.longitude;
}
//-->
</script>
</form>';
}
function desktop_theme_avatar($url, $force_large = false) {
       return "<img src='$url' width='48' height='48' />";
}
function desktop_theme_css() {
       $out = theme_css();
       $out .= '<link rel="stylesheet" href="browsers/desktop.css" />';
       return $out;
}
?>
