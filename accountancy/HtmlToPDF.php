<?php
use Plugin\fileRW;

class HtmlToPDF
{
  private $fileName;
  private $apikey;
  private $savePath;

  function __construct()
  {
    $this->fileName = "mypdf-2.pdf";
    $this->apikey = fileRW::readJson(CONFIG_FILE)['html2pdfrocket'];
    $this->savePath = DOWNLOAD_PATH . $this->fileName;
  }

  /**
   * Set the value of fileName
   */ 
  public function setFileName(string $fileName)
  {
    $this->fileName = $fileName;
  }
  
  /**
   * Set the value of savePath
   */ 
  public function setSavePath(string $savePath)
  {
    $this->savePath = $savePath;
  }

  /**
   * Downloads PDF/ Convert HTML to PDF 
   */
  public function parseAsEmbed($value)
  {
    $apikey = fileRW::readJson(CONFIG_FILE)['html2pdfrocket'];
    $src = DOWNLOAD_PATH . $this->fileName;

    if (strlen($apikey) < 6) return "put api key";

    $result = file_get_contents(
      "http://api.html2pdfrocket.com/pdf?apikey="
        . urlencode($apikey) .
        "&value=" . urlencode($value)
    );

    file_put_contents($src, $result);

    $src = plugins_url() . '/' . plugin_basename(ROOT) . '/' . basename(DOWNLOAD_PATH) . '/' . $this->fileName;

    return "<embed src='{$src}' type='application/pdf' width='90%' height='90%' />";
  }
}
