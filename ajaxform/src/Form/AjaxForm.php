<?php
/*
#
# page loading without refresh using ajax form
#
*/

namespace Drupal\ajaxform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ajaxform\CustomService;

class AjaxForm extends FormBase {
	
	/**
	   * @var AccountInterface $account
	*/
	protected $account;
	protected $innerclass;

	/**
	* Class constructor.
	*/
	
	public function __construct(AccountInterface $account,CustomService $cservice) {
		$this->account = $account;
		$this->innerclass = $cservice;
	}

	/**
	* {@inheritdoc}
	*/
	public static function create(ContainerInterface $container) {
		// Instantiates this form class.
		return new static(
			// Load the service required to construct this class.
			$container->get('current_user'),
			$container->get('ajaxform.say_hello')
		);
	}

	
	//Creating form id
	public function getFormId() {
		return simpleajaxform;
	}
	
	//Creating form fields with ajax callback
	public function buildForm(array $form, FormStateInterface $form_state) {
		//Username field properties
		$form['username'] = array(
			'#type' => 'textfield',
			'#title' => 'UserName',
			'#description' => 'Please enter in a username',
			'#prefix' => '<div id="user-email-result"></div>',
			'#value' => $this->innerclass->sayHello("manikandan"),
        );
		//User email field with ajax callback.
		$form['user_email'] = array(
			'#type' => 'textfield',
			'#title' => 'User or Email',
			'#description' => 'Please enter in a user or email',
			'#prefix' => '<div id="user-email-result"></div>',
			'#value' => $this->account->getEmail(),
			'#ajax' => array(
				'callback' => '::checkUserEmailValidation',
				'effect' => 'fade',
				'event' => 'change',
				'progress' => array(
					'type' => 'throbber',
					'message' => NULL,
				),
			),
        );
		return $form;
	}
	//valiadting the email or username 
	public function checkUserEmailValidation(array $form, FormStateInterface $form_state) {
		$ajax_response = new AjaxResponse();
 
		// Check if User or email exists or not
	    if (user_load_by_name($form_state->getValue(user_email)) || user_load_by_mail($form_state->getValue(user_email))) {
			$text = 'User or Email is exists';
	    } else {
			$text = 'User or Email does not exists';
		}
	    $ajax_response->addCommand(new HtmlCommand('#user-email-result', $text));
	    return $ajax_response;
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state) {
		//Form submission.
	}
}
?>