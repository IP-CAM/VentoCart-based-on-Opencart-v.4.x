<?php
namespace Ventocart\Catalog\Controller\Account;
/**
 * Class Account
 *
 * @package Ventocart\Catalog\Controller\Account
 */
class Account extends \Ventocart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{
		$this->load->language('account/account');

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account');

			$this->response->redirect($this->url->link('account/login'));
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$datab['breadcrumbs'] = [];

		$datab['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		];

		$datab['breadcrumbs'][] = [
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account')
		];

		$data['breadcrumb'] = $this->load->view('common/breadcrumb', $datab);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('account/wishlist');
		$results = $this->model_account_wishlist->getWishlist();

		$results = $this->model_account_wishlist->getWishlist();
		$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), count($results));
		$data['edit'] = $this->url->link('account/edit');
		$data['password'] = $this->url->link('account/password');
		$data['address'] = $this->url->link('account/address');
		$data['payment_method'] = $this->url->link('account/payment_method');
		$data['wishlist'] = $this->url->link('account/wishlist');
		$data['order'] = $this->url->link('account/order');
		$data['subscription'] = $this->url->link('account/subscription');
		$data['download'] = $this->url->link('account/download');
		$data['giftcard_link'] = $this->url->link('giftcards/account');


		if ($this->config->get('total_reward_status')) {
			$data['reward'] = $this->url->link('account/reward');
		} else {
			$data['reward'] = '';
		}

		$data['return'] = $this->url->link('account/returns');
		$data['transaction'] = $this->url->link('account/transaction');
		$data['newsletter'] = $this->url->link('account/newsletter');

		if ($this->config->get('config_affiliate_status')) {
			$data['affiliate'] = $this->url->link('account/affiliate');

			$this->load->model('account/affiliate');

			$affiliate_info = $this->model_account_affiliate->getAffiliate($this->customer->getId());

			if ($affiliate_info) {
				$data['tracking'] = $this->url->link('account/tracking');
			} else {
				$data['tracking'] = '';
			}
		} else {
			$data['affiliate'] = '';
		}

		$data['config_affiliate_status'] = $this->config->get('config_affiliate_status');
		$data['config_download_status'] = $this->config->get('config_download_status');
		$data['config_subscription_status'] = $this->config->get('config_subscription_status');
		$data['config_giftcard_status'] = $this->config->get('config_giftcard_status');
		$data['config_reward_status'] = $this->config->get('config_reward_status');
		$data['config_blog_status'] = $this->config->get('config_blog_status');

		$this->response->setOutput($this->load->view('account/account', $data));
	}
}
