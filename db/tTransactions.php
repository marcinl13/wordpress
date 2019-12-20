<?php

namespace DB;

class tTransactions
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
    $db->getTableNames($noUsed, $noUsed, $noUsed, $noUsed, $this->tName);
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

  private function update(int $version = 0, string &$query = "")
  {
    switch ((int) $version) {
      case 20:
        $query .= "ALTER TABLE {$this->tName} ADD dateCreate DEFAULT CURRENT_TIMESTAMP; ";
        
        return $query;
        break;
      default:
        self::update($version + 1, $query);
        break;
    }
  }
}
