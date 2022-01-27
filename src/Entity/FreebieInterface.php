<?php

namespace Drupal\commerce_freebie\Entity;

use Drupal\commerce\PurchasableEntityInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Defines the interface for freebies.
 */
interface FreebieInterface extends PurchasableEntityInterface, EntityChangedInterface, EntityOwnerInterface, EntityPublishedInterface {

  /**
   * Gets the referenced purchasable entity.
   *
   * @return \Drupal\commerce\PurchasableEntityInterface
   *   The referenced purchasable entity.
   */
  public function getPurchasableEntity();

  /**
   * Gets the referenced purchasable entity ID.
   *
   * @return int
   *   The referenced purchasable entity ID.
   */
  public function getPurchasableEntityId();

  /**
   * Gets whether the referenced purchasable entity is set and active.
   *
   * @return bool
   *   If the freebie has it's purchasable entity reference set and that entity
   *   is published.
   */
  public function hasActivePurchasableEntity();

  /**
   * Get the SKU.
   *
   * @return string|null
   *   The SKU.
   */
  public function getSku();

  /**
   * Gets the start date/time.
   *
   * The start date/time should always be used in the store timezone. The
   * timezone is provided by the caller instead.
   *
   * Note that the returned date/time value is the same in any timezone,
   * the "2019-10-17 10:00" stored value is returned as "2019-10-17 10:00 CET"
   * for "Europe/Berlin" and "2019-10-17 10:00 ET" for "America/New_York".
   *
   * @param string $store_timezone
   *   The store timezone. E.g. "Europe/Berlin".
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   The start date/time.
   */
  public function getStartDate(string $store_timezone = 'UTC'): DrupalDateTime;

  /**
   * Sets the start date/time.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime $start_date
   *   The start date/time.
   *
   * @return $this
   */
  public function setStartDate(DrupalDateTime $start_date);

  /**
   * Gets the end date/time.
   *
   * The end date/time should always be used in the store timezone. The timezone
   * is provided by the caller instead.
   *
   * Note that the returned date/time value is the same in any timezone,
   * the "2019-10-17 11:00" stored value is returned as "2019-10-17 11:00 CET"
   * for "Europe/Berlin" and "2019-10-17 11:00 ET" for "America/New_York".
   *
   * @param string $store_timezone
   *   The store timezone. E.g. "Europe/Berlin".
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime|null
   *   The end date/time.
   */
  public function getEndDate(string $store_timezone = 'UTC'): ?DrupalDateTime;

  /**
   * Sets the end date/time.
   *
   * @param \Drupal\Core\Datetime\DrupalDateTime|null $end_date
   *   The end date/time.
   *
   * @return $this
   */
  public function setEndDate(DrupalDateTime $end_date = NULL);

  /**
   * Gets the order item creation timestamp.
   *
   * @return int
   *   The order item creation timestamp.
   */
  public function getCreatedTime();

  /**
   * Sets the order item creation timestamp.
   *
   * @param int $timestamp
   *   The order item creation timestamp.
   *
   * @return $this
   */
  public function setCreatedTime($timestamp);

}
