<?php

namespace Documents;

use Models\mDocuments;

interface IDocuments
{
  const FVS = "FVS";
  const FVK = "FVK";
  const WZ = "WZ";
  const PZ = "PZ";

  const tmp_FVS = 1;
  const tmp_FVK = 2;
  const FVS_ID = 1;
  const FVK_ID = 2;
  const WZ_ID = 3;
  const PZ_ID = 4;

  public function createDocument(string $IDocuments, mDocuments $mDocuments);

  public function createDoc(int $IDocuments, mDocuments $mDocuments);
}
