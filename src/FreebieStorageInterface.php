<?php

namespace Drupal\commerce_freebie;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlEntityStorageInterface;

/**
 * Defines the interface for freebie storage.
 */
interface FreebieStorageInterface extends ContentEntityStorageInterface, SqlEntityStorageInterface {

  /**
   * Returns a list of active freebies, ready sorted.
   *
   * Activeness is not only determined by published state of the freebie. It
   * also has to hold a reference to an active purchasable entity. Additionally,
   * a FreebieSelection event is dispatched, giving other modules the chance to
   * exclude a candidate from the list (eg. if it's out of stock).
   *
   * @param int $max_results
   *   The number of maximum results returned. Defaults to 3.
   *
   * @return \Drupal\commerce_freebie\Entity\FreebieInterface[]
   *   A list of active freebies, ready sorted.
   */
  public function getActiveFreebies(int $max_results = 3);

}
