<?php

namespace Models;

class PaymentPayU
{

  private $description;
  private $totalPrice;
  private $userID;
  private $orderID;
  private $orderProducts;

  public function __construct()
  { }

  public function getDescription():string
  {
    return $this->description;
  }

  public function setDescription(string $description)
  {
    $this->description = $description;
  }

  public function getTotalPrice():float
  {
    return floatval($this->totalPrice);
  }

  public function setTotalPrice(float $totalPrice)
  {
    $this->totalPrice = $totalPrice;
  }
  
  public function getUserId():int
  {
    return $this->userID;
  }

  public function setUserId(int $userId)
  {
    $this->userID = $userId;
  }
  
  public function getOrderID():int
  {
    return intval($this->orderID);
  }

  public function setOrderID(int $orderID)
  {
    $this->orderID = $orderID;
  }
  
  public function getOrderProducts()
  {
    return $this->orderProducts;
  }

  public function setOrderProducts($orderProducts)
  {
    $this->orderProducts = $orderProducts;
  }

  public function setAll(string $description, float $totalPrice, int $userID, int $orderID, $orderProducts)
  {
    $this->description = $description;
    $this->totalPrice = floatval($totalPrice);
    $this->userID = $userID;
    $this->orderID = $orderID;
    $this->orderProducts = $orderProducts;
  }
}
