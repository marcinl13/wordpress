<?php

namespace Roles;

include_once INTERFACE_PATH . 'IRoles.php';

class Roles implements IRoles
{
  function __construct()
  { }

  public static function getRole($id)
  {
    $result = ['id' => null, 'role' => null];

    $result['id'] = isID($id) && (bool) get_user_by('id', $id);

    $result['role'] = $result['id'] == true ? get_object_vars(get_user_by('id', $id))['roles'][0] : null;

    return $result;
  }

  // public static function isAdmin($id) //inactive
  // {
  //   return
  //     isID($id) &&
  //     (bool)get_user_by('id', $id) &&
  //     (bool)get_object_vars(get_user_by('id', $id))['roles'][0] == IRoles::Administrator;
  // }

  public static function isAny($id, $role): bool
  {
    return
      isID($id) &&
      (bool) get_user_by('id', $id) &&
      (bool) get_object_vars(get_user_by('id', $id))['roles'][0] == $role;
  }

  public static function actionFor($userId, $Irole): bool
  {
    $role = self::getRole($userId);

    return $role['id'] && $role['role'] == $Irole;
  }

  public static function isAccount($userId = 0): bool
  {
    return isID($userId) && (bool) get_user_by('id', $userId);
  }
}
