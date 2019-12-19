<?php

namespace Models\v18;

use DB\DBConnection;

class mInvoices
{
  private $dbConnection;
  private $tableInvoices;


  private $id;
  private $userId;
  private $docType;
  private $orderId;
  private $price;


  function __construct()
  {
    $this->dbConnection = new DBConnection();

    self::setTableName();
  }

  private function setTableName()
  {
    $noUsed = "";

    $this->dbConnection->getTableNames($noUsed, $noUsed, $noUsed, $noUsed, $noUsed, $noUsed, $noUsed, $noUsed,  $this->tableInvoices);
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
    $userId = self::getUserId();
    $docType = self::getDocType();
    $orderId = self::getOrderId();
    $price = self::getPrice();


    $status = $this->dbConnection->insert(
      "insert INTO {$this->tableInvoices}(userId,docType,orderID,price) VALUES ('{$userId}','{$docType}','{$orderId}','{$price}')",
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
   * Set the value of orderId
   *    
   */
  public function setOrderId(int $orderId)
  {
    $this->orderId = $orderId;
  }

  /**
   * Set the value of userId
   *
   */
  public function setUserId(int $userId)
  {
    $this->userId = $userId;
  }

  /**
   * Set the value of price
   *
   */
  public function setPrice(float $price)
  {
    $this->price = $price;
  }
  #endregion

  #region getters

  /**
   * Get the value of id
   */
  public function getId(): int
  {
    return (int) $this->id;
  }

  /**
   * Get the value of docType
   */
  public function getDocType(): int
  {
    return (int) $this->docType;
  }

  /**
   * Get the value of orderId
   */
  public function getOrderId(): int
  {
    return (int) $this->orderId;
  }

  /**
   * Get the value of userId
   */
  public function getUserId(): int
  {
    return (int) $this->userId;
  }

  /**
   * Get the value of price
   */
  public function getPrice(): float
  {
    return (float) $this->price;
  }
  #endregion







}
