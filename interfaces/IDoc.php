<?php

namespace Accountancy;

interface IDoc
{
  public function getDetails($id): array;
  public function save(int &$insertedId = 0): bool;
}
