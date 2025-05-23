<?php
namespace Ventocart\Admin\Model\Design;
/**
 *  Class Banner
 *
 * @package Ventocart\Admin\Model\Design
 */
class Banner extends \Ventocart\System\Engine\Model
{
	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function addBanner(array $data): int
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "banner` SET `name` = '" . $this->db->escape((string) $data['name']) . "', `status` = '" . (bool) (isset($data['status']) ? $data['status'] : 0) . "'");

		$banner_id = $this->db->getLastId();

		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image) {
					$this->db->query("
					INSERT INTO `" . DB_PREFIX . "banner_image` 
					SET 
						`banner_id` = '" . (int) $banner_id . "',
						`language_id` = '" . (int) $language_id . "',
						`title` = '" . $this->db->escape($banner_image['title']) . "',
				        `description` = '" . $this->db->escape($banner_image['description']) . "',
						`link` = '" . $this->db->escape($banner_image['link']) . "',
						`image` = '" . $this->db->escape($banner_image['image']) . "',
						`sort_order` = '" . (int) $banner_image['sort_order'] . "',
				 
				");
				}
			}
		}

		return $banner_id;
	}

	/**
	 * @param int   $banner_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function editBanner(int $banner_id, array $data): void
	{
		// Update the banner details
		$this->db->query("
			UPDATE `" . DB_PREFIX . "banner` 
			SET 
				`name` = '" . $this->db->escape((string) $data['name']) . "',
				`status` = '" . (int) (isset($data['status']) ? $data['status'] : 0) . "' 
			WHERE 
				`banner_id` = '" . (int) $banner_id . "'
		");

		// Delete existing banner images
		$this->db->query("
			DELETE FROM `" . DB_PREFIX . "banner_image` 
			WHERE `banner_id` = '" . (int) $banner_id . "'
		");

		// Insert new banner images if provided
		if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $images) {
				foreach ($images as $banner_image) {
					$this->db->query("
						INSERT INTO `" . DB_PREFIX . "banner_image` 
						SET 
							`banner_id` = '" . (int) $banner_id . "',
							`language_id` = '" . (int) $language_id . "',
							`title` = '" . $this->db->escape($banner_image['title']) . "',
							`link` = '" . $this->db->escape($banner_image['link']) . "',
							`image` = '" . $this->db->escape($banner_image['image']) . "',
							`sort_order` = '" . (int) $banner_image['sort_order'] . "',
							`description` = '" . $this->db->escape(isset($banner_image['description']) ? $banner_image['description'] : '') . "'
					");
				}
			}
		}
	}

	/**
	 * @param int $banner_id
	 *
	 * @return void
	 */
	public function deleteBanner(int $banner_id): void
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "banner` WHERE `banner_id` = '" . (int) $banner_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "banner_image` WHERE `banner_id` = '" . (int) $banner_id . "'");
	}

	/**
	 * @param int $banner_id
	 *
	 * @return array
	 */
	public function getBanner(int $banner_id): array
	{
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "banner` WHERE `banner_id` = '" . (int) $banner_id . "'");

		return $query->row;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getBanners(array $data = []): array
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "banner`";

		$sort_data = [
			'name',
			'status'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `name`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * @param int $banner_id
	 *
	 * @return array
	 */
	public function getImages(int $banner_id): array
	{
		$banner_image_data = [];

		$banner_image_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "banner_image` WHERE `banner_id` = '" . (int) $banner_id . "' ORDER BY `sort_order` ASC");

		foreach ($banner_image_query->rows as $banner_image) {
			$banner_image_data[$banner_image['language_id']][] = [
				'title' => $banner_image['title'],
				'description' => $banner_image['description'],
				'link' => $banner_image['link'],
				'image' => $banner_image['image'],
				'sort_order' => $banner_image['sort_order']
			];
		}

		return $banner_image_data;
	}

	/**
	 * @return int
	 */
	public function getTotalBanners(): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "banner`");

		return (int) $query->row['total'];
	}
}
