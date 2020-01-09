<?php

namespace Plugin;

use plugin\PluginInfo;
use plugin\PluginUpdater;

class wpShopPluginUpdate
{
  public function update()
  {
    $gitUpdater = new PluginUpdater();
    $gitUpdater->GitConnectAlternative();
    $gitUpdater->setLocalData(PluginInfo::getFull());
    $gitUpdater->setPluginVersion(PluginInfo::getVersion());
    
    // trace(array('is new update' => $gitUpdater->CheckForUpdates()));

    $file = dirname(ROOT) . '\wordpress-' . $gitUpdater->getGitDataVersion();

    if (!file_exists($file)) {
      $place = str_replace('plugins', 'upgrade', dirname(ROOT));

      $gitUpdater->DownloadFile($place);
      $gitUpdater->UnZip(dirname(ROOT));
    }
  }
}
