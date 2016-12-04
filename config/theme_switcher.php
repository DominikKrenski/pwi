<?php

if (isset($_GET['style'])) {
  $style = $_GET['style'];

  switch($style)
  {
    case 'basic':
    $_SESSION['style'] = $style;
    setcookie('style', $style, time() + 3600);
    break;

    case 'extended':
    $_SESSION['style'] = $style;
    setcookie('style', $style, time() + 3600);
    break;

    default:
    $_SESSION['style'] = 'basic';
    setcookie('style', 'basic', time() + 3600);
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

/*if (isset($_GET['lang'])) {
  $lang = $_GET['lang'];

  switch($lang)
  {
    case $lang:
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + 3600);
    break;

    case 'en':
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + 3600);
    break;

    default:
    $_SESSION['lang'] = 'pl';
    setcookie('lang', 'pl', time() + 3600);
    $lang = 'pl';
  }

} elseif (isset($_SESSION['lang'])) {
  $lang = $_SESSION['lang'];

} elseif (isset($_COOKIE['lang'])) {
  $lang = $_COOKIE['lang'];

} else {
  $acceptedLanguage = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);

  if (strpos($acceptedLanguage, 'pl')) {
    $lang = 'pl';
  } elseif (strpos($acceptedLanguage, 'en')) {
    $lang = 'en';
  } else {
    $lang = 'pl';
  }
}

switch($lang)
{
  case 'pl':
  $langFile = 'polish.php';
  break;

  case 'en':
  $langFile = 'english.php';
  break;

  default:
  $langFile = 'polish.php';
}*/
