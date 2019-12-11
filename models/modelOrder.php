<?php

namespace Models;

use DB\DBConnection;
use DB\IDBDataFactor;
use Plugin\fileRW;

class mOrder
{
  private $id;
  private $ids;
  private $userID;
  private $statusID;
  private $orderDate;
  private $realizeDate;
  private $price;
  private $orderDetails;
  private $token;

  private $dbConnection;

  private $tableOrders = '';
  private $tableUsers = '';
  private $tableTransaction = '';
  private $tableDocuments = '';
  private $tableProducts = '';
  private $tableVat = '';

  public function __construct()
  {    
    $this->dbConnection = new DBConnection();

    self::setTableNames();
  }

#region getters/setters
  /**
   * Get the value of id
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Set the value of ids
   */
  public function setIds(string $ids = '')
  {
    $this->ids = $ids;
  }
  /**
   * Get the value of ids
   */
  public function getIds(): string
  {
    return $this->ids;
  }

  /**
   * Set the value of id
   */
  public function setId(int $id = 0)
  {
    $this->id = $id;
  }

  /**
   * Get the value of userID
   */
  public function getUserID(): int
  {
    return $this->userID;
  }

  /**
   * Set the value of userID
   */
  public function setUserID(int $userID = 0)
  {
    $this->userID = $userID;
  }

  /**
   * Get the value of statusID
   */
  public function getStatusID(): int
  {
    return $this->statusID;
  }

  /**
   * Set the value of statusID
   */
  public function setStatusID(int $statusID = 0)
  {
    $this->statusID = $statusID;
  }

  /**
   * Get the value of orderDate
   */
  public function getOrderDate(): string
  {
    return $this->orderDate;
  }

  /**
   * Set the value of orderDate
   */
  public function setOrderDate(string $orderDate = "")
  {
    $this->orderDate = $orderDate;
  }

  /**
   * Get the value of realizeDate
   */
  public function getRealizeDate(): string
  {
    return $this->realizeDate;
  }

  /**
   * Set the value of realizeDate
   */
  public function setRealizeDate(string $realizeDate = "")
  {
    $this->realizeDate = $realizeDate;
  }

  /**
   * Get the value of price
   */
  public function getPrice(): float
  {
    return $this->price;
  }

  /**
   * Set the value of price
   */
  public function setPrice(float $price = 0.00)
  {
    $this->price = $price;
  }

  /**
   * Get the value of orderDetails
   */
  public function getOrderDetails(): string
  {
    return $this->orderDetails;
  }

  /**
   * Set the value of orderDetails
   */
  public function setOrderDetails(string $orderDetails = "")
  {
    $this->orderDetails = $orderDetails;
  }

  /**
   * Get the value of token
   */
  public function getToken(): string
  {
    return $this->token;
  }

  /**
   * Get the value of transport price
   */
  public function getTransportPrice(): float
  {
    return (float) fileRW::readJsonAssoc(CONFIG_FILE)['transportPrice'];
  }

  /**
   * Set the value of token
   */
  public function setToken(string $token = "")
  {
    $this->token = $token;
  }

#endregion
  /** */

  private function setTableNames() //
  {
    $products="";
    $taxes="";
    $transaction="";
    $order="";
    $documents="";
    $user="";

    $this->dbConnection->getTableNames($products,  $categories, $taxes, $order, $transaction, $magazine, $documents, $user);
    
    $this->tableProducts = $products;
    $this->tableVat = $taxes;
    $this->tableTransaction = $transaction;
    $this->tableOrders = $order;
    $this->tableDocuments = $documents;
    $this->tableUsers = $user;
  }

  public function getAll(): array //
  {
    $args = $this->dbConnection->getResults(
      "select o.*,(o.price+o.priceTransport) as total,  u.user_nicename as user, t.status as completed, IF(d.id >1 ,1,0) as doc
      FROM {$this->tableOrders} o
      LEFT JOIN {$this->tableUsers} u ON o.id_uzytkownika = u.ID
      LEFT JOIN {$this->tableTransaction} t ON t.orderID = o.ID
      LEFT JOIN {$this->tableDocuments} d ON d.orderId = o.ID
      GROUP BY o.id 
      ORDER BY o.id DESC", 
      IDBDataFactor::ARRAY_A
    );

    return (array) $args;
  }

  public function getOne(): array //
  {
    $args = $this->dbConnection->getResults(
      "select o.id_statusu,o.data_zamowienia,o.data_realizacji,o.price,o.priceTransport,(o.price+o.priceTransport)as total, o.produkty, t.status as completed, if(t.status=0, t.redirect, 0 ) as redirect
      FROM {$this->tableOrders} o
      JOIN {$this->tableTransaction} t ON t.orderID = o.ID
      WHERE o.id_uzytkownika={$this->userID} 
      ORDER by o.id desc", 
      IDBDataFactor::ARRAY_A
    );

    return (array) $args;
  }

  public function getProductListFromOrder(int $orderID, int $userID): array
  {
    $args = $this->dbConnection->getRow("
      select produkty, priceTransport 
      from {$this->tableOrders} 
      WHERE id={$orderID} and id_uzytkownika={$userID} 
      LIMIT 1", 
      IDBDataFactor::ARRAY_A
    );

    $args2 = json_decode(base64_decode($args['produkty']), true);

    return (array) array($args2, $args['priceTransport']);
  }

  public function getMany(): array //
  {
    $ids = self::getIds();
    $args = $this->dbConnection->getResults(
      "select 
          p.id, p.nazwa, p.jm, p.netto, ROUND(p.netto *(1+ sv.stawka),2) as price , (1) as quantity 
        FROM {$this->tableProducts} p 
        INNER JOIN {$this->tableVat} sv ON p.id_stawki = sv.id 
        WHERE p.id IN({$ids})",
      IDBDataFactor::ARRAY_A
    );

    $data = array_count_values(explode(',', $this->ids));

    foreach ($args as $key => $value) {
      $args[$key]['quantity'] = $data[$value['id']];
    }

    return (array) $args;
  }

  public function getOrderData(int $orderID)
  {
    $args = $this->dbConnection->getResults(
      "select o.price, o.priceTransport,(o.price+o.priceTransport) as total, o.produkty
      FROM {$this->tableOrders} o
      JOIN {$this->tableTransaction} t ON t.orderID = o.ID
      WHERE o.id={$orderID} 
      ORDER by o.id desc",
      IDBDataFactor::ARRAY_A
    );

    return (array) $args;
  }

  public function update(): bool //
  {
    $status = $this->dbConnection->query("update {$this->tableOrders} SET id_statusu={$this->statusID}, data_realizacji='{$this->orderDate}'
    WHERE id={$this->id}");

    return (bool) $status;
  }

  public function automaticCancelOrders(): bool //
  {
    $days = (int) fileRW::readJsonAssoc(CONFIG_FILE)['dayCancel'];

    $status = $this->dbConnection->query("update {$this->tableOrders} SET id_statusu = 3 WHERE DATEDIFF(CURDATE(),data_zamowienia)>{$days} AND id_statusu = 1");

    return (bool) $status;
  }

  public function delete(): bool //
  {
    $status =  false;

    return (bool) $status;
  }

  public function save(int &$insertID = 0): bool //
  {
    $price = self::getPrice();
    $priceTransport = self::getTransportPrice();
    $total = (float) ($price + $priceTransport);

    $status = $this->dbConnection->insert("insert INTO {$this->tableOrders} (id_uzytkownika, price, priceTransport, total, produkty)
    VALUES ('{$this->userID}', '{$price}', '{$priceTransport}', '{$total}', '{$this->orderDetails}')", $insertID);

    return (bool) $status;
  }
}
