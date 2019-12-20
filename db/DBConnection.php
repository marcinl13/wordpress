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
    return $this->db->get_results($this->db->prepare($query, ""), $IDBFactor);
  }

  public function insert(string $query, int &$insertedID = 0): bool
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

  public function getTableNames(
    &$productsTable = null,
    &$categoriesTable = null,
    &$taxesTable = null,
    &$orderTable = null,
    &$transactionTable = null,
    &$magazineTable = null,
    &$documentsTable = null,
    &$userTable = null,
    &$invoicesTable = null
  ) {

    $shopPrefix = self::getShopPrefix();
    $wpPrefix = self::getWPPrefix();

    $productsTable = $shopPrefix . ITableNames::Products;
    $categoriesTable = $shopPrefix . ITableNames::Category;
    $taxesTable = $shopPrefix . ITableNames::Vat;
    $orderTable = $shopPrefix . ITableNames::Orders;
    $transactionTable = $shopPrefix . ITableNames::Transactions;
    $magazineTable = $shopPrefix . ITableNames::Magazine18;
    $documentsTable = $shopPrefix . ITableNames::Documents18;
    $userTable =  $wpPrefix . ITableNames::Users;

    $invoicesTable = $invoicesTable === null ? "" : $shopPrefix . ITableNames::Invoices;
  }
}
