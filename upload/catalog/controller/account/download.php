<?php
namespace Ventocart\Catalog\Controller\Account;
/**
 * Class Download
 *
 * @package Ventocart\Catalog\Controller\Account
 */
class Download extends \Ventocart\System\Engine\Controller
{
	/**
	 * @return void
	 */
	public function index(): void
	{
		$this->load->language('account/download');

		if (isset($this->request->get['page'])) {
			$page = (int) $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download');

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
			'text' => $this->language->get('text_downloads'),
			'href' => $this->url->link('account/download')
		];
		$data['breadcrumb'] = $this->load->view('common/breadcrumb', $datab);
		$limit = 10;

		$data['downloads'] = [];

		$this->load->model('account/download');

		$results = $this->model_account_download->getDownloads(($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			if (is_file(DIR_DOWNLOAD . $result['filename'])) {
				$size = filesize(DIR_DOWNLOAD . $result['filename']);

				$i = 0;

				$suffix = [
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				];

				while (($size / 1024) > 1) {
					$size = $size / 1024;
					$i++;
				}

				$data['downloads'][] = [
					'order_id' => $result['order_id'],
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'name' => $result['name'],
					'size' => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
					'href' => $this->url->link('account/download.download' . '&download_id=' . $result['download_id'])
				];
			}
		}

		$download_total = $this->model_account_download->getTotalDownloads();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $download_total,
			'page' => $page,
			'limit' => $limit,
			'url' => $this->url->link('account/download' . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($download_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($download_total - $limit)) ? $download_total : ((($page - 1) * $limit) + $limit), $download_total, ceil($download_total / $limit));

		$data['continue'] = $this->url->link('account/account');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/download', $data));
	}

	/**
	 * @return void
	 */
	public function download(): void
	{
		if (isset($this->request->get['download_id'])) {
			$download_id = (int) $this->request->get['download_id'];
		} else {
			$download_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download');

			$this->response->redirect($this->url->link('account/login'));
		}

		$this->load->model('account/download');

		$download_info = $this->model_account_download->getDownload($download_id);

		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (is_file($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					if (ob_get_level()) {
						ob_end_clean();
					}

					readfile($file, 'rb');

					$this->model_account_download->addReport($download_id, $this->request->server['REMOTE_ADDR']);

					exit();
				} else {
					exit(sprintf($this->language->get('error_not_found'), basename($file)));
				}
			} else {
				exit($this->language->get('error_headers_sent'));
			}
		} else {
			$this->response->redirect($this->url->link('account/download'));
		}
	}
}
