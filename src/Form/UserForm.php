<?php

namespace Drupal\form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;

/**
 * Provides the form for adding countries.
 */
class UserForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'UserForm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {

    $form['civilite'] = array(
      '#type' => 'select',
      '#title' => $this->t('Civilité'),
      '#options' => array(
        'M.' => $this->t('M.'),
        'Mme' => $this->t('Mme'),
        'Mlle' => $this->t('Mlle'),
      ),
      '#required' => TRUE,
    );

    $form['nom'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Nom'),
      '#required' => TRUE,
      '#maxlength' => 100,
      '#default_value' =>  '',
    ];

    $form['email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#default_value' => $this->t('Save'),
    ];

    //$form['#validate'][] = 'studentFormValidate';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $field = $form_state->getValues();

    $fields["nom"] = $field['nom'];
    if (!$form_state->getValue('nom') || empty($form_state->getValue('nom'))) {
      $form_state->setErrorByName('nom', $this->t('Veuillez indiquez votre nom'));
    }

    $fields["email"] = $field['email'];
    if (!$form_state->getValue('email') || empty($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t('Veuillez indiquez votre adresse email'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    try {
      $conn = Database::getConnection();

      $field = $form_state->getValues();

      $fields["civilite"] = $field['civilite'];
      $fields["nom"] = $field['nom'];
      $fields["email"] = $field['email'];

      $conn->insert('newsletter')
        ->fields($fields)->execute();
      \Drupal::messenger()->addMessage($this->t('Utilisateur sauvegardé'));
      $form_state->setRedirect('<front>');
    } catch (\Exception $e) {
      \Drupal::messenger()->addMessage(($e->getMessage()));
      $form_state->setRedirect('<front>');
    }
  }
}
