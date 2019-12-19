<?php

namespace DB;

class tCategory
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
    $db->getTableNames($noUsed, $this->tName);
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
      visible BOOLEAN NOT NULL DEFAULT TRUE COMMENT '0-hide, 1-show,
			PRIMARY KEY (id)
		)";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }

  private function update(int $version = 0, string &$query = "")
  {
    switch ((int) $version) {
      case 20:
        $query .= "ALTER TABLE {$this->tName} CHANGE nazwa name VARCHAR(20) CHARACTER SET utf8 NOT NULL;";

        return $query;
        break;
      default:
        self::update($version + 1, $query);
        break;
    }
  }
}
