<?php

namespace Rest;

use Documents\Document;
use Documents\IDocuments;
use Documents\Magazine;
use http\IHttpStatusCode;
use Rest\rAccess;
use Models\mOrder;
use Models\PaymentPayU;
use Payments\FactoryPaymentGate;
use Payments\Gates\IPaymentsGates;

class cOrderPrefab
{
  private $restAccess;

  function __construct()
  {
    $this->restAccess = new rAccess();
  }

  private function paymentGate(string $description, float $totalPrice, int $userId, int $orderID, $orderProducts)
  {
    $gate = new FactoryPaymentGate();

    $paymentModel = new PaymentPayU();
    $paymentModel->setAll($description, $totalPrice, $userId, $orderID, $orderProducts);


    return $gate->createTransaction(IPaymentsGates::PAYU, $paymentModel);
  }

  public function getMany($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $ids = $body['ids'];
    $token = isset($body['token']) ? $body['token'] : '';

    /*//grand access only to users
      if (!$this->restAccess->accessNonAdmin($token, $userId)) {
        return new \WP_REST_Response(array(
          "status" => IHttpStatusCode::Unauthorized,
          "message" => "Unauthorized",
          "data" => array()
        ), IHttpStatusCode::Unauthorized);
      }
    */

    $model = new mOrder();
    $model->setUserID($userId);
    $model->setIds($ids);
    $output = $model->getMany();

    $msg = array(
      "status" => !empty($output) ? IHttpStatusCode::OK : IHttpStatusCode::Forbidden,
      "message" => !empty($output) ? "OK" : "Forbidden",
      "data" => $output
    );

    return new \WP_REST_Response($msg, $msg['status']);
  }

  private function TakeProductsFromMagazine(int $orderID, array $products)
  {
    $mag = new Magazine();

    foreach ($products as $key => $value) {
      if (!empty($value['id'])  && !empty($value['quantity'])) {
        $mag->TakeFromMag((int) $orderID, (int) $value['id'], (int) $value['quantity']);
      }
    }

    $document = new Document();
    $document->create($orderID, IDocuments::WZ_ID, date('Y-m-d'), 'Y-m-d');
  }

  public function save($request)
  {
    $body = $request->get_params();

    $userId = 0;
    $insertId = 0;
    $redirect = null;
    $order = $body['order'];
    $token = isset($body['token']) ? $body['token'] : '';
    $sumPrice =  $body['sumPrice'];
    $status = IHttpStatusCode::Forbidden;


    //grand access only to admin role
    if (!$this->restAccess->accessNonAdmin($token, $userId)) {
      return new \WP_REST_Response(array(
        "status" => IHttpStatusCode::Unauthorized,
        "message" => "Zaloguj się",
        "data" => array()
      ), IHttpStatusCode::Unauthorized);
    }

    $model = new mOrder();
    $model->setOrderDetails(base64_encode(json_encode($order)));
    $model->setPrice((float) $sumPrice);
    $model->setUserID($userId);

    $status = $model->save($insertId);


    if ($status) {
      $orderID = $insertId;

      self::TakeProductsFromMagazine($insertId, $order);

      $redirect = self::paymentGate("Zapłata za zamowienie nr {$orderID}", floatval($sumPrice), (int) $userId, (int) $orderID, $order);
    }

    $msg = array(
      "status" => $status ? IHttpStatusCode::Created : IHttpStatusCode::Forbidden,
      "message" => $status ? "Created" : "Forbidden",
      "redirect" => $redirect
    );

    if ($redirect == null) {
      $msg['status'] = IHttpStatusCode::No_Content;
      $msg["message"] = IHttpStatusCode::No_Content;
    }

    return new \WP_REST_Response($msg, $msg['status']);
  }
}
