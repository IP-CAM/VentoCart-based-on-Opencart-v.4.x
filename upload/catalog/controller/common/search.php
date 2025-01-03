<?php
namespace Ventocart\Catalog\Controller\Common;
/**
 * Class Search
 *
 * @package Ventocart\Catalog\Controller\Common
 */
class Search extends \Ventocart\System\Engine\Controller {
	/**
	 * @return string
	 */
	public function index(): string {
		$this->load->language('common/search');

		$data['text_search'] = $this->language->get('text_search');

		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}

		$data['language'] = $this->config->get('config_language');
		$data['category_id'] = !empty($this->request->get['path']) ? $this->request->get['path'] : '0';

		return $this->load->view('common/search', $data);
	}
}