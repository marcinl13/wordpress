<?php

namespace DB;

use ReflectionClass;

class cTabelki
{
  private $collate;
  private $prefix;

  function __construct()
  {
    global $wpdb;

    $this->prefix = $wpdb->prefix . PROJECT_PREFIX;

    $this->collate = $wpdb->has_cap('collation') ? $wpdb->get_charset_collate() : '';
  }

  public function getPrefix()
  {
    return $this->prefix;
  }

  public function getCollate()
  {
    return $this->collate;
  }

  public function getTableName(string $ITablesNames)
  {
    return $this->prefix . $ITablesNames;
  }

  static function getConstants()
  {
    $oClass = new ReflectionClass(__CLASS__);
    return $oClass->getConstants();
  }
}
