<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 15/10/2019
   Objetivo:  Controller para opções do menu Sumprimento Cadastro
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class SuprimentoAlmoxarifadoController extends Action {

	// ================================================== //

	public function validaAutenticacao() {
		session_start();
		
		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}

	// ================================================== //

	public function suprimentoAlmoxarifado() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		$this->render('suprimentoAlmoxarifado');
	}



}	//	Fim da classe

?>
				