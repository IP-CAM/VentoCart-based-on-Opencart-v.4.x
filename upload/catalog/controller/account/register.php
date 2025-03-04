<?php
namespace Ventocart\Catalog\Controller\Account;
/**
 * Class Register
 *
 * @package Ventocart\Catalog\Controller\Account
 */
class Register extends \Ventocart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{

		if ($this->customer->isLogged()) {
			$this->response->redirect($this->url->link('account/account'));
		}


		$this->load->language('account/register');

		$this->document->setTitle($this->language->get('heading_title'));

		$datab['breadcrumbs'] = [];

		$datab['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		];

		$datab['breadcrumbs'][] = [
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account')
		];

		$datab['breadcrumbs'][] = [
			'text' => $this->language->get('text_register'),
			'href' => $this->url->link('account/register')
		];
		$data['breadcrumb'] = $this->load->view('common/breadcrumb', $datab);
		$data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login'));

		$data['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));

		$data['config_file_max_size'] = ((int) $this->config->get('config_file_max_size') * 1024 * 1024);
		$data['config_telephone_display'] = $this->config->get('config_telephone_display');
		$data['config_telephone_required'] = $this->config->get('config_telephone_required');


		$data['register'] = $this->url->link('account/register.register');
		$data['upload'] = $this->url->link('tool/upload');

		$data['customer_groups'] = [];

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$data['customer_groups'][] = $customer_group;
				}
			}
		}

		$data['customer_group_id'] = $this->config->get('config_customer_group_id');

		// Custom Fields
		$data['custom_fields'] = [];

		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields();

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				$data['custom_fields'][] = $custom_field;
			}
		}

		// Captcha
		$this->load->model('setting/extension');

		$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

		if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array) $this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code']);
		} else {
			$data['captcha'] = '';
		}

		$this->load->model('catalog/information');

		$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

		if ($information_info) {
			$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information.info', 'information_id=' . $this->config->get('config_account_id')), $information_info['title']);
		} else {
			$data['text_agree'] = '';
		}

		// Register and logins are done with "Zero-Field Name Nonce" approach, where a nonce  
		// comes in unknown dynamically named input fields which have to be submited with the form and validated.

		$this->session->data['register_token'] = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);

		// We also generate a random field name for the token 
		$this->session->data['register_token_field'] = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 5 + rand(1, 10));

		// We also generate a random id and/or name for the form  
		$data['form_name'] = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 5 + rand(1, 10));

		// email field is a honeypot, name also randomly generated.
		$data['email_field'] = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 5 + rand(1, 10));
		$this->session->data['email_field'] = $data['email_field'];


		$data['register_token_field'] = $this->session->data['register_token_field'];
		$data['register_token'] = $this->session->data['register_token'];


		$data['language'] = $this->config->get('config_language');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/register', $data));
	}

	/**
	 * @return mixed
	 */
	public function register(): void
	{
		$this->load->language('account/register');

		$json = [];

		$keys = [
			'customer_group_id',
			'firstname',
			'lastname',
			'email',
			'telephone',
			'custom_field',
			'password',
			'confirm',
			'agree'
		];

		if (!empty($this->request->post['email'])) { //email field is honeypot
			$json['redirect'] = $this->url->link('account/register', '', true);
		} else {
			$this->request->post['email'] = $this->request->post[$this->session->data['email_field']];
		}
		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}

		if (!isset($this->request->post[$this->session->data['register_token_field']]) || !isset($this->session->data['register_token']) || ($this->session->data['register_token'] != $this->request->post[$this->session->data['register_token_field']])) {
			$json['redirect'] = $this->url->link('account/register', '', true);
		}

		if (!$json) {
			// Customer Group
			if ($this->request->post['customer_group_id']) {
				$customer_group_id = (int) $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = (int) $this->config->get('config_customer_group_id');
			}

			$this->load->model('account/customer_group');

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

			if (!$customer_group_info || !in_array($customer_group_id, (array) $this->config->get('config_customer_group_display'))) {
				$json['error']['warning'] = $this->language->get('error_customer_group');
			}

			if ((oc_strlen($this->request->post['firstname']) < 1) || (oc_strlen($this->request->post['firstname']) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}

			if ((oc_strlen($this->request->post['lastname']) < 1) || (oc_strlen($this->request->post['lastname']) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}

			if ((oc_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			$this->load->model('account/customer');

			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error']['warning'] = $this->language->get('error_exists');
			}

			if ($this->config->get('config_telephone_required') && (oc_strlen($this->request->post['telephone']) < 3) || (oc_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account') {
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
					}
				}
			}

			if ((oc_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (oc_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				$json['error']['password'] = $this->language->get('error_password');
			}

			// Captcha
			$this->load->model('setting/extension');

			$extension_info = $this->model_setting_extension->getExtensionByCode('captcha', $this->config->get('config_captcha'));

			if ($extension_info && $this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array) $this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/' . $extension_info['extension'] . '/captcha/' . $extension_info['code'] . '.validate');

				if ($captcha) {
					$json['error']['captcha'] = $captcha;
				}
			}

			// Agree to terms
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info && !$this->request->post['agree']) {
				$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		if (!$json) {
			// Make sure it is unsubscribed from guest news letter
			$this->load->model('guest/newsletter');
			$this->model_guest_newsletter->unsubscribe($this->request->post['telephone']);

			$customer_id = $this->model_account_customer->addCustomer($this->request->post);

			// Login if requires approval
			if (!$customer_group_info['approval']) {
				$this->customer->login($this->request->post['email'], html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8'));

				// Add customer details into session
				$this->session->data['customer'] = [
					'customer_id' => $customer_id,
					'customer_group_id' => $customer_group_id,
					'firstname' => $this->request->post['firstname'],
					'lastname' => $this->request->post['lastname'],
					'email' => $this->request->post['email'],
					'telephone' => $this->request->post['telephone'],
					'custom_field' => $this->request->post['custom_field']
				];



				// Log the IP info
				$this->model_account_customer->addLogin($this->customer->getId(), $this->request->server['REMOTE_ADDR']);

				// Create customer token
				$this->session->data['customer_token'] = oc_token(26);
			}

			// Clear any previous login attempts for unregistered accounts.
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

			unset($this->session->data['guest']);
			unset($this->session->data['register_token']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_address']);
			unset($this->session->data['shipping_address']);

			$json['redirect'] = $this->url->link('account/success', '', true);
		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}




}

