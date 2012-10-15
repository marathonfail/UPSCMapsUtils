<?php include_once 'fbaccess.php'; ?>

<html>
<head>
<title>maps++ | Maps for IAS/IPS/IFS Exams</title>
<script
	src="http://maps.google.com/maps/api/js?key=AIzaSyByDaJQtdfxMBDxYRXVQqISAXCgCqSKul0&sensor=false"
	type="text/javascript">
    </script>
<script src="js/MarkerWithLabel.js" type="text/javascript"></script>
 
 <style type="text/css">
   .labels {
     color: red;
     background-color: white;
     font-family: "Lucida Grande", "Arial", sans-serif;
     font-size: 10px;
     font-weight: bold;
     text-align: center;
     border: 2px solid black;
     white-space: nowrap;
   }
 </style>
 
 <script src="js/jquery-1.8.2.js" type="text/javascript"></script>
 <script src="js/MarkerWithLabel.js" type="text/javascript"></script>
<script src="js/json-parse.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript">
 //<![CDATA[

//Globals
 //initialise the map 
 var map;
 var currMarker;
 var placesMarked = [];
 var currentMapName;
 
function fillWindow(){
	var mapDiv = document.getElementById("mapDiv");
	var infoDiv = document.getElementById("infoDiv");
	try{
		if (window.innerHeight) { //if browser supports window.innerWidth
			mapDiv.style.height = window.innerHeight+'px';
			mapDiv.style.width = window.innerWidth-300+'px';
			infoDiv.style.height = window.innerHeight+'px';
		}
		else{	//MSIE
			document.body.scroll="no";
			mapDiv.style.height = document.body.clientHeight+'px';
			mapDiv.style.width = document.body.clientWidth-300+'px'; 
			infoDiv.style.height = document.body.clientHeight+'px';
        }
	}
	catch(ex){
	}
}

function reloadMap(name) {
	var mapOptions = {
			  mapTypeControl: false,
			  mapTypeControlOptions: {
				  mapTypeIds: ['outline'],
			      position: google.maps.ControlPosition.TOP_CENTER
			  },
	          center: new google.maps.LatLng(22.0, 81.0),
	          zoom: 5,
	          mapTypeId: 'outline',
	          zoomControl: false
	        };
	outlineMapStyleOpts = [ 
	                       { 
	                   	"featureType": "administrative", "elementType": "labels", "stylers": 
	                       	[ { "visibility": "off" } ] 
	           			},
	           			{ "featureType": "landscape", "elementType": "geometry", "stylers": 
	               			[ { "visibility": "on" } ] 
	           			},
	               		{ "featureType": "road", "elementType": "geometry", "stylers":
	                   		 [ { "visibility": "off" } ] 
	              		    },
	              		    { "featureType": "poi", "elementType": "labels", "stylers": 
	                  		    [ { "visibility": "off" } ] 
	              		    },
	              		    { "featureType": "landscape.natural", "stylers":
	              		        [ { "weight": 0.1 }, { "color": "#ffffff" } ] 
	              		    },
	          		        { "featureType": "poi.park", "stylers": 
	          	   		        [ { "color": "#ffffff" } ] 
	       	   		    } 
	       	   ];
	map = new google.maps.Map(document.getElementById("mapDiv"), mapOptions);
	map.mapTypes.set('outline', new google.maps.StyledMapType(outlineMapStyleOpts, { name: name }));
	currMarker = new google.maps.Marker({
        position: map.getCenter(),
        map: map
      });
    currentMapName = name;
   google.maps.event.addListener(map, 'click', function(event) {
      currMarker.setPosition(event.latLng);
      currMarker.setVisible(true);
      currMarker.setTitle("Click again to add details about the place");
      if (document.getElementById("infoWindowPopupLat")) {
    	  document.getElementById("infoWindowPopupLat").value = Math.ceil(currMarker.getPosition().lat() * 100)/100;
      }
      if (document.getElementById("infoWindowPopupLng")) {
    	  document.getElementById("infoWindowPopupLng").value = Math.ceil(currMarker.getPosition().lng() * 100)/100;
      }
   });
}


function load()
{
	    fillWindow();
		reloadMap("Outline Map");
 }
 
 function clearMap() {
   currMarker.setVisible(false);
   if (placesMarked != null) {
     for( var i in placesMarked) {
       placesMarked[i].setMap(null);
     }
     placesMarked = null;
   }
   return true;
 }
 
 function getPlaces(selectedMap) {
   var places = {
     MetropolitanCities: [
      {
        name: "Delhi",
      	latLng: new google.maps.LatLng(29.01, 77.38)
      },
      {
        name: "Kolkata",
      	latLng: new google.maps.LatLng(22.56, 88.36)
      },
      {
          name: "Mumbai",
          latLng: new google.maps.LatLng(18.96, 72.82)
      },
      {
          name: "Chennai",
       	  latLng: new google.maps.LatLng(13.08, 80.27)
      },
      {
          name: "Hyderabad",
          latLng: new google.maps.LatLng(17.36, 78.46)
      },
      {
          name: "Bangalore",
       	  latLng: new google.maps.LatLng(12.98, 77.58)
      }
     ], 
     NuclearPowerPlants: [
		{
		    name: "Tarapur",
		  	latLng: new google.maps.LatLng(19.49, 72.39)
		  },
		  {
		    name: "Kalpakkam",
		  	latLng: new google.maps.LatLng(12.50, 80.15)
		  },
		  {
		      name: "Narora",
		      latLng: new google.maps.LatLng(27.5, 78.43)
		  },
		  {
		      name: "Kudankulam",
		   	  latLng: new google.maps.LatLng(8.163, 77.71)
		  }
     ],
     IndusValleyCivilization: [
      {
        name: "Mohenjadaro",
      	latLng: new google.maps.LatLng(27.32, 68.13)
      },
      {
          name: "Harappa",
          latLng: new google.maps.LatLng(30.63, 72.88)
       }
     ],
     MangrovesInIndia: [
		{
		 name: "Pichavaram",
		 latLng: new google.maps.LatLng(11.43, 79.77)
		},
		{
		 name: "Sundarbans",
		 latLng: new google.maps.LatLng(21.94, 88.9)
		},
		{
		 name: "Bhitarkanika",
		 latLng: new google.maps.LatLng(20.67, 87.0)
		}      
     ]
   };
   return places[selectedMap];
 }


 function loadMap(selectedMap) {
	 if (placesMarked == null) {
		    placesMarked = [];
		   } else {
		    placesMarked = null;
		    placesMarked = [];
		   }
		   reloadMap(selectedMap);
		   var placesToLoad = getPlaces(selectedMap);
		   for (var i in placesToLoad) {
		    var placeMarker = new MarkerWithLabel({
		      position: placesToLoad[i].latLng, 
		      map: map,
		      title: placesToLoad[i].name,
		      labelContent: placesToLoad[i].name,
		      labelAnchor: new google.maps.Point(30, 0),
		      labelClass: "labels",
		      labelStyle: {opacity: 0.75}
		     });
		     placesMarked.push(placeMarker);
	  }
 }
 
 function loadPlaces() {
   var selectMapOption = document.getElementById("sampleMapListDropDown");
   var selectedMap = selectMapOption.options[selectMapOption.selectedIndex].value;
   loadMap(selectedMap);
 }

 var practicePlaces=[]
 var practiceMapMarker;
 var practicePlaceNumber = 0;
 var practicePlaceAsked;
 
 var practiceScore=0;
 var practiceMaxScore=0;
 var included = false;

 function checkAnswer() {
	 practiceMapMarker=currMarker;
	 var tolerableError = 1;
	 if (practiceMapMarker.getPosition() != null) {
		 var error = Math.pow(practiceMapMarker.getPosition().lat() - practicePlaceAsked.latLng.lat(), 2.0) 
		 				+ Math.pow(practiceMapMarker.getPosition().lng() - practicePlaceAsked.latLng.lng(), 2.0);  
		 if (error <= tolerableError) {
			 if (!included)
			 	practiceScore++;
			 alert("Congratulations!! Your answer is correct, your score is now: " + practiceScore);
			 return true;
		 } else {
			 alert("Your answer is wrong, click cancel to try again");
			 return false;
		 }
		 return false;
	 }
 }
 
 function showPlace() {
	 if (practicePlaceNumber < practicePlaces.length) {
		 included = false;
		 var newHtml = "<p>Place: " + practicePlaces[practicePlaceNumber].name;
		 newHtml += "<form><input type=\"button\"  name=\"Submit\" value=\"Next\"" +
		 			"onClick=\"var r = checkAnswer(); if (r) showPlace(); return true;\"></form>";
		 document.getElementById('sampleMapPracticePlace').innerHTML = newHtml;
		 practicePlaceAsked = practicePlaces[practicePlaceNumber];
		 practicePlaceNumber++;
	 } else {
		 var newHtml = "<p>Your score is " + practiceScore + "/" + practiceMaxScore
		 					+ ". Select a different map and press 'Go' to take another test. <p>";
		 document.getElementById('sampleMapPracticePlace').innerHTML = newHtml;
	 }
 }

 function clearPracticeVars() {
	 practicePlaces=[]
	 practiceMapMarker=null;
	 practicePlaceNumber = 0;
	 practicePlaceAsked = null;
	 practiceScore=0;
	 practiceMaxScore=0;
	 document.getElementById('sampleMapPracticePlace').innerHTML = "";
 }

 function loadPlacesForPractice() {
	   clearPracticeVars();
	   var selectMapOption = document.getElementById("sampleMapPracticeDropDown");
	   var selectedMap = selectMapOption.options[selectMapOption.selectedIndex].value;
	   if (placesMarked == null) {
	    placesMarked = [];
	   } else {
	    placesMarked = null;
	    placesMarked = [];
	   }
	   practicePlaces = getPlaces(selectedMap);
	   practiceMaxScore = practicePlaces.length;
	   clearMap();
	   showPlace();
  }

  $(document).ready(function() {
	  $("#searchAddress").keyup(function(event){
			if (event.keyCode == 13) {
				mapSearch();
			}
	  });

	  refreshMap();

  });

  function refreshMap() {
	  var id = 0;
	  var mapList = ["MetropolitanCities", "MangrovesInIndia", "IndusValleyCivilization", "NuclearPowerPlants"];
	  var intervalId = setInterval(function() {
		  loadMap(mapList[id]);
		  id=(id+1)%mapList.length;
	  }, 7000);
  }

 function mapSearch() {
		$("#googleMapsSearchAjaxLoader").show();
		var addressField = document.getElementById('searchAddress');
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode(
		        {'address': addressField.value}, 
		        function(results, status) {
			        var html = "";
		            if (status == google.maps.GeocoderStatus.OK) {
		                var loc = results[0].geometry.location;
		                if (currMarker == null) {
		                	currMarker = new google.maps.Marker({
		                        position: loc,
		                        map: map,
		                        title: addressField.value,
		                        visible: true
		                    });
		                }
		                currMarker.setPosition(loc);
		                currMarker.setVisible(true);
		                map.setCenter(loc);
		                html = "<font size=2 color=green><i>" + results.length + " results found for "+ addressField.value + "</i></font>";
		                // use loc.lat(), loc.lng()
		            }
		            else {
		                html = "<font size=2 color=red><i>" + addressField.value + " could not be found</i></font>"
		            }
		            $("#mapSearchResult").html(html);
		            setTimeout(function() { $("#mapSearchResult").html("");}, 50000);
		        }
		  );
		$("#googleMapsSearchAjaxLoader").hide();
	}
 
</script>
</head>


<body onload="load()" style="background-color: #ddf7c6; height: 100%; margin: 0; padding: 0;">
    
	<div id="infoDiv"
		style="overflow: auto; border-width: 0px; position: absolute; left: 0px; top: 0px; width: 300px; height: 100%;">
	<div id="logo" style="position: absolute; left: 0px; top: 0px; width: 295px; height: 100px;">
	   <img src="static/images/Map++.jpg"/>
	</div>
	<div style="position:absolute; left: 5px; top: 105px; width: 290px">
	  <p>
			map++ is a utility built exclusively for Civil Service aspirants with
			History and Geography optionals to practice for map based questions
			which fetches easy marks with good practice. map++ allows you to create
			your own custom maps, share it with your friends and groups all for <font
				color="green"><b>FREE!</b> </font>
		</p>

		<div id="fb-root"></div>
		<script>
        window.fbAsyncInit = function() {
          FB.init({
            appId      : '<?= $app_id ?>', // App ID
            channelUrl : '<?= $url ?>/channel.html', // Channel File
            status     : true, // check login status
            cookie     : true, // enable cookies to allow the server to access the session
            xfbml      : true  // parse XFBML
          });
          FB.Event.subscribe('auth.login', function(response) {
              window.location = "home.php";
          });
        };
        
        // Load the SDK Asynchronously
        (function(d){
           var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
           if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = "//connect.facebook.net/en_US/all.js";
           ref.parentNode.insertBefore(js, ref);
         }(document));

      </script>
		<p>No registration required!, simply login with your facebook account,
			and unleash the power of maps++ and see the difference for yourself!
		</p>
		<center>
			<div class="fb-login-button" scope="email">Login</div>
		</center>

		<!--  	
      	<div 
	        class="fb-registration" 
	        data-fields="[{'name':'name'}, {'name':'email'}]" 
	        data-redirect-uri="http://UPSCMapsUtils-env-96jtppihdm.elasticbeanstalk.com/register.php" >
      	</div>
      	 -->
		<p>Not convinced still? Check out our sample maps, and some practice
			tests.</p>
		<p>View Sample Map:
		<form name="sampleMapList">
			<select id="sampleMapListDropDown">
				<option value="MetropolitanCities" selected>Metropolitan Cities</option>
				<option value="NuclearPowerPlants">Nuclear Power Plants in India</option>
				<option value="IndusValleyCivilization">Indus Valley Civilization</option>
				<option value="MangrovesInIndia">Mangroves In India</option>
			</select> <input type="button" name="Submit" value="Go"
				onClick="var r = clearMap(); if (r) loadPlaces(); return true;">
		</form>
		
		<p> Practice Map:
		<form name="sampleMapPractice">
			<select id="sampleMapPracticeDropDown">
				<option value="MetropolitanCities" selected>Metropolitan Cities</option>
				<option value="NuclearPowerPlants">Nuclear Power Plants in India</option>
				<option value="IndusValleyCivilization">Indus Valley Civilization</option>
				<option value="MangrovesInIndia">Mangroves In India</option>
			</select> <input type="button" name="Submit" value="Go"
				onClick="var r = clearMap(); if (r) loadPlacesForPractice(); return true;">
		</form>
		<div id="sampleMapPracticePlace"></div>
		
		</div>
	</div>
	<div id="mapDiv"
		style="position: absolute; left: 300px; top: 0px; height: 100%"></div>
	<div id="mapHeading" style="position:absolute; left: 400px; top: 10px; height: 30px; background: white; font: ">
	</div>
	<div id="mapSearchDiv" style="position: relative; float: right; top: 0px; height: 40px; zIndex=10;">
		<input type="text" id="searchAddress" value=""/>
		<button onclick="mapSearch();">Search In Map</button>
		<img src="static/images/ajax-loader.gif" id="googleMapsSearchAjaxLoader" style="display: none" />
		<div id="mapSearchResult"></div>
	</div>

</body>

</html>
