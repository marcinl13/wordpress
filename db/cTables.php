<?php

namespace DB;

include_once TABLE_DB . "cTabelki.php";


class cTables
{
  private $db;
  private $ctables;
  private $dtables;

  function __construct()
  {
    global $wpdb;

    $this->db = $wpdb;
  }

  private function prepareCreateQuery()
  {
    $tab = new tCategory();
    $this->ctables[] = $tab->createQuery();

    $tab = new tOrders();
    $this->ctables[] = $tab->createQuery();

    $tab = new tProducts();
    $this->ctables[] = $tab->createQuery();

    $tab = new tTransactions();
    $this->ctables[] = $tab->createQuery();

    $tab = new tVat();
    $this->ctables[] = $tab->createQuery();
    //v18

    $tab = new tDocuments18();
    $this->ctables[] = $tab->createQuery();

    $tab = new tMagazine18();
    $this->ctables[] = $tab->createQuery();

    $tab = new tInvoices();
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

  public function createTables(): bool
  {
    $check = true;

    self::prepareCreateQuery();

    if (is_array($this->ctables) && !empty($this->ctables)) {
      foreach ($this->ctables as $count => $query) {
        $r = (bool) $this->db->query($query);
        $check = $check && $r;
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
