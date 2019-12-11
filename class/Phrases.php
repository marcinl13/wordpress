<?php

namespace Langs;

use Plugin\fileRW;

class Phrases
{

  public static function getLang()
  {
    $file = fileRW::readJsonAssoc(CONFIG_FILE);

    return $file['lang'];
  }

  public function getLangFileName()
  {
    $lang = self::getLang();
    
    return LANGS_PATH . $lang . '.json';
  }

  public static function getPhreses(string $key)
  {
    return json_decode(file_get_contents(self::getLangFileName()), true)[$key];
  }
}
