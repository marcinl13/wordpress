<?php

namespace Magazine;

use Documents\IDocuments;
use Models\v18\mMagazine;

class addWZ extends mMagazine
{
  private $docType;
  private $modelMagazine;

  function __construct()
  {
    $this->docType = IDocuments::WZ_ID;

    $this->modelMagazine = new mMagazine();
    $this->modelMagazine->setDocType($this->docType);
  }

  public function addQuantity(int $quantity)
  {
    $this->modelMagazine->setQuantity($quantity);
  }

  public function addProduct(int $productId)
  {
    $this->modelMagazine->setProductId($productId);
  }

  public function addGroupId(int $groupId)
  {
    $this->modelMagazine->setGroupId($groupId);
  }

  public function save(int &$insertedId = 0): bool
  {
    return (bool) $this->modelMagazine->save($insertedId);
  }
}
