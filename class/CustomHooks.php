<?php

namespace Hooks;

use Langs\Phrases;
use Models\v18\mMagazine;
use Orders\IOrdersStatus;
use Plugin\fileRW;
use Token\Token;
use Units\IUnits;

class CustomHooks
{
  public static function AddHookUnits()
  {
    add_action('wp_head', self::hookUnits());
  }

  public static function AddHookStatuses()
  {
    add_action('wp_head', self::hookStatus());
  }

  public static function AddHookSettings()
  {
    add_action('wp_head', self::hookSettings());
  }

  public static function AddHookLanguage(string $tag)
  {
    add_action('wp_head', self::hookLanguage($tag));
  }

  public static function AddHookTransportPrice()
  {
    add_action('wp_head', self::hookTransportPrice());
  }

  public static function AddHookToken()
  {
    add_action('wp_head', self::hookToken());
  }

  public static function AddHookCurrencyCode()
  {
    add_action('wp_head', self::hookCurrencyCode());
  }

  public static function AddHookNoAccess()
  {
    add_action('body_class', self::msg_NoAccess());
  }


  public static function AddHookChart01()
  {
    add_action('wp_head', self::addChart01());
  }

  public static function AddHookChart02()
  {
    add_action('wp_head', self::addChart02());
  }

  public static function AddHookChart03()
  {
    add_action('wp_head', self::addChart03());
  }

  public static function AddHookChart04()
  {
    add_action('wp_head', self::addChart04());
  }



  /*========================== functions =============================== */

  public function hookUnits()
  {
    $units = json_encode(IUnits::UNITS);

    echo "
      <script>
        let units = [$units];
      </script>
    ";
  }

  public function hookSettings()
  {
    $testSettings = fileRW::readFile(CONFIG_FILE);

    echo "
      <script>
        let testSettings = [$testSettings];  
      </script>
    ";
  }

  public function hookStatus()
  {
    $lang = Phrases::getLang();

    $units = json_encode(strtoupper($lang) == 'PL' ? IOrdersStatus::statusListPL : IOrdersStatus::statusListEN);

    echo "
      <script>
        let statuses = [$units];
      </script>
    ";
  }

  public function hookTransportPrice()
  {
    $transportPrice = (float) fileRW::readJsonAssoc(CONFIG_FILE)['transportPrice'];

    echo "
      <script>
        let transportPrice = [$transportPrice];
      </script>
    ";
  }

  public function hookCurrencyCode()
  {
    $currencyCode = (string) fileRW::readJsonAssoc(CONFIG_FILE)['currencyCode'];

    echo "
      <script>
        let currencyCode = ['$currencyCode'];
      </script>
    ";
  }

  public function hookToken()
  {
    $t = new Token();
    // $t = $t->CreateToken(array(['id'] => get_current_user_id()));

    // $accessToken = (string) $t->CreateToken( array(['id'] => get_current_user_id()));
    $accessToken = (string) '';

    echo "
      <script>
        let act = '$accessToken';
      </script>
    ";
  }


  public function hookLanguage(string $tag)
  {
    $langSettings = json_encode(Phrases::getPhreses($tag));

    $filterSettings = json_encode(Phrases::getPhreses('FILTERS'));

    echo "
      <script>
        let langSettings = [$langSettings];
        let langSettingsFilter = [$filterSettings];        
      </script>
    ";
  }

  private function msg_NoAccess()
  {
    $noAccessMsg = Phrases::getPhreses('HTTP')['NO_ACCESS'] ;

    echo "</article> <h3 class='text-center'>{$noAccessMsg}</h3>";
  }

  /*========================== charts =============================== */


  private  function addChart01()
  {
    $mag = new mMagazine();
    $data = json_encode($mag->getChar01Data());
    $urlJS = plugins_url('assets/js/chart01.js', dirname(ROOT . plugin_basename(ROOT) . "/assets"));

    echo "
      <script>
        let chart1Data = {$data};   
      </script>
      <script defer src='{$urlJS}'></script>
    ";
  }

  private  function addChart02()
  {
    $mag = new mMagazine();
    $data = json_encode($mag->getProductInMagazine2());
    $urlJS = plugins_url('assets/js/chart02.js', dirname(ROOT . plugin_basename(ROOT) . "/assets"));

    echo "
      <script>
        let chart2Data = {$data};   
      </script>
      <script defer src='{$urlJS}'></script>
    ";
  }

  private  function addChart03()
  {
    $mag = new mMagazine();
    $data = json_encode($mag->quantityProductGroup());
    $urlJS = plugins_url('assets/js/chart03.js', dirname(ROOT . plugin_basename(ROOT) . "/assets"));

    echo "
      <script>
        let chart3Data = [$data];   
      </script>
      <script defer src='{$urlJS}'></script>
    ";
  }

  private  function addChart04()
  {
    $mag = new mMagazine();
    $data = json_encode($mag->productCategory());
    $urlJS = plugins_url('assets/js/chart04.js', dirname(ROOT . plugin_basename(ROOT) . "/assets"));

    echo "
      <script>
        let chart4Data = [$data];   
      </script>
      <script defer src='{$urlJS}'></script>
    ";
  }
}
