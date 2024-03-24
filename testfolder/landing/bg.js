function getViewportSize() {
  var viewportwidth;
  var viewportheight;
  if (typeof window.innerWidth != "undefined") {
    viewportwidth = window.innerWidth;
    viewportheight = window.innerHeight;
  } else if (typeof document.documentElement != "undefined"
  && typeof document.documentElement.clientWidth !=
  "undefined" && document.documentElement.clientWidth != 0) {
    viewportwidth = document.documentElement.clientWidth;
    viewportheight = document.documentElement.clientHeight;
  } else {
    viewportwidth = document.getElementsByTagName("body")[0].clientWidth;
    viewportheight = document.getElementsByTagName("body")[0].clientHeight;
  }
  return Array(viewportwidth,viewportheight);
}
function alignBg() {
 var dimensions = getViewportSize();
 var hOffset = dimensions[0]/2 - 1441/2;
 if (document.getElementById('floatHeader')) {
  var myHeader = document.getElementById('floatHeader');
  var hOffset = dimensions[0]/2 - 1441/2;
  myHeader.style.left = hOffset+"px";
 }
 if (document.getElementById('floatHeader2')) {
  var myHeader = document.getElementById('floatHeader2');
  var hOffset = dimensions[0]/2 - 1280/2;
  myHeader.style.left = hOffset+"px";
 }
 if (document.getElementById('floatFooter')) {
  var myFooter = document.getElementById('floatFooter');
  var vOffset = dimensions[1] - 230;
  myFooter.style.left = hOffset+"px";
  myFooter.style.top = vOffset+"px";
  myFooter.style.visibility = "visible";
 }
}