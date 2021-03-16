<?php

namespace Drupal\commerce_freebie;

use Drupal\commerce_order\Entity\OrderInterface;

/**
 * Defines the freebie service interface.
 */
interface FreebieServiceInterface {

  /**
   * Checks whether or not the given order applies for freebie.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order entity.
   *
   * @return bool
   *   TRUE, if the given order applies for having a freebie.
   */
  public function appliesForFreebie(OrderInterface $order);

  /**
   * Extracts the freebie order items of the given order.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order entity.
   *
   * @return \Drupal\commerce_order\Entity\OrderItemInterface[]
   *   A list of freebie order items of the order. It is never expected to
   *   return more than one item. If for whatever reason more than one item
   *   exists, they will get removed automatically on processing the order.
   */
  public function getFreebieItems(OrderInterface $order);

  /**
   * Returns a list of active freebies, ready sorted.
   *
   * Convenience method for calling the equally named storage function, passing
   * the configured maximum number of items and keeping the results in a static
   * cache.
   *
   * @return \Drupal\commerce_freebie\Entity\FreebieInterface[]
   *   A list of active freebies, ready sorted.
   */
  public function getActiveFreebies();

}
