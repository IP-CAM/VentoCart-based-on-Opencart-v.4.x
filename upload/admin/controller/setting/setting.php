<?php
namespace Ventocart\Admin\Controller\Setting;
/**
 * Class Setting
 *
 * @package Ventocart\Admin\Controller\Setting
 */
class Setting extends \Ventocart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{
		$data = $this->load->language('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_stores'),
			'href' => $this->url->link('setting/store', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('setting/setting', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('setting/setting.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);

		// General
		$data['config_meta_title'] = $this->config->get('config_meta_title');
		$data['config_meta_description'] = $this->config->get('config_meta_description');
		$data['config_meta_keyword'] = $this->config->get('config_meta_keyword');

		$data['store_url'] = HTTP_CATALOG;

		$data['themes'] = [];

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getExtensionsByType('theme');

		foreach ($extensions as $extension) {
			$this->load->language('extension/' . $extension['extension'] . '/theme/' . $extension['code'], 'extension');

			$data['themes'][] = [
				'text' => $this->language->get('extension_heading_title'),
				'value' => $extension['code']
			];
		}

		$data['config_theme'] = $this->config->get('config_theme');

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['config_layout_id'] = $this->config->get('config_layout_id');

		// Store Details
		$data['config_name'] = $this->config->get('config_name');
		$data['config_owner'] = $this->config->get('config_owner');
		$data['config_address'] = $this->config->get('config_address');
		$data['config_geocode'] = $this->config->get('config_geocode');
		$data['config_email'] = $this->config->get('config_email');
		$data['config_telephone'] = $this->config->get('config_telephone');
		$data['config_image'] = $this->config->get('config_image');


		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if (is_file(DIR_IMAGE . html_entity_decode($data['config_image'], ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['config_image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
			$data['thumb'] = $data['placeholder'];
		}

		$data['config_open'] = $this->config->get('config_open');
		$data['config_comment'] = $this->config->get('config_comment');

		$this->load->model('localisation/location');

		$data['locations'] = $this->model_localisation_location->getLocations();

		$data['config_location'] = (array) $this->config->get('config_location');

		// Localisation
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['config_country_id'] = $this->config->get('config_country_id');
		$data['config_zone_id'] = $this->config->get('config_zone_id');
		$data['config_timezone'] = $this->config->get('config_timezone');

		$data['timezones'] = [];

		$timestamp = date_create('now');

		$timezones = timezone_identifiers_list();

		foreach ($timezones as $timezone) {
			date_timezone_set($timestamp, timezone_open($timezone));

			$hour = ' (' . date_format($timestamp, 'P') . ')';

			$data['timezones'][] = [
				'text' => $timezone . $hour,
				'value' => $timezone
			];
		}

		// Language
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['config_language'] = $this->config->get('config_language');
		$data['config_language_admin'] = $this->config->get('config_language_admin');

		// Currency
		$this->load->model('localisation/currency');

		$data['currencies'] = $this->model_localisation_currency->getCurrencies();

		$data['config_currency'] = $this->config->get('config_currency');

		$data['currency_engines'] = [];

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getExtensionsByType('currency');

		foreach ($extensions as $extension) {
			if ($this->config->get('currency_' . $extension['code'] . '_status')) {
				$this->load->language('extension/' . $extension['extension'] . '/currency/' . $extension['code'], 'extension');

				$data['currency_engines'][] = [
					'text' => $this->language->get('extension_heading_title'),
					'value' => $extension['code']
				];
			}
		}

		$data['config_currency_engine'] = $this->config->get('config_currency_engine');
		$data['config_currency_auto'] = $this->config->get('config_currency_auto');

		$this->load->model('localisation/length_class');

		$data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();

		$data['config_length_class_id'] = $this->config->get('config_length_class_id');

		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		$data['config_weight_class_id'] = $this->config->get('config_weight_class_id');

		// Options
		$data['config_product_description_length'] = $this->config->get('config_product_description_length');
		$data['config_pagination'] = $this->config->get('config_pagination');
		$data['config_product_count'] = $this->config->get('config_product_count');

		$data['config_product_infinite_scroll'] = $this->config->get('config_product_infinite_scroll');
		$data['config_pagination_admin'] = $this->config->get('config_pagination_admin');
		$data['config_product_report_status'] = $this->config->get('config_product_report_status');

		// Review
		$data['config_review_status'] = $this->config->get('config_review_status');
		$data['config_review_purchased'] = $this->config->get('config_review_purchased');
		$data['config_review_guest'] = $this->config->get('config_review_guest');

		// CMS
		$data['config_article_description_length'] = $this->config->get('config_article_description_length');
		$data['config_comment_status'] = $this->config->get('config_comment_status');
		$data['config_comment_guest'] = $this->config->get('config_comment_guest');
		$data['config_comment_approve'] = $this->config->get('config_comment_approve');



		$data['config_cookie_id'] = $this->config->get('config_cookie_id');

		$data['config_gdpr_id'] = $this->config->get('config_gdpr_id');
		$data['config_gdpr_limit'] = $this->config->get('config_gdpr_limit');

		// Tax
		$data['config_tax'] = $this->config->get('config_tax');
		$data['config_tax_display_without_tax'] = $this->config->get('config_tax_display_without_tax');
		$data['config_tax_display_amount_tax'] = $this->config->get('config_tax_display_amount_tax');
		$data['config_tax_default'] = $this->config->get('config_tax_default');
		$data['config_tax_customer'] = $this->config->get('config_tax_customer');

		// Customer
		$data['config_customer_online'] = $this->config->get('config_customer_online');
		$data['config_customer_online_expire'] = $this->config->get('config_customer_online_expire');
		$data['config_customer_activity'] = $this->config->get('config_customer_activity');
		$data['config_customer_search'] = $this->config->get('config_customer_search');

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['config_customer_group_id'] = $this->config->get('config_customer_group_id');
		$data['config_customer_group_display'] = (array) $this->config->get('config_customer_group_display');

		$data['config_customer_price'] = $this->config->get('config_customer_price');
		$data['config_telephone_display'] = $this->config->get('config_telephone_display');
		$data['config_telephone_required'] = $this->config->get('config_telephone_required');
		$data['config_customer_2fa'] = $this->config->get('config_customer_2fa');
		$data['config_login_attempts'] = $this->config->get('config_login_attempts');

		$this->load->model('catalog/information');

		$data['informations'] = $this->model_catalog_information->getInformations();

		$data['config_account_id'] = $this->config->get('config_account_id');

		// Checkout
		$data['config_cart_weight'] = $this->config->get('config_cart_weight');
		$data['config_checkout_guest'] = $this->config->get('config_checkout_guest');
		$data['config_show_company_field'] = $this->config->get('config_show_company_field');

		$data['config_checkout_id'] = $this->config->get('config_checkout_id');

		if ($this->config->get('config_invoice_prefix')) {
			$data['config_invoice_prefix'] = $this->config->get('config_invoice_prefix');
		} else {
			$data['config_invoice_prefix'] = 'INV-' . date('Y') . '-00';
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['config_order_status_id'] = $this->config->get('config_order_status_id');
		$data['config_processing_status'] = (array) $this->config->get('config_processing_status');
		$data['config_complete_status'] = (array) $this->config->get('config_complete_status');
		$data['config_fraud_status_id'] = $this->config->get('config_fraud_status_id');

		// Subscription
		$this->load->model('localisation/subscription_status');

		$data['subscription_statuses'] = $this->model_localisation_subscription_status->getSubscriptionStatuses();

		$data['config_subscription_status_id'] = $this->config->get('config_subscription_status_id');
		$data['config_subscription_active_status_id'] = $this->config->get('config_subscription_active_status_id');
		$data['config_subscription_suspended_status_id'] = $this->config->get('config_subscription_suspended_status_id');
		$data['config_subscription_expired_status_id'] = $this->config->get('config_subscription_expired_status_id');
		$data['config_subscription_canceled_status_id'] = $this->config->get('config_subscription_canceled_status_id');
		$data['config_subscription_failed_status_id'] = $this->config->get('config_subscription_failed_status_id');
		$data['config_subscription_denied_status_id'] = $this->config->get('config_subscription_denied_status_id');


		$data['config_image_default_width'] = $this->config->get('config_image_default_width');
		$data['config_image_default_height'] = $this->config->get('config_image_default_height');
		$data['config_image_filetype'] = $this->config->get('config_image_filetype');
		$data['config_image_quality'] = $this->config->get('config_image_quality');

		// Api
		$this->load->model('user/api');

		$data['apis'] = $this->model_user_api->getApis();

		$data['config_api_id'] = $this->config->get('config_api_id');

		// Stock
		$data['config_stock_display'] = $this->config->get('config_stock_display');
		$data['config_stock_warning'] = $this->config->get('config_stock_warning');
		$data['config_stock_checkout'] = $this->config->get('config_stock_checkout');

		$data['config_affiliate_status'] = $this->config->get('config_affiliate_status');
		$data['config_download_status'] = $this->config->get('config_download_status');
		$data['config_subscription_status'] = $this->config->get('config_subscription_status');
		$data['config_giftcard_status'] = $this->config->get('config_giftcard_status');
		$data['config_reward_status'] = $this->config->get('config_reward_status');
		$data['config_blog_status'] = $this->config->get('config_blog_status');

		$data['config_affiliate_group_id'] = $this->config->get('config_affiliate_group_id');
		$data['config_affiliate_approval'] = $this->config->get('config_affiliate_approval');
		$data['config_affiliate_auto'] = (bool) $this->config->get('config_affiliate_auto');
		$data['config_affiliate_commission'] = (float) $this->config->get('config_affiliate_commission');
		$data['config_affiliate_expire'] = $this->config->get('config_affiliate_expire');

		// Affiliate terms
		$data['config_affiliate_id'] = $this->config->get('config_affiliate_id');

		// Returns
		$this->load->model('localisation/return_status');

		$data['return_statuses'] = $this->model_localisation_return_status->getReturnStatuses();

		$data['config_return_status_id'] = $this->config->get('config_return_status_id');

		// Return terms
		$data['config_return_id'] = $this->config->get('config_return_id');

		// Captcha
		$data['config_captcha'] = $this->config->get('config_captcha');

		$this->load->model('setting/extension');

		$data['captchas'] = [];

		// Get a list of installed captchas
		$extensions = $this->model_setting_extension->getExtensionsByType('captcha');

		foreach ($extensions as $extension) {
			$this->load->language('extension/' . $extension['extension'] . '/captcha/' . $extension['code'], 'extension');

			if ($this->config->get('captcha_' . $extension['code'] . '_status')) {
				$data['captchas'][] = [
					'text' => $this->language->get('extension_heading_title'),
					'value' => $extension['code']
				];
			}
		}

		$data['config_captcha_page'] = (array) $this->config->get('config_captcha_page');

		$data['captcha_pages'] = [];

		$data['captcha_pages'][] = [
			'text' => $this->language->get('text_register'),
			'value' => 'register'
		];

		$data['captcha_pages'][] = [
			'text' => $this->language->get('text_guest'),
			'value' => 'guest'
		];

		$data['captcha_pages'][] = [
			'text' => $this->language->get('text_review'),
			'value' => 'review'
		];

		$data['captcha_pages'][] = [
			'text' => $this->language->get('text_comment'),
			'value' => 'comment'
		];

		$data['captcha_pages'][] = [
			'text' => $this->language->get('text_return'),
			'value' => 'returns'
		];

		$data['captcha_pages'][] = [
			'text' => $this->language->get('text_contact'),
			'value' => 'contact'
		];

		// Images
		$data['config_logo'] = $this->config->get('config_logo');

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if (is_file(DIR_IMAGE . html_entity_decode($data['config_logo'], ENT_QUOTES, 'UTF-8'))) {
			$data['logo'] = $this->model_tool_image->resize(html_entity_decode($data['config_logo'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
			$data['logo'] = $data['placeholder'];
		}

		// Image
		$data['config_image_default_width'] = $this->config->get('config_image_default_width');
		$data['config_image_default_height'] = $this->config->get('config_image_default_height');
		$data['config_image_category_width'] = $this->config->get('config_image_category_width');
		$data['config_image_category_height'] = $this->config->get('config_image_category_height');
		$data['config_image_thumb_width'] = $this->config->get('config_image_thumb_width');
		$data['config_image_thumb_height'] = $this->config->get('config_image_thumb_height');
		$data['config_image_popup_width'] = $this->config->get('config_image_popup_width');
		$data['config_image_popup_height'] = $this->config->get('config_image_popup_height');
		$data['config_image_product_width'] = $this->config->get('config_image_product_width');
		$data['config_image_product_height'] = $this->config->get('config_image_product_height');
		$data['config_image_additional_width'] = $this->config->get('config_image_additional_width');
		$data['config_image_additional_height'] = $this->config->get('config_image_additional_height');
		$data['config_image_related_width'] = $this->config->get('config_image_related_width');
		$data['config_image_related_height'] = $this->config->get('config_image_related_height');
		$data['config_image_compare_width'] = $this->config->get('config_image_compare_width');
		$data['config_image_compare_height'] = $this->config->get('config_image_compare_height');
		$data['config_image_article_width'] = $this->config->get('config_image_article_width');
		$data['config_image_article_height'] = $this->config->get('config_image_article_height');
		$data['config_image_topic_width'] = $this->config->get('config_image_topic_width');
		$data['config_image_topic_height'] = $this->config->get('config_image_topic_height');
		$data['config_image_wishlist_width'] = $this->config->get('config_image_wishlist_width');
		$data['config_image_wishlist_height'] = $this->config->get('config_image_wishlist_height');
		$data['config_image_cart_width'] = $this->config->get('config_image_cart_width');
		$data['config_image_cart_height'] = $this->config->get('config_image_cart_height');
		$data['config_image_location_width'] = $this->config->get('config_image_location_width');
		$data['config_image_location_height'] = $this->config->get('config_image_location_height');

		// Mail
		$data['config_mail_engine'] = $this->config->get('config_mail_engine');
		$data['config_mail_parameter'] = $this->config->get('config_mail_parameter');
		$data['config_mail_smtp_hostname'] = $this->config->get('config_mail_smtp_hostname');
		$data['config_mail_smtp_username'] = $this->config->get('config_mail_smtp_username');
		$data['config_mail_smtp_password'] = $this->config->get('config_mail_smtp_password');
		$data['config_mail_smtp_port'] = $this->config->get('config_mail_smtp_port');
		$data['config_mail_smtp_timeout'] = $this->config->get('config_mail_smtp_timeout');
		$data['config_mail_alert'] = (array) $this->config->get('config_mail_alert');

		$data['mail_alerts'] = [];

		$data['mail_alerts'][] = [
			'text' => $this->language->get('text_mail_account'),
			'value' => 'account'
		];

		$data['mail_alerts'][] = [
			'text' => $this->language->get('text_mail_affiliate'),
			'value' => 'affiliate'
		];

		$data['mail_alerts'][] = [
			'text' => $this->language->get('text_mail_order'),
			'value' => 'order'
		];

		$data['mail_alerts'][] = [
			'text' => $this->language->get('text_mail_review'),
			'value' => 'review'
		];

		$data['config_mail_alert_email'] = $this->config->get('config_mail_alert_email');

		// Server
		$data['config_maintenance'] = $this->config->get('config_maintenance');
		$data['config_session_expire'] = $this->config->get('config_session_expire');
		$data['config_session_samesite'] = $this->config->get('config_session_samesite');
		$data['config_seo_url'] = $this->config->get('config_seo_url');
		$data['config_minify_all'] = $this->config->get('config_minify_all');
		$data['config_robots'] = $this->config->get('config_robots');
		$data['config_compression'] = $this->config->get('config_compression');

		// Security
		$data['config_user_2fa'] = $this->config->get('config_user_2fa');
		$data['config_shared'] = $this->config->get('config_shared');

		// Uploads
		$data['config_file_max_size'] = $this->config->get('config_file_max_size');
		$data['config_file_ext_allowed'] = $this->config->get('config_file_ext_allowed');
		$data['config_file_mime_allowed'] = $this->config->get('config_file_mime_allowed');

		// Errors
		$data['config_error_display'] = $this->config->get('config_error_display');
		$data['config_error_log'] = $this->config->get('config_error_log');
		$data['config_error_filename'] = $this->config->get('config_error_filename');

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('setting/setting', $data));
	}

	/**
	 * @return void
	 */
	public function save(): void
	{
		$this->load->language('setting/setting');

		$json = [];



		// Check for minify  $this->config->get('config_theme')


		if (!empty($this->request->post['config_minify_all']) && $this->request->post['config_minify_all']) {
			$this->minifyAssets();

		} else {
			$this->restoreAssets();
		}

		if (!$this->user->hasPermission('modify', 'setting/setting')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}
		// Validation for image file type
		if (empty($this->request->post['config_image_filetype']) || !in_array($this->request->post['config_image_filetype'], ['as_uploaded', 'webp', 'avif', 'jpeg', 'png'])) {
			$json['error']['image_filetype'] = $this->language->get('error_image_filetype');
		}

		// Validation for image quality
		if (empty($this->request->post['config_image_quality']) || !is_numeric($this->request->post['config_image_quality']) || $this->request->post['config_image_quality'] < 1 || $this->request->post['config_image_quality'] > 100) {
			$json['error']['image_quality'] = $this->language->get('error_image_quality');
		}
		if (!$this->request->post['config_meta_title']) {
			$json['error']['meta_title'] = $this->language->get('error_meta_title');
		}

		if (!$this->request->post['config_name']) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		if ((oc_strlen($this->request->post['config_owner']) < 3) || (oc_strlen($this->request->post['config_owner']) > 64)) {
			$json['error']['owner'] = $this->language->get('error_owner');
		}

		if ((oc_strlen($this->request->post['config_address']) < 3) || (oc_strlen($this->request->post['config_address']) > 256)) {
			$json['error']['address'] = $this->language->get('error_address');
		}

		if ((oc_strlen($this->request->post['config_email']) > 96) || !filter_var($this->request->post['config_email'], FILTER_VALIDATE_EMAIL)) {
			$json['error']['email'] = $this->language->get('error_email');
		}

		if (!$this->request->post['config_product_description_length']) {
			$json['error']['product_description_length'] = $this->language->get('error_product_description_length');
		}

		if (!$this->request->post['config_pagination']) {
			$json['error']['pagination'] = $this->language->get('error_pagination');
		}

		if (!$this->request->post['config_pagination_admin']) {
			$json['error']['pagination_admin'] = $this->language->get('error_pagination');
		}

		if (!$this->request->post['config_article_description_length']) {
			$json['error']['article_description_length'] = $this->language->get('error_article_description_length');
		}

		if (!empty($this->request->post['config_customer_group_display']) && !in_array($this->request->post['config_customer_group_id'], $this->request->post['config_customer_group_display'])) {
			$json['error']['customer_group_display'] = $this->language->get('error_customer_group_display');
		}

		if ($this->request->post['config_login_attempts'] < 1) {
			$json['error']['login_attempts'] = $this->language->get('error_login_attempts');
		}

		if (!$this->request->post['config_customer_online_expire']) {
			$json['error']['customer_online_expire'] = $this->language->get('error_customer_online_expire');
		}



		if (!isset($this->request->post['config_processing_status'])) {
			$json['error']['processing_status'] = $this->language->get('error_processing_status');
		}

		if (!isset($this->request->post['config_complete_status'])) {
			$json['error']['complete_status'] = $this->language->get('error_complete_status');
		}

		if (!$this->request->post['config_image_default_width'] || !$this->request->post['config_image_default_height']) {
			$json['error']['image_default'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['config_image_category_width'] || !$this->request->post['config_image_category_height']) {
			$json['error']['image_category'] = $this->language->get('error_image_category');
		}

		if (!$this->request->post['config_image_thumb_width'] || !$this->request->post['config_image_thumb_height']) {
			$json['error']['image_thumb'] = $this->language->get('error_image_thumb');
		}

		if (!$this->request->post['config_image_popup_width'] || !$this->request->post['config_image_popup_height']) {
			$json['error']['image_popup'] = $this->language->get('error_image_popup');
		}

		if (!$this->request->post['config_image_product_width'] || !$this->request->post['config_image_product_height']) {
			$json['error']['image_product'] = $this->language->get('error_image_product');
		}

		if (!$this->request->post['config_image_additional_width'] || !$this->request->post['config_image_additional_height']) {
			$json['error']['image_additional'] = $this->language->get('error_image_additional');
		}

		if (!$this->request->post['config_image_related_width'] || !$this->request->post['config_image_related_height']) {
			$json['error']['image_related'] = $this->language->get('error_image_related');
		}

		if (!$this->request->post['config_image_article_width'] || !$this->request->post['config_image_article_height']) {
			$json['error']['image_article'] = $this->language->get('error_image_cart');
		}

		if (!$this->request->post['config_image_topic_width'] || !$this->request->post['config_image_topic_height']) {
			$json['error']['image_topic'] = $this->language->get('error_image_cart');
		}

		if (!$this->request->post['config_image_compare_width'] || !$this->request->post['config_image_compare_height']) {
			$json['error']['image_compare'] = $this->language->get('error_image_compare');
		}

		if (!$this->request->post['config_image_wishlist_width'] || !$this->request->post['config_image_wishlist_height']) {
			$json['error']['image_wishlist'] = $this->language->get('error_image_wishlist');
		}

		if (!$this->request->post['config_image_cart_width'] || !$this->request->post['config_image_cart_height']) {
			$json['error']['image_cart'] = $this->language->get('error_image_cart');
		}

		if (!$this->request->post['config_image_location_width'] || !$this->request->post['config_image_location_height']) {
			$json['error']['image_location'] = $this->language->get('error_image_location');
		}

		if ($this->request->post['config_user_2fa'] && !$this->request->post['config_mail_engine']) {
			$json['error']['warning'] = $this->language->get('error_user_2fa');
		}

		if (!$this->request->post['config_file_max_size']) {
			$json['error']['file_max_size'] = $this->language->get('error_file_max_size');
		}

		$disallowed = [
			'php',
			'php4',
			'php3'
		];

		$extensions = explode("\n", $this->request->post['config_file_ext_allowed']);

		foreach ($extensions as $extension) {
			if (in_array(trim($extension), $disallowed)) {
				$json['error']['file_ext_allowed'] = $this->language->get('error_extension');

				break;
			}
		}

		$disallowed = [
			'php',
			'php4',
			'php3'
		];

		$mimes = explode("\n", $this->request->post['config_file_mime_allowed']);

		foreach ($mimes as $mime) {
			if (in_array(trim($mime), $disallowed)) {
				$json['error']['file_mime_allowed'] = $this->language->get('error_mime');

				break;
			}
		}

		if (!$this->request->post['config_error_filename']) {
			$json['error']['error_filename'] = $this->language->get('error_log_required');
		} elseif (preg_match('/\.\.[\/\\\]?/', $this->request->post['config_error_filename'])) {
			$json['error']['error_filename'] = $this->language->get('error_log_invalid');
		} elseif (substr($this->request->post['config_error_filename'], strrpos($this->request->post['config_error_filename'], '.')) != '.log') {
			$json['error']['error_filename'] = $this->language->get('error_log_extension');
		}

		if (isset($json['error']) && !isset($json['error']['warning'])) {
			$json['error']['warning'] = $this->language->get('error_warning');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('config', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * @return void
	 */
	public function theme(): void
	{
		if (isset($this->request->get['theme'])) {
			$theme = basename($this->request->get['theme']);
		} else {
			$theme = '';
		}

		$this->load->model('setting/extension');

		$extension_info = $this->model_setting_extension->getExtensionByCode('theme', $theme);

		if ($extension_info) {
			$this->response->setOutput(HTTP_CATALOG . 'extension/' . $extension_info['extension'] . '/admin/view/image/' . $extension_info['code'] . '.png');
		} else {
			$this->response->setOutput(HTTP_CATALOG . 'image/no_image.png');
		}
	}


	private function autoloader(): void
	{
		require_once DIR_STORAGE . 'vendor/minify/src/Minify.php';
		require_once DIR_STORAGE . 'vendor/minify/src/CSS.php';
		require_once DIR_STORAGE . 'vendor/minify/src/JS.php';
		require_once DIR_STORAGE . 'vendor/minify/src/Exception.php';
		require_once DIR_STORAGE . 'vendor/minify/src/ConverterInterface.php';
		require_once DIR_STORAGE . 'vendor/minify/src/Converter.php';
	}

	public function minifyAssets(): void
	{
		$this->autoloader();
		$files = $this->findAssets('minify');
		foreach ($files as $file) {
			$this->processFile($file, 'minify');
		}
	}

	public function restoreAssets(): void
	{
		$files = $this->findAssets('restore');
		foreach ($files as $file) {
			$this->processFile($file, 'restore');
		}
	}

	private function findAssets(string $mode): array
	{
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(DIR_VENTOCART));
		$assets = [];
		foreach ($iterator as $file) {


			if (strpos($file->getPath(), '/assets/') !== false || strpos($file->getPath(), '/view/') !== false && in_array($file->getExtension(), ['css', 'js'])) {
				if ($mode === 'minify') {
					$assets[] = $file->getPathname();
				} elseif ($mode === 'restore' && file_exists($file->getPathname() . '_unminified')) {
					$assets[] = $file->getPathname();
				}
			}
		}
		return $assets;
	}

	private function processFile(string $filePath, string $action): void
	{

		$extension = pathinfo($filePath, PATHINFO_EXTENSION);
		if ($action === 'minify' && file_exists($filePath) && is_file($filePath)) {
			if (strpos($filePath, "_unminified") !== false) {
				return;
			}
			$unminifiedPath = $filePath . '_unminified';
			if (!file_exists($unminifiedPath)) {
				rename($filePath, $unminifiedPath);

				$content = file_get_contents($unminifiedPath);

				if ($extension === 'css') {
					$minifier = new \MatthiasMullie\Minify\CSS();
				} elseif ($extension === 'js') {
					$minifier = new \MatthiasMullie\Minify\JS();
				} else {
					return;
				}

				$minifiedContent = $minifier->add($content)->minify();
				file_put_contents($filePath, $minifiedContent);
			}
		} elseif ($action === 'restore') {
			$unminifiedPath = $filePath . '_unminified';
			if (file_exists($unminifiedPath)) {
				unlink($filePath);
				rename($unminifiedPath, $filePath);
			}
		}
	}

}
