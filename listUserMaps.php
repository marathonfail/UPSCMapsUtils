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
		$lastMap = strtolower($lastMap);
		$lastMap = trim($lastMap);
		$lastMap = str_replace(" ", ".", $lastMap);
	}
	
	$query = NULL;
	if (isset($_GET["query"])) {
		$query = $_GET["query"];
		$query = strtolower($query);
		$query = trim($query);
		$query = str_replace(" ", ".", $query);
	}
	
	$rangeConditions = NULL;
	
	if ($query) {
		$rangeConditions=array("ComparisonOperator" => AmazonDynamoDB::CONDITION_BEGINS_WITH,
				"AttributeValueList" => array(array(AmazonDynamoDB::TYPE_STRING => $query)));
	}
	
	if ($lastMap) {
		$rangeConditions = array("ComparisonOperator" => AmazonDynamoDB::CONDITION_GREATER_THAN,
				"AttributeValueList" => array(array(AmazonDynamoDB::TYPE_STRING => $lastMap)));
	}
	
	if ($query) {
		// ajax query with prefix = $query
		$listUserMapsResponse = $dynamo->query(array(
				"TableName"    => "MapPlusPlusUserMaps",
				"HashKeyValue" => array(
						AmazonDynamoDB::TYPE_STRING => $user
				),
				"RangeKeyCondition" => $rangeConditions,
				"Limit" => $maxMaps
		));
		if ($listUserMapsResponse->isOK()) {
			$result = array();
			$mapArray=array();
			foreach ($listUserMapsResponse->body->Items as $item) {
				$mapName = $item->originalName->{AmazonDynamoDB::TYPE_STRING};
				$mapDescription = $item->mapDescription->{AmazonDynamoDB::TYPE_STRING};
				if ($mapName) {
					$mapArray[] = array("mapName" => $mapName,
										"mapDescription" => $mapDescription);
				}
			}
			if (count($mapArray)) {
				$result=array("maps" => $mapArray, "maxMaps" => $maxMaps);
			} else {
				$result=array("error" => "No maps found");
			}
			echo json_encode($result);
		} else {
			print_r ($listUserMapsResponse);
			echo json_encode(array("error" => "There was a problem creating map, try again later."));
		}
 	} else {
		if ($lastMap) {
			$listUserMapsResponse = $dynamo->query(array(
				"TableName"    => "MapPlusPlusUserMaps",
				"HashKeyValue" => array(
						AmazonDynamoDB::TYPE_STRING => $user
				),
				"RangeKeyCondition" => $rangeConditions,
				"Limit" => $maxMaps
			));
		} else {
			$listUserMapsResponse = $dynamo->query(array(
					"TableName"    => "MapPlusPlusUserMaps",
					"HashKeyValue" => array(
							AmazonDynamoDB::TYPE_STRING => $user
					),
					"Limit" => $maxMaps
			));
		}
		
		$result = array();
		$mapArray=array();
		if ($listUserMapsResponse->isOK()) {
			$count = 0;
			foreach ($listUserMapsResponse->body->Items as $item) {
				$mapName = $item->originalName->{AmazonDynamoDB::TYPE_STRING};
				$mapDescription = $item->mapDescription->{AmazonDynamoDB::TYPE_STRING};
				$count++;
				if ($mapName) {
					$mapArray[] = array("mapName" => $mapName,
										"mapDescription" => $mapDescription);
				}
			}
			if ($count) {
				$result=array("maps" => $mapArray, "maxMaps" => $maxMaps);
			}
			else {
				$result=array(
							"error" => "No maps found",
						);
			}
		} else {
			// log this.
			$result=array(
					 "error" => "There was a problem while retrieving your maps, please try again later."
					);
		}
		echo json_encode($result);
	}
}
?>