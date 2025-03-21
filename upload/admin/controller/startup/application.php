<?php
namespace Ventocart\Admin\Controller\Startup;
/**
 * Class Application
 *
 * @package Ventocart\Admin\Controller\Startup
 */
class Application extends \Ventocart\System\Engine\Controller {
	/**
	 * @return void
	 */
	public function index(): void {
		// Url
		$this->registry->set('url', new \Ventocart\System\Library\Url($this->config->get('site_url')));

		// Customer
		$this->registry->set('customer', new \Ventocart\System\Library\Cart\Customer($this->registry));

		// Currency
		$this->registry->set('currency', new \Ventocart\System\Library\Cart\Currency($this->registry));

		// Tax
		$this->registry->set('tax', new \Ventocart\System\Library\Cart\Tax($this->registry));

		if ($this->config->get('config_tax_default') == 'shipping') {
			$this->tax->setShippingAddress((int)$this->config->get('config_country_id'), (int)$this->config->get('config_zone_id'));
		}

		if ($this->config->get('config_tax_default') == 'payment') {
			$this->tax->setPaymentAddress((int)$this->config->get('config_country_id'), (int)$this->config->get('config_zone_id'));
		}

		$this->tax->setStoreAddress((int)$this->config->get('config_country_id'), (int)$this->config->get('config_zone_id'));

		// Weight
		$this->registry->set('weight', new \Ventocart\System\Library\Cart\Weight($this->registry));
		
		// Length
		$this->registry->set('length', new \Ventocart\System\Library\Cart\Length($this->registry));
		
		// Cart
		$this->registry->set('cart', new \Ventocart\System\Library\Cart\Cart($this->registry));
	}
}
