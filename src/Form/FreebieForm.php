<?php

namespace Drupal\commerce_freebie\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the entity form for freebies.
 */
class FreebieForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\commerce_freebie\Entity\FreebieInterface $freebie */
    $freebie = $this->getEntity();
    $freebie->save();
    $this->messenger()->addMessage($this->t('The freebie %label has been successfully saved.', ['%label' => $freebie->label()]));
    $form_state->setRedirect('entity.commerce_freebie.collection');
  }

}
