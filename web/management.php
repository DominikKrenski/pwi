<?php

session_start();

require_once '../config/twig_config.php';
require_once '../config/language_switcher.php';
require_once "../lang/$langFile";
require_once '../config/theme_switcher.php';

echo $twig->render('management.html', [
  'langArray' => $langArray,
  'styleFile' => $styleFile,
  'uri' => 'management.php'
]);
