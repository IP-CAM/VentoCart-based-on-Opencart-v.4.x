<?php
namespace Ventocart\Catalog\Controller\Extension\VentoCart\Module;
class MostViewed extends \Ventocart\System\Engine\Controller
{
	public function index(array $setting): mixed
	{
		$this->load->language('extension/ventocart/module/mostviewed');

		$data['axis'] = $setting['axis'];
		$api_output = $this->customer->isApiClient();
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$data['products'] = [];


		$results = $this->model_catalog_product->getMostViewed($setting['timeframe'], $setting['limit']);

		if ($results) {

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float) $result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}
				if (!isset($result['description']))
					$result['description'] = '';
				$product_data = [
					'product_id' => $result['product_id'],
					'thumb' => $image,
					'setWidth' => $setting['width'],
					'date_added' => $result['date_added'],
					'new' => (strtotime($result['date_added']) >= time() - 604800) ? true : false,
					'setHeight' => $setting['height'],
					'name' => $result['name'],
					'description' => oc_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('config_product_description_length')) . '..',
					'price' => $price,
					'special' => $special,
					'tax' => $tax,
					'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating' => $result['rating'],
					'href' => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $result['product_id'])
				];

				$data['products'][] = $this->load->controller('product/thumb', $product_data);
			}
			if (!$api_output) {
				return $this->load->view('extension/ventocart/module/mostviewed', $data);
			} else {
				$data['module'] = "MostviewedProducts";
				$data['lang_values'] = $this->language->loadForAPI('extension/ventocart/module/mostviewed');
				return $data;
			}
		} else {
			return '';
		}
	}
}
