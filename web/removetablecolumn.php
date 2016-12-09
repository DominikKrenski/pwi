<?php

session_start();

if (isset($_GET['table']) && isset($_GET['column'])) {
  $table = $_GET['table'];
  $column = $_GET['column'];

  /* Sprawdzenie czy zmienne nie zawierają niedozwolonych znaków */
  if (preg_match('/[^A-Za-z0-9_.]/', $table) || preg_match('/[^A-Za-z0-9_.]/', $column)) {
    echo "<h2>Nazwa tabeli lub kolumny zawiera niedozwolone znaki</h2>";
    die();
  }

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_ASSOC
    ]);

    $stmt = $connection->prepare("ALTER TABLE $table DROP COLUMN $column");
    $stmt->execute();
  }
  catch (PDOException $ex) {
    $message = $ex->getMessage();
    echo "<h2>$message</h2>";
  }
}
