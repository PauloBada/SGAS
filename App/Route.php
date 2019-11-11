<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controla as rotas para acesso às páginas do sistema
*/

// Esta classe implementa os controladores, que são os endereços URL constantes quando 
// se escolhe alguma opção do sistema

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {
		// Qdo se entra na página principal da aplicação "www.../"
		$routes['home'] = array (
			'route' => '/',
			'controller' => 'IndexController',
			'action' => 'index'
		);

		// Ação de autenticar e-mail e senha
		$routes['autenticar'] = array (
			'route' => '/autenticar',
			'controller' => 'AuthController',
			'action' => 'autenticar'
		);

		// Botão sair, quando logado na timeline
		$routes['sair'] = array (
			'route' => '/sair',
			'controller' => 'AuthController',
			'action' => 'sair'
		);

		// Tela Principal do sistema, onde estarão todas as opções e links //

		$routes['menuPrincipal'] = array (
			'route' => '/menuPrincipal',
			'controller' => 'AppController',
			'action' => 'menuPrincipal'
		);

// ============  Início de Alteração de Senha =========== //

		// Tela Cadastro - Alterar Senha do Menu Principal - Buscar senha atual da base
		$routes['voluntarioSenha'] = array (
			'route' => '/voluntarioSenha',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioSenha'
		);
		
		// Tela Cadastro - Alterar Senha do Menu Principal - Buscar senha atual da base
		$routes['voluntarioSenhaNova'] = array (
			'route' => '/voluntarioSenhaNova',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioSenhaNova'
		);

// ============  Início de Região Administrativa =========== //

		// Tela Apoio - Região Admnistrativa
		$routes['apoioRA'] = array (
			'route' => '/apoioRA',
			'controller' => 'ApoioController',
			'action' => 'apoioRA'
		);

		// Tela Apoio - Incluir Região Admnistrativa
		$routes['apoioRAIncluir'] = array (
			'route' => '/apoioRAIncluir',
			'controller' => 'ApoioController',
			'action' => 'apoioRAIncluir'
		);

		// Incluir na base de Região Admnistrativa
		$routes['apoioRAIncluirBase'] = array (
			'route' => '/apoioRAIncluirBase',
			'controller' => 'ApoioController',
			'action' => 'apoioRAIncluirBase'
		);

		// Tela Apoio - Alterar Região Admnistrativa
		$routes['apoioRAAlterar'] = array (
			'route' => '/apoioRAAlterar',
			'controller' => 'ApoioController',
			'action' => 'apoioRAAlterar'
		);

		// Tela Apoio - Menu Alterar Região Admnistrativa
		$routes['apoioRAAlterarMenu'] = array (
			'route' => '/apoioRAAlterarMenu',
			'controller' => 'ApoioController',
			'action' => 'apoioRAAlterarMenu'
		);

		// Alterar base de Região Admnistrativa
		$routes['apoioRAAlterarBase'] = array (
			'route' => '/apoioRAAlterarBase',
			'controller' => 'ApoioController',
			'action' => 'apoioRAAlterarBase'
		);

		// Tela Apoio - Encerrar Região Admnistrativa
		$routes['apoioRAEncerrar'] = array (
			'route' => '/apoioRAEncerrar',
			'controller' => 'ApoioController',
			'action' => 'apoioRAEncerrar'
		);

		// Tela Apoio - Menu Encerrar Região Admnistrativa
		$routes['apoioRAEncerrarMenu'] = array (
			'route' => '/apoioRAEncerrarMenu',
			'controller' => 'ApoioController',
			'action' => 'apoioRAEncerrarMenu'
		);

		// Alterar base de Região Admnistrativa
		$routes['apoioRAEncerrarBase'] = array (
			'route' => '/apoioRAEncerrarBase',
			'controller' => 'ApoioController',
			'action' => 'apoioRAEncerrarBase'
		);

		// Tela Apoio - Consultar Região Admnistrativa
		$routes['apoioRAConsultar'] = array (
			'route' => '/apoioRAConsultar',
			'controller' => 'ApoioController',
			'action' => 'apoioRAConsultar'
		);

		// Tela Apoio - Consultar Região Admnistrativa
		$routes['apoioRAConsultarMenu'] = array (
			'route' => '/apoioRAConsultarMenu',
			'controller' => 'ApoioController',
			'action' => 'apoioRAConsultarMenu'
		);

// ============  Início de Item =========== //

		// Tela Apoio - Item e Subitem
		$routes['apoioItemSubitem'] = array (
			'route' => '/apoioItemSubitem',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemSubitem'
		);

		$routes['apoioItemIncluir'] = array (
			'route' => '/apoioItemIncluir',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemIncluir'
		);

		$routes['apoioItemIncluirBase'] = array (
			'route' => '/apoioItemIncluirBase',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemIncluirBase'
		);

		$routes['apoioItemAlterar'] = array (
			'route' => '/apoioItemAlterar',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemAlterar'
		);

		$routes['apoioItemAlterarMenu'] = array (
			'route' => '/apoioItemAlterarMenu',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemAlterarMenu'
		);

		$routes['apoioItemAlterarBase'] = array (
			'route' => '/apoioItemAlterarBase',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemAlterarBase'
		);

		$routes['apoioItemConsultar'] = array (
			'route' => '/apoioItemConsultar',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemConsultar'
		);

		$routes['apoioItemConsultarMenu'] = array (
			'route' => '/apoioItemConsultarMenu',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioItemConsultarMenu'
		);

// ============  Início de Subitem =========== //

		$routes['apoioSubitemIncluir'] = array (
			'route' => '/apoioSubitemIncluir',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioSubitemIncluir'
		);

		$routes['apoioSubitemIncluirBase'] = array (
			'route' => '/apoioSubitemIncluirBase',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioSubitemIncluirBase'
		);

		$routes['apoioSubitemAlterar'] = array (
			'route' => '/apoioSubitemAlterar',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioSubitemAlterar'
		);

		// Para Atualizar o combobox de Subitem de acordo com o Item
		$routes['atualizaCbSubitem'] = array (
			'route' => '/atualizaCbSubitem',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'atualizaCbSubitem'
		);

		$routes['apoioSubitemAlterarMenu'] = array (
			'route' => '/apoioSubitemAlterarMenu',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioSubitemAlterarMenu'
		);

		$routes['apoioSubitemAlterarBase'] = array (
			'route' => '/apoioSubitemAlterarBase',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioSubitemAlterarBase'
		);

		$routes['apoioSubitemConsultar'] = array (
			'route' => '/apoioSubitemConsultar',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioSubitemConsultar'
		);

		$routes['apoioSubitemConsultarMenu'] = array (
			'route' => '/apoioSubitemConsultarMenu',
			'controller' => 'SuprimentoCadastroController',
			'action' => 'apoioSubitemConsultarMenu'
		);

// ============ Início tratamento de Voluntário ========= //

		$routes['voluntario'] = array (
			'route' => '/voluntario',
			'controller' => 'VoluntarioController',
			'action' => 'voluntario'
		);

		$routes['voluntarioIncluir'] = array (
			'route' => '/voluntarioIncluir',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioIncluir'
		);

		$routes['voluntarioIncluirBase'] = array (
			'route' => '/voluntarioIncluirBase',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioIncluirBase'
		);

		$routes['voluntarioAlterar'] = array (
			'route' => '/voluntarioAlterar',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioAlterar'
		);
	
		$routes['voluntarioAlterarMenu'] = array (
			'route' => '/voluntarioAlterarMenu',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioAlterarMenu'
		);
	
		$routes['voluntarioAlterarBase'] = array (
			'route' => '/voluntarioAlterarBase',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioAlterarBase'
		);

		$routes['voluntarioConsultar'] = array (
			'route' => '/voluntarioConsultar',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioConsultar'
		);
	
		$routes['voluntarioConsultarMenu'] = array (
			'route' => '/voluntarioConsultarMenu',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioConsultarMenu'
		);
	
		$routes['voluntarioAlterarNAS'] = array (
			'route' => '/voluntarioAlterarNAS',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioAlterarNAS'
		);

		$routes['voluntarioAlterarNASMenu'] = array (
			'route' => '/voluntarioAlterarNASMenu',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioAlterarNASMenu'
		);

		$routes['voluntarioAlterarNASBase'] = array (
			'route' => '/voluntarioAlterarNASBase',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioAlterarNASBase'
		);

		$routes['voluntarioPerfilAtuacaoMenu'] = array (
			'route' => '/voluntarioPerfilAtuacaoMenu',
			'controller' => 'VoluntarioController',
			'action' => 'voluntarioPerfilAtuacaoMenu'
		);

// ============ Início tratamento de Grupo ========= //
		
		$routes['grupoSubgrupo'] = array (
			'route' => '/grupoSubgrupo',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoSubgrupo'
		);

		$routes['grupoIncluir'] = array (
			'route' => '/grupoIncluir',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoIncluir'
		);

		$routes['grupoIncluirBase'] = array (
			'route' => '/grupoIncluirBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoIncluirBase'
		);

		$routes['grupoAlterar'] = array (
			'route' => '/grupoAlterar',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoAlterar'
		);

		$routes['grupoAlterarMenu'] = array (
			'route' => '/grupoAlterarMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoAlterarMenu'
		);

		$routes['grupoAlterarBase'] = array (
			'route' => '/grupoAlterarBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoAlterarBase'
		);

		$routes['grupoEncerrar'] = array (
			'route' => '/grupoEncerrar',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoEncerrar'
		);

		$routes['grupoEncerrarMenu'] = array (
			'route' => '/grupoEncerrarMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoEncerrarMenu'
		);

		$routes['grupoEncerrarBase'] = array (
			'route' => '/grupoEncerrarBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoEncerrarBase'
		);

		$routes['grupoConsultar'] = array (
			'route' => '/grupoConsultar',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoConsultar'
		);

		$routes['grupoConsultarMenu'] = array (
			'route' => '/grupoConsultarMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'grupoConsultarMenu'
		);

// ============ Início tratamento de Subgrupo ========= //
		
		$routes['subgrupoIncluir'] = array (
			'route' => '/subgrupoIncluir',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoIncluir'
		);

		$routes['subgrupoIncluirBase'] = array (
			'route' => '/subgrupoIncluirBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoIncluirBase'
		);

		$routes['subgrupoAlterar'] = array (
			'route' => '/subgrupoAlterar',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoAlterar'
		);

		$routes['subgrupoAlterarMenu'] = array (
			'route' => '/subgrupoAlterarMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoAlterarMenu'
		);

		$routes['subgrupoAlterarBase'] = array (
			'route' => '/subgrupoAlterarBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoAlterarBase'
		);

		$routes['subgrupoEncerrar'] = array (
			'route' => '/subgrupoEncerrar',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoEncerrar'
		);

		$routes['subgrupoEncerrarMenu'] = array (
			'route' => '/subgrupoEncerrarMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoEncerrarMenu'
		);

		$routes['subgrupoEncerrarBase'] = array (
			'route' => '/subgrupoEncerrarBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoEncerrarBase'
		);

		$routes['subgrupoConsultar'] = array (
			'route' => '/subgrupoConsultar',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoConsultar'
		);

		$routes['subgrupoConsultarMenu'] = array (
			'route' => '/subgrupoConsultarMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoConsultarMenu'
		);

		$routes['subgrupoVincularVoluntario'] = array (
			'route' => '/subgrupoVincularVoluntario',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoVincularVoluntario'
		);

		$routes['subgrupoVincularVoluntarioBase'] = array (
			'route' => '/subgrupoVincularVoluntarioBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoVincularVoluntarioBase'
		);

		$routes['subgrupoDesvincularVoluntario'] = array (
			'route' => '/subgrupoDesvincularVoluntario',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoDesvincularVoluntario'
		);

		$routes['subgrupoDesvincularVoluntarioMenu'] = array (
			'route' => '/subgrupoDesvincularVoluntarioMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoDesvincularVoluntarioMenu'
		);

		$routes['subgrupoDesvincularVoluntarioBase'] = array (
			'route' => '/subgrupoDesvincularVoluntarioBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoDesvincularVoluntarioBase'
		);

		$routes['subgrupoConsultarVinculoVoluntario'] = array (
			'route' => '/subgrupoConsultarVinculoVoluntario',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoConsultarVinculoVoluntario'
		);

		$routes['subgrupoConsultarVinculoVoluntarioMenu'] = array (
			'route' => '/subgrupoConsultarVinculoVoluntarioMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoConsultarVinculoVoluntarioMenu'
		);

		$routes['subgrupoVincularFamilia'] = array (
			'route' => '/subgrupoVincularFamilia',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoVincularFamilia'
		);

		$routes['subgrupoVincularFamiliaBase'] = array (
			'route' => '/subgrupoVincularFamiliaBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoVincularFamiliaBase'
		);

		$routes['subgrupoDesvincularFamilia'] = array (
			'route' => '/subgrupoDesvincularFamilia',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoDesvincularFamilia'
		);

		$routes['subgrupoDesvincularFamiliaMenu'] = array (
			'route' => '/subgrupoDesvincularFamiliaMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoDesvincularFamiliaMenu'
		);

		$routes['subgrupoDesvincularFamiliaBase'] = array (
			'route' => '/subgrupoDesvincularFamiliaBase',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoDesvincularFamiliaBase'
		);

		$routes['subgrupoConsultarVinculoFamilia'] = array (
			'route' => '/subgrupoConsultarVinculoFamilia',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoConsultarVinculoFamilia'
		);

		$routes['subgrupoConsultarVinculoFamiliaMenu'] = array (
			'route' => '/subgrupoConsultarVinculoFamiliaMenu',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoConsultarVinculoFamiliaMenu'
		);

/*
		$routes['subgrupoConsultarSemVinculoFamilia'] = array (
			'route' => '/subgrupoConsultarSemVinculoFamilia',
			'controller' => 'GrupoSubgrupoController',
			'action' => 'subgrupoConsultarSemVinculoFamilia'
		);
*/

// ============ Início tratamento de Cadastro de Família ========= //

		$routes['familiaCadastro'] = array (
			'route' => '/familiaCadastro',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastro'
		);

		$routes['familiaCadastroIncluir'] = array (
			'route' => '/familiaCadastroIncluir',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroIncluir'
		);

		$routes['familiaCadastroIncluirBase'] = array (
			'route' => '/familiaCadastroIncluirBase',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroIncluirBase'
		);

		$routes['familiaCadastroAlterar'] = array (
			'route' => '/familiaCadastroAlterar',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroAlterar'
		);

		$routes['familiaCadastroAlterarMenu'] = array (
			'route' => '/familiaCadastroAlterarMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroAlterarMenu'
		);

		$routes['familiaCadastroAlterarBase'] = array (
			'route' => '/familiaCadastroAlterarBase',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroAlterarBase'
		);

		$routes['familiaCadastroConsultar'] = array (
			'route' => '/familiaCadastroConsultar',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroConsultar'
		);

		$routes['familiaCadastroConsultarMenu'] = array (
			'route' => '/familiaCadastroConsultarMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroConsultarMenu'
		);

		$routes['familiaCadastroIncluirIntegrante'] = array (
			'route' => '/familiaCadastroIncluirIntegrante',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroIncluirIntegrante'
		);

		$routes['familiaCadastroIncluirIntegranteMenu'] = array (
			'route' => '/familiaCadastroIncluirIntegranteMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroIncluirIntegranteMenu'
		);

		$routes['familiaCadastroIncluirIntegranteBase'] = array (
			'route' => '/familiaCadastroIncluirIntegranteBase',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroIncluirIntegranteBase'
		);

		$routes['familiaCadastroAlterarIntegrante'] = array (
			'route' => '/familiaCadastroAlterarIntegrante',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroAlterarIntegrante'
		);

		$routes['familiaCadastroAlterarIntegranteMenu'] = array (
			'route' => '/familiaCadastroAlterarIntegranteMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroAlterarIntegranteMenu'
		);

		$routes['familiaCadastroAlterarIntegranteBase'] = array (
			'route' => '/familiaCadastroAlterarIntegranteBase',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroAlterarIntegranteBase'
		);

		$routes['familiaCadastroEncerrarIntegrante'] = array (
			'route' => '/familiaCadastroEncerrarIntegrante',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroEncerrarIntegrante'
		);

		$routes['familiaCadastroEncerrarIntegranteMenu'] = array (
			'route' => '/familiaCadastroEncerrarIntegranteMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroEncerrarIntegranteMenu'
		);

		$routes['familiaCadastroEncerrarIntegranteBase'] = array (
			'route' => '/familiaCadastroEncerrarIntegranteBase',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroEncerrarIntegranteBase'
		);

		$routes['ncurses_can_change_color()'] = array (
			'route' => '/familiaCadastroConsultarIntegrante',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroConsultarIntegrante'
		);

		$routes['familiaCadastroConsultarIntegranteMenu'] = array (
			'route' => '/familiaCadastroConsultarIntegranteMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroConsultarIntegranteMenu'
		);

		$routes['familiaCadastroPreIncluir'] = array (
			'route' => '/familiaCadastroPreIncluir',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroPreIncluir'
		);

		$routes['familiaCadastroPreIncluirMenu'] = array (
			'route' => '/familiaCadastroPreIncluirMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroPreIncluirMenu'
		);

		$routes['familiaCadastroPreIncluirIntegrantesMenu'] = array (
			'route' => '/familiaCadastroPreIncluirIntegrantesMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroPreIncluirIntegrantesMenu'
		);

		$routes['familiaCadastroConsultaPesquisaMenu'] = array (
			'route' => '/familiaCadastroConsultaPesquisaMenu',
			'controller' => 'FamiliaCadastroController',
			'action' => 'familiaCadastroConsultaPesquisaMenu'
		);

// ============ Início tratamento de Acompanhamento de Família ========= //
		
		$routes['familiaAcompanhamento'] = array (
			'route' => '/familiaAcompanhamento',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompanhamento'
		);

		$routes['familiaAcompanhamentoPreIncluirRelTriagem'] = array (
			'route' => '/familiaAcompanhamentoPreIncluirRelTriagem',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompanhamentoPreIncluirRelTriagem'
		);

		$routes['familiaAcompanhamentoIncluirRelTriagem'] = array (
			'route' => '/familiaAcompanhamentoIncluirRelTriagem',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompanhamentoIncluirRelTriagem'
		);

		$routes['familiaAcompanhamentoIncluirRelTriagemMenu'] = array (
			'route' => '/familiaAcompanhamentoIncluirRelTriagemMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompanhamentoIncluirRelTriagemMenu'
		);

		$routes['familiaAcompIncRelTriagemEducacaoMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemEducacaoMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemEducacaoMenu'
		);

		$routes['familiaAcompIncRelTriagemEducacaoBase'] = array (
			'route' => '/familiaAcompIncRelTriagemEducacaoBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemEducacaoBase'
		);

		$routes['familiaAcompIncRelTriagemReligiosidadeMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemReligiosidadeMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemReligiosidadeMenu'
		);

		$routes['familiaAcompIncRelTriagemReligiosidadeBase'] = array (
			'route' => '/familiaAcompIncRelTriagemReligiosidadeBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemReligiosidadeBase'
		);

		$routes['familiaAcompIncRelTriagemMoradiaMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemMoradiaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemMoradiaMenu'
		);

		$routes['familiaAcompIncRelTriagemMoradiaBase'] = array (
			'route' => '/familiaAcompIncRelTriagemMoradiaBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemMoradiaBase'
		);

		$routes['familiaAcompIncRelTriagemSaudeMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemSaudeMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemSaudeMenu'
		);

		$routes['familiaAcompIncRelTriagemSaudeBase'] = array (
			'route' => '/familiaAcompIncRelTriagemSaudeBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemSaudeBase'
		);

		$routes['familiaAcompIncRelTriagemDespesaMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemDespesaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemDespesaMenu'
		);

		$routes['familiaAcompIncRelTriagemDespesaBase'] = array (
			'route' => '/familiaAcompIncRelTriagemDespesaBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemDespesaBase'
		);

		$routes['familiaAcompIncRelTriagemRendaMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemRendaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemRendaMenu'
		);

		$routes['familiaAcompIncRelTriagemRendaBase'] = array (
			'route' => '/familiaAcompIncRelTriagemRendaBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemRendaBase'
		);

		$routes['familiaAcompIncRelTriagemCapProfissionalMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemCapProfissionalMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemCapProfissionalMenu'
		);

		$routes['familiaAcompIncRelTriagemCapProfissionalBase'] = array (
			'route' => '/familiaAcompIncRelTriagemCapProfissionalBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemCapProfissionalBase'
		);

		$routes['familiaAcompIncRelTriagemAspectoIntMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemAspectoIntMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemAspectoIntMenu'
		);

		$routes['familiaAcompIncRelTriagemAspectoIntBase'] = array (
			'route' => '/familiaAcompIncRelTriagemAspectoIntBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemAspectoIntBase'
		);

		$routes['familiaAcompIncRelTriagemVoluntario'] = array (
			'route' => '/familiaAcompIncRelTriagemVoluntario',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemVoluntario'
		);

		$routes['familiaAcompIncRelTriagemVoluntarioMenu'] = array (
			'route' => '/familiaAcompIncRelTriagemVoluntarioMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemVoluntarioMenu'
		);

		$routes['familiaAcompIncRelTriagemVoluntarioBase'] = array (
			'route' => '/familiaAcompIncRelTriagemVoluntarioBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'familiaAcompIncRelTriagemVoluntarioBase'
		);

		$routes['fAPreAlterarRT'] = array (
			'route' => '/fAPreAlterarRT',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreAlterarRT'
		);


		$routes['fAAlterarRT'] = array (
			'route' => '/fAAlterarRT',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRT'
		);

		$routes['fAAlterarRTMenu'] = array (
			'route' => '/fAAlterarRTMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTMenu'
		);

		$routes['fAAlterarRTBase'] = array (
			'route' => '/fAAlterarRTBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTBase'
		);

		$routes['fAAlterarRTEducacaoMenu'] = array (
			'route' => '/fAAlterarRTEducacaoMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTEducacaoMenu'
		);

		$routes['fAAlterarRTEducacaoBase'] = array (
			'route' => '/fAAlterarRTEducacaoBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTEducacaoBase'
		);

		$routes['fAAlterarRTReligiosidadeMenu'] = array (
			'route' => '/fAAlterarRTReligiosidadeMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTReligiosidadeMenu'
		);

		$routes['fAAlterarRTReligiosidadeBase'] = array (
			'route' => '/fAAlterarRTReligiosidadeBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTReligiosidadeBase'
		);

		$routes['fAAlterarRTMoradiaMenu'] = array (
			'route' => '/fAAlterarRTMoradiaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTMoradiaMenu'
		);

		$routes['fAAlterarRTMoradiaBase'] = array (
			'route' => '/fAAlterarRTMoradiaBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTMoradiaBase'
		);

		$routes['fAAlterarRTSaudeMenu'] = array (
			'route' => '/fAAlterarRTSaudeMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTSaudeMenu'
		);

		$routes['fAAlterarRTSaudeBase'] = array (
			'route' => '/fAAlterarRTSaudeBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTSaudeBase'
		);

		$routes['fAAlterarRTDespesaMenu'] = array (
			'route' => '/fAAlterarRTDespesaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTDespesaMenu'
		);

		$routes['fAAlterarRTDespesaBase'] = array (
			'route' => '/fAAlterarRTDespesaBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTDespesaBase'
		);

		$routes['fAAlterarRTRendaMenu'] = array (
			'route' => '/fAAlterarRTRendaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTRendaMenu'
		);

		$routes['fAAlterarRTRendaBase'] = array (
			'route' => '/fAAlterarRTRendaBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTRendaBase'
		);

		$routes['fAAlterarRTCapProfissionalMenu'] = array (
			'route' => '/fAAlterarRTCapProfissionalMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTCapProfissionalMenu'
		);

		$routes['fAAlterarRTCapProfissionalBase'] = array (
			'route' => '/fAAlterarRTCapProfissionalBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTCapProfissionalBase'
		);

		$routes['fAAlterarRTAspectoIntMenu'] = array (
			'route' => '/fAAlterarRTAspectoIntMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTAspectoIntMenu'
		);

		$routes['fAAlterarRTAspectoIntBase'] = array (
			'route' => '/fAAlterarRTAspectoIntBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTAspectoIntBase'
		);

		$routes['fAAlterarRTVoluntario'] = array (
			'route' => '/fAAlterarRTVoluntario',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTVoluntario'
		);

		$routes['fAAlterarRTVoluntarioMenu'] = array (
			'route' => '/fAAlterarRTVoluntarioMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTVoluntarioMenu'
		);

		$routes['fAAlterarRTVoluntarioBase'] = array (
			'route' => '/fAAlterarRTVoluntarioBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRTVoluntarioBase'
		);

		$routes['fAPreConcluirRT'] = array (
			'route' => '/fAPreConcluirRT',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreConcluirRT'
		);

		$routes['fAConcluirRT'] = array (
			'route' => '/fAConcluirRT',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRT'
		);

		$routes['fAConcluirRTMenu'] = array (
			'route' => '/fAConcluirRTMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRTMenu'
		);

		$routes['fAConcluirRTBaseAtualiza'] = array (
			'route' => '/fAConcluirRTBaseAtualiza',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRTBaseAtualiza'
		);

		$routes['fAConcluirRTBaseConclui'] = array (
			'route' => '/fAConcluirRTBaseConclui',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRTBaseConclui'
		);

		$routes['fAPreRevisarRT'] = array (
			'route' => '/fAPreRevisarRT',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreRevisarRT'
		);

		$routes['fARevisarRT'] = array (
			'route' => '/fARevisarRT',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRT'
		);

		$routes['fARevisarRTMenu'] = array (
			'route' => '/fARevisarRTMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRTMenu'
		);

		$routes['fARevisarRTBaseAtualiza'] = array (
			'route' => '/fARevisarRTBaseAtualiza',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRTBaseAtualiza'
		);

		$routes['fARevisarRTBaseConclui'] = array (
			'route' => '/fARevisarRTBaseConclui',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRTBaseConclui'
		);

		$routes['fAConsultarRTVoluntario'] = array (
			'route' => '/fAConsultarRTVoluntario',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTVoluntario'
		);

		$routes['fAPreIncluirRV'] = array (
			'route' => '/fAPreIncluirRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreIncluirRV'
		);

		$routes['fAIncluirRV'] = array (
			'route' => '/fAIncluirRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAIncluirRV'
		);

		$routes['fAIncluirRVMenu'] = array (
			'route' => '/fAIncluirRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAIncluirRVMenu'
		);

		$routes['fAIncluirDadosRVMenu'] = array (
			'route' => '/fAIncluirDadosRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAIncluirDadosRVMenu'
		);

		$routes['fAIncluirDadosRVBase'] = array (
			'route' => '/fAIncluirDadosRVBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAIncluirDadosRVBase'
		);

		$routes['fAPreAlterarRV'] = array (
			'route' => '/fAPreAlterarRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreAlterarRV'
		);

		$routes['fAAlterarRV'] = array (
			'route' => '/fAAlterarRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRV'
		);

		$routes['fAAlterarRVMenu'] = array (
			'route' => '/fAAlterarRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarRVMenu'
		);

		$routes['fAAlterarDadosRVMenu'] = array (
			'route' => '/fAAlterarDadosRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarDadosRVMenu'
		);

		$routes['fAAlterarDadosRVBase'] = array (
			'route' => '/fAAlterarDadosRVBase',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAAlterarDadosRVBase'
		);

		$routes['fAPreConcluirRV'] = array (
			'route' => '/fAPreConcluirRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreConcluirRV'
		);

		$routes['fAConcluirRV'] = array (
			'route' => '/fAConcluirRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRV'
		);

		$routes['fAConcluirRVMenu'] = array (
			'route' => '/fAConcluirRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRVMenu'
		);

		$routes['fAConcluirRVBaseAtualiza'] = array (
			'route' => '/fAConcluirRVBaseAtualiza',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRVBaseAtualiza'
		);

		$routes['fAConcluirRVBaseConclui'] = array (
			'route' => '/fAConcluirRVBaseConclui',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRVBaseConclui'
		);

		$routes['fAConsultarRVVoluntario'] = array (
			'route' => '/fAConsultarRVVoluntario',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRVVoluntario'
		);

		$routes['fAPreRevisarRV'] = array (
			'route' => '/fAPreRevisarRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreRevisarRV'
		);

		$routes['fARevisarRV'] = array (
			'route' => '/fARevisarRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRV'
		);

		$routes['fARevisarRVMenu'] = array (
			'route' => '/fARevisarRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRVMenu'
		);

		$routes['fARevisarRVBaseAtualiza'] = array (
			'route' => '/fARevisarRVBaseAtualiza',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRVBaseAtualiza'
		);

		$routes['fARevisarRVBaseConclui'] = array (
			'route' => '/fARevisarRVBaseConclui',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fARevisarRVBaseConclui'
		);

		$routes['fAPreIncluirRD'] = array (
			'route' => '/fAPreIncluirRD',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreIncluirRD'
		);

		$routes['fAIncluirRD'] = array (
			'route' => '/fAIncluirRD',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAIncluirRD'
		);

		$routes['fAConcluirRDMenu'] = array (
			'route' => '/fAConcluirRDMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRDMenu'
		);

		$routes['fAConcluirRDBaseAtualiza'] = array (
			'route' => '/fAConcluirRDBaseAtualiza',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRDBaseAtualiza'
		);

		$routes['fAConcluirRDBaseFormaliza'] = array (
			'route' => '/fAConcluirRDBaseFormaliza',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConcluirRDBaseFormaliza'
		);

		$routes['fAPreConsultarRTRV'] = array (
			'route' => '/fAPreConsultarRTRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreConsultarRTRV'
		);

		$routes['fAConsultarRTRV'] = array (
			'route' => '/fAConsultarRTRV',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTRV'
		);

		$routes['fAConsultarRTRVMenu'] = array (
			'route' => '/fAConsultarRTRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTRVMenu'
		);

		$routes['fAConsultarRTMenu'] = array (
			'route' => '/fAConsultarRTMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTMenu'
		);

		$routes['fAConsultarRTEducacaoMenu'] = array (
			'route' => '/fAConsultarRTEducacaoMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTEducacaoMenu'
		);

		$routes['fAConsultarRTReligiosidadeMenu'] = array (
			'route' => '/fAConsultarRTReligiosidadeMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTReligiosidadeMenu'
		);

		$routes['fAConsultarRTMoradiaMenu'] = array (
			'route' => '/fAConsultarRTMoradiaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTMoradiaMenu'
		);

		$routes['fAConsultarRTSaudeMenu'] = array (
			'route' => '/fAConsultarRTSaudeMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTSaudeMenu'
		);

		$routes['fAConsultarRTDespesaMenu'] = array (
			'route' => '/fAConsultarRTDespesaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTDespesaMenu'
		);

		$routes['fAConsultarRTRendaMenu'] = array (
			'route' => '/fAConsultarRTRendaMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTRendaMenu'
		);

		$routes['fAConsultarRTCapProfissionalMenu'] = array (
			'route' => '/fAConsultarRTCapProfissionalMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTCapProfissionalMenu'
		);

		$routes['fAConsultarRTAspectoIntMenu'] = array (
			'route' => '/fAConsultarRTAspectoIntMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTAspectoIntMenu'
		);

		$routes['fAConsultarRVMenu'] = array (
			'route' => '/fAConsultarRVMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRVMenu'
		);

		$routes['fAConsultarRDMenu'] = array (
			'route' => '/fAConsultarRDMenu',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRDMenu'
		);

		$routes['fAPreConsultarRTRVPendentes'] = array (
			'route' => '/fAPreConsultarRTRVPendentes',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreConsultarRTRVPendentes'
		);

		$routes['fAConsultarRTRVPendentes'] = array (
			'route' => '/fAConsultarRTRVPendentes',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRTRVPendentes'
		);

		$routes['fAPreConsultarRankingFml'] = array (
			'route' => '/fAPreConsultarRankingFml',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAPreConsultarRankingFml'
		);

		$routes['fAConsultarRankingFml'] = array (
			'route' => '/fAConsultarRankingFml',
			'controller' => 'FamiliaAcompanhamentoController',
			'action' => 'fAConsultarRankingFml'
		);

// ============ Início tratamento de Necessidade de Família ========= //
		
		$routes['familiaNecessidade'] = array (
			'route' => '/familiaNecessidade',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'familiaNecessidade'
		);

		$routes['fNPreIncluirNeces'] = array (
			'route' => '/fNPreIncluirNeces',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNPreIncluirNeces'
		);

		$routes['fNIncluirNeces'] = array (
			'route' => '/fNIncluirNeces',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNIncluirNeces'
		);

		$routes['fNIncluirNecesProxVisitaMenu'] = array (
			'route' => '/fNIncluirNecesProxVisitaMenu',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNIncluirNecesProxVisitaMenu'
		);

		$routes['fNIncluirNecesProxVisitaBase'] = array (
			'route' => '/fNIncluirNecesProxVisitaBase',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNIncluirNecesProxVisitaBase'
		);

		$routes['fNIncluirNecesEventualMenu'] = array (
			'route' => '/fNIncluirNecesEventualMenu',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNIncluirNecesEventualMenu'
		);

		$routes['fNIncluirNecesEventualBase'] = array (
			'route' => '/fNIncluirNecesEventualBase',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNIncluirNecesEventualBase'
		);

		$routes['fNPreAlterarNeces'] = array (
			'route' => '/fNPreAlterarNeces',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNPreAlterarNeces'
		);

		$routes['fNAlterarNeces'] = array (
			'route' => '/fNAlterarNeces',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNAlterarNeces'
		);

		$routes['fNAlterarNecesProxVisitaMenu'] = array (
			'route' => '/fNAlterarNecesProxVisitaMenu',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNAlterarNecesProxVisitaMenu'
		);

		$routes['fNAlterarNecesProxVisitaBase'] = array (
			'route' => '/fNAlterarNecesProxVisitaBase',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNAlterarNecesProxVisitaBase'
		);

		$routes['fNAlterarNecesEventualMenu'] = array (
			'route' => '/fNAlterarNecesEventualMenu',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNAlterarNecesEventualMenu'
		);

		$routes['fNAlterarNecesEventualBase'] = array (
			'route' => '/fNAlterarNecesEventualBase',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNAlterarNecesEventualBase'
		);

		$routes['fNExcluirNecesBase'] = array (
			'route' => '/fNExcluirNecesBase',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNExcluirNecesBase'
		);

		$routes['fNImprimeNeces'] = array (
			'route' => '/fNImprimeNeces',
			'controller' => 'FamiliaNecessidadeController',
			'action' => 'fNImprimeNeces'
		);




// ============ Início tratamento de Suprimento Almoxarifado ========= //
		
		$routes['suprimentoAlmoxarifado'] = array (
			'route' => '/suprimentoAlmoxarifado',
			'controller' => 'SuprimentoAlmoxarifadoController',
			'action' => 'suprimentoAlmoxarifado'
		);

// ============ Início tratamento de Recursos Financeiros Famílias ========= //

		$routes['familiaFinanceiro'] = array (
			'route' => '/familiaFinanceiro',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'familiaFinanceiro'
		);

		$routes['recFinanFamiliaPreSolicitar'] = array (
			'route' => '/recFinanFamiliaPreSolicitar',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaPreSolicitar'
		);

		$routes['recFinanFamiliaSolicitar'] = array (
			'route' => '/recFinanFamiliaSolicitar',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaSolicitar'
		);

		$routes['recFinanFamiliaSolicitarMenu'] = array (
			'route' => '/recFinanFamiliaSolicitarMenu',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaSolicitarMenu'
		);

		$routes['recFinanFamiliaSolicitarIncluirBase'] = array (
			'route' => '/recFinanFamiliaSolicitarIncluirBase',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaSolicitarIncluirBase'
		);


		$routes['recFinanFamiliaSolicitarAtualizarBase'] = array (
			'route' => '/recFinanFamiliaSolicitarAtualizarBase',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaSolicitarAtualizarBase'
		);

		$routes['recFinanFamiliaVincular'] = array (
			'route' => '/recFinanFamiliaVincular',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaVincular'
		);

		$routes['recFinanFamiliaVincularBase'] = array (
			'route' => '/recFinanFamiliaVincularBase',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaVincularBase'
		);

		$routes['recFinanFamiliaSolicitarCancelarBase'] = array (
			'route' => '/recFinanFamiliaSolicitarCancelarBase',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaSolicitarCancelarBase'
		);

		$routes['recFinanFamiliaSolicitarConcluirBase'] = array (
			'route' => '/recFinanFamiliaSolicitarConcluirBase',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaSolicitarConcluirBase'
		);

		$routes['recFinanFamiliaPreConsultar'] = array (
			'route' => '/recFinanFamiliaPreConsultar',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaPreConsultar'
		);

		$routes['recFinanFamiliaConsultar'] = array (
			'route' => '/recFinanFamiliaConsultar',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaConsultar'
		);

		$routes['recFinanFamiliaConsultarMenu'] = array (
			'route' => '/recFinanFamiliaConsultarMenu',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaConsultarMenu'
		);

		$routes['recFinanFamiliaConsultarVinculo'] = array (
			'route' => '/recFinanFamiliaConsultarVinculo',
			'controller' => 'RecFinanFamiliaController',
			'action' => 'recFinanFamiliaConsultarVinculo'
		);

// ============ Início tratamento de Recursos Financeiros DPS - Recursos ========= //

		$routes['recFinanDPSRecurso'] = array (
			'route' => '/recFinanDPSRecurso',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecurso'
		);

		$routes['recFinanDPSRecursoVMR'] = array (
			'route' => '/recFinanDPSRecursoVMR',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoVMR'
		);

		$routes['recFinanDPSRecursoVMRMenu'] = array (
			'route' => '/recFinanDPSRecursoVMRMenu',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoVMRMenu'
		);

		$routes['recFinanDPSRecursoVMRBase'] = array (
			'route' => '/recFinanDPSRecursoVMRBase',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoVMRBase'
		);

		$routes['recFinanDPSRecursoReal'] = array (
			'route' => '/recFinanDPSRecursoReal',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoReal'
		);

		$routes['recFinanDPSRecursoRealMenu'] = array (
			'route' => '/recFinanDPSRecursoRealMenu',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoRealMenu'
		);

		$routes['recFinanDPSRecursoRealIncluiBase'] = array (
			'route' => '/recFinanDPSRecursoRealIncluiBase',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoRealIncluiBase'
		);

		$routes['recFinanDPSRecursoRealGerencia'] = array (
			'route' => '/recFinanDPSRecursoRealGerencia',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoRealGerencia'
		);

		$routes['recFinanDPSRecursoRealGerenciaAlteraBase'] = array (
			'route' => '/recFinanDPSRecursoRealGerenciaAlteraBase',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoRealGerenciaAlteraBase'
		);

		$routes['recFinanDPSRecursoRealGerenciaCancelaBase'] = array (
			'route' => '/recFinanDPSRecursoRealGerenciaCancelaBase',
			'controller' => 'RecFinanDPSRecursoController',
			'action' => 'recFinanDPSRecursoRealGerenciaCancelaBase'
		);

// ============ Início tratamento de Recursos Financeiros DPS - Gerencia ========= //

		$routes['recFinanDPSGerencia'] = array (
			'route' => '/recFinanDPSGerencia',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerencia'
		);

		$routes['recFinanDPSGerenciaAutorizacao'] = array (
			'route' => '/recFinanDPSGerenciaAutorizacao',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaAutorizacao'
		);

		$routes['recFinanDPSGerenciaAutorizacaoBase'] = array (
			'route' => '/recFinanDPSGerenciaAutorizacaoBase',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaAutorizacaoBase'
		);

		$routes['recFinanDPSGerenciaCancelaAutorizacao'] = array (
			'route' => '/recFinanDPSGerenciaCancelaAutorizacao',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaCancelaAutorizacao'
		);

		$routes['recFinanDPSGerenciaCancelaAutorizacaoBase'] = array (
			'route' => '/recFinanDPSGerenciaCancelaAutorizacaoBase',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaCancelaAutorizacaoBase'
		);

		$routes['recFinanDPSGerenciaCancelaSolicitacaoBase'] = array (
			'route' => '/recFinanDPSGerenciaCancelaSolicitacaoBase',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaCancelaSolicitacaoBase'
		);

		$routes['recFinanDPSGerenciaRessarcimento'] = array (
			'route' => '/recFinanDPSGerenciaRessarcimento',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaRessarcimento'
		);

		$routes['recFinanDPSGerenciaRessarcimentoMenu'] = array (
			'route' => '/recFinanDPSGerenciaRessarcimentoMenu',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaRessarcimentoMenu'
		);

		$routes['recFinanDPSGerenciaRessarIncluirBase'] = array (
			'route' => '/recFinanDPSGerenciaRessarIncluirBase',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaRessarIncluirBase'
		);

		$routes['recFinanDPSGerenciaRessarAlterarBase'] = array (
			'route' => '/recFinanDPSGerenciaRessarAlterarBase',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaRessarAlterarBase'
		);

		$routes['recFinanDPSGerenciaRessarCancelarBase'] = array (
			'route' => '/recFinanDPSGerenciaRessarCancelarBase',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaRessarCancelarBase'
		);

		$routes['recFinanDPSGerenciaRessarAutorizarBase'] = array (
			'route' => '/recFinanDPSGerenciaRessarAutorizarBase',
			'controller' => 'RecFinanDPSGerenciaController',
			'action' => 'recFinanDPSGerenciaRessarAutorizarBase'
		);


// ==================== Último comando ============= //

		$this->setRoutes($routes);

	}
}

?>