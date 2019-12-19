<?php

namespace DB;

class tOrders
{
  private $tName;
  private $update = false;
  private $currentVersion = 0;

  function __construct(bool $update = false, int $version = 0)
  {
    $this->update = $update;
    $this->currentVersion = $version;

    $noUsed = "";
    $db = new DBConnection();
    $db->getTableNames($noUsed, $noUsed, $noUsed, $this->tName);
  }

  public function createQuery(): string
  {
    if ($this->update) {
      $query = "";
      self::update($this->currentVersion, $query);

      return $query;
    }

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
		);";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }

  private function update(int $version = 0, string &$query = "")
  {
    switch ((int) $version) {
      case 20:
        $query .= "ALTER TABLE {$this->tName} CHANGE id_uzytkownika userID INT NOT NULL; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE id_statusu statusID INT  NOT NULL; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE data_zamowienia dateOrder DATETIME DEFAULT CURRENT_TIMESTAMP; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE data_realizacji dateRealization DATETIME NULL; ";
        $query .= "ALTER TABLE {$this->tName} CHANGE produkty products TEXT CHARACTER SET utf8 NULL; ";

        return $query;
        break;
      default:
        self::update($version + 1, $query);
        break;
    }
  }
}
