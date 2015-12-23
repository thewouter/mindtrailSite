<?php
	include('session.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" content="initial-scale=1.0, user-scalable=no" />
    <title>Map</title>
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map-canvas { height: 100% }
    </style>
	<link href="style.css" rel="stylesheet" type="text/css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeHumDLq5xR3NOYc-ugJwaTnGXoHHtmys&sensor=false"
            type="text/javascript"></script>
    <script src="jquery-1.11.3.js" type="text/javascript"></script>
    <script type="text/javascript">
	var map;
	var markers = new Array();
      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(52.5763881, 6.4748419),
          zoom: 15,
          mapTypeId: google.maps.MapTypeId.TERRAIN
        };

        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);


        $.ajax({
        	  type: "GET",
        	  url: "/route.gpx",
        	  dataType: "xml",
        	  success: function(xml) {
        		var points = [];
        		var bounds = new google.maps.LatLngBounds ();
        		$(xml).find("rtept").each(function() {
        		  var lat = $(this).attr("lat");
        		  var lon = $(this).attr("lon");
        		  var p = new google.maps.LatLng(lat, lon);
        		  points.push(p);
        		  bounds.extend(p);
        		  console.log("tests");
        		});

        		var poly = new google.maps.Polyline({
        			path: points,
        			geodesic: true,
        			strokeColor: '#FF0000',
        			strokeOpacity: 0.5,
        			strokeWeight:2
        		});
        		
        		poly.setMap(map);
        		// fit bounds to track
        		map.fitBounds(bounds);
        	  }
        	});
     }
	  
      google.maps.event.addDomListener(window, 'load', initialize);


      setInterval(function() {
		  var xhttp = new XMLHttpRequest();
		  xhttp.onreadystatechange = function() {
		    if (xhttp.readyState == 4 && xhttp.status == 200) {
				setCoordinates(xhttp.responseText);
			}
		  };
		  xhttp.open("POST", "groepjes.txt", true);
		  xhttp.send();
    	}, 1000);
  	
		function setCoordinates(coords) {
			  var infoWindow = new google.maps.InfoWindow();
			  var groups = coords.split("\n");
			  for(var ii = 0; ii < groups.length-1; ii++){
				  var data = groups[ii].split(" ");
				  var groupName = data[0];
				  for (var i = 1; i < data.length - 2; i++){
				  		groupName = groupName + " " + data[i];
				  }
				  
				  var lon = parseInt(data[data.length - 1])/1000000;
				  var lat = parseInt(data[data.length - 2])/1000000;
				  //console.log(ii + " " + groupName + " " + lon + " " + lat);
				  if(markers[groupName] == null){ //new unregistered group
					console.log("creating new group", true);
				  	var letters = '0123456789abcdef'.split('');
					var pinColor = "";
					var nameHash = 0;
					for (var i = 0; i < groupName.length; i++){
						nameHash+=groupName.charCodeAt(i);
					}
					for (var i = 0; i < 6; i++ ) {
						pinColor += letters[Math.round(((Math.PI*nameHash*(groupName.charCodeAt(i % groupName.length)))*Math.pow(10,i) % 10)/10) * 15];
						//console.log(pinColor);
					}
					var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor + "|000000",
						new google.maps.Size(21, 34),
						new google.maps.Point(0,0),
						new google.maps.Point(10, 34));
					var pinShadow = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
						new google.maps.Size(40, 37),
						new google.maps.Point(0, 0),
						new google.maps.Point(12, 35));
					  var markerOptions = {
						position: new google.maps.LatLng(lat, lon),
						map: map,
						title: groupName,
						animation: google.maps.Animation.DROP,
						icon: pinImage,
						shadow: pinShadow
					  };
					  markers[groupName] = new google.maps.Marker(markerOptions);
					  var content = 
					   	"<div> "+
		                		"<form action=\"sendMessage.php\" method=\"POST\" name='button' id=\"testdd\">"+
		                		"<div>"+
								groupName<?php if($user_check == "tochtstaf"){?> +
		                		"<br><input type=\"hidden\" value=\"" + groupName + "\" name=\"group\"></input><input type=\"text\" size\"25\" id=\"message\" name=\"message\"></input><input type='submit' value='send Message' >"+
		                		"<\/div>"+
		                		"<\/form>"<?php }?>+
		                		"<\/div>";
		  					  
					  makeInfoWindowEvent(map, infoWindow, content, markers[groupName]);
					  //console.log("done", true);
				  }else{
					  //console.log("resetting coordinates", true);
				  	  markers[groupName].setPosition(new google.maps.LatLng(lat, lon));
				  }
			}
		};
		
		function makeInfoWindowEvent(map, infoWindow, content, marker){
			google.maps.event.addListener(marker, 'click', function(){
				infoWindow.setContent(content);
				infoWindow.open(map, marker);
			});
		}
		
		 </script>
    
    
  </head>
  <body>
  	<div id="wrapper">
    	<div id="map-canvas"/></div>
    	<div id="logout_button">
    		<button onclick="location.href = 'logout.php';">Logout</button>
    	</div>
	</div>
  </body>
</html>


