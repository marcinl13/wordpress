<?php

namespace Pdf;

use Accountancy\DOC_PZ;
use DB\DataRefactor;
use html\BuidTable;

class HTML_PZ extends PrefabPDF implements IPDF
{
  private $docId;

  function __construct(int $documentId)
  {
    $this->docId = $documentId;
  }

  public function getHtml(): string
  {
    $time = date("Y-m-d H:i:s");

    $docWZ = new DOC_PZ(0, '');
    $res = $docWZ->getDetails($this->docId);

    $prefab = new PrefabPDF();

    $text = '';
    $text .= $prefab->prepareDates((string) $res['data_zamowienia'], (string) $res['dateEnd']);
    $text .= $prefab->prepareTitle($res);
    $text .= "<br><div style='heigth:50px; width:100%; margin:10px 0px;'>&nbsp;</div>";
    $text .=  "<br><center>" . self::tableSection($res) . "</center>";
    $text .= "<br><div style='heigth:50px; width:100%; margin:10px 0px;'>&nbsp;</div>";
    $text .=  "<div style='position:absolute; bottom:10px; text-align:center; padding:0 30%;'>Wygenerowano {$time}</div>";

    return $text;
  }

  public function tableSection($res)
  {
    $args = DataRefactor::refactorToPZTable($res);
    return BuidTable::buildTable($args);
  }
}
