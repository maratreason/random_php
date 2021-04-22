<?php
/**
 * api.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */
/*
ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);
*/
require_once('./classes/MOXMAN.php');

define("MOXMAN_API_FILE", __FILE__);

$context = MOXMAN_Http_Context::getCurrent();
$pluginManager = MOXMAN::getPluginManager();
foreach ($pluginManager->getAll() as $plugin) {
	if ($plugin instanceof MOXMAN_Http_IHandler) {
		$plugin->processRequest($context);
	}
}

?>