<?php include_once 'fbaccess.php';
if (!$user) {
	require_once 'unregistered.php';
} else {
	include_once 'home.php';
}
?>
