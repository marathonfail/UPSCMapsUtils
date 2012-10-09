<?php

$mapName = $_POST['mapName'];
$mapDescription = $_POST['mapDescription'];

include_once 'fbaccess.php';
if (!$user):
require_once 'logout.php';
else:

$flash_success = NULL;
$flash_error = NULL; 

$userNum = intval($user);
date_default_timezone_set('Asia/Kolkata');
$date = date('Y/m/d h:i:s a', time());

$getUserMapResponse = $dynamo->get_item(array(
		"TableName" => "MapPlusPlusUserMaps",
		"Key" => $dynamo->attributes(array(
				'HashKeyElement' => $userNum,
				'RangeKeyElement' => $mapName,
		)),
		"AttributesToGet" => array("UserId", "MapName"),
)
);
if ($getUserMapResponse->isOK()) {
	$userMapItem = $getUserMapResponse->body->Item;
	if ($userMapItem) {
		$flash_error = "Map with the given name already exists, try with a different name";
		// map already exists, show him other options.
	} else {
			$createUserMapResponse = $dynamo->put_item(array(
					"TableName" => "MapPlusPlusUserMaps",
					"Item" => $dynamo->attributes(array(
							"UserId" => $userNum,
							"MapName" => $mapName,
							"mapDescription" => $mapDescription,
							"createdBy" => $user,
							"creationDate" => $date
					))
			));
			
			if ($createUserMapResponse->isOK()) {
				$flash_success = "Successfully created map";	
			} else {
				$flash_error = "Unable to create map at this time, please try again later.";
			}
	}
} else {
	$flash_error = "Unable to create map at this time, please try again later.";
}

?>

<font size="3" color=<?= ($flash_error) ? "red" : "green" ?>><?= ($flash_error ? $flash_error : $flash_success)?>
</font>

<?php

endif;

?>
