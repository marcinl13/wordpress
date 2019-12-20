<?php

namespace Rest;

use http\IHttpStatusCode;
use Models\mCategory;
use Rest\rAccess;

class cCategory
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
    $id =  $body['id'];
    $token = isset($body['token']) ? $body['token'] : '';

    //grand access only to users
    if (!$this->restAccess->accessNonAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $model = new mCategory();
    $model->setID($id);
    $output = $model->getOne();

    $result = count($output) > 0;

    $msg = array(
      "status" => $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => $result ? "OK" : "Forbidden",
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function getAll($request, &$output = array())
  {
    $body = $request->get_params();

    $userId = 0;
    $token = isset($body['token']) ? $body['token'] : '';

    /*    //grand access only to users
    if (!$this->restAccess->accessNonAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }
*/

    $model = new mCategory();
    $output = $model->getAll();

    $msg = array(
      "status" => !empty($output) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($output) ? "OK" : "Forbidden",
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function setCategory($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $id = (int) $body['id'];
    $name = (string) $body['name'];
    $token = (string) $body['token'];
    $status = IHttpStatusCode::Forbidden;

    //validate
    if (mb_strlen($name) == 0) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Not_Found,
        "message" => "Uzupełnij pola"
      ), IHttpStatusCode::Not_Found);
    }

    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $model = new mCategory();
    $model->setName((string) $name);
    $model->setID((int) $id);

    if (isID($id)>0) {
      $result = $model->update();

      $status = $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden;
      $message = $result ? "Updated" : "Update failed";
    }

    if (!isID($id)) {
      $result = $model->save();

      $status = $result ? IHttpStatusCode::Created : IHttpStatusCode::Forbidden;
      $message = $result ? "Created" : "Forbidden";
    }
    
    $output = $model->getAll();

    $msg = array(
      "status" => $status,
      "message" => $message,
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  //delete Category
  public function deleteCategory($request) //v2
  {
    $body = $request->get_params();

    $userId = 0;
    $id = $body['id'];
    $token = $body['token'];

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

    $model = new mCategory();
    $model->setID($id);

    $result = $model->delete();

    $msg = array(
      "status" => $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => $result ? "Deleted" : "Forbidden"
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }
}
