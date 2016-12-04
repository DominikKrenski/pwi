/* Funkcja, której zadaniem jest sprawdzenie poprawności danych
 * wpisanych do formularza. Funkcja sprawdza jedynie czy wypełnione zostały
 * wymagane pola. Reszta walidacji przeprowadzana jest przez silnik bazy
 * danych.
*/
function validateForm()
{
  var form = document.forms['loginform'];

  /* Wskaźnik pokazujący czy w formularzu wystąpiły błędy.
   * false -> brak błędów
   * true -> błędy
   */
  var errorFlag = false;

  /* Sprawdzenie czy została wpisana nazwa użytkownika */
  if (checkIfFieldIsEmpty(form['userName'].value)) {
    addEmptyErrorMessage(form['userName']);
    errorFlag = true;
  }

  /* Sprawdzenie czy została wpisana nazwa bazy danych */
  if (checkIfFieldIsEmpty(form['databaseName'].value)) {
    addEmptyErrorMessage(form['databaseName']);
    errorFlag = true;
  }

  /* Sprawdzenie, czy został wpisany numer portu w odpowiednim formacie */
  if (!checkIfFieldIsEmpty(form['port'].value)) {
    if (!/^\d+$/.test(form['port'].value)) {
      addInvalidFormatMessage(form['port']);
      errorFlag = true;
    }
  }

  if (errorFlag) {
    return false;
  }

  /* Jeśli wyczyszczone zostało pole hosta, portu lub kodowania przypisane
   * zostaną im wartości domyślne.
   */
   if (checkIfFieldIsEmpty(form['hostName'].value)) {
     form['hostName'].value = 'localhost';
   }

   if (checkIfFieldIsEmpty(form['port'].value)) {
     form['port'].value = 3306;
   }

   if (checkIfFieldIsEmpty(form['charset'].value)) {
     form['charset'].value = "utf8";
   }

  alert("Użytkownik: " + form['userName'].value + "\nHasło: " + form['userPassword'].value +
          "\nBaza danych: " + form['databaseName'].value + '\nHost: ' + form['hostName'].value +
          "\nPort: " + form['port'].value + "\nKodowanie: " + form['charset'].value);

  return true;
}

function clearErrorMessage(e)
{
  var element = e.target;
  var nextElement = element.nextElementSibling;
  element.className = "";
  element.value = "";
  nextElement.style.visibility = "hidden";
}

function checkIfFieldIsEmpty(value)
{
  value = value.replace(/ /g, "");
  if (value == "" || value.length == 0) {
    return true;
  } else {
    return false;
  }
}

function addEmptyErrorMessage(element)
{
  var nextElement = element.nextElementSibling;
  element.className = 'field-required-error';
  nextElement.style.visibility = "visible";
}

function addInvalidFormatMessage(element)
{
  var nextElement = element.nextElementSibling;
  element.className = 'field-required-error';
  nextElement.style.visibility = "visible";
}
