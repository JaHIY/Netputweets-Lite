<?php
function desktop_theme_status_form($text = '', $in_reply_to_id = NULL) {
    if (user_is_authenticated()) {
        $fixedtagspre = setting_fetch('fixedtagspre');
        $fixedtagspost = setting_fetch('fixedtagspost');
        $fixedtagspre = (!empty($fixedtagspre) && (setting_fetch('fixedtagspreo', 'no') == "yes") && ($text == '')) ? $fixedtagspre." " : NULL;
        $fixedtagspost = (!empty($fixedtagspost) && (setting_fetch('fixedtagsposto', 'no') == "yes") && ($text == '')) ? " ".$fixedtagspost : NULL;
        $text = $fixedtagspre.$text.$fixedtagspost;
        $output = '<form method="post" action="update">
  <fieldset><legend>What\'s Happening?</legend>
  <div><textarea id="status" name="status" rows="3" cols="60">'.$text.'</textarea>
  <div><input name="in_reply_to_id" value="'.$in_reply_to_id.'" type="hidden" /><button id="submit" type="submit">Tweet</button><span id="remaining">140</span>';
        if (setting_fetch('buttongeo') == 'yes') {
            $output .= '<span id="geo" style="display: none; float: right;"><input onclick="goGeo()" type="checkbox" id="geoloc" name="location" /> <label for="geoloc" id="lblGeo"></label></span>
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
    geoStatus("Tweet my <a href=\'http://maps.google.com/maps?q=loc:" + position.coords.latitude + "," + position.coords.longitude + "\' target=\'blank\'>location</a>");
    chkbox.value = position.coords.latitude + "," + position.coords.longitude;
}
//-->
</script>
';
        }
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
  return "<form action='search' method='get'><div><input name='query' id='query' value=\"$query\" /><button type='submit'>Search</button></div></form>";
}
?>
