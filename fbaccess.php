<?php

include_once 'properties.php';

$app_id		= $fb_app_id;
$app_secret	= $fb_secret_id;
$site_url	= $url;

$awsAccessId = "AKIAI73JNSH4BLSH3EEA";
$awsSecretKey = "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";

include_once 'vendor/facebook/src/facebook.php';
include_once 'vendor/amazonwebservices/aws-sdk-for-php/sdk.class.php';

session_start();

$facebook = new Facebook(array(
		'appId' => $app_id,
		'secret' => $app_secret,
));

$dynamo = new AmazonDynamoDB();
$dynamo->set_hostname("dynamodb.ap-southeast-1.amazonaws.com");
$dynamo->disable_ssl_verification();

$user = $facebook->getUser();

if ($user)  {
	try {
		$user_profile = $facebook->api('/me');
	}catch(FacebookApiException $e) {
		$user=0;
	}
	if (!isset($_SESSION['UserId'])) {
		date_default_timezone_set('Asia/Kolkata');
		$date = date('Y/m/d h:i:s a', time());
		$validUser = false;
		if($user) {
			try{
				// Proceed knowing you have a logged in user who's authenticated.
				if ($user_profile != null) {
					$response = $dynamo->get_item(array(
							"TableName" => "MapPlusPlusUsers",
							"Key" => $dynamo->attributes(array(
									"HashKeyElement" => $user,
							)),
							"AttributesToGet" => array("UserId"),
					)
					);
					if ($response->isOK()) {
						$item = $response->body->Item;
						if (!$item) {
							$response_write = $dynamo->put_item(array(
									"TableName" => "MapPlusPlusUsers",
									"Item" => $dynamo->attributes(array(
											"UserId" => $user,
											"UserName" => $user_profile['name'],
											"email" => $user_profile['email'],
											"gender" => $user_profile['gender'],
											"CurrentDate" =>  $date
									)
									)
							));
							if ($response_write->isOK()) {
								$validUser = true;
							}
						} elseif(strcmp($user, (string)$item->UserId->{AmazonDynamoDB::TYPE_STRING}) == 0) {
							$validUser = true;
						} else {
							print "Still not a valid user";
						}
					}
				} else {
					print "User profile: null";
				}
			}catch(FacebookApiException $e){
				error_log($e);
				$user = NULL;
			}
			if ($validUser) {
				$_SESSION['UserId'] = $user;
				$_SESSION['UserProfile'] = $user_profile;
				$logoutUrl = $facebook->getLogoutUrl(array(
						'next'	=> $url.$project_path."/logout.php", // URL to which to redirect the user after logging out
				));
			}
		}
	} else if ($user) {
		$user_profile = $_SESSION['UserProfile'];
		$logoutUrl = $facebook->getLogoutUrl(array(
				'next'	=> $url.$project_path."/logout.php", // URL to which to redirect the user after logging out
		));
	}
}

$facebook->destroySession();

?>