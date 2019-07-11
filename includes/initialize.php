<?php
require_once ('env.php');

// Set the timezone
date_default_timezone_set(TIMEZONE);

// Directories
define ('ADMIN_URL', SITE_URL.'Admin/');
define ('USERS_URL', SITE_URL.'User/');
define ('IMAGE_URL', SITE_URL.'public/images/');
define ('CSS_URL', SITE_URL.'public/_CSS/');
define ('JS_URL', SITE_URL.'public/_JS/');

// Include some functions and the auto loader
require_once(SITE_ROOT . 'App/Helpers/FunkChins.php');
require_once (SITE_ROOT.'vendor/autoload.php');

// Connect to the db
foreach ($Los_DB as $k => $v) { $GLOBALS['DB'][$k] = new PDO('mysql:host='.$v['HN'].';dbname='.$v['DB'].';charset=utf8', $v['UN'], $v['PW']); }

// Start the session
$SESSION = App\Helpers\Session::GetTheSession();

// Route the app
require_once ('router.php');
?>
