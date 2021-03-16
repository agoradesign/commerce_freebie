<?php

namespace Drupal\commerce_freebie;

use Drupal\commerce_freebie\Entity\FreebieInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_price\Price;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Default freebie service implementation.
 */
class FreebieService implements FreebieServiceInterface {

  /**
   * The module configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The freebie storage.
   *
   * @var \Drupal\commerce_freebie\FreebieStorageInterface
   */
  protected $freebieStorage;

  /**
   * The order item storage.
   *
   * @var \Drupal\commerce_order\OrderItemStorageInterface
   */
  protected $orderItemStorage;

  /**
   * A static cache of the currently available freebies for orders.
   *
   * @var \Drupal\commerce_freebie\Entity\FreebieInterface[]|null
   */
  protected $activeFreebies;

  /**
   * Constructs a new FreebieService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->config = $config_factory->get('commerce_freebie.settings');
    $this->freebieStorage = $entity_type_manager->getStorage('commerce_freebie');
    $this->orderItemStorage = $entity_type_manager->getStorage('commerce_order_item');
    $this->activeFreebies = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function appliesForFreebie(OrderInterface $order) {
    $subtotal_threshold = (int) $this->config->get('order_subtotal_threshold');
    // A negative threshold means no freebies at all.
    if ($subtotal_threshold < 0) {
      return FALSE;
    }

    $total = $this->getOrderTotalWithoutShipping($order);
    if (empty($total)) {
      return FALSE;
    }

    $threshold_price = new Price($subtotal_threshold, $total->getCurrencyCode());
    return $total->greaterThanOrEqual($threshold_price);
  }

  /**
   * {@inheritdoc}
   */
  public function getFreebieItems(OrderInterface $order) {
    $freebie_items = [];
    foreach ($order->getItems() as $item) {
      if ($item->hasPurchasedEntity() && $item->getPurchasedEntity() instanceof FreebieInterface) {
        $freebie_items[$item->id()] = $item;
      }
    }
    return $freebie_items;
  }

  /**
   * {@inheritdoc}
   */
  public function getActiveFreebies() {
    if (is_null($this->activeFreebies)) {
      $this->activeFreebies = $this->freebieStorage->getActiveFreebies($this->config->get('selection_number'));
    }
    return $this->activeFreebies;
  }

  /**
   * Calculates the order total excluding shipping costs.
   *
   * As $order->getSubtotalPrice() does not consider promotions. we need to
   * calculate the amount by ourselves.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order entity.
   *
   * @return \Drupal\commerce_price\Price|null
   *   The order total without shipping costs. NULL will be returned in theory,
   *   if the order total is also NULL.
   */
  protected function getOrderTotalWithoutShipping(OrderInterface $order) {
    $total_price = $order->getTotalPrice();
    if (empty($total_price)) {
      return NULL;
    }

    // $order->getSubtotalPrice() does not consider promotion. So we need to
    // calculate the subtotal without shipping by ourselves.
    $shipping = $order->getAdjustments(['shipping']);
    if (!empty($shipping)) {
      foreach ($shipping as $shipping_adjustment) {
        $total_price = $total_price->subtract($shipping_adjustment->getAmount());
      }
    }
    return $total_price;
  }

}
