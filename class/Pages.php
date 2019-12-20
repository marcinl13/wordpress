<?php

use DB\DBConnection;
use DB\IDBDataFactor;
use IShortCodes\IShortCodes;

class Pages implements IShortCodes
{

  function __construct()
  { 
  }

  public static  function createPages()
  {
    self::createPage(IShortCodes::USER_ORDERS, IShortCodes::USER_ORDERS, IShortCodes::USER_ORDERS);
    self::createPage(IShortCodes::DUMP, IShortCodes::DUMP, IShortCodes::DUMP);
    self::createPage(IShortCodes::SHOP, IShortCodes::SHOP, IShortCodes::SHOP);
    self::createPage(IShortCodes::PAYMENT_NOTYFICATION, IShortCodes::PAYMENT_NOTYFICATION, IShortCodes::PAYMENT_NOTYFICATION);

    // self::createPage('sklep_produkty', 'sklepProdukty', 'sklepProdukty');
  }

  public function createPage($shortCode, $postTitle, $postName = 'orders', $url = null)
  {
    $db = new DBConnection();

    $homeUrl = home_url();
    $id = $db->getResults(
        "select MAX(ID) + 1 as max from wp_posts ", 
        IDBDataFactor::ARRAY_A)[0]['max'];

    $url = "{$homeUrl}/?page_id={$id}";
    $date = date("Y-m-d H:i:s");
    $userId = 1;

    if ((bool) $db->query("select * from wp_posts where post_content = '[{$shortCode}]'") == false) {
      $db->query("insert INTO wp_posts (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count) 
        VALUES ({$id}, '$userId' , '{$date}', '{$date}', '[{$shortCode}]', '{$postTitle}', '', 'publish', 'closed', 'closed', '', '{$postName}', '', '', '{$date}', '{$date}', '', '0', '{$url}', '0', 'page', '', '0')");
    }
  }

  public static function deletePages(): bool
  {
    $db = new DBConnection();

    // $query = "FROM wp_posts WHERE post_content LIKE \"[%]\" ";
    $query = "FROM wp_posts WHERE post_content LIKE \"[loginModal]\" ";
    // $rows = self::$db->getRow("SELECT COUNT(*) as count {$query}", ARRAY_A)['count'];
    $result = $db->query("DELETE {$query}");


    return (bool) $result;

    // $result = self::$db->query("DELETE {$query}") ? "success" : "failure";
    // $today = date("Y-m-d H:i:s");
    // $userId = get_current_user_id();
    // file_put_contents(ROOT . 'log.txt', "
    //   \r\n[{$today} | {$userId}] status{$result}  
    //   \t DELETE rows({$rows}) in wp_post
    //   \t DELETE {$query}\r\n", FILE_APPEND);
  }
}
