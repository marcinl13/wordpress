<?php

namespace Models;

use DB\DBConnection;
use DB\IDBDataFactor;

class mVat
{
  private $id;
  private $name;
  private $value;
  private $token;

  private $dbConnection;
  private $tableVat;

  function __construct()
  {
    $this->dbConnection = new DBConnection();

    self::setTableName();
  }

#region getters/setters
  /**
   * Get the value of id
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Set the value of id
   */
  public function setId(int $id = 0)
  {
    $this->id = $id;
  }

  /**
   * Get the value of name
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Set the value of name
   */
  public function setName(string $name = "")
  {
    $this->name = $name;
  }

  /**
   * Get the value of value
   */
  public function getValue(): float
  {
    return $this->value;
  }

  /**
   * Set the value of value
   */
  public function setValue(float $value = 0.00)
  {
    $this->value = $value;
  }

  /**
   * Get the value of token
   */
  public function getToken(): string
  {
    return $this->token;
  }

  /**
   * Set the value of token
   */
  public function setToken(string $token = "")
  {
    $this->token = $token;
  }
#endregion

  /** */

  private function setTableName()
  {
    $taxes="";
    
    $this->dbConnection->getTableNames($products,  $categories, $taxes, $order, $transaction, $magazine, $user);
    
    $this->tableVat = $taxes;
  }

  public function getAll(): array
  {
    $args = $this->dbConnection->getResults(
      "select * FROM {$this->tableVat} WHERE visible=1", 
      IDBDataFactor::ARRAY_A
    );

    return (array) $args;
  }

  public function getOne(): array
  {
    $args = $this->dbConnection->getResults(
      "select * FROM {$this->tableVat} WHERE visible=1 and id={$this->id}", 
      IDBDataFactor::ARRAY_A
    );

    return (array) $args;
  }

  public function update(): bool
  {
    $status = $this->dbConnection->query("update {$this->tableVat} SET name='{$this->name}' taxRate='{$this->value}' WHERE id=" . $this->id);

    return (bool) $status;
  }

  public function delete(): bool
  {
    $status =  $this->dbConnection->query("update {$this->tableVat} SET visible='0' WHERE id = {$this->id}");

    return (bool) $status;
  }

  public function save(int &$insertID = 0): bool
  {
    $status = $this->dbConnection->insert(
      "insert INTO {$this->tableVat} (name, taxRate) VALUES ('{$this->name}','{$this->value}')",
      $insertID);

    return (bool) $status;
  }
}
