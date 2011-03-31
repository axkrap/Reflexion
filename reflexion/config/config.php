<?php
//Domain
define('THIS_DOMAIN', '');
//Set URL array and uri to be decomposed
$url = parse_url('http://'.THIS_DOMAIN.$_SERVER['REQUEST_URI']);
//uri could= $_GET['uri'] check advanced documentation to see why/how
$uri = substr($url['path'], 1);
//Log errors, etc
define('DEVELOPMENT_ENVIRONMENT',true);
/** URL Settings **/
//This string indicates what the admin url starts with (default is "admin")
define('ADMIN_URL', 'admin');
//This string indicates how classes are called from the client "rf_someclassname", must be alnum only
define('ACTION_VAR','rf');
//Internal Action variable, this one should be changed, but it can remain the same as the other, must be alnum only
define('INTERNAL_ACTION', 'rfinternal');
//The Pingback URL.
define('PINGBACK', '_pingback_');
//The Feed URL.
define('RSS_URI','rss_feed');
//This string indicates what type of url structure the site is using
//either 'name', 'day-name', 'month-name','category-name'
define('URL_STATE', 'category-name');
//Reflexion Version #
define('REFLEX_VERSION', '0.5');
//Timezone
define('TIME_ZONE','America/Los_Angeles');