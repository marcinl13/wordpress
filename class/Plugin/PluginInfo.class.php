<?php

namespace plugin;

class PluginInfo
{
  private $default_headers = array();
  private $file = '';


  function __construct()
  { }

  function setHeaders()
  {
    $this->default_headers = array(
      'Name'        => 'Plugin Name',
      'PluginURI'   => 'Plugin URI',
      'Version'     => 'Version',
      'Description' => 'Description',
      'Author'      => 'Author',
      'AuthorURI'   => 'Author URI',
      'TextDomain'  => 'Text Domain',
      'DomainPath'  => 'Domain Path',
      'Network'     => 'Network',
      // Site Wide Only is deprecated in favor of Network.
      '_sitewide'   => 'Site Wide Only',
    );
  }

  function setFile($filePath = null)
  {
    $this->file = $filePath == null ? PROJEKT_PLUGIN_FILE . 'projekt.php' : $filePath;
  }

  function getData()
  {

    if ($this->file == '') {
      self::setFile();
    }

    if (empty($this->default_headers)) {
      self::setHeaders();
    }

    $data = (array) get_file_data($this->file, $this->default_headers);
    return $data;
  }

  public static function getFull()
  {
    $tmp = new PluginInfo();
    return $tmp->getData();
  }

  public static function getBaseName(): string
  {
    return basename(ROOT);
  }

  public static function getVersion(): string
  {
    $tmp = new PluginInfo();
    return $tmp->getData()['Version'];
  }
}
