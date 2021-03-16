<?php

namespace Drupal\commerce_freebie\Entity;

use Drupal\commerce\Entity\CommerceBundleEntityBase;

/**
 * Defines the freebie type entity class.
 *
 * @ConfigEntityType(
 *   id = "commerce_freebie_type",
 *   label = @Translation("Freebie type"),
 *   label_collection = @Translation("Freebie types"),
 *   label_singular = @Translation("freebie type"),
 *   label_plural = @Translation("freebie types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count freebie type",
 *     plural = "@count freebie types",
 *   ),
 *   handlers = {
 *     "access" = "Drupal\commerce\CommerceBundleAccessControlHandler",
 *     "list_builder" = "Drupal\commerce_freebie\FreebieTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_freebie\Form\FreebieTypeForm",
 *       "edit" = "Drupal\commerce_freebie\Form\FreebieTypeForm",
 *       "duplicate" = "Drupal\commerce_freebie\Form\FreebieTypeForm",
 *       "delete" = "Drupal\commerce\Form\CommerceBundleEntityDeleteFormBase"
 *     },
 *     "local_task_provider" = {
 *       "default" = "Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "commerce_freebie_type",
 *   admin_permission = "administer commerce_freebie_type",
 *   bundle_of = "commerce_freebie",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "purchasableEntityType",
 *     "orderItemType",
 *     "traits",
 *     "locked",
 *   },
 *   links = {
 *     "add-form" = "/admin/commerce/config/freebie-types/add",
 *     "edit-form" = "/admin/commerce/config/freebie-types/{commerce_freebie_type}/edit",
 *     "duplicate-form" = "/admin/commerce/config/freebie-types/{commerce_freebie_type}/duplicate",
 *     "delete-form" = "/admin/commerce/config/freebie-types/{commerce_freebie_type}/delete",
 *     "collection" =  "/admin/commerce/config/freebie-types"
 *   }
 * )
 */
class FreebieType extends CommerceBundleEntityBase implements FreebieTypeInterface {

  /**
   * The purchasable entity type ID.
   *
   * @var string
   */
  protected $purchasableEntityType;

  /**
   * The order item type ID.
   *
   * @var string
   */
  protected $orderItemType;

  /**
   * {@inheritdoc}
   */
  public function getPurchasableEntityTypeId() {
    return $this->purchasableEntityType;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrderItemTypeId() {
    return $this->orderItemType;
  }

}
