<?php

namespace html;

use Langs\Phrases;

class fillHTML
{
  private $homeUrl = null;
  private $pluginUrl = null;
  private $projName = null;
  private $webapp = null;

  function __construct()
  {
    $this->homeUrl = home_url();
    $this->projName = plugin_basename(ROOT);
    $this->webapp = plugin_basename(WEBAPP_PATH);

    $this->pluginUrl = plugins_url() . "/{$this->webapp}/";
  }

  function addToHeadTag()
  {
    add_filter('wp_head', self::theme_header_metadata());
  }

  private function setPluginUrl()
  {
    if ($this->pluginUrl == null) {
      // $app = WEBAPP_PATH;
      $this->pluginUrl = plugins_url() . "/{$this->webapp}/";
    }
  }

  public function inputMe($name)
  {
    self::setPluginUrl();

    $url = $this->pluginUrl . "js/{$name}.js";

    echo "<div id='{$name}' class='d-block'></div><script type='module' src='{$url}'></script>";
  }

  function addVueScripts($name)
  {
    self::setPluginUrl();

    $url = $this->pluginUrl; // plugins_url() . '/projekt/webapp/';

    echo "<div id='{$name}'></div> <script type='module' src='{$url}js/importTest.js'></script>";
  }

  public function theme_header_metadata()
  {
    $urlCSS = plugins_url('assets/css/', dirname(__FILE__));
    $urlJS = plugins_url('assets/js/', dirname(__FILE__));

    $curLang = strtolower(Phrases::getLang());

    ?>
    <link rel="stylesheet" href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href='https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css'>
    <link rel="stylesheet" href='<?php echo $urlCSS . 'main.css'; ?>'>
    <link rel="stylesheet" href='<?php echo $urlCSS . 'docs.css'; ?>'>

    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@8'></script>
    <script src='https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/vue@2.6.8/dist/vue.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.5.1/vue-resource.min.js'></script>
    <script src='https://unpkg.com/vue-router/dist/vue-router.js'></script>
    <script src='<?php echo $urlJS . 'main.js'; ?>'></script>

    <script>
      const LSI = "koszyk"
      var settings = {
        apiUrl: '<?php echo $this->homeUrl; ?>/wp-json/shop/v2/',
        id: <?php echo get_current_user_id(); ?>,
        curLang: '<?php echo $curLang; ?>',
        templatePath: '<?php echo $this->homeUrl; ?>/wp-content/plugins/<?php echo $proj; ?>/webapp',
        homeUrl: '<?php echo home_url(); ?>'
      }

      function genToken() {
        var newToken = serverPost(settings.apiUrl + "token", {});

        if (!localStorage.getItem('token')) {
          localStorage.setItem('token', newToken.jwt);
        }


        return null;
      }
      // genToken();

      const token = serverPost(settings.apiUrl + "token", {});

      window.onload = () => {
        localStorage.removeItem('auto_saved_sql')
        localStorage.removeItem('auto_saved_sql_sort')

        try {
          if (localStorage.getItem(LSI) == null) {
            var tmp = {
              _token: null,
              products: [],
              rowsPerSite: 5
            };
            localStorage.setItem(LSI, JSON.stringify(tmp));
          } else {
            document.getElementById("ordersCount").innerText =
              JSON.parse(localStorage.getItem(LSI)).products.length;
          }
        } catch (error) {}

      }
    </script>
<?php
  }
}
