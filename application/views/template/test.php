 <style>
	  #map {
	    width: 100%;
	    height: 400px;
	    background-color: grey;
	  }
</style>
test page
<div id="map"></div>
<script>
// Initialize and add the map
function initMap() {
  // The location of Uluru
  var uluru = {lat: -25.344, lng: 131.036};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 4, center: uluru});
  // The marker, positioned at Uluru
  var marker = new google.maps.Marker({position: uluru, map: map});
}
</script>
<!--Load the API from the specified URL
* The async attribute allows the browser to render the page while the API loads
* The key parameter will contain your own API key (which is not needed for this tutorial)
* The callback parameter executes the initMap() function
-->
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBNcsYt2Qftv9YkWVer0pj67vvNNOSrP3I&callback=initMap">
</script>