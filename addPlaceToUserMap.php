<?php

include_once 'fbaccess.php';
if (!$user):
require_once 'logout.php';
else:

$mapName = $_POST['mapName'];
$placeName = $_POST['placeName'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$placeDescription = $_POST['placeDescription'];

$result = array();

$responseCode = 200;
$success = "Successfully added map";

$mapName = strtolower($mapName);
$mapName = trim($mapName);
$mapName = str_replace(" ", ".", $mapName);

$mapId = $user.$mapName;

$getMapResponse = $dynamo->get_item(array(
		"TableName" => "MapPlusPlusUserMaps",
		"Key" => $dynamo->attributes(array(
				'HashKeyElement' => $user,
				'RangeKeyElement' => $mapName,
		)),
		"AttributesToGet" => array("UserId", "originalName", "totalPlaces"),
		));

if ($getMapResponse->isOK()) {
	if ($getMapResponse->body->Item) {
		// Check if the map exists.
		$getPlaceResponse = $dynamo->get_item(array(
				"TableName" => "MapPlusPlusUserPlaces",
				"Key" => $dynamo->attributes(array(
						'HashKeyElement' => $mapId,
						'RangeKeyElement' => $placeName,
				)),
				"AttributesToGet" => array("MapId", "PlaceName", "placeDescription"),
		)
		);
		//print_r($getMapResponse);
		
		// Check if the place already exists.
		if ($getPlaceResponse->isOK()) {
			if ($getPlaceResponse->body->Item) {
				// This place is already marked.
				$result = array(
						   "responseCode" => 200,
						   "success" => "The ".$placeName." already exists in the map"
						);
			} else {
				$addPlaceResponse = $dynamo->put_item(array(
						"TableName" => "MapPlusPlusUserPlaces",
						"Item" => $dynamo->attributes(array(
								"MapId" => $mapId,
								"PlaceName" => $placeName,
								"placeDescription" => $placeDescription,
								"latitude" => $lat,
								"longitude" => $lng,
								"createdBy" => $user,
								"creationDate" => $date
						))
				));
				if ($addPlaceResponse->isOK()) {
					// Adding place was successful.
					$result = array(
							"responseCode" => $responseCode,
							"message" => $success
					);
					
				} else {
					$result = array(
							"responseCode" => 500,
							"message" => "Unable to add this place at this time, please try again later"
							);
					// Adding place was failure.
				}
				
			}
		}
	} else {
		$result = array(
					"responseCode" => 404,
				"message" => "The specified map doesn't exist, please create this map."
				);
	}
} else {
	//print_r($getMapResponse);
	$result = array(
			"responseCode" => 500,
			"message" => "Unable to add this place at this time, please try again later"
	);
}


echo json_encode($result);
endif;
?>