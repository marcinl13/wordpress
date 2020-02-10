<?php

namespace UI;

use Accountancy\addInvoice;
use Accountancy\DOC_FVS;
use Accountancy\DOC_WZ;
use DB\DBConnection;
use DB\IDBDataFactor;
use html\fillHTML;
use Plugin\fileRW;
use Documents\IDocuments;
use Hooks\CustomHooks;
use HtmlToPDF;
use Langs\Phrases;
use Statystic\Statystic;
use Pdf\HTML_FVS;
use Pdf\HTML_PZ;
use Pdf\HTML_WZ;
use plugin\PluginInfo;
use plugin\PluginUpdater;
use Plugin\wpShopPluginUninstall;
use Plugin\wpShopPluginUpdate;

class AdminUI
{
  private static $configFile = CONFIG_FILE;

  function __construct()
  {
    $src = new fillHTML();
    $src->addToHeadTag();

    // self::AddToMenuBar();
  }

  public function AddToMenuBar()
  {
    $getLangData = (array) Phrases::getPhreses('ADMIN_PANEL');

    $shop = $getLangData['SHOP'];
    $stats = $getLangData['STATS'];
    $settings = $getLangData['SETTINGS'];
    $products = $getLangData['PRODUCTS'];
    $category = $getLangData['CATEGORY'];
    $vat = $getLangData['VAT'];
    $orders = $getLangData['ORDERS'];
    $doc = $getLangData['DOCS'];

    $args = array(
      array($shop, 'shop', __CLASS__ . '::optionShop'),
      array($settings, 'settings', __CLASS__ . '::optionSettings'),
      array($stats, 'stats', __CLASS__ . '::optionStats'),
      array($products, 'products', __CLASS__ . '::optionProducts'),
      array($category, 'category', __CLASS__ . '::optionCategory'),
      array($vat, 'vat', __CLASS__ . '::optionVats'),
      array($orders, 'orders', __CLASS__ . '::optionOrders'),
      array($doc, 'docs', __CLASS__ . '::optionDocs')
    );

    $namespace = $args[0][1];

    for ($i = 0; $i < sizeof($args); $i++) {
      if ($i === 0) {
        add_menu_page($args[$i][0], $args[$i][0], 'manage_options', $namespace, $args[$i][2]);
      } else {
        add_submenu_page($namespace, $args[$i][0], $args[$i][0], 'manage_options', $args[$i][1], $args[$i][2]);
      }
    }

    //hidden
    add_submenu_page('', '', '', 'manage_options', 'unst', __CLASS__ . '::Uninstall');
    add_submenu_page('', '', '', 'manage_options', 'wts', __CLASS__ . '::Help');
    add_submenu_page('', '', '', 'manage_options', 'plupd', __CLASS__ . '::PluginUpdate');
  }

  public function  optionShop()
  {
    $getLangData = (array) Phrases::getPhreses('ADMIN_PANEL');

    $stats = $getLangData['STATS'];
    $settings = $getLangData['SETTINGS'];
    $products = $getLangData['PRODUCTS'];
    $category = $getLangData['CATEGORY'];
    $vat = $getLangData['VAT'];
    $orders = $getLangData['ORDERS'];
    $doc = $getLangData['DOCS'];
    $enter = $getLangData['ENTER'];

    $data = array(
      array(
        "img" => "https://cdn3.iconfinder.com/data/icons/ballicons-free/128/box.png",
        "text" => $products,
        "linkHref" => "?page=products",
        "linkText" => $enter
      ),
      array(
        "img" => "https://www.shareicon.net/data/128x128/2017/02/07/878405_list_512x512.png",
        "text" => $category,
        "linkHref" => "?page=category",
        "linkText" => $enter
      ),
      array(
        "img" => "https://www.shareicon.net/data/128x128/2017/07/13/888377_document_512x512.png",
        "text" => $orders,
        "linkHref" => "?page=orders",
        "linkText" => $enter
      ),
      array(
        "img" => "https://www.shareicon.net/data/128x128/2015/10/03/111196_money_512x512.png",
        "text" => $vat,
        "linkHref" => "?page=vat",
        "linkText" => $enter
      ),
      array(
        "img" => "https://www.shareicon.net/data/512x512/2017/01/06/868303_folder_512x512.png",
        "text" => $doc,
        "linkHref" => "?page=docs",
        "linkText" => $enter
      ),
      array(
        "img" => "https://www.shareicon.net/data/512x512/2015/09/22/105521_database_512x512.png",
        "text" => $settings,
        "linkHref" => "?page=options",
        "linkText" => $enter
      ),
      array(
        "img" => "https://www.shareicon.net/data/128x128/2017/07/13/888374_chart_512x512.png",
        "text" => $stats,
        "linkHref" => "?page=stats",
        "linkText" => $enter
      )
    );

    $body = "";
    foreach ($data as $key => $value) {
      $img = $value['img'];
      $text = $value['text'];
      $linkHref = $value['linkHref'];
      $linkText = $value['linkText'];

      $body .= "<div class='card border border-primary'>
        <p class='main-content ' style='text-align: center'>
          <img src='{$img}' class='icon-md mr-2 float-left' />
          {$text}
        </p>
        <div class='text-center'>
          <a href='{$linkHref}' class=''>{$linkText}</a>
        </div>
      </div>";
    }

    echo "<div class='m-5 card-columns align-items-center'>{$body}</div>";
  }

  public function  optionStats()
  {
    $draw1 = new Statystic();
    $draw1->buildPage();
  }

  public function optionProducts()
  {
    CustomHooks::AddHookUnits();
    CustomHooks::AddHookLanguage("ADMIN_OPTION_PRODUCTS");

    $src = new fillHTML();
    $src->inputMe('adm-products');
  }

  public function optionDocs()
  {
    if ($_REQUEST['mode'] == 'show') {

      if ($_REQUEST['dT'] == IDocuments::FVS_ID) {
        $pdf = new HTML_FVS($_REQUEST['id']);
        $value =  $pdf->getHtml();
      }
      if ($_REQUEST['dT'] == IDocuments::WZ_ID) {
        $pdf = new HTML_WZ($_REQUEST['id']);
        $value =  $pdf->getHtml();
      }
      if ($_REQUEST['dT'] == IDocuments::PZ_ID) {
        $pdf = new HTML_PZ($_REQUEST['id']);
        $value =  $pdf->getHtml();
      }

      // echo $value;

      if (!empty($value)) {
        $h = new HtmlToPDF();
        echo  $h->parseAsEmbed($value);
      }
    } else {
      CustomHooks::AddHookLanguage("ADMIN_OPTION_DOCS");

      $src = new fillHTML();
      $src->inputMe('adm-docs');
    }
  }

  public function optionCategory()
  {
    CustomHooks::AddHookLanguage("ADMIN_OPTION_CATEGORY");

    $src = new fillHTML();
    $src->inputMe('adm-category');
  }

  public function optionVats()
  {
    CustomHooks::AddHookLanguage("ADMIN_OPTION_VAT");

    $src = new fillHTML();
    $src->inputMe('adm-vat');
  }

  public function optionSettings()
  {
    $configData =  fileRW::readJsonAssoc(CONFIG_FILE);

    if (isset($_REQUEST['action']) && $_REQUEST['action'] == "saveOther") {

      $body = array("sName", "sAdress", "sEmail", "sLogo");
      foreach ($body as $f) $$f = $_REQUEST[$f];

      if ($sName != '' && $sAdress != '' && $sEmail != '' && $sLogo != '') {
        $configData['SHOP']['name'] = $sName;
        $configData['SHOP']['adress'] = $sAdress;
        $configData['SHOP']['email'] = $sEmail;
        $configData['SHOP']['logoLink'] = $sLogo;

        fileRW::writeJson(self::$configFile, $configData);
      }
    }
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == "savePayments") {

      $body = array("mode", "mID", "cID", "mSec", "cSec");
      foreach ($body as $f) $$f = $_REQUEST[$f];

      if ($mode != '' && $mID != '' && $cID != '' && $mSec != '' && $cSec != '') {
        $configData['PAYMENT']['PAYU']['paymentMode'] = $mode;
        $configData['PAYMENT']['PAYU']['merchantPosId'] = $mID;
        $configData['PAYMENT']['PAYU']['merchantSecond'] = $mSec;
        $configData['PAYMENT']['PAYU']['clientID'] = $cID;
        $configData['PAYMENT']['PAYU']['clientSecret'] = $cSec;


        fileRW::writeJson(self::$configFile, $configData);
      }
    }
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == "saveSettings") {

      $body = array("cLang", "cCur", "dCan", "tPri", "csf", "hpr");
      foreach ($body as $f) $$f = $_REQUEST[$f];

      if ($cLang != '' && $cCur != '' && $dCan != '' && $tPri != '' && $csf != '' && $hpr != '') {
        $configData['lang'] = $cLang;
        $configData['currencyCode'] = $cCur;
        $configData['dayCancel'] = (int) $dCan;
        $configData['transportPrice'] =  number_format((float) str_replace(",", ".", $tPri), 2);
        $configData['invoiceFormat'] = $csf;
        $configData['html2pdfrocket'] = $hpr;

        fileRW::writeJson(self::$configFile, $configData);
      }
    }
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == "import") {
      echo
        '<form action="?page=settings&action=import" method="post" enctype="multipart/form-data">
          Select image to upload:
          <input type="file" name="fileToUpload" id="fileToUpload">
          <input type="submit" value="Upload" name="submit">
        </form>';


      $allowExt = array('application/json', 'application/octet-stream');
      $fileInfo = $_FILES['fileToUpload'];
      $fileName = $fileInfo['name'];
      $fileTo = CONFIGS . 'config.json';

      if (!file_exists($fileTo) && in_array($fileInfo['type'], $allowExt)) {
        file_put_contents($fileTo, file_get_contents($fileInfo['tmp_name']));

        echo "<h3>File uploaded</h3>";
      }

      echo '<div><a class="btn btn-primary btn-small" href="?page=settings">Back</a></div>';
    } else {
      $confifFile = plugins_url() . '/' . plugin_basename(CONFIGS) . '/config.json';

      echo "<div class=\"d-block w-100 px-3 mt-3\">
        <a class=\"btn btn-danger btn-small\" href=\"{$confifFile}\" download>Export</a>
        <a class=\"btn btn-primary btn-small\" href=\"?page=settings&action=import\">Import</a>
      </div>";
    }

    CustomHooks::AddHookSettings();
    CustomHooks::AddHookLanguage("ADMIN_PANEL_SETTINGS");

    if ($_REQUEST['action'] != "import") {
      $src = new fillHTML();
      $src->inputMe('adm-settings');
    }
  }

  public function optionOrders()
  {
    if (isset($_REQUEST['mode']) && isset($_REQUEST['id'])) {
      if ($_REQUEST['mode'] == 'ccc' && isset($_REQUEST['id'])) {

        $unUsed = "";
        $orderTable = "";

        $db = new DBConnection();

        $db->getTableNames($unUsed, $unUsed, $unUsed, $orderTable);

        $orderID = (int) $_REQUEST['id'];
        $res = $db->getRow("select * from {$orderTable} where id={$orderID}", IDBDataFactor::ARRAY_A);

        $inserted = 0;
        $docWZ = new DOC_WZ($orderID, $res['dateOrder']);
        $docWZ->save($inserted);


        $invNew = 0;
        $invoice = new addInvoice($res['userID'], IDocuments::FVS_ID, $orderID, $res['total']);
        $invoice->save($invNew);


        $invDocNew = 0;
        $invoiceDoc = new DOC_FVS($invNew, $res['dateOrder']);
        $invoiceDoc->save($invDocNew);


        $created = Phrases::getPhreses('HTTP')['CREATED'];

        echo  "
        <h3>
          <a href='?page=docs&mode=show&dT=" . IDocuments::WZ_ID . "&id=" . $inserted . "'>" . $created . IDocuments::WZ . "</a>
          <br>
          <a href='?page=docs&mode=show&dT=" . IDocuments::FVS_ID . "&id=" . $invNew . "'>" . $created . IDocuments::FVS . "</a>
        </h3>";
      }
    } else {
      CustomHooks::AddHookLanguage("ADMIN_OPTION_ORDERS");
      CustomHooks::AddHookCurrencyCode();
      CustomHooks::AddHookStatuses();

      $src = new fillHTML();
      $src->inputMe('adm-order');
    }
  }

  public function PluginUpdate()
  {
    $update = new wpShopPluginUpdate();
    $update->update();
  }

  public function Help()
  {
    $gitUpdater = new PluginUpdater();
    $gitUpdater->GitConnectAlternative();
    $gitUpdater->setLocalData(PluginInfo::getFull());
    $gitUpdater->setPluginVersion(PluginInfo::getVersion());


    echo "Remove plugin instruction
    <ol>
      <li>Uninstall</li>
      <li>Deactivate Plugin</li>
      <li>DELETE</li>
    </ol> ";

    echo str_replace("-", "<br/>-", $gitUpdater->getGitBody());
  }

  public function Uninstall()
  {
    $uninstall = new wpShopPluginUninstall();
    $uninstall->uninstall();
  }
}
