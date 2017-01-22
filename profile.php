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
	var removed = [];
	var markers = new Array();
	var infoWindows = new Array();
	var infowindow = new google.maps.InfoWindow();
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
      		$(xml).find("wpt").each(function() {
      		  var lat = $(this).attr("lat");
      		  var lon = $(this).attr("lon");
      		  var p = new google.maps.LatLng(lat, lon);
      		  points.push(p);
      		  bounds.extend(p);
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
  	}, 5000);
	
    String.prototype.replaceAll = function(search, replace) {
        if (replace === undefined) {
            return this.toString();
        }
        return this.replace(new RegExp('[' + search + ']', 'g'), replace);
    };


	
		function setCoordinates(coords) {
			google.maps.event.clearListeners(map, 'click');
			  var infoWindow = new google.maps.InfoWindow();
			  var groups = coords.split("\n");
			  for(var ii = 0; ii < groups.length-1; ii++){
				  var data = groups[ii].split(" ");
				  if(data[1] === "removed" && $.inArray(data[0], removed) < 0){
					  console.log("removing " + data[0]);
					  removed.push(data[0]);
					  markers[data[0]].setMap(null);
					  delete markers[data[0]];
				  } else if ($.inArray(data[0], removed) > -1 && data[1] === "removed") {
				  } else {
					  var groupName = data[0];
					  for (var i = 1; i < data.length - 3; i++){
					  		groupName = groupName + " " + data[i];
					  }
					  var time = data[data.length - 1];
					  
					  var lon = parseInt(data[data.length - 2])/1000000;
					  var lat = parseInt(data[data.length - 3])/1000000;
					  //console.log(ii + " " + groupName + " " + lon + " " + lat);
					  
					  if(markers[groupName] == null){ //new unregistered group
						console.log("creating new group at " + lat + " " + lon, true);
					  	var letters = '0123456789abcdef'.split('');
						var pinColor = "";
						var nameHash = 0;
						for (var i = 0; i < groupName.length; i++){
							nameHash+=groupName.charCodeAt(i);
						}
						for (var i = 0; i < 6; i++ ) {
							pinColor += letters[Math.round(((Math.PI*nameHash*(groupName.charCodeAt(i % groupName.length)))*Math.pow(10,i) % 10)/10 * 15)];
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
						  var content = getContent(groupName, time);
			  			  //infoWindows[groupName] = new google.maps.InfoWindow({
							//	content:content
				  			//  });
			  			google.maps.event.addListener(markers[groupName], 'click', (function(mm, tt) {
			  			    return function() {
			  			        infowindow.setContent(tt);
			  			        infowindow.open(map, mm);
			  			    }
			  			})(markers[groupName], content));
						  console.log("done", true);
					  }else{
							//console.log("resetting coordinates", true);
						    markers[groupName].setPosition(new google.maps.LatLng(lat, lon));
						  	var content = getContent(groupName, time);
						  	google.maps.event.addListener(markers[groupName], 'click', (function(mm, tt) {
				  			    return function() {
				  			        infowindow.setContent(tt);
				  			        infowindow.open(map, mm);
				  			    }
				  			})(markers[groupName], content));
					}
				}
			}
		};

		function getContent(groupName, time){
			 var content = 
		   			"<div> "+
					groupName<?php if($username == "tochtstaf"){?> + "<br>Laatst update: " + Math.floor(time / 60) + "m " + time%60 + "s geleden.<br>"  +
					"<input width=\"25\" id=\"message\" required=\"required\"><\/input>"+
					"<button onclick=\"sendMessage('" + groupName + "')\" > send <\/button><br>"+
         			"<button onclick=\"removeGroup('" + groupName + "')\" > remove <\/button>"<?php }?>+
         			"<\/div>";
			return content;
         }
		
		function makeInfoWindowEvent(map, infoWindow, content, marker){
			google.maps.event.addListener(marker, 'click', function(){
				infoWindow.setContent(content);
				infoWindow.open(map, marker);
			});
		}

		function sendMessage(groupName){
			$.post("sendMessage.php", {group: groupName, message: document.getElementById("message").value}, function(data, status){
		        alert("sending message: " + status);
		    });
		}
		
		function removeGroup(groupName){
			$.post("sendMessage.php", {group: groupName, message: "remove"}, function(data, status){
		        alert("Removing group: " + status);
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


