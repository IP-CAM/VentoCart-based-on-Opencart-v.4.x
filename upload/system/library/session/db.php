<?php
/*
CREATE TABLE IF NOT EXISTS `session` (
  `session_id` varchar(32) NOT NULL,
  `data` text NOT NULL,
  `expire` datetime NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
*/
namespace Ventocart\System\Library\Session;
/**
 * Class DB
 *
 * @package Ventocart\System\Library\Session
 */
class DB {
	private object $db;
	private object $config;

	/**
     * Constructor
     *
     * @param object $registry
     */
	public function __construct(\Ventocart\System\Engine\Registry $registry) {
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
	}

	/**
     * Read
     *
     * @param string $session_id
     *
     * @return array
     */
	public function read(string $session_id): array {
		$query = $this->db->query("SELECT `data` FROM `" . DB_PREFIX . "session` WHERE `session_id` = '" . $this->db->escape($session_id) . "' AND `expire` > '" . $this->db->escape(gmdate('Y-m-d H:i:s'))  . "'");

		if ($query->num_rows) {
			return (array)json_decode($query->row['data'], true);
		} else {
			return [];
		}
	}
	
	/**
     * Write
     *
     * @param string $session_id
     * @param array  $data
     *
     * @return bool
     */
	public function write(string $session_id, array $data): bool {
    if ($session_id) {
        // Print the data for debugging purposes
      
 
        // Encode the data array to JSON
        $encoded_data = $this->db->escape(json_encode($data, true));

        // Calculate the new expiration time
        $expire_time = gmdate('Y-m-d H:i:s', time() + $this->config->get('session_expire'));

        // Update/or insert the session record in the database
		$result = $this->db->query("
		INSERT INTO `" . DB_PREFIX . "session` (`session_id`, `data`, `expire`)
		VALUES ('" . $this->db->escape($session_id) . "', '" . $encoded_data . "', '" . $this->db->escape($expire_time) . "')
		ON DUPLICATE KEY UPDATE
		`data` = VALUES(`data`),
		`expire` = VALUES(`expire`)
	");

 
    }

    return  $result;
}

	/**
     * Destroy
     *
     * @param string $session_id
     *
     * @return bool
     */
	public function destroy(string $session_id): bool {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE `session_id` = '" . $this->db->escape($session_id) . "'");

		return true;
	}

	/**
     * GC
     *
     * @return bool
     */
	public function gc(): bool {
		if (round(rand(1, $this->config->get('session_divisor') / $this->config->get('session_probability'))) == 1) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "session` WHERE `expire` < '" . $this->db->escape(gmdate('Y-m-d H:i:s', time())) . "'");
		}

		return true;
	}
}
