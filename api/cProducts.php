<?php

namespace Rest;

use Accountancy\DOC_PZ;
use http\IHttpStatusCode;
use Magazine\addPZ;
use Models\mMag;
use Models\mProduct;
use Models\v18\mMagazine;
use Rest\rAccess;

class cProducts
{
  private $restAccess;


  function __construct()
  {
    $this->restAccess = new rAccess();
  }

  public function getOne($request)
  {
    $body = $request->get_params();

    $id = $body['id'];
    $token = isset($body['token']) ? $body['token'] : '';

    //grand access only to users
    if (!$this->restAccess->accessNonAdmin($token)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $mProduct = new mProduct();
    $mProduct->setId($id);

    $productsSingle = $mProduct->getOne();

    $result = count($productsSingle) > 0;

    $msg = array(
      "status" => $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => $result ? "OK" : "Forbidden",
      "data" => $productsSingle
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function getMany($request)
  {
    $body = $request->get_params();

    $ids = $body['ids'];
    $token = isset($body['token']) ? $body['token'] : '';

    //grand access only to users
    if (!$this->restAccess->accessNonAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $mProduct = new mProduct();
    $mProduct->setIds($ids);

    $productsMultiple = $mProduct->getMany();

    $msg = array(
      "status" => !empty($productsMultiple) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($productsMultiple) ? "OK" : "Forbidden",
      "data" => $productsMultiple
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  private function searchMag($id, $magList): int
  {
    $count = 0;

    foreach ($magList as $key => $param) {
      if ($param['id'] == $id) $count = (int) $param['curMag'];
    }

    return $count;
  }

  public function getAll($request, &$output = array())
  {
    $body = $request->get_params();
    $token = isset($body['token']) ? $body['token'] : '';

    /*//grand access only to users
      if (!$this->restAccess->accessNonAdmin($token)) {
        return new \WP_REST_Response(array(
          "status" => IHttpStatusCode::Unauthorized,
          "message" => "Unauthorized",
          "data" => array()
        ), IHttpStatusCode::Unauthorized);
    }*/

    $mProduct = new mProduct();
    $output = $mProduct->getAll();

    $magazine = new mMagazine();
    $amount = $magazine->getProductInMagazine2();
    
    // $mMag = new mMag();
    // $amount = $mMag->getProductInMagazine2();

    foreach ($output as $key => $param) {
      $output[$key]['quantity'] = self::searchMag($param['id'], $amount);
    }


    $msg = array(
      "status" => !empty($output) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($output) ? "OK" : "Forbidden",
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  public function setProducts($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $id = (int) $body['id'];
    $token = (string) isset($body['token']) ? $body['token'] : '';
    $nazwa = (string) $body['nazwa'];
    $netto = (float) $body['netto'];
    $brutto = (float) $body['brutto'];
    $id_stawki = (int) $body['id_stawki'];
    $id_kategori = (int) $body['id_kategori'];
    $quantity = isset($body['quantity']) ? (int) $body['quantity'] : 0;
    $status = IHttpStatusCode::Forbidden;
    $message = "Error";
    
    
    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }
    
    $mProduct = new mProduct();
    $mProduct->setId((int) $id);
    $mProduct->setToken($token);
    $mProduct->setName((string) $nazwa);
    $mProduct->setVatID((int) $id_stawki);
    $mProduct->setCategoryId((int) $id_kategori);
    $mProduct->setNetto((float) $netto);
    $mProduct->setBrutto((float) $brutto);
    $mProduct->setImage((string) $body['zdjecie']);
    $mProduct->setDescription((string) $body['opis']);
    $mProduct->setJm((string) $body['unit']);

    
    if (isID($id)>0) {
      $result = $mProduct->update();

      $status = $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden;
      $message = $result ? "Updated" : "Update failed";
    }

    if (!isID($id)) {
      $insertID = 0;
      $newPZ = 0;

      $result = $mProduct->save($insertID, true);

      $pz = new addPZ();
      $pz->addProduct($insertID);
      $pz->addQuantity($quantity);

      $sv = $pz->save($newPZ);

      $docPZ = new DOC_PZ($newPZ);
      $docPZ->save();


      $status = $result ? IHttpStatusCode::Created : IHttpStatusCode::Forbidden;
      $message = $result ? "Created" : "Forbidden";
    }

    self::getAll($request, $list);

    $msg = array(
      "status" => $status,
      "message" => $message,
      "data" => $list //$mProduct->getAll()
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  //delete products
  public function deteleProducts($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $ids = $body['ids'];
    $token = isset($body['token']) ? $body['token'] : '';

    if (!isset($ids) || !isset($token)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Forbidden,
        "message" => "Forbidden"
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

    $mProduct = new mProduct();
    $mProduct->setIds($ids);
    $result = $mProduct->delete();

    $msg = array(
      "status" => $result ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => $result ? "Deleted" : "Forbidden"
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }
}
