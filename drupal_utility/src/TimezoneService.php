<?php

namespace Drupal\drupal_utility;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * TimezoneService service to get current datetime.
 */
class TimezoneService {

  /**
   * Returns current datetime as per specified timezone.
   */
  public function getCurrentDateTime($timezone = 'America/New_York', $format = 'jS M Y - h:i A') {
    $dateTime = DrupalDateTime::createFromTimestamp(time(), $timezone);
    return $dateTime->format($format);
  }

}
