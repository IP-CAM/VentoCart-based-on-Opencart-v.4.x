<?php
namespace Ventocart\Catalog\Model\Extension\VentoCart\Module;
/**
 * Class Latest
 *
 * @package Ventocart\Catalog\Model\Extension\VentoCart\Module
 */
class Latest extends \Ventocart\Catalog\Model\Catalog\Product
{
	/**
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getLatest(int $limit): array
	{
		$sql = "SELECT DISTINCT *, `pd`.`name`, `p`.`image`, " . $this->statement['discount'] . ", " . $this->statement['special'] . ", " . $this->statement['reward'] . ", " . $this->statement['review'] . " 
		FROM `" . DB_PREFIX . "product` `p` 
		LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) 
		WHERE `pd`.`language_id` = '" . (int) $this->config->get('config_language_id') . "' 
		AND `p`.`status` = '1' 
		AND `p`.`date_available` <= NOW() 
		ORDER BY `p`.`date_added` DESC LIMIT 0," . (int) $limit;

		$product_data = $this->cache->get('product.' . md5($sql));

		if (!$product_data) {
			$query = $this->db->query($sql);

			$product_data = $query->rows;

			$this->cache->set('product.' . md5($sql), $product_data);
		}

		return (array) $product_data;
	}
}
