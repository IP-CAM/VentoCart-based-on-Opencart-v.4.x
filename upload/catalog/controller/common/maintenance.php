<?php
namespace Ventocart\Catalog\Controller\Common;
/**
 * Class Maintenance
 *
 * @package Ventocart\Catalog\Controller\Common
 */
class Maintenance extends \Ventocart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{
		$this->load->language('common/maintenance');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['SERVER_PROTOCOL'] == 'HTTP/1.1') {
			$this->response->addHeader('HTTP/1.1 503 Service Unavailable');
		} else {
			$this->response->addHeader('HTTP/1.0 503 Service Unavailable');
		}

		$this->response->addHeader('Retry-After: 3600');

		$datab['breadcrumbs'] = [];

		$datab['breadcrumbs'][] = [
			'text' => $this->language->get('text_maintenance'),
			'href' => $this->url->link('common/maintenance', 'language=' . $this->config->get('config_language'))
		];
		$data['breadcrumb'] = $this->load->view('common/breadcrumb', $datab);
		$data['message'] = $this->language->get('text_message');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('common/maintenance', $data));
	}
}
