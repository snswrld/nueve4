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
	private $instances = [];

	public static function getInstance() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bind($key, $resolver) {
		if (empty($key) || !is_string($key)) {
			throw new \InvalidArgumentException('Service key must be a non-empty string');
		}
		if (!is_callable($resolver)) {
			throw new \InvalidArgumentException('Service resolver must be callable');
		}
		$this->services[$key] = $resolver;
	}

	public function singleton($key, $resolver) {
		if (empty($key) || !is_string($key)) {
			throw new \InvalidArgumentException('Service key must be a non-empty string');
		}
		if (!is_callable($resolver)) {
			throw new \InvalidArgumentException('Service resolver must be callable');
		}
		$this->services[$key] = $resolver;
		$this->singletons[$key] = true;
	}

	public function resolve($key) {
		if (!isset($this->services[$key])) {
			throw new \InvalidArgumentException("Service '{$key}' not found. Available services: " . implode(', ', array_keys($this->services)));
		}

		if (isset($this->singletons[$key])) {
			if (!isset($this->instances[$key])) {
				$this->instances[$key] = $this->services[$key]();
			}
			return $this->instances[$key];
		}

		return $this->services[$key]();
	}
}