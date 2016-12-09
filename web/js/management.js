function showTableStructure(event)
{
  event.preventDefault();

  var target = event.target;
  var tableName = target.text;

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("GET", "tablestructure.php?name=" + tableName, true);

  ajaxRequest.onreadystatechange = function () {
    if (this.readyState == 4) {
      if (this.status == 200) {
        if (this.responseText != null) {
          document.getElementById('main-content').innerHTML = this.responseText;
        }
      }
    }
  }

  ajaxRequest.send(null);
}

function dropColumnFromTable(event)
{
  event.preventDefault();

  var target = event.target;
  var href = target.href;

  var ajaxRequest = createAjaxRequest();

  if (ajaxRequest === false) {
    alert("Przeglądarka nie wspiera technologii Ajax");
  }

  ajaxRequest.open("GET", href, true);

  ajaxRequest.onreadystatechange = function() {
    if (this.readyState == 4) {
      if (this.status == 200) {
        if (this.responseText != null) {

        }
      }
    }
  }

  ajaxRequest.send(null);
}

function createAjaxRequest()
{
  try {
    var request = new XMLHttpRequest();
  }
  catch (ex1) {
    try {
      request = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (ex2) {
      try {
        request = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch (ex3) {
        request = false;
      }
    }
  }
  return request;
}
