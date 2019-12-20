<?php

namespace plugin;

class PluginUpdater
{
  private $repository;
  private $gitData;
  private $locData;
  private $downloadPath;
  private $localPath;
  private $err = array();

  private $gitRepoURL = '';
  private $pluginVersion = '';


  function __construct()
  {
    // $this->repository = '';
    // $this->account = '';
    // $this->gitData = array();
    // $this->locData = array();
    // $this->downloadPath = '';
    // $this->localPath = '';
    // $this->err = array();
  }

  function setLocalData($data = array())
  {
    if (empty($data)) {
      $this->err[] = __FUNCTION__;
    } else {
      $this->gitRepoURL = $data['PluginURI'];
      $this->pluginVersion = $data['Version'];
    }
  }

  public function GitConnect()
  {
    if (empty($this->err)) {
      $urlRelease = str_replace('https://github.com/', 'https://api.github.com/repos/', $this->gitRepoURL);
      $urlRelease .= '/releases';

      $this->gitData = json_decode(file_get_contents(
        $urlRelease,
        false,
        stream_context_create(['http' => ['header' => "User-Agent: Vestibulum\r\n"]])
      ), true);
    }
  }

  public function GitConnectAlternative()
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.github.com/repos/marcinl13/wordpress/releases",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_POSTFIELDS => "",
      CURLOPT_HTTPHEADER => array(
        "User-Agent: Vestibulum",
        "Authorization: Bearer 681683976da911ef3c880198afd7a7843588b70a"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    $this->gitData = json_decode($response, true);
  }

  public function CheckForUpdates()
  {
    if (is_array($this->err) && empty($this->err)) {
      $latestVersionGit = $this->gitData[0]['name'];
      $currentVersion  = $this->pluginVersion;

      $currentVersion =  intval(join("", explode(".", $currentVersion)));
      $latestVersionGit = intval(join("", explode(".", $latestVersionGit)));

      // trace(array($currentVersion, $latestVersionGit, $currentVersion < $latestVersionGit));
      return $currentVersion < $latestVersionGit;
    } else {
      $this->err[] = 'update fail';
    }
  }

  public function DownloadFile($dp)
  {
    if (mb_strlen($dp) == 0) $this->err[] = __FUNCTION__;

    if (self::CheckForUpdates() == true) {
      $latestVersionGit = $this->gitData[0]['tag_name'];

      \preg_match('/((.+)\/(.+)\/(.+))/', 'https://github.com/marcinl13/wordpress', $output_array);
      $repository = $output_array[4];

      $fileName = "{$repository}-{$latestVersionGit}.zip";
      $downloadLink = "{$this->gitRepoURL}/archive/{$latestVersionGit}.zip";

      $this->downloadPath = $dp . $fileName;

      $fileData = file_get_contents($downloadLink);

      // trace($fileData);die();

      fopen($dp . $fileName, 'w');

      // Save Content to file
      $downloaded = file_put_contents($dp . $fileName, $fileData);

      trace($downloaded);

      if ($downloaded == 0) {
        $this->err[] = 'can\'t download';
      }
    }
  }

  public function UnZip($location)
  {
    if (mb_strlen($this->downloadPath) > 0) {
      $zip = new \ZipArchive;
      $res = $zip->open($this->downloadPath);

      if ($res === true) {
        $zip->extractTo($location);
        $zip->close();
        // self::overrideLocal();
        // unlink($this->downloadPath);
      } else {
        $this->err[] = __FUNCTION__;
      }
    }
  }

  public function ShowErrors()
  {
    return $this->err;
  }

  private function overrideLocal()
  {
    self::LocalData($this->localPath);

    $this->locData[0]['version'] = $this->gitData[0]['tag_name'];

    //rewrite json
    $fp = fopen($this->localPath, 'w');
    fwrite($fp, json_encode($this->locData));
    fclose($fp);
  }

  /**
   * Get the value of gitData
   */
  public function getGitData(): array
  {
    return (array) $this->gitData;
  }
  /**
   * Get the value of Git Data Version
   */
  public function getGitDataVersion(): string
  {
    return (string) $this->gitData[0]['tag_name'];
  }

  /**
   * Get the value of Git Zip Url
   */
  public function getGitZipUrl(): string
  {
    return (string) $this->gitData[0]['zipball_url'];
  }

  /**
   * Get the value of Git Zip Url
   */
  public function getGitBody(): string
  {
    return (string) $this->gitData[0]['body'];
  }
  /**
   * Get the value of Git Zip Url
   */
  public function isRelease(): bool
  {
    return (bool) $this->gitData[0]['prerelease'] == "1" ? false : true;
  }

  /**
   * Set the value of pluginVersion
   *
   * @return  self
   */
  public function setPluginVersion($pluginVersion)
  {
    $this->pluginVersion = $pluginVersion;
  }
}
