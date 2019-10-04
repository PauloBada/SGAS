<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 11/07/2019                       
   Objetivo:  Faz a conexão das ações de controller do sistema
*/

namespace MF\Controller;

abstract class Action {

	protected $view;
	protected $dados;

	public function __construct() {
		$this->view = new \stdClass();
	}

	//                                       = valor padrão, caso não haja valor
	protected function render($view, $layout = 'layout') {
		$this->view->page = $view;	
	
		if(file_exists("../App/Views/".$layout.".phtml")) {
			require_once "../App/Views/".$layout.".phtml";	
		} else {
			$this->content();
		}
	}		

	protected function content() {
		
		$classeAtual = get_class($this);
		// $classeAtual ==> "App\Controllers\IndexController"
		
		$classeAtual =  str_replace('App'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR, '', $classeAtual);
		// $classeAtual ==> "IndexController"

		$classeAtual = mb_strtolower(str_replace('Controller', '', $classeAtual));
		// $classeAtual ==> "index"

		// Levando em consideração que o arquivo de Controller será IndexController.php
		require_once "../App/Views/".$classeAtual."/".$this->view->page.".phtml";		
	}	
}

?>