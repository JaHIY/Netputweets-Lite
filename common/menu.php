<?php

$menu_registry = array();

function menu_register($items) {
  foreach ($items as $url => $item) {
    $GLOBALS['menu_registry'][$url] = $item;
  }
}

function menu_execute_active_handler() {
  $query = (array) explode('/', $_GET['q']);
  $GLOBALS['page'] = $query[0];
  $page = $GLOBALS['menu_registry'][$GLOBALS['page']];
  if (!$page) {
    header('HTTP/1.0 404 Not Found');
    die('404 - Page not found.');
  }
  
  if ($page['security'])
    user_ensure_authenticated();

  if (function_exists('config_log_request'))
    config_log_request();
  
  if (function_exists($page['callback']))
    return call_user_func($page['callback'], $query);

  return false;
}

function menu_current_page() {
  return $GLOBALS['page'];
}

function menu_visible_items() {
  static $items;
  if (!isset($items)) {
    $items = array();
    foreach ($GLOBALS['menu_registry'] as $url => $page) {
      if ($page['security'] && !user_is_authenticated()) continue;
      if ($page['hidden']) continue;
      $items[$url] = $page;
    }
  }
  return $items;
}

function theme_menu_top() {
  return theme('menu_toptop', 'top');
}

function theme_menu_bottom() {
  return theme('menu_bottomtom', 'bottom');
}

function theme_menu_toptop($menu) {
    $links = array();
    if (user_is_authenticated()){
        if (setting_fetch('topuser') == 'yes') {
            $user = user_current_username();
            $links[] = "<a href='user/$user'>$user</a>";
        }
        if (setting_fetch('tophome', 'yes') == 'yes') {
            $links[] = "<a href='' accesskey='0'>Home</a>";
        }
        if (setting_fetch('topreplies', 'yes') == 'yes') {
            $links[] = "<a href='replies' accesskey='1'>Replies</a>";
        }
        if (setting_fetch('topretweets', 'yes') == 'yes') {
            $links[] = "<a href='retweets' accesskey='2'>Retweets</a>";
        }
        if (setting_fetch('topretweeted') == 'yes') {
            $links[] = "<a href='retweeted' accesskey='6'>Retweeted</a>";
        }
        if (setting_fetch('topdirects', 'yes') == 'yes') {
            $links[] = "<a href='directs' accesskey='3'>Directs</a>";
        }
        if (setting_fetch('topsearch') == 'yes') {
            $links[] = "<a href='search' accesskey='4'>Search</a>";
        }
        if (setting_fetch('toppicture') == 'yes') {
            $links[] = "<a href='picture'>Picture</a>";
        }
    } else {
        $links[] = "<span class='textb menu'>Enjoy Twitter with NetPutweets Lite!</span>";
    }
    return "<div class='menu menu-$menu'>".implode(' | ', $links).'</div>';
}

function theme_menu_bottomtom($menu) {
    $links = array();
    $links[] = "<a href='".BASE_URL."' accesskey='0'>Home</a>";
    if (user_is_authenticated()) {
        if (setting_fetch('replies') == 'yes') {
            $links[] = "<a href='replies' accesskey='1'>Replies</a>";
        }
        if (setting_fetch('retweets') == 'yes') {
            $links[] = "<a href='retweets' accesskey='2'>Retweets</a>";
        }
        if (setting_fetch('retweeted') == 'yes') {
            $links[] = "<a href='retweeted' accesskey='6'>Retweeted</a>";
        }
        if (setting_fetch('directs') == 'yes') {
            $links[] = "<a href='directs' accesskey='3'>Directs</a>";
        }
        if (setting_fetch('search') == 'yes') {
            $links[] = "<a href='search' accesskey='4'>Search</a>";
        }
        if (setting_fetch('picture') == 'yes') {
            $links[] = "<a href='picture'>Picture</a>";
        }
        if (setting_fetch('favourites') == 'yes') {
            $links[] = "<a href='favourites'>Favourites</a>";
        }
        if (setting_fetch('lists') == 'yes') {
            $links[] = "<a href='lists'>Lists</a>";
        }
        if (setting_fetch('followers') == 'yes') {
            $links[] = "<a href='followers'>Followers</a>";
        }
        if (setting_fetch('friends') == 'yes') {
            $links[] = "<a href='friends'>Friends</a>";
        }
        if (setting_fetch('trends') == 'yes') {
            $links[] = "<a href='trends'>Trends</a>";
        }
    }

    if (user_is_authenticated()) {
        $user = user_current_username();
        array_unshift($links, "<span class='textb'><a href='user/$user'>$user</a></span>");
        if (setting_fetch('about') == 'yes') {
            $links[] = "<a href='about'>About</a>";
        }
        if (setting_fetch('ssettings', 'yes') == 'yes') {
            $links[] = "<a href='settings'>Settings</a>";
        }
        if (setting_fetch('slogout', 'yes') == 'yes') {
            $links[] = "<a href='logout'>Logout</a>";
        }
    }
    if (setting_fetch('srefresh', 'yes') == 'yes') {
        $links[] = "<a href='{$_GET['q']}' accesskey='5'>Refresh</a>";
    }
    return '<div class="menu menu-$menu">'.implode(' | ', $links).'</div>';
}

?>