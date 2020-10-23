<?php
// Handle server -> rabbit login & registration requests here

session_start();

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");

if (!isset($_POST))
{
	$msg = "NO POST MESSAGE SET, POLITELY FUCK OFF";
	echo json_encode($msg);
	exit(0);
}
$request = $_POST;
$response = "unsupported request type, politely FUCK OFF";
switch ($request["type"])
{
	// request.send("type=login&uname="+username+"&pword="+password)
	case "login":
		$loginRequest = array();
		$loginRequest['type'] = "login";
		$loginRequest['username'] = $request["uname"];
		$loginRequest['password'] = $request["pword"];
		$loginRequest['message'] = "login request";
		$response = $client->send_request($loginRequest);
		if ($response == 1)
		{
			$_SESSION['username'] = $request["uname"];
		}
		break;
	case "register":
		$regRequest = array();
		$regRequest['type'] = "register";
		$regRequest['username'] = $request["uname"];
		$regRequest['password'] = $request["pword"];
		$regRequest['message'] = "registration request";
		$response = $client->send_request($regRequest);
		break;
}

echo json_encode($response);

exit(0);
?>
