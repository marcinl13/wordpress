<?php

namespace Models;

use DB\DBConnection;
use DB\IDBDataFactor;

class mProduct 
{
  private $id;
  private $ids;
  private $name;
  private $vatID;
  private $categoryId;
  private $netto;
  private $brutto;
  private $image;
  private $description;
  private $token;
  private $jm;

  
  private $dbConnection;
  private $tableProducts = '';
  private $tableCategory = '';
  private $tableVat = '';

  public function __construct()
  {
    $this->dbConnection = new DBConnection();

    self::setTableNames();
  }

#region getters
  /**
   * Get the value of id
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Set the value of id
   */
  public function setId(int $id = 0)
  {
    $this->id = $id;
  }

  /**
   * Get the value of ids
   */
  public function getIds(): string
  {
    return $this->ids;
  }

  /**
   * Set the value of ids
   */
  public function setIds(string $ids = "")
  {
    $this->ids = $ids;
  }

  /**
   * Get the value of name
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Set the value of name
   */
  public function setName(string $name = "")
  {
    $this->name = $name;
  }

  /**
   * Get the value of vatID
   */
  public function getVatID(): int
  {
    return $this->vatID;
  }

  /**
   * Set the value of vatID
   */
  public function setVatID(int $vatID)
  {
    $this->vatID = $vatID;
  }

  /**
   * Get the value of categoryId
   */
  public function getCategoryId(): int
  {
    return $this->categoryId;
  }

  /**
   * Set the value of categoryId
   */
  public function setCategoryId(int $categoryId = 0)
  {
    $this->categoryId = $categoryId;
  }

  /**
   * Get the value of netto
   */
  public function getNetto(): float
  {
    return $this->netto;
  }

  /**
   * Set the value of netto
   */
  public function setNetto(float $netto = 0.00)
  {
    $this->netto = $netto;
  }

  /**
   * Get the value of brutto
   */
  public function getBrutto(): float
  {
    return $this->brutto;
  }

  /**
   * Set the value of brutto
   */
  public function setBrutto(float $brutto = 0.00)
  {
    $this->brutto = $brutto;
  }

  /**
   * Get the value of image
   */
  public function getImage(): string
  {
    return $this->image;
  }
#endregion

#region setters
  /**
   * Set the value of image
   */
  public function setImage(string $image = "")
  {
    $this->image = $image;
  }

  /**
   * Get the value of description
   */
  public function getDescription(): string
  {
    return $this->description;
  }

  /**
   * Set the value of description
   */
  public function setDescription(string $description = "")
  {
    $this->description = $description;
  }

  /**
   * Get the value of token
   */
  public function getToken(): string
  {
    return $this->token;
  }

  /**
   * Set the value of token
   */
  public function setToken(string $token = "")
  {
    $this->token = $token;
  }

  /**
   * Get the value of jm
   */
  public function getJm(): string
  {
    return $this->jm;
  }

  /**
   * Set the value of jm
   */
  public function setJm(string $jm = "")
  {
    $this->jm = $jm;
  }

  /** */
  
#endregion
  
  private function setTableNames()
  {
    $products="";
    $categories="";
    $taxes="";

    $this->dbConnection->getTableNames($products,  $categories, $taxes, $order, $transaction, $magazine, $user);

    $this->tableProducts = $products;
    $this->tableCategory = $categories;
    $this->tableVat = $taxes;
  }

  public function getLastAddedProducts(int $amount = 5): array
  {
    $lastAddedProducts = $this->dbConnection->getResults(
      "sELECT name as name 
        FROM {$this->tableProducts} 
        ORDER BY id DESC 
        LIMIT {$amount}"
      ,IDBDataFactor::ARRAY_A);

    return (array) $lastAddedProducts;
  }

  public function getAll(): array
  {
    $args = $this->dbConnection->getResults(
      "select p.*, k.name as nazwa_kategori, ROUND(p.netto *(1+ sv.taxRate),2) as price
      from {$this->tableProducts} p
      INNER JOIN {$this->tableVat} sv ON p.taxRateID = sv.id
      LEFT JOIN {$this->tableCategory} k ON p.categoryID = k.id
      where p.visible = 1
      ORDER by p.id asc",
      IDBDataFactor::ARRAY_A
    );
    return (array) $args;
  }

  public function getOne(): array
  {
    $args =$this->dbConnection->getResults(
      "select p.*, k.name as nazwa_kategori, ROUND(p.netto *(1+ sv.taxRate),2) as price 
      from {$this->tableProducts} p 
      left join {$this->tableCategory} k ON p.categoryID = k.id
      INNER JOIN {$this->tableVat} sv ON p.taxRateID = sv.id
      where p.id = {$this->id} and p.visible = 1
      ORDER by p.id asc"
      ,IDBDataFactor::ARRAY_A
    )              ;

    return (array) $args;
  }

  public function getMany(): array
  {
    $args = $this->dbConnection->getResults(
      "select p.*, k.name as nazwa_kategori, ROUND(p.netto *(1+ sv.taxRate),2) as price 
      from {$this->tableProducts} p 
      left join {$this->tableCategory} k ON p.categoryID = k.id
      INNER JOIN {$this->tableVat} sv ON p.taxRateID = sv.id
      where p.id IN({$this->ids}) and p.visible = 1
      ORDER by p.id asc"
      ,IDBDataFactor::ARRAY_A
    );

    return (array) $args;
  }

  public function update(): bool
  {
    $id = self::getId();
    $name = self::getName();
    $categoryId = self::getCategoryId();
    $taxRate = self::getVatID();
    $brutto = self::getBrutto();
    $netto = self::getNetto();
    $desc = self::getDescription();
    $unit = self::getJm();
    $image = self::getImage();

    $status = $this->dbConnection->query("update {$this->tableProducts} SET 
      name='{$name}', taxRateID='{$taxRate}', categoryID='{$categoryId}', 
      netto='{$netto}', brutto='{$brutto}', image='{$image}', 
      details='{$desc}', unit='{$unit}' 
      WHERE id={$id}");

    return (bool) $status;
  }

  public function delete(): bool
  {
    $status =  $this->dbConnection->query("update {$this->tableProducts} SET visible='0' WHERE id IN({$this->ids})");

    return (bool) $status;
  }

  public function save(int &$insertID = 0, $addToMag = false): bool
  {
    $status = $this->dbConnection->insert(
      "insert INTO {$this->tableProducts} (name, taxRateID, categoryID, unit, netto, brutto, image, details)
        VALUES ('{$this->name}', '{$this->vatID}', '{$this->categoryId}', '{$this->jm}', '{$this->netto}', 
          '{$this->brutto}', '{$this->image}', '{$this->description}')"
      , $insertID);

    // $insertID = $this->db->insert_id;

    // if ($addToMag === true) {
    //   $mMag = new mMag();
    //   $mMag->setDocType(IDocuments::PZ_ID);
    //   $mMag->setOrderID(0);
    //   $mMag->setProductID($insertID);
    //   $mMag->setQuantity(1);
    //   $sv = $mMag->save();
    // }


    return (bool) $status;
  }
}
