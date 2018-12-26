function initMap() {
  // AIzaSyCgOYYNGdJV_5X_VG1PRgFChTnekgc-6To
  var thisLat = $('[name=lat]').val();
  var thisLng = $('[name=lng]').val();
  var mapPosition = {lat:parseFloat(thisLat),lng:parseFloat(thisLng)};
  console.log(mapPosition)
  // if (thisMapPosition) {
  //   mapPosition = thisMapPosition;
  // }else{
  //   mapPosition = {lat: 13.736717, lng: 100.523186} //bankok
  // }

  var image = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'; //imageMarker
  var map = new google.maps.Map(document.getElementById('map'), {
    center: mapPosition,
    zoom: 13
  });

  var infowindow = new google.maps.InfoWindow;
  infowindow.setContent('<div class="h1 text-red">aaaaaaaaaa</div>');
 
  var marker = new google.maps.Marker({
    map: map,
    // icon: image,
    // animation: google.maps.Animation.DROP,
    position: mapPosition
  });

  marker.addListener('click', function() {
    infowindow.open(map, marker);
  });
}