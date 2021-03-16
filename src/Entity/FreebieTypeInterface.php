<?php

namespace Drupal\commerce_freebie\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityInterface;

/**
 * Defines the interface for freebie types.
 */
interface FreebieTypeInterface extends CommerceBundleEntityInterface {

  /**
   * Gets the freebie type's referenced purchasable entity type ID.
   *
   * E.g, if freebies of this type are used to reference product variations, the
   * purchasable entity type ID will be 'commerce_product_variation'.
   *
   * @return string
   *   The purchasable entity type ID.
   */
  public function getPurchasableEntityTypeId();

  /**
   * Gets the freebie type's order item type ID.
   *
   * Used for finding/creating the appropriate order item when adding to an
   * order).
   *
   * @return string
   *   The order item type ID.
   */
  public function getOrderItemTypeId();

}
