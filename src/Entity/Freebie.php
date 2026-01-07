<?php

namespace Drupal\commerce_freebie\Entity;

use Drupal\commerce\Entity\CommerceContentEntityBase;
use Drupal\commerce\EntityOwnerTrait;
use Drupal\commerce_price\Price;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Defines the freebie entity class.
 *
 * @ContentEntityType(
 *   id = "commerce_freebie",
 *   label = @Translation("Freebie"),
 *   label_collection = @Translation("Freebies"),
 *   label_singular = @Translation("freebie"),
 *   label_plural = @Translation("freebies"),
 *   label_count = @PluralTranslation(
 *     singular = "@count freebie",
 *     plural = "@count freebies",
 *   ),
 *   bundle_label = @Translation("Freebie type"),
 *   handlers = {
 *     "storage" = "Drupal\commerce_freebie\FreebieStorage",
 *     "access" = "Drupal\entity\EntityAccessControlHandler",
 *     "permission_provider" = "Drupal\entity\EntityPermissionProvider",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_freebie\FreebieListBuilder",
 *     "views_data" = "Drupal\commerce\CommerceEntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\commerce_freebie\Form\FreebieForm",
 *       "add" = "Drupal\commerce_freebie\Form\FreebieForm",
 *       "edit" = "Drupal\commerce_freebie\Form\FreebieForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\AdminHtmlRouteProvider",
 *       "delete-multiple" = "Drupal\entity\Routing\DeleteMultipleRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer commerce_freebie",
 *   permission_granularity = "bundle",
 *   base_table = "commerce_freebie",
 *   data_table = "commerce_freebie_field_data",
 *   entity_keys = {
 *     "id" = "freebie_id",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *     "owner" = "uid",
 *     "uid" = "uid",
 *   },
 *   links = {
 *     "add-page" = "/freebie/add",
 *     "add-form" = "/freebie/add/{commerce_freebie_type}",
 *     "edit-form" = "/freebie/{commerce_freebie}/edit",
 *     "delete-form" = "/freebie/{commerce_freebie}/delete",
 *     "delete-multiple-form" = "/admin/commerce/freebies/delete",
 *     "collection" = "/admin/commerce/freebies"
 *   },
 *   bundle_entity_type = "commerce_freebie_type",
 *   field_ui_base_route = "entity.commerce_freebie_type.edit_form",
 * )
 */
class Freebie extends CommerceContentEntityBase implements FreebieInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getPurchasableEntity() {
    return $this->getTranslatedReferencedEntity('purchasable_entity');
  }

  /**
   * {@inheritdoc}
   */
  public function getPurchasableEntityId() {
    return $this->get('purchasable_entity')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function hasActivePurchasableEntity() {
    $purchasable_entity = $this->getPurchasableEntity();
    if (!$purchasable_entity) {
      return FALSE;
    }
    if ($purchasable_entity instanceof EntityPublishedInterface) {
      return $purchasable_entity->isPublished();
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getSku() {
    $purchasable_entity = $this->getPurchasableEntity();
    if ($purchasable_entity && $purchasable_entity->hasField('sku') && !$purchasable_entity->get('sku')->isEmpty()) {
      return $purchasable_entity->get('sku')->value;
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStartDate(string $store_timezone = 'UTC'): DrupalDateTime {
    return new DrupalDateTime($this->get('start_date')->value, $store_timezone);
  }

  /**
   * {@inheritdoc}
   */
  public function setStartDate(DrupalDateTime $start_date) {
    $this->get('start_date')->value = $start_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEndDate(string $store_timezone = 'UTC'): ?DrupalDateTime {
    if (!$this->get('end_date')->isEmpty()) {
      return new DrupalDateTime($this->get('end_date')->value, $store_timezone);
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setEndDate(?DrupalDateTime $end_date = NULL) {
    $this->get('end_date')->value = NULL;
    if ($end_date) {
      $this->get('end_date')->value = $end_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStores() {
    return $this->getTranslatedReferencedEntities('stores');
  }

  /**
   * {@inheritdoc}
   */
  public function getOrderItemTypeId() {
    // The order item type is a bundle-level setting.
    $type_storage = $this->entityTypeManager()->getStorage('commerce_freebie_type');
    /** @var \Drupal\commerce_freebie\Entity\FreebieTypeInterface $type_entity */
    $type_entity = $type_storage->load($this->bundle());

    return $type_entity->getOrderItemTypeId();
  }

  /**
   * {@inheritdoc}
   */
  public function getOrderItemTitle() {
    return $this->label();
  }

  /**
   * {@inheritdoc}
   */
  public function getPrice() {
    $original_price = $this->getPurchasableEntity()->getPrice();
    return new Price('0', $original_price->getCurrencyCode());
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->getPurchasableEntity()->label();
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['purchasable_entity'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Free product'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -1,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['priority'] = BaseFieldDefinition::create('weight')
      ->setLabel(t('Priority'))
      ->setRequired(TRUE)
      ->setSetting('range', 20)
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'weight_selector',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['start_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start date'))
      ->setRequired(TRUE)
      ->setSetting('datetime_type', 'datetime')
      ->setDefaultValueCallback('Drupal\commerce_freebie\Entity\Freebie::getDefaultStartDate')
      ->setDisplayOptions('form', [
        'type' => 'commerce_store_datetime',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['end_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End date'))
      ->setRequired(FALSE)
      ->setSetting('datetime_type', 'datetime')
      ->setSetting('datetime_optional_label', t('Provide an end date'))
      ->setDisplayOptions('form', [
        'type' => 'commerce_store_datetime',
        'weight' => 6,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status']
      ->setLabel(t('Published'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 90,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time when the variation was created.'))
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time when the variation was last edited.'))
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    /** @var \Drupal\commerce_freebie\Entity\FreebieTypeInterface $freebie_type */
    $freebie_type = FreebieType::load($bundle);
    if (!$freebie_type) {
      throw new \RuntimeException(sprintf('Could not load the "%s" freebie type.', $bundle));
    }
    $purchasable_entity_type_id = $freebie_type->getPurchasableEntityTypeId();

    $fields = [];
    $fields['purchasable_entity'] = clone $base_field_definitions['purchasable_entity'];
    if ($purchasable_entity_type_id) {
      $fields['purchasable_entity']->setSetting('target_type', $purchasable_entity_type_id);
    }
    else {
      throw new \RuntimeException(sprintf('No purchasable entity type set for the "%s" freebie type.', $bundle));
    }

    return $fields;
  }

  /**
   * Default value callback for 'start_date' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return string
   *   The default value (date string).
   */
  public static function getDefaultStartDate(): string {
    $timestamp = \Drupal::time()->getRequestTime();
    return date(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $timestamp);
  }

}
