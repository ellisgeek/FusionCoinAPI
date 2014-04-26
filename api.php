<?php
error_reporting( E_ALL | E_STRICT );
$db= new mysqli("84.39.119.213", "rootwerk_mfFC", "NQs.hR#6!HCD", "rootwerk_mfFusionCoins");
	if ($db->connect_error) {
		die("Couldn't Connect to MySQL Database.\nError (" . $db->connect_errno . "): " . $db->connect_error);
	}
   //==========//
  // Request  //
 // Variables//
//==========//
if (isset($_GET['action'])){
	print("<h2 style='color:red;'>WARNING: Using GET requests to interact with this system is insecure and is available only during the development of this API!</h2>");
	print("Passed GET request variables:<br>");
	print("SteamID: " . $_GET["steamID"] . "<br>");
	print("Action: " . $_GET["action"] . "<br>");
	if (isset($_GET['action']) && empty($_POST['action'])) {
		$action = $_GET['action'];
	}
	if (isset($_GET['steamID']) && empty($_POST['steamID'])) {
		$steamID = $_GET['steamID'];
	}
}
elseif (isset($_POST['action'])) {
	if (isset($_POST['action']) && empty($_POST['action'])) {
		$action = $_POST['action'];
	}
	if (isset($_POST['steamID']) && empty($_POST['steamID'])) {
		$steamID = $_POST['steamID'];
	}
}
  //===========//
 // Functions //
//===========//
function userExists($steamID) {
	//Query Database to check if there is a steamID that matches the input
	global $db;
	$sql = "SELECT SteamID FROM users WHERE SteamID='" . $steamID . "';";
	$query = $db->query($sql);
	$result = $query->fetch_assoc();
	if ($result["SteamID"] === $steamID) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function addUser($steamID) {
	global $db;
	$sql = "INSERT INTO users (SteamID) VALUES('" . $steamID . "');";
	$query = $db->query($sql);
	if ($query === FALSE) {
		printf("Error: Unable to update table. (" . $db->error . ")");
	}
	
	$sql2 = "SELECT UID FROM users WHERE SteamID='" . $steamID . "';";
	$query2 = $db->query($sql2);
	$result2 = $query2->fetch_assoc();
	
	$sql3 ="INSERT INTO vault (UID) VALUES(" . $result2["UID"] . ");"
	$query3 = $db->query($sql3);
	if ($query3 === FALSE) {
		printf("Error: Unable to update table. (" . $db->error . ")");
	}
}

function getUID($steamID) {
	//Query Database to get user ID
	global $db;
	if (userExists($steamID)) {
		$sql = "SELECT UID FROM users WHERE SteamID='" . $steamID . "';";
		$query = $db->query($sql);
		$result = $query->fetch_assoc();
		return $result["UID"];
	}
	else {
		die("Unable to retrieve UID. No matching user.");
	}
}
function getBalance($UID) {
	global $db;
	if (!empty($UID)) {
		$sql = "SELECT balance FROM vault WHERE UID='" . $UID . "';";
		$query = $db->query($sql);
		$result = $query->fetch_assoc();
		return $result["balance"];
	}
	else {
		die("Unable to retrieve balance. UID is empty");
	}
}

function setCoin($UID, $ammount) {
	global $db;
	$sql = "UPDATE vault SET balance=" . $ammount . " WHERE UID=" . $UID . ";";
	$query = $db->query($sql);
	if ($query === FALSE) {
		printf("Error: Unable to update table. (" . $db->error . ")");
	}
}

function addCoin($UID, $ammount) {
	global $db;
	if (!empty($UID)) {
		$currentBalance = getBalance($UID);
	}
	else {
		die("Unable to retrieve balance. UID is empty");
	}
	$newBalance = $currentBalance + $ammount;
	setCoin($UID, $newBalance);
}

function subtractCoin($UID, $ammount) {
	global $db;
	if (!empty($UID)) {
		$currentBalance = getBalance($UID);
	}
	else {
		die("Unable to retrieve balance. UID is empty");
	}
	$newBalance = $currentBalance - $ammount;
	setCoin($UID, $newBalance);
}

function isLocked($UID) {
	global $db;
	if (!empty($UID)) {
		$sql = "SELECT locked FROM users WHERE UID='" . $UID . "';";
		$query = $db->query($sql);
		$result = $query->fetch_assoc();
		return $result["locked"];
	}
	else {
		die("Unable to retrieve account status. UID is empty");
	}
}

function lockUser($UID) {
	global $db;
	$sql = "UPDATE users SET locked=1;";
	$query = $db->query($sql);
	if ($query === FALSE) {
		printf("Error: Unable to update table. (" . $db->error . ")");
	}
}

function unlockUser($UID) {
	global $db;
	$sql = "UPDATE users SET locked=0;";
	$query = $db->query($sql);
	if ($query === FALSE) {
		printf("Error: Unable to update table. (" . $db->error . ")");
	}
}

   //=======//
  // Tests //
 //=======//
if($action === "test"){
	$sid = $steamID;
	print("Steam ID: " . $sid);
	print("<br>");
	print("<br>");
	print("<br>");
	print("<br>");
	print "User Exists: " . userExists($sid);
	if (!userExists($sid)) {
		print("User does not exist! <br> Will Create!");
		addUser($sid);
		userExists($sid);
	}
	print("<br>");
	print("<br>");
	print "User ID: " . getUID($sid);
	print("<br>");
	print("<br>");
	print "Balance: " . getBalance(getUID($sid));
	print("<br>");
	print("<br>");
	print "Add 500FC";
	print("<br>");
	addCoin(getUID($sid), 500);
	print "Balance: " . getBalance(getUID($sid));
	print("<br>");
	print("<br>");
	print "Subtract 6500FC";
	print("<br>");
	subtractCoin(getUID($sid), 6500);
	print "Balance: " . getBalance(getUID($sid));
	print("<br>");
	print("<br>");
	print "Set to 1000FC";
	print("<br>");
	setCoin(getUID($sid), 1000);
	print "Balance: " . getBalance(getUID($sid));
	print("<br>");
	print("<br>");
	print "Account Status: " . isLocked(getUID($sid));
	print("<br>");
	print("<br>");
	print "Lock Acct.";
	print("<br>");
	lockUser(getUID($sid));
	print "Account Status: " . isLocked(getUID($sid));
	print("<br>");
	print("<br>");
	print "Unlock Acct.";
	print("<br>");
	unlockUser(getUID($sid));
	print "Account Status: " . isLocked(getUID($sid));
}
$db->close();
?>
