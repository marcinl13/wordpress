<?php

namespace Plugin;

class fileRW
{
  public static function create(string $fileName)
  {
    if ($fileName != CONFIG_FILE) {
      file_put_contents($fileName, json_encode(array()));
    }

    $data = array(
      "paymentMode" => "sandbox",
      "payuSandboxUrl" => "https://secure.snd.payu.com",
      "payuProductionUrl" => "https://secure.payu.com",
      "merchantPosId" => "",
      "merchantSecond" => "",
      "clientID" => "",
      "clientSecret" => "",
      "currencyCode" => "PLN",
      "invoiceAutoCreate" => "1",
      "invoiceFormat" => "NR/DD/MM/RRRR",
      "html2pdfrocket" => "",
      "lang" => "PL",
      "dbv" => "18",
      "transportPrice" => "0.00",
      "dayCancel" => 7,
      "SHOP" => array("name" => "", "adress" => "", "email" => "", "logoLink" => "")
    );

    file_put_contents($fileName, json_encode($data));
  }

  public static function readFile(string $file)
  {
    if (!file_exists($file)) self::create($file);

    return file_get_contents($file);
  }

  public static function readJson(string $file)
  {
    if (!file_exists($file)) self::create($file);

    $read = self::readFile($file);

    return get_object_vars(json_decode($read));
  }

  public static function readJsonAssoc(string $file)
  {
    if (!file_exists($file)) self::create($file);

    $read = self::readFile($file);

    return json_decode($read, true);
  }

  public static function writeJson(string $file, array $toJson)
  {
    if (!file_exists($file)) self::create($file);

    file_put_contents($file, json_encode($toJson));
  }
}
