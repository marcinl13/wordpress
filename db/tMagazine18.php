<?php

namespace DB;

use TableDB\ITableNames;

class tMagazine18
{
  private $tName;
  private $collate;

  function __construct()
  {
    $tab = new cTabelki();
    $this->tName = $tab->getTableName(ITableNames::Magazine18);
    $this->collate = $tab->getCollate();
  }

  public function createQuery(): string
  {
    return "create TABLE IF NOT EXISTS {$this->tName}(
			id INT NOT NULL AUTO_INCREMENT,
      docType INT NOT NULL,
      productID INT NOT NULL,
      quantity INT NOT NULL,
      groupId INT NOT NULL,
			PRIMARY KEY (id)
		) {$this->collate} ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }
}
