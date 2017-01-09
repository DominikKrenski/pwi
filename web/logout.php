<?php

session_start();
//function deleteSession()
//{
  // Usunięcie wszystkich zmiennych zapisanych w sesji
  $_SESSION = [];

  /* Ponieważ do przechowywania identyfikatora sesji wykorzystywane jest
   * ciasteczko, je również należy usunąć.
  */
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 30000, $params['path'],
            $params['domain'], $params['secure'], $params['httponly']
  );

  // Na zakończenie należy zniszczyć sesję
  session_destroy();

  // Przekierowanie do strony logowania
  header('Location: index.php');
//}

/*function deleteInactiveSession()
{
  if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity']) > 60) {
    deleteSession();
  }
}*/
