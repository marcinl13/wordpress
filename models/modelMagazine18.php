<?php

namespace Models\v18;

use DB\DBConnection;
use DB\IDBDataFactor;
use Documents\IDocuments;

class mMagazine
{
  private $dbConnection;
  private $tableMagazine;
  private $tableProducts;
  private $tableOrders;
  private $tableCategory;

  private $id;
  private $productId;
  private $docType;
  private $quantity;
  private $groupId;

  function __construct()
  {
    $this->dbConnection = new DBConnection();

    self::setTableName();
  }

  private function setTableName()
  {
    $noUsed = "";

    $this->dbConnection->getTableNames($this->tableProducts,  $this->tableCategory, $noUsed, $this->tableOrders, $noUsed, $this->tableMagazine);
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
      "insert INTO {$this->tableMagazine}(docType,productID,quantity, groupId) VALUES ('{$this->docType}','{$this->productId}','{$this->quantity}','{$this->groupId}')",
      $insertID
    );

    return (bool) $status;
  }

  #endregion

  #region STATYSTIC

  public function getProductInMagazine2(): array
  {
    $pz = IDocuments::PZ_ID;
    $wz = IDocuments::WZ_ID;

    $currentProdMagStatus = $this->dbConnection->getResults("
        SELECT p.id, p.name as name,  
        
        (Select sum(m.quantity) FROM {$this->tableMagazine} m where m.docType ={$pz} and p.id = m.productID) as inMag,
        
        COALESCE((Select sum(m.quantity) FROM {$this->tableMagazine} m where m.docType ={$wz} and p.id = m.productID), 0) as outMag,
        
        (Select sum(m.quantity) FROM {$this->tableMagazine} m where m.docType ={$pz} and p.id = m.productID)-COALESCE((Select sum(m.quantity) FROM {$this->tableMagazine} m where m.docType ={$wz} and p.id = m.productID), 0) as curMag
        
        FROM {$this->tableMagazine} m
        JOIN {$this->tableProducts} p ON p.id = m.productID
        GROUP by p.id", IDBDataFactor::ARRAY_A);

    return (array) $currentProdMagStatus;
  }

  public function quantityProductGroup()
  {
    $wz = IDocuments::WZ_ID;

    $quantityProductGroup = $this->dbConnection->getResults(
      "sELECT 
          p.name, 
          SUM(mg.quantity) as quantity, 
          MONTH(z.dateOrder) as m,
          YEAR(z.dateOrder) as y 
        FROM {$this->tableMagazine} mg
        LEFT JOIN {$this->tableProducts} p  ON mg.productID = p.id
        LEFT JOIN {$this->tableOrders} z ON mg.groupId = z.id
        WHERE mg.docType = {$wz} and YEAR(z.dateOrder) = YEAR(CURDATE())
        GROUP BY m,p.id,y",
      IDBDataFactor::ARRAY_A
    );

    // foreach ($quantityProductGroup as $key => $value) {
    //   if (strlen($value['nazwa']) == 0) {
    //     unset($quantityProductGroup[$key]);
    //   }
    // }

    return (array) $quantityProductGroup;
  }

  public function getChar01Data(): array
  {
    $output = $this->dbConnection->getResults(
      "sELECT SUM(total) as price, MONTH(dateOrder) as m, YEAR(dateOrder) as y 
      FROM {$this->tableOrders} 
      WHERE visible = 1 AND statusID =2 
      GROUP BY y,m 
      ORDER BY YEAR(dateOrder) DESC",
      IDBDataFactor::ARRAY_A
    );

    $tmp = array();
    $tmp2 = array();

    if (empty($output)) return $tmp;

    foreach ($output as $key => $tag) {
      $tmp2[$tag['y']][$tag['m']] = $tag['price'];
    }

    foreach ($tmp2 as $key => $tag) {
      for ($i = 0; $i < 12; $i++) {
        $tmp[$key][$i] = number_format(floatval($tag[$i + 1]), 2, '.', '');
      }
    }

    return $tmp;
  }

  public function productCategory()
  {
    $wz = IDocuments::WZ_ID;

    $productCategory = $this->dbConnection->getResults(
      "sELECT 
        SUM(mg.quantity) as quantity, 
        MONTH(z.dateOrder) as m,
        YEAR(z.dateOrder) as y,
        k.name as categoryName
      FROM {$this->tableMagazine} mg
      LEFT JOIN {$this->tableProducts} p  ON mg.productID = p.id
      LEFT JOIN {$this->tableCategory} k ON p.categoryID = k.id
      LEFT JOIN {$this->tableOrders} z ON mg.groupId = z.id
      WHERE mg.docType = {$wz} and YEAR(z.dateOrder) = YEAR(CURDATE())
      GROUP BY y,m, k.id",
      IDBDataFactor::ARRAY_A
    );

    return (array) $productCategory;
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
   * Set the value of productId
   *
   */
  public function setProductId(int $productId)
  {
    $this->productId = $productId;
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
   * Set the value of quantity
   *
   */
  public function setQuantity(int $quantity)
  {
    $this->quantity = $quantity;
  }

  /**
   * Set the value of groupID
   *
   */
  public function setGroupId(int $groupId)
  {
    $this->groupId = $groupId;
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
   * Get the value of productId
   */
  public function getProductId(): int
  {
    return $this->productId;
  }

  /**
   * Get the value of docType
   */
  public function getDocType(): int
  {
    return $this->docType;
  }

  /**
   * Get the value of quantity
   */
  public function getQuantity(): int
  {
    return $this->quantity;
  }

  /**
   * Get the value of groupId
   */
  public function getGroupId(): int
  {
    return $this->groupId;
  }

  #endregion
}
