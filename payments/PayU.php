<?php

namespace Payments;

use DB\DBConnection;
use DB\IDBDataFactor;
use Payments\Status\IPaymentsStatus;
use IShortCodes\IShortCodes;
use Models\PaymentPayU;
use Plugin\fileRW;
use Users\Buyer;

class PayU implements IPaymentsStatus
{
  private $payuJSON = array();
  private $accessToken = "";

  private $db;
  private $tableTransactions;

  function __construct()
  {
    $this->db = new DBConnection();

    self::getJSONData();
  }

  private function getTableNames(){
    $noUsed = "";

    $this->db->getTableNames($noUsed,$noUsed, $noUsed, $noUsed, $this->tableTransactions);
  }

  private function getJSONData()
  {
    if (empty($this->payuJSON)) {
      $file = CONFIG_FILE;

      if (!file_exists($file)) return;

      $read = file_get_contents($file);

      $this->payuJSON = get_object_vars(json_decode($read));
    }
  }

  private function getBaarel()
  {
    self::getJSONData();

    $cID = $this->payuJSON['clientID'];
    $cSecret = $this->payuJSON['clientSecret'];
    $url = $this->payuJSON['paymentMode'] == 'sandbox' ?
      $this->payuJSON['payuSandboxUrl'] : $this->payuJSON['payuProductionUrl'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$url}/pl/standard/user/oauth/authorize");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials&client_id={$cID}&client_secret={$cSecret}");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/x-www-form-urlencoded"
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    $accessToken =  get_object_vars(json_decode($response))['access_token'];

    $this->accessToken = $accessToken;
  }

  private function prepareBuyer($buyerID)
  {
    $userData = new Buyer($buyerID);

    return json_encode(array(
      "email" => $userData->getEmail(),
      "phone" => "654111654",
      "firstName" => "John",
      "lastName" => "Doe"
    ), JSON_UNESCAPED_UNICODE);
  }

  private function prepareProducts($productList)
  {
    $tmp = array();

    foreach ($productList as $key => $value) {
      $tmp[$key]['name'] = $value['name'];
      $tmp[$key]['unitPrice'] = intval(floatval($value['price']) * 100);
      $tmp[$key]['quantity'] = $value['quantity'];
    }

    return json_encode($tmp, JSON_UNESCAPED_UNICODE);
  }

  private function prepareTransactionBody(string $description, float $totalPrice, int $orderID, array $productList, int $buyerID)
  {
    if ($this->accessToken == "") self::getBaarel();

    self::getJSONData();

    //add transport price 

    $config = fileRW::readJsonAssoc(CONFIG_FILE);
    $transportPrice = (float) $config['transportPrice'];

    $productList[] = array(
      'name' => 'transport',
      'price' => $transportPrice,
      'quantity' => 1
    );

    $mID = $this->payuJSON['merchantPosId'];
    $notify = home_url() . "/" . IShortCodes::PAYMENT_NOTYFICATION . "?mode=p&o={$orderID}";
    $url = $this->payuJSON['paymentMode'] == 'sandbox' ? $this->payuJSON['payuSandboxUrl'] : $this->payuJSON['payuProductionUrl'];

    $totalPrice = intval($totalPrice * 100) + intval($transportPrice * 100);
    $ip = "127.0.0.1";
    $currency = (string) fileRW::readJsonAssoc(CONFIG_FILE)['currencyCode'];
    $buyer = self::prepareBuyer($buyerID);
    $productList = self::prepareProducts($productList);

    //POST
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$url}/api/v2_1/orders");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
      "notifyUrl": "' . $notify . '",
      "continueUrl": "' . $notify . '",
      "customerIp": "' . $ip . '",
      "merchantPosId": "' . $mID . '",
      "description": "' . $description . '",
      "currencyCode": "' . $currency . '",
      "totalAmount": "' . $totalPrice . '",
      "extOrderId":"' . uniqid('', true) . '",
      "buyer": ' . $buyer . ',
      "products": ' . $productList . '
    }');

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/json",
      "Authorization: Bearer " . $this->accessToken
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    $response = get_object_vars(json_decode($response));


    return array(
      "hashOrder" => $response['orderId'],
      "status" => get_object_vars($response['status'])['statusCode'],
      "redirectUri" => $response['redirectUri']
    );
  }

  private function saveTransaction($orderID, $userID, $payuOrder)
  {
    $tableTransaction = $this->tableTransactions;

    $hashOrder = $payuOrder['hashOrder'];
    $redirect = $payuOrder['redirectUri'];
    $status = IPaymentsStatus::
    DEFAULT;

    $this->db->query("insert INTO {$tableTransaction} (orderID, userID, hashOrder, status, redirect)
        VALUES ('$orderID', '$userID', '$hashOrder', '$status','$redirect')");
  }


  public function getOrder(string $hashOrder)
  {
    if ($this->accessToken == "") self::getBaarel();

    $url = $this->payuJSON['paymentMode'] == 'sandbox' ?
      $this->payuJSON['payuSandboxUrl'] : $this->payuJSON['payuProductionUrl'];


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$url}/api/v2_1/orders/" . $hashOrder);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Authorization: Bearer " . $this->accessToken
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    return get_object_vars(json_decode($response));
  }

  public function createTranzaction(PaymentPayU $paymentModel)
  {
    $description = $paymentModel->getDescription();
    $totalPrice = $paymentModel->getTotalPrice();
    $userID = $paymentModel->getUserId();
    $orderID = $paymentModel->getOrderID();
    $orderProducts = $paymentModel->getOrderProducts();

    $result = self::prepareTransactionBody($description, $totalPrice, $orderID, $orderProducts, $userID);

    self::saveTransaction($orderID, $userID, $result);

    return $result['redirectUri'];
  }

  public function updateTransaction(int $orderID)
  {
    self::getTableNames();

    $tableTransaction = $this->tableTransactions;

    $hashOrder = $this->db->getRow(
      "select hashOrder from {$tableTransaction} WHERE orderID={$orderID}",
      IDBDataFactor::ARRAY_A
    )['hashOrder'];

    $prepare = get_object_vars(self::getOrder($hashOrder)['orders'][0]);
    trace($prepare);

    $status = IPaymentsStatus::COMPLETED;

    if ($prepare['status'] == 'COMPLETED')
      $this->db->query("uPDATE {$tableTransaction} SET status='{$status}' WHERE orderID={$orderID}");
  }

}
