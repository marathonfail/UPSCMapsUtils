<?php
include_once 'fbaccess.php';
if (!$user) {
	require_once 'logout.php';
} 
else {

	$maxMaps = 5;
	$mapsRetrieved = 0;
	$flash_success = NULL;
	$flash_error = NULL;
	
	$lastMap = NULL;
	
	if (isset($_GET["lastProcessedMap"])) {
		$lastMap = $_GET["lastProcessedMap"];
	}
	
	$query = NULL;
	if (isset($_GET["q"])) {
		$query = $_GET["q"];
	}
	
	if ($query) {
		// ajax query with prefix = $query
		$listUserMapsResponse = $dynamo->query(array(
				"TableName"    => "MapPlusPlusUserMaps",
				"HashKeyValue" => array(
						AmazonDynamoDB::TYPE_NUMBER => $user
				),
				"RangeKeyCondition" => array(
						"ComparisonOperator" => AmazonDynamoDB::CONDITION_GREATER_THAN,
						"AttributeValueList" => array(
								array(AmazonDynamoDB::TYPE_STRING => $lastMap)
						)
				),
		));
		if ($listUserMapsResponse->isOK()) {
			foreach ($listUserMapsResponse->body->Items as $item) {
				echo $item->MapName->{AmazonDynamoDB::TYPE_STRING}."\n";
			}
		}
 	} else {
	
		if ($lastMap) {
			$listUserMapsResponse = $dynamo->query(array(
				"TableName"    => "MapPlusPlusUserMaps",
				"HashKeyValue" => array(
						AmazonDynamoDB::TYPE_NUMBER => $user
				),
				"RangeKeyCondition" => array(
						"ComparisonOperator" => AmazonDynamoDB::CONDITION_GREATER_THAN,
						"AttributeValueList" => array(
								array(AmazonDynamoDB::TYPE_STRING => $lastMap)
						)
				),
			));
		} else {
			$listUserMapsResponse = $dynamo->query(array(
					"TableName"    => "MapPlusPlusUserMaps",
					"HashKeyValue" => array(
							AmazonDynamoDB::TYPE_NUMBER => $user
					),
			));
		}
		
		$result = array();
		$mapArray=array();
		if ($listUserMapsResponse->isOK()) {
			foreach ($listUserMapsResponse->body->Items as $item) {
				$mapName = $item->MapName->{AmazonDynamoDB::TYPE_STRING};
				$mapDescription = $item->mapDescription->{AmazonDynamoDB::TYPE_STRING};
				if ($mapName) {
					$mapArray[] = array("mapName" => $mapName,
										"mapDescription" => $mapDescription);
				}
			}
			$result=array("maps" => $mapArray, "maxMaps" => $maxMaps);
		} else {
			// log this.
			$result=array("maps" => $mapArray,
					 "error" => "There was a problem while retrieving your maps, please try again later."
					);
		}
		echo json_encode($result);
	}
}
?>