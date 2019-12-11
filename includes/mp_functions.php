<?php

function trace($n)
{
  echo "<pre>";
  print_r($n);
  echo "</pre>";
}

/*=======================VALIDATION=============================*/

function isEmail($email)
{
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return true;
  } else return false;
}

function isFloat($float)
{
  if (filter_var($float, FILTER_VALIDATE_FLOAT)) {
    return true;
  } else return false;
}

function isInt($int)
{
  if (filter_var($int, FILTER_VALIDATE_INT)) {
    return true;
  } else return false;
}

function isBool($bool)
{
  if (filter_var($bool, FILTER_VALIDATE_BOOLEAN)) {
    return true;
  } else return false;
}

function isDate($date, $format = 'Y-m-d')
{
  $d = DateTime::createFromFormat($format, $date);
  return $d && $d->format($format) == $date;
}

function isID($id)
{
  return (preg_match('/[\d]+/', $id) && $id > 0) ? true : false;
}

function getmicrotime()
{
  list($usec, $sec) = explode(" ", microtime());

  return ((float) $usec + (float) $sec);
}

function microtime_float()
{
  list($usec, $sec) = explode(" ", microtime());
  return ((float) $usec + (float) $sec);
}


?>