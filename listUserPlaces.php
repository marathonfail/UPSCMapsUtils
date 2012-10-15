<?php

include_once 'fbaccess.php';
if (!$user):
require_once 'logout.php';
else:

$mapName = null;
$lastPlace = null;

if(isset($_GET['mapName'])) {
	$mapName = $_GET['mapName'];
}
if (isset($_GET['lastPlace'])) {
	$lastPlace = $_GET['lastPlace'];
}

$result = array();

$responseCode = 200;
$success = "Successfully fetched map";

$mapName = strtolower($mapName);
$mapName = trim($mapName);
$mapName = str_replace(" ", ".", $mapName);

$mapId = $user.".".$mapName;

$rangeConditions = null;

if ($lastPlace) {
	$rangeConditions = array("ComparisonOperator" => AmazonDynamoDB::CONDITION_GREATER_THAN,
			"AttributeValueList" => array(array(AmazonDynamoDB::TYPE_STRING => $lastPlace)));
}

$maxPlaces = 50;

if ($rangeConditions) {
	$listUserPlacesResponse = $dynamo->query(array(
		"TableName"    => "MapPlusPlusUserPlaces",
		"HashKeyValue" => array(
				AmazonDynamoDB::TYPE_STRING => $mapId
		),
		"RangeKeyCondition" => $rangeConditions,
		"Limit" => $maxPlaces
	));
} 
else {
	$listUserMapsResponse = $dynamo->query(array(
		"TableName"    => "MapPlusPlusUserPlaces",
		"HashKeyValue" => array(
				AmazonDynamoDB::TYPE_STRING => $mapId
		),
		"Limit" => $maxPlaces
	));
}

$places = array(); 
$result = array();
if ($listUserMapsResponse->isOK()) {
	foreach ($listUserMapsResponse->body->Items as $item) {
		$placeName = $item->PlaceName->{AmazonDynamoDB::TYPE_STRING};
		$placeDescription = $item->placeDescription->{AmazonDynamoDB::TYPE_STRING};
		$lat = $item->latitude->{AmazonDynamoDB::TYPE_STRING};
		$lng = $item->longitude->{AmazonDynamoDB::TYPE_STRING};
		if ($placeName) {
			$places[] = array("PlaceName" => $placeName,
						"placeDescription" => $placeDescription,
						"lng" => $lng,
						"lat" => $lat
						);
		}
	}
}
if (count($places) == 0) {
	$result[] = array(
				"placesReturned" => 0,
				"message" => ($listUserMapsResponse->isOK()? "No places found for the map" : "Error while getting the places, please try again later.")
			);
} else {
	$result[] = array(
		"places" => $places,
		"maxPlaces" => $maxPlaces,
		"placesReturned" => count($places),
		"message" => "Successfully fetched places"  
	);
}

echo json_encode($result);

endif;
