<?php

namespace Number;

use Langs\ILanguages;
use Langs\Phrases;

class NumberConverter implements ILanguages
{
  private $lang;

  function __construct()
  {
    $this->lang = strtoupper(Phrases::getLang());
  }

  private function phrasesEN(&$unit, &$nascie, &$tens, &$hundreds, &$groups)
  {
    $unit = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
    $nascie = array('', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
    $tens = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
    $hundreds = array('', 'hundred', 'two hundred', 'three hundred', 'four hundred', 'five hundred', 'six hundred', 'seven hundred', 'eight hundred', 'nine hundred');
    $groups = array(
      array('', '', ''),
      array('thousand', 'thousands', 'thousands'),
      array('million', 'millions', 'million'),
      array('billion', 'billion', 'billion'),
      array('trillion', 'trillion', 'trillion'),
      array('billiards', 'billions', 'billions'),
      array('trillion', 'trillion', 'trillion')
    );
  }

  private function phrasesPL(&$unit, &$nascie, &$tens, &$hundreds, &$groups)
  {
    $unit = array('', ' jeden', ' dwa', ' trzy', ' cztery', ' pięć', ' sześć', ' siedem', ' osiem', ' dziewięć');
    $nascie = array('', ' jedenaście', ' dwanaście', ' trzynaście', ' czternaście', ' piętnaście', ' szesnaście', ' siedemnaście', ' osiemnaście', ' dziewietnaście');
    $tens = array('', ' dziesieć', ' dwadzieścia', ' trzydzieści', ' czterdzieści', ' pięćdziesiąt', ' sześćdziesiąt', ' siedemdziesiąt', ' osiemdziesiąt', ' dziewięćdziesiąt');
    $hundreds = array('', ' sto', ' dwieście', ' trzysta', ' czterysta', ' pięćset', ' sześćset', ' siedemset', ' osiemset', ' dziewięćset');
    $groups = array(
      array('', '', ''),
      array(' tysiąc', ' tysiące', ' tysięcy'),
      array(' milion', ' miliony', ' milionów'),
      array(' miliard', ' miliardy', ' miliardów'),
      array(' bilion', ' biliony', ' bilionów'),
      array(' biliard', ' biliardy', ' biliardów'),
      array(' trylion', ' tryliony', ' trylionów')
    );
  }

  private function convertToTextPL($liczba)
  {
    $jednosci = array();
    $nascie = array();
    $dziesiatki = array();
    $setki  = array();
    $grupy = array();

    if ($this->lang == ILanguages::PL) self::phrasesPL($jednosci, $nascie, $dziesiatki, $setki, $grupy);
    else  self::phrasesEN($jednosci, $nascie, $dziesiatki, $setki, $grupy);

    $wynik = '';
    $znak = '';
    if ($liczba == 0)
      return 'zero';
    if ($liczba < 0) {
      $znak = 'minus';
      $liczba = -$liczba;
    }
    $g = 0;
    while ($liczba > 0) {


      $s = floor(($liczba % 1000) / 100);
      $n = 0;
      $d = floor(($liczba % 100) / 10);
      $j = floor($liczba % 10);


      if ($d == 1 && $j > 0) {
        $n = $j;
        $d = $j = 0;
      }

      $k = 2;
      if ($j == 1 && $s + $d + $n == 0)
        $k = 0;
      if ($j == 2 || $j == 3 || $j == 4)
        $k = 1;

      if ($s + $d + $n + $j > 0)
        $wynik = $setki[$s] . $dziesiatki[$d] . $nascie[$n] . $jednosci[$j] . $grupy[$g][$k] . $wynik;

      $g++;
      $liczba = floor($liczba / 1000);
    }
    return trim($znak . $wynik);
  }

  public function numberToText(int $number): string
  {
    return self::convertToTextPL($number);
  }

  public function priceToText(string $number, string $currencyCode = "PLN"): string
  {
    $exploded = explode(".", $number);

    $lang = $this->lang;;

    $toMerge = array("", "", "", "");

    $odm = array(
      ILanguages::PL => array(
        "gr" => array('grosz', 'grosze', 'groszy'),
        "zl" => array('złoty', 'złote', 'złotych')
      ),
      ILanguages::EN => array(
        "gr" => array('penny', 'pennies', 'pennies'),
        "zl" => array('zloty', 'zlote', 'zlotys')
      )

    );
    
    if ($lang != ILanguages::PL) {
      $numberFormatter = new \NumberFormatter($lang, \NumberFormatter::SPELLOUT);

      $toMerge[0] = $numberFormatter->format($exploded[0]);
      $toMerge[2] = $numberFormatter->format($exploded[1]);
    } else {
      $toMerge[0] = self::convertToTextPL($exploded[0]);
      $toMerge[2] = self::convertToTextPL($exploded[1]);
    }



    if ((int) $exploded[0] == 1) {
      $toMerge[1] = $odm[$lang]["zl"][0]; //1 złoty
    }
    if ((int) $exploded[0] > 1 && $exploded[0] <= 4) {
      $toMerge[1] = $odm[$lang]["zl"][1]; //2,3,4 złote
    }
    if ((int) (int) $exploded[0] >= 5) {
      $toMerge[1] = $odm[$lang]["zl"][2]; //11 złotych
    }

    if ((int) $exploded[1] == 1) {
      $toMerge[3] = $odm[$lang]["gr"][0]; //1 grosz
    }
    if ((int) $exploded[1] > 1 && $exploded[1] <= 4) {
      $toMerge[3] = $odm[$lang]["gr"][1]; //2,3,4 grosze
    }
    if ((int) $exploded[1] >= 5) {
      $toMerge[3] = $odm[$lang]["gr"][2]; //11 groszy
    }

    return join(" ", $toMerge);
  }
}
