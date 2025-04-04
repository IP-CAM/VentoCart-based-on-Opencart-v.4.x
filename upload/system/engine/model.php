<?php

/**
 * Model class
 */
namespace Ventocart\System\Engine;
/**
 * Class Model
 *
 * @mixin \Ventocart\System\Engine\Registry
 */
class Model
{
	/**
	 * @var object|\Ventocart\System\Engine\Registry
	 */
	protected $registry;

	/**
	 * Constructor
	 *
	 * @param object $registry
	 */
	public function __construct(\Ventocart\System\Engine\Registry $registry)
	{
		$this->registry = $registry;
	}

	/**
	 * __get
	 *
	 * @param string $key
	 *
	 * @return object
	 */
	public function __get(string $key): object
	{
		if ($this->registry->has($key)) {
			return $this->registry->get($key);
		} else {
			throw new \Exception('Error: Could not call registry key ' . $key . '!');
		}
	}

	/**
	 * __set
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return void
	 */
	public function __set(string $key, object $value): void
	{
		$this->registry->set($key, $value);
	}

	/**
	 * __isset
	 *
	 * https://www.php.net/manual/en/language.oop5.overloading.php#object.set
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset(string $key): bool
	{
		return $this->registry->has($key);
	}
}
