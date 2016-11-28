/*
 * Funkcja mająca na celu dodanie cienia do nagłówka strony, gdy jej
 * zawartość zostanie przesunięta o co najmniej 30px,
 * ponieważ taki jest odstęp między nagłówkiem a zawartością strony.
 * Gdy zawartość wraca na swoje miejsce, cień jest usuwany.
 */
function addShadowToHeader()
{
  var target = document.getElementById('page-header');
  if (this.scrollY >= 30) {
    target.style['-webkit-box-shadow'] = '1px 10px 10px -4px rgba(255,255,255,1)';
    target.style['-moz-box-shadow'] = '1px 10px 10px -4px rgba(255,255,255,1)';
    target.style['box-shadow'] = '1px 10px 10px -4px rgba(255,255,255,1)';
  } else {
    target.style['-webkit-box-shadow'] = null;
    target.style['-moz-box-shadow'] = null;
    target.style['box-shadow'] = null;
  }
}
