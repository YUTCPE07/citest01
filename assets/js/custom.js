// $( document ).ready(function() {

// });
// if (window.XMLHttpRequest) {
//   var xmlHttp = new XMLHttpRequest ()
//   console.log(xmlHttp)
// } else {
//   if (window.ActiveXObject) {
//     var xmlHttp = new ActiveXObject ("Microsoft.XMLHTTP") ;
//   }
// }


function backToTop(){
	$('html, body').animate({scrollTop:0}, 2000);
	// document.body.scrollTop = 0; // For Safari
 //    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}