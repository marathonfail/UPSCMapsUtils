<?php
$app_id		= "497255016953576";
$app_secret	= "fbb56419582583afa797ec36546b98b8";
$site_url	= "http://upscmapsutils-env-96jtppihdm.elasticbeanstalk.com/";

include_once 'vendor/facebook/src/facebook.php';

$facebook = new Facebook(array(
		'appId' => $app_id,
		'secret' => $app_secret,
));

$user = $facebook->getUser();

if($user) {
	try{
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	}catch(FacebookApiException $e){
		error_log($e);
		$user = NULL;
	}
}

if($user)  {
	$loginUrl = $facebook->getLoginUrl(array(
			//'scope'		=> 'Your list of Permissions', // Permissions to request from the user
			'redirect_uri'	=> 'http://upscmapsutils-env-96jtppihdm.elasticbeanstalk.com/', // URL to redirect the user to once the login/authorization process is complete.
	));
} else {

	$logoutUrl = $facebook->getLogoutUrl(array(
			'next'	=> 'http://upscmapsutils-env-96jtppihdm.elasticbeanstalk.com', // URL to which to redirect the user after logging out
	));
}

if ($user) {
	try {
		$user_info = $facebook->api('/'.$user);
	} catch (FacebookApiException $e) {
		error_log($e);
	}
}




?>