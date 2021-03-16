<?php

namespace Drupal\commerce_freebie\Event;

use Drupal\commerce_freebie\Entity\FreebieInterface;
use Drupal\Component\EventDispatcher\Event;

/**
 * Defines an event allowing to exclude a freebie from being selectable.
 */
class FreebieSelectionEvent extends Event {

  /**
   * The freebie entity.
   *
   * @var \Drupal\commerce_freebie\Entity\FreebieInterface
   */
  protected $freebie;

  /**
   * Whether the freebie should be excluded from being selectable.
   *
   * @var bool
   */
  protected $isExcluded;

  /**
   * Constructs a new FreebieSelectionEvent object.
   *
   * @param \Drupal\commerce_freebie\Entity\FreebieInterface $freebie
   *   The freebie entity.
   */
  public function __construct(FreebieInterface $freebie) {
    $this->freebie = $freebie;
    $this->isExcluded = FALSE;
  }

  /**
   * Set the freebie as excluded.
   */
  public function exclude() {
    $this->isExcluded = TRUE;
    /** @noinspection PhpDeprecationInspection */
    $this->stopPropagation();
  }

  /**
   * Get the freebie entity.
   *
   * @return \Drupal\commerce_freebie\Entity\FreebieInterface
   *   The freebie entity.
   */
  public function getFreebie(): FreebieInterface {
    return $this->freebie;
  }

  /**
   * Get whether the freebie should be excluded from being selectable.
   *
   * @return bool
   *   TRUE, if the freebie should be excluded from being selectable. FALSE
   *   otherwise.
   */
  public function isExcluded(): bool {
    return $this->isExcluded;
  }

}
