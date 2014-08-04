<?php
require_once realpath(dirname(__FILE__).'/includes/class.user.php');
define("DB_HOST", "");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_DB"  , "");
define("DB_PREFIX"  , "fc_");

$user = new User("STEAM_0:0:31249793");
print $user->steamID . "\n";
print $user->balance . "\n";
print $user->locked . "\n";
print "\nInventory:\n";
var_dump($user->inventory);
?>
