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

.createMapOverlayClass {
     position: absolute;
     left: 30px;
     top: 165px;
     width: 30%;
     height:20%;
     text-align:left;
     background-color: white;
}

</style>

<script language="javascript" type="text/javascript">
 //<![CDATA[

//Globals
 //initialise the map 
 var map;
 var currentMapName;
 var currMarker;
 var placesMarked = [];
 var outlineMapStyleOpts;
 var modalId; // Id of open modal window.
 var infoWindow;
 
function fillWindow() {
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
   google.maps.event.addListener(currMarker, 'click', function(event) {
	   var html = "<div id=\"\">";
	   var lat = Math.ceil(currMarker.getPosition().lat() * 100.0)/100;
	   var lng = Math.ceil(currMarker.getPosition().lng() * 100.0)/100;
	   html += "<form id=\"addPlaceForm\">";
	   html += "<label>Map Name</label><input type=\"text\" name=\"mapName\" value=\"" + currentMapName + "\" readOnly=\"readOnly\"/>";
	   html += "<label>Place Name</label>";
	   html += "<input type=\"text\" name=\"placeName\"/><br>";
	   html += "<label>Position: </label> Lat: <input id=\"infoWindowPopupLat\" type=\"text\" width=6 name=\"lat\" value=\"" + lat +
	   					"\"/>,  Lng: <input id=\"infoWindowPopupLng\" type=\"text\" width=6 name=\"lng\" value=\"" + lng + "\"/><br>";
	   html += "<label>Description:</label><textarea rows=6 cols=50 name=\"description\" maxlength=300></textarea><br>"; 
	   html += "<button type=\"submit\" onclick=\"return addPlace()\" name=\"Add\">Add Place</button>";
	   html += "<img src=\"static/images/ajax-loader.gif\" style=\"visibility: hidden\" id=\"addPlaceAjaxLoader\" />";
	   html += "</form>";
	   html += "<div id=\"addPlaceResponse\"></div>";
	   html += "</div>";
	   if (infoWindow) {
		   infoWindow.close();
	   }
	   infoWindow = new google.maps.InfoWindow({
		    content: html 
	   });
	   infoWindow.open(map, currMarker);
   });
}

function addPlace() {
	document.getElementById("addPlaceAjaxLoader").style.visibility='visible';
	$.ajax({
        type: "POST",
        url: "addPlaceToUserMap.php",
        data: $("#addPlaceForm").serialize(), // serializes the form's elements.
        success: function(data)
        {
            var jsonData = json_parse(data);
            if (jsonData.responseCode == 200) {
            	var message = "<font size=3 color=green>" + jsonData.success + "</font>";
                $("#addPlaceResponse").html(message);
                document.getElementById("addPlaceAjaxLoader").style.visibility='hidden';
                setTimeout(function() { if (infoWindow) infoWindow.close(); }, 3000);
            }
        }
	});
	return false;
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
		  parseAndFillDocument(data, {});
	  });
  }


 function loadMap(mapName) {
	 reloadMap(mapName);
	 loadPlaces(mapName);
 }

 function replaceAll(string, replace, wit) {
	 var result = "";
	 for(var i=0;i<string.length;i++) {
		 if (string[i]==replace[0]) result += wit;
		 else result += string[i]; 
	 }
	 return result;
 }

 function getTr(userMapName, userMapDescription) {
	 var moddedUserName=replaceAll(userMapName, " ", ",");
	 var tr = "<tr><td style=\"width: 270px;\" onmouseout=\"this.style.background='transparent';\"  onmouseover=\"this.style.background='white'; this.style.cursor='pointer';\""
     +  " onclick=\"loadMap('"  + userMapName + "')\">"	 + userMapName + "<br> <font size=1><i>(" 
	  + userMapDescription + ")</i></font>" +  "</td></tr>";
	  return tr;
 }

 function addMapToList(userMapName, userMapDescription, args) {
	 if (document.getElementById("myMapsList")) {
		 $("#myMapsList tbody").append(getTr(userMapName, userMapDescription));
	 } else {
		var table="<table id=\"myMapsList\" border=0>";
		table += getTr(userMapName, userMapDescription);
		table+="</table>";
		$("#myMaps").html(table);
	 }
 }

 function parseAndFillDocument(data, args) {
	    var available = false;
	 	$("#myMapsAjaxLoader").hide();
		var html = "";
		var table="<table id=\"myMapsList\" border=0>";
		var jsonData = json_parse(data);
		var matchStr = null;
		if (args["matchStr"]) {
			matchStr = args["matchStr"]; 
		}
		var listOfUserMaps = jsonData.maps;
		var lastProcessed = "";
		for (var i=0;i<listOfUserMaps.length;i++) {
			var mapName=listOfUserMaps[i].mapName[0];
			if (mapName == matchStr) {
				available = true;
			}
			var mapDescription = listOfUserMaps[i].mapDescription[0];
			table+=getTr(mapName, mapDescription);;
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
		//$("#myMaps").addClass("mapListClass");
		return available;
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
			parseAndFillDocument(data, {});
		});

		$("#mapSearchInput").keypress(function(){
			var searchInput = document.getElementById("mapSearchInput").value;
			$("#mapSearchOutputDiv").html("");
			if (searchInput.length >= 2) {
				 $("#myMapsAjaxLoader").show();
				 var available = false;
				 $.get('listUserMaps.php', {query: searchInput}, function(data) {
					  available = parseAndFillDocument(data, {});
				 });
				 if (!available) {
					 var html="<font size=2><i>This map is not available. <a onclick=\"createMapWithDescription()\" href=\"#\">Create this map</a></i></font>";
					 $("#mapSearchOutputDiv").html(html);
				 } else {
					 $("#mapSearchOutputDiv").html("");
				 }
			}
		});

		$(document).keyup(function(e){
            if(e.keyCode == 27){
                if (modalId != null) {
                    $("#transparentDiv").hide();
                    modalId=null;
                }
            }
        });
	});

	function createMapWithDescription() {
		var createMapOverlayHtml = "";
		mapSearchInput = document.getElementById("mapSearchInput").value;
		createMapOverlayHtml += "<form id=\"createMapOverlayForm\"><fieldset><legend>Create Map</legend>";
		createMapOverlayHtml += "<label>Map Name</label><input type=\"text\" name=\"mapName\" value=\"" + mapSearchInput + "\"/><br>";
		createMapOverlayHtml += "<label>Description</label><input type=\"text\" name=\"mapDescription\"/>";
		createMapOverlayHtml += "<input type=\"submit\" onClick=\"return createMap()\" id=\"createMapSubmitButton\" name=\"Create Map\"/></fieldset></form>";
		createMapOverlayHtml += "[Press Esc to go back]";
		createMapOverlayHtml += "<img src=\"static/images/ajax-loader.gif\" style=\"visibility: hidden\" id=\"createMapAjaxLoader\" />";
		$("#modalDiv").html(createMapOverlayHtml);
		$("#modalDiv").attr("class", "createMapOverlayClass");
		$("#transparentDiv").show();
		modalId = "modalDiv";
	}

	function createMap() {
		var url = "createMap.php"; // the script where you handle the form input.
		document.getElementById("createMapAjaxLoader").style.visibility='visible';
	    $.ajax({
	           type: "POST",
	           url: url,
	           data: $("#createMapOverlayForm").serialize(), // serializes the form's elements.
	           success: function(data)
	           {
		           jsonData = json_parse(data);
		           var message = "";
		           if (jsonData.resultCode == 500) {
			           // error
			           message = "<font size=3 color=red>" + jsonData.error + "</font>";
		           } else {
			           message = "<font size=3 color=green>" + jsonData.success + "</font>";
			           addMapToList(jsonData.map[0].mapName, jsonData.map[1].mapDescription);
		               reloadMap(jsonData.map[0].mapName);
		           }
		           
	               $("#createMapResponse").html(message); // show response from the php script.
	               setTimeout(function() { $("#createMapResponse").html(""); }, 7000);
	               $("#transparentDiv").hide();
	               modalId = null;
	           }
         });
        return false;
	}

</script>
</head>

<body onload="load()" style="background-color: #D8D8D8; height: 100%; margin: 0; padding: 0;">
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
			<div id="mapSearch">
    			<label for="mapSearchInput">Search</label>
    			<input type="text" id="mapSearchInput"/>
    			<img src="static/images/ajax-loader.gif" id="myMapsAjaxLoader" />
    		</div>
    		<div id="mapSearchOutputDiv"></div>
    		<div id="createMapOverlay"></div>
    		<div id="createMapResponse"></div>
			<div id="myMaps"></div>
		</div>
		<div id='PracticeTab'>
			<p>Coming Soon..</p>
		</div>
	</div>
	<div id="mapDiv"
		style="position: absolute; left: 300px; top: 0px; height: 100%"></div>
	
	<div id="transparentDiv" style="display: none; background: transparent; position: absolute; left: 0px; top: 0px; height:100%; width: 100%">
		<div id="modalDiv">
		</div>
	</div>

</body>

</html>

<?php endif; 
?>