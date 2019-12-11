<?php

namespace DB;

use TableDB\ITableNames;

class tDocuments18
{
  private $tName;
  private $collate;

  function __construct()
  {
    $tab = new cTabelki();
    $this->tName = $tab->getTableName(ITableNames::Documents18);
    $this->collate = $tab->getCollate();
  }

  public function createQuery(): string
  {
    return "create TABLE IF NOT EXISTS {$this->tName}(
      id INT NOT NULL AUTO_INCREMENT,
      docType INT NOT NULL,
      orderId INT DEFAULT NULL,
      num INT NOT NULL,
      day INT NOT NULL,
      month INT NOT NULL,
      year INT NOT NULL,
      dateCreate DATETIME DEFAULT CURRENT_TIMESTAMP,
      dateEnd DATETIME NULL,
			PRIMARY KEY (id)
		) {$this->collate} ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }
}
