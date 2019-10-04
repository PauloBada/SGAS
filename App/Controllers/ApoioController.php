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


	/* ================================================================ */
	/* ================ Início Tratamento Item ======================== */
	/* ================================================================ */

// ====================================================== //

	public function apoioItemSubitem() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;
		
		$this->render('apoioItemSubitem');
	}

// ====================================================== //

	public function apoioItemIncluir() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);
		
		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioItemSubitem');				
		} else {
			$this->view->erroApoio = 0;
			$this->render('apoioItemIncluir');
		}

	}	// Fim da function apoioItemIncluir

// ====================================================== //

	public function apoioItemIncluirBase() {
		            
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Pesquisar se nome do Item já existe na tabela tb_item_suprimt
		$qtdnomeItem = Container::getModel('TbItemSuprimt');
		$qtdnomeItem->__set('nomeItem_pesq', $_POST['nomeItem']);
		$qtdnomeItem->getNomeItemInclusao();

		// Para validar se o nome do Item existe na base
		if ($qtdnomeItem->__get('qtde_nome_item') > 0 ) {
			$this->view->erroApoio = 2;
			$this->view->nomeItem_inserido = $_POST['nomeItem'];
			$this->render('apoioItemIncluir');				
		} else {
			// Inserir na base
			$insereItem = Container::getModel('TbItemSuprimt');
			$insereItem->__set('nomeItem_insere', $_POST['nomeItem']);
			$insereItem->__set('evento_insere', $_POST['evento']);
			$insereItem->insereItem();			

			$this->view->erroApoio = 1;
			$this->view->nomeItem_inserido = $_POST['nomeItem'];
			$this->view->evento_inserido = $_POST['evento'];
			$this->render('apoioItemIncluir');
		}
		// */
	}	// Fim da function apoioItemIncluirBase

// ====================================================== //

	public function apoioItemAlterar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioItemSubitem');				
		} else {
			// Buscar todos os itens e remeter array para montar o combobox
			$itensNec = Container::getModel('TbItemSuprimt');
			$itensBase = $itensNec->getDadosItemAll();

			$this->view->itens = $itensBase;

			$this->view->erroApoio = 0;
			$this->render('apoioItemAlterar');
		}
	}	// Fim da function apoioitemAlterar

// ====================================================== //

	public function apoioItemAlterarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Buscar o nome da RA e a uf
		$dadosItem_alterar = Container::getModel('TbItemSuprimt');
		$dadosItem_alterar->__set('cd_item', $_POST['item_escolhido']);
		$dadosItem_alterar->getDadosItemAll2();

		$this->view->dadosItem_escolhida =  array(
			'cd_item_a_alterar' => $dadosItem_alterar->__get('cd_item') ,
			'nome_item_a_alterar' => $dadosItem_alterar->__get('nome_item') ,
			'cd_evento_a_alterar' => $dadosItem_alterar->__get('evento'));
				
		$this->render('apoioItemAlterarMenu');

		// */
		
	}	// Fim da function apoioItemAlterarMenu

// ====================================================== //

	public function apoioItemAlterarBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Pesquisar se nome do Item já existe na tabela tb_item_suprimt
		$qtdnomeItem = Container::getModel('TbItemSuprimt');
		$qtdnomeItem->__set('nomeItem_pesq', $_POST['nomeItem']);
		$qtdnomeItem->__set('evento_pesq', $_POST['evento']);
		$qtdnomeItem->getNomeItemAlteracao();

		// Para validar se o nome do Item existe na base
		if ($qtdnomeItem->__get('qtde_nome_item') > 0 ) {
			$this->view->dadosItem_escolhida =  array(
			'cd_item_a_alterar' => $_POST['codItem'] ,
			'nome_item_a_alterar' => $_POST['nomeItem'] ,
			'cd_evento_a_alterar' => $_POST['evento']);

			$this->view->erroApoio = 2;

			$this->view->nomeItem_alterado = $_POST['nomeItem'];
			$this->render('apoioItemAlterarMenu');				
		} else {
			// Alterar na base
			$alteraItem = Container::getModel('TbItemSuprimt');
			$alteraItem->__set('cdItem_altera', $_POST['codItem']);
			$alteraItem->__set('nomeItem_altera', $_POST['nomeItem']);
			$alteraItem->__set('evento_altera', $_POST['evento']);
			$alteraItem->alteraItem();			

			$this->view->erroApoio = 1;
			$this->view->nomeItem_alterado = $_POST['nomeItem'];
			$this->view->evento_alterado = $_POST['evento'];

			// Para poder reenderizar e montar o combobox novamente com dados da base atualizados
			$itensNec = Container::getModel('TbItemSuprimt');
			$itensBase = $itensNec->getDadosItemAll();

			$this->view->itens = $itensBase;

			$this->render('apoioItemAlterar');
		}
		// */
	}	// Fim da function apoioItemAlterarBase

// ====================================================== //

	public function apoioItemConsultar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioItemSubitem');				
		} else {
			// Buscar todas os Itens e remeter array para montar o combobox
			$itensNec = Container::getModel('TbItemSuprimt');
			$itensBase = $itensNec->getDadosItemAll3();

			$this->view->itens = $itensBase;

			$this->view->erroApoio = 0;
			$this->render('apoioItemConsultar');
		}
	}	// Fim da function apoioItemConsultar

// ====================================================== //

	public function apoioItemConsultarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Buscar o nome da RA e a uf
		$dadosItem_consultar = Container::getModel('TbItemSuprimt');
		$dadosItem_consultar->__set('cd_item', $_POST['item_escolhido']);
		$dadosItem_consultar->getDadosItemAll2();

		$this->view->dadosItem_escolhida =  array(
			'cd_item_a_consultar' => $dadosItem_consultar->__get('cd_item') ,
			'nome_item_a_consultar' => $dadosItem_consultar->__get('nome_item') ,
			'cd_evento_a_consultar' => $dadosItem_consultar->__get('evento'));

		$this->render('apoioItemConsultarMenu');

	}	// Fim da function apoioItemConsultarMenu


	/* ================================================================ */
	/* ================ Início Tratamento Subitem ======================== */
	/* ================================================================ */
	
// ====================================================== //

	public function apoioSubitemIncluir() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);
		
		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioItemSubitem');				
		} else {
			// Buscar todas os Itens e remeter array para montar o combobox
			$itensNec = Container::getModel('TbItemSuprimt');
			$itensBase = $itensNec->getDadosItemAll3();

			$this->view->itens = $itensBase;

			$this->view->erroApoio = 0;
			$this->render('apoioSubitemIncluir');
		}

	}	// Fim da function apoioSubitemIncluir

// ====================================================== //

	public function apoioSubitemIncluirBase() {
		            
		$this->validaAutenticacao();	

		$this->view->erroApoio = 0;

		// Pesquisar se nome do Subitem já existe na tabela tb_subitem_suprimt
		$qtdnomeSubitem = Container::getModel('TbSbitemSuprimt');
		$qtdnomeSubitem->__set('cdItem_pesq', $_POST['item_escolhido']);
		$qtdnomeSubitem->__set('nomeSubitem_pesq', $_POST['nomeSubitem']);
		$qtdnomeSubitem->getNomeSubitemInclusao();

		// Para validar se o nome do Item existe na base
		if ($qtdnomeSubitem->__get('qtde_nome_subitem') > 0 ) {
			$this->view->erroApoio = 2;
			$this->view->nomeSubitem_inserido = $_POST['nomeSubitem'];

			// Reenderizar valores na tela
			$itensNec = Container::getModel('TbItemSuprimt');
			$itensBase = $itensNec->getDadosItemAll3();

			$this->view->itens = $itensBase;

			$this->render('apoioSubitemIncluir');				
		} else {
			// Obter o próximo código de Subitem
			$max_seql_grava = Container::getModel('TbSbitemSuprimt');
			$max_seql_grava->__set('cdItem_insere', $_POST['item_escolhido']);
			$max_seql_grava->obtemNrMaxSbitem();	

			if ($max_seql_grava->__get('max_seql_grava') == null) {
				$seql_subitem = 1;
			} else {
				$seql_subitem = $max_seql_grava->__get('max_seql_grava') + 1;
			}

			$insereSubitem = Container::getModel('TbSbitemSuprimt');
			$insereSubitem->__set('cdItem_insere', $_POST['item_escolhido']);
			$insereSubitem->__set('cdSubitem_insere', $seql_subitem);
			$insereSubitem->__set('nomeSubitem_insere', $_POST['nomeSubitem']);
			$insereSubitem->insereSubitem();			

			$this->view->erroApoio = 1;
			$this->view->nomeSubitem_inserido = $_POST['nomeSubitem'];
			$this->view->codItem_inserido = $_POST['item_escolhido'];

			// Reenderizar valores na tela
			$itensNec = Container::getModel('TbItemSuprimt');
			$itensBase = $itensNec->getDadosItemAll3();

			$this->view->itens = $itensBase;

			$this->render('apoioSubitemIncluir');
		
		}
	}	// Fim da function apoioSubitemIncluirBase

// ====================================================== //

	public function apoioSubitemAlterar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioItemSubitem');				
		} else {
			
			/* Não será necessário, pois a busca ficou no Javascript do programa "apoioSubitemAlterar.phtml"
			// Buscar todos os itens e remeter array para montar o combobox
			$itensNec = Container::getModel('TbItemSuprimt');
			$itensBase = $itensNec->getDadosItemAll();

			$this->view->itens = $itensBase;

			// Buscar Códigos de Subitem do item 1 e montar na tela 
			$subitensNec = Container::getModel('TbSbitemSuprimt');
			$subitensNec->__set('cdItem_pesq', 1);
			$subitensBase = $subitensNec->getDadosSubItemAll();

			$this->view->subitens = $subitensBase;
			*/

			$this->view->erroApoio = 0;
			$this->render('apoioSubitemAlterar');
		}
	}	// Fim da function apoioSubitemAlterar

// ====================================================== //

	public function apoioSubitemAlterarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Testar se o cd_subitem_escolhido não é numérico (está "Escolha Subitem")
		if (!is_numeric($_POST['cb_subitem_escolhido'])) {
			$this->view->erroApoio = 3;
			$this->render('apoioSubitemAlterar');
		} else {
			// Buscar dados Subitem 
			$dadosSubitem_alterar = Container::getModel('TbSbitemSuprimt');
			$dadosSubitem_alterar->__set('cdItem_pesq', $_POST['cb_item_escolhido']);
			$dadosSubitem_alterar->__set('cdSubitem_pesq', $_POST['cb_subitem_escolhido']);
			$dadosSubitem_alterar->getDadosSubitem();

			$this->view->dadosSubitem_escolhida =  array(
				'cod_item_a_alterar' => $dadosSubitem_alterar->__get('cd_item') ,
				'nome_item_a_alterar' => $dadosSubitem_alterar->__get('nome_item') ,
				'cod_subitem_a_alterar' => $dadosSubitem_alterar->__get('cd_subitem') ,
				'nome_subitem_a_alterar' => $dadosSubitem_alterar->__get('nome_subitem'));
					
			$this->render('apoioSubitemAlterarMenu');
		}
		// */ 
		
	}	// Fim da function apoioSubitemAlterarMenu

// ====================================================== //

	public function apoioSubitemAlterarBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Pesquisar se nome do Subitem já existe na tabela tb_sbitem_suprimt
		$qtdnomeSubitem = Container::getModel('TbSbitemSuprimt');
		$qtdnomeSubitem->__set('nomeSubitem_pesq', $_POST['nomeSubitem']);
		$qtdnomeSubitem->getNomeSubitemAlteracao();

		// Para validar se o nome do Subitem existe na base
		if ($qtdnomeSubitem->__get('qtde_nome_subitem') > 0 ) {
			$this->view->dadosSubitem_escolhida =  array(
			'cod_item_a_alterar' => $_POST['codItem'] ,
			'nome_item_a_alterar' => $_POST['nomeItem'] ,
			'cod_subitem_a_alterar' => $_POST['codSubitem'] ,
			'nome_subitem_a_alterar' => $_POST['nomeSubitem'] ,);


			$this->view->erroApoio = 2;

			$this->view->nomeItem_alterado = $_POST['nomeSubitem'];
			$this->render('apoioSubitemAlterarMenu');				
		} else {
			// Alterar na base
			$alteraSubitem = Container::getModel('TbSbitemSuprimt');
			$alteraSubitem->__set('cdItem_altera', $_POST['codItem']);
			$alteraSubitem->__set('cdSbitem_altera', $_POST['codSubitem']);
			$alteraSubitem->__set('nomeSubitem_altera', $_POST['nomeSubitem']);
			$alteraSubitem->alteraSubitem();			

			$this->view->erroApoio = 1;
			$this->view->nomeSubitem_alterado = $_POST['nomeSubitem'];

			$this->render('apoioSubitemAlterar');
		}
		// */
	}	// Fim da function apoioSubtemAlterarBase

// ====================================================== //

	public function apoioSubitemConsultar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroApoio = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('apoioItemSubitem');				
		} else {
			$this->view->erroApoio = 0;
			$this->render('apoioSubitemConsultar');
		}
	}	// Fim da function apoioSubitemConsultar

// ====================================================== //

	public function apoioSubitemConsultarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroApoio = 0;

		// Testar se o cd_subitem_escolhido não é numérico (está "Escolha Subitem")
		if (!is_numeric($_POST['cb_subitem_escolhido'])) {
			$this->view->erroApoio = 3;
			$this->render('apoioSubitemConsultar');
		} else {
			// Buscar dados Subitem 
			$dadosSubitem_alterar = Container::getModel('TbSbitemSuprimt');
			$dadosSubitem_alterar->__set('cdItem_pesq', $_POST['cb_item_escolhido']);
			$dadosSubitem_alterar->__set('cdSubitem_pesq', $_POST['cb_subitem_escolhido']);
			$dadosSubitem_alterar->getDadosSubitem();

			$this->view->dadosSubitem_escolhida =  array(
				'cod_item_a_alterar' => $dadosSubitem_alterar->__get('cd_item') ,
				'nome_item_a_alterar' => $dadosSubitem_alterar->__get('nome_item') ,
				'cod_subitem_a_alterar' => $dadosSubitem_alterar->__get('cd_subitem') ,
				'nome_subitem_a_alterar' => $dadosSubitem_alterar->__get('nome_subitem'));
					
			$this->render('apoioSubitemConsultarMenu');
		}
		// */ 
	}
	
}	//	Fim da classe

?>
