<?php

// Bootstrap não é "bootstrap.css", mas a definição de "script de inicialização"

namespace MF\Init;

// Setar para que a data seja a Brasileira
date_default_timezone_set('America/Sao_Paulo');

// Classe abstract não pode ser instanciada, somente herdada
abstract class Bootstrap {	
	private $routes;

	abstract protected function initRoutes();
	
	public function __construct() {
		$this->initRoutes();
		$this->run($this->getUrl());
	}
	
	public function getRoutes() {
		return $this->routes;
	}

	public function setRoutes(array $routes) {
		$this->routes = $routes;
	}

	protected function run($url) {
		foreach ($this->getRoutes() as $key => $route) {
			if ($url == $route['route']) {
				
				# DIRECTORY_SEPARATOR substitui "\\", que no Linux pode ser "//"
				# $class = "App\\Controllers\\".ucfirst($route['controller']);
					
				$class = "App".DIRECTORY_SEPARATOR."Controllers".DIRECTORY_SEPARATOR.ucfirst($route['controller']);
				// $class ==> App\Controllers\IndexController
			
				$controller = new $class;

				$action = $route['action'];			

				$controller->$action();			
				// Aqui chama em "App\Controllers\" a classe "IndexController.php" xx // e as funções index() ou sobreNos(), dependendo de $action.		
			}
		}
	}

	protected function getUrl() {
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}
}

?>