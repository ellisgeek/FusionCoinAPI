<?php 
class User {


	private $inputSteamID;
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
		 * define("DB_HOST", "");
		 * define("DB_USER", "");
		 * define("DB_PASS", "");
		 * define("DB_DB"  , "");
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
		$sql = "SELECT * FROM fc_users WHERE steamID='"
			. $this->inputSteamID . "';";
		$query = $this->db->query($sql);
		$this->userInfo = $query->fetch_assoc();
		
		$this->steamID   = $this->userInfo["steamID"];
		$this->balance   = $this->userInfo["balance"];
		$this->inventory = $this->userInfo["inventory"];
		$this->locked    = $this->userInfo["locked"];
	}
}
