<?php

/**
 * Configuration service
 */
class ConfService extends Service
{
	/** @var $conf array Configuration */
	private $conf;
	
	/**
	 * @see Service::start()
	 */
	public function start() {
		// Parse configuration file
		$this->conf = parse_ini_file(ROOT_DIR.'conf/app.ini',true);
	}
	
	/**
	 * @see Service::getRessource()
	 */
	public function getRessource() {
		return $this->conf;
	}
	
	/**
	 * @see Service::stop()
	 */
	public function stop() {
		
	}
}