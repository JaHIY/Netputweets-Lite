<?php

require 'touch.php';

function bigtouch_theme_action_icon($url, $image_url, $text) {
if ($text == 'MAP')	{
  return "<a href='$url' target='_blank'>$text</a>";
  }
  return "<a href='$url'>$text</a>";
}

function bigtouch_theme_status_form($text = '', $in_reply_to_id = NULL) {
  return desktop_theme_status_form($text, $in_reply_to_id);
}
function bigtouch_theme_search_form($query) {
  return desktop_theme_search_form($query);
}

function bigtouch_theme_avatar($url, $name='', $force_large = false) {
if (setting_fetch('avataro', 'yes') !== 'yes') {
  return "<img src='$url' alt='$name' width='48' height='48' />";
  } else {
        return '';
    }
}

function bigtouch_theme_page($title, $content) {
return theme_page($title, $content);
}

function bigtouch_theme_menu_top() {
return touch_theme_menu_top();
}

function bigtouch_theme_menu_bottom() {
  return '';
}

function bigtouch_theme_status_time_link($status, $is_link = true) {
return touch_theme_status_time_link($status, $is_link);
}

function bigtouch_theme_css() {
  $out = theme_css();
  $out .= '<link rel="stylesheet" href="browsers/bigtouch.css" />';
  $out .= '<script type="text/javascript">
  <!--
  '.file_get_contents('browsers/touch.js').'
  //-->
  </script>';
  return $out;
}
?>