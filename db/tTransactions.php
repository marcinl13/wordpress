<?php

namespace DB;

use TableDB\ITableNames;

class tTransactions
{
  private $tName;
  private $collate;

  function __construct()
  {
    $tab = new cTabelki();
    $this->tName = $tab->getTableName(ITableNames::Transactions);
    $this->collate = $tab->getCollate();
  }

  public function createQuery(): string
  {
    return "create TABLE IF NOT EXISTS {$this->tName}(
			id INT NOT NULL AUTO_INCREMENT, 
      orderID INT NOT NULL, 
      userID INT NOT NULL,
      hashOrder VARCHAR(50) NOT NULL,
      status TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0-uncompleted, 1-completed,
      redirect TEXT NOT NULL,
			PRIMARY KEY (id)
		) {$this->collate} ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }
}
