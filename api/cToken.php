<?php
namespace Rest;

use http\IHttpStatusCode;
use Roles\Roles;

class cToken
{
  private $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b24';
  
  function __construct()
  { }

  public function jwtDecode($login)
  {
    $explode = explode(' ; ', $login);
    $details = explode('/', $explode[0]);
    $uniqid = substr($explode[1], 0, $details[0]);
    $userId = substr($explode[1], $details[0]);

    return array(
      "uniqid" => $uniqid,
      "userId" => (int)$userId
    );
  }

  public function decode($res)
  {
    $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b24';
    $payload = \JWT::decode($res, $serverKey, array('HS256'));
    return self::jwtDecode(get_object_vars($payload)['userId']);
  }

  function generate($response)
  {
    $uid = \uniqid();
    $id = $_SESSION['your_current_user_id'];

    $msg = array();
    $decode = array();
    $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b24';
    $login = strlen($uid) . "/" . strlen($id) . " ; " . $uid . $id;

    if (isset($response['jwt'])) {
      $payload = \JWT::decode($response['jwt'], $serverKey, array('HS256'));

      $msg = array(
        "userID" => self::loginDecode(get_object_vars($payload)['userId'])
      );
    } else {
      if (isset($response['login']))
        $decode =  self::loginDecode($response['login']);

      $payloadArray = array();
      $payloadArray['userId'] = $login;
      
      if (isset($nbf)) {
        $payloadArray['nbf'] = $nbf;
      }
      if (isset($exp)) {
        $payloadArray['exp'] = $exp;
      }

      $msg = array(
        "status" => IHttpStatusCode::OK,
        "uid" => $uid,
        "id" => $id,
        "login" => $login,
        "decode" => $decode,
        "jwt" => \JWT::encode($payloadArray, $this->serverKey)
      );
    }

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function CreateToken($request)
  {
    $uid = \uniqid();
    $id = $_SESSION['your_current_user_id'];

    $request = $request->get_params();
    $reqID = isset($request['id']) ? $request['id'] : "";
    $reqAPIKEY = isset($request['APIKEY']) ? $request['APIKEY'] : "";

    if (Roles::isAccount($reqID)) {
      $id = $reqID;
    }

    $code = strlen($uid) . "." . strlen($id);
    $login = "{$code}.{$uid}{$id}";
    $tokenCreatedTime = \time();
    $tokenLiveTime = \time() + (24 * 60 * 60); //live time 24h

    $merged = array(
      't' => $login,
      'c' => $tokenCreatedTime,
      'l' => $tokenLiveTime,
      'i' => $id
    );

    $msg = array(
      "merget" => $merged,
      "status" => IHttpStatusCode::Created,
      "jwt" => \JWT::encode($merged, $this->serverKey)
    );

    return new \WP_REST_Response($msg, IHttpStatusCode::Created);
  }
}
