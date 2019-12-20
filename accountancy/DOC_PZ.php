<?php

namespace Accountancy;

use DB\DBConnection;
use DB\IDBDataFactor;
use Documents\IDocuments;
use Models\v18\mDocuments2;

class DOC_PZ extends mDocuments2 implements IDoc
{
  private $docType = IDocuments::PZ_ID;
  private $groupId;
  private $dateFormat = 'Y-m-d';

  private $model;

  function __construct(int $groupId)
  {
    $this->groupId = $groupId;

    self::FunctionName();
  }

  private function FunctionName()
  {
    $data = date($this->dateFormat);

    $m = new mDocuments2();
    $numeration = new Numeration($this->docType);

    $m->setNum($numeration->getNum());
    $m->setDay($numeration->getDay());
    $m->setMonth($numeration->getMonth());
    $m->setYear($numeration->getYear());
    $m->setGroupId($this->groupId);
    $m->setDocType($this->docType);
    $m->setDataCreate($data);
    $m->setDataRealization($data);

    $this->model = $m;
  }

  public function save(int &$insertedId = 0): bool
  {
    return (bool) $this->model->save($insertedId);
  }

  public function getDetails($id): array
  {
    $documentsTable = "";
    $magazineTable = "";
    $productsTable = "";
    $noUsed = "";

    $db = new DBConnection();

    $db->getTableNames($productsTable, $noUsed, $noUsed,$noUsed, $noUsed, $magazineTable, $documentsTable, $noUsed, $noUsed);

    $args = $db->getRow("select d.*, m.quantity, p.name as productName, p.unit
      FROM {$documentsTable} d 
      LEFT JOIN {$magazineTable} m ON d.orderID = m.id
      LEFT JOIN {$productsTable} p ON m.productID = p.id
      WHERE d.id={$id} "
    , IDBDataFactor::ARRAY_A);

    return (array) $args;
  }
}
