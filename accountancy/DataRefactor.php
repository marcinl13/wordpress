<?php

namespace DB;

use Langs\Phrases;
use Plugin\fileRW;

class DataRefactor
{
  private static $phrases;

  private function getPhrases()
  {
    self::$phrases = Phrases::getPhreses('ADMIN_OPTION_PRODUCTS');
  }

  public static function refactorToPZTable(array $array): array
  {
    self::getPhrases();

    $result = array();

    $result[] = array(
      self::$phrases['LP'] =>  1,
      self::$phrases['NAME'] => $array['productName'],
      self::$phrases['UNIT'] => $array['unit'],
      self::$phrases['QUANTITY'] => $array['quantity']
    );

    return $result;
  }

  public static function refactorToWZTable(array $array): array
  {
    self::getPhrases();
    $result = array();

    foreach ($array as $key => $value) {
      $result[] = array(
        self::$phrases['LP'] => $key + 1,
        self::$phrases['NAME'] => $value['name'],
        self::$phrases['UNIT'] => $value['jm'],
        self::$phrases['QUANTITY'] => $value['quantity']
      );
    }

    return $result;
  }

  public static function refactorToFVSTable(array $array): array
  {
    self::getPhrases();

    $waluta = fileRW::readJson(CONFIG_FILE)['currencyCode'];

    $sum1 = 0.00;
    $sum2 = 0.00;

    foreach ($array as $key => $value) {

      $netto = $value['netto'];
      $discount = number_format(floatval($value['discount']), 2);
      $cNetto = number_format(floatval($netto), 2);
      $quantity = $value['quantity'];
      $vat = $netto != 0 ? $value['price'] / $netto : 0;
      $wNetto = number_format(floatval($quantity * $cNetto), 2);
      $wBrutto = number_format(floatval($wNetto * $vat), 2);
      $vat = $netto != 0 ? $value['price'] / $netto : 0;
      $wNetto = number_format(floatval($quantity * $cNetto), 2);
      $wBrutto = number_format(floatval($wNetto * $vat), 2);
      $vat = $netto != 0 ? ($vat - 1) * 100 : 0;
      $sum1 += $wNetto;
      $sum2 += $wBrutto;

      //pola
      $array[$key][self::$phrases['LP']] = $key + 1;
      $array[$key][self::$phrases['NAME']] = $value['name'];
      $array[$key][self::$phrases['UNIT']] = $value['jm'];
      $array[$key][self::$phrases['QUANTITY']] = $quantity;
      $array[$key][self::$phrases['DISCOUNT']] = number_format(floatval($discount), 2);
      $array[$key][self::$phrases['PRICE_NETTO']] = number_format(floatval($netto), 2) . ' ' . $waluta;
      $array[$key][self::$phrases['TAX_RATE']] = $vat . ' %';
      $array[$key][self::$phrases['SUM_NETTO']] = number_format(floatval($wNetto), 2) . ' ' . $waluta;
      $array[$key][self::$phrases['SUM_BRUTTO']] = number_format(floatval($wBrutto), 2) . ' ' . $waluta;

      unset($array[$key]['id'], $array[$key]['name'], $array[$key]['jm'],
      $array[$key]['discount'], $array[$key]['quantity'], $array[$key]['price'],
      $array[$key]['netto']);
    }

    $sum1 = number_format($sum1, 2);
    $sum2 = number_format($sum2, 2);

    $sum = self::$phrases['SUM'];
    $tfoot = "<tfoot><tr style='text-align: right;'><td colspan='5' >{$sum}</td><td>{$discount}</td><td></td><td>{$sum1} {$waluta}</td><td>{$sum2} {$waluta}</td></tr></tfoot>";

    return array("tbody" => $array, "tfoot" => $tfoot);
  }

  public static function refactorCartTable(array $array)
  {
    $array[0][] = array(
      'id' => '2',
      'name' => Phrases::getPhreses('ADMIN_OPTION_PRODUCTS')['TRANSPORT_PRICE'],
      'jm' => 'szt',
      'discount' => '0.00',
      'quantity' => '1',
      'price' => $array[1],
      'netto' => $array[1]
    );

    unset($array[1]);

    return self::refactorToFVSTable($array[0]);
  }
}
