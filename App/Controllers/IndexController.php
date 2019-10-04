<?php
/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para definir as rotas de acesso no sistema
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;


class IndexController extends Action {

	public function index() {

		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';

		$this->render('index');
	}
}	// 	Fim da classe
?>