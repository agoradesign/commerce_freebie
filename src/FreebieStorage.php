<?php

namespace Drupal\commerce_freebie;

use Drupal\commerce\CommerceContentEntityStorage;
use Drupal\commerce_freebie\Event\FreebieEvents;
use Drupal\commerce_freebie\Event\FreebieSelectionEvent;

/**
 * Defines the freebie storage.
 */
class FreebieStorage extends CommerceContentEntityStorage implements FreebieStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function getActiveFreebies(int $max_results = 3) {
    $freebies = [];
    $entity_query = $this->getQuery();
    $entity_query->accessCheck(FALSE);
    $entity_query->condition('status', 1);
    $entity_query->sort('priority');
    $entity_query->sort('freebie_id');
    $result = $entity_query->execute();

    /** @var \Drupal\commerce_freebie\Entity\FreebieInterface[] $candidates */
    $candidates = $result ? $this->loadMultiple($result) : [];
    foreach ($candidates as $candidate) {
      if ($candidate->hasActivePurchasableEntity()) {
        $event = new FreebieSelectionEvent($candidate);
        $this->eventDispatcher->dispatch($event);
        if (!$event->isExcluded()) {
          $freebies[(int) $candidate->id()] = $candidate;
          if (count($freebies) >= $max_results) {
            break;
          }
        }
      }
    }
    return $freebies;
  }

}
