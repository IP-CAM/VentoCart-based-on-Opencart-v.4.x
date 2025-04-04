<?php
namespace Ventocart\System\Library\Cache;
/**
 * Class File
 *
 * @package Ventocart\System\Library\Cache
 */
class File {
	/**
	 * @var int
	 */
	private int $expire;

	/**
	 * Constructor
	 *
	 * @param int $expire
	 */
	public function __construct(int $expire = 3600) {
		$this->expire = $expire;
	}

	/**
	 * Get
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get(string $key) {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			return json_decode(file_get_contents($files[0]), true);
		} else {
			return [];
		}
	}

	/**
	 * Set
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function set(string $key, $value, int $expire = 0): void {
		$this->delete($key);

		if (!$expire) {
			$expire = $this->expire;
		}

		file_put_contents(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + $expire), json_encode($value));
	}

	/**
	 * Delete
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function delete(string $key): void {
		$files = glob(DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			foreach ($files as $file) {
				if (file_exists($file)) {
				if (!@unlink($file)) {
					clearstatcache(false, $file);
				}
			}
			}
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		$files = glob(DIR_CACHE . 'cache.*');

		if ($files && rand(1, 100) == 1) {
			foreach ($files as $file) {
				$time = substr(strrchr($file, '.'), 1);

				if ($time < time()) {
					if (!@unlink($file)) {
						clearstatcache(false, $file);
					}
				}
			}
		}
	}
}
