<?php

namespace Pdf;

use Documents\IDocuments;
use Langs\Phrases;
use Number\NumberConverter;
use Plugin\fileRW;
use Users\Buyer;
use Users\Seller;

class PrefabPDF
{
  private $phrases;

  function __construct()
  {
    $this->phrases = Phrases::getPhreses('ADMIN_OPTION_DOCS');
  }

  public function prepareTitle(array $query): string
  {
    $short = '';
    if ($query['docType'] == IDocuments::tmp_FVS) $short = IDocuments::FVS;
    if ($query['docType'] == IDocuments::tmp_FVK) $short = IDocuments::FVK;
    if ($query['docType'] == IDocuments::WZ_ID) $short = IDocuments::WZ;
    if ($query['docType'] == IDocuments::PZ_ID) $short = IDocuments::PZ;


    $args = array();

    foreach (explode("/", fileRW::readJsonAssoc(CONFIG_FILE)['invoiceFormat']) as $key => $value) {
      if ($value == 'RRRR') $args[] = $query['year'];
      if ($value == 'MM') $args[] = $query['month'];
      if ($value == 'DD') $args[] = $query['day'];
      if ($value == 'NR') $args[] = $query['num'];
    }

    return "<h2 style='text-align:center;'>" .
      $this->phrases['DOCUMENT_NO'] . ' ' .
      $short . ' ' .
      join(" / ", $args) . "</h2>";
  }

  public function prepareDates(string $date1 = '', string $date2 = ''): string
  {
    $data1Text = $this->phrases['DATE_BEGIN'];
    $data2Text = $this->phrases['DATE_END'];

    if (strlen($date2) < 5) $date2 = '........................';
    else {
      $time = strtotime($date2);
      $date2 = date('Y-m-d', $time);
    }

    $time = strtotime($date1);
    $date1 = date('Y-m-d', $time);

    return "<div style='float:right; margin: 10px 5px;'><p>$data1Text: {$date1}</p><p>$data2Text: {$date2}</p></div><br/>";
  }

  public function prepareSeller(): string
  {
    $sellerPhrases = $this->phrases['SELLER'];

    $seller = new Seller();

    $name = $seller->getName();
    $adress = $seller->getAdress();
    $email = $seller->getEmail();

    $text =
      "<p>" . $sellerPhrases['NAME'] . ': ' . $name . "</p>
      <p>" . $sellerPhrases['ADRESS'] . ': ' . $adress . "</p>
      <p>" . $sellerPhrases['EMAIL'] . ': ' . $email . "</p>";

    return "<div style='display:inline-block; '><h5>Sprzedawca</h5>{$text}</div>";
  }

  public function prepareBuyer(int $userID): string
  {
    $buyerPhrases = $this->phrases['BUYER'];

    $seller = new Buyer($userID);

    $name = $seller->getName();
    // $adress = $seller->getAdress();
    $email = $seller->getEmail();

    $text =
      "<p>" . $buyerPhrases['NAME'] . ': ' . $name . "</p>
      <p></p>
    <p>" . $buyerPhrases['EMAIL'] . ': ' . $email . "</p>";

    return "<div style='display:inline-block; float:right;'><h5>Nabywca</h5>{$text}</div>";
  }

  public function priceToText(float $amount, bool $payed = false)
  {
    $numberC = new NumberConverter();
    $priceText = $numberC->priceToText(number_format($amount, 2));

    $text = $payed ? $this->phrases['PAIED'] : $this->phrases['TO_PAY'];

    $priceText = $text . ": " . $priceText;

    return "<h3 style=''>{$priceText}</h3>";
  }
}
