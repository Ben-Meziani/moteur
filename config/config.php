<?php

if(isset($_SERVER["HTTP_X_REAL_IP"]) && $_SERVER["HTTP_X_REAL_IP"]!="") {
    $_SERVER["REMOTE_ADDR"]=$_SERVER["HTTP_X_REAL_IP"];
}
if(!isset($_SERVER["REMOTE_ADDR"])) $_SERVER["REMOTE_ADDR"]="";

return array(
    "db_user" => "root",
    "db_pass" => "",
    "db_host" => "localhost",
    "db_name" => "moteur",
    "db_charset" => "utf8",
    "db_DebugMode" => 1,
    "db_LocalTime" => "fr_FR",
    "name_projet" => "moteur",
);
?>
