<?php

session_start();

require_once '../config/twig_config.php';
require_once '../config/language_switcher.php';
require_once "../lang/$langFile";
require_once '../config/theme_switcher.php';

$dsn = '';
$userName = '';
$userPassword = '';
$connection = '';
$tableList = [];

if (isset($_POST['userName']) && isset($_POST['userPassword']) && isset($_POST['databaseName']) && isset($_POST['hostName']) && isset($_POST['port']) && isset($_POST['charset'])) {
  $userName = trim($_POST['userName']);
  $userPassword = trim($_POST['userPassword']);
  $databaseName = trim($_POST['databaseName']);
  $hostName = trim($_POST['hostName']);
  $port = trim($_POST['port']);
  $charset = trim($_POST['charset']);
  $dsn = "mysql:dbname=$databaseName;host=$hostName;port=$port;charset=$charset";

  $_SESSION['dsn'] = $dsn;
  $_SESSION['userName'] = $userName;
  $_SESSION['userPassword'] = $userPassword;
  $_SESSION['logged'] = 'logged';
}
elseif(isset($_SESSION['logged']) && ($_SESSION['logged'] == 'logged')) {
  $dsn = $_SESSION['dsn'];
  $userName = $_SESSION['userName'];
  $userPassword = $_SESSION['userPassword'];
}
else {
  header('Location: https://www.projekt-pwi.com');
}

try {
  $connection = new PDO($dsn, $userName, $userPassword, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
} catch (PDOException $e) {
  $_SESSION['logged'] = '';
  $_SESSION['dsn'] = '';
  $_SESSION['userName'] = '';
  $_SESSION['userPassword'] = '';

  echo $twig->render('management.html', [
    'langArray' => $langArray,
    'styleFile' => $styleFile,
    'uri' => 'management.php',
    'tableList' => [],
    'connectionError' => $e->getMessage()
  ]);
  die();
}

$stmt = $connection->prepare('SHOW TABLES');
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
  $tableList[] = $row[0];
}

echo $twig->render('management.html', [
  'langArray' => $langArray,
  'styleFile' => $styleFile,
  'uri' => 'management.php',
  'tableList' => $tableList
]);
