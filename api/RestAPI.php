<?php
use Rest\cCart;
use Rest\cToken;
use Rest\cOrder;
use Rest\cVat;
use Rest\cProducts;
use Rest\cCategory;
use Rest\cDocs;
use Rest\cMagazine;

class RestApi
{
  public function __construct()
  { }

  public static function  init()
  {
    if (!function_exists('register_rest_route')) {
      // The REST API wasn't integrated into core until 4.4, and we support 4.0+ (for now).
      return false;
    }

    self::routeProducts_V2();
    self::routeCategory_V2();
    self::routeOrder_V2();
    self::routeCart_V2();
    self::routeVat_V2();
    self::routeToken_V2();
    self::routeMagazine();
    self::routeDocs();
  }

  /**
   * v2
   */
  public function routeProducts_V2()
  {
    register_rest_route(
      'shop/v2',
      '/products',
      array(
        'method' => 'GET',
        'callback' => function ($request) {
          $body = $request->get_params();

          $cProduct2 = new cProducts();

          if (isset($body['id'])) {
            return $cProduct2->getOne($request);
          } else if (isset($body['ids'])) {
            return $cProduct2->getMany($request);
          } else {
            return $cProduct2->getAll($request);
          }
        }
      )
    );

    register_rest_route('shop/v2', '/products', array(
      'methods' => 'POST',
      'callback' => function ($request) {
        $body = $request->get_params();

        $cProduct = new cProducts();

        return $cProduct->setProducts($request);
      }
    ));

    register_rest_route('shop/v2', '/products', array(
      'methods' => 'DELETE',
      'callback' => function ($request) {
        $body = $request->get_params();

        

        $cProduct = new cProducts();

        return $cProduct->deteleProducts($request);
      }
    ));
  }

  public function routeCategory_V2()
  {
    register_rest_route(
      'shop/v2',
      '/category/',
      array(
        'method' => 'GET',
        'callback' => function ($request) {
          $body = $request->get_params();

          $controlCategory = new cCategory();

          if (isset($body['id'])) {
            return $controlCategory->getOne($request);
          } else {
            return $controlCategory->getAll($request);
          }
        }
      )
    );

    register_rest_route('shop/v2', '/category', array(
      'methods' => 'POST',
      'callback' => function ($request) {
        $body = $request->get_params();

        

        $controlCategory = new cCategory();

        return $controlCategory->setCategory($request);
      }
    ));

    register_rest_route('shop/v2', '/category', array(
      'methods' => 'DELETE',
      'callback' => function ($request) {
        $body = $request->get_params();

        

        $controlCategory = new cCategory();
        return $controlCategory->deleteCategory($request);
      }
    ));
  }

  public function routeOrder_V2() //v2
  {
    register_rest_route(
      'shop/v2',
      '/orders',
      array(
        'method' => 'GET',
        'callback' => function ($request) {
          $body = $request->get_params();          

          $controlOrder = new cOrder();

          return $controlOrder->getAll($request); //returns for getOne too
        }
      )
    );

    register_rest_route('shop/v2', '/orders', array(
      'methods' => 'POST',
      'callback' => function ($request) {
        $body = $request->get_params();

        

        $controlOrder = new cOrder();
        return $controlOrder->setOrders($request);
      }
    ));
  }

  public function routeCart_V2() //2
  {
    #region old
    // register_rest_route(
    //   'shop/v2',
    //   '/dump/',
    //   array(
    //     'method' => 'GET',
    //     'callback' => function ($request) {
    //       $cPrepareOrder = new cOrderPrefab();

    //       return $cPrepareOrder->getMany($request);
    //     }
    //   )
    // );

    // register_rest_route('shop/v2', '/dump', array(
    //   'methods' => 'POST',
    //   'callback' => function ($request) { 
    //     $cPrepareOrder = new cOrderPrefab();

    //     return $cPrepareOrder->save($request);
    //   }
    // ));
    #endregion
    
    //cart
    register_rest_route(
      'shop/v2',
      '/cart/',
      array(
        'method' => 'GET',
        'callback' => function ($request) {
          $cPrepareOrder = new cCart();

          return $cPrepareOrder->getMany($request);
        }
      )
    );

    register_rest_route('shop/v2', '/cart', array(
      'methods' => 'POST',
      'callback' => function ($request) { 
        $cPrepareOrder = new cCart();

        return $cPrepareOrder->save($request);
      }
    ));
  }

  public function routeVat_V2() 
  {
    register_rest_route(
      'shop/v2',
      '/vat',
      array(
        'method' => 'GET',
        'callback' => function ($request) {
          $body = $request->get_params();

          $controlerVat = new cVat();
         
          return $controlerVat->getAll($request);
        }
      )
    );

    register_rest_route('shop/v2', '/vat', array(
      'methods' => 'POST',
      'callback' => function ($request) {
        $body = $request->get_params();

        $controlerVat = new cVat();
        return $controlerVat->setVat($request);
      }
    ));

    register_rest_route('shop/v2', '/vat', array(
      'methods' => 'DELETE',
      'callback' => function ($request) {
        $body = $request->get_params();


        $controlerVat = new cVat();
        return $controlerVat->deleteVat($request);
      }
    ));
  }

  public function routeToken_V2()
  {
    register_rest_route(
      'shop/v2',
      '/token',
      array(
        'methods' => 'POST',
        'callback' => function ($request) {

          $token = new cToken();
          return $token->CreateToken($request);
        }
      )
    );
  }

  public function routeMagazine()
  {
    register_rest_route(
      'shop/v2',
      '/mag',
      array(
        'methods' => 'POST',
        'callback' => function ($request) {
          $cMagazine = new cMagazine();
          return $cMagazine->addTo($request);
        }
      )
    );
  }

  public function routeDocs()
  {    
    register_rest_route(
      'shop/v2',
      '/docs',
      array(
        'method' => 'GET',
        'callback' => function ($request) {
          $cDocs = new cDocs();
          return $cDocs->getAll($request);
        }
      )
    );
    register_rest_route(
      'shop/v2',
      '/docs',
      array(
        'methods' => 'POST',
        'callback' => function ($request) {
          $cDocs = new cDocs();
          return $cDocs->preview($request);
        }
      )
    );
  }
}
