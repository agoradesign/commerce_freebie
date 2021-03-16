<?php

namespace Drupal\commerce_freebie;

use Drupal\commerce_freebie\Entity\FreebieType;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Defines the list builder for freebies.
 */
class FreebieListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Title');
    $header['type'] = $this->t('Type');
    $header['status'] = $this->t('Status');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\commerce_freebie\Entity\FreebieInterface $entity */
    $freebie_type = FreebieType::load($entity->bundle());

    $row['title'] = $entity->label();
    $row['type'] = $freebie_type->label();
    $row['status'] = $entity->isPublished() && $entity->hasActivePurchasableEntity() ? $this->t('Published') : $this->t('Unpublished');

    return $row + parent::buildRow($entity);
  }

}
