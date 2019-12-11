<?php
namespace plugin;

use Roles\IRoles;
use IShortCodes\IShortCodes;

include_once INTERFACE_PATH . "IRoles.php";



class PluginMenuBar implements IRoles, IShortCodes
{
  private static $homeUrl = null;
  private static $menuTop = null;
  private static $menuSocial = null;

  function __construct()
  {
    self::rewriteHomeUrl();
    self::rewriteTermId();
  }

  private function rewriteTermId()
  {
    $menu_locations = get_nav_menu_locations();

    if (self::$menuTop  == null || self::$menuSocial  == null) {

      self::$menuTop = $menu_locations['top'];
      self::$menuSocial = $menu_locations['social'];
    }
  }

  private function rewriteHomeUrl()
  {
    if (self::$homeUrl  == null) {
      self::$homeUrl = home_url();
    }
  }

  public function AddToMenuBar()
  {
    self::rewriteHomeUrl();
    self::rewriteTermId();

    add_filter('wp_nav_menu_items', array(__class__, 'sklep'), 10, 2);
    add_filter('wp_nav_menu_items', array(__class__, 'koszyk'), 10, 2);
    add_filter('wp_nav_menu_items', array(__class__, 'lunchbox_add_loginout_link'), 10, 2);
    add_filter('wp_nav_menu_items', array(__class__, 'orders'), 10, 2);
  }

  public static function lunchbox_add_loginout_link($items, $args)
  {
    $isNavMenu = get_object_vars(get_object_vars($args)['menu'])['term_id'] == self::$menuTop ? true : false;

    if (is_user_logged_in() && $isNavMenu) {
      $items .= '<li><a href="' . wp_logout_url() . '"><i class="fa fa-send"></i>Log Out</a></li>';
    } elseif (!is_user_logged_in() && $isNavMenu) {
      $items .= '<li><a href="' . site_url('wp-login.php') . '"><i class="fa fa-log-in"></i>Log In</a></li>';
    }

    return $items;
  }

  public static function sklep($items, $args)
  {
    $isNavMenu = get_object_vars(get_object_vars($args)['menu'])['term_id'] == self::$menuTop ? true : false;

    $url = self::$homeUrl;
    $shop = IShortCodes::SHOP;

    if ($isNavMenu)
      $items .= "<li><a href='{$url}/{$shop}'>Sklep</a></li>";

    return $items;
  }

  public static function orders($items, $args)
  {
    try {
      if (!isID(get_current_user_id())) return $items;
      $user = get_object_vars(get_user_by('id', get_current_user_id()))['roles'][0] != IRoles::Administrator ? true : false;
      $isNavMenu = get_object_vars(get_object_vars($args)['menu'])['term_id'] == self::$menuTop ? true : false;

      // trace($user);
      $url = self::$homeUrl;
      $orders = IShortCodes::USER_ORDERS;

      if ($isNavMenu && $user && is_user_logged_in())
        $items .= "<li><a href='{$url}/{$orders}'>orders</a></li>";

      return $items;
    } catch (\Exception $e) {
      //throw $th;
    }
  }

  public static function koszyk($items, $args)
  {
    $isNavMenu = get_object_vars(get_object_vars($args)['menu'])['term_id'] == self::$menuTop ? true : false;
    $url = self::$homeUrl;
    $dump = IShortCodes::DUMP;

    if ($isNavMenu)
      $items .= "<li class=\"order\" onclick=\"window.location ='{$url}/{$dump}';\"><img src=\"data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIC02OCA0ODAgNDgwIiB3aWR0aD0iNTEycHgiPjxwYXRoIGQ9Im0yMDggMTQ0YzAtNC40MTc5NjktMy41ODIwMzEtOC04LThoLTk2Yy00LjQxNzk2OSAwLTggMy41ODIwMzEtOCA4djMyYzAgNC40MTc5NjkgMy41ODIwMzEgOCA4IDhoOTZjNC40MTc5NjkgMCA4LTMuNTgyMDMxIDgtOHptLTE2IDI0aC04MHYtMTZoODB6bTAgMCIgZmlsbD0iIzAwNkRGMCIvPjxwYXRoIGQ9Im00NzIgMjMyaC05Mi42ODc1bC0yOS42NTYyNS0yOS42NTYyNWMtMS41LTEuNS0zLjUzNTE1Ni0yLjM0Mzc1LTUuNjU2MjUtMi4zNDM3NWgtNDB2LTE5MmMwLTQuNDE3OTY5LTMuNTgyMDMxLTgtOC04aC0yODhjLTQuNDE3OTY5IDAtOCAzLjU4MjAzMS04IDh2Mjg4YzAgNC40MTc5NjkgMy41ODIwMzEgOCA4IDhoMTM2djMyYzAgNC40MTc5NjkgMy41ODIwMzEgOCA4IDhoMTc2YzIuMTIxMDk0IDAgNC4xNTYyNS0uODQzNzUgNS42NTYyNS0yLjM0Mzc1bDI5LjY1NjI1LTI5LjY1NjI1aDEwOC42ODc1YzQuNDE3OTY5IDAgOC0zLjU4MjAzMSA4LTh2LTY0YzAtNC40MTc5NjktMy41ODIwMzEtOC04LTh6bS0zMTYuOTM3NS0xOTEuMzkwNjI1Yy0yLjk4ODI4MS0xLjIzODI4MS02LjQyOTY4OC0uNTU0Njg3LTguNzE4NzUgMS43MzQzNzVsLTM0LjM0Mzc1IDM0LjM0Mzc1di02MC42ODc1aDgwdjcyaC0zMnYtNDBjMC0zLjIzNDM3NS0xLjk0OTIxOS02LjE1MjM0NC00LjkzNzUtNy4zOTA2MjV6bS0xMzkuMDYyNSAyNDcuMzkwNjI1di0yNzJoODB2ODBjMCAzLjIzNDM3NSAxLjk0OTIxOSA2LjE1MjM0NCA0LjkzNzUgNy4zOTA2MjUuOTY4NzUuNDA2MjUgMi4wMTE3MTkuNjEzMjgxIDMuMDYyNS42MDkzNzUgMi4xMjEwOTQgMCA0LjE1NjI1LS44NDM3NSA1LjY1NjI1LTIuMzQzNzVsMzQuMzQzNzUtMzQuMzQzNzV2MjguNjg3NWMwIDQuNDE3OTY5IDMuNTgyMDMxIDggOCA4aDQ4YzQuNDE3OTY5IDAgOC0zLjU4MjAzMSA4LTh2LTgwaDgwdjE4NGgtNzJjLTQuNDE3OTY5IDAtOCAzLjU4MjAzMS04IDh2NDBjMCA0LjQxNzk2OSAzLjU4MjAzMSA4IDggOGg2OC42ODc1bC0zMiAzMnptNDQ4IDhoLTEwNGMtMi4xMjEwOTQgMC00LjE1NjI1Ljg0Mzc1LTUuNjU2MjUgMi4zNDM3NWwtMjkuNjU2MjUgMjkuNjU2MjVoLTE2NC42ODc1di0yNGg5NmMyLjEyMTA5NCAwIDQuMTU2MjUtLjg0Mzc1IDUuNjU2MjUtMi4zNDM3NWw0OC00OGMyLjI4NTE1Ni0yLjI4OTA2MiAyLjk3MjY1Ni01LjczMDQ2OSAxLjczNDM3NS04LjcxODc1cy00LjE1NjI1LTQuOTM3NS03LjM5MDYyNS00LjkzNzVoLTgwdi0yNGgxMTYuNjg3NWwyOS42NTYyNSAyOS42NTYyNWMxLjUgMS41IDMuNTM1MTU2IDIuMzQzNzUgNS42NTYyNSAyLjM0Mzc1aDg4em0wIDAiIGZpbGw9IiMwMDZERjAiLz48L3N2Zz4K\" />
          <span id=\"ordersCount\">0</span></li>";

    return $items;
  }
}
