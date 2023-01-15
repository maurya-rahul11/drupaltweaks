<?php

namespace Drupal\drupal_utility\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Drupal Utility settings for this site.
 */
class DateTimeConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'drupal_utility_date_time_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['drupal_utility.date_time_config'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Country'),
      '#default_value' => $this->config('drupal_utility.date_time_config')->get('country'),
    ];

    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#default_value' => $this->config('drupal_utility.date_time_config')->get('city'),
    ];

    $form['timezone'] = [
      '#type' => 'select',
      '#title' => $this->t('Timezone'),
      '#default_value' => $this->config('drupal_utility.date_time_config')->get('timezone'),
      '#options' => [
        '' => $this->t('--Select--'),
        'America/Chicago' => $this->t('Chicago'),
        'America/New_York' => $this->t('New York'),
        'Asia/Tokyo' => $this->t('Tokyo'),
        'Asia/Dubai' => $this->t('Dubai'),
        'Asia/Kolkata' => $this->t('Kolkata'),
        'Europe/Amsterdam' => $this->t('Amsterdam'),
        'Europe/Oslo' => $this->t('Oslo'),
        'Europe/London' => $this->t('London'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('country'))) {
      $form_state->setErrorByName('country', $this->t('Country is required.'));
    }

    if (empty($form_state->getValue('city'))) {
      $form_state->setErrorByName('city', $this->t('City is required.'));
    }

    if (empty($form_state->getValue('timezone'))) {
      $form_state->setErrorByName('timezone', $this->t('Timezone is required.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('drupal_utility.date_time_config')
      ->set('country', $form_state->getValue('country'))
      ->set('city', $form_state->getValue('city'))
      ->set('timezone', $form_state->getValue('timezone'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
