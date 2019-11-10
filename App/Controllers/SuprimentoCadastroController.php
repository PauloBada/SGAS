<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 15/10/2019
   Objetivo:  Controller para opções do menu Sumprimento Cadastro
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class SuprimentoCadastroController extends Action {

	// ================================================== //

	public function validaAutenticacao() {
		session_start();
		
		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}

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
				