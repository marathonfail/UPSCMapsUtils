<?php

$mapName = $_POST['mapName'];
$mapDescription = $_POST['mapDescription'];

include_once 'fbaccess.php';
if (!$user):
require_once 'logout.php';
else:
$dynamo = new AmazonDynamoDB();
$dynamo->set_hostname("dynamodb.ap-southeast-1.amazonaws.com");
$dynamo->disable_ssl_verification();

$flash_success = NULL;
$flash_error = NULL;

$getMapResponse = $dynamo->get_item(array(
		"TableName" => "MapPlusPlusMaps",
		"Key" => $dynamo->attributes(array(
				"HashKeyElement" => $mapName,
		)),
		"AttributesToGet" => array("MapName"),
)
);
if ($getMapResponse->isOK()) {
	$mapItem = $getMapResponse->body->Item;
	if ($mapItem) {
		$flash_error = "Map with the given name already exists, use a new name";
		// map already exists.
	} else {
		// create a new map.
		$createMapResponse = $dynamo->put_item(array(
				"TableName" => "MapPlusPlusMaps",
				"Item" => $dynamo->attributes(array(
						"MapName" => $mapName,
						"mapDescription" => $mapDescription
				)
				)
		));
		if ($createMapResponse->isOK()) {
			$flash_success = "Successfully created map";
		} else {
			$flash_error = "Unable to create map at this time, please try again later.";
		}
	}
}

?>

<font size="3" color=<?= ($flash_error) ? "red" : "green" ?>><?= ($flash_error ? $flash_error : $flash_success)?>
</font>

<?php

  if ($flash_success) {
	 
  } else {
	 
  }

endif;

?>
