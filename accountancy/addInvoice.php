<?php

namespace Accountancy;

use Models\v18\mInvoices;

class addInvoice extends mInvoices
{
  private $model;

  function __construct(int $userId, int $docType, int $orderId, int $price)
  {
    $model = new mInvoices();
    $model->setUserId($userId);
    $model->setDocType($docType);
    $model->setOrderId($orderId);
    $model->setPrice($price);

    $this->model = $model;
  }

  public function save(int &$insertedId = 0): bool
  {
    return $this->model->save($insertedId);
  }
}
