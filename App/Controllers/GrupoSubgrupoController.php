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
	
	public function SubgrupoAlterar() {
		
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
	}	// Fim da function SubgrupoAlterar

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
	
	public function SubgrupoEncerrar() {
		
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
	}	// Fim da function SubgrupoEncerrar

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
	
	public function SubgrupoConsultar() {
		
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
	}	// Fim da function SubgrupoConsultar

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
	
}	//	Fim da classe

?>