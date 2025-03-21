<?php
namespace Ventocart\Admin\Model\Cms;
/**
 * Class Article
 *
 * @package Ventocart\Admin\Model\Cms
 */
class Article extends \Ventocart\System\Engine\Model
{
	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function addArticle(array $data): int
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "article` SET `topic_id` = '" . (int) $data['topic_id'] . "', `author` = '" . $this->db->escape($data['author']) . "', `status` = '" . (bool) (isset($data['status']) ? $data['status'] : 0) . "', `date_added` = NOW(), `date_modified` = NOW()");

		$article_id = $this->db->getLastId();

		foreach ($data['article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "article_description` SET `article_id` = '" . (int) $article_id . "', `language_id` = '" . (int) $language_id . "', `image` = '" . $this->db->escape($value['image']) . "', `name` = '" . $this->db->escape($value['name']) . "', `description` = '" . $this->db->escape($value['description']) . "', `tag` = '" . $this->db->escape($value['tag']) . "', `meta_title` = '" . $this->db->escape($value['meta_title']) . "', `meta_description` = '" . $this->db->escape($value['meta_description']) . "', `meta_keyword` = '" . $this->db->escape($value['meta_keyword']) . "'");
		}


		foreach ($data['article_seo_url'] as $language_id => $keyword) {

			$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET   `language_id` = '" . (int) $language_id . "', `key` = 'article_id', `value`= '" . (int) $article_id . "', `keyword` = '" . $this->db->escape($keyword) . "'");

		}



		$this->cache->delete('article');

		return $article_id;
	}

	/**
	 * @param int   $article_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function editArticle(int $article_id, array $data): void
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "article` SET `topic_id` = '" . (int) $data['topic_id'] . "', `author` = '" . $this->db->escape($data['author']) . "', `status` = '" . (bool) (isset($data['status']) ? $data['status'] : 0) . "', `date_modified` = NOW() WHERE `article_id` = '" . (int) $article_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "article_description` WHERE `article_id` = '" . (int) $article_id . "'");

		foreach ($data['article_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "article_description` SET `article_id` = '" . (int) $article_id . "', `language_id` = '" . (int) $language_id . "', `image` = '" . $this->db->escape($value['image']) . "', `name` = '" . $this->db->escape($value['name']) . "', `description` = '" . $this->db->escape($value['description']) . "', `tag` = '" . $this->db->escape($value['tag']) . "', `meta_title` = '" . $this->db->escape($value['meta_title']) . "', `meta_description` = '" . $this->db->escape($value['meta_description']) . "', `meta_keyword` = '" . $this->db->escape($value['meta_keyword']) . "'");
		}




		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `key` = 'article_id' AND `value` = '" . (int) $article_id . "'");

		foreach ($data['article_seo_url'] as $language_id => $keyword) {

			$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET  `language_id` = '" . (int) $language_id . "', `key` = 'article_id', `value` = '" . (int) $article_id . "', `keyword` = '" . $this->db->escape($keyword) . "'");

		}



		$this->cache->delete('article');
	}

	/**
	 * @param int $article_id
	 *
	 * @return void
	 */
	public function deleteArticle(int $article_id): void
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "article` WHERE `article_id` = '" . (int) $article_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "article_comment` WHERE `article_id` = '" . (int) $article_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "article_description` WHERE `article_id` = '" . (int) $article_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `key` = 'article_id' AND `value` = '" . (int) $article_id . "'");

		$this->cache->delete('article');
	}

	/**
	 * @param int $article_id
	 *
	 * @return array
	 */
	public function getArticle(int $article_id): array
	{
		$sql = "SELECT DISTINCT * FROM `" . DB_PREFIX . "article` `a` LEFT JOIN `" . DB_PREFIX . "article_description` `ad` ON (`a`.`article_id` = `ad`.`article_id`) WHERE `a`.`article_id` = '" . (int) $article_id . "' AND `ad`.`language_id` = '" . (int) $this->config->get('config_language_id') . "'";

		$article_data = $this->cache->get('article.' . md5($sql));

		if (!$article_data) {
			$query = $this->db->query($sql);

			$article_data = $query->row;

			$this->cache->set('article.' . md5($sql), $article_data);
		}

		return $article_data;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getArticles(array $data = []): array
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "article` `a` LEFT JOIN `" . DB_PREFIX . "article_description` `ad` ON (`a`.`article_id` = `ad`.`article_id`) WHERE `ad`.`language_id` = '" . (int) $this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(`ad`.`name`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_name'])) . "'";
		}

		$sort_data = [
			'ad.name',
			'a.date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `a`.`date_added`";
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

		$article_data = $this->cache->get('article.' . md5($sql));

		if (!$article_data) {
			$query = $this->db->query($sql);

			$article_data = $query->rows;

			$this->cache->set('article.' . md5($sql), $article_data);
		}

		return $article_data;
	}

	/**
	 * @param int $article_id
	 *
	 * @return array
	 */
	public function getDescriptions(int $article_id): array
	{
		$article_description_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "article_description` WHERE `article_id` = '" . (int) $article_id . "'");

		foreach ($query->rows as $result) {
			$article_description_data[$result['language_id']] = [
				'image' => $result['image'],
				'name' => $result['name'],
				'description' => $result['description'],
				'tag' => $result['tag'],
				'meta_title' => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword' => $result['meta_keyword']
			];
		}

		return $article_description_data;
	}

	/**
	 * @param int $article_id
	 *
	 * @return array
	 */
	public function getSeoUrls(int $article_id): array
	{
		$article_seo_url_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `key` = 'article_id' AND `value` = '" . (int) $article_id . "'");

		foreach ($query->rows as $result) {
			$article_seo_url_data[$result['language_id']] = $result['keyword'];
		}

		return $article_seo_url_data;
	}





	/**
	 * @return int
	 */
	public function getTotalArticles(): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "article`");

		return (int) $query->row['total'];
	}

	/**
	 * @param int $article_comment_id
	 * @param bool $status
	 *
	 * @return void
	 */
	public function editCommentStatus(int $article_comment_id, bool $status): void
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "article_comment` SET `status` = '" . (bool) $status . "' WHERE `article_comment_id` = '" . (int) $article_comment_id . "'");
	}

	/**
	 * @param int $article_comment_id
	 *
	 * @return void
	 */
	public function deleteComment(int $article_comment_id): void
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "article_comment` WHERE `article_comment_id` = '" . (int) $article_comment_id . "'");
	}

	/**
	 * @param int $article_comment_id
	 *
	 * @return array
	 */
	public function getComment(int $article_comment_id): array
	{
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "article_comment` WHERE `article_comment_id` = '" . (int) $article_comment_id . "'");

		return $query->row;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getComments(array $data = []): array
	{
		$sql = "SELECT *, `ac`.`status`, `ac`.`date_added` FROM `" . DB_PREFIX . "article_comment` `ac` LEFT JOIN `" . DB_PREFIX . "article` `a` ON (`ac`.`article_id` = `a`.`article_id`) LEFT JOIN `" . DB_PREFIX . "article_description` `ad` ON (`ac`.`article_id` = `ad`.`article_id`)";

		$implode = [];

		if (!empty($data['filter_keyword'])) {
			$implode[] = "LCASE(`ac`.`comment`) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_keyword']) . '%') . "'";
		}

		if (!empty($data['filter_article'])) {
			$implode[] = "LCASE(`ad`.`name`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_article']) . '%') . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$implode[] = "`ac`.`customer_id` = '" . (int) $data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_author'])) {
			$implode[] = "LCASE(`ac`.`author`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_author']) . '%') . "'";
		}

		if (!empty($data['filter_status'])) {
			$implode[] = "`ac`.`status` = '" . (bool) $data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(`ac`.`date_added`) =  DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY `ac`.`date_added` DESC";

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
	 * @param array $data
	 *
	 * @return int
	 */
	public function getTotalComments(array $data = []): int
	{
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "article_comment` `ac` LEFT JOIN `" . DB_PREFIX . "article` `a` ON (`ac`.`article_id` = `a`.`article_id`)";

		$implode = [];

		if (!empty($data['filter_keyword'])) {
			$implode[] = "LCASE(`ac`.`comment`) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_keyword']) . '%') . "'";
		}

		if (!empty($data['filter_article'])) {
			$implode[] = "LCASE(`ad`.`name`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_article']) . '%') . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$implode[] = "`ac`.`customer_id` = '" . (int) $data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_author'])) {
			$implode[] = "LCASE(`ac`.`author`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_author']) . '%') . "'";
		}

		if (!empty($data['filter_status'])) {
			$implode[] = "`ac`.`status` = '" . (bool) $data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(`ac`.`date_added`) =  DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int) $query->row['total'];
	}
}
