<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções de autenticação e nível de acesso as opções do sistema
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {

// ====================================================== //

	public function autenticar() {

		$usuario = Container::getModel('TbCadLoginSess');
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));
		$usuario->autenticar();
		
		// Testar se achou ou não o Voluntário
		if ($usuario->__get('id_vlnt') != '' && $usuario->__get('nome') != '') {
			// Inserir na tabela tb_login_sess
			$login = Container::getModel('TbLoginSess');
			$login->__set('seql_cad_login', $usuario->__get('seql_cad_login'));
			$login->iniciarSessao();

			session_start();
			// Coloca dados em memória para utilização pelo sistema
			$_SESSION['id'] = $usuario->__get('id_vlnt');
			$_SESSION['nome'] = $usuario->__get('nome');
			$_SESSION['seql_cad_login'] = $usuario->__get('seql_cad_login');

			header('Location: /menuPrincipal');
									
		} else {
			//Volta para tela inicial, acrescentando na URL "?login=erro"
			$email = $_POST["email"];
			header('Location: /?login=erro&'.'email='.$email.''); 
		}
	}

// ====================================================== //

	public static function verificaNivelAcesso($nivel_acesso_requerido) {
		$nivelVoluntario = Container::getModel('TbCadLoginSess');
		$nivelVoluntario->__set('seql_cad_login', $_SESSION['seql_cad_login']);
		$nivelVoluntario->getNivelAcesso();
		$nivelVoluntarioLogado = $nivelVoluntario->__get('nivelAcesso');

		if ($nivelVoluntarioLogado > $nivel_acesso_requerido) {
			// Não está autorizado
			$autorizado = 0;
		} else {
			// Está autorizado
			$autorizado = 1;
		}

		$retorno = array (
			'autorizado' =>  $autorizado,
			'nivelVoluntario' => $nivelVoluntarioLogado
		);

		return $retorno;
	}	

// ====================================================== //

	public function sair() {
		// Alterar tabela tb_login_sess, com ts_login_sai e status_login = 2 (Deslogado)
		session_start();
		$logout = Container::getModel('TbLoginSess');
		$logout->__set('seql_cad_login', $_SESSION['seql_cad_login']);
		$logout->encerrarSessao();

		// Fecha Menu Principal e sai do sistema
		session_destroy();
		header('Location: /');
	}
}

?>