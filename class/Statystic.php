<?php

namespace Statystic;

use Hooks\CustomHooks;
use Models\mProduct;

class Statystic
{

  function __construct()
  { }

  public function buildPage()
  {
    CustomHooks::AddHookLanguage('ADMIN_STATYSTIC');

    echo "<div>";

    self::drawChart01();
    self::drawChart02();
    self::drawChart03();
    self::drawChart04();
    self::drawLastProduct(6);
    echo "</div>";
  }

  //charts
  public function drawLastProduct(int $count = 5)
  {
    $prod = new mProduct();
    $lastAddedProducts = $prod->getLastAddedProducts($count);

    $table = "<table class='table table-sm'> <thead> <th>Lp</th><th>nazwa</th> </thead> <tbody>";
    foreach ($lastAddedProducts as $key => $value) {
      $table .= "<tr><td>" . ($key + 1) . "</td><td>" . $value['name'] . "</td></tr>";
    }
    $table .= "</tbody> </table>";

    echo "
      <div class=''>        
        <div class='card border border-primary'>
          <h3 class='card-title mb-2 text-center'>ostatnio dodane produkty</h3>
          {$table}
        </div>
      </div>";
  }

  public function drawChart01()
  {
    CustomHooks::AddHookChart01();

    echo "<div class='chartBox'>
      <canvas class='customChart' id='myChart01' width='400' height='200' style=''></canvas>
    </div>";
  }

  public function drawChart02()
  {
    CustomHooks::AddHookChart02();

    echo "<div class='chartBox'>
      <canvas class='customChart' id='myChart02' width='400' height='200' style=''></canvas>
      </div>";
  }

  public function drawChart03()
  {
    CustomHooks::AddHookChart03();

    echo "<div class='chartBox'>
      <canvas class='customChart' id='myChart3' width='400' height='200' style=''></canvas>
    </div>";
  }

  public function drawChart04()
  {
    CustomHooks::AddHookChart04();

    echo "<div class='chartBox'>
      <canvas class='customChart' id='myChart4' width='400' height='200' style=''></canvas>
    </div>";
  }
}
