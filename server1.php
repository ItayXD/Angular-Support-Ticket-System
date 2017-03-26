<?php
$srvr="localhost";
$db="test";
$usr="user";
$pswd="pass";
$cn = mysqli_connect ($srvr, $usr, $pswd, $db)
	or die("connection failed");

if ($_GET['alltickets']) {
	// mysqli_query($cn, "SELECT * FROM tickets")
	// 	or die('failed to get table');
	if ($res = mysqli_query($cn,'SELECT * FROM tickets')) {
		$rows = [];
		while($r = mysqli_fetch_assoc($res)) {
		    $rows[] = $r;
		}
		echo json_encode($rows);
	}
} elseif ($_GET['ticketid']) {
	$ticketid = mysqli_real_escape_string($cn, $_GET['ticketid']);
	$messages = mysqli_query($cn, "SELECT * FROM `messages` WHERE ticketid = $ticketid");
	$ticket   = mysqli_query($cn, "SELECT * FROM `tickets` WHERE id = $ticketid");
	$op = [];
	$op['ticket'] = mysqli_fetch_assoc($ticket);
	//$op[ticket] = 'hi';
	$op['messages'] = [];
	while($r = mysqli_fetch_assoc($messages)) {
	   $op['messages'][] = $r;
	}
	echo json_encode($op);
}
if ($_POST['new'] === 'msg' && $_POST['ticketid'] && $_POST['user'] && $_POST['content']) {
	$ticketid = mysqli_real_escape_string($cn, $_POST['ticketid']);
	$user     = mysqli_real_escape_string($cn, $_POST['user']);
	$content  = mysqli_real_escape_string($cn, $_POST['content']);
	$insert = "INSERT INTO messages (ticketid, user, content) VALUES ('$ticketid', '$user', '$content')";
	if (mysqli_query($cn, $insert)) {
		$id = mysqli_insert_id($cn);
		$created = mysqli_query($cn, "SELECT * FROM `messages` WHERE id = $id");
		http_response_code(201);
		$res = mysqli_fetch_assoc($created);
		$res = json_encode($res);
	} else {
		http_response_code(500);
		$res = mysqli_error($db) . "Unable to save your message to database";
	}
	echo $res;
} 


?>