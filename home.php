<?php 
require_once 'fbaccess.php';
if (!$user):
require_once 'logout.php';
else:
?>

<html>
<head>
<title>maps++ | Maps for IAS/IPS/IFS Exams</title>

<style type="text/css">
.tabs li {
	list-style: none;
	display: inline;
}

.tabs a {
	padding: 5px 10px;
	display: inline-block;
	background: #666;
	color: #fff;
	text-decoration: none;
}

.tabs a.active {
	background: #fff;
	color: #000;
}

.myMapsTabsClass ul {
	text-align: left;
	list-style: none;
	padding: 0;
	margin: 0 auto;
}

.myMapsTabsClass li {
	display: block;
	margin: 0;
	padding: 0;
}

.myMapsTabsClass li a {
	display: block;
	padding: 0.5em 0 0.5em 2em;
	border-width: 1px;
	border-color: #ffe #aaab9c #ccc #fff;
	border-style: solid;
	color: #777;
	text-decoration: none;
	background: #f7f2ea;
}

;
.myMapsTabsClass li a.active {
	background: transparent;
	color: #800000;
}

.myMapsTabsClass li a:hover {
	color: #800000;
	background: transparent;
	border-color: #aaab9c #fff #fff #ccc
}
</style>
<script
	src="js/jquery-1.8.2.js"></script>

<script
	src="http://maps.google.com/maps/api/js?key=AIzaSyByDaJQtdfxMBDxYRXVQqISAXCgCqSKul0&sensor=false"
	type="text/javascript">
    </script>
<script src="js/MarkerWithLabel.js" type="text/javascript"></script>
<script src="js/json-parse.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.0.custom.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="static/css/jquery-ui-1.9.0.custom.min.css"/>
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

<script language="javascript" type="text/javascript">
 //<![CDATA[

//Globals
 //initialise the map 
 var map;
 var currMarker;
 var placesMarked = [];
 var outlineMapStyleOpts;
 
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
			  mapTypeControl: true,
			  mapTypeControlOptions: {
				  mapTypeIds: ['outline', google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.TERRAIN],
			      position: google.maps.ControlPosition.TOP_LEFT
			  },
	          center: new google.maps.LatLng(22.0, 81.0),
	          zoom: 5,
	          mapTypeId: 'outline'
	        };
	map = new google.maps.Map(document.getElementById("mapDiv"), mapOptions);
	map.mapTypes.set('outline', new google.maps.StyledMapType(outlineMapStyleOpts, { name: name }));
	currMarker = new google.maps.Marker({
        position: map.getCenter(),
        map: map
      });
  
   google.maps.event.addListener(map, 'click', function(event) {
      currMarker.setPosition(event.latLng);
      currMarker.setVisible(true);
      currMarker.setTitle("Click again to add details about the place");
   });
}


function load()
{
	    fillWindow();
	    
		var mapOptions = {
		  mapTypeControl: true,
		  mapTypeControlOptions: {
			  mapTypeIds: ['outline', google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.TERRAIN],
		      position: google.maps.ControlPosition.TOP_LEFT
		  },
          center: new google.maps.LatLng(22.0, 81.0),
          zoom: 5,
          mapTypeId: 'outline'
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
	    map.mapTypes.set('outline', new google.maps.StyledMapType(outlineMapStyleOpts, { name: 'Outline Map' }));
	    
	    currMarker = new google.maps.Marker({
	          position: map.getCenter(),
	          map: map
	        });
	    
	    google.maps.event.addListener(map, 'click', function(event) {
	        currMarker.setPosition(event.latLng);
	        currMarker.setVisible(true);
	        currMarker.setTitle("Click again to add details about the place");
	     });
	     
	     google.maps.event.addListener(currMarker, 'click', function(event) {
	        
	     }); 
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
 
 function loadPlaces(mapName) {
   var selectedMap = mapName;
   clearMap();
   if (placesMarked == null) {
    placesMarked = [];
   } else {
    placesMarked = null;
    placesMarked = [];
   }
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

  function loadNextSetOfMaps(lastProcessed) {
	  $("#myMapsAjaxLoader").show();
	  $.get('listUserMaps.php', {lastProcessedMap: lastProcessed}, function(data) {
		  parseAndFillDocument(data);
	  });
  }


 function loadMap(mapName) {
	 reloadMap(mapName);
	 loadPlaces(mapName);
 }

 function parseAndFillDocument(data) {
	 	$("#myMapsAjaxLoader").hide();
		var html = "";
		var table="<table border=0>";
		var jsonData = json_parse(data);
		var listOfUserMaps = jsonData.maps;
		var lastProcessed = "";
		for (var i=0;i<listOfUserMaps.length;i++) {
			table+="<tr>";
			var mapName=listOfUserMaps[i].mapName[0];
			var mapDescription = listOfUserMaps[i].mapDescription[0];
			var td="<td style=\"width: 270px;\" onmouseout=\"this.style.background='transparent';\"  onmouseover=\"this.style.background='white'; this.style.cursor='pointer';\""
				              +  " onclick=\"loadMap('"  + mapName + "')\">"	 + mapName + "<br> <font size=1><i>(" 
							  + mapDescription + ")</i></font>" +  "</td>";
			table+=td;
			table+="</tr>";
			lastProcessed = mapName;
		}
		table+="</table>";
		html += table;
		if (listOfUserMaps.length >= jsonData.maxMaps) {
			html += "<button style=\"float: right;\" type=\"button\" onclick=\"loadNextSetOfMaps('" + lastProcessed + "')\">View more maps</button>";
		}
		if (listOfUserMaps.length == 0) {
			html = "<font size=2><i>There are no more maps.</i></font>";
		}
		$("#myMaps").html(html);
		$("myMaps").addClass("mapListClass");
 }



//Wait until the DOM has loaded before querying the document
	$(document).ready(function(){
		$('ul.tabs').each(function(){
			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $content, $links = $(this).find('a');

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
			$active.addClass('active');
			$content = $($active.attr('href'));

			// Hide the remaining content
			$links.not($active).each(function () {
				$($(this).attr('href')).hide();
			});

			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active.removeClass('active');
				$content.hide();

				// Update the variables with the new link and content
				$active = $(this);
				$content = $($(this).attr('href'));

				// Make the tab active.
				$active.addClass('active');
				$content.show();

				// Prevent the anchor's default click action
				e.preventDefault();
			});
		});

		$.get('listUserMaps.php', function(data) {
			parseAndFillDocument(data);
		});

		// this is the id of the submit button
		$("#submitButton").click(function() {
		    var url = "createMap.php"; // the script where you handle the form input.
			document.getElementById("createMapAjaxLoader").style.visibility='visible';
		    $.ajax({
		           type: "POST",
		           url: url,
		           data: $("#createMapForm").serialize(), // serializes the form's elements.
		           success: function(data)
		           {
		        	   document.getElementById("createMapAjaxLoader").style.visibility='hidden';
		               document.getElementById("createMapResponse").innerHTML = data; // show response from the php script.
		           }
	         });

		    return false; // avoid to execute the actual submit of the form.
		});

		$("#mapSearchInput").keypress(function(){
			var searchInput = document.getElementById("mapSearchInput").value;
			if (searchInput.length >= 2){
				 $("#myMapsAjaxLoader").show();
				 $.get('listUserMaps.php', {query: searchInput}, function(data) {
					  parseAndFillDocument(data);
				 });
			}
		});
	});

</script>
</head>


<body onload="load()" style="background-color: #D8D8D8">
	<div id="infoDiv"
		style="overflow: auto; border-width: 0px; position: absolute; left: 5px; top: 0px; width: 290px; height: 100%;">
		Welcome,
		<?= $user_profile['name']?>
		<a href=<?= $logoutUrl ?>>Logout</a>
		<ul class='tabs'>
			<li><a href='#MapsTab'>Maps</a></li>
			<li><a href='#PracticeTab'>Practice</a></li>
		</ul>
		<div id='MapsTab'>
			<p>You can add places to new maps or the existing maps.</p>
			<div id="mapSearch">
    			<label for="mapSearchInput">Search</label>
    			<input type="text" id="mapSearchInput"/>
    			<div id="mapSearchOutputDiv"></div>
			</div>
			<img src="static/images/ajax-loader.gif" id="myMapsAjaxLoader" />
			<div id="myMaps">
			</div>
			<div id="myMapsTabsList"></div>
			<div id="createMap">
				<form id="createMapForm" action="createMap.php" method="post">
					<fieldset>
						<legend>Create Map</legend>
						<input type="text" name="mapName" value="Name of the map"
							onblur='if(this.value=="") this.value="Name of the map";'
							onfocus='if(this.value=="Name of the map") this.value="";'
							size=15 /> <br> <input type="text" name="mapDescription"
							value="Description of the map"
							onblur='if(this.value=="") this.value="Description of the map";'
							onfocus='if(this.value=="Description of the map") this.value="";'
							size=35 /> <input type="submit" id="submitButton"
							value="Create Map" /> <img src="static/images/ajax-loader.gif"
							style="visibility: hidden" id="createMapAjaxLoader" />
					</fieldset>
				</form>
				<div id="createMapResponse"></div>
			</div>
		</div>
		<div id='PracticeTab'>
			<p>Take map practice here.</p>
		</div>
	</div>
	<div id="mapDiv"
		style="position: absolute; left: 300px; top: 0px; height: 100%"></div>

</body>

</html>

<?php endif; 
?>