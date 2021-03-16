<?php

namespace Drupal\commerce_freebie\Entity;

use Drupal\commerce\PurchasableEntityInterface;
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
