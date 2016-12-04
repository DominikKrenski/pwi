<?php

session_start();

require_once '../config/twig_config.php';
require_once '../config/language_switcher.php';
require_once "../lang/$langFile";
require_once '../config/theme_switcher.php';

echo $twig->render('index.html', array(
  'langArray' => $langArray,
  'styleFile' => $styleFile,
  'uri' => 'index.php'
));
