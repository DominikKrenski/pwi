<?php
require_once '../vendor/twig/twig/lib/Twig/Autoloader.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('../templates');

$twig = new Twig_Environment($loader, [
  'debug' => true,
  'charset' => 'utf-8',
  'cache' => false,
  'auto_reload' => true,
  'strict_variables' => false,
  'autoescape' => true
]);
