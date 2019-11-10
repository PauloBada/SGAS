<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class ApoioController extends Action {

	public function validaAutenticacao() {
		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}
	
/* ================ Início Tratamento Região Administrativa ======================== */
	public function apoioRA() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;
		
		$this->render('apoioRA');
	}

// ====================================================== //

	public function apoioRAIncluir() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);
		
		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioRA');				
		} else {
			$this->view->erroApoio = 0;
			$this->render('apoioRAIncluir');
		}

	}	// Fim da function apoioRAIncluir

// ====================================================== //

	public function apoioRAIncluirBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Pesquisar se nome da Região Administrativa já existe na tabela tb_reg_adm
		$qtdnomeRA = Container::getModel('TbRegAdm');
		$qtdnomeRA->__set('nomeRA_pesq', $_POST['nomeRA']);
		$qtdnomeRA->__set('UF_pesq', $_POST['estadoUF']);
		$qtdnomeRA->getNomeRA();

		// Para validar se o nome da Região Administrativa existe na base
		if ($qtdnomeRA->__get('qtde_nome_ra') > 0 ) {
			$this->view->erroApoio = 2;
			$this->view->nomeRA_inserido = $_POST['nomeRA'];
			$this->render('apoioRAIncluir');				
		} else {
			// Inserir na base
			$insereRA = Container::getModel('TbRegAdm');
			$insereRA->__set('nomeRA_insere', $_POST['nomeRA']);
			$insereRA->__set('ufRA_insere', $_POST['estadoUF']);
			$insereRA->insereRA();			

			$this->view->erroApoio = 1;
			$this->view->nomeRA_inserido = $_POST['nomeRA'];
			$this->view->ufRA_inserido = $_POST['estadoUF'];
			$this->render('apoioRAIncluir');
		}
		// */
	}	// Fim da function apoioRAIncluirBase

// ====================================================== //

	public function apoioRAAlterar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioRA');				
		} else {
			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->view->erroApoio = 0;
			$this->render('apoioRAAlterar');
		}
	}	// Fim da function apoioRAAlterar

// ====================================================== //

	public function apoioRAAlterarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Buscar o nome da RA e a uf
		$dadosRA_alterar = Container::getModel('TbRegAdm');
		$dadosRA_alterar->__set('cd_reg_adm', $_POST['RA_escolhida']);
		$dadosRA_alterar->getDadosRAAll2();

		$this->view->dadosRA_escolhida =  array(
			'cd_ra_a_alterar' => $dadosRA_alterar->__get('cd_reg_adm') ,
			'nome_ra_a_alterar' => $dadosRA_alterar->__get('nome_ra') ,
			'uf_ra_a_alterar' => $dadosRA_alterar->__get('uf_ra'));
				
		$this->render('apoioRAAlterarMenu');
		
	}	// Fim da function apoioRAAlterarMenu

// ====================================================== //

	public function apoioRAAlterarBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Pesquisar se nome da Região Administrativa já existe na tabela tb_reg_adm
		$qtdnomeRA = Container::getModel('TbRegAdm');
		$qtdnomeRA->__set('nomeRA_pesq', $_POST['nomeRA']);
		$qtdnomeRA->__set('UF_pesq', $_POST['estadoUF']);
		$qtdnomeRA->getNomeRA();

		// Para validar se o nome da Região Administrativa existe na base
		if ($qtdnomeRA->__get('qtde_nome_ra') > 0 ) {
			$this->view->dadosRA_escolhida =  array(
			'cd_ra_a_alterar' => $_POST['cdRA'] ,
			'nome_ra_a_alterar' => $_POST['nomeRA'] ,
			'uf_ra_a_alterar' => $_POST['estadoUF']);

			$this->view->erroApoio = 2;

			$this->view->nomeRA_alterado = $_POST['nomeRA'];
			$this->render('apoioRAAlterarMenu');				
		} else {
			// Alterar na base
			$alteraRA = Container::getModel('TbRegAdm');
			$alteraRA->__set('cdRA_altera', $_POST['cdRA']);
			$alteraRA->__set('nomeRA_altera', $_POST['nomeRA']);
			$alteraRA->__set('ufRA_altera', $_POST['estadoUF']);
			$alteraRA->alteraRA();			

			$this->view->erroApoio = 1;
			$this->view->nomeRA_alterado = $_POST['nomeRA'];
			$this->view->ufRA_alterado = $_POST['estadoUF'];

			// Para poder reenderizar e montar o combobox novamente com dados da base atualizados
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->render('apoioRAAlterar');
		}
		// */
	}	// Fim da function apoioRAAlterarBase

// ====================================================== //

	public function apoioRAEncerrar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioRA');				
		} else {
			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->view->erroApoio = 0;
			$this->render('apoioRAEncerrar');
		}
	}	// Fim da function apoioRAEncerrar

// ====================================================== //

	public function apoioRAEncerrarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Buscar o nome da RA e a uf
		$dadosRA_alterar = Container::getModel('TbRegAdm');
		$dadosRA_alterar->__set('cd_reg_adm', $_POST['RA_escolhida']);
		$dadosRA_alterar->getDadosRAAll2();

		$this->view->dadosRA_escolhida =  array(
			'cd_ra_a_alterar' => $dadosRA_alterar->__get('cd_reg_adm') ,
			'nome_ra_a_alterar' => $dadosRA_alterar->__get('nome_ra') ,
			'uf_ra_a_alterar' => $dadosRA_alterar->__get('uf_ra'));

		$this->render('apoioRAEncerrarMenu');
		
	}	// Fim da function apoioRAEncerrarMenu

// ====================================================== //

	public function apoioRAEncerrarBase() {
		
		$this->validaAutenticacao();	
		
		$this->view->erroApoio = 0;

		// Verificar se codigo Região Administrativa não está cadastrado em outras tabelas
		$consultaOTRA = Container::getModel('TbRegAdm');
		$consultaOTRA->__set('cdRA_altera', $_POST['cdRA']);
		$consultaOTRA_total = $consultaOTRA->consultaOutrasTabelasRA();

		$cont_cd_ra = 0;
		foreach ($consultaOTRA_total as $indice => $qtde) {
			if ($qtde['qtde'] > 0) {
				$cont_cd_ra = $cont_cd_ra + 1;
			}
		}

		if ($cont_cd_ra == 0) { 
			// Alterar na base, encerrando a Região Administrativa
			$encerraRA = Container::getModel('TbRegAdm');
			$encerraRA->__set('cdRA_altera', $_POST['cdRA']);
			$encerraRA->encerraRA();			
			
			$this->view->erroApoio = 1;
			$this->view->nomeRA_alterado = $_POST['nomeRA'];
			$this->view->ufRA_alterado = $_POST['estadoUF'];
		} else {
			$this->view->erroApoio = 2;
			$this->view->nomeRA_alterado = $_POST['nomeRA'];
			$this->view->ufRA_alterado = $_POST['estadoUF'];
		}				
		
		// Para poder reenderizar e montar o combobox novamente com dados da base atualizados
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();

		$this->view->regioes = $regioesBase;

		$this->render('apoioRAEncerrar');

		// */
	}	// Fim da function apoioRAEncerrarBase

// ====================================================== //

	public function apoioRAConsultar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioRA');				
		} else {
			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll3();

			$this->view->regioes = $regioesBase;

			$this->view->erroApoio = 0;
			$this->render('apoioRAConsultar');
		}
	}	// Fim da function apoioRAConsultar

// ====================================================== //

	public function apoioRAConsultarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Buscar todos os dados da Região Administrativa a consultar
		$dadosRA_consultar = Container::getModel('TbRegAdm');
		$dadosRA_consultar->__set('cd_reg_adm', $_POST['RA_escolhida']);
		$dadosRA_consultar->getDadosRAAll4();

		// Colocar a descrição do estado (situação) da RA
		if ($dadosRA_consultar->__get('cd_est_ra') == 1) {
			$estado_a_consultar = 'Ativa';
			$data_fim = '31/12/9998';
		} else {
			$estado_a_consultar = 'Inativa (encerrada)';
			$data_fim = Funcoes::editaData($dadosRA_consultar->__get('dt_fim_ra'));
		}

		// Formatar as datas de AAAA-MM-DD para DD/MM/AAAA
		$data_inicio = Funcoes::editaData($dadosRA_consultar->__get('dt_inc_ra'));

		$this->view->dadosRA_escolhida =  array(
			'cd_ra_a_consultar' => $dadosRA_consultar->__get('cod_ra') ,
			'nome_ra_a_consultar' => $dadosRA_consultar->__get('nome_ra') ,
			'dt_inc_ra_a_consultar' => $data_inicio ,
			'dt_fim_ra_a_consultar' => $data_fim ,
			'cd_est_ra_a_consultar' => $estado_a_consultar ,
			'uf_ra_a_consultar' => $dadosRA_consultar->__get('uf_ra'));

		$this->render('apoioRAConsultarMenu');

	}	// Fim da function apoioRAConsultarMenu

}	//	Fim da classe

?>
