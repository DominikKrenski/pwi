<?php

if (isset($_GET['lang'])) {
  $lang = $_GET['lang'];

  switch($lang)
  {
    case 'pl':
    $_SESSION['lang'] = $lang;
    //$_SESSION['lastActivity'] = time();
    setcookie('lang', $lang, time() + 60);
    break;

    case 'en':
    $_SESSION['lang'] = $lang;
    //$_SESSION['lastActivity'] = time();
    setcookie('lang', $lang, time() + 60);
    break;

    default:
    $_SESSION['lang'] = 'pl';
    //$_SESSION['lastActivity'] = time();
    setcookie('lang', 'pl', time() + 60);
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
}
