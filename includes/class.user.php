<?php
/**
 *
 *
 * @author Elliott Saille
 * @version 0.0.1
 * @copyright 2014
 * @package FusionCoin
 */

/**
 * Define DocBlock
 */

/**
 * User
 * This class handles all functions relating to a users account
 *
 * @package FusionCoin
 * @author  Elliott Saille
 */
class User {
	/**
	 * steamID that is passed to the class when it is created
	 *
	 * @var inputSteamID
	 */
	private $inputSteamID;

	/**
	 * steamID that is passed to the class when it is created
	 *
	 * @var inputSteamID
	 */
	private $userInfo;
	var $steamID;
	var $balance;
	var $inventory;
	var $locked;

	protected $db;


	function __construct($steamID) {
		// Set input SteamID when class is created
		$this->inputSteamID = $steamID;

		//Connect to database
		//There has to be a better more OOP method of
		//handling the SQL connection besides a set of Constants
		/*For Future Refrence the rewuired defines are:
		 *
		 * define("DB_HOST"  , "");
		 * define("DB_USER"  , "");
		 * define("DB_PASS"  , "");
		 * define("DB_DB"    , "");
		 * define("DB_PREFIX", "");
		 */
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DB);
		if ($this->db->connect_error) {
			die("Couldn't Connect to MySQL Database.\n
					Error (" . $this->db->connect_errno . "): "
					. $this->db->connect_error);
		}
		$this->getInfo();
	}


	private function getInfo() {
		$sql = "SELECT * FROM " . DB_PREFIX . "users WHERE steamID='"
				. $this->inputSteamID . "';";
		$query = $this->db->query($sql);
		$this->userInfo = $query->fetch_assoc();

		$this->steamID   = $this->userInfo["steamID"];
		$this->balance   = $this->userInfo["balance"];
		$this->inventory = $this->userInfo["inventory"];
		$this->locked    = $this->userInfo["locked"];
	}


	private function setBalance($amount) {
		$sql = "UPDATE " . DB_PREFIX . "users SET balance=" . $amount .
				" WHERE steamID=" . $this->steamID . ";";
		$query = $this->db->query($sql);
		if ($query === FALSE) {
			die("Error: Unable to update balance for user " . $this->steamID
					. ". (" . $this->db->error . ")");
		}
	}

	function addCoin($amount) {
		$newBalance = $this->balance + $amount;
		$this->setBalance($newBalance);
	}

	function subtractCoin($amount) {
		$newBalance = $this->balance + $amount;
		$this->setBalance($newBalance);
	}

	function giveCoin($user, $amount) {
		//Get balance of recieving user
		$sql = "SELECT balance FROM " . DB_PREFIX . "users WHERE steamID='"
				. $this->steamID . "';";
		$query = $this->db->query($sql);
		$tempArray = $query->fetch_assoc();
		$toUserBalance = $tempArray["balance"];
		//Figure out the reciving users new balance
		$toUserNewBalance = $toUserBalance + $amount;
		//Set the balance of the recieving user
		$sql2 = "UPDATE " . DB_PREFIX . "users SET balance="
				. $toUserNewBalance . " WHERE steamID=" . $user . ";";
		$query2 = $this->db->query($sql2);
		if ($query2 === FALSE) {
			die("Error: Unable to update balance for user " . $user
					. ". (" . $this->db->error . ")");
		}
		//Subtract the amount from the giving users balance
		$this->subtractCoin($amount);
	}
	function lock() {
		$sql = "UPDATE " . DB_PREFIX . "users SET locked=1 WHERE steamID="
				. $this->steamID . ";";
		$query = $this->db->query($sql);
		if ($query === FALSE) {
			die("Error: Unable to lock user " . $this-steamID . ". (" . $this->db->error . ")");
		}
	}

	function unlock($UID) {
		$sql = "UPDATE " . DB_PREFIX . "users SET locked=0 WHERE steamID="
				. $this->steamID . ";";
		$query = $this->db->query($sql);
		if ($query === FALSE) {
			die("Error: Unable to unlock user " . $this-steamID . ". (" . $this->db->error . ")");
		}
	}
}
?>