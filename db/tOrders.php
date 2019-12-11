<?php

namespace DB;

use TableDB\ITableNames;

class tOrders
{
  private $tName;
  private $collate;

  function __construct()
  {
    $tab = new cTabelki();
    $this->tName = $tab->getTableName(ITableNames::Orders);
    $this->collate = $tab->getCollate();
  }

  public function createQuery(): string
  {
    return "create TABLE IF NOT EXISTS {$this->tName}(
			id INT NOT NULL AUTO_INCREMENT,
			id_uzytkownika INT NOT NULL,
			id_statusu INT DEFAULT 1 COMMENT '1-inprogress, 2-done, 3-cancel',
			data_zamowienia DATETIME DEFAULT CURRENT_TIMESTAMP,
			data_realizacji DATETIME NULL,
      price DOUBLE(10,2) NOT NULL,
      priceTransport DOUBLE(4,2) DEFAULT 0.00,
      total DOUBLE(10,2) NOT NULL,
			produkty TEXT NULL,
      visible BOOLEAN NOT NULL DEFAULT TRUE COMMENT '0-hide, 1-show,
			PRIMARY KEY (id)
		) {$this->collate} ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }
}
