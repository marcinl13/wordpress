<?php

namespace DB;

use Plugin\fileRW;

class cTables
{
  private $db;
  private $ctables;
  private $dtables;
  private $dbVersion = 20;

  function __construct()
  {
    $this->db = new DBConnection();
  }

  private function prepareCreateQuery($update = false, int $DBcurVersion = 0)
  {
    $tab = new tCategory($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();

    $tab = new tOrders($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();

    $tab = new tProducts($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();

    $tab = new tTransactions($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();

    $tab = new tVat($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();
    //v18

    $tab = new tDocuments18($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();

    $tab = new tMagazine18($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();

    $tab = new tInvoices($update, $DBcurVersion);
    $this->ctables[] = $tab->createQuery();
  }

  private function prepareDeleteQuery()
  {
    // $tab = new tCategory();
    // $this->dtables[] = $tab->deleteQuery();

    // $tab = new tOrders();
    // $this->dtables[] = $tab->deleteQuery();

    // $tab = new tProducts();
    // $this->dtables[] = $tab->deleteQuery();

    // $tab = new tTransactions();
    // $this->dtables[] = $tab->deleteQuery();

    // $tab = new tVat();
    // $this->dtables[] = $tab->deleteQuery();

    // //v18

    // $tab = new tDocuments18();
    // $this->ctables[] = $tab->deleteQuery();

    // $tab = new tMagazine18();
    // $this->ctables[] = $tab->deleteQuery();

    // $tab = new tInvoices();
    // $this->ctables[] = $tab->deleteQuery();
  }

  private function isUpToDate(int &$curVersion = 0): bool
  {
    $json = fileRW::readJsonAssoc(CONFIG_FILE);

    $curVersion = isset($json['dbv']) ? (int) $json['dbv'] : $this->dbVersion;

    return isset($json['dbv']) && (int) $json['dbv'] < $this->dbVersion;
  }

  public function createTables(): bool
  {
    $check = true;
    $curVersion = 0;
    $needUpdate = self::isUpToDate($curVersion);

    self::prepareCreateQuery($needUpdate, $curVersion);

    if (is_array($this->ctables) && !empty($this->ctables)) {
      foreach ($this->ctables as $count => $query) {

        if (sizeof($query)) {
          $r = (bool) $this->db->query($query);
          $check = $check && $r;
        }
      }

      if ($check === true) {
        $json = fileRW::readJsonAssoc(CONFIG_FILE);
        $json['dbv'] = $this->dbVersion;
        fileRW::writeJson(CONFIG_FILE, $json);
      }
      
    } else {
      $check = false;
    }

    return $check;
  }

  public function deleteTables(): bool
  {
    $check = true;

    self::prepareDeleteQuery();

    if (is_array($this->dtables) && !empty($this->dtables)) {
      foreach ($this->dtables as $count => $query) {
        $r = (bool) $this->db->query($query);
        $check = $check && $r;
      }
    } else {
      $check = false;
    }

    return $check;
  }
}
