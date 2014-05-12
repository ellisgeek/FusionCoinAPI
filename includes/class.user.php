<?php
/**
 *Manage user balance and invintory in FusionCoin
 *
 * @author Elliott Saille <ellisgeek@live.com>
 * @version 0.0.1
 * @copyright 2014
 * @package FusionCoin
 */

/**
 * Define DocBlock
 */
class User {
	/**
	 * This class handles all functions relating to a users account
	 *
	 * @package FusionCoin
	 * @author  Elliott Saille
	 */

	/**
	 * steamID that is passed to the class when it is created
	 *
	 * @var string
	 * @access protected
	 */
	protected $inputSteamID;


	/**
	 * Array of current user info as returned from MySQL
	 *
	 * @var array
	 * @access protected
	 */
	protected $userInfo;

	/**
	 * Array of current user info as returned from MySQL
	 *
	 * @var array
	 * @access private
	 */
	var $steamID;

	/**
	 * Users Balance
	 *
	 * @var integer
	 * @access public
	 */
	var $balance;

	/**
	 * Array of user's invintory
	 *
	 * @var array
	 * @access public
	 */
	var $inventory;

	/**
	 * Users account status. True if account is locked or false if it's not.
	 *
	 * @var boolean
	 * @access public
	 */
	var $locked;



	/**
	 * Database Connection
	 *
	 * @var object
	 * @access protected
	 */
	protected $db;


	function __construct($steamID) {
		/**
	 	 * Initalize class and get current user info
		 *
		 * @return void
		 * @author  Elliott Saille
		 */

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
		/**
		 * Get user info from MYSQL DB
		 *
		 * @return void
		 * @author  Elliott Saille
		 */
		$sql = "SELECT * FROM " . DB_PREFIX . "users WHERE steamID='"
				. $this->inputSteamID . "';";
		$query = $this->db->query($sql);
		$this->userInfo = $query->fetch_assoc();

		$this->steamID   = $this->userInfo["steamID"];
		$this->balance   = $this->userInfo["balance"];
		$this->inventory = json_decode($this->userInfo["inventory"]);
		$this->locked    = $this->userInfo["locked"];
	}


	private function setBalance($amount) {
		/**
		 * Set the users balance to a arbitrary ammount
		 *
		 * @return array
		 * @author  Elliott Saille
		 */
		$sql = "UPDATE " . DB_PREFIX . "users SET balance=" . $amount .
				" WHERE steamID=" . $this->steamID . ";";
		$query = $this->db->query($sql);
		if ($query === FALSE) {
			die("Error: Unable to update balance for user " . $this->steamID
					. ". (" . $this->db->error . ")");
		}
		$oldBalance=$this->balance;
		$this->getInfo();
		$newBalance=$this->balance;
		return array("oldBalance"=>$oldBalance, "newBalance"=>$newBalance);
	}

	function addCoin($amount) {
		/**
		 * Add money to a users account
		 *
		 * @return void
		 * @author  Elliott Saille
		 */
		$newBalance = $this->balance + $amount;
		$this->setBalance($newBalance);
	}

	function subtractCoin($amount) {
		/**
		 * Subtract money from a users account
		 *
		 * @return void
		 * @author  Elliott Saille
		 */
		$newBalance = $this->balance + $amount;
		$this->setBalance($newBalance);
	}

	function giveCoin($user, $amount) {
		/**
		 * Transfer money from one user to another
		 *
		 * @return void
		 * @author  Elliott Saille
		 */

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
		/**
		 * Lock a Users Account
		 *
		 * @return array
		 * @author  Elliott Saille
		 */
		$sql = "UPDATE " . DB_PREFIX . "users SET locked=1 WHERE steamID="
				. $this->steamID . ";";
		$query = $this->db->query($sql);
		if ($query === FALSE) {
			die("Error: Unable to lock user " . $this-steamID . ". (" . $this->db->error . ")");
		}
		$oldStatus=$this->locked;
		$this->getInfo();
		$newStatus=$this->locked;
		return array("oldStatus"=>$oldStatus, "newStatus"=>$newStatus);
	}

	function unlock() {
		/**
		 * UnLock a Users Account
		 *
		 * @return array
		 * @author  Elliott Saille
		 */
		$sql = "UPDATE " . DB_PREFIX . "users SET locked=0 WHERE steamID="
				. $this->steamID . ";";
		$query = $this->db->query($sql);
		if ($query === FALSE) {
			die("Error: Unable to unlock user " . $this-steamID . ". (" . $this->db->error . ")");
		}
		$oldStatus=$this->locked;
		$this->getInfo();
		$newStatus=$this->locked;
		return array("oldStatus"=>$oldStatus, "newStatus"=>$newStatus);
	}
}
?>