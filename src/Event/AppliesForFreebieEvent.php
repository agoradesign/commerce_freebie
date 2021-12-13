<?php

namespace Drupal\commerce_freebie\Event;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Component\EventDispatcher\Event;

/**
 * Defines an event allowing to reject order for applying freebies.
 *
 * This is called after the order total value has been calculated, but before it
 * has been compared to the configured threshold. Custom modules can use this
 * event to deny adding the freebie under all circumstances, eg if the order
 * only contains virtual products, thus is not shippable at all.
 */
class AppliesForFreebieEvent extends Event {

  /**
   * The order entity.
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $order;

  /**
   * Whether the order should never apply for freebie.
   *
   * @var bool
   */
  protected $neverApply;

  /**
   * Constructs a new AppliesForFreebieEvent object.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The freebie entity.
   */
  public function __construct(OrderInterface $order) {
    $this->order = $order;
    $this->neverApply = FALSE;
  }

  /**
   * Get the order entity.
   *
   * @return \Drupal\commerce_order\Entity\OrderInterface
   *   The order entity.
   */
  public function getOrder(): OrderInterface {
    return $this->order;
  }

  /**
   * Get whether the order should never apply for freebies.
   *
   * @return bool
   *   TRUE, if the order should never apply for freebies, FALSE otherwise.
   */
  public function shouldNeverApply(): bool {
    return $this->neverApply;
  }

  /**
   * Set whether the order should never apply for freebies.
   *
   * @param bool $neverApply
   *   TRUE, if the order should never apply for freebies, FALSE otherwise.
   */
  public function setNeverApply(bool $neverApply) {
    $this->neverApply = $neverApply;
  }

}
