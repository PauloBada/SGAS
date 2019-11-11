<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class GrupoSubgrupoController extends Action {

	public function validaAutenticacao() {
		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}

// ================================================== //

	public function atualizaqtdFamiliasSemVinculo() {
		// Busca Quantidade de Famílias sem vínculo com Subgrupo para mostrar na tela
		$qtdSemVinculoFamilia = Container::getModel('TbFml');
		$qtdSemVinculoFmlr = $qtdSemVinculoFamilia->getQtdFamiliasSemVFS();

		$this->view->qtdFamiliasSemVinculo = $qtdSemVinculoFmlr['qtde'];
	}

// ====================================================== //	

	public function grupoSubgrupo() {

		$this->validaAutenticacao();

		$this->atualizaqtdFamiliasSemVinculo();		

		$this->view->erroValidacao = 2;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		$this->render('grupoSubgrupo');
	}

// ====================================================== //

	public function grupoIncluir() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);
		
		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdFamiliasSemVinculo();		

			$this->render('grupoSubgrupo');				
		} else {
			$this->view->erroValidacao = 0;
			$this->render('grupoIncluir');
		}

	}	// Fim da function grupoIncluir 

// ====================================================== //

	public function grupoIncluirBase() {
		            
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Pesquisar se nome do Grupo já existe na tabela tb_grp
		$qtdnomeGrupoBase = Container::getModel('TbGrp');
		$qtdnomeGrupoBase->__set('nomeGrupo_pesq', $_POST['nomeGrupo']);
		$qtdnomeGrupo = $qtdnomeGrupoBase->getNomeGrupoInclusao();

		// Para validar se o nome do Grupo existe na base
		if ($qtdnomeGrupo['qtde'] > 0 ) {
			$this->view->erroValidacao = 2;
			$this->view->nomeGrupo_inserido = $_POST['nomeGrupo'];
			$this->render('grupoIncluir');				
		} else {
			// Inserir na base
			$insereGrupo = Container::getModel('TbGrp');
			$insereGrupo->__set('nomeGrupo_insere', $_POST['nomeGrupo']);
			$insereGrupo->__set('semana_insere', $_POST['semanaAtuacao']);
			$insereGrupo->insereGrupo();			

			$this->view->erroValidacao = 1;
			$this->view->nomeGrupo_inserido = $_POST['nomeGrupo'];
			$this->view->semana_inserida = $_POST['semanaAtuacao'];
			$this->render('grupoIncluir');
		}
		// */
	
	}	// Fim da function grupoIncluirBase

// ====================================================== //
	
	public function grupoAlterar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
	
			$this->atualizaqtdFamiliasSemVinculo();		

			$this->render('grupoSubgrupo');				
		} else {
			// Buscar todos os itens e remeter array para montar o combobox
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			$this->view->erroValidacao = 0;
			$this->render('grupoAlterar');
		}
	}	// Fim da function grupoAlterar

// ====================================================== //

	public function grupoAlterarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Buscar o nome da RA e a uf
		$dadosGrupo_alterar = Container::getModel('TbGrp');
		$dadosGrupo_alterar->__set('cd_grp', $_POST['grupo_escolhido']);
		
		$this->view->dadosGrupo_escolhido = $dadosGrupo_alterar->getDadosGrupo();
				
		$this->render('grupoAlterarMenu');
		
	}	// Fim da function grupoAlterarMenu


// ====================================================== //

	public function grupoAlterarBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Pesquisar se Grupo está em tb_vncl_vlnt_grp ou tb_sbgrp
		$qtdGrupoVoluntario = Container::getModel('TbGrp');
		$qtdGrupoVoluntario->__set('codGrupo_pesq', $_POST['codGrupo']);
		$qtdGrupoVlnt = $qtdGrupoVoluntario->getQtdGrupoVinculoVoluntario();

		$qtdGrupoSubgrupo = Container::getModel('TbGrp');
		$qtdGrupoSubgrupo->__set('codGrupo_pesq', $_POST['codGrupo']);
		$qtdGrupoSbgrp = $qtdGrupoSubgrupo->getQtdGrupoVinculoSubgrupo();

		// Para validar se o nome do Grupo existe na base
		if ($qtdGrupoVlnt['qtde'] > 0 || $qtdGrupoSbgrp['qtde'] > 0) {
			$this->view->dadosGrupo_escolhido =  array(
			'cod_grupo' => $_POST['codGrupo'] ,
			'nome_grupo' => $_POST['nomeGrupo'] ,
			'cod_semana' => $_POST['semanaAtuacao']);

			$this->view->erroValidacao = 2;

			$this->view->nomeGrupo_alterado = $_POST['nomeGrupo'];
			$this->render('grupoAlterarMenu');				
		} else {
			// Alterar na base
			$alteraGrupo = Container::getModel('TbGrp');
			$alteraGrupo->__set('cdGrupo_altera', $_POST['codGrupo']);
			$alteraGrupo->__set('nomeGrupo_altera', $_POST['nomeGrupo']);
			$alteraGrupo->__set('semanaAtuacao_altera', $_POST['semanaAtuacao']);
			$alteraGrupo->alteraGrupo();			

			$this->view->erroValidacao = 1;
			$this->view->codGrupo_alterado = $_POST['codGrupo'];

			// Buscar todos os itens e remeter array para montar o combobox
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			$this->render('grupoAlterar');
		}
	
	}	// Fim da function grupoAlterarBase

// ====================================================== //
	
	public function grupoEncerrar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdFamiliasSemVinculo();		

			$this->render('grupoSubgrupo');				
		} else {
			// Buscar todos os itens e remeter array para montar o combobox
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			$this->view->erroValidacao = 0;
			$this->render('grupoEncerrar');
		}
	}	// Fim da function grupoEncerrar

// ====================================================== //

	public function grupoEncerrarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Buscar o nome da RA e a uf
		$dadosGrupo_alterar = Container::getModel('TbGrp');
		$dadosGrupo_alterar->__set('cd_grp', $_POST['grupo_escolhido']);
		
		$this->view->dadosGrupo_escolhido = $dadosGrupo_alterar->getDadosGrupo();
				
		$this->render('grupoEncerrarMenu');
		
	}	// Fim da function grupoEncerrarMenu

// ====================================================== //

	public function grupoEncerrarBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Pesquisar se Grupo está em tb_vncl_vlnt_grp ou tb_sbgrp
		$qtdGrupoVoluntario = Container::getModel('TbGrp');
		$qtdGrupoVoluntario->__set('codGrupo_pesq', $_POST['codGrupo']);
		$qtdGrupoVlnt = $qtdGrupoVoluntario->getQtdGrupoVinculoVoluntario();

		$qtdGrupoSubgrupo = Container::getModel('TbGrp');
		$qtdGrupoSubgrupo->__set('codGrupo_pesq', $_POST['codGrupo']);
		$qtdGrupoSbgrp = $qtdGrupoSubgrupo->getQtdGrupoVinculoSubgrupo();

		// Para validar se o nome do Grupo existe na base
		if ($qtdGrupoVlnt['qtde'] > 0 || $qtdGrupoSbgrp['qtde'] > 0) {
			$this->view->dadosGrupo_escolhido =  array(
			'cod_grupo' => $_POST['codGrupo'] ,
			'nome_grupo' => $_POST['nomeGrupo'] ,
			'cod_semana' => $_POST['semanaAtuacao']);

			$this->view->erroValidacao = 2;

			$this->render('grupoEncerrarMenu');				
		} else {
			// Alterar na base, encerrando o Grupo
			$alteraGrupo = Container::getModel('TbGrp');
			$alteraGrupo->__set('cdGrupo_altera', $_POST['codGrupo']);
			$alteraGrupo->encerraGrupo();			

			$this->view->erroValidacao = 1;
			$this->view->codGrupo_alterado = $_POST['codGrupo'];

			// Buscar todos os itens e remeter array para montar o combobox
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			$this->render('grupoEncerrar');
		}
	
	}	// Fim da function grupoEncerrarBase

// ====================================================== //
	
	public function grupoConsultar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('grupoSubgrupo');				
		} else {
			// Buscar todos os grupos e remeter array para montar o combobox
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll2();

			$this->view->grupos = $grupo;

			$this->view->erroValidacao = 0;
			$this->render('grupoConsultar');
		}
	}	// Fim da function grupoConsultar

// ====================================================== //

	public function grupoConsultarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$dadosGrupo_alterar = Container::getModel('TbGrp');
		$dadosGrupo_alterar->__set('cd_grp', $_POST['grupo_escolhido']);
		
		$this->view->dadosGrupo_escolhido = $dadosGrupo_alterar->getDadosGrupoAll3();
				
		$this->render('grupoConsultarMenu');
		
	}	// Fim da function grupoConsultarMenu

// ====================================================== //
	
	public function subgrupoIncluir() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);
		
		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdFamiliasSemVinculo();		

			$this->render('grupoSubgrupo');				
		} else {
			// Buscar todos os Grupos e remeter array para montar o combobox
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			// Para inicializar as variáveis dos campos da View (tela de inclusão)
			$this->view->subgrupo = array (
								'grupo_escolhido' => '',
							    'nomeSubgrupo' => '',
							    'descricaoSubgrupo' => '',
							    'ra_escolhida' => '',
							    'descricaoHorario' => ""
			);

			$this->view->erroValidacao = 0;
			$this->render('subgrupoIncluir');
		}

	}	// Fim da function subgrupoIncluir

// ====================================================== //	

	public function subgrupoIncluirBase() {
		            
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Pesquisar se nome do Subgrupo já existe na base
		$qtdnomeSubgrupoBase = Container::getModel('TbSbgrp');
		$qtdnomeSubgrupoBase->__set('cdGrupo_pesq', $_POST['grupo_escolhido']);
		$qtdnomeSubgrupoBase->__set('nomeSubgrupo_pesq', $_POST['nomeSubgrupo']);
		$qtdnomeSubgrupo = $qtdnomeSubgrupoBase->getNomeSubrupoInclusao();

		// Para validar se o nome do Item existe na base
		if ($qtdnomeSubgrupo['qtde'] > 0 ) {
			$this->view->erroValidacao = 2;
			$this->view->nomeSubgrupo_inserido = $_POST['nomeSubgrupo'];

			// Reenderizar valores na tela
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->view->subgrupo = array (
								'grupo_escolhido' => $_POST['grupo_escolhido'],
							    'nomeSubgrupo' => $_POST['nomeSubgrupo'],
							    'descricaoSubgrupo' => $_POST['descricaoSubgrupo'],
							    'ra_escolhida' => $_POST['ra_escolhida'],
							    'descricaoHorario' => $_POST['descricaoHorario']
			);

			$this->render('subgrupoIncluir');
		} else {
			// Obter o próximo código de Subgrupo
			$max_seql_base = Container::getModel('TbSbgrp');
			$max_seql_base->__set('cdGrupo_insere', $_POST['grupo_escolhido']);
			$max_seql_grava = $max_seql_base->obtemNrMaxSbgrupo();	

			if ($max_seql_grava['max_seql'] == null) {
				$seql_subgrupo = 1;
			} else {
				$seql_subgrupo = $max_seql_grava['max_seql'] + 1;
			}

			$insereSubgrupo = Container::getModel('TbSbgrp');
			$insereSubgrupo->__set('cdGrupo_insere', $_POST['grupo_escolhido']);
			$insereSubgrupo->__set('cdSubgrupo_insere', $seql_subgrupo);
			$insereSubgrupo->__set('nomeSubgrupo_insere', $_POST['nomeSubgrupo']);
			$insereSubgrupo->__set('descricaoSubgrupo_insere', $_POST['descricaoSubgrupo']);
			$insereSubgrupo->__set('ra_insere', $_POST['ra_escolhida']);
			$insereSubgrupo->__set('descricaoHorario_insere', $_POST['descricaoHorario']);
			$insereSubgrupo->insereSubgrupo();			

			$this->view->erroValidacao = 1;
			$this->view->nomeSubgrupo_inserido = $_POST['nomeSubgrupo'];

			// Reenderizar valores na tela
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->view->subgrupo = array (
								'grupo_escolhido' => '',
							    'nomeSubgrupo' => '',
							    'descricaoSubgrupo' => '',
							    'ra_escolhida' => '',
							    'descricaoHorario' => ""
			);

			$this->render('subgrupoIncluir');
		}
		// */
	}	// Fim da function subgrupoIncluirBase

// ====================================================== //	
	
	public function subgrupoAlterar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdFamiliasSemVinculo();		

			$this->render('grupoSubgrupo');				
		} else {
			$this->view->erroValidacao = 0;
			$this->render('SubgrupoAlterar');
		}
	}	// Fim da function subgrupoAlterar

// ====================================================== //	

	public function subgrupoAlterarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Testar se o cd_sugrupo_escolhido não é numérico (está "Escolha Subgrupo")
		if (!is_numeric($_POST['cb_subgrupo_escolhido'])) {
			$this->view->erroValidacao = 3;
			$this->render('subgrupoAlterar');
		
		} else {
			// Verificar se Subgrupo está em tb_vncl_vlnt_grp/tb_vncl_fml_sbgrp/tb_pedido_recur_finan, se estiver
			// não deixar alterar Região Administrativa

			// Verifica se está em tb_vncl_vlnt_grp
			$qtdSubgrupoVoluntario = Container::getModel('TbVnclVlntGrp');
			$qtdSubgrupoVoluntario->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$qtdSubgrupoVoluntario->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$qtdSubgrupoVlnt = $qtdSubgrupoVoluntario->getQtdSubgrupoVinculoVoluntario();
			
			// Verifica se está em tb_vncl_fml_sbgrp
			$qtdSubgrupoFamilia = Container::getModel('TbSbgrp');
			$qtdSubgrupoFamilia->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$qtdSubgrupoFamilia->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$qtdSubgrupoFml = $qtdSubgrupoFamilia->getQtdSubgrupoVinculoFamilia();

			// Verifica se está em tb_pedido_recur_finan
			$qtdSubgrupoFinanceiro = Container::getModel('TbSbgrp');
			$qtdSubgrupoFinanceiro->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$qtdSubgrupoFinanceiro->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$qtdSubgrupoFinan = $qtdSubgrupoFinanceiro->getQtdSubgrupoFinanceiro();

			// Buscar dados Subgrupo 
			$dadosSubgrupo_alterar = Container::getModel('TbSbgrp');
			$dadosSubgrupo_alterar->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$dadosSubgrupo_alterar->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$this->view->dadosSubgrupo_escolhido = $dadosSubgrupo_alterar->getDadosSubgrupo();

			if ($qtdSubgrupoVlnt['qtde'] > 0 || $qtdSubgrupoFml['qtde'] > 0 || $qtdSubgrupoFinan['qtde'] > 0) {
				$this->view->dadosSubgrupo_escolhido['altera_ra'] = 'nao';
			} else {
				$this->view->dadosSubgrupo_escolhido['altera_ra'] = 'sim';
			}

			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;
				
			$this->render('subgrupoAlterarMenu');
		}

	}	// Fim da function subgrupoAlterarMenu

// ====================================================== //	

	public function subgrupoAlterarBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Pesquisar se nome do Subgrupo já existe na base
		$qtdnomeSubgrupoBase = Container::getModel('TbSbgrp');
		$qtdnomeSubgrupoBase->__set('codGrupo_pesq', $_POST['codGrupo']);
		$qtdnomeSubgrupoBase->__set('codSubgrupo_pesq', $_POST['codSubgrupo']);
		$qtdnomeSubgrupoBase->__set('nomeSubgrupo_pesq', $_POST['nomeSubgrupo']);
		$qtdnomeSubgrupo = $qtdnomeSubgrupoBase->getNomeSubrupoAlteracao();

		// Para validar se o nome do Item existe na base
		if ($qtdnomeSubgrupo['qtde'] > 0 ) {
			$this->view->erroValidacao = 2;
			$this->view->nomeSubgrupo_inserido = $_POST['nomeSubgrupo'];

			// Reenderizar valores na tela
			$grupoBase = Container::getModel('TbGrp');
			$grupo = $grupoBase->getDadosGrupoAll();

			$this->view->grupos = $grupo;

			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->view->dadosSubgrupo_escolhido = array (
								'cod_grupo' => $_POST['codGrupo'],
							    'nome_grupo' => $_POST['nomeGrupo'],
							    'cod_subgrupo' => $_POST['codSubgrupo'],
							    'nome_subgrupo' => $_POST['nomeSubgrupo'],
							    'desc_subgrupo' => $_POST['descricaoSubgrupo'],
							    'altera_ra' => $_POST['altera_ra'],
							    'ra_subgrupo' => $_POST['ra_subgrupo'],
							    'desc_horario' => $_POST['descricaoHorario']
			);

			$this->render('subgrupoAlterarMenu');
		} else {
			// Alterar na base
			$insereSubgrupo = Container::getModel('TbSbgrp');
			$insereSubgrupo->__set('codGrupo_altera', $_POST['codGrupo']);
			$insereSubgrupo->__set('codSubgrupo_altera', $_POST['codSubgrupo']);
			$insereSubgrupo->__set('nomeSubgrupo_altera', $_POST['nomeSubgrupo']);
			$insereSubgrupo->__set('descricaoSubgrupo_altera', $_POST['descricaoSubgrupo']);
			$insereSubgrupo->__set('ra_altera', $_POST['ra_subgrupo']);
			$insereSubgrupo->__set('descricaoHorario_altera', $_POST['descricaoHorario']);
			$insereSubgrupo->alteraSubgrupo();			

			$this->view->erroValidacao = 1;
			$this->view->nomeSubgrupo_alterado = $_POST['nomeSubgrupo'];

			$this->render('subgrupoAlterar');
		}

	}	// Fim da function subgrupoAlterarBase

// ====================================================== //	
	
	public function subgrupoEncerrar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdFamiliasSemVinculo();		
			
			$this->render('grupoSubgrupo');				
		} else {
			$this->view->erroValidacao = 0;
			$this->render('subgrupoEncerrar');
		}
	}	// Fim da function subgrupoEncerrar

// ====================================================== //	

	public function subgrupoEncerrarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Testar se o cd_sugrupo_escolhido não é numérico (está "Escolha Subgrupo")
		if (!is_numeric($_POST['cb_subgrupo_escolhido'])) {
			$this->view->erroValidacao = 3;
			$this->render('subgrupoEncerrar');
		} else {
			// Verificar se Subgrupo está em tb_vncl_vlnt_grp/tb_vncl_fml_sbgrp/tb_pedido_recur_finan, se estiver
			// não deixar alterar Região Administrativa

			// Verifica se está em tb_vncl_vlnt_grp
			$qtdSubgrupoVoluntario = Container::getModel('TbVnclVlntGrp');
			$qtdSubgrupoVoluntario->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$qtdSubgrupoVoluntario->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$qtdSubgrupoVlnt = $qtdSubgrupoVoluntario->getQtdSubgrupoVinculoVoluntario();
			
			// Verifica se está em tb_vncl_fml_sbgrp
			$qtdSubgrupoFamilia = Container::getModel('TbSbgrp');
			$qtdSubgrupoFamilia->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$qtdSubgrupoFamilia->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$qtdSubgrupoFml = $qtdSubgrupoFamilia->getQtdSubgrupoVinculoFamilia();

			// Verifica se está em tb_pedido_recur_finan
			$qtdSubgrupoFinanceiro = Container::getModel('TbSbgrp');
			$qtdSubgrupoFinanceiro->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$qtdSubgrupoFinanceiro->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$qtdSubgrupoFinan = $qtdSubgrupoFinanceiro->getQtdSubgrupoFinanceiro();

			// Buscar dados Subgrupo 
			$dadosSubgrupo_alterar = Container::getModel('TbSbgrp');
			$dadosSubgrupo_alterar->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$dadosSubgrupo_alterar->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$this->view->dadosSubgrupo_escolhido = $dadosSubgrupo_alterar->getDadosSubgrupo();

			if ($qtdSubgrupoVlnt['qtde'] > 0 || $qtdSubgrupoFml['qtde'] > 0 || $qtdSubgrupoFinan['qtde'] > 0) {
				$this->view->erroValidacao = 4;
				$this->view->nomeGrupo = $this->view->dadosSubgrupo_escolhido['nome_grupo'];
				$this->view->nomeSubgrupo = $this->view->dadosSubgrupo_escolhido['nome_subgrupo'];
				$this->render('subgrupoEncerrar');
			} else {
				// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
				$regioesAdm = Container::getModel('TbRegAdm');
				$regioesBase = $regioesAdm->getDadosRAAll();

				$this->view->regioes = $regioesBase;
					
				$this->render('subgrupoEncerrarMenu');
			}
		}

	}	// Fim da function subgrupoEncerrarMenu

// ====================================================== //	

	public function subgrupoEncerrarBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Alterar na base
		$insereSubgrupo = Container::getModel('TbSbgrp');
		$insereSubgrupo->__set('codGrupo_altera', $_POST['codGrupo']);
		$insereSubgrupo->__set('codSubgrupo_altera', $_POST['codSubgrupo']);
		$insereSubgrupo->encerraSubgrupo();			

		$this->view->erroValidacao = 1;
		$this->view->nomeGrupo_alterado = $_POST['nomeGrupo'];
		$this->view->nomeSubgrupo_alterado = $_POST['nomeSubgrupo'];

		$this->render('subgrupoEncerrar');

	}	// Fim da function subgrupoEncerrarBase

// ====================================================== //	
	
	public function subgrupoConsultar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('grupoSubgrupo');				
		} else {
			$this->view->erroValidacao = 0;
			$this->render('subgrupoConsultar');
		}
	}	// Fim da function subgrupoConsultar

// ====================================================== //	

	public function subgrupoConsultarMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Testar se o cd_sugrupo_escolhido não é numérico (está "Escolha Subgrupo")
		if (!is_numeric($_POST['cb_subgrupo_escolhido'])) {
			$this->view->erroValidacao = 3;
			$this->render('subgrupoConsultar');
		} else {
			// Buscar dados Subgrupo 
			$dadosSubgrupo_alterar = Container::getModel('TbSbgrp');
			$dadosSubgrupo_alterar->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$dadosSubgrupo_alterar->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$this->view->dadosSubgrupo_escolhido = $dadosSubgrupo_alterar->getDadosSubgrupoAll();

			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;
				
			$this->render('subgrupoConsultarMenu');

		}

	}	// Fim da function subgrupoConsultarMenu

// As funções abaixo vieram de VoluntarioController.php

// ====================================================== //	
	
	public function subgrupoVincularVoluntario() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('grupoSubgrupo');
							
		} else {
			$this->view->erroValidacao = 0;

			// Buscar todos os voluntários da base
			$voluntariosAll = Container::getModel('TbVlnt');
			//$voluntariosBase = $voluntariosAll->getDadosVoluntariosAll();
			$voluntariosBase = $voluntariosAll->getDadosVoluntariosAllComSemVinculo();

			$this->view->voluntarios = $voluntariosBase;

			$this->view->vinculo = array (
				'cb_grupo_escolhido' => '',
				'cb_subgrupo_escolhido' => '',
				'voluntario_escolhido' => '',
				'atuacao_escolhida' => ''
			);

			$this->render('subgrupoVincularVoluntario');
		}
	}	// Fim da function subgrupoVincularVoluntario

// ====================================================== //	

	public function subgrupoVincularVoluntarioBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Valida se Grupo foi escolhido
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" ||
			$_POST['voluntario_escolhido'] == "" ||
			$_POST['atuacao_escolhida'] == "") {
			$this->view->erroValidacao = 2;

			// Buscar todos os voluntários da base
			$voluntariosAll = Container::getModel('TbVlnt');
			$voluntariosBase = $voluntariosAll->getDadosVoluntariosAllComSemVinculo();

			$this->view->voluntarios = $voluntariosBase;

			$this->view->vinculo = array (
				'cb_grupo_escolhido' => $_POST['cb_grupo_escolhido'],
				'cb_subgrupo_escolhido' => $_POST['cb_subgrupo_escolhido'],
				'voluntario_escolhido' => $_POST['voluntario_escolhido'],
				'atuacao_escolhida' => $_POST['atuacao_escolhida']
			);

			$this->render('subgrupoVincularVoluntario');
		} else { 

			if ($_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
				$codSubgrupo_pesq = '';
			} else {
				$codSubgrupo_pesq = $_POST['cb_subgrupo_escolhido'];
			}

			// Verifica se Grupo e Voluntário estão em tb_vncl_vlnt_grp
			$qtdGrupoVoluntario = Container::getModel('TbVnclVlntGrp');
			$qtdGrupoVoluntario->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$qtdGrupoVoluntario->__set('codSubgrupo_pesq', $codSubgrupo_pesq);
			$qtdGrupoVoluntario->__set('codVoluntario_pesq', $_POST['voluntario_escolhido']);
			$qtdGrupoVlnt = $qtdGrupoVoluntario->getQtdGrupoVoluntario();

			if ($qtdGrupoVlnt['qtde'] > 0) {

				// Não houve subgrupo escolhido no on line	
				if ($_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
					// Já há vinculo para este Grupo somente
					$this->view->erroValidacao = 3;

					// Buscar Nome de Grupo
					$dadosGrupo = Container::getModel('TbGrp');
					$dadosGrupo->__set('cd_grp', $_POST['cb_grupo_escolhido']);
					$dadosGrp = $dadosGrupo->getDadosGrupo();

					$this->view->grupoTratado  = $dadosGrp['nome_grupo'];

					// Buscar todos os voluntários da base
					$voluntariosAll = Container::getModel('TbVlnt');
					$voluntariosBase = $voluntariosAll->getDadosVoluntariosAllComSemVinculo();

					$this->view->voluntarios = $voluntariosBase;

					$this->view->vinculo = array (
						'cb_grupo_escolhido' => $_POST['cb_grupo_escolhido'],
						'cb_subgrupo_escolhido' => $_POST['cb_subgrupo_escolhido'],
						'voluntario_escolhido' => $_POST['voluntario_escolhido'],
						'atuacao_escolhida' => $_POST['atuacao_escolhida']
					);

					$this->render('subgrupoVincularVoluntario');		
				
				} else {													

					// Já há vinculo para este Grupo e Subgrupo
					$this->view->erroValidacao = 4;

					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

					$this->view->grupoTratado = $dadosGS['nome_grupo'];
					$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

					// Buscar todos os voluntários da base
					$voluntariosAll = Container::getModel('TbVlnt');
					$voluntariosBase = $voluntariosAll->getDadosVoluntariosAllComSemVinculo();

					$this->view->voluntarios = $voluntariosBase;

					$this->view->vinculo = array (
						'cb_grupo_escolhido' => $_POST['cb_grupo_escolhido'],
						'cb_subgrupo_escolhido' => $_POST['cb_subgrupo_escolhido'],
						'voluntario_escolhido' => $_POST['voluntario_escolhido'],
						'atuacao_escolhida' => $_POST['atuacao_escolhida']
					);

					$this->render('subgrupoVincularVoluntario');		
				}

			} else {
				// 6o teste - Não há grupo e subgrupo cadastrados
				if ($_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
					$subgrupo_grava = '';
				} else {
					$subgrupo_grava = $_POST['cb_subgrupo_escolhido'];
				}

				// Insere na tabela tb_vncl_vlnt_grp
				$insereTbVinculo = Container::getModel('TbVnclVlntGrp');
				$insereTbVinculo->__set('codVoluntario', $_POST['voluntario_escolhido']);
				$insereTbVinculo->__set('codGrupo', $_POST['cb_grupo_escolhido']);
				$insereTbVinculo->__set('codSubgrupo', $subgrupo_grava);
				$insereTbVinculo->__set('codAtuacao', $_POST['atuacao_escolhida']);
				$insereTbVinculo->insertVinculo();

				// Buscar todos os voluntários da base
				$voluntariosAll = Container::getModel('TbVlnt');
				$voluntariosBase = $voluntariosAll->getDadosVoluntariosAllComSemVinculo();

				$this->view->voluntarios = $voluntariosBase;

				$this->view->erroValidacao = 1;

				$this->view->vinculo = array (
					'cb_grupo_escolhido' => '',
					'cb_subgrupo_escolhido' => '',
					'voluntario_escolhido' => '',
					'atuacao_escolhida' => ''
				);

				$this->render('subgrupoVincularVoluntario');		
			
			}
		}
		// */
	}	// Fim da function subgrupoVincularVoluntarioBase		

// ====================================================== //	
	
	public function subgrupoDesvincularVoluntario() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('grupoSubgrupo');				
		} else {
			$this->view->erroValidacao = 0;

			$this->render('subgrupoDesvincularVoluntario');
		}
	}	// Fim da function subgrupoDesvincularVoluntario

// ====================================================== //	

	public function subgrupoDesvincularVoluntarioMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo" || 
		    $_POST['cb_voluntario_escolhido'] == "Escolha Voluntário") {

			$this->view->erroValidacao = 2;	
		
			$this->render('subgrupoDesvincularVoluntario');	
		} 

		// Busca Nome Voluntário
		$nomeVoluntario = Container::getModel('TbVlnt');
		$nomeVoluntario->__set('id', $_POST['cb_voluntario_escolhido']);
		$nomeVlnt = $nomeVoluntario->getInfoVoluntario();
	
		$nomeVlnt = $nomeVlnt['nm_vlnt'];

		// Busca Nome Grupo
		$nomeGrupoBase = Container::getModel('TbGrp');
		$nomeGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$nomeGrupo = $nomeGrupoBase->getDadosGrupo();

		$nomeGrp = $nomeGrupo['nome_grupo'];

		if ($_POST['cb_subgrupo_escolhido'] == "Sem Subgrupo") {
			$codSbgrp =  '';
			$nomeSbgrp = '';
		} else {
			$codSbgrp =  $_POST['cb_subgrupo_escolhido'];

			// Busca Nome Subgrupo
			$nomeSubgrupo = Container::getModel('TbSbgrp');
			$nomeSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$nomeSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$nomeSbgrp = $nomeSubgrupo->getDadosSubgrupo();

			$nomeSbgrp = $nomeSbgrp['nome_subgrupo'];
		}

		// Busca Perfil de Atuacao com subgrupo preenchido TESTAR NULO NA CONSULTA
		$perfilAtuacao = Container::getModel('TbVnclVlntGrp');
		$perfilAtuacao->__set('codVoluntario_pesq', $_POST['cb_voluntario_escolhido']);
		$perfilAtuacao->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$perfilAtuacao->__set('codSubgrupo_pesq', $codSbgrp);
		$perfilAtu = $perfilAtuacao->getDadosVVG();

		$perfil = $perfilAtu['cod_atuacao_base'];
		$sequencial = $perfilAtu['sequencial_base'];

		$this->view->dadosTVVG = array (
					'cod_grupo' => $_POST['cb_grupo_escolhido'],
					'nome_grupo' => $nomeGrp,
					'cod_subgrupo' => $codSbgrp,
					'nome_subgrupo' => $nomeSbgrp,
					'cod_voluntario' => $_POST['cb_voluntario_escolhido'],
					'nome_voluntario' => $nomeVlnt,
					'perfil_atuacao' => $perfil,
					'sequencial' =>  $sequencial
					);
			
		$this->render('subgrupoDesvincularVoluntarioMenu');

	}	// Fim da function subgrupoDesvincularVoluntarioMenu

// ====================================================== //	

	public function subgrupoDesvincularVoluntarioBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 1;

		// Encerra vinculo
		$encerraVinculo = Container::getModel('TbVnclVlntGrp');
		$encerraVinculo->__set('codVoluntario', $_POST['codVoluntario']);
		$encerraVinculo->__set('codGrupo', $_POST['codGrupo']);
		$encerraVinculo->__set('sequencial', $_POST['sequencial']);
		$encerraVinculo->encerraVVG();

		$this->view->nomeGrupo = $_POST['nomeGrupo'];
		$this->view->nomeVoluntario = $_POST['nomeVoluntario'];

		if (empty($_POST['codSubgrupo'])) { 
			$this->view->nomeSubgrupo = 'Sem Subgrupo';
		} else {
			$this->view->nomeSubgrupo = $_POST['nomeSubgrupo'];
		}

		$this->render('subgrupoDesvincularVoluntario');

	}	// Fim da function subgrupoDesvincularVoluntarioBase

// ====================================================== //	
	
	public function subgrupoConsultarVinculoVoluntario() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('grupoSubgrupo');				

		} else {

			$this->view->erroValidacao = 0;

			// Um ano de período
			$periodo = new \Dateinterval("P1Y");

			// Data de hoje
			$dt_inicial = new \DateTime();
			// Subtrai um ano
			$dt_inicial->sub($periodo);

			// Data de hoje
			$dt_final = new \DateTime();
			// Soma um ano
			$dt_final->add($periodo);

			// Transforma as datas em string DD/MM/AAAA
			$dt_inicial	= $dt_inicial->format("d/m/Y");
			$dt_final = $dt_final->format("d/m/Y");

			$this->view->datas = array (
				'data_inicial' => $dt_inicial,
				'data_final' => $dt_final
			);

			$this->render('subgrupoConsultarVinculoVoluntario');
		}
	}	// Fim da function subgrupoConsultarVinculoVoluntario

// ====================================================== //	

	public function subgrupoConsultarVinculoVoluntarioMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo") {

			$this->view->erroValidacao = 2;	

			$this->view->datas = array (
				'data_inicial' => $_POST['dt_inc'],
				'data_final' => $_POST['dt_fim']
			);
		
			$this->render('subgrupoConsultarVinculoVoluntario');	

		} else {

			// Validar se datas válidas
			
			// Data recebida $_POST no formato DD/MM/AAAA
			$valida_data_inicio = Funcoes::ValidaData($_POST['dt_inc']);
			$valida_data_fim = Funcoes::ValidaData($_POST['dt_fim']);

			if ($valida_data_inicio == 0 || $valida_data_fim == 0) {
				$this->view->erroValidacao = 3;	

				$this->view->datas = array (
					'data_inicial' => $_POST['dt_inc'],
					'data_final' => $_POST['dt_fim']
				);
				
				$this->render('subgrupoConsultarVinculoVoluntario');	
			
			} else {
			
				// Validar se Data Inicial é maior que Data Final
				$data_inicio_format = str_replace('/', '-', $_POST['dt_inc']);
				$data_fim_format = str_replace('/', '-', $_POST['dt_fim']);

				// O barra "\" antes do DataTime foi devido ao namespace utilizado neste programa, pois sem a barra não reconhecia. Dica pega na internet.
				$data_inicio = new \DateTime($data_inicio_format);		
				$data_fim = new \DateTime($data_fim_format);		

				if($data_inicio > $data_fim) {
					$this->view->erroValidacao = 4;	

					$this->view->datas = array (
						'data_inicial' => $_POST['dt_inc'],
						'data_final' => $_POST['dt_fim']
					);
					
					$this->render('subgrupoConsultarVinculoVoluntario');	

				} else {

					// Busca Nome Grupo
					$nomeGrupoBase = Container::getModel('TbGrp');
					$nomeGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
					$nomeGrupo = $nomeGrupoBase->getDadosGrupo();

					$nomeGrp = $nomeGrupo['nome_grupo'];

					if ($_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
						$codSbgrp =  '';
						$nomeSbgrp = '';
					
					} else {
						$codSbgrp =  $_POST['cb_subgrupo_escolhido'];

						// Busca Nome Subgrupo
						$nomeSubgrupo = Container::getModel('TbSbgrp');
						$nomeSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
						$nomeSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
						$nomeSbgrp = $nomeSubgrupo->getDadosSubgrupo();

						$nomeSbgrp = $nomeSbgrp['nome_subgrupo'];
					}

					// Busca Perfil de Atuacao com subgrupo preenchido
					$perfilAtuacao = Container::getModel('TbVnclVlntGrp');
					$perfilAtuacao->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$perfilAtuacao->__set('codSubgrupo_pesq', $codSbgrp);
					$perfilAtuacao->__set('dataInicio_pesq', $_POST['dt_inc']);
					$perfilAtuacao->__set('dataFim_pesq', $_POST['dt_fim']);
					$perfilAtu = $perfilAtuacao->getDadosVVGAll_2();

					$this->view->dadosTVVG = array ();

					foreach ($perfilAtu as $index => $arr) {
					
						array_push($this->view->dadosTVVG, array (
								'sequencial' => $arr['sequencial_base'],
								'cd_vlnt' => $arr['cd_vlnt'],
								'nm_vlnt' => $arr['nm_vlnt'],
								'perfil_atuacao' => $arr['cod_atuacao_base'],
								'data_inicio_vinculo' =>  $arr['data_inicio_vinculo'],
								'data_fim_vinculo' =>  $arr['data_fim_vinculo'],
								'situacao_vinculo' =>  $arr['estado_vinculo'],
								'nm_sbgrp' =>  $arr['nm_sbgrp'],
								'cb_grupo_escolhido' => $_POST['cb_grupo_escolhido'],
								'cb_subgrupo_escolhido' => $_POST['cb_subgrupo_escolhido'],
								'data_inicial' => $_POST['dt_inc'],
								'data_final' => $_POST['dt_fim'],
						));
					}	

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrp;
					$this->view->codSubgrupo = $codSbgrp;
					$this->view->nomeSubgrupo = $nomeSbgrp;

					$this->render('subgrupoConsultarVinculoVoluntarioMenu');

				}
			}
		}
	}	// Fim da function subgrupoConsultarVinculoVoluntarioMenu

// As funções abaixo vieram de FamiliaCadastroController.php

// ====================================================== //	
	
	public function subgrupoVincularFamilia() {
		
		$this->validaAutenticacao();		

		// Verifica se tem nível de acesso requerido
		$nivel_acesso_requerido = 2;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('grupoSubgrupo');				
		
		} else {
			$this->view->erroValidacao = 0;

			// Buscar todoas as famílias da base
			$familiasAll = Container::getModel('TbFml');
			$familiasBase = $familiasAll->getDadosFamiliasAll();

			$this->view->familias = $familiasBase;

			$this->view->vinculo = array (
				'cb_grupo_escolhido' => '',
				'cb_subgrupo_escolhido' => '',
				'cb_familia_escolhida' => ''
			);

			$this->render('subgrupoVincularFamilia');		
		}
	}	// Fim da function subgrupoVincularFamilia

// ====================================================== //	

	public function subgrupoVincularFamiliaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Valida se Grupo foi escolhido
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo" ||
			$_POST['cb_familia_escolhida'] == "Escolha Família") {
			$this->view->erroValidacao = 2;

			// Buscar todas as famílias da base
			$familiasAll = Container::getModel('TbFml');
			$familiasBase = $familiasAll->getDadosFamiliasAll();

			$this->view->familias = $familiasBase;

			$this->view->vinculo = array (
				'cb_grupo_escolhido' => '',
				'cb_subgrupo_escolhido' => '',
				'cb_familia_escolhida' => $_POST['cb_familia_escolhida']
			);

			$this->render('subgrupoVincularFamilia');

		} else { 
			// Verifica se Familia já tem vinculo cadastrado em tb_vncl_fml_sgrp
			$qtdGrupoFamilia = Container::getModel('TbVnclFmlSbgrp');
			$qtdGrupoFamilia->__set('codFamilia_pesq', $_POST['cb_familia_escolhida']);
			$qtdGrupoFml = $qtdGrupoFamilia->getQtdSubgrupoVinculoFamilia();
                                             
			if ($qtdGrupoFml['qtde'] > 0) {
				
				$this->view->erroValidacao = 3;
				
				// Buscar dados atuais do cadastro da família no vínculo
				$dadosFamiliaAtual = Container::getModel('TbVnclFmlSbgrp');
				$dadosFamiliaAtual->__set('codFamilia_pesq', $_POST['cb_familia_escolhida']);
				$dadosFmlAtual = $dadosFamiliaAtual->getDadosVinculoFamilia();

				// Buscar Nome de Grupo e Subgrupo do cadastro da família no vínculo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $dadosFmlAtual['cd_grpID']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $dadosFmlAtual['cd_sbgrpID']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

				// Buscar Nome da Família
				$dadosFamilia = Container::getModel('TbFml');
				$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
				$dadosFml = $dadosFamilia->getDadosFamilia();

				$this->view->grupoTratado = $dadosGS['nome_grupo'];
				$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];
				$this->view->familiaTratada = $dadosFml['nm_grp_fmlr'];

				// Buscar todas as famílias da base
				$familiasAll = Container::getModel('TbFml');
				$familiasBase = $familiasAll->getDadosFamiliasAll();

				$this->view->familias = $familiasBase;

				$this->view->vinculo = array (
					'cb_grupo_escolhido' => '',
					'cb_subgrupo_escolhido' => '',
					'cb_familia_escolhida' => ''
				);

				$this->render('subgrupoVincularFamilia');

			} else {
				// Verifica se tem atuacao requerida no grupo/Subgrupo e se é de Coordenador Geral
				// Tem que pesquisar por Subgrupo, pois o voluntário logado pode estar em mais de um subgrupo e tem
				// que pertencer ao subgrupo sendo tratado
				$nivel_atuacao_requerido = 4;  								// Coordenador Geral
				
				$atuacaoVoluntarioBase = Container::getModel('TbVnclVlntGrp');
				$atuacaoVoluntarioBase->__set('codVoluntario', $_SESSION['id']);
				$atuacaoVoluntarioBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
				$atuacaoVoluntarioBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
				$atuacaoVoluntario = $atuacaoVoluntarioBase->getNivelAtuacao();

				if (empty($atuacaoVoluntario['cod_atuacao'])) { 				// Não está na tabela de vinculo
					$this->view->erroValidacao = 4;

					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

					$this->view->grupoTratado = $dadosGS['nome_grupo'];
					$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

					$this->render('subgrupoVincularFamilia');				
				
				} else {										
					if ($atuacaoVoluntario['cod_atuacao'] != $nivel_atuacao_requerido) { // Não é Coordenador Geral
						$this->view->erroValidacao = 5;

						// Buscar Nome de Grupo e Subgrupo
						$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
						$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
						$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
						$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

						$this->view->grupoTratado = $dadosGS['nome_grupo'];
						$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

						$this->view->atuacaoRequerida = 'Coordenador';
						$this->view->atuacaoLogado = 'Voluntário Normal';
						$this->render('subgrupoVincularFamilia');				
					} else {
						// Insere na tabela tb_vncl_fml_sbgrp
						$insereTbVinculo = Container::getModel('TbVnclFmlSbgrp');
						$insereTbVinculo->__set('codGrupo', $_POST['cb_grupo_escolhido']);
						$insereTbVinculo->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
						$insereTbVinculo->__set('codFamilia',  $_POST['cb_familia_escolhida']);
						$insereTbVinculo->insertVinculo();

						// Altera situação da Família (seta para 2 o cd_est_situ_fml)
						$alteraSituacaoFamilia = Container::getModel('TbFml');
						$alteraSituacaoFamilia->__set('codFamilia',  $_POST['cb_familia_escolhida']);
						$alteraSituacaoFamilia->__set('situFamilia',  2);
						$alteraSituacaoFamilia->updateSituFamilia();

						// Buscar todas as famílias da base
						$familiasAll = Container::getModel('TbFml');
						$familiasBase = $familiasAll->getDadosFamiliasAll();

						$this->view->familias = $familiasBase;

						$this->view->erroValidacao = 1;

						// Buscar Nome de Grupo e Subgrupo
						$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
						$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
						$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
						$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

						// Buscar Nome da Família
						$dadosFamilia = Container::getModel('TbFml');
						$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
						$dadosFml = $dadosFamilia->getDadosFamilia();

						$this->view->grupoTratado  = $dadosGS['nome_grupo'];
						$this->view->subgrupoTratado  = $dadosGS['nome_subgrupo'];
						$this->view->familiaTratada  = $dadosFml['nm_grp_fmlr'];

						$this->view->vinculo = array (
							'cb_grupo_escolhido' => '',
							'cb_subgrupo_escolhido' => '',
							'cb_familia_escolhida' => ''
						);

						$this->render('subgrupoVincularFamilia');
					}
				}
			}
		}
	}	// Fim da function subgrupoVincularFamiliaBase		

// ====================================================== //	
	
	public function subgrupoDesvincularFamilia() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('grupoSubgrupo');				
		} else {
			$this->view->erroValidacao = 0;

			$this->render('subgrupoDesvincularFamilia');
		}
	}	// Fim da function subgrupoDesvincularFamilia


// ====================================================== //	

	public function subgrupoDesvincularFamiliaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo" || 
		    $_POST['cb_familia_escolhida'] == "Escolha Família") {

			$this->view->erroValidacao = 2;	
		
			$this->render('subgrupoDesvincularFamilia');	
		} else {
			// Verifica se tem atuacao requerida no grupo/Subgrupo e se é de Coordenador Geral
			// Tem que pesquisar por Subgrupo, pois o voluntário logado pode estar em mais de um subgrupo e tem
			// que pertencer ao subgrupo sendo tratado
			$nivel_atuacao_requerido = 4;  								// Coordenador Geral
			
			$atuacaoVoluntarioBase = Container::getModel('TbVnclVlntGrp');
			$atuacaoVoluntarioBase->__set('codVoluntario', $_SESSION['id']);
			$atuacaoVoluntarioBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
			$atuacaoVoluntarioBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
			$atuacaoVoluntario = $atuacaoVoluntarioBase->getNivelAtuacao();

			if (empty($atuacaoVoluntario['cod_atuacao'])) { 				// Não está na tabela de vinculo
				$this->view->erroValidacao = 4;

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

				$this->view->grupoTratado = $dadosGS['nome_grupo'];
				$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

				$this->render('subgrupoDesvincularFamilia');				
			
			} else {										
				if ($atuacaoVoluntario['cod_atuacao'] != $nivel_atuacao_requerido) { // Não é Coordenador Geral
					$this->view->erroValidacao = 5;

					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

					$this->view->grupoTratado = $dadosGS['nome_grupo'];
					$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

					$this->view->atuacaoRequerida = 'Coordenador';
					$this->view->atuacaoLogado = 'Voluntário Normal';
					$this->render('subgrupoDesvincularFamilia');				
				} else {
					// Buscar Nome da Família
					$dadosFamilia = Container::getModel('TbFml');
					$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
					$dadosFml = $dadosFamilia->getDadosFamilia();
				
					$nomeFml = $dadosFml['nm_grp_fmlr'];

					// Busca Nome Grupo
					$nomeGrupoBase = Container::getModel('TbGrp');
					$nomeGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
					$nomeGrupo = $nomeGrupoBase->getDadosGrupo();

					$nomeGrp = $nomeGrupo['nome_grupo'];

					// Busca Nome Subgrupo
					$nomeSubgrupo = Container::getModel('TbSbgrp');
					$nomeSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$nomeSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$nomeSbgrp = $nomeSubgrupo->getDadosSubgrupo();

					$nomeSbgrp = $nomeSbgrp['nome_subgrupo'];

					// Buscar Sequencial da tabela tb_vncl_fml_sbgrp, para alteração na base
					$dadosFamiliaAtual = Container::getModel('TbVnclFmlSbgrp');
					$dadosFamiliaAtual->__set('codFamilia_pesq', $_POST['cb_familia_escolhida']);
					$dadosFmlAtual = $dadosFamiliaAtual->getDadosVinculoFamilia();

					$sequencial = $dadosFmlAtual['seql_vnclID'];

					$this->view->dadosTVFS = array (
								'cod_grupo' => $_POST['cb_grupo_escolhido'],
								'nome_grupo' => $nomeGrp,
								'cod_subgrupo' => $_POST['cb_subgrupo_escolhido'],
								'nome_subgrupo' => $nomeSbgrp,
								'cod_familia' => $_POST['cb_familia_escolhida'],
								'nome_familia' => $nomeFml,
								'sequencial' =>  $sequencial
								);
					$this->render('subgrupoDesvincularFamiliaMenu');
				}
			}
		}
	}	// Fim da function subgrupoDesvincularFamiliaMenu

// ====================================================== //	

	public function subgrupoDesvincularFamiliaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 1;

		// Encerra vinculo
		$encerraVinculo = Container::getModel('TbVnclFmlSbgrp');
		$encerraVinculo->__set('codGrupo', $_POST['codGrupo']);
		$encerraVinculo->__set('codSubgrupo', $_POST['codSubgrupo']);
		$encerraVinculo->__set('codFamilia', $_POST['codFamilia']);
		$encerraVinculo->__set('sequencial', $_POST['sequencial']);
		$encerraVinculo->encerraVFS();

		// Altera situação da Família (seta para 1 o cd_est_situ_fml)
		$alteraSituacaoFamilia = Container::getModel('TbFml');
		$alteraSituacaoFamilia->__set('codFamilia',  $_POST['codFamilia']);
		$alteraSituacaoFamilia->__set('situFamilia',  1);
		$alteraSituacaoFamilia->updateSituFamilia();


		$this->view->nomeGrupo = $_POST['nomeGrupo'];
		$this->view->nomeSubgrupo = $_POST['nomeSubgrupo'];
		$this->view->nomeFamilia = $_POST['nomeFamilia'];

		$this->render('subgrupoDesvincularFamilia');

	}	// Fim da function subgrupoDesvincularFamiliaBase

// ====================================================== //	
	
	public function subgrupoConsultarVinculoFamilia() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('grupoSubgrupo');				
		
		} else {
			$this->view->erroValidacao = 0;

			// Um ano de período
			$periodo = new \Dateinterval("P1Y");

			// Data de hoje
			$dt_inicial = new \DateTime();
			// Subtrai um ano
			$dt_inicial->sub($periodo);

			// Data de hoje
			$dt_final = new \DateTime();
			// Soma um ano
			$dt_final->add($periodo);

			// Transforma as datas em string DD/MM/AAAA
			$dt_inicial	= $dt_inicial->format("d/m/Y");
			$dt_final = $dt_final->format("d/m/Y");

			$this->view->datas = array (
				'data_inicial' => $dt_inicial,
				'data_final' => $dt_final
			);

			$this->render('subgrupoConsultarVinculoFamilia');
		}
	}	// Fim da function subgrupoConsultarVinculoFamilia

// ====================================================== //	

	public function subgrupoConsultarVinculoFamiliaMenu() {
		
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {

			$this->view->erroValidacao = 2;	

			$this->view->datas = array (
				'data_inicial' => $_POST['dt_inc'],
				'data_final' => $_POST['dt_fim'],
				'rota' => $_POST['rota']
			);
			
			$this->render('subgrupoConsultarVinculoFamilia');	

		} else {
			// Validar se datas válidas
			
			// Data recebida $_POST no formato DD/MM/AAAA
			$valida_data_inicio = Funcoes::ValidaData($_POST['dt_inc']);
			$valida_data_fim = Funcoes::ValidaData($_POST['dt_fim']);

			if ($valida_data_inicio == 0 || $valida_data_fim == 0) {
				$this->view->erroValidacao = 3;	

				$this->view->datas = array (
					'data_inicial' => $_POST['dt_inc'],
					'data_final' => $_POST['dt_fim'],
					'rota' => $_POST['rota']
				);
				
				$this->render('subgrupoConsultarVinculoFamilia');	
			} else {
				// Validar se Data Inicial é maior que Data Final
				$data_inicio_format = str_replace('/', '-', $_POST['dt_inc']);
				$data_fim_format = str_replace('/', '-', $_POST['dt_fim']);

				// O barra "\" antes do DataTime foi devido ao namespace utilizado neste programa, pois sem a barra não reconhecia. Dica pega na internet.
				$data_inicio = new \DateTime($data_inicio_format);		
				$data_fim = new \DateTime($data_fim_format);		

				if($data_inicio > $data_fim) {
					$this->view->erroValidacao = 4;	

					$this->view->datas = array (
						'data_inicial' => $_POST['dt_inc'],
						'data_final' => $_POST['dt_fim'],
						'rota' => $_POST['rota']
					);
					
					$this->render('subgrupoConsultarVinculoFamilia');	

				} else {
					// Busca Nome Grupo
					$nomeGrupoBase = Container::getModel('TbGrp');
					$nomeGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
					$nomeGrupo = $nomeGrupoBase->getDadosGrupo();

					$nomeGrp = $nomeGrupo['nome_grupo'];

					$codSbgrp =  $_POST['cb_subgrupo_escolhido'];

					// Busca Nome Subgrupo
					$nomeSubgrupo = Container::getModel('TbSbgrp');
					$nomeSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$nomeSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$nomeSbgrp = $nomeSubgrupo->getDadosSubgrupo();

					$nomeSbgrp = $nomeSbgrp['nome_subgrupo'];

					// Busca Dados das Famílias
					$dadosVinculoFamilia = Container::getModel('TbVnclFmlSbgrp');
					$dadosVinculoFamilia->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$dadosVinculoFamilia->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosVinculoFamilia->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosVinculoFamilia->__set('dataInicio_pesq', $_POST['dt_inc']);
					$dadosVinculoFamilia->__set('dataFim_pesq', $_POST['dt_fim']);

					$dadosFmlr = $dadosVinculoFamilia->getDadosVFSAll();

					$this->view->dadosTVFS = array ();

					foreach ($dadosFmlr as $index => $arr) {

						// Buscar Nome da Família
						$dadosFamilia = Container::getModel('TbFml');
						$dadosFamilia->__set('codFamilia', $arr['codigo_familia']);
						$dadosFml = $dadosFamilia->getDadosFamilia();
						$nomeFml = $dadosFml['nm_grp_fmlr'];
					
						array_push($this->view->dadosTVFS, array (
								'codigo_familia' => $arr['codigo_familia'],
								'nome_familia' => $nomeFml,
								'sequencial' => $arr['sequencial_base'],
								'data_inicio_vinculo' =>  $arr['data_inicio_vinculo'],
								'data_fim_vinculo' =>  $arr['data_fim_vinculo'],
								'situacao_vinculo' =>  $arr['estado_vinculo'],
								'dt_inc' =>  $_POST['dt_inc'],
								'dt_fim' =>  $_POST['dt_fim'],
								'cb_grupo_escolhido' => $_POST['cb_grupo_escolhido'],
								'cb_subgrupo_escolhido' => $_POST['cb_subgrupo_escolhido'],
								'rota' => $_POST['rota'],
								'cd_est_situ_fml' => $arr['estado_situacao_familia']
						));
					}	

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrp;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSbgrp;

					$this->render('subgrupoConsultarVinculoFamiliaMenu');

				}
			}
		}

	}	// Fim da function subgrupoConsultarVinculoFamiliaMenu

/*
// ====================================================== //	
	
	public function subgrupoConsultarSemVinculoFamilia() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('grupoSubgrupo');				
		} else {
			$this->view->erroValidacao = 0;

			// Busca Dados das Famílias
			$dadosSemVinculoFamilia = Container::getModel('TbFml');
			$dadosSemVinculoFmlr = $dadosSemVinculoFamilia->getDadosFamiliasAll();

			$this->view->dadosSemVinculoFml = array ();

			foreach ($dadosSemVinculoFmlr as $index => $arr) {
				// Buscar o nome da Região Administrativa
				$dadosRABase = Container::getModel('TbRegAdm');
				$dadosRABase->__set('cd_reg_adm', $arr['cd_reg_adm']);
				// O abaixo não foi usado devido a retorno $this-> ao inver de return PDO
				//$dadosRA = $dadosRABase->getDadosRAAll2();
				$dadosRABase->getDadosRAAll2();

				array_push($this->view->dadosSemVinculoFml, array (
						'codigo_familia' => $arr['cd_fmlID'],
						'nome_familia' => $arr['nm_grp_fmlr'],
						'nome_assistido_principal' => $arr['nm_astd_prin'],
						'data_cadastro' =>  $arr['data_cadastro'],
						'regiao_administrativa' =>  $dadosRABase->__get('nome_ra'),
						'uf_regiao_administrativa' =>  $dadosRABase->__get('uf_ra'),
						'rota' =>  'rota_03'
				));
			}	

			$this->render('subgrupoConsultarSemVinculoFamilia');
		}
	}	// Fim da function subgrupoConsultarSemVinculoFamilia
*/

}	//	Fim da classe

?>