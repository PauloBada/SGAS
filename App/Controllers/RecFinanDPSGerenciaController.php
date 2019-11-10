<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class RecFinanDPSGerenciaController extends Action {

	// ================================================== //

	public function validaAutenticacao() {
		session_start();
		
		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}

	// ====================================================== //	
	
	public function validaAcesso() {

		$this->retornoValidaAcesso = 0;

		// Para possibilitar quem tem nível 1 e 2 ter acesso sem estar atrelado a grupo/subgrupo

		// $nivel_acesso_requerido 		  ==> constante da tabela tb_ace_login_sess
		$nivel_acesso_requerido = 2;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;

			$this->retornoValidaAcesso = 1;
		}

	}	// Fim da function validaAcesso

// ================================================== //

	public function recFinanDPSGerencia() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		$this->render('recFinanDPSGerencia');
	}

// ====================================================== //	
	
	public function recFinanDPSGerenciaAutorizacao() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->erroValidacao_msg)) {
			$this->view->erroValidacao_msg = '';
		} 

		$this->nivel_atuacao_requerido = 99;
		
		$this->validaAcesso();

		if ($this->retornoValidaAcesso == 1) {
			$this->render('recFinanDPSGerencia');				

		} else {

			// Buscar Pedidos Aguardando autorização => cd_est_pedido = 2
			$pedidoRecurFinanBase = Container::getModel('TbPedidoRecurFinan');
			$pedidoRecurFinanBase->__set('cd_est_pedido1',  2);
			$pedidoRecurFinanBase->__set('cd_est_pedido2',  2);
			$pedidoRecurFinan = $pedidoRecurFinanBase->getDadosPedidoRecurFinanAutorizacao();

			$this->view->pedidoRecFinan = array ();

			if (count($pedidoRecurFinan) > 0) {
				foreach ($pedidoRecurFinan as $index => $arr) {
					
					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $arr['cd_grp']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $arr['cd_sbgrp']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
					
					// Obter dados Pedido Recurso
					$dadosPedidoBase = Container::getModel('TbPedidoRecurFinan');
					$dadosPedidoBase->__set('cd_grp', $arr['cd_grp']);
					$dadosPedidoBase->__set('cd_sbgrp', $arr['cd_sbgrp']);
					$dadosPedidoBase->__set('seql_pedido_finan', $arr['seql_pedido_finan']);	
					$dadosPedido = $dadosPedidoBase->getDadosPedidoRecurFinan();

					// Buscar Nome do Voluntário
					$nomeVlntBase = Container::getModel('TbVlnt');
					$nomeVlntBase->__set('id', $arr['cd_vlnt_resp_pedido']);
					$nomeVlnt = $nomeVlntBase->getInfoVoluntario();

					$menor_vlr = number_format($arr['menor_vlr_encontra'], 2, ',', '.');

					// Há Pedidos Registrados
					array_push($this->view->pedidoRecFinan, array (
							'cd_grp' => $arr['cd_grp'],
							'nm_grp' => $dadosGS['nome_grupo'],

							'cd_nm_grp' => $arr['cd_grp'].'-'.$dadosGS['nome_grupo'],
							'cd_nm_sbgrp' => $arr['cd_sbgrp'].'-'.$dadosGS['nome_subgrupo'],
						
							'cd_sbgrp' => $arr['cd_sbgrp'], 
							'nm_sbgrp' => $dadosGS['nome_subgrupo'],
							'seql_pedido_finan' => $arr['seql_pedido_finan'],
							'dsc_sucinta_pedido' => $arr['dsc_sucinta_pedido'],
							'dsc_resum_pedido' => $arr['dsc_resum_pedido'],
							'menor_vlr_encontra' => $menor_vlr,
							'arq_orc_pedido' => $arr['arq_orc_pedido'],
							'arq_compara_preco_pedido' => $arr['arq_compara_preco_pedido'],
							'dt_incl_pedido' => $arr['dt_incl_pedido'],
							'dt_incl_pedido_format' => $arr['dt_incl_pedido_format'],
							'cd_vlnt_resp_pedido' => $arr['cd_vlnt_resp_pedido'],
							'nm_vlnt_resp_pedido' => $nomeVlnt['nm_vlnt'],
							'dt_autoriza_pedido' => $arr['dt_autoriza_pedido'],
							'dt_autoriza_pedido_format' => $arr['dt_autoriza_pedido_format'],
							'cd_vlnt_resp_autoriza' => $arr['cd_vlnt_resp_autoriza'],
							'cd_tip_enquadra' => $arr['cd_tip_enquadra_pedido'],
							'nm_tip_enquadra_format' => $arr['nm_tip_enquadra_pedido'],
							'cd_est_pedido' => $arr['cd_est_pedido'],
							'nm_est_pedido_format' => $arr['nm_est_pedido'],
							'cd_situ_envio_ressar_pedido' => $arr['cd_situ_envio_ressar_pedido'],
							'nm_situ_envio_ressar_pedido_format' => $arr['nm_situ_envio_ressar_pedido'],
							'dir_guarda_arq' => $arr['dir_guarda_arq'],
							'pedidoRF' => $arr['cd_grp'].';'.$arr['cd_sbgrp'].';'.$arr['seql_pedido_finan'].';'.$dadosGS['nome_grupo'].';'.$dadosGS['nome_subgrupo']
					));

				} 
			
			}
			
			// Obtem o saldo Atual de Recursos
			$saldoOrcBase = Container::getModel('TbOrcDps');
			$saldoOrc = $saldoOrcBase->getSaldoOrcDps();

			$this->view->saldoORC = $saldoOrc['saldo_atual_orc'];	
			$this->view->saldoORC = number_format($this->view->saldoORC, 2, ',', '.');				

			$this->render('recFinanDPSGerenciaAutorizacao');	

		}	
		
	}	// Fim da function recFinanDPSGerenciaAutorizacao

// ====================================================== //	
	
	public function recFinanDPSGerenciaAutorizacaoBase() {
		
		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Obtem o saldo Atual de Recursos
		$saldoOrcBase = Container::getModel('TbOrcDps');
		$saldoOrc = $saldoOrcBase->getSaldoOrcDps();

		// Vem da Base com ponto na casa decimal
		$saldo_atual_orc = $saldoOrc['saldo_atual_orc'];	
		
		// Formatar valor da Solicitação, para deixar somente com ponto nas casas decimais
		$vlr_solicitacao = str_replace('.','', $_POST['menor_vlr_encontra']);
		$vlr_solicitacao = str_replace(',','.', $vlr_solicitacao);

		// Saldo disponível permite autorizar solicitação
		if ($saldo_atual_orc >= $vlr_solicitacao) {
			// Buscar Dados da tabela tb_orc_dps
			$dadosOrcBase = Container::getModel('TbOrcDps');
			$dadosOrcBase->__set('cd_situ_recur_orc1', 1);  // 1-Orçamento com saldo
			$dadosOrcBase->__set('cd_situ_recur_orc2', 1);	 
			$dadosOrc = $dadosOrcBase->getDadosOrcDps2();

			if (count($dadosOrc) > 0) {
				// Alterar o Estado do Pedido para Autorizado
				$alteraPRF = Container::getModel('TbPedidoRecurFinan');
				$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
				$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
				$alteraPRF->__set('cd_est_pedido', 3);	// 3-Autorizado
				$alteraPRF->__set('cd_vlnt_resp_autoriza', $_SESSION['id']);	
				$alteraPRF->updatePRFCdEstadoAutoriza();

				foreach ($dadosOrc as $index => $arr) {
					if ($arr['vlr_sdo_recur_orc'] >= $vlr_solicitacao) {
						// Incluir em tb_vncl_orc_pedido
						$incluiVOP = Container::getModel('TbVnclOrcPedido');
						$incluiVOP->__set('cd_grp', $_POST['cb_grupo_escolhido']);
						$incluiVOP->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
						$incluiVOP->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
						$incluiVOP->__set('seql_orc', $arr['seql_orc']);	
						$incluiVOP->__set('vlr_orc_pedido', $vlr_solicitacao);	
						$incluiVOP->insertVnclOrcPedido();

						$novo_vlr_sdo_recur_orc = $arr['vlr_sdo_recur_orc'] - $vlr_solicitacao;

						if ($novo_vlr_sdo_recur_orc == 0) {						
							// Alterar vlr_sdo_recur_orc em tb_orc_dps
							$alteraOrc = Container::getModel('TbOrcDps');
							$alteraOrc->__set('seql_orc', $arr['seql_orc']);	
							$alteraOrc->__set('vlr_sdo_recur_orc', $novo_vlr_sdo_recur_orc);	
							$alteraOrc->__set('cd_situ_recur_orc', 2);	// 2-Orçamento realizado e sem saldo
							$alteraOrc->updateSaldoSituacaoOrcDps();

						} else {
							// Alterar vlr_sdo_recur_orc em tb_orc_dps
							$alteraOrc = Container::getModel('TbOrcDps');
							$alteraOrc->__set('seql_orc', $arr['seql_orc']);	
							$alteraOrc->__set('vlr_sdo_recur_orc', $novo_vlr_sdo_recur_orc);	
							$alteraOrc->updateSaldoOrcDps();
						}

						break;

					} else {

						// Incluir em tb_vncl_orc_pedido
						$incluiVOP = Container::getModel('TbVnclOrcPedido');
						$incluiVOP->__set('cd_grp', $_POST['cb_grupo_escolhido']);
						$incluiVOP->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
						$incluiVOP->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
						$incluiVOP->__set('seql_orc', $arr['seql_orc']);	
						$incluiVOP->__set('vlr_orc_pedido', $arr['vlr_sdo_recur_orc']);	
						$incluiVOP->insertVnclOrcPedido();

						$novo_vlr_sdo_recur_orc = 0;

						// Alterar tb_orc_dps
						$alteraOrc = Container::getModel('TbOrcDps');
						$alteraOrc->__set('seql_orc', $arr['seql_orc']);	
						$alteraOrc->__set('vlr_sdo_recur_orc', $novo_vlr_sdo_recur_orc);	
						$alteraOrc->__set('cd_situ_recur_orc', 2);	// 2-Orçamento realizado e sem saldo
						$alteraOrc->updateSaldoSituacaoOrcDps();

						$vlr_solicitacao = $vlr_solicitacao - $arr['vlr_sdo_recur_orc'];
					}
				}

				// Retornar com informação de ok
				$this->view->erroValidacao = 2;
				$this->view->erroValidacao_msg = 'Autorização Realizada com Sucesso! Grupo: '.$_POST['cb_grupo_escolhido'].'-'.$_POST['nome_grupo'].', Subgrupo: '.$_POST['cb_subgrupo_escolhido'].'-'.$_POST['nome_subgrupo']. ', Seql Pedido: '.$_POST['seql_pedido_finan'];

			} else {
				// Retornar com informação de que não há saldo suficiente
				$this->view->erroValidacao = 3;
				$this->view->erroValidacao_msg = 'Erro: Sem Saldo Suficiente para Autorização! Grupo: '.$_POST['cb_grupo_escolhido'].'-'.$_POST['nome_grupo'].', Subgrupo: '.$_POST['cb_subgrupo_escolhido'].'-'.$_POST['nome_subgrupo']. ', Seql Pedido: '.$_POST['seql_pedido_finan'];

			}

		} else {
			// Retornar com informação de que não há saldo suficiente
			$this->view->erroValidacao = 3;
			$this->view->erroValidacao_msg = 'Erro: Sem Saldo Suficiente para Autorização! Grupo: '.$_POST['cb_grupo_escolhido'].'-'.$_POST['nome_grupo'].', Subgrupo: '.$_POST['cb_subgrupo_escolhido'].'-'.$_POST['nome_subgrupo']. ', Seql Pedido: '.$_POST['seql_pedido_finan'];
		}

		session_write_close();
		$this->recFinanDPSGerenciaAutorizacao();


	}	// Fim da function recFinanDPSGerenciaAutorizacaoBase

// ====================================================== //	
	
	public function recFinanDPSGerenciaCancelaAutorizacao() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->erroValidacao_msg)) {
			$this->view->erroValidacao_msg = '';
		} 

		$this->nivel_atuacao_requerido = 99;
		
		$this->validaAcesso();

		if ($this->retornoValidaAcesso == 1) {
			$this->render('recFinanDPSGerencia');				

		} else {

			// Buscar Pedidos Autorizados => cd_est_pedido = 3
			$pedidoRecurFinanBase = Container::getModel('TbPedidoRecurFinan');
			$pedidoRecurFinanBase->__set('cd_est_pedido',  3);						// 3-Autorizado
			$pedidoRecurFinanBase->__set('cd_situ_envio_ressar_pedido1',  1); // 1-Não enviado
			$pedidoRecurFinanBase->__set('cd_situ_envio_ressar_pedido2',  1);
			$pedidoRecurFinan = $pedidoRecurFinanBase->getDadosPedidoRecurFinanCancelaAutorizacao();

			$this->view->pedidoRecFinan = array ();

			if (count($pedidoRecurFinan) > 0) {
				foreach ($pedidoRecurFinan as $index => $arr) {
					
					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $arr['cd_grp']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $arr['cd_sbgrp']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
					
					// Obter dados Pedido Recurso
					$dadosPedidoBase = Container::getModel('TbPedidoRecurFinan');
					$dadosPedidoBase->__set('cd_grp', $arr['cd_grp']);
					$dadosPedidoBase->__set('cd_sbgrp', $arr['cd_sbgrp']);
					$dadosPedidoBase->__set('seql_pedido_finan', $arr['seql_pedido_finan']);	
					$dadosPedido = $dadosPedidoBase->getDadosPedidoRecurFinan();

					// Buscar Nome do Voluntário
					$nomeVlntBase = Container::getModel('TbVlnt');
					$nomeVlntBase->__set('id', $arr['cd_vlnt_resp_pedido']);
					$nomeVlnt = $nomeVlntBase->getInfoVoluntario();

					$menor_vlr = number_format($arr['menor_vlr_encontra'], 2, ',', '.');

					// Há Pedidos Registrados
					array_push($this->view->pedidoRecFinan, array (
							'cd_grp' => $arr['cd_grp'],
							'nm_grp' => $dadosGS['nome_grupo'],

							'cd_nm_grp' => $arr['cd_grp'].'-'.$dadosGS['nome_grupo'],
							'cd_nm_sbgrp' => $arr['cd_sbgrp'].'-'.$dadosGS['nome_subgrupo'],
						
							'cd_sbgrp' => $arr['cd_sbgrp'], 
							'nm_sbgrp' => $dadosGS['nome_subgrupo'],
							'seql_pedido_finan' => $arr['seql_pedido_finan'],
							'dsc_sucinta_pedido' => $arr['dsc_sucinta_pedido'],
							'dsc_resum_pedido' => $arr['dsc_resum_pedido'],
							'menor_vlr_encontra' => $menor_vlr,
							'arq_orc_pedido' => $arr['arq_orc_pedido'],
							'arq_compara_preco_pedido' => $arr['arq_compara_preco_pedido'],
							'dt_incl_pedido' => $arr['dt_incl_pedido'],
							'dt_incl_pedido_format' => $arr['dt_incl_pedido_format'],
							'cd_vlnt_resp_pedido' => $arr['cd_vlnt_resp_pedido'],
							'nm_vlnt_resp_pedido' => $nomeVlnt['nm_vlnt'],
							'dt_autoriza_pedido' => $arr['dt_autoriza_pedido'],
							'dt_autoriza_pedido_format' => $arr['dt_autoriza_pedido_format'],
							'cd_vlnt_resp_autoriza' => $arr['cd_vlnt_resp_autoriza'],
							'cd_tip_enquadra' => $arr['cd_tip_enquadra_pedido'],
							'nm_tip_enquadra_format' => $arr['nm_tip_enquadra_pedido'],
							'cd_est_pedido' => $arr['cd_est_pedido'],
							'nm_est_pedido_format' => $arr['nm_est_pedido'],
							'cd_situ_envio_ressar_pedido' => $arr['cd_situ_envio_ressar_pedido'],
							'nm_situ_envio_ressar_pedido_format' => $arr['nm_situ_envio_ressar_pedido'],
							'dir_guarda_arq' => $arr['dir_guarda_arq'],
							'pedidoRF' => $arr['cd_grp'].';'.$arr['cd_sbgrp'].';'.$arr['seql_pedido_finan'].';'.$dadosGS['nome_grupo'].';'.$dadosGS['nome_subgrupo']
					));
				} 
			}
			
			// Obtem o saldo Atual de Recursos
			$saldoOrcBase = Container::getModel('TbOrcDps');
			$saldoOrc = $saldoOrcBase->getSaldoOrcDps();

			$this->view->saldoORC = $saldoOrc['saldo_atual_orc'];	
			$this->view->saldoORC = number_format($this->view->saldoORC, 2, ',', '.');				

			$this->render('recFinanDPSGerenciaCancelaAutorizacao');	

		}	
		
	}	// Fim da function recFinanDPSGerenciaCancelaAutorizacao

// ====================================================== //	
	
	public function recFinanDPSGerenciaCancelaAutorizacaoBase() {
		
		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Buscar dados na tb_vncl_orc_pedido
		$dadosVOPBase = Container::getModel('TbVnclOrcPedido');
		$dadosVOPBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$dadosVOPBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$dadosVOPBase->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$dadosVOP = $dadosVOPBase->getVnclOrcPedido();

		foreach ($dadosVOP as $index => $arr) {		
			$dadosOrcBase = Container::getModel('TbOrcDps');
			$dadosOrcBase->__set('seql_orc', $arr['seql_orc']); 
			$dadosOrc = $dadosOrcBase->getDadosOrcDps();

			$novo_vlr_sdo_recur_orc = $arr['vlr_orc_pedido'] + $dadosOrc['vlr_sdo_recur_orc'];

			// Alterar tb_orc_dps
			$alteraOrc = Container::getModel('TbOrcDps');
			$alteraOrc->__set('seql_orc', $arr['seql_orc']);	
			$alteraOrc->__set('vlr_sdo_recur_orc', $novo_vlr_sdo_recur_orc);	
			$alteraOrc->__set('cd_situ_recur_orc', 1);	// 1-Orçamento com saldo
			$alteraOrc->updateSaldoSituacaoOrcDps();
		
			// Alterar tb_vncl_orc_pedido para cd_est_vncl = 2 - Cancelado
			$alteraVOP = Container::getModel('TbVnclOrcPedido');
			$alteraVOP->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$alteraVOP->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$alteraVOP->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
			$alteraVOP->__set('seql_orc', $arr['seql_orc']);	
			$alteraVOP->updateVnclOrcPedido();
		}

		// Alterar o Estado do Pedido para 2-Aguardando Autorização
		$alteraPRF = Container::getModel('TbPedidoRecurFinan');
		$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraPRF->__set('cd_est_pedido', 2);				// 2-Aguardando Autorização
		$alteraPRF->__set('cd_vlnt_resp_autoriza', '');	
		$alteraPRF->updatePRFCdEstadoAutoriza();
		
		session_write_close();
		$this->recFinanDPSGerenciaCancelaAutorizacao();

	}	// Fim da function recFinanDPSGerenciaCancelaAutorizacaoBase


// ====================================================== //	
	
	public function recFinanDPSGerenciaCancelaSolicitacaoBase() {
		
		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Alterar o Estado do Pedido para Autorizado
		$alteraPRF = Container::getModel('TbPedidoRecurFinan');
		$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraPRF->__set('cd_est_pedido', 4);			// 3-Cancelado
		$alteraPRF->__set('cd_vlnt_resp_autoriza', '');	
		$alteraPRF->updatePRFCdEstadoAutoriza();

		// Retornar com informação de ok
		$this->view->erroValidacao = 2;
		$this->view->erroValidacao_msg = 'Cancelamento Realizado com Sucesso! Grupo: '.$_POST['cb_grupo_escolhido'].'-'.$_POST['nome_grupo'].', Subgrupo: '.$_POST['cb_subgrupo_escolhido'].'-'.$_POST['nome_subgrupo']. ', Seql Pedido: '.$_POST['seql_pedido_finan'];

		session_write_close();
		$this->recFinanDPSGerenciaAutorizacao();

	}	// Fim da function recFinanDPSGerenciaCancelaSolicitacaoBase

// ====================================================== //	
	
	public function recFinanDPSGerenciaRessarcimento() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->erroValidacao_msg)) {
			$this->view->erroValidacao_msg = '';
		} 

		$this->nivel_atuacao_requerido = 99;
		
		$this->validaAcesso();

		if ($this->retornoValidaAcesso == 1) {
			$this->render('recFinanDPSGerencia');				

		} else {

			// Buscar Pedidos Aguardando autorização => cd_est_pedido = 3
			$pedidoRecurFinanBase = Container::getModel('TbPedidoRecurFinan');
			$pedidoRecurFinanBase->__set('cd_est_pedido',  3);   						// 3-Autorizado
			$pedidoRecurFinanBase->__set('cd_situ_envio_ressar_pedido1',  1);   	// 1-Não Enviado
			$pedidoRecurFinanBase->__set('cd_situ_envio_ressar_pedido2',  1);
			$pedidoRecurFinan = $pedidoRecurFinanBase->getDadosPedidoRecurFinanRessarcimento();

			$this->view->pedidoRecFinan = array ();

			if (count($pedidoRecurFinan) > 0) {
				foreach ($pedidoRecurFinan as $index => $arr) {
					
					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $arr['cd_grp']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $arr['cd_sbgrp']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
					
					// Obter dados Pedido Recurso
					$dadosPedidoBase = Container::getModel('TbPedidoRecurFinan');
					$dadosPedidoBase->__set('cd_grp', $arr['cd_grp']);
					$dadosPedidoBase->__set('cd_sbgrp', $arr['cd_sbgrp']);
					$dadosPedidoBase->__set('seql_pedido_finan', $arr['seql_pedido_finan']);	
					$dadosPedido = $dadosPedidoBase->getDadosPedidoRecurFinan();

					// Buscar Nome do Voluntário Responsável pelo Pedido
					$nomeVlntBase = Container::getModel('TbVlnt');
					$nomeVlntBase->__set('id', $arr['cd_vlnt_resp_pedido']);
					$nomeVlntPedido = $nomeVlntBase->getInfoVoluntario();

					// Buscar Nome do Voluntário Responsável pela Autorização
					$nomeVlntBase = Container::getModel('TbVlnt');
					$nomeVlntBase->__set('id', $arr['cd_vlnt_resp_autoriza']);
					$nomeVlntAutoriza = $nomeVlntBase->getInfoVoluntario();

					$menor_vlr = number_format($arr['menor_vlr_encontra'], 2, ',', '.');

					// Há Pedidos Registrados
					array_push($this->view->pedidoRecFinan, array (
							'cd_grp' => $arr['cd_grp'],
							'nm_grp' => $dadosGS['nome_grupo'],

							'cd_nm_grp' => $arr['cd_grp'].'-'.$dadosGS['nome_grupo'],
							'cd_nm_sbgrp' => $arr['cd_sbgrp'].'-'.$dadosGS['nome_subgrupo'],
						
							'cd_sbgrp' => $arr['cd_sbgrp'], 
							'nm_sbgrp' => $dadosGS['nome_subgrupo'],
							'seql_pedido_finan' => $arr['seql_pedido_finan'],
							'dsc_sucinta_pedido' => $arr['dsc_sucinta_pedido'],
							'dsc_resum_pedido' => $arr['dsc_resum_pedido'],
							'menor_vlr_encontra' => $menor_vlr,
							'arq_orc_pedido' => $arr['arq_orc_pedido'],
							'arq_compara_preco_pedido' => $arr['arq_compara_preco_pedido'],
							'dt_incl_pedido' => $arr['dt_incl_pedido'],
							'dt_incl_pedido_format' => $arr['dt_incl_pedido_format'],

							'cd_vlnt_resp_pedido' => $arr['cd_vlnt_resp_pedido'],
							'nm_vlnt_resp_pedido' => $nomeVlntPedido['nm_vlnt'],

							'dt_autoriza_pedido' => $arr['dt_autoriza_pedido'],
							
							'dt_autoriza_pedido_format' => $arr['dt_autoriza_pedido_format'],

							'cd_vlnt_resp_autoriza' => $arr['cd_vlnt_resp_autoriza'],
							'nm_vlnt_resp_autoriza' => $nomeVlntAutoriza['nm_vlnt'],
							
							'cd_tip_enquadra' => $arr['cd_tip_enquadra_pedido'],
							'nm_tip_enquadra_format' => $arr['nm_tip_enquadra_pedido'],
							'cd_est_pedido' => $arr['cd_est_pedido'],
							'nm_est_pedido_format' => $arr['nm_est_pedido'],
							'cd_situ_envio_ressar_pedido' => $arr['cd_situ_envio_ressar_pedido'],
							'nm_situ_envio_ressar_pedido_format' => $arr['nm_situ_envio_ressar_pedido'],
							'dir_guarda_arq' => $arr['dir_guarda_arq'],
							'pedidoRF' => $arr['cd_grp'].';'.$arr['cd_sbgrp'].';'.$arr['seql_pedido_finan'].';'.$dadosGS['nome_grupo'].';'.$dadosGS['nome_subgrupo'].';'.$menor_vlr
					));

				} 
			
			}
			
			// Obtem a soma dos pedidos dos recursos autorizados
			$saldoPRFBase = Container::getModel('TbPedidoRecurFinan');
			$saldoPRFBase->__set('cd_est_pedido', 3);				//3-Autorizado
			$saldoPRF = $saldoPRFBase->getSumVlrAutorizado();

			$this->view->saldoPRF = $saldoPRF['saldo_vlr_autorizado'];	
			$this->view->saldoPRF = number_format($this->view->saldoPRF, 2, ',', '.');				

			$this->render('recFinanDPSGerenciaRessarcimento');	

		}	
		
	}	// Fim da function recFinanDPSGerenciaRessarcimento

// ====================================================== //	
	
	public function recFinanDPSGerenciaRessarcimentoMenu() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 
	
		// Verifica se há solicitação de Ressarcimento para o Pedido em tb_ressar_pedido_recur_finan
		$qtdRPRFBase = Container::getModel('TbRessarPedidoRecurFinan');
		$qtdRPRFBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$qtdRPRFBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$qtdRPRFBase->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$qtdRPRFBase->__set('cd_est_ressar', 1);				// 1-Aguardando envio ao DAF
		$qtdRPRF = $qtdRPRFBase->getCountRPRF();

		if ($qtdRPRF['qtde'] == 0) {

			$data_hoje 		= 	new \DateTime();
			$data_hoje_f	= 	$data_hoje->format("d/m/Y");

			// Vem da Tela Inicial
			if (isset($_POST['menor_valor_encontra'])) {
				$menor_valor_encontra = $_POST['menor_valor_encontra'];
			} else {
				// Vem qdo se Cancela
				if (isset($_POST['vlr_doc_ressar'])) {
					$menor_valor_encontra = $_POST['vlr_doc_ressar'];
				}
			}

			$this->view->ressarcimentoRecFinan = array(
					'cd_grp' => $_POST['cb_grupo_escolhido'],
					'nm_grp' => $_POST['nome_grupo'],
					'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
					'nm_sbgrp' => $_POST['nome_subgrupo'],
					'seql_pedido_finan' => $_POST['seql_pedido_finan'],
					'seql_ressar_pedido_finan' => '',
					'dt_doc_ressar' => $data_hoje_f,
					'cd_tip_doc_ressar' => 1,
					'vlr_doc_ressar' => $menor_valor_encontra,
					
					'bco_cred_ressar' => '',
					'ag_cred_ressar' => '',
					'cta_cred_ressar' => '',
					'dig_verifica_cta_cred_ressar' => '',

					'cpf_cred_ressar' => '',
					'cnpj_cred_ressar' => '',

					'dt_envio_ressar_daf' => '',
					'dt_efetiva_cred_ressar' => '',
					'cd_est_ressar' => 1,
					'dsc_mtvo_indefer_ressar_daf' => '',
					'cd_vlnt_resp_incl_ressar' => $_SESSION['id'],
					'cd_vlnt_resp_envio_daf' => '',
					'cd_vlnt_resp_baixa_ressar' => '',

					'origem' => 'inclusao'
			);

		} else {

			// Buscar dados de tb_ressar_pedido_recur_finan
			$dadosPRFBase = Container::getModel('TbRessarPedidoRecurFinan');
			$dadosPRFBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPRFBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPRFBase->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
			$dadosPRFBase->__set('cd_est_ressar', 1);				// 1-Aguardando envio ao DAF
			$dadosPRF = $dadosPRFBase->getDadosRPRF();

			if (!empty($dadosPRF['cpf_cred_ressar'])) {
				$cpf_cred_ressar = $dadosPRF['cpf_cred_ressar'];
				$cpf_cred_ressar = Funcoes::formatarNumeros('cpf', $cpf_cred_ressar, 11, "");
				
				$cnpj_cred_ressar = '';
			} else {
				$cnpj_cred_ressar = $dadosPRF['cnpj_cred_ressar'];
				$cnpj_cred_ressar = Funcoes::formatarNumeros('cnpj', $cnpj_cred_ressar, 14, "");

				$cpf_cred_ressar = '';
			}

			$this->view->ressarcimentoRecFinan = array(
					'cd_grp' => $_POST['cb_grupo_escolhido'],
					'nm_grp' => $_POST['nome_grupo'],
					'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
					'nm_sbgrp' => $_POST['nome_subgrupo'],
					'seql_pedido_finan' => $_POST['seql_pedido_finan'],
					'seql_ressar_pedido_finan' => $dadosPRF['seql_ressar_pedido_finan'],
					'dt_incl_ressar' => $dadosPRF['dt_incl_ressar_format'],
					'dt_doc_ressar' => $dadosPRF['dt_doc_ressar_format'],
					'cd_tip_doc_ressar' => $dadosPRF['cd_tip_doc_ressar'],
					'vlr_doc_ressar' => $dadosPRF['vlr_doc_ressar'],
					'bco_cred_ressar' => $dadosPRF['bco_cred_ressar'],
					'ag_cred_ressar' => $dadosPRF['ag_cred_ressar'],
					'cta_cred_ressar' => $dadosPRF['cta_cred_ressar'],
					'dig_verifica_cta_cred_ressar' => $dadosPRF['dig_verifica_cta_cred_ressar'],
					'cpf_cred_ressar' => $cpf_cred_ressar,
					'cnpj_cred_ressar' => $cnpj_cred_ressar,
					'dt_envio_ressar_daf' => '',
					'dt_efetiva_cred_ressar' => '',
					'cd_est_ressar' => $dadosPRF['cd_est_ressar'],
					'nm_cd_est_ressar' => $dadosPRF['nm_cd_est_ressar'],
					'dsc_mtvo_indefer_ressar_daf' => '',
					'cd_vlnt_resp_incl_ressar' => $dadosPRF['cd_vlnt_resp_incl_ressar'],
					'cd_vlnt_resp_envio_daf' => '',
					'cd_vlnt_resp_baixa_ressar' => '',

					'origem' => 'gerenciamento'
			);
		}

		$this->render('recFinanDPSGerenciaRessarcimentoMenu');	
		
	}	// Fim da function recFinanDPSGerenciaRessarcimentoMenu

// ====================================================== //	
	
	public function recFinanDPSGerenciaRessarIncluirBase() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!empty($_POST['cpf_cred_ressar'])) {
			$cpf_cred_ressar = str_replace('.','', $_POST['cpf_cred_ressar']);
			$cpf_cred_ressar = str_replace('-','', $cpf_cred_ressar);

		}	else {
			$cpf_cred_ressar = '';
		}

		if (!empty($_POST['cnpj_cred_ressar'])) {
			$cnpj_cred_ressar = str_replace('.','', $_POST['cnpj_cred_ressar']);
			$cnpj_cred_ressar = str_replace('-','', $cnpj_cred_ressar);
			$cnpj_cred_ressar = str_replace('/','', $cnpj_cred_ressar);

		}	else {
			$cnpj_cred_ressar = '';
		}

		$vlr_doc_ressar = str_replace('.','', $_POST['vlr_doc_ressar']);
		$vlr_doc_ressar = str_replace(',','.', $vlr_doc_ressar);

		// Incluir em tb_ressar_pedido_recur_finan
		$insereRPRF = Container::getModel('TbRessarPedidoRecurFinan');
		$insereRPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$insereRPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$insereRPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$insereRPRF->__set('cd_tip_doc_ressar', $_POST['cd_tip_doc_ressar']);	
		$insereRPRF->__set('dt_doc_ressar', $_POST['dt_doc_ressar']);	
		$insereRPRF->__set('vlr_doc_ressar', $vlr_doc_ressar);	
		$insereRPRF->__set('bco_cred_ressar', $_POST['bco_cred_ressar']);	
		$insereRPRF->__set('ag_cred_ressar', $_POST['ag_cred_ressar']);	
		$insereRPRF->__set('cta_cred_ressar', $_POST['cta_cred_ressar']);	
		$insereRPRF->__set('dig_verifica_cta_cred_ressar', $_POST['dig_verifica_cta_cred_ressar']);	
		$insereRPRF->__set('cpf_cred_ressar', $cpf_cred_ressar);	
		$insereRPRF->__set('cnpj_cred_ressar', $cnpj_cred_ressar);	
		$insereRPRF->__set('cd_vlnt_resp_incl_ressar', $_SESSION['id']);	
		$insereRPRF->__set('cd_est_ressar', 1);				// 1-Aguardando envio ao DAF
		$insereRPRF->insertRessarPRF();

		$this->view->erroValidacao = 3;

		session_write_close();
		$this->recFinanDPSGerenciaRessarcimentoMenu();


	}	// Fim da function recFinanDPSGerenciaRessarIncluirBase

// ====================================================== //	
	
	public function recFinanDPSGerenciaRessarAlterarBase() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!empty($_POST['cpf_cred_ressar'])) {
			$cpf_cred_ressar = str_replace('.','', $_POST['cpf_cred_ressar']);
			$cpf_cred_ressar = str_replace('-','', $cpf_cred_ressar);

		}	else {
			$cpf_cred_ressar = '';
		}

		if (!empty($_POST['cnpj_cred_ressar'])) {
			$cnpj_cred_ressar = str_replace('.','', $_POST['cnpj_cred_ressar']);
			$cnpj_cred_ressar = str_replace('-','', $cnpj_cred_ressar);
			$cnpj_cred_ressar = str_replace('/','', $cnpj_cred_ressar);

		}	else {
			$cnpj_cred_ressar = '';
		}

		// Alterar em tb_ressar_pedido_recur_finan
		$alteraRPRF = Container::getModel('TbRessarPedidoRecurFinan');
		$alteraRPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraRPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraRPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraRPRF->__set('seql_ressar_pedido_finan', $_POST['seql_ressar_pedido_finan']);	
		$alteraRPRF->__set('cd_tip_doc_ressar', $_POST['cd_tip_doc_ressar']);	
		$alteraRPRF->__set('dt_doc_ressar', $_POST['dt_doc_ressar']);	
		$alteraRPRF->__set('bco_cred_ressar', $_POST['bco_cred_ressar']);	
		$alteraRPRF->__set('ag_cred_ressar', $_POST['ag_cred_ressar']);	
		$alteraRPRF->__set('cta_cred_ressar', $_POST['cta_cred_ressar']);	
		$alteraRPRF->__set('dig_verifica_cta_cred_ressar', $_POST['dig_verifica_cta_cred_ressar']);	
		$alteraRPRF->__set('cpf_cred_ressar', $cpf_cred_ressar);	
		$alteraRPRF->__set('cnpj_cred_ressar', $cnpj_cred_ressar);	
		$alteraRPRF->__set('cd_vlnt_resp_incl_ressar', $_SESSION['id']);	
		$alteraRPRF->updateRessarPRF();

		$this->view->erroValidacao = 4;

		session_write_close();
		$this->recFinanDPSGerenciaRessarcimentoMenu();

	}	// Fim da function recFinanDPSGerenciaRessarAlterarBase

// ====================================================== //	
	
	public function recFinanDPSGerenciaRessarCancelarBase() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Alterar em tb_ressar_pedido_recur_finan
		$alteraRPRF = Container::getModel('TbRessarPedidoRecurFinan');
		$alteraRPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraRPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraRPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraRPRF->__set('seql_ressar_pedido_finan', $_POST['seql_ressar_pedido_finan']);	
		$alteraRPRF->__set('cd_est_ressar', 4);	// 4-Cancelado
		$alteraRPRF->__set('cd_vlnt_resp_incl_ressar', $_SESSION['id']);	
		$alteraRPRF->updateEstadoRessarPRF();

		$this->view->erroValidacao = 5;

		session_write_close();
		$this->recFinanDPSGerenciaRessarcimentoMenu();

	}	// Fim da function recFinanDPSGerenciaRessarCancelarBase

// ====================================================== //	
	
	public function recFinanDPSGerenciaRessarAutorizarBase() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Alterar em tb_ressar_pedido_recur_finan
		$alteraRPRF = Container::getModel('TbRessarPedidoRecurFinan');
		$alteraRPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraRPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraRPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraRPRF->__set('seql_ressar_pedido_finan', $_POST['seql_ressar_pedido_finan']);	
		$alteraRPRF->__set('cd_est_ressar', 2);	// 2-Aguardando ressarcimento pela DAF
		$alteraRPRF->__set('cd_vlnt_resp_envio_daf', $_SESSION['id']);	
		$alteraRPRF->updateAutorizaRessarPRF();

		// Alterar tb_pedido_recur_finan
		$alteraPRF = Container::getModel('TbPedidoRecurFinan');
		$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraPRF->__set('cd_situ_envio_ressar_pedido', 2);		// Enviado	
		$alteraPRF->updatePRFRemeteDAF();

		$this->view->erroValidacao = 6;
		$this->view->erroValidacao_msg = 'Marcada como enviada ao DAF! Grupo:'.$_POST['cb_grupo_escolhido'].'-'.$_POST['nome_grupo'].', Subgrupo: '.$_POST['cb_subgrupo_escolhido'].'-'.$_POST['nome_subgrupo'].', Solic.: '.$_POST['seql_pedido_finan'].'. Ressarc.: '.$_POST['seql_ressar_pedido_finan'].', Valor (R$) '.$_POST['vlr_doc_ressar'];

		session_write_close();
		$this->recFinanDPSGerenciaRessarcimento();

	}	// Fim da function recFinanDPSGerenciaRessarAutorizarBase


}	//	Fim da classe

?>
				