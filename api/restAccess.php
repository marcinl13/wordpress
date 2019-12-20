<?php

namespace Rest;

use Token\Token;
use Roles\Roles;
use Roles\IRoles;

class rAccess
{
  private function decodeToken(string $token = '', int &$userId = 0, bool &$tokenLive = false)
  {
    $decode = new Token();
    $decode = $decode->DecodeToken($token);

    $userId = $decode['userId'];
    $tokenLive = (bool) $decode['tokenAlive'];
  }

  public function accessAdmin(string $token = '', int &$userId = 0, bool $checkAlive = false): bool
  {
    $grandAccess = false;
    $tokenLive = false;

    self::decodeToken($token, $userId, $tokenLive);

    $grandAccess = Roles::isAccount($userId) && Roles::actionFor($userId, IRoles::Administrator);

    if ($checkAlive === true) {
      $grandAccess = $grandAccess && $tokenLive;
    }

    $userId = $userId;

    return $grandAccess;
  }

  public function accessNonAdmin(string $token = '', int &$userId = 0, bool $checkAlive = false): bool
  {
    $grandAccess = false;
    $tokenLive = false;

    self::decodeToken($token, $userId, $tokenLive);
    $userId = $userId;

    $grandAccess = Roles::isAccount($userId);

    if ($checkAlive === true) {
      $grandAccess = $grandAccess && $tokenLive;
    }


    return $grandAccess;
  }
}
