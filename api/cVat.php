<?php

namespace Rest;

use http\IHttpStatusCode;
use Rest\rAccess;
use Models\mVat;

class cVat
{
  private $restAccess;

  function __construct()
  {
    $this->restAccess = new rAccess();
  }

  public function getOne($request)
  {
    // $body = $request->get_params();

    // $userId = 0;
    // $id =  $body['id'];
    // $token = $body['token'];

    // //grand access only to users
    // if (!$this->restAccess->accessNonAdmin($token, $userId)) {
    //   return new \WP_REST_Response(array(
    //     "status" => IHttpStatusCode::Unauthorized,
    //     "message" => "Unauthorized",
    //     "data" => array()
    //   ), IHttpStatusCode::Unauthorized);
    // }

    // $model = new mVat();
    // $model->setID($id);
    // $output = $model->getOne();

    // $result = count($output) > 0;

    // $msg = array(
    //   "status" => $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
    //   "message" => $result ? "OK" : "Forbidden",
    //   "data" => $output
    // );

    // return new \WP_REST_Response($msg, $msg['status']);
  }

  public function getAll($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $token = isset($body['token']) ? $body['token'] : '';

    //grand access only to users
    if (!$this->restAccess->accessNonAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $model = new mVat();
    $output = $model->getAll();

    $msg = array(
      "status" => !empty($output) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($output) ? "OK" : "Forbidden",
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function setVat($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $id = $body['id'];
    $name = $body['name'];
    $token = isset($body['token']) ? $body['token'] : '';
    $value = $body['value'];
    $status = IHttpStatusCode::Forbidden;


    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $model = new mVat();
    $model->setId((int) $id);
    $model->setName($name);
    $model->setValue((float) $value);

    if (isID($id) && $id > 0) {
      $status = $model->update() ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden;

      $message = $status == IHttpStatusCode::OK ? "Updated" : "Forbidden";
    } else {
      $status = $model->save() ? IHttpStatusCode::Created : IHttpStatusCode::Forbidden;

      $message = $status == IHttpStatusCode::OK ? "Created" : "Forbidden";
    }

    $msg = array(
      "status" => $status,
      "message" => $message,
      "data" => $model->getAll()
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  //delete Category
  public function deleteVat($request) //v2
  {
    $body = $request->get_params();

    $userId = 0;
    $id = $body['id'];
    $token = isset($body['token']) ? $body['token'] : '';

    if (!isset($id) || !isset($token)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Forbidden,
        "message" => "Forbidden1"
      ), IHttpStatusCode::Forbidden);
    }

    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $model = new mVat();
    $model->setID((int)$id);

    $result = $model->delete();

    $msg = array(
      "status" => $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => $result ? "Deleted" : "Forbidden"
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }
}
