<?php
namespace Ventocart\Catalog\Controller\Extension\Ventocart\Total;
/**
 * Class Coupon
 *
 * @package Ventocart\Catalog\Controller\Extension\Ventocart\Total
 */
class Coupon extends \Ventocart\System\Engine\Controller {
	/**
	 * @return string
	 */
	public function index(): string {
		if ($this->config->get('total_coupon_status')) {
			$this->load->language('extension/ventocart/total/coupon');

			$data['save'] = $this->url->link('extension/ventocart/total/coupon.save', 'language=' . $this->config->get('config_language'), true);
			$data['list'] = $this->url->link('checkout/cart.list', 'language=' . $this->config->get('config_language'), true);

			if (isset($this->session->data['coupon'])) {
				$data['coupon'] = $this->session->data['coupon'];
			} else {
				$data['coupon'] = '';
			}

			return $this->load->view('extension/ventocart/total/coupon', $data);
		}

		return '';
	}

	/**
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/ventocart/total/coupon');

		$json = [];

		if (isset($this->request->post['coupon'])) {
			$coupon = $this->request->post['coupon'];
		} else {
			$coupon = '';
		}

		if (!$this->config->get('total_coupon_status')) {
			$json['error'] = $this->language->get('error_status');
		}

		if ($coupon) {
			$this->load->model('marketing/coupon');

			$coupon_info = $this->model_marketing_coupon->getCoupon($coupon);

			if (!$coupon_info) {
				$json['error'] = $this->language->get('error_coupon');
			}
		}

		if (!$json) {
			if ($coupon) {
				$json['success'] = $this->language->get('text_success');

				$this->session->data['coupon'] = $coupon;
			} else {
				$json['success'] = $this->language->get('text_remove');

				unset($this->session->data['coupon']);
			}

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
