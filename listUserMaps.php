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
						AmazonDynamoDB::TYPE_NUMBER => $user
				),
				"RangeKeyCondition" => $rangeConditions,
				"Limit" => $maxMaps
		));
		if ($listUserMapsResponse->isOK()) {
			$result = array();
			foreach ($listUserMapsResponse->body->Items as $item) {
				$mapName = $item->originalName->{AmazonDynamoDB::TYPE_STRING};
				$mapDescription = $item->mapDescription->{AmazonDynamoDB::TYPE_STRING};
				if ($mapName) {
					$mapArray[] = array("mapName" => $mapName,
										"mapDescription" => $mapDescription);
				}
			}
			$result=array("maps" => $mapArray, "maxMaps" => $maxMaps);
			echo json_encode($result);
		}
 	} else {
		if ($lastMap) {
			$listUserMapsResponse = $dynamo->query(array(
				"TableName"    => "MapPlusPlusUserMaps",
				"HashKeyValue" => array(
						AmazonDynamoDB::TYPE_NUMBER => $user
				),
				"RangeKeyCondition" => $rangeConditions,
				"Limit" => $maxMaps
			));
		} else {
			$listUserMapsResponse = $dynamo->query(array(
					"TableName"    => "MapPlusPlusUserMaps",
					"HashKeyValue" => array(
							AmazonDynamoDB::TYPE_NUMBER => $user
					),
					"Limit" => $maxMaps
			));
		}
		
		$result = array();
		$mapArray=array();
		if ($listUserMapsResponse->isOK()) {
			foreach ($listUserMapsResponse->body->Items as $item) {
				$mapName = $item->originalName->{AmazonDynamoDB::TYPE_STRING};
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