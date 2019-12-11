<?php

namespace Plugin;

use DB\cTables;
use Langs\Phrases;
use Pages;

class wpShopPluginUninstall
{
  public function uninstall()
  {
    $del1 = Pages::deletePages();

    $tables = new cTables();
    $del2 = $tables->deleteTables();

    if ($del1 && $del2) {
      echo Phrases::getPhreses('HTTP')['DELETED'] . "Plugin";
    }

    echo '<br/><a href="plugins.php" class="delete" aria-label="Pluginy">Pluginy</a>';
  }
}
