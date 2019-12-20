<?php
namespace Payments;

use Payments\PayU;
use Payments\Gates\IPaymentsGates;
use Models\PaymentPayU;

class FactoryPaymentGate implements IPaymentsGates
{
  function __construct()
  { }

  public function createTransaction(string $IPaymentsGates, PaymentPayU $paymentModel )
  {
    switch ($IPaymentsGates) {
      case IPaymentsGates::PAYU:
        $payU = new PayU();
        return $payU->createTranzaction($paymentModel);
        break;

      default:
        trace(array($IPaymentsGates, $paymentModel ));
        break;
    }
  }

  public function updateTransaction(string $IPaymentsGates, int $orderID)
  {
    switch ($IPaymentsGates) {
      case IPaymentsGates::PAYU:
        $payU = new PayU();
        $payU->updateTransaction($orderID);
        break;

      default:
        trace(array($IPaymentsGates, $orderID));
        break;
    }
  }
}
