<?php
/**
 * Dependency Injection Container
 *
 * @package Nueve4\Core
 */

namespace Nueve4\Core;

/**
 * Simple DI Container for theme services
 */
class Container {
	private static $instance = null;
	private $services = [];
	private $singletons = [];

	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bind($key, $resolver) {
		$this->services[$key] = $resolver;
	}

	public function singleton($key, $resolver) {
		$this->services[$key] = $resolver;
		$this->singletons[$key] = true;
	}

	public function resolve($key) {
		if (!isset($this->services[$key])) {
			throw new \Exception("Service {$key} not found");
		}

		if (isset($this->singletons[$key])) {
			static $instances = [];
			if (!isset($instances[$key])) {
				$instances[$key] = $this->services[$key]();
			}
			return $instances[$key];
		}

		return $this->services[$key]();
	}
}