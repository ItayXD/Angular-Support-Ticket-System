<?php
//set db parameters
$host="localhost";
$db="test";
$usr="user";
$pswd="pass";
//connect to db
try {
	$pdo = new PDO("mysql:host=$host;dbname=$db", $usr, $pswd, [PDO::ATTR_PERSISTENT => true]);
} catch (PDOException $e) { //catch error and do:
	http_response_code(500);
	echo "<h1>Error:</h1> <h3>could not connect to database!</h3>";
	exit();
}

//handle get alltickets req
if ($_GET['alltickets']) {
	$stAllTickets = $pdo->prepare ('SELECT * FROM tickets');
	if ($stAllTickets->execute()) {
		$res = [];
		while($r = $stAllTickets->fetch(PDO::FETCH_ASSOC)) {
		    $res[] = $r;
		}
		echo json_encode($res);
	}
//handle get ticket by id req
} elseif ($_GET['ticketid']) {
	//prepare the sql query, gets user data based on join from user table on user-id
	$stGetMessages   = $pdo->prepare ('SELECT * FROM `messages` INNER JOIN users ON messages.uid=users.uid WHERE ticketid = :ticketid');
	$stGetTicket = $pdo->prepare ('SELECT * FROM `tickets` INNER JOIN users ON tickets.uid=users.uid WHERE id = :ticketid');
	$stGetTicket   -> bindParam(':ticketid', $ticketid);
	$stGetMessages -> bindParam(':ticketid', $ticketid);
	$ticketid = $_GET['ticketid'];
	if ($stGetTicket->execute() && $stGetMessages->execute()) {
		$res = [];
		$res['ticket'] = $stGetTicket->fetch(PDO::FETCH_ASSOC);
		$res['messages'] = [];
		while($r = $stGetMessages->fetch(PDO::FETCH_ASSOC)) {
		   $res['messages'][] = $r;
		}
		echo json_encode($res);
	}

} elseif ($_POST['new'] === 'msg' && $_POST['ticketid'] && $_POST['user'] && $_POST['content']) {
	//prepare sql query to insert new message, get the user-id based on the username from users table
	$stNewMessage = $pdo->prepare('INSERT INTO messages (ticketid, content, uid) SELECT :ticketid, :content, uid FROM users WHERE users.username = :user');
	$stNewMessage->bindParam(':ticketid', $ticketid);
	$stNewMessage->bindParam(':user', $user);
	$stNewMessage->bindParam(':content', $content);
	$ticketid = $_POST['ticketid'];
	$user     = $_POST['user'];
	$content  = $_POST['content'];
	if ($stNewMessage->execute()) {
		$id = $pdo->lastInsertId();
		$stGetMessage = $pdo->prepare('SELECT * FROM `messages` INNER JOIN users ON messages.uid=users.uid WHERE id = ?');
		$stGetMessage->execute([$id]);
		$res = $stGetMessage->fetch(PDO::FETCH_ASSOC);
		http_response_code(201);
		$res = json_encode($res);
	} else {
		http_response_code(500);
		$res = print_r($stNewMessage->errorInfo(), true) . "Unable to save your message to database";
	}
	echo $res;
} elseif ($_POST['new'] === 'tckt' && $_POST['user'] && $_POST['priority'] && $_POST['subject'] && $_POST['content']) {
	$stNewTicket = $pdo->prepare('INSERT INTO tickets (subject, uid, priority, content) VALUES (:subject, :uid, :priority, :content)');
	$stNewTicket->bindParam(':subject', $subject);
	$stNewTicket->bindParam(':uid', $uid);
	$stNewTicket->bindParam(':priority', $priority);
	$stNewTicket->bindParam(':content', $content);
	$subject  = $_POST['subject'];
	$uid   = 1;
	$priority = $_POST['priority'];
	$content  = $_POST['content'];
	if ($stNewTicket->execute()) {
		$id = $pdo->lastInsertId();
		$stGetTicket = $pdo->prepare('SELECT * FROM `tickets` INNER JOIN users ON tickets.uid=users.uid WHERE id = ?');
		$stGetTicket->execute([$id]);
		$res = $stGetTicket->fetch(PDO::FETCH_ASSOC);
		http_response_code(201);
		$res = json_encode($res);
	} else {
		http_response_code(500);
		$res = print_r($stNewTicket->errorInfo(), true) . "Unable to save your message to database";
	}
	echo $res;
}


?>