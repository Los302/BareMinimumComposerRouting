<?php
require_once ('includes/initialize.php');
// Check for authorization
if ($SESSION->IsLoggedIn ()) { $SESSION->LogOut (SITE_URL); }
?>
