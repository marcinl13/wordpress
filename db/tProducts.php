<?php

namespace DB;

use TableDB\ITableNames;

class tProducts
{
  private $tName;
  private $collate;

  function __construct()
  {
    $tab = new cTabelki();
    $this->tName = $tab->getTableName(ITableNames::Products);
    $this->collate = $tab->getCollate();
  }

  public function createQuery(): string
  {
    return "create TABLE IF NOT EXISTS {$this->tName}(
			id INT NOT NULL AUTO_INCREMENT , 
			nazwa VARCHAR(20) NOT NULL , 
			id_stawki INT NOT NULL , 
			id_kategori INT NOT NULL, 
			jm VARCHAR(10) NOT NULL, 
			netto DOUBLE(10,2) NOT NULL , 
			brutto DOUBLE(10,2) NOT NULL , 
			zdjecie VARCHAR(255) DEFAULT '',
      opis VARCHAR(255) DEFAULT '',
      jm VARCHAR(10) DEFAUL 'szt',
      visible BOOLEAN NOT NULL DEFAULT TRUE COMMENT '0-hide, 1-show,
			PRIMARY KEY (id)
		) {$this->collate} ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }
}
