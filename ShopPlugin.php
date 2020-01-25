<?php

use admin\AdminKokpitMenu;
use DB\cTables;
use html\fillHTML;
use plugin\PluginInfo;
use plugin\PluginUpdater;
use UI\UserUI;

class ShopPlugin
{
  public function __construct()
  {
    self::run();
  }

  public static function run()
  {
    self::autoload();
    self::initHooks();

    $menuBar = new UserUI();
    $menuBar->AddToMenuBar();

    $tableFactory = new cTables();
    $tableFactory->createTables();
  }

  private function initHooks()
  {
    add_action('init', array(__class__, 'loggedUserID'));

    add_action("admin_menu", array(__class__, 'initAdminKokpit2'));

    add_action("wp_head", array(__class__, 'initHead'));

    add_action('init', array('Shortcodes', 'init'));

    add_filter('plugin_action_links', array(__class__, 'Action_links'), 10, 5);

    add_action('init', array('Pages', 'createPages'));

    add_action('rest_api_init', array('RestAPI', 'init'));


    // add_filter('plugins_api', 'wp_shopM_info', 20, 3);
    // add_filter('site_transient_update_plugins', 'wp_shopM_update');
    // add_action('admin_init', array($this, 'registerPluginActionLinks'));
  }

  public function Action_links($actions, $plugin_file)
  {
    if (plugin_basename(ROOT) == dirname($plugin_file)) {

      $gitUpdater = new PluginUpdater();
      $gitUpdater->GitConnectAlternative();
      $gitUpdater->setLocalData(PluginInfo::getFull());
      $isUpdate = $gitUpdater->CheckForUpdates();

      $settings = array(
        'settings' => '<a href="admin.php?page=plupd">Update</a>',
      );
      $site_link = array(
        'settings2' => '<a href="admin.php?page=wts">Help </a>',
        'settings3' => '<a href="admin.php?page=unst">Uninstall </a>'
      );

      if ($isUpdate) $actions = array_merge($settings, $actions);
      $actions = array_merge($site_link, $actions);
    }

    return $actions;
  }

  public static function loggedUserID()
  {
    $your_current_user_id = get_current_user_id();
    $_SESSION['your_current_user_id'] =  $your_current_user_id;
  }

  private function autoload()
  {
    include_once "autoload.php";
  }

  public static function initAdminKokpit2()
  {
    new AdminKokpitMenu();
  }

  public static function initHead()
  {
    $head = new fillHTML();
    $head->theme_header_metadata();
  }
}
