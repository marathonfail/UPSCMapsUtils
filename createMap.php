<?php

$mapName = $_POST['mapName'];
$mapDescription = $_POST['mapDescription'];

include_once 'fbaccess.php';
if (!$user):
require_once 'logout.php';
else:

$flash_success = NULL;
$flash_error = NULL; 

date_default_timezone_set('Asia/Kolkata');
$date = date('Y/m/d h:i:s a', time());

$originalName = $mapName;
$mapName = strtolower($mapName);
$mapName = trim($mapName);
$mapName = str_replace(" ", ".", $mapName);

$getUserMapResponse = $dynamo->get_item(array(
		"TableName" => "MapPlusPlusUserMaps",
		"Key" => $dynamo->attributes(array(
				'HashKeyElement' => $user,
				'RangeKeyElement' => $mapName,
		)),
		"AttributesToGet" => array("UserId", "MapName", "originalName", "mapDescription"),
)
);

$resultCode = 200;

if ($getUserMapResponse->isOK()) {
	$userMapItem = $getUserMapResponse->body->Item;
	if ($userMapItem) {
		$flash_success = "Map with the given name already exists";
		$resultCode = 400;
		$originalName = $userMapItem->originalName->{AmazonDynamoDB::TYPE_STRING};
		$mapDescription = $userMapItem->mapDescription->{AmazonDynamoDB::TYPE_STRING};
		// map already exists, show other options.
	} else {
			$createUserMapResponse = $dynamo->put_item(array(
					"TableName" => "MapPlusPlusUserMaps",
					"Item" => $dynamo->attributes(array(
							"UserId" => $user,
							"MapName" => $mapName,
							"mapDescription" => $mapDescription,
							"originalName" => $originalName,
							"totalPlaces" => 0,
							"createdBy" => $user,
							"creationDate" => $date
					))
			));
			
			if ($createUserMapResponse->isOK()) {
				$flash_success = "Successfully created map";	
			} else {
				$flash_error = "Unable to create map at this time, please try again later.";
				$resultCode = 500;
			}
	}
} else {
	$flash_error = "Unable to create map at this time, please try again later.";
	$resultCode = 500;
	print_r($getUserMapResponse);
}

$result = array();
if ($resultCode == 500 ) {
	$result = array(
				"error" => $flash_error,
				"resultCode" => $resultCode
			);
}
if ($resultCode == 400 || $resultCode == 200) {
	$result = array(
				"success" => $flash_success,
			    "resultCode" => $resultCode,
				"map" => array(
							array("mapName" => $originalName,
									"mapDescription" => $mapDescription)
						)
			);
}
echo json_encode($result);
endif;
?>
