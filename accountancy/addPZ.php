<?php

namespace Magazine;

use Documents\IDocuments;
use Models\v18\mMagazine;

class addPZ extends mMagazine
{
  private $docType;
  private $groupID = 0;
  private $quantity;
  private $productId;
  private $modelMagazine;

  function __construct()
  {
    $this->docType = IDocuments::PZ_ID;

    $this->modelMagazine = new mMagazine();
  }

  public function addQuantity(int $quantity)
  {
    $this->quantity = $quantity;
  }

  public function addProduct(int $productId)
  {
    $this->productId = $productId;
  }


  public function save(int &$insertedId = 0): bool
  {
    $this->modelMagazine->setDocType($this->docType);
    $this->modelMagazine->setGroupId($this->groupID);
    $this->modelMagazine->setQuantity($this->quantity);
    $this->modelMagazine->setProductId($this->productId);


    return $this->modelMagazine->save($insertedId);
  }
}
