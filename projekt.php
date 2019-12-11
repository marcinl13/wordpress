<?php

/*
Plugin Name: ShopPlugin
Plugin URI: https://github.com/marcinl13/wordpress
Description: ShopPlugin
Author: Marcin Portykus
Author URI: 
Version: 1.0.18
*/



if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Define PROJEKT_PLUGIN_FILE.
if (!defined('PROJEKT_PLUGIN_FILE')) {
	define("DS", DIRECTORY_SEPARATOR);
	define("PROJEKT_PLUGIN_FILE", __DIR__ . DS);
	define("ROOT", PROJEKT_PLUGIN_FILE . DS);

	define("CONFIG_FILE", ROOT . "config.json"); //config file
	// define("CONFIG_FILE", ROOT . "config.conf"); //config file

	define("INCLUDES_PATH", ROOT . "includes" . DS);
	define("DOWNLOAD_PATH", ROOT . "downloads" . DS);
	define("CLASS_PATH", ROOT . "class" . DS);
	define("WEBAPP_PATH", ROOT . "webapp" . DS); 	//new
	define("INTERFACE_PATH", ROOT . "interfaces" . DS); //new
	define("ASSETS_PATH", ROOT . "assets" . DS); //new
	define("LANGS_PATH", ROOT . "langs" . DS); //new
	define("MODELS_PATH", ROOT . "models" . DS); //new
	define("REST_API", ROOT . "api" . DS); //new
	define("TABLE_DB", ROOT . "db" . DS); //new
	define("PAYMENTS", ROOT . "payments" . DS); //new

	//v18
	define("ACCOUNTANCY", ROOT . "accountancy" . DS); //new
}

define("PROJECT_PREFIX", "sklep_");
define('WP_DEBUG', true);

global $projectPrefix;
$projectPrefix = $wpdb->prefix . PROJECT_PREFIX;

include_once "ShopPlugin.php";
new ShopPlugin();
