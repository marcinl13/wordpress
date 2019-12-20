<?php

namespace Rest;

use http\IHttpStatusCode;
use Rest\rAccess;
use Models\mOrder;

class cOrder
{
  private $restAccess;

  function __construct()
  {
    $this->restAccess = new rAccess();
  }

  public function getOne($request)
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

    $model = new mOrder();
    $model->setUserID($userId);
    $output = $model->getOne();

    $msg = array(
      "status" => !empty($output) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($output) ? "OK" : "Forbidden",
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function getAll($request, &$output = array())
  {
    $body = $request->get_params();

    $userId = 0;
    $token = isset($body['token']) ? $body['token'] : '';
    $output= array();

    //grand access only to users
    if (!$this->restAccess->accessNonAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $model = new mOrder();
    $model->automaticCancelOrders();

    if ($this->restAccess->accessAdmin($token, $userId)) {
      $output = $model->getAll();
    }
    if(!$this->restAccess->accessAdmin($token, $userId)){
      $model->setUserID($userId);
      $output = $model->getOne();
    }

    $msg = array(
      "status" => !empty($output) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($output) ? "OK" : "Forbidden",
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function setOrders($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $id = $body['id'];
    $name = $body['name'];
    $token = isset($body['token']) ? $body['token'] : '';
    $statusId = $body['id_statusu'];
    $status = IHttpStatusCode::Forbidden;

    // //validate
    // if (mb_strlen($name) == 0) {
    //   return new \WP_REST_Response(array(
    //     "status" => IHttpStatusCode::Not_Found,
    //     "message" => "Uzupełnij pola"
    //   ), IHttpStatusCode::Not_Found);
    // }

    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }


    $model = new mOrder();
    $model->setId($id);
    $model->setStatusID($statusId);
    $model->setOrderDate(date("Y-m-d H:i:s"));

    $status = $model->update();

    $msg = array(
      "status" => $status ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => $status ? "Updated" : "Forbidden",
      "data" => $model->getAll()
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }
}
