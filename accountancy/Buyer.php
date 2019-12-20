<?php

namespace Users;

use DB\DBConnection;
use DB\IDBDataFactor;

class Buyer
{
  private $email;
  private $name;

  function __construct($userID)
  {
    self::getUserData($userID);
  }

  private function getUserData(int $userID)
  {
    $db = new DBConnection();

    $tableUsers = $db->getWPPrefix(). "users";

    $result = $db->getRow("
      sELECT display_name, user_email, id 
        FROM {$tableUsers}  
        WHERE id={$userID}",
      IDBDataFactor::ARRAY_A);


    self::setEmail($result['user_email']);
    self::setName($result['display_name']);
  }

  #region getters
  /**
   * Get the value of email
   */
  public function getEmail(): string
  {
    return $this->email;
  }

  /**
   * Get the value of name
   */
  public function getName(): string
  {
    return $this->name;
  }

  #endregion

  #region setters

  /**
   * Set the value of email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * Set the value of name=
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  #endregion
}
