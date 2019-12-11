<?php
include CLASS_PATH . "Plugin/PluginInfo.class.php";
include CLASS_PATH . "Plugin/PluginUpdater.class.php";

use plugin\PluginUpdater;
use plugin\PluginInfo;



function prefix_plugin_update_message($data, $response)
{
	printf(
		'<div class="update-message"><p><strong>%s</strong></p></div>',
		__('Version 2.3.4 is a recommended update', 'text-domain')
	);
}

function wp_shopM_info($res, $action, $args)
{
	if ($action !== 'plugin_information')
		return false;

	// do nothing if it is not our plugin	
	if (PluginInfo::getBaseName() !== $args->slug)
		return $res;

	//  set_transient('misha_upgrade_projekt', $remote, 43200); // 12 hours cache

	$gitUpdater = new PluginUpdater();
	$gitUpdater->setLocalData(PluginInfo::getFull());
	$gitUpdater->GitConnectAlternative();
	$gitUpdater->DownloadFile(DOWNLOAD_PATH);
	$gitUpdater->UnZip(ROOT);

	$data = PluginInfo::getFull();

	$res = new stdClass();
	$res->name = $data['Name'];
	$res->slug = PluginInfo::getBaseName();
	$res->version = $gitUpdater->getGitDataVersion();
	$res->tested = '5.2.4';
	$res->requires = '5.2';
	$res->author = $data['Author'];
	$res->author_profile = $data['PluginURI']; // WordPress.org profile
	$res->download_link = $gitUpdater->getGitZipUrl();
	$res->trunk = $gitUpdater->getGitZipUrl();
	$res->last_updated = "2018-01-02 10:10:10";
	$res->sections = array(
		'changelog' => str_replace('-', '<br/>- ', $gitUpdater->getGitBody())
	);

	return $res;
}


function wp_shopM_update($transient)
{
	// $remote=array();
	// set_transient('misha_upgrade_YOUR_PLUGIN_SLUG', $remote, 43200); // 12 hours cache
	
	$gitUpdater = new PluginUpdater();
	$gitUpdater->setLocalData(PluginInfo::getFull());
	$gitUpdater->GitConnectAlternative();

	$res = new stdClass();
	$res->slug =  PluginInfo::getBaseName();
	$res->plugin = PluginInfo::getBaseName() . '/ShopPlugin.php'; 
	$res->new_version = $gitUpdater->getGitDataVersion();
	$res->tested = $gitUpdater->getGitDataVersion();
	$res->package = $gitUpdater->getGitZipUrl();
	$transient->response[$res->plugin] = $res;

	return $transient;
}


function misha_after_update($upgrader_object, $options)
{
	if ($options['action'] == 'update' && $options['type'] === 'plugin') {
		// just clean the cache when new plugin version is installed
		delete_transient('misha_upgrade_projekt');
	}
}
