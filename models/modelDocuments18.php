<?php

namespace Models\v18;

use DB\DBConnection;
use DB\IDBDataFactor;
use Documents\IDocuments;
use Plugin\fileRW;

class mDocuments2
{
  private $id;
  private $docType;
  private $num;
  private $day;
  private $month;
  private $year;
  private $groupId;
  private $dataCreate;
  private $dataRealization;

  private $dbConnection;
  private $tableDocuments;
  private $tableOrders;

  function __construct()
  {
    $this->dbConnection= new DBConnection();

    self::setTableName();
  }

  private function setTableName()
  {
    $order="";
    $documents="";

    $this->dbConnection->getTableNames($products,  $categories, $taxes, $order, $transaction, $magazine, $documents, $user);
    
    $this->tableDocuments = $documents;
    $this->tableOrders = $order;
  }

  private function getShortName(int $docType, int $num, int $day, int $month, int $year): string
  {
    $short = '';
    if ($docType == IDocuments::tmp_FVS) $short = IDocuments::FVS;
    if ($docType == IDocuments::tmp_FVK) $short = IDocuments::FVK;
    if ($docType == IDocuments::WZ_ID) $short = IDocuments::WZ;
    if ($docType == IDocuments::PZ_ID) $short = IDocuments::PZ;


    $args = array();

    foreach (explode("/", fileRW::readJsonAssoc(CONFIG_FILE)['invoiceFormat']) as $key => $value) {
      if ($value == 'RRRR') $args[] = $year;
      if ($value == 'MM') $args[] = $month;
      if ($value == 'DD') $args[] = $day;
      if ($value == 'NR') $args[] = $num;
    }

    return $short . ' ' . join(" / ", $args);
  }

  public function getAll(): array
  {
    $args = $this->dbConnection->getResults(
      "select d.id, d.docType,d.num,d.day,d.month,d.year,d.dateCreate,d.dateEnd
       FROM {$this->tableDocuments} d
       ORDER by d.id desc",
     IDBDataFactor::ARRAY_A);

    foreach ($args as $key => $value) {
      $args[$key]['shortName'] = self::getShortName($value['docType'], $value['num'], $value['day'], $value['month'], $value['year']);
      
      unset($args[$key]['num'], $args[$key]['day'], $args[$key]['month'], $args[$key]['year']);
    }

    return (array) $args;
  }

  #region CRUD

  public function update(): bool
  {
    $status = false;

    return (bool) $status;
  }

  public function delete(): bool
  {
    $status =  false; 

    return (bool) $status;
  }

  public function save(int &$insertID = 0): bool
  {
    $status = $this->dbConnection->insert(
      "insert INTO {$this->tableDocuments}(docType,orderID,num,day,month,year,dateCreate,dateEnd) 
        VALUES ('{$this->docType}','{$this->groupId}','{$this->num}','{$this->day}','{$this->month}','{$this->year}',
          '{$this->dataCreate}','{$this->dataRealization}')",
      $insertID
    );

    return (bool) $status;
  }

  #endregion

  #region setters

  /**
   * Set the value of id
   *
   */
  public function setId(int $id)
  {
    $this->id = $id;
  }

  /**
   * Set the value of docType
   *
   */
  public function setDocType(int $docType)
  {
    $this->docType = $docType;
  }

  /**
   * Set the value of dataCreate
   *
   */
  public function setDataCreate(string $dataCreate)
  {
    $this->dataCreate = $dataCreate;
  }

  /**
   * Set the value of orderId
   *
   */
  public function setGroupId(int $groupId)
  {
    $this->groupId = $groupId;
  }

  /**
   * Set the value of num
   *
   */
  public function setNum(int $num)
  {
    $this->num = $num;
  }

  /**
   * Set the value of day
   *
   */
  public function setDay(int $day)
  {
    $this->day = $day;
  }

  /**
   * Set the value of month
   *
   */
  public function setMonth(int $month)
  {
    $this->month = $month;
  }

  /**
   * Set the value of year
   *
   */
  public function setYear(int $year)
  {
    $this->year = $year;
  }

  /**
   * Set the value of dataRealization
   *
   */
  public function setDataRealization(string $dataRealization)
  {
    $this->dataRealization = $dataRealization;
  }

  #endregion

  #region getters

  /**
   * Get the value of id
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Get the value of docType
   */
  public function getDocType(): int
  {
    return $this->docType;
  }

  /**
   * Get the value of dataCreate
   */
  public function getDataCreate(): string
  {
    return $this->dataCreate;
  }

  /**
   * Get the value of orderId
   */
  public function getGroupId(): int
  {
    return $this->groupId;
  }

  /**
   * Get the value of num
   */
  public function getNum(): int
  {
    return $this->num;
  }

  /**
   * Get the value of day
   */
  public function getDay(): int
  {
    return $this->day;
  }


  /**
   * Get the value of month
   */
  public function getMonth(): int
  {
    return $this->month;
  }

  /**
   * Get the value of year
   */
  public function getYear(): int
  {
    return $this->year;
  }

  /**
   * Get the value of dataRealization
   */
  public function getDataRealization(): string
  {
    return $this->dataRealization;
  }

  #endregion
}
