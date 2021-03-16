<?php

namespace Drupal\commerce_freebie\Plugin\Commerce\InlineForm;

use Drupal\commerce\Plugin\Commerce\InlineForm\InlineFormBase;
use Drupal\commerce_freebie\FreebieServiceInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an inline form for choosing a different freebie.
 *
 * @CommerceInlineForm(
 *   id = "freebie_selection",
 *   label = @Translation("Freebie selection"),
 * )
 */
class FreebieSelection extends InlineFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The freebie service.
   *
   * @var \Drupal\commerce_freebie\FreebieServiceInterface
   */
  protected $freebieService;

  /**
   * Constructs a new FreebieSelection object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_freebie\FreebieServiceInterface $freebie_service
   *   The freebie service.
   */
  public function __construct(array $configuration, string $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, FreebieServiceInterface $freebie_service) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->freebieService = $freebie_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('commerce_freebie.freebie_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      // The order_id is passed via configuration to avoid serializing the
      // order, which is loaded from scratch in the submit handler to minimize
      // chances of a conflicting save.
      'order_id' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function requiredConfiguration() {
    return ['order_id'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildInlineForm(array $inline_form, FormStateInterface $form_state) {
    $inline_form = parent::buildInlineForm($inline_form, $form_state);

    $candidates = $this->freebieService->getActiveFreebies();
    if (count($candidates) <= 1) {
      return $inline_form;
    }

    $order = $this->entityTypeManager->getStorage('commerce_order')
      ->load($this->configuration['order_id']);
    if (!$order) {
      throw new \RuntimeException('Invalid order_id given to the coupon_redemption inline form.');
    }
    assert($order instanceof OrderInterface);

    $inline_form = [
      '#tree' => TRUE,
      '#theme' => 'commerce_freebie_selection_form',
      '#configuration' => $this->getConfiguration(),
    ] + $inline_form;

    $freebie_view_builder = $this->entityTypeManager->getViewBuilder('commerce_freebie');

    foreach ($candidates as $candidate) {
      $id = $candidate->id();
      $button_name = 'select_button_' . $id;
      $inline_form['freebies'][$id]['rendered_freebie'] = $freebie_view_builder->view($candidate);
      $inline_form['freebies'][$id]['label'] = [
        '#plain_text' => $candidate->label(),
      ];
      $inline_form['freebies'][$id]['select_button'] = [
        '#type' => 'submit',
        '#value' => $this->t('Select'),
        '#name' => $button_name,
        '#ajax' => [
          'callback' => [get_called_class(), 'ajaxRefreshForm'],
          'element' => $inline_form['#parents'],
        ],
        '#weight' => 50,
        '#limit_validation_errors' => [
          $inline_form['#parents'],
        ],
        '#freebie_id' => $id,
        '#submit' => [
          [get_called_class(), 'selectFreebie'],
        ],
        // Simplify ajaxRefresh() by having all triggering elements
        // on the same level.
        '#parents' => array_merge($inline_form['#parents'], [$button_name]),
      ];
    }

    return $inline_form;
  }

  /**
   * Submit callback for the "Select freebie" button.
   */
  public static function selectFreebie(array $form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $parents = array_slice($triggering_element['#parents'], 0, -1);
    $inline_form = NestedArray::getValue($form, $parents);

    $order_storage = \Drupal::entityTypeManager()->getStorage('commerce_order');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $order_storage->load($inline_form['#configuration']['order_id']);
    $freebie_id = (int) $triggering_element['#freebie_id'];
    $order->setData('selected_freebie', $freebie_id);
    $order->save();
    $form_state->setRebuild();
  }

}
