<?php
$responseCode = 200;
$success = "Successfully added map";
$result = array(
		  "responseCode" => $responseCode,
		  "success" => $success
		);
echo json_encode($result);
?>