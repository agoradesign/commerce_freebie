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
   * Calculates the price difference needed to get a free for the given order.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order entity.
   *
   * @return \Drupal\commerce_price\Price|null
   *   The needed amount (as price object) for the given cart (order entity) to
   *   meet the criteria to get a freebie. A zero-amount price is returned, if
   *   the criteria is already met. NULL is returned, if freebie threshold is
   *   deactivated at the moment, or the order does not have a total price set,
   *   yet, hence no currency is available.
   */
  public function calcDifferenceToGetFreebie(OrderInterface $order);

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
