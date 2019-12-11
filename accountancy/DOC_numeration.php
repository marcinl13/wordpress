<?php

namespace Accountancy;

use DateTime;
use DB\cTabelki;
use DB\DBConnection;
use DB\IDBDataFactor;
use TableDB\ITableNames;

class Numeration
{
  private $db;
  private $docTable;

  private $docType;
  private $currentDate;
  private $currentFormat;

  private $create = false;

  private $num;
  private $day;
  private $month;
  private $year;

  function __construct(int $docTypeID, string $currentDate = '', string $currentFormat = 'Y-m-d')
  {
    $this->db = new DBConnection();

    $this->docType = (int) $docTypeID;
    $this->currentDate = $currentDate == '' ? date('Y-m-d') : $currentDate;
    $this->currentFormat = $currentFormat;

    self::build();
  }

  private function setTableName()
  {
    $table = new cTabelki();
    $this->docTable = $table->getTableName(ITableNames::Documents18);
  }

  public function getNextNumber(int $docType = 1): array
  {
    self::setTableName();

    $result = $this->db->getRow("select MAX(num)+1 as num FROM {$this->docTable} WHERE docType={$docType}", IDBDataFactor::ARRAY_A);

    return $result;
  }

  private function build()
  {
    if ($this->create == false) {
      self::setTableName();
      $docType = self::getDocType();

      $result = $this->db->getRow("select MAX(num)+1 as num FROM {$this->docTable} WHERE docType={$docType}", IDBDataFactor::ARRAY_A);

      $date = DateTime::createFromFormat($this->currentFormat, $this->currentDate);

      $this->num = empty($result['num']) ? 1 : (int) $result['num'];
      $this->year = (int) $date->format("Y");
      $this->month = (int) $date->format("m");
      $this->day = (int) $date->format("d");

      $this->create = true;
    }
  }

  /**
   * Get the value of num
   */
  public function getNum(): int
  {
    return (int) $this->num;
  }

  /**
   * Get the value of day
   */
  public function getDay(): int
  {
    return (int) $this->day;
  }

  /**
   * Get the value of month
   */
  public function getMonth(): int
  {
    return (int) $this->month;
  }

  /**
   * Get the value of year
   */
  public function getYear(): int
  {
    return (int) $this->year;
  }

  /**
   * Get the value of docType
   */
  public function getDocType()
  {
    return $this->docType;
  }
}
