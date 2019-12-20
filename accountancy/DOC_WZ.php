<?php

namespace Accountancy;

use DB\DBConnection;
use DB\IDBDataFactor;
use Documents\IDocuments;
use Models\v18\mDocuments2;

class DOC_WZ extends mDocuments2 implements IDoc
{
  private $docType = IDocuments::WZ_ID;
  private $dateFormat = 'Y-m-d';

  private $model;

  function __construct(int $order, string $dataCreate)
  {
    $this->groupId = $order;

    self::FunctionName($dataCreate);
  }

  private function FunctionName(string $dataCreate)
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
    $m->setDataCreate($dataCreate);
    $m->setDataRealization($data);

    $this->model = $m;
  }

  public function save(int &$insertedId = 0): bool
  {
    return $this->model->save($insertedId);
  }

  public function getDetails($id): array
  {
    $db = new DBConnection();

    $documentsTable = "";
    $ordersTable = "";
    $noUsed="";

    $db->getTableNames($noUsed, $noUsed, $noUsed, $ordersTable, $noUsed, $noUsed, $documentsTable);
    
    $args = $db->getRow("select d.*, o.products
      FROM {$documentsTable} d 
      LEFT JOIN {$ordersTable} o ON d.orderID = o.id
      WHERE d.id={$id}"
    , IDBDataFactor::ARRAY_A);

    return (array) $args;
  }
}
