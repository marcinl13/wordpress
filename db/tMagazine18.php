<?php

namespace DB;


class tMagazine18
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
    $db->getTableNames($noUsed, $noUsed, $noUsed, $noUsed, $noUsed, $this->tName);
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
      docType INT NOT NULL,
      productID INT NOT NULL,
      quantity INT NOT NULL,
      groupId INT NOT NULL,
			PRIMARY KEY (id)
		)  ;";
  }

  public function deleteQuery(): string
  {
    return "drop TABLE {$this->tName};";
  }

  private function update(int $version = 0, string &$query = "")
  {
    switch ((int) $version) {
      case 20:
        return $query;
        break;
      default:
        self::update($version + 1, $query);
        break;
    }
  }
}
