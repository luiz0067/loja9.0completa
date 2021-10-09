<?php
define('ISC_CLI', true);
define('NL', "\n");

// CLI only
if(PHP_SAPI != 'cli' || isset($_SERVER['SERVER_PORT']) || isset($_SERVER['REQUEST_METHOD'])) {
	echo 'CLI only';
	exit;
}

if(!isset($argv) || !isset($argv[1])) {
	echo "Incorrect usage.";
	exit;
}

$route = ucfirst($argv[1]);
$route = preg_replace('#_([a-z])#', '$1', $route);
if(!method_exists('CLI', $route)) {
	echo "Incorrect usage.";
	exit;
}

class CLI
{
	public $arguments = array();

	public function Install()
	{
		$configFile = dirname(__FILE__).'/../config/config.php';

		if(file_exists($configFile)) {
			require $configFile;
		}

		if(isset($GLOBALS['ISC_CFG']['isSetup']) && $GLOBALS['ISC_CFG']['isSetup'] == true) {
			fwrite(STDOUT, "A instalacao so funciona se a loja nao estiver instalada".NL);
			exit(1);
		}

		define('CLI_INSTALL', true);
		require dirname(__FILE__).'/index.php';
		exit(0);
	}

	public function Upgrade()
	{
		define('NO_UPGRADE_CHECK', true);
		require dirname(__FILE__).'/init.php';
		$upgrader = GetClass('ISC_ADMIN_UPGRADE');
		if(!$upgrader->CanUpgrade()) {
			fwrite(STDOUT, "ERROR: This installation does not need an upgrade.".NL);
			exit(0);
		}
		$upgrader->CliUpgrade();
		exit(0);
	}

	public function DownloadTemplate()
	{
		require dirname(__FILE__).'/init.php';
		if(empty($this->arguments[2])) {
			$this->arguments[2] = GetConfig('template');
		}

		$_REQUEST['template'] = basename($this->arguments[2]);
		$result = GetClass('ISC_ADMIN_LAYOUT')->DownloadNewTemplates2();
		if($result == false || !empty($GLOBALS['ErrorMessage'])) {
			fwrite(STDOUT, 'ERROR: '.$GLOBALS['ErrorMessage'].NL);
			exit(1);
		}
		echo 'SUCCESS';
		exit(0);
	}
}

$cli = new CLI;
$cli->arguments = $argv;
$cli->$route();
?>