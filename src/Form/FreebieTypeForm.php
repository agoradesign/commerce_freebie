<?php

namespace Drupal\commerce_freebie\Form;

use Drupal\commerce\EntityHelper;
use Drupal\commerce\Form\CommerceBundleEntityFormBase;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity\Form\EntityDuplicateFormTrait;

/**
 * Defines the freebie type form.
 */
class FreebieTypeForm extends CommerceBundleEntityFormBase {

  use EntityDuplicateFormTrait;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\commerce_freebie\Entity\FreebieTypeInterface $freebie_type */
    $freebie_type = $this->entity;
    // Prepare the list of purchasable entity types.
    $entity_types = $this->entityTypeManager->getDefinitions();
    $purchasable_entity_types = array_filter($entity_types, function (EntityTypeInterface $entity_type) {
      return $entity_type->entityClassImplements(PurchasableEntityInterface::class);
    });
    $purchasable_entity_types = array_map(function (EntityTypeInterface $entity_type) {
      return $entity_type->getLabel();
    }, $purchasable_entity_types);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $freebie_type->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $freebie_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_freebie\Entity\FreebieType::load',
      ],
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#disabled' => !$freebie_type->isNew(),
    ];
    $form = $this->buildTraitForm($form, $form_state);

    $form['purchasableEntityType'] = [
      '#type' => 'select',
      '#title' => $this->t('Purchasable entity type'),
      '#default_value' => $freebie_type->getPurchasableEntityTypeId(),
      '#options' => $purchasable_entity_types,
      '#empty_value' => '',
      '#disabled' => !$freebie_type->isNew(),
    ];

    if ($this->moduleHandler->moduleExists('commerce_order')) {
      // Prepare a list of order item types used to purchase freebies.
      $order_item_type_storage = $this->entityTypeManager->getStorage('commerce_order_item_type');
      $order_item_types = $order_item_type_storage->loadMultiple();
      $order_item_types = array_filter($order_item_types, function ($order_item_type) {
        /** @var \Drupal\commerce_order\Entity\OrderItemTypeInterface $order_item_type */
        return $order_item_type->getPurchasableEntityTypeId() === 'commerce_freebie';
      });

      $form['orderItemType'] = [
        '#type' => 'select',
        '#title' => $this->t('Order item type'),
        '#default_value' => $freebie_type->getOrderItemTypeId(),
        '#options' => EntityHelper::extractLabels($order_item_types),
        '#empty_value' => '',
        '#required' => TRUE,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->validateTraitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    $this->postSave($this->entity, $this->operation);
    $this->submitTraitForm($form, $form_state);

    $this->messenger()->addMessage($this->t('Saved the %label freebie type.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.commerce_freebie_type.collection');
  }

}
