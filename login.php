<?php
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
		$loginRequest['message'] = "gadzooks!";
		$response = $client->send_request($loginRequest);
	break;
}
echo json_encode($response);
exit(0);

?>
