<?php

namespace Users;

use Plugin\fileRW;

class Seller
{
  private $name;
  private $adress;
  private $email;
  private $imageLink;

  function __construct()
  {
    self::getData();
  }

  private function getData()
  {
    $file = fileRW::readJsonAssoc(CONFIG_FILE)['SHOP'];

    $this->name = $file['name'];
    $this->adress = $file['adress'];
    $this->email = $file['email'];
    $this->imageLink = $file['logoLink'];
  }

  #region getters

  /**
   * Get the value of name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the value of adress
   */
  public function getAdress()
  {
    return $this->adress;
  }

  /**
   * Get the value of email
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Get the value of imageLink
   */
  public function getImageLink()
  {
    return $this->imageLink;
  }

  #endregion
}
