<?php
require_once realpath(dirname(__FILE__).'/includes/class.user.php');
define("DB_HOST", "84.39.119.213");
define("DB_USER", "rootwerk_mfFC");
define("DB_PASS", "NQs.hR#6!HCD");
define("DB_DB"  , "rootwerk_mfFusionCoins");

$user = new User("STEAM_0:0:31249793");
print $user->steamID . "\n";
print $user->balance . "\n";
print $user->inventory . "\n";
print $user->locked . "\n";
print "\nInv test:\n";
$inventory = array(1 => array(1 => 50));
$jsinv = json_encode($inventory);
$js2array = json_decode($jsinv, true);
print json_last_error();
var_dump($inventory);
print "\njson: " . $jsinv . "\nBack to array:\n";
var_dump($js2array);
?>