<?php

namespace Models;

use DB\DBConnection;
use DB\IDBDataFactor;

class mCategory
{

  private $token;
  private $name;
  private $id;

  private $dbConnection;
  private $tableCat;

  public function __construct()
  {
    $this->dbConnection = new DBConnection();
    
    self::setTableName();
  }

#region getters
  public function getToken(): string
  {
    return $this->token;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getID(): int
  {
    return $this->id;
  }
#endregion
#region setters
  public function setToken(string $token = "")
  {
    $this->token = $token;
  }

  public function setName(string $name = "")
  {
    $this->name = $name;
  }

  public function setID(int $id = 0)
  {
    $this->id = $id;
  }
#endregion

  private function setTableName()
  {
    $categories="";
    $this->dbConnection->getTableNames($products,  $categories, $taxes, $order, $transaction, $magazine, $user);
    $this->tableCat = $categories;
  }

  public function getAll(): array
  {
    $args = $this->dbConnection->getResults(
      "select * from {$this->tableCat} where visible = 1 ORDER by ID asc",
      IDBDataFactor::ARRAY_A
    );
    return (array) $args;
  }

  public function getOne(): array
  {
    $args = $this->dbConnection->getResults(
      "select * from {$this->tableCat} where id={$this->id} and visible = 1 ORDER by ID asc",
      IDBDataFactor::ARRAY_A
    );
    return (array) $args;
  }

  public function update(): bool
  {
    $name = self::getName();
    $id= self::getID();

    $status = $this->dbConnection->query("update {$this->tableCat} SET name='{$name}' WHERE id='{$id}'");

    return (bool) $status;
  }

  public function delete(): bool
  {
    $status =  $this->dbConnection->query("update {$this->tableCat} SET visible='0' WHERE id IN({$this->id})");

    return (bool) $status;
  }

  public function save(int &$insertID = 0): bool
  {
    $status = $this->dbConnection->insert(
      "insert INTO {$this->tableCat}(name) VALUES ('{$this->name}')",
      $insertID
    );

    return (bool) $status;
  }
}
