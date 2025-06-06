<?php

namespace Ventocart\System\Library;
/**
 * Class DB Adapter
 *
 * @package Ventocart\System\Library
 */
class DB
{
	/**
	 * @var object
	 */
	private object $adaptor;
	private bool $firewall;
	private array $flippers = [];
	/**
	 * Constructor
	 *
	 * @param string $adaptor
	 * @param string $hostname
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 * @param string $port
	 * @param string $ssl_key
	 * @param string $ssl_cert
	 * @param string $ssl_ca
	 */
	public function __construct(string $adaptor, string $hostname, string $username, string $password, string $database, string $port = '', string $ssl_key = '', string $ssl_cert = '', string $ssl_ca = '')
	{
		$class = 'Ventocart\System\Library\DB\\' . $adaptor;
		$this->firewall = false;
		if (class_exists($class)) {
			$this->adaptor = new $class($hostname, $username, $password, $database, $port, $ssl_key, $ssl_cert, $ssl_ca);
		} else {
			throw new \Exception('Error: Could not load database adaptor ' . $adaptor . '!');
		}
	}
	/**
	 * Register a flipper (query modifier).
	 *
	 * @param string $tag    The tag that identifies the type of query (e.g., 'search_query').
	 * @param callable $call The function that modifies the query.
	 */
	public function registerFlipper(string $tag, callable $call)
	{
		$this->flippers[] = ['tag' => $tag, 'call' => $call];
	}

	public function enable_firewall()
	{
		$this->firewall = true;
	}
	/**
	 * Execute a query, applying optional flippers.
	 *
	 * @param string $sql    The SQL statement to execute.
	 * @param string $flipper Optional tag to apply specific query modifications.
	 *
	 * @return mixed The result of the query execution.
	 */
	public function query(string $sql, string $flipper = '')
	{
		// If a flipper tag is provided, apply the corresponding modifier
		if ($flipper && isset($this->flippers[$flipper])) {
			// Apply the callback to modify the query
			return $this->flippers[$flipper]['call']($sql); // Return the flipper Query result
		}

		// If firewall is enabled, sanitize parameters
		if ($this->firewall) {
			$sql = $this->sanitizeData($sql);
		}
		return $this->adaptor->query($sql);
	}

	/**
	 * Sanitize SQL String
	 *
	 * Sanitize the SQL string itself to prevent injection (can be used for direct SQL queries).
	 *
	 * @param string $sql
	 * @return string
	 */
	private function sanitizeData(string $sql): string
	{
		// Prevent [plugin=...] tags from being injected into SQL
		$sql = str_replace('[plugin=', '[illegal=', $sql);


		return $sql;
	}
	/**
	 * Escape
	 *
	 * @param string $value Value to be protected against SQL injections
	 *
	 * @return string       Returns escaped value
	 */
	public function escape(string $value): string
	{
		return $this->adaptor->escape($value);
	}

	/**
	 * countAffected
	 *
	 * Gets the total number of affected rows from the last query
	 *
	 * @return int          Returns the total number of affected rows.
	 */
	public function countAffected(): int
	{
		return $this->adaptor->countAffected();
	}

	/**
	 * getLastId
	 *
	 * Get the last ID gets the primary key that was returned after creating a row in a table.
	 *
	 * @return int          Returns last ID
	 */
	public function getLastId(): int
	{
		return $this->adaptor->getLastId();
	}

	/**
	 * isConnected
	 *
	 * Checks if a DB connection is active.
	 *
	 * @return bool
	 */
	public function isConnected(): bool
	{
		return $this->adaptor->isConnected();
	}
}
