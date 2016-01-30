<?php
	mkdir("route");
	$myfile = fopen("route/data.dat", "a+") or die("Unable to open file!");
	$gpx = simplexml_load_file("route.gpx");
	$file = explode(" ", fread($myfile, 100000));
	$sortableArray = array();
	
	$locations = $gpx->wpt;
	foreach ($locations as $l){
		$sortableArray[] = $l;
	}
	
	usort($sortableArray,function($obj1,$obj2){
		$str1 = ($obj1->name);
		$str2 = ($obj2->name);
		return strnatcmp($str1, $str2);
	});
	
	
	$i =1; 
	foreach ($sortableArray as $pt) {
		$lat = (string) $pt['lat'];
		$lon = (string) $pt['lon'];
		if(array_search("1".strval(intval($lat*1000000)), $file) === false && array_search(strval(intval($lon*1000000)), $file) === false){
			break;
		} 
		$i++;
	}

	if($_GET){
		fwrite($myfile, "1".(intval($lat*1000000))." ".intval($lon*1000000)." ".$_GET["r"]." " . explode("/",$_GET["m"])[1] . " ");
		mkdir("route/images");
		copy("images/image_".$_GET["i"].".png", "route/images/".$i.".png");
		mkdir("route/sounds");
		copy($_GET["m"], "route/sounds/".explode("/",$_GET["m"])[1]);
	}
	fclose($myfile);
	
	
	$myfile = fopen("route/data.dat", "a+") or die("Unable to open file!");
	$file = explode(" ", fread($myfile, 100000));
	$sortableArray = array();
	
	$locations = $gpx->wpt;
	foreach ($locations as $l){
		$sortableArray[] = $l;
	}
	
	usort($sortableArray,function($obj1,$obj2){
		$str1 = ($obj1->name);
		$str2 = ($obj2->name);
		return strnatcmp($str1, $str2);
	});

	$i =1;
	foreach ($sortableArray as $pt) {
		$lat = (string) $pt['lat'];
		$lon = (string) $pt['lon'];
		if(array_search("1".strval(intval($lat*1000000)), $file) === false && array_search(strval(intval($lon*1000000)), $file) === false){
			break;
		}
		$i++;
	}
	
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Simple Map</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 20%;
        width: 20%;
      }
    </style>
    
    <style type="text/css">
		.tg  {
			border-collapse:collapse;
			border-spacing:0;
		}
		.tg td{
			font-family:Arial, sans-serif;
			font-size:14px;
			padding:10px 5px;
			border-style:solid;
			border-width:1px;
			overflow:hidden;
			word-break:normal;
		}
		.tg th{
			font-family:Arial, sans-serif;
			font-size:14px;
			font-weight:normal;
			padding:10px 5px;
			border-style:solid;
			border-width:1px;
			overflow:hidden;
			word-break:normal;
		}
		.tg .tg-yw4l{
			vertical-align:top;
		}
		#slider {
			max-width: 400px;
			position: absolute;
		    right: 20%;
		    width: 300px;
		    border: 3px solid #73AD21;
		    padding: 10px;
		}
		
		#current {
			max-width: 400px;
			position: absolute;
		    right: 50%;
		    top: 40px;
		    width: 300px;
		    border: 3px solid #73AD21;
		    padding: 10px;
		    text-align: center;
		}
		
		.rangeslider__handle {
		  border-radius: 22px;
		  min-width: 62px;
		  line-height: 42px;
		  text-align: center;
		  
		  &:after {
		    background: 0;
		  }
		}
	</style>
	<script src="jquery-1.11.3.js"></script>
	<script src="rangeslider.min.js"></script>
	<script>
	    // Initialize a new plugin instance for all
	    // e.g. $('input[type="range"]') elements.
	    //$('input[type="range"]').rangeslider();
	</script>

	
	<script>
      var map;
      function initMap() {
    	  <?php 
              	echo "myLatLng = {lat: ".$lat.", lng: ".$lon."};";
          ?>
        	map = new google.maps.Map(document.getElementById('map'), {
	            center: myLatLng,
	          	zoom: 14,
	          	mapTypeId: google.maps.MapTypeId.HYBRID
        	});

        	var marker = new google.maps.Marker({
        	    position: myLatLng,
        	    map: map,
        	    title: 'Locatie'
        	});
        	            
      }
		
		function addPart($image){
			if(document.getElementById("music").innerHTML != ""){
				window.location = "createroute.php?i=" + $image + "&r=" + document.getElementById("s").value + "&m=" + document.getElementById("music").innerHTML; 
			}
		}

		function sliderChanged(){
			document.getElementById("number").innerHTML = document.getElementById("s").value+" meter";
		}

		function addSound($sound, $i){
			document.getElementById("music").innerHTML = $sound;
			document.getElementById(toString($i)).style.background_color = "red";
		}
		
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeHumDLq5xR3NOYc-ugJwaTnGXoHHtmys&callback=initMap"
    async defer></script>
    <link href="rangeslider.css" rel="stylesheet" type="text/css">
  </head>
  <body>
  

    <div id="slider">
    	<input type='range' value="20 meter" onchange="sliderChanged()" id="s">
    	<div id="number">20</div>
	</div>
	
	<div id="current"> <?php echo $i?> </div>

    <div id="map"></div>
	
	<table class="tg">
	  <?php 
	  $sounds = ["sounds/L.wav", "sounds/R.wav", "sounds/RD.wav"];
	  $collums = 10;
	  $images = 43;
	  	for ($i=0; $i < ceil($images/$collums); $i++){
	  		echo '<tr>';
	  		for ($row = 1; $row <= $collums; $row++){
	  			echo '<th class="tg-yw4l"> <img height="125" width="125" alt="'.($i*$collums+$row).'" src="images/image_'.($i*$collums+$row).'.png" onclick="addPart('.($i*$collums+$row).')"/></th>';
	  		}	
	  		if($i < sizeof($sounds)){
	  			echo '<th class="'.$i.'">'.$sounds[$i].'<br> <img height="125" width="125" alt="'.$sounds[$i].'" src="images/noot.png" onclick="addSound(\''.strval($sounds[$i]).'\','.$i.')"/></th>';
	  		} else if($i == sizeof($sounds)){
	  			echo '<th class="tg-yw4l"> <div id="music"></div></th>';
	  		} else {
	  			echo '<th class="tg-yw4l"></th>';
	  			 
	  		}
	  		echo '</tr>';
	  	}
	  	?>
	</table>

  </body>
</html>

<?php 
unset($gpx);
fclose($myfile);