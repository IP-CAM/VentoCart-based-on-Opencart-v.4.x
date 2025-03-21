<?php
namespace Ventocart\System\Library\Session;
/**
 * Class File
 *
 * @package Ventocart\System\Library\Session
 */
class File {
	private object $config;
	/**
     * Constructor
     *
     * @param object $registry
     */
	public function __construct(\Ventocart\System\Engine\Registry $registry) {
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
		$file = DIR_SESSION . 'sess_' . basename($session_id);

		if (is_file($file)) {
			return json_decode(file_get_contents($file), true);
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
		file_put_contents(DIR_SESSION . 'sess_' . basename($session_id), json_encode($data));

		return true;
	}

	/**
     * Destroy
     *
     * @param string $session_id
     *
     * @return void
     */
	public function destroy(string $session_id): void {
		$file = DIR_SESSION . 'sess_' . basename($session_id);

		if (is_file($file)) {
			unlink($file);
		}
	}
	
	/**
     * GC
     *
     * @return void
     */
	public function gc(): void {
		if (round(rand(1, $this->config->get('session_divisor') / $this->config->get('session_probability'))) == 1) {
			$expire = time() - $this->config->get('session_expire');

			$files = glob(DIR_SESSION . 'sess_*');

			foreach ($files as $file) {
				if (is_file($file) && filemtime($file) < $expire) {
					unlink($file);
				}
			}
		}
	}
}
