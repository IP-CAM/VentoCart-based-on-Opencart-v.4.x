<?php
namespace Ventocart\Catalog\Controller\Account;
/**
 * Class Reward
 *
 * @package Ventocart\Catalog\Controller\Account
 */
class Reward extends \Ventocart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{
		$this->load->language('account/reward');

		if (isset($this->request->get['page'])) {
			$page = (int) $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/reward');

			$this->response->redirect($this->url->link('account/login'));
		}

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
			'text' => $this->language->get('text_reward'),
			'href' => $this->url->link('account/reward')
		];
		$data['breadcrumb'] = $this->load->view('common/breadcrumb', $datab);
		$limit = 10;

		$data['rewards'] = [];

		$filter_data = [
			'sort' => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		];

		$this->load->model('account/reward');

		$results = $this->model_account_reward->getRewards($filter_data);

		foreach ($results as $result) {
			$data['rewards'][] = [
				'order_id' => $result['order_id'],
				'points' => $result['points'],
				'description' => $result['description'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href' => $this->url->link('account/order.info', 'order_id=' . $result['order_id'])
			];
		}

		$reward_total = $this->model_account_reward->getTotalRewards();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $reward_total,
			'page' => $page,
			'limit' => $limit,
			'url' => $this->url->link('account/reward', 'page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($reward_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($reward_total - $limit)) ? $reward_total : ((($page - 1) * $limit) + $limit), $reward_total, ceil($reward_total / $limit));

		$data['total'] = (int) $this->customer->getRewardPoints();

		$data['continue'] = $this->url->link('account/account');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/reward', $data));
	}
}