<?php
require_once realpath(dirname(__FILE__).'/includes/class.user.php');
define("DB_HOST", "84.39.119.213");
define("DB_USER", "rootwerk_mfFC");
define("DB_PASS", "NQs.hR#6!HCD");
define("DB_DB"  , "rootwerk_mfFusionCoins");
define("DB_PREFIX"  , "fc_");

$user = new User("STEAM_0:0:31249793");
print $user->steamID . "\n";
print $user->balance . "\n";
print $user->locked . "\n";
print "\nInventory:\n";
var_dump(json_decode($user->inventory));
?>