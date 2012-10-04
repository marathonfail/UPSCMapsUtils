<?php include_once 'fbaccess.php'; ?>



<html>
  <head>
    <title>Maps for IAS/IPS/IFS Exams</title>
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyByDaJQtdfxMBDxYRXVQqISAXCgCqSKul0&sensor=false" 
    		type="text/javascript">
    </script>

<script language="javascript" type="text/javascript">
 //<![CDATA[

//Globals
 //initialise the map 
 var map;
 var currMarker;
 var placesMarked = [];
 
function fillWindow(){
	var mapDiv = document.getElementById("mapDiv");
	var mapsMenu = document.getElementById("mapsMenu");
	try{
		if (window.innerHeight) { //if browser supports window.innerWidth
			mapDiv.style.height = window.innerHeight+'px';
			mapDiv.style.width = window.innerWidth-300+'px';
			mapsMenu.style.height = window.innerHeight-245+'px';
		}
		else{	//MSIE
			document.body.scroll="no";
			mapDiv.style.height = document.body.clientHeight+'px';
			mapDiv.style.width = document.body.clientWidth-300+'px'; 
			mapsMenu.style.height = document.body.clientHeight-245+'px';
        }
	}
	catch(ex){
	}
}


function load()
{
	    fillWindow();
	    
		var mapOptions = {
          center: new google.maps.LatLng(22.0, 81.0),
          zoom: 5,
          mapTypeId: google.maps.MapTypeId.SATELLITE
        };
        
	    map = new google.maps.Map(document.getElementById("mapDiv"), mapOptions);
	    
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
     MauryanEmpire: [
      {
        name: "place1",
      	latLng: new google.maps.LatLng(10.0, 78.0)
      },
      {
        name: "place2",
      	latLng: new google.maps.LatLng(20.0, 74.0)
      }
     ], 
     MajorRockEdicts: [
      {
        name: "place3",
      	latLng: new google.maps.LatLng(26, 73.0)
      },
      {
        name: "place4",
      	latLng: new google.maps.LatLng(26.0, 86.0)
      }
     ],
     MinorRockEdicts: [
      {
        name: "place5",
      	latLng: new google.maps.LatLng(29.5, 87.5)
      }
     ],
     MauryanEmpireUnderAshoka: [
		{
		 name: "place6",
		 latLng: new google.maps.LatLng(25.0, 82.0)
		},
		{
		 name: "place7",
		 latLng: new google.maps.LatLng(27.9, 88.0)
		},
		{
		 name: "place8",
		 latLng: new google.maps.LatLng(30.0, 75.0)
		}      
     ]
   };
   return places[selectedMap];
 }
 
 function loadPlaces() {
   var selectMapOption = document.getElementById("mapListMenu");
   var selectedMap = selectMapOption.options[selectMapOption.selectedIndex].value;
   if (placesMarked == null) {
    placesMarked = [];
   } else {
    placesMarked = null;
    placesMarked = [];
   }
   var placesToLoad = getPlaces(selectedMap);
   for (var i in placesToLoad) {
    var placeMarker = new google.maps.Marker({
      position: placesToLoad[i].latLng, 
      map: map,
      title: placesToLoad[i].name
     });
     placesMarked.push(placeMarker);
    }
 }
 
</script>
    
</head>
  
  <body onload="load()" style="background-color:#C0C0C0;">
    <div id="mapsMenu" style="overflow:auto; border-width: 0px; position: absolute; left: 5px; top: 0px; width: 290px; height:100%;">
    	<div id="fb-root"></div>
     	 <script>
	       	 window.fbAsyncInit = function() {
	          FB.init({
	            appId      : '497255016953576', // App ID
	            channelUrl : 'http://UPSCMapsUtils-env-96jtppihdm.elasticbeanstalk.com/channel.html', // Channel File
	            status     : true, // check login status
	            cookie     : true, // enable cookies to allow the server to access the session
	            xfbml      : true  // parse XFBML
	          });
	          // Additional initialization code here
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
      	<?php  if (!$user) { ?>
      		<div class="fb-login-button">Login</div>
      	<?php } else { ?>
      		<div class="fb-logout-button">Logout</div>
      	<?php }?>
      	
      	<div 
	        class="fb-registration" 
	        data-fields="[{'name':'name'}, {'name':'email'}]" 
	        data-redirect-uri="http://UPSCMapsUtils-env-96jtppihdm.elasticbeanstalk.com/register.php" >
      	</div>
      <form name="mapList">
		<select id="mapListMenu">
			<option value="MauryanEmpire" selected>Mauryan Empire</option>
			<option value="MauryanEmpireUnderAshoka">Mauryan Empire Under Ashoka</option>
			<option value="MajorRockEdicts">Major Rock Edicts</option>
			<option value="MinorRockEdicts">Minor Rock Edicts</option>
		</select>
		<input type="button" name="Submit" value="Go" 
			onClick="var r = clearMap(); if (r) loadPlaces(); return true;">
      </form>
      <p>
      <img src="http://code.google.com/appengine/images/appengine-silver-120x30.gif" 
		 alt="Powered by Google App Engine" />
	  </p>
    </div>

   <div id="mapDiv" style="position:absolute; left:300px; top: 0px; height: 100%"></div>
    
  </body>
  
</html>