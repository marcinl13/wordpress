<?php
namespace Payments\Gates;

use Models\PaymentPayU;

interface IPaymentsGates
{
  const PAYU = 'PAYU';
  const PAYPAL = 'PAYPAL'; //for tests doesnt exist jet

  public function createTransaction(string $IPaymentsGates, PaymentPayU $paymentOrder );
  
  public function updateTransaction(string $IPaymentsGates, int $orderID);
}
