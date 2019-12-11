<?php

namespace Rest;

use Accountancy\DOC_FVS;
use Accountancy\DOC_PZ;
use Accountancy\DOC_WZ;
use DB\DataRefactor;
use Documents\IDocuments;
use html\BuidTable;
use http\IHttpStatusCode;
use Models\v18\mDocuments2;
use Rest\rAccess;

class cDocs
{
  private $restAccess;

  function __construct()
  {
    $this->restAccess = new rAccess();
  }

  public function getAll($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $token = isset($body['token']) ? $body['token'] : '';

    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $mDoc = new mDocuments2();
    $sv = $mDoc->getAll();

    $msg = array(
      "status" => !empty($sv) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($sv) ? "OK" : "Forbidden",
      "data" => $sv
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }


  public function preview($request)
  {
    $body = $request->get_params();
    $userId = 0;
    $token = isset($body['token']) ? $body['token'] : '';

    //grand access only to admin role
    if (!$this->restAccess->accessAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Unauthorized",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    if ($body['dT'] == IDocuments::tmp_FVS && (int) $body['id'] > 0) {

      $docFVS = new DOC_FVS($body['id'], '');
      $res = $docFVS->getDetails($body['id']);


      $args = json_decode(base64_decode($res['produkty']), true);
      $args = DataRefactor::refactorToFVSTable($args);

      $html = BuidTable::buildTable($args['tbody'], $args['tfoot'], false);
    }

    if ($body['dT'] == IDocuments::WZ_ID && (int) $body['id'] > 0) {

      $docWZ = new DOC_WZ($body['id'], '');
      $res = $docWZ->getDetails($body['id']);


      $args = json_decode(base64_decode($res['produkty']), true);
      $args = DataRefactor::refactorToWZTable($args);

      $html = BuidTable::buildTable($args, "", false);
    }

    if ($body['dT'] == IDocuments::PZ_ID && (int) $body['id'] > 0) {

      $docPZ = new DOC_PZ($body['id']);
      $res = $docPZ->getDetails($body['id']);

      $args = DataRefactor::refactorToPZTable($res);
      $html = BuidTable::buildTable($args, "", false);
    }


    $msg = array(
      "status" => !empty($html) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($html) ? "OK" : "Forbidden",
      "data" => $html
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }
}
