<?php
namespace Ventocart\Catalog\Controller\Account;
/**
 * Class Affiliate
 *
 * @package Ventocart\Catalog\Controller\Account
 */
class Affiliate extends \Ventocart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{
		$this->load->language('account/affiliate');
		if (!$this->customer->isLogged()) {
			$this->customer->logout();

			$this->session->data['redirect'] = $this->url->link('account/affiliate');

			$this->response->redirect($this->url->link('account/login'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$datab['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));

		$datab['config_file_max_size'] = ((int) $this->config->get('config_file_max_size') * 1024 * 1024);

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
			'text' => $this->language->get('text_affiliate'),
			'href' => $this->url->link('account/affiliate')
		];
		$data['breadcrumb'] = $this->load->view('common/breadcrumb', $datab);
		$data['save'] = $this->url->link('account/affiliate.save');
		$data['upload'] = $this->url->link('tool/upload');

		$this->load->model('account/affiliate');

		$affiliate_info = $this->model_account_affiliate->getAffiliate($this->customer->getId());

		if (!empty($affiliate_info)) {
			$data['company'] = $affiliate_info['company'];
		} else {
			$data['company'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['website'] = $affiliate_info['website'];
		} else {
			$data['website'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['tax'] = $affiliate_info['tax'];
		} else {
			$data['tax'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['payment_method'] = $affiliate_info['payment_method'];
		} else {
			$data['payment_method'] = 'cheque';
		}

		if (!empty($affiliate_info)) {
			$data['cheque'] = $affiliate_info['cheque'];
		} else {
			$data['cheque'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['paypal'] = $affiliate_info['paypal'];
		} else {
			$data['paypal'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['bank_name'] = $affiliate_info['bank_name'];
		} else {
			$data['bank_name'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['bank_branch_number'] = $affiliate_info['bank_branch_number'];
		} else {
			$data['bank_branch_number'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['bank_swift_code'] = $affiliate_info['bank_swift_code'];
		} else {
			$data['bank_swift_code'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['bank_account_name'] = $affiliate_info['bank_account_name'];
		} else {
			$data['bank_account_name'] = '';
		}

		if (!empty($affiliate_info)) {
			$data['bank_account_number'] = $affiliate_info['bank_account_number'];
		} else {
			$data['bank_account_number'] = '';
		}

		// Custom Fields
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields((int) $this->config->get('config_customer_group_id'));

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'affiliate') {
				$data['custom_fields'][] = $custom_field;
			}
		}

		if (!empty($affiliate_info)) {
			$data['affiliate_custom_field'] = $affiliate_info['custom_field'];
		} else {
			$data['affiliate_custom_field'] = [];
		}

		$affiliate_info = $this->model_account_affiliate->getAffiliate($this->customer->getId());

		if (!$affiliate_info && $this->config->get('config_affiliate_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_affiliate_id'));

			if ($information_info) {
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information.info' . '&information_id=' . $this->config->get('config_affiliate_id')), $information_info['title']);
			} else {
				$data['text_agree'] = '';
			}
		} else {
			$data['text_agree'] = '';
		}

		$data['back'] = $this->url->link('account/account');

		$data['language'] = $this->config->get('config_language');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/affiliate', $data));
	}

	/**
	 * @return void
	 */
	public function save(): void
	{
		$this->load->language('account/affiliate');

		$json = [];

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/affiliate');

			$json['redirect'] = $this->url->link('account/login', true);
		}

		if (!$this->config->get('config_affiliate_status')) {
			$json['redirect'] = $this->url->link('account/account', true);
		}

		$keys = [
			'payment_method',
			'cheque',
			'paypal',
			'bank_account_name',
			'bank_account_number',
			'agree'
		];

		foreach ($keys as $key) {
			if (!isset($this->request->post[$key])) {
				$this->request->post[$key] = '';
			}
		}

		if (!$json) {
			// Payment validation
			if (empty($this->request->post['payment_method'])) {
				$json['error']['payment_method'] = $this->language->get('error_payment_method');
			}

			if ($this->request->post['payment_method'] == 'cheque' && !$this->request->post['cheque']) {
				$json['error']['cheque'] = $this->language->get('error_cheque');
			} elseif ($this->request->post['payment_method'] == 'paypal' && ((oc_strlen($this->request->post['paypal']) > 96) || !filter_var($this->request->post['paypal'], FILTER_VALIDATE_EMAIL))) {
				$json['error']['paypal'] = $this->language->get('error_paypal');
			} elseif ($this->request->post['payment_method'] == 'bank') {
				if ($this->request->post['bank_account_name'] == '') {
					$json['error']['bank_account_name'] = $this->language->get('error_bank_account_name');
				}

				if ($this->request->post['bank_account_number'] == '') {
					$json['error']['bank_account_number'] = $this->language->get('error_bank_account_number');
				}
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields((int) $this->config->get('config_customer_group_id'));

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'affiliate') {
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !preg_match(html_entity_decode($custom_field['validation'], ENT_QUOTES, 'UTF-8'), $this->request->post['custom_field'][$custom_field['custom_field_id']])) {
						$json['error']['custom_field_' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_regex'), $custom_field['name']);
					}
				}
			}

			// Validate agree only if customer not already an affiliate
			$this->load->model('account/affiliate');

			$affiliate_info = $this->model_account_affiliate->getAffiliate($this->customer->getId());

			if (!$affiliate_info) {
				$this->load->model('catalog/information');

				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_affiliate_id'));

				if ($information_info && !$this->request->post['agree']) {
					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}
		}

		if (!$json) {
			if (!$affiliate_info) {
				$this->model_account_affiliate->addAffiliate($this->customer->getId(), $this->request->post);
			} else {
				$this->model_account_affiliate->editAffiliate($this->customer->getId(), $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$json['redirect'] = $this->url->link('account/account', true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}