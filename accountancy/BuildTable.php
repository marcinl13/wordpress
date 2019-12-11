<?php

namespace html;

class BuidTable
{
  function __construct()
  { }

  public static function buildTable($array, string $tfoot = "", bool $styleAuto = true)
  {
    // start table
    $style = "";

    if ($styleAuto) {
      $style = "<style> th,td{border-bottom: 1px solid gray; } th:not(:last-child),td:not(:last-child){border-right: 1px solid gray;}  td{padding:5px;text-align: center;} th{padding: 0px 5px;} tfoot{font-weight: bold !important;} </style>";
    }

    $html = $style . '<table class=\'table\'>';
    // header row
    $html .= '<tr>';
    foreach ($array[0] as $key => $value) {
      $html .= '<th>' . htmlspecialchars($key) . '</th>';
    }
    $html .= '</tr>';

    // data rows
    foreach ($array as $key => $value) {
      $html .= '<tr>';
      foreach ($value as $key2 => $value2) {
        $html .= '<td>' . htmlspecialchars($value2) . '</td>';
      }
      $html .= '</tr>';
    }

    $html .= strlen($tfoot) > 0 ? $tfoot : "";
    // finish table and return it

    $html .= '</table>';
    return $html;
  }
}
