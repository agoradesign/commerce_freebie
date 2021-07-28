<?php

namespace Drupal\commerce_freebie;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Defines the freebie order processor.
 */
class FreebieOrderProcessor implements OrderProcessorInterface {

  /**
   * The freebie service.
   *
   * @var \Drupal\commerce_freebie\FreebieServiceInterface
   */
  protected $freebieService;

  /**
   * The order item storage.
   *
   * @var \Drupal\commerce_order\OrderItemStorageInterface
   */
  protected $orderItemStorage;

  /**
   * Constructs a new FreebieOrderProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_freebie\FreebieServiceInterface $freebie_service
   *   The freebie service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FreebieServiceInterface $freebie_service) {
    $this->freebieService = $freebie_service;
    $this->orderItemStorage = $entity_type_manager->getStorage('commerce_order_item');
  }

  /**
   * {@inheritdoc}
   */
  public function process(OrderInterface $order) {
    $applies_for_freebie = $this->freebieService->appliesForFreebie($order);
    $existing_freebie_items = $this->freebieService->getFreebieItems($order);

    // Early exit, if it doesn't apply and we have nothing to remove.
    if (!$applies_for_freebie && empty($existing_freebie_items)) {
      return;
    }

    $items_to_remove = [];
    $freebie_to_add = NULL;
    if ($applies_for_freebie) {
      $candidates = $this->freebieService->getActiveFreebies();
      if (!empty($existing_freebie_items)) {
        $existing_item = array_shift($existing_freebie_items);
        $existing_freebie_id = $existing_item->getPurchasedEntity() ? (int) $existing_item->getPurchasedEntity()->id() : 0;

        // If for whatever reason we have more than one item, clear the rest.
        if (!empty($existing_freebie_items)) {
          $items_to_remove = $existing_freebie_items;
        }

        $target_freebie = $this->selectFreebieForOrder($order, $candidates);
        $target_freebie_id = (int) $target_freebie->id();
        if ($existing_freebie_id !== $target_freebie_id || !in_array($existing_freebie_id, array_keys($candidates))) {
          // We have to replace the freebie in the cart.
          $items_to_remove[$existing_item->id()] = $existing_item;
          $freebie_to_add = $target_freebie;
        }
      }
      else {
        $freebie_to_add = $this->selectFreebieForOrder($order, $candidates);
      }
    }
    else {
      $items_to_remove = $existing_freebie_items;
    }

    // Early exit, if we have nothing to process here.
    if (empty($items_to_remove) && empty($freebie_to_add)) {
      return;
    }

    if (!empty($items_to_remove)) {
      $item_ids_to_remove = array_keys($items_to_remove);
      $order->get('order_items')->filter(function ($item) use ($item_ids_to_remove) {
        return !in_array($item->target_id, $item_ids_to_remove);
      });
      $this->orderItemStorage->delete($items_to_remove);
    }

    if (!empty($freebie_to_add)) {
      $order_item = $this->orderItemStorage->createFromPurchasableEntity($freebie_to_add, [
        'quantity' => 1,
      ]);
      $order_item->lock();
      $order_item->set('order_id', $order->id());
      $order_item->save();
      $order->addItem($order_item);
    }
  }

  /**
   * Selects a freebie to add for the given order from the given candidates.
   *
   * If the order has a 'selected_freebie' data value set, this will be
   * respected, if that freebie is among the candidates. Otherwise the first
   * candidate will be used.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order entity.
   * @param \Drupal\commerce_freebie\Entity\FreebieInterface[] $candidates
   *   The freebie candidates.
   *
   * @return \Drupal\commerce_freebie\Entity\FreebieInterface
   *   The freebie.
   */
  protected function selectFreebieForOrder(OrderInterface $order, array $candidates) {
    $selected_freebie_id = (int) $order->getData('selected_freebie', 0);
    return $selected_freebie_id > 0 && isset($candidates[$selected_freebie_id]) ? $candidates[$selected_freebie_id] : reset($candidates);
  }

}
