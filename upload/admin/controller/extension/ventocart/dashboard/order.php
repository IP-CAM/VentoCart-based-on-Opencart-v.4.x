<?php
namespace Ventocart\Admin\Controller\Extension\Ventocart\Dashboard;
/**
 * Class Order
 *
 * @package Ventocart\Admin\Controller\Extension\Ventocart\Dashboard
 */
class Order extends \Ventocart\System\Engine\Controller {
	/**
	 * @return void
	 */
	public function index(): void {
		$this->load->language('extension/ventocart/dashboard/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/ventocart/dashboard/order', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/ventocart/dashboard/order.save', 'user_token=' . $this->session->data['user_token']);

		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard');

		$data['dashboard_order_width'] = $this->config->get('dashboard_order_width');

		$data['columns'] = [];
		
		for ($i = 3; $i <= 12; $i++) {
			$data['columns'][] = $i;
		}

		$data['dashboard_order_status'] = $this->config->get('dashboard_order_status');
		$data['dashboard_order_sort_order'] = $this->config->get('dashboard_order_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/ventocart/dashboard/order_form', $data));
	}

	/**
	 * @return void
	 */
	public function save(): void {
		$this->load->language('extension/ventocart/dashboard/order');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/ventocart/dashboard/order')) {
			$json['error']  = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('dashboard_order', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * @return string
	 */
	public function dashboard(): string {
		$this->load->language('extension/ventocart/dashboard/order');

		// Total Orders
		$this->load->model('sale/order');

		$today = $this->model_sale_order->getTotalOrders(['filter_date_added' => date('Y-m-d', strtotime('-1 day'))]);

		$yesterday = $this->model_sale_order->getTotalOrders(['filter_date_added' => date('Y-m-d', strtotime('-2 day'))]);

		$difference = $today - $yesterday;

		if ($difference && $today) {
			$data['percentage'] = round(($difference / $today) * 100);
		} else {
			$data['percentage'] = 0;
		}

		$order_total = $this->model_sale_order->getTotalOrders();

		if ($order_total > 1000000000000) {
			$data['total'] = round($order_total / 1000000000000, 1) . 'T';
		} elseif ($order_total > 1000000000) {
			$data['total'] = round($order_total / 1000000000, 1) . 'B';
		} elseif ($order_total > 1000000) {
			$data['total'] = round($order_total / 1000000, 1) . 'M';
		} elseif ($order_total > 1000) {
			$data['total'] = round($order_total / 1000, 1) . 'K';
		} else {
			$data['total'] = $order_total;
		}

		$data['order'] = $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token']);

		$data['user_token'] = $this->session->data['user_token'];

		return $this->load->view('extension/ventocart/dashboard/order_info', $data);
	}
}
