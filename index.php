<?php include_once 'fbaccess.php';
if (!$user) {
	include_once 'unregistered.php';
} else {
	include_once 'home.php';
}
?>

