<?php

session_start();

require_once '../config/language_switcher.php';
require_once "../lang/$langFile";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  echo "<div id=\"execute-sql-form-div\">
          <form id=\"execute-sql-form\" method=\"POST\" action=\"executesql.php\">
            <p>". $langArray['chooseFile'] ."</p>
            <input type=\"file\" id=\"file-input\">
            <p>". $langArray['chooseFormat'] ."</p>
            <input type=\"radio\" name=\"responseFormat\" value=\"json\" checked>JSON<br>
            <input type=\"radio\" name=\"responseFormat\" value=\"text\">TEXT<br>
            <input type=\"radio\" name=\"responseFormat\" value=\"html\" onchange=\"addPaginationRow(event)\">HTML<br>
            <div style=\"display:none\" id=\"pagination\">
              <p>". $langArray['resultsOnPage'] ."</p>
              <input type=\"radio\" name=\"pagination\" value=\"5\">5
              <input type=\"radio\" name=\"pagination\" value=\"10\" checked>10
              <input type=\"radio\" name=\"pagination\" value=\"15\">15<br>
            </div>
            <input id=\"submit-file\" type=\"submit\" value=\"". $langArray['sendFile'] ."\" onclick=\"executeSQL(event)\">
          </form>
      </div>";
}

if (isset($_POST['query']) && isset($_POST['format']) && isset($_POST['pagination'])) {
  $query = $_POST['query'];
  $format = $_POST['format'];
  $pagination = intval($_POST['pagination']);

  try {
    $connection = new PDO($_SESSION['dsn'], $_SESSION['userName'], $_SESSION['userPassword'], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::FETCH_NUM
    ]);

    if (preg_match("/^select/i", $query)) {
      if ($format == 'text') {
        $result = $connection->query($query, PDO::FETCH_ASSOC);
        createFile($result);
        echo '<form action="download.php" method="POST">
                <input id="submit-file" type="submit" value="Pobierz plik">
              </form>';
      }
      elseif ($format == 'json') {
        $result = $connection->query($query, PDO::FETCH_ASSOC);
        generateJsonResponse($result);
      }
      else {
        generateHtmlResponse($connection, $query, $pagination);
      }
    }
  }
  catch (PDOException $ex) {
    $message = $ex->getMessage();

    echo "<div class=\"connection-error\">
            <div class=\"connection-error-header\">
              <h2>". $langArray['errorHeader'] . "</h2>
            </div>
            <div class=\"connection-error-content\">
              <p>$message</p>
            </div>
          </div>";
  }
}

function createFile($result)
{
  $file = fopen('tmp/results.txt', 'w') or die ("Chuj");

  foreach ($result as $inner) {
    if (is_array($inner)) {
      foreach($inner as $key => $value) {
        fwrite($file, $key . ' => '. $value. "\n");
      }
      fwrite($file, "\n\n");
    }
  }
  fclose($file);
}

function generateJsonResponse($result)
{
  echo '<div id="result-div">';
  foreach ($result as $inner) {
    if (is_array($inner)) {
      echo json_encode($inner, JSON_UNESCAPED_UNICODE). '<br><br>';
    }
  }
  echo '</div>';
}

function generateHtmlResponse($connection, $query, $pagination)
{

  $tableKeys = [];
  $numberOfRows = 0; // liczba wyników zapytania
  $resultTable = "<div id=\"result-div\"><table id=\"result-table\"><tr>";
  $tmpTable = "";

  try {

    /* Pierwsze zapytanie ma na celu sprawdzenie ile wyników
     * zostało zwróconych
    */
    $result = $connection->query($query, PDO::FETCH_ASSOC);

    foreach ($result as $inner) {
      $numberOfRows++;
    }

    $numberOfPages = ceil($numberOfRows / $pagination);

    $query1 = rtrim($query, ';');

    $_SESSION['customQuery'] = $query1;
    $_SESSION['rowsPerPage'] = $pagination;
    $_SESSION['numberOfRows'] = $numberOfRows;
    $_SESSION['numberOfPages'] = $numberOfPages;

    $query1 = $query1 . " limit 0, $pagination;";

    $counter = 0;

    $result = $connection->query($query1, PDO::FETCH_ASSOC);

    foreach ($result as $inner) {
      if ($counter == 0) {
        $tableKeys = array_keys($inner);
      }
      $tmpTable .= '<tr>';

      foreach ($inner as $key => $value) {
        $tmpTable .= "<td>$value</td>";
      }

      $tmpTable .= "</tr>";
      $counter++;
    }



    foreach ($tableKeys as $value) {
      $resultTable .= "<th>$value</th>";
    }

    $resultTable .= "</tr>";
    $resultTable .= $tmpTable;
    $resultTable .= "</table>";

    echo $resultTable;

    $navigation = '<ul id="navigation">';

    for ($i = 1; $i <= $numberOfPages; $i++) {
      $navigation .= "<li><a href=\"pagination.php?page=$i\" onclick=\"handlePagination(event)\">$i</li>";
    }

    $navigation .= '</ul>';

    echo $navigation;


  }
  catch (PDOException $ex) {
    $message = $ex->getMessage();

    echo "<div class=\"connection-error\">
            <div class=\"connection-error-header\">
              <h2>". $langArray['errorHeader'] ."</h2>
            </div>
            <div class=\"connection-error-content\">
              <p>$message</p>
            </div>
          </div>";
  }
}
