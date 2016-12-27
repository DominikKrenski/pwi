function showExecuteSqlForm(event)
{
  event.preventDefault();
  var link = event.target.href;

  var link = event.target.href
  var ajaxRequest = createAjaxRequest();


  if (ajaxRequest == null) {
    alert('Przeglądarka nie wspiera technologii Ajax');
  }

  ajaxRequest.open("GET", link, true);

  ajaxRequest.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      if (this.responseText != null) {
        document.getElementById('main-content').innerHTML = this.responseText;
      }
    }
  }
  ajaxRequest.send(null);
}

function executeSQL(event)
{
  event.preventDefault();
  var errorMessage;

  var file = document.getElementById('file-input').files[0];

  errorMessage = checkFile(file);

  if (errorMessage == "OK") {
    parseForm(file);
  }
  else {
    alert(errorMessage);
  }
}

function checkFile(file)
{
  if (!(window.File && window.FileReader && window.FileList && window.Blob)) {
    return "Przeglądarka nie wspiera w pełni FileReader";
  }

  if (!file) {
    return "Nie wybrano pliku";
  }

  return "OK";
}

function parseForm(file)
{
  var reader = new FileReader();

  reader.readAsText(file, 'UTF-8');

  reader.onload = function(event) {
    var fileContent = event.target.result;
    var query = fileContent.split(';');
    query[0] += ';';

    var form = document.forms[0];
    var data = new FormData();

    data.append('query', query[0]);

    for (var i = 1; i < form.elements.length - 1; i++) {
      if (form.elements[i].checked) {
        data.append('format', form.elements[i].value);
      }
    }
    var ajaxRequest = createAjaxRequest();

    if (ajaxRequest == null) {
      alert ('Przeglądarka nie wspiera technologii Ajax');
    }

    ajaxRequest.open("POST", 'executesql.php', true);

    ajaxRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        if (this.responseText != null) {
          document.getElementById('main-content').innerHTML = this.responseText;
        }
      }
    }

    ajaxRequest.send(data);
  }
}
