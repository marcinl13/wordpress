<?php

use DB\DataRefactor;
use html\fillHTML;
use Payments\FactoryPaymentGate;
use Payments\Gates\IPaymentsGates;
use IShortCodes\IShortCodes;
use Hooks\CustomHooks;
use html\BuidTable;
use Models\mOrder;

//

class Shortcodes implements IShortCodes
{

  function __construct()
  {
    self::init();
  }

  public static function init()
  {
    add_shortcode(IShortCodes::DUMP, array(__class__, 'cartPage'));
    add_shortcode(IShortCodes::SHOP, array(__class__, 'shopPage'));
    add_shortcode(IShortCodes::USER_ORDERS, array(__class__, 'userOrdersPage'));
    add_shortcode(IShortCodes::PAYMENT_NOTYFICATION, array(__class__, 'paymentNotyfication'));
  }


  public function shopPage()
  {
    CustomHooks::AddHookLanguage("ADMIN_OPTION_PRODUCTS");

    $input = new fillHTML();
    $input->inputMe('usr-products');
  }


  public static function cartPage()
  {
    CustomHooks::AddHookLanguage("ADMIN_OPTION_PRODUCTS");
    CustomHooks::AddHookTransportPrice();
    CustomHooks::AddHookCurrencyCode();

    $input = new fillHTML();
    $input->inputMe('usr-koszyk');
  }

  public static function userOrdersPage()
  {
    if (get_current_user_id() > 0 && is_admin() == false) {
      CustomHooks::AddHookLanguage("ADMIN_OPTION_ORDERS");
      CustomHooks::AddHookStatuses();
      CustomHooks::AddHookCurrencyCode();

      $input = new fillHTML();
      $input->inputMe('usr-orders');
    }

    CustomHooks::AddHookNoAccess();
  }

  public static function paymentNotyfication()
  {
    $gate = new FactoryPaymentGate();
    // $gate->createTransaction(IPaymentsGates::PAYPAL, "as", 12,1,1, array());

    if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'p') {
      $gate->updateTransaction(IPaymentsGates::PAYU, $_REQUEST['o']);
    }

    if (is_user_logged_in() && get_current_user_id() > 0) {
      $model = new mOrder();
      $data = $model->getProductListFromOrder((int) $_REQUEST['o'], get_current_user_id());
      $args = DataRefactor::refactorCartTable($data);

      echo '</article>' . BuidTable::buildTable($args['tbody'], $args['tfoot']);
    } else {
      CustomHooks::AddHookNoAccess();
    }
  }
}
