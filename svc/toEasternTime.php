<?php

  function toEasternTime($date) {
    $utc_timezone = new DateTimeZone("UTC");
    $easterntimezone = new DateTimeZone("America/Louisville"); 
    $datetime = new DateTime($date, $utc_timezone);
    $datetime->setTimezone($easterntimezone);

    return $datetime;
  }

?>