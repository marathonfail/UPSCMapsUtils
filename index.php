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

<script language="javascript" type="text/javascript">
 //<![CDATA[

//Globals
 //initialise the map 
 var map;
 var currMarker;
 var placesMarked = [];
 
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


function load()
{
	    fillWindow();
	    
		var mapOptions = {
		  mapTypeControlOptions: {
			  mapTypeIds: ['outline', google.maps.MapTypeId.ROADMAP, google.maps.MapTypeId.TERRAIN]
		  },
          center: new google.maps.LatLng(22.0, 81.0),
          zoom: 5,
          mapTypeId: 'outline'
        };

        var outlineMapStyleOpts = [ 
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
	    map.mapTypes.set('outline', new google.maps.StyledMapType(outlineMapStyleOpts, { name: 'Outline' }));
	    
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
</script>

</head>

<body onload="load()" style="background-color: #D8D8D8" >
	<div id="infoDiv"
		style="overflow: auto; border-width: 0px; position: absolute; left: 5px; top: 0px; width: 290px; height: 100%;">
		<?php if ($user):
			require_once 'home.php';
		   else :
		   	require_once 'unregistered.php';
		   endif; 
		?>
	<div id="mapDiv"
		style="position: absolute; left: 300px; top: 0px; height: 100%"></div>

</body>

</html>
