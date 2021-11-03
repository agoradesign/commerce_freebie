<?php

namespace Drupal\commerce_freebie\Event;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_price\Price;
use Drupal\Component\EventDispatcher\Event;

/**
 * Defines an event allowing to modify the order total used for calculations.
 */
class OrderTotalPriceEvent extends Event {

  /**
   * The order entity.
   *
   * @var \Drupal\commerce_order\Entity\OrderInterface
   */
  protected $order;

  /**
   * The order total.
   *
   * @var \Drupal\commerce_price\Price|null
   */
  protected $orderTotal;

  /**
   * Constructs a new OrderTotalPriceEvent object.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The freebie entity.
   */
  public function __construct(OrderInterface $order) {
    $this->order = $order;
    $this->orderTotal = $order->getTotalPrice();
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
   * Get the order total.
   *
   * @return \Drupal\commerce_price\Price|null
   *   The order total.
   */
  public function getOrderTotal() {
    return $this->orderTotal;
  }

  /**
   * Set the order total.
   *
   * @param \Drupal\commerce_price\Price $order_total
   *   The order total.
   */
  public function setOrderTotal(Price $order_total) {
    $this->orderTotal = $order_total;
  }

}
