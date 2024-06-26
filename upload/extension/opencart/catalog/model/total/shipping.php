<?php
namespace Opencart\Catalog\Model\Extension\Opencart\Total;
/**
 * Class Shipping
 *
 * @package Opencart\Catalog\Model\Extension\Opencart\Total
 */
class Shipping extends \Opencart\System\Engine\Model {
	/**
	 * @param array $totals
	 * @param array $taxes
	 * @param float $total
	 *
	 * @return void
	 */
	public function getTotal(array &$totals, array &$taxes, float &$total): void {
		if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {
			$totals[] = [
				'extension'  => 'opencart',
				'code'       => 'shipping',
				'title'      => $this->session->data['shipping_method']['name'],
				'value'      => $this->session->data['shipping_method']['cost'],
				'sort_order' => (int)$this->config->get('total_shipping_sort_order')
			];

			if (isset($this->session->data['shipping_method']['tax_class_id'])) {
				$tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);
		 
				foreach ($tax_rates as $tax_rate) {
					if (!isset($taxes[$tax_rate['tax_rate_id']])) {
						$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
					if ($this->config->get('config_tax')) {
						$totals[count($totals) -1]['value'] = $totals[count($totals) -1]['value']  + $taxes[$tax_rate['tax_rate_id']];
					}
				}
			}
		 
		 
			$total += $this->session->data['shipping_method']['cost'];
		}
	}
}
