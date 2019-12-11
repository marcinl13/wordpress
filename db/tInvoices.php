<?php

namespace DB;

use TableDB\ITableNames;

class tInvoices
{
  private $tName;
  private $collate;

  function __construct()
  {
    $tab = new cTabelki();
    $this->tName = $tab->getTableName(ITableNames::Invoices);
    $this->collate = $tab->getCollate();
  }

  public function createQuery(): string
  {
    return "create TABLE IF NOT EXISTS {$this->tName}(
			id INT NOT NULL AUTO_INCREMENT, 
      userId INT NOT NULL,
      docType INT NOT NULL,
      orderId INT DEFAULT NULL,
      price DOUBLE(10,2) NOT NULL,
      PRIMARY KEY (id)
		) {$this->collate} ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }
}
