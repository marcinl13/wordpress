<?php

namespace Pdf;

use DB\DataRefactor;
use DB\DBConnection;
use DB\IDBDataFactor;
use html\BuidTable;

class HTML_FVS extends PrefabPDF implements IPDF
{
  private $db;
  private $docId;

  private $tableOrders;
  private $tableTransactions;
  private $tableDocuments;
  private $tableInvoices;

  function __construct(int $documentId)
  {
    $this->db = new DBConnection();
    $this->docId = $documentId;

    self::setTableNames();
  }

  private function setTableNames()
  {
    $noUsed = "";

    $this->db->getTableNames(
      $noUsed,
      $noUsed,
      $noUsed,
      $this->tableOrders,
      $this->tableTransactions,
      $noUsed,
      $this->tableDocuments,
      $noUsed,
      $this->tableInvoices
    );
  }

  public function getHtml(): string
  {
    self::setTableNames();

    $time = date("Y-m-d H:i:s");

    $result = $this->db->getRow(
      "sELECT * 
      FROM {$this->tableDocuments} d
      LEFT JOIN {$this->tableInvoices} inv ON d.orderID = inv.id 
      LEFT JOIN {$this->tableOrders} o ON inv.orderID = o.id 
      LEFT JOIN {$this->tableTransactions} t ON o.id = t.orderID
      WHERE d.id='{$this->docId}'",
      IDBDataFactor::ARRAY_A
    );

    $price = (float) $result['price'] < (float) $result['total'] ? (float) $result['total'] : (float) $result['price'];

    $prefab = new PrefabPDF();

    $text = '';
    $text .= $prefab->prepareDates((string) $result['dateCreate'], (string) $result['dateEnd']);
    $text .= $prefab->prepareTitle($result);
    $text .= "<br/><div style='margin:0 20px;'>" .
      $prefab->prepareBuyer((int) $result['userID']) .
      $prefab->prepareSeller()  . "<div>";
    $text .= "<br><div style='heigth:50px; width:100%; margin:10px 0px;'>&nbsp;</div>";
    $text .=  "<br><center>" . self::tableSection($result) . "</center>";
    $text .= "<br><div style='heigth:50px; width:100%; margin:10px 0px;'>&nbsp;</div>";
    $text .= $prefab->priceToText($price, (bool) $result['status']);
    $text .=  "<div style='position:absolute; bottom:10px; text-align:center; padding:0 30%;'>Wygenerowano {$time}</div>";

    return $text;
  }

  public function tableSection($result)
  {
    $args = json_decode(base64_decode($result['products']), true);

    $args = DataRefactor::refactorToFVSTable($args);

    return BuidTable::buildTable($args['tbody'], $args['tfoot']);
  }
}
