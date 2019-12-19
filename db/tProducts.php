<?php

namespace DB;

class tProducts
{
  private $tName;
  private $update = false;
  private $currentVersion = 0;

  function __construct(bool $update = false, int $version = 0)
  {
    $this->update = $update;
    $this->currentVersion = $version;

    $db = new DBConnection();
    $db->getTableNames($this->tName);
  }

  public function createQuery(): string
  {
    if ($this->update) {
      $query = "";
      self::update($this->currentVersion, $query);

      return $query;
    }

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
		) ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }

  private function update(int $version = 0, string &$query = "")
  {
    switch ((int) $version) {
      case 20:
        $query .= "ALTER TABLE {$this->tName} CHANGE nazwa name VARCHAR(20) CHARACTER SET utf8 NOT NULL; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE id_stawki taxRateID INT NOT NULL; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE id_kategori categoryID INT NOT NULL; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE jm unit VARCHAR(10) CHARACTER SET utf8 DEFAUL 'szt'; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE zdjecie image TEXT CHARACTER SET utf8 DEFAULT ''; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE opis details TEXT CHARACTER SET utf8 DEFAULT ''; ";

        return $query;
        break;
      default:
        self::update($version + 1, $query);
        break;
    }
  }
}
