<?php 

namespace DB;

use TableDB\ITableNames;

class DBConnection implements IDBDataFactor, ITableNames
{
  private $db;

  function __construct()
  {
    global $wpdb;
    $this->db = $wpdb;
  }

  public function getRow(string $query, string $IDBFactor): array
  {
    return (array) $this->db->get_row($query, $IDBFactor);
  }

  public function getResults(string $query, string $IDBFactor): array
  {
    return $this->db->get_results( $this->db->prepare($query, ""), $IDBFactor );
  }

  public function insert(string $query, int &$insertedID=0): bool
  {
    $isInserted = $this->db->query($query);

    $insertedID = $this->db->insert_id;

    return (bool) $isInserted;
  }

  public function query(string $query): bool
  {
    return (bool) $this->db->query($query);
  }

  public function getWPPrefix()
  {
    return $this->db->prefix;
  }

  public function getShopPrefix()
  {
    return self::getWPPrefix() . PROJECT_PREFIX;
  }

  public function getTableNames( &$productsTable=null,  &$categoriesTable=null, &$taxesTable=null, 
      &$orderTable=null, &$transactionTable=null, &$magazineTable=null, &$documentsTable=null, &$userTable=null, &$invoicesTable=null ){

    $shopPrefix = self::getShopPrefix();
    $wpPrefix = self::getWPPrefix();

    $productsTable = $productsTable===null ? "": $shopPrefix . ITableNames::Products;
    $categoriesTable = $categoriesTable===null ? "": $shopPrefix . ITableNames::Category;
    $taxesTable = $taxesTable===null ? "": $shopPrefix . ITableNames::Vat;
    $orderTable = $orderTable===null ? "": $shopPrefix . ITableNames::Orders;
    $transactionTable = $transactionTable===null ? "": $shopPrefix . ITableNames::Transactions;
    $magazineTable = $magazineTable===null ? "": $shopPrefix . ITableNames::Magazine18;
    $documentsTable = $documentsTable===null ? "": $shopPrefix . ITableNames::Documents18;
    $userTable = $userTable===null ? "": $wpPrefix . ITableNames::Users;

    $invoicesTable = $invoicesTable===null ? "": $wpPrefix . ITableNames::Invoices;
  }

}
