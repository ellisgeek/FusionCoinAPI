<?php
/*Initialize Database Connection*/
$db= new mysqli("84.39.119.213", "rootwerk_mfFC", "NQs.hR#6!HCD", "rootwerk_mfFusionCoins");
	if ($db->connect_error) {
		die("Couldn't Connect to MySQL Database.\nError (" . $db->connect_errno . "): " . $db->connect_error);
	}

/*Define internal functions*/
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

function addUser($steamID) {
	global $db;
	$sql = "INSERT INTO users (SteamID) VALUES('" . $steamID . "');";
	$query = $db->query($sql);
	if ($query === FALSE) {
		die("Error: Unable to add user. (" . $db->error . ")");
	}
	
	$sql2 = "SELECT UID FROM users WHERE SteamID='" . $steamID . "';";
	$query2 = $db->query($sql2);
	$result2 = $query2->fetch_assoc();
	
	$sql3 ="INSERT INTO vault (UID) VALUES(" . $result2["UID"] . ");"
	$query3 = $db->query($sql3);
	if ($query3 === FALSE) {
		die("Error: Unable to add user to vault. (" . $db->error . ")");
	}
	return getUID($steamID);
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

function setCoin($UID, $amount) {
	global $db;
	$sql = "UPDATE vault SET balance=" . $amount . " WHERE UID=" . $UID . ";";
	$query = $db->query($sql);
	if ($query === FALSE) {
		die("Error: Unable to update user balance. (" . $db->error . ")");
	}
	
}

function addCoin($UID, $amount) {
	global $db;
	if (!empty($UID)) {
		$currentBalance = getBalance($UID);
	}
	else {
		die("Unable to retrieve balance. UID is empty");
	}
	$newBalance = $currentBalance + $amount;
	setCoin($UID, $newBalance);
}

function subtractCoin($UID, $amount) {
	global $db;
	if (!empty($UID)) {
		$currentBalance = getBalance($UID);
	}
	else {
		die("Unable to retrieve balance. UID is empty");
	}
	$newBalance = $currentBalance - $amount;
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
		die("Error: Unable to update table. (" . $db->error . ")");
	}
}

function unlockUser($UID) {
	global $db;
	$sql = "UPDATE users SET locked=0;";
	$query = $db->query($sql);
	if ($query === FALSE) {
		die("Error: Unable to update table. (" . $db->error . ")");
	}
}
/*create easy to use variables for Request variables*/
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
if (userExists($steamID){
	$uid = getUID($steamID);
}
else {
	die("User does not exist please create first!")
}
elseif($action === "addUser"){}
if(isset($_POST["amount"]) {
	$amount = $_POST["amount"];
}

/*Case to handle HTTP Requests*/
case($action) {
	switch "addUser":
			if(!userExists($steamID)){
				addUser($steamID);
				$uid = getUID($steamID);
				print $uid; //Return something useful in JSON or XML
			}
			else{
				print("User Exists! Will not create!");
			}
	break;
	switch "balance":
		if(userExists($steamID){
			print getBalance($uid); //Return something useful in JSON or XML
		}
	break;
	switch "addCoin":
		if(userExists($steamID){
			$uid = getUID($steamID);
			addCoin($uid, $ammount);
			//Return something useful in JSON or XML
		}
	break;
	switch "subtractCoin":
		if(userExists($steamID){
			$uid = getUID($steamID);
			subtractCoin($uid, $ammount);
			//Return something useful in JSON or XML
		}
	break;
	switch "setCoin":
		if(userExists($steamID){
			$uid = getUID($steamID);
			setCoin($uid, $ammount);
			//Return something useful in JSON or XML
		}
	break;
	switch "accountStatus":
		if(userExists($steamID){
			$uid = getUID($steamID);
			$balance = getBalance($uid);
			$lock = isLocked($uid);
			//Return something useful in JSON or XML
		}
	break;
	switch "isLocked":
		if(userExists($steamID){
			$uid = getUID($steamID);
			$lock = isLocked($uid);
			print $lock;
		}
	break;
	switch "lockUser":
		if(userExists($steamID){
			$uid = getUID($steamID);
			lockUser($uid);
			//Return something useful in JSON or XML
		}
	break;
	switch "unlockUser":
		if(userExists($steamID){
			$uid = getUID($steamID);
			unlockUser($uid);
			//Return something useful in JSON or XML
		}
	break;
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
