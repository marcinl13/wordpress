<?php

namespace Rest;

use Accountancy\DOC_PZ;
use Documents\IDocuments;
use http\IHttpStatusCode;
use Magazine\addPZ;
use Models\mMag;
use Rest\rAccess;

class cMagazine
{
  private $restAccess;

  function __construct()
  {
    $this->restAccess = new rAccess();
  }

  public function addTo($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $token = isset($body['token']) ? $body['token'] : '';
    $productId = $body['pID'];
    $quantity = $body['quantity'];
    $sv = false;

    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }



    if (isID($quantity) && $quantity > 0) {
      $insertedPZ = 0;

      $pz = new addPZ();
      $pz->addProduct((int) $productId);
      $pz->addQuantity((int) $quantity);
      $sv = $pz->save($insertedPZ);

      $docPZ = new DOC_PZ($insertedPZ);
      $docPZ->save();
    }


    $msg = array(
      "status" => $sv ? IHttpStatusCode::Created : IHttpStatusCode::Forbidden,
      "message" => $sv ? "Dodano" : "Forbidden",
      "data" => array()
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function takeFrom($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $token = isset($body['token']) ? $body['token'] : '';
    $productId = $body['pID'];
    $orderID = $body['orderID'];
    $quantity = $body['quantity'];

    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $mMag = new mMag();
    $mMag->setDocType(IDocuments::WZ_ID);
    $mMag->setOrderID($orderID);
    $mMag->setProductID($productId);
    $mMag->setQuantity($quantity);
    $sv = $mMag->save();

    $msg = array(
      "status" => $sv ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => $sv ? "OK" : "Forbidden",
      "data" => array()
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }
}
