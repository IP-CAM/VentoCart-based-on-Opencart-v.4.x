<?php
// Configuration
if (!is_file('config.php')) {
	exit();
}

// Config
require_once('config.php');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Autoloader
$autoloader = new \Ventocart\System\Engine\Autoloader();
$autoloader->register('Ventocart\\Catalog', DIR_APPLICATION);
$autoloader->register('Ventocart\System', DIR_SYSTEM);

require_once(DIR_SYSTEM . 'vendor.php');

// Registry
$registry = new \Ventocart\System\Engine\Registry();
$registry->set('autoloader', $autoloader);

// Config
$config = new \Ventocart\System\Engine\Config();
$registry->set('config', $config);

// Load the default config
$config->addPath(DIR_CONFIG);
$config->load('default');
$config->load('catalog');
$config->set('application', 'Catalog');

// Set the default time zone
date_default_timezone_set($config->get('date_timezone'));


// Logging
$log = new \Ventocart\System\Library\Log($config->get('error_filename'));
$registry->set('log', $log);

// Error Handler
set_error_handler(function (string $code, string $message, string $file, string $line) use ($log, $config) {
	// error suppressed with @
	if (@error_reporting() === 0) {
		return false;
	}

	switch ($code) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}

	if ($config->get('error_log')) {
		$log->write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);
	}

	if ($config->get('error_display')) {
		echo '<b>' . $error . '</b>: ' . $message . ' in <b>' . $file . '</b> on line <b>' . $line . '</b>';
	} else {
		header('Location: ' . $config->get('error_page'));
		exit();
	}

	return true;
});

// Exception Handler
set_exception_handler(function (\Throwable $e) use ($log, $config): void {
	if ($config->get('error_log')) {
		$log->write(get_class($e) . ':  ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
	}

	if ($config->get('error_display')) {
		echo '<b>' . get_class($e) . '</b>: ' . $e->getMessage() . ' in <b>' . $e->getFile() . '</b> on line <b>' . $e->getLine() . '</b>';
	} else {
		header('Location: ' . $config->get('error_page'));
		exit();
	}
});

$event = new Ventocart\System\Engine\Event($registry);
$registry->set('event', $event);
// Loader
$loader = new \Ventocart\System\Engine\Loader($registry);
$registry->set('load', $loader);

// Request
$request = new \Ventocart\System\Library\Request();
$registry->set('request', $request);

// Response
$response = new \Ventocart\System\Library\Response();
$registry->set('response', $response);

// Database
if ($config->get('db_autostart')) {
	$db = new \Ventocart\System\Library\DB($config->get('db_engine'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port'), $config->get('db_ssl_key'), $config->get('db_ssl_cert'), $config->get('db_ssl_ca'));
	$registry->set('db', $db);

	// Sync PHP and DB time zones
	$db->query("SET `time_zone` = '" . $db->escape(date('P')) . "'");
}

// Session
if ($config->get('session_autostart')) {
	$session = new \Ventocart\System\Library\Session($config->get('session_engine'), $registry);
	$registry->set('session', $session);

	if (isset($request->cookie[$config->get('session_name')])) {
		$session_id = $request->cookie[$config->get('session_name')];
	} else {
		$session_id = '';
	}

	$session->start($session_id);

	// Require higher security for session cookies
	$option = [
		'expires' => 0,
		'path' => $config->get('session_path'),
		'domain' => $config->get('session_domain'),
		'secure' => $request->server['HTTPS'],
		'httponly' => false,
		'SameSite' => $config->get('session_samesite')
	];

	setcookie($config->get('session_name'), $session->getId(), $option);
}

// Cache
$registry->set('cache', new \Ventocart\System\Library\Cache($config->get('cache_engine'), $config->get('cache_expire')));

// Template
$template = new \Ventocart\System\Library\Template($config->get('template_engine'));
$registry->set('template', $template);
$template->addPath(DIR_TEMPLATE);

// Language
$language = new \Ventocart\System\Library\Language($config->get('language_code'));
$registry->set('language', $language);
$language->addPath(DIR_LANGUAGE);
$loader->load->language($config->get('language_code'));

// Url
$registry->set('url', new \Ventocart\System\Library\Url($config->get('site_url')));

// Pre Actions
foreach ($config->get('action_pre_action') as $pre_action) {
	$loader->controller($pre_action);
}

// Dispatch
$loader->controller('cron/cron');

// Output
$response->output();
