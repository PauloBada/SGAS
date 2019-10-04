<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções de informações do menu principal e validação de login
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

	public function menuPrincipal() {

		$this->validaAutenticacao();

		// Para incluir informações do usuário logado na tela inicial
		$usuario = Container::getModel('TbVlnt');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoVoluntario();

		$this->render('menuPrincipal');
	}

	public function validaAutenticacao() {
		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}
	

}	//	Fim da classe

?>
