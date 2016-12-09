<?php

if (isset($_GET['style'])) {
  $style = $_GET['style'];

  switch($style)
  {
    case 'basic':
    $_SESSION['style'] = $style;
    //$_SESSION['lastActivity'] = time();
    setcookie('style', $style, time() + 60);
    break;

    case 'extended':
    $_SESSION['style'] = $style;
    //$_SESSION['lastActivity'] = time();
    setcookie('style', $style, time() + 60);
    break;

    default:
    $_SESSION['style'] = 'basic';
    //$_SESSION['lastActivity'] = time();
    setcookie('style', 'basic', time() + 60);
    $style = 'basic';
  }
} elseif (isset($_SESSION['style'])) {
  $style = $_SESSION['style'];

} elseif (isset($_COOKIE['style'])) {
  $style = $_COOKIE['style'];

} else {
  $style = 'basic';
}

switch($style) {
  case 'basic':
  $styleFile = 'css/style2.css';
  break;

  case 'extended':
  $styleFile = 'css/style1.css';
  break;
}
