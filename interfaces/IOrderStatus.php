<?php

namespace Orders;

interface IOrdersStatus
{
  const statusListPL = array(
    1 => "W realizacji",
    2 => "Zrealizowano",
    3 => "Anulowano"
  );

  const statusListEN = array(
    1 => "In progress",
    2 => "Completed",
    3 => "Canceled"
  );
}
