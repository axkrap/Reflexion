<?php
define('DS', '/');
define('ROOT', dirname(__FILE__));
define('MAIN', 'reflexion');
define('CRON_JOB',false);
require(ROOT.DS.MAIN.DS.'config'.DS.'config.php');
require(ROOT.DS.MAIN.DS.'library'.DS.'urlparser.php');
?>