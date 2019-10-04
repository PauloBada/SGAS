<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 28/09/2019
   Objetivo:  Controller para opções do menu Necessidades de Famílias do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class FamiliaNecessidadeController extends Action {

// ================================================== //

	public function validaAutenticacao() {
		session_start();
		
		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}

// ====================================================== //	
	
	public function validaAcessoAcompanhamento() {

		$this->retornoValidaAcessoAcompanhamento = 0;

		$atuacaoVoluntarioBase = Container::getModel('TbVnclVlntGrp');
		$atuacaoVoluntarioBase->__set('codVoluntario', $_SESSION['id']);
		$atuacaoVoluntarioBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
		$atuacaoVoluntarioBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
		$atuacaoVoluntario = $atuacaoVoluntarioBase->getNivelAtuacao();

		// Não está na tabela tb_vncl_vlnt_grp, ou seja, não está atrelado ao grupo/subgrupo
		if (empty($atuacaoVoluntario['cod_atuacao'])) { 

			// Para possibilitar quem tem nível 1 e 2 consultar relatórios sem estar atrelado a grupo/subgrupo
			if ($this->nivel_atuacao_requerido == 99) {
				$nivel_acesso_requerido = 2;
				$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

				// Para validar se Voluntário tem o nível adequado para fazer a operação
				if ($autenticar_acesso['autorizado'] == 0) {
					$this->view->erroValidacao = 5;

					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

					$this->view->grupoTratado = $dadosGS['nome_grupo'];
					$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

					$this->retornoValidaAcessoAcompanhamento = 1;
				}

			} else {

				$this->view->erroValidacao = 5;

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

				$this->view->grupoTratado = $dadosGS['nome_grupo'];
				$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

				$this->retornoValidaAcessoAcompanhamento = 1;
			}

		} else {	

			// Nível 99 somente verifica se está no grupo/subgrupo, exceto nível gerak 1 e 2 (Para consulta de relatórios)
			if ($this->nivel_atuacao_requerido != 99) {
				// Está na tabela tb_vncl_vlnt_grp, mas não tem o nível Requerido
				if ($atuacaoVoluntario['cod_atuacao'] != $this->nivel_atuacao_requerido) { 
					$this->view->erroValidacao = 6;

					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

					$this->view->grupoTratado = $dadosGS['nome_grupo'];
					$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

					if ($atuacaoVoluntario['cod_atuacao'] == 1) {
						$this->view->atuacaoLogado = 'Coordenador de Cadastro';	

					}	else if ($atuacaoVoluntario['cod_atuacao'] == 2){
						$this->view->atuacaoLogado = 'Coordenador de Finanças';

					}	else if ($atuacaoVoluntario['cod_atuacao'] == 3){
						$this->view->atuacaoLogado = 'Coordenador Revisor';
					
					}	else if ($atuacaoVoluntario['cod_atuacao'] == 4){
						$this->view->atuacaoLogado = 'Coordenador Geral';
					
					}	else if ($atuacaoVoluntario['cod_atuacao'] == 5){
						$this->view->atuacaoLogado = 'Voluntário';
					}

					if ($this->nivel_atuacao_requerido == 1) {
						$this->view->atuacaoRequerida = 'Coordenador de Cadastro';	

					}	else if ($this->nivel_atuacao_requerido == 2){
						$this->view->atuacaoRequerida = 'Coordenador de Finanças';

					}	else if ($this->nivel_atuacao_requerido == 3){
						$this->view->atuacaoRequerida = 'Coordenador Revisor';
					
					}	else if ($this->nivel_atuacao_requerido == 4){
						$this->view->atuacaoRequerida = 'Coordenador Geral';
					
					}	else if ($this->nivel_atuacao_requerido == 5){
						$this->view->atuacaoRequerida = 'Voluntário';
					}

					$this->retornoValidaAcessoAcompanhamento = 2;
				}
			}
		}

	}	// Fim da function validaAcessoAcompanhamento

// ====================================================== //		

	public function obtemEstSituFml($cdEstSituFml) {

		switch ($cdEstSituFml)
	    	{
	        case 1:
	            {
               	$this->view->cd_est_situ_fml = 'Aguardando definição de grupo/subgrupo';
                break;
	            }

	        case 2:
	            {
               	$this->view->cd_est_situ_fml = 'Aguardando Triagem (início ou conclusão)';
                break;
	            }

	        case 3:
	            {
               	$this->view->cd_est_situ_fml = 'Em atendimento pela DPS';
                break;
	            }

	        case 4:
	            {
               	$this->view->cd_est_situ_fml = 'Atendimento realizado e encerrado';
                break;
	            }

	        case 5:
	            {
               	$this->view->cd_est_situ_fml = 'Atendimento não realizado por impossibilidade triagem';
                break;
	            }

	        case 6:
	            {
               	$this->view->cd_est_situ_fml = 'Atendimento não realizado por família não necessitar';
                break;
	            }
	      }

	}	// Fim da function obtemEstSituFml

	// ====================================================== //	
	
	public function obtemDataProximaVisita($data, $grupo) {

		// $data = formato AAAA-MM-DD

		$this->semana_atuacao_grupo = 0;
		$this->prox_data_visita = '9999-99-99';

		// Buscar cd_semn_atu em tb_grp
		$pegaSemanaAtuacaoBase = Container::getModel('TbGrp');
		$pegaSemanaAtuacaoBase->__set('cd_grp', $grupo);
		$pegaSemanaAtuacao = $pegaSemanaAtuacaoBase->getDadosGrupo(); 

		$this->semana_atuacao_grupo = $pegaSemanaAtuacao['cod_semana'];

		$this->prox_data_visita = Funcoes::CalculaProximaDataVisita( $data, $this->semana_atuacao_grupo );

	}	// Fim da function obtemDataProximaVisita


// ================================================== //
//          Início de Necessidade de Família          //
// ================================================== //

	public function familiaNecessidade() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		$this->render('familiaNecessidade');
	}

// ====================================================== //	
		
	public function fNPreIncluirNeces() {
		
		$this->validaAutenticacao();		

		// Verifica se tem nível de acesso requerido
		$nivel_acesso_requerido = 3;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdPendenciasRelatorios();

			$this->render('familiaAcompanhamento');				
		} else {
			$this->view->erroValidacao = 0;

			$this->render('fNPreIncluirNeces');
		}
	}	// Fim da function fNPreIncluirNeces

// ====================================================== //	
	
	public function fNIncluirNeces() {

		$this->validaAutenticacao();

		$this->view->erroValidacao = 0;

		// Valida se Grupo e subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fNPreIncluirNeces');

		} else {

			// Para todos poderem consultar, apenas deverão estar cadastrados em grupo/subgrupo (exceto nível acesso geral 1 e 2)
			$this->nivel_atuacao_requerido = 99;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fNPreIncluirNeces');				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fNPreIncluirNeces');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];
				
				// Buscar Familias
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);

				$familiasVnclGrupo = $familiasVnclGrupoBase->getConsultaFamiliasNeces();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$ha_todos_segmentos = 0;
					
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado
						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

						if ($_POST['cd_tip_evt_neces'] == 1) {
							$cd_tip_evt_neces = 'Próxima Visita';
						} else {
							$cd_tip_evt_neces = 'Eventual';							
						}

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'cd_est_situ_fml' => $cd_est_situ_fml,
								'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces']
						));

						// Para compor os dados do Grupo e Subgrupo acima da tabela
						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;
						$this->view->nomeEventoNeces = $cd_tip_evt_neces;
					}

					// Verifica se há famílias a terem relatório consultado
					if (count($this->view->familia) == 0) {
						$this->view->erroValidacao = 4;

						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;

						$this->render('fNPreIncluirNeces');
					} else {
						$this->render('fNIncluirNeces');	
					}
				
				} else {

					$this->view->erroValidacao = 3;

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fNPreIncluirNeces');
				}
			}	
		}

	}	// Fim da function fNIncluirNeces


// ====================================================== //	
	
	public function fNIncluirNecesProxVisitaMenu() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		if ($_POST['cd_tip_evt_neces'] == 1) {
			$cd_tip_evt_neces = 'Próxima Visita';
		} else {
			$cd_tip_evt_neces = 'Eventual';							
		}

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		// Data Atual
		$dt_atual = new \DateTime();
		$dt_atual = $dt_atual->format("Y-m-d");

		// Retroagem um mês na data atual
		$periodo = new \Dateinterval("P1M");
		$dt_atual_m1 = new \DateTime();
		$dt_atual_m1->sub($periodo);
		$dt_atual_m1 = $dt_atual_m1->format("Y-m-d");

		// Calcular a Próxima Data Visita com um mês a menos
		$this->obtemDataProximaVisita($dt_atual_m1, $_POST['cb_grupo_escolhido']);

		// Formata as datas para efeito de comparação de valor
		$ano_mes_da = str_replace('-','', $dt_atual);
		$ano_mes_pv = str_replace('-','', $this->prox_data_visita);

		// Se data da Próxima Visita calculada com data atual menos um mês é menor que a data atual, ou seja, já passou // a data, calcula com a data atual normal
		if ($ano_mes_da > $ano_mes_pv) {
			$this->obtemDataProximaVisita($dt_atual, $_POST['cb_grupo_escolhido']);			
		} 

		$dt_proxima_visita = Funcoes::formatarNumeros('data', $this->prox_data_visita, 10, "AMD");	

		$this->view->dadosNecessidade = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $_POST['nomeGrupo'], 
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
				'nm_sbgrp' => $_POST['nomeSubgrupo'], 
				'cd_fml' => $_POST['cb_familia_escolhida'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
				'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
				'dt_proxima_visita' => $dt_proxima_visita,
				'cd_setor_resp' => 1,
				'obs_sobre_item' => '',
				'dsc_item_neces' => '',
				'qtd_item_neces' => '1',
				'cd_tip_unid_item' => '2',
				'vlr_neces' => ''
		);

		$this->render('fNIncluirNecesProxVisitaMenu');

	}	// Fim da function fNIncluirNecesProxVisitaMenu

// ====================================================== //	
	
	public function fNIncluirNecesProxVisitaBase() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		if (!isset($_POST['vlr_neces'])) {
			$vlr_neces = '';
		} else {
			$vlr_neces = $_POST['vlr_neces'];
		}

		if ($_POST['cb_item_escolhido'] == 'Escolha Item' || $_POST['cb_subitem_escolhido'] == 'Escolha Subitem') {
			$this->view->erroValidacao = 2;		

			$this->view->dadosNecessidade = array (
					'cd_grp' => $_POST['cb_grupo_escolhido'], 
					'nm_grp' => $_POST['nm_grp'], 
					'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
					'nm_sbgrp' => $_POST['nm_sbgrp'], 
					'cd_fml' => $_POST['cb_familia_escolhida'], 
					'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
					'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
					'dt_proxima_visita' => $_POST['dt_proxima_visita'], 
					'cd_setor_resp' => $_POST['cd_setor_resp'], 
					'obs_sobre_item' => $_POST['obs_sobre_item'],
					'dsc_item_neces' => $_POST['dsc_item_neces'],
					'qtd_item_neces' => $_POST['qtd_item_neces'],
					'cd_tip_unid_item' => $_POST['cd_tip_unid_item'],
					'vlr_neces' => ''
			);

			$this->render('fNIncluirNecesProxVisitaMenu');

		} else {

			// Modifica a data da Próxima Visita que está DD/MM/AAAA para AAAA-MM-DD, pois é neste formato que está a pesquisa
			$data_pv = substr($_POST['dt_proxima_visita'], 6, 4).'-'.substr($_POST['dt_proxima_visita'], 3, 2).'-'.substr($_POST['dt_proxima_visita'], 0, 2);
			
			// Estão vindo (cod_item;nome_item) e (cod_subitem;nome_subitem)
			$itemNome = explode(';', $_POST['cb_item_escolhido']);
			$item = $itemNome[0];
			$nomeItem = $itemNome[1];

			$subitemNome = explode(';', $_POST['cb_subitem_escolhido']);
			$subitem = $subitemNome[0];
			$nomeSubitem = $subitemNome[1];

			if ($item >= 5 && $item <= 7) {
				if (empty($_POST['dsc_item_neces'])) {
					$this->view->erroValidacao = 6;		

					$this->view->dadosNecessidade = array (
							'cd_grp' => $_POST['cb_grupo_escolhido'], 
							'nm_grp' => $_POST['nm_grp'], 
							'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
							'nm_sbgrp' => $_POST['nm_sbgrp'], 
							'cd_fml' => $_POST['cb_familia_escolhida'], 
							'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
							'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
							'dt_proxima_visita' => $_POST['dt_proxima_visita'], 
							'cd_setor_resp' => $_POST['cd_setor_resp'], 
							'obs_sobre_item' => $_POST['obs_sobre_item'],
							'dsc_item_neces' => $_POST['dsc_item_neces'],
							'qtd_item_neces' => $_POST['qtd_item_neces'],
							'cd_tip_unid_item' => $_POST['cd_tip_unid_item'],
							'vlr_neces' => ''
					);

					$this->view->codItem = $item;
					$this->view->nomeItem = $nomeItem;
					$this->view->codSubitem = $subitem;
					$this->view->nomeSubitem = $nomeSubitem;

					$this->render('fNIncluirNecesProxVisitaMenu');

					exit;				
				}
			}

			// Verificar se Item e Subitem já estão cadastrados, pois podem ter sido cadastrados revisão
			$qtdItemSubitem = Container::getModel('TbItemNecesFml');
			$qtdItemSubitem->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$qtdItemSubitem->__set('cd_item', $item);
			$qtdItemSubitem->__set('cd_sbitem', $subitem);
			$qtdItemSubitem->__set('dt_prev_disponib_item', $data_pv);
			$qtdItemSubitemBase = $qtdItemSubitem->getQtdItemSubitemNecessidade();

			if ($qtdItemSubitemBase['qtde'] == 0) {
				// Insere registros
				$insereItemSubitemNeces = Container::getModel('TbItemNecesFml');
				$insereItemSubitemNeces->__set('cd_fml', $_POST['cb_familia_escolhida']);
				$insereItemSubitemNeces->__set('cd_item', $item);
				$insereItemSubitemNeces->__set('cd_sbitem', $subitem);
				$insereItemSubitemNeces->__set('cd_setor_resp', $_POST['cd_setor_resp']);
				$insereItemSubitemNeces->__set('obs_sobre_item', $_POST['obs_sobre_item']);
				$insereItemSubitemNeces->__set('dt_prev_disponib_item', $data_pv);
				$insereItemSubitemNeces->__set('cd_disponib_item', 1);
				$insereItemSubitemNeces->__set('dt_disponib_item_entrega', $data_pv);
				$insereItemSubitemNeces->__set('dsc_item_neces', $_POST['dsc_item_neces']);
				$insereItemSubitemNeces->__set('qtd_item_neces', $_POST['qtd_item_neces']);
				$insereItemSubitemNeces->__set('vlr_neces', $vlr_neces);
				$insereItemSubitemNeces->__set('cd_situ_item_solicitado', 1);
				$insereItemSubitemNeces->__set('cd_tip_unid_item', $_POST['cd_tip_unid_item']);
				$insereItemSubitemNeces->__set('cd_vlnt_resp_cadas', $_SESSION['id']);
				$insereItemSubitemNeces->__set('cd_tip_evt_neces', $_POST['cd_tip_evt_neces']);
				$insereItemSubitemNeces->insertItemSubitemNeces();

				$this->view->erroValidacao = 1;		

				$this->view->dadosNecessidade = array (
						'cd_grp' => $_POST['cb_grupo_escolhido'], 
						'nm_grp' => $_POST['nm_grp'], 
						'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
						'nm_sbgrp' => $_POST['nm_sbgrp'], 
						'cd_fml' => $_POST['cb_familia_escolhida'], 
						'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
						'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
						'dt_proxima_visita' => $_POST['dt_proxima_visita'], 
						'cd_setor_resp' => 1, 
						'obs_sobre_item' => '',
						'dsc_item_neces' => '',
						'qtd_item_neces' => '1',
						'cd_tip_unid_item' => '2',
						'vlr_neces' => ''
				);

			} else {

				$this->view->erroValidacao = 3;		

				$this->view->dadosNecessidade = array (
						'cd_grp' => $_POST['cb_grupo_escolhido'], 
						'nm_grp' => $_POST['nm_grp'], 
						'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
						'nm_sbgrp' => $_POST['nm_sbgrp'], 
						'cd_fml' => $_POST['cb_familia_escolhida'], 
						'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
						'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
						'dt_proxima_visita' => $_POST['dt_proxima_visita'], 
						'cd_setor_resp' => $_POST['cd_setor_resp'], 
						'obs_sobre_item' => $_POST['obs_sobre_item'],
						'dsc_item_neces' => $_POST['dsc_item_neces'],
						'qtd_item_neces' => $_POST['qtd_item_neces'],
						'cd_tip_unid_item' => $_POST['cd_tip_unid_item'],
						'vlr_neces' => $vlr_neces
				);
			}

			$this->view->codItem = $item;
			$this->view->nomeItem = $nomeItem;
			$this->view->codSubitem = $subitem;
			$this->view->nomeSubitem = $nomeSubitem;

			$this->render('fNIncluirNecesProxVisitaMenu');

		}

	}	// Fim da function fNIncluirNecesProxVisitaBase

// ====================================================== //	
	
	public function fNIncluirNecesEventualMenu() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		if ($_POST['cd_tip_evt_neces'] == 1) {
			$cd_tip_evt_neces = 'Próxima Visita';
		} else {
			$cd_tip_evt_neces = 'Eventual';							
		}

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		// Data Atual
		$dt_atual = new \DateTime();
		$dt_atual = $dt_atual->format("Y-m-d");

		// Avança uma semana na data atual
		$periodo = new \Dateinterval("P7D");
		$dt_atual_d7 = new \DateTime();
		$dt_atual_d7->add($periodo);
		$dt_atual_d7 = $dt_atual_d7->format("Y-m-d");

		$dt_atual = Funcoes::formatarNumeros('data', $dt_atual_d7, 10, "AMD");	

		$this->view->dadosNecessidade = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $_POST['nomeGrupo'], 
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
				'nm_sbgrp' => $_POST['nomeSubgrupo'], 
				'cd_fml' => $_POST['cb_familia_escolhida'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
				'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
				'dt_prev_disponib_item' => $dt_atual,
				'cd_setor_resp' => '1',
				'obs_sobre_item' => '',
				'dsc_item_neces' => '',
				'cd_tip_sexo' => '0',
				'cd_estrut_corporal' => '0',
				'cd_tam_corporal' => '0',
				'nr_corporal' => '',
				'idade_aparente_pessoa' => '',
				'cd_tempo_idade' => '0',
				'cd_tip_clas_item' => '0',
				'qtd_item_neces' => '1',
				'cd_tip_unid_item' => '2',
				'vlr_neces' => ''
		);

		$this->render('fNIncluirNecesEventualMenu');

	}	// Fim da function fNIncluirNecesEventualMenu

// ====================================================== //	
	
	public function fNIncluirNecesEventualBase() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		/*
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		*/

		$trata_inclusao = 1;

		if (!isset($_POST['vlr_neces'])) {
			$this->view->vlr_neces = '';
		} else {
			$this->view->vlr_neces = $_POST['vlr_neces'];
		}

		if (!isset($_POST['cd_tip_clas_item'])) {
			$this->view->cd_tip_clas_item = 0;
		} else {
			$this->view->cd_tip_clas_item = $_POST['cd_tip_clas_item'];
		}

		if ($_POST['cb_item_escolhido'] == 'Escolha Item' || $_POST['cb_subitem_escolhido'] == 'Escolha Subitem') {
			$trata_inclusao = 0;
			
			$this->view->erroValidacao = 2;	

			$this->view->item = '';
			$this->view->nomeItem = '';

			$this->view->subitem = '';
			$this->view->nomeSubitem = '';

			$this->retornoErroInclusaoNeces();	
		} 

		// Estão vindo (cod_item;nome_item) e (cod_subitem;nome_subitem)
		$itemNome = explode(';', $_POST['cb_item_escolhido']);
		$this->view->item = $itemNome[0];
		$this->view->nomeItem = $itemNome[1];

		$subitemNome = explode(';', $_POST['cb_subitem_escolhido']);
		$this->view->subitem = $subitemNome[0];
		$this->view->nomeSubitem = $subitemNome[1];

		// Data Atual
		$dt_atual = new \DateTime();
		$dt_atual = $dt_atual->format("Y-m-d");
		$dt_atual = str_replace('-','', $dt_atual);

		// Modifica a data que está DD/MM/AAAA para AAAAMMDD
		$dt_disp_item = substr($_POST['dt_prev_disponib_item'], 6, 4).substr($_POST['dt_prev_disponib_item'], 3, 2).substr($_POST['dt_prev_disponib_item'], 0, 2);

		// Verifica se data de disponibilização do item é menor que a data atual
		if ($dt_disp_item < $dt_atual) {
			$trata_inclusao = 0;
			
			$this->view->erroValidacao = 4;		

			$this->retornoErroInclusaoNeces();				
		}

		// Verifica se para Recursos Financeiros o valor está maior que zero
		if ($this->view->item == 8) {
			if ($this->view->vlr_neces == 0 || empty($this->view->vlr_neces)) {
				$trata_inclusao = 0;
				
				$this->view->erroValidacao = 5;	

				$this->retornoErroInclusaoNeces();				
			}

			// Verifica se a descrição está preenchida
			if (empty($_POST['dsc_item_neces'])) {
				$trata_inclusao = 0;
				
				$this->view->erroValidacao = 6;		

				$this->retornoErroInclusaoNeces();								
			}
		} 

		if (($this->view->item >= 18) || ($this->view->item >= 12 && $this->view->item <= 15)) {
			if (empty($_POST['dsc_item_neces'])) {
				$trata_inclusao = 0;
				
				$this->view->erroValidacao = 6;		

				$this->retornoErroInclusaoNeces();								
			}
		}

		if ($trata_inclusao == 1) {
			// Modifica a data Prevista que está DD/MM/AAAA para AAAA-MM-DD, pois é neste formato que está a pesquisa
			$data_pv = substr($_POST['dt_prev_disponib_item'], 6, 4).'-'.substr($_POST['dt_prev_disponib_item'], 3, 2).'-'.substr($_POST['dt_prev_disponib_item'], 0, 2);

			// Verificar se Item e Subitem já estão cadastrados, pois podem ter sido cadastrados revisão
			$qtdItemSubitem = Container::getModel('TbItemNecesFml');
			$qtdItemSubitem->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$qtdItemSubitem->__set('cd_item', $this->view->item);
			$qtdItemSubitem->__set('cd_sbitem', $this->view->subitem);
			$qtdItemSubitem->__set('dt_prev_disponib_item', $data_pv);
			$qtdItemSubitemBase = $qtdItemSubitem->getQtdItemSubitemNecessidade();

			if ($qtdItemSubitemBase['qtde'] == 0) {
				// Insere registros
				$insereItemSubitemNeces = Container::getModel('TbItemNecesFml');
				$insereItemSubitemNeces->__set('cd_fml', $_POST['cb_familia_escolhida']);
				$insereItemSubitemNeces->__set('cd_item', $this->view->item);
				$insereItemSubitemNeces->__set('cd_sbitem', $this->view->subitem);
				$insereItemSubitemNeces->__set('cd_setor_resp', $_POST['cd_setor_resp']);
				$insereItemSubitemNeces->__set('obs_sobre_item', $_POST['obs_sobre_item']);
				$insereItemSubitemNeces->__set('dt_prev_disponib_item', $data_pv);
				$insereItemSubitemNeces->__set('cd_disponib_item', 1);
				$insereItemSubitemNeces->__set('dt_disponib_item_entrega', $data_pv);
				$insereItemSubitemNeces->__set('dsc_item_neces', $_POST['dsc_item_neces']);
				$insereItemSubitemNeces->__set('qtd_item_neces', $_POST['qtd_item_neces']);
				$insereItemSubitemNeces->__set('vlr_neces', $this->view->vlr_neces);
				$insereItemSubitemNeces->__set('cd_situ_item_solicitado', 1);
				$insereItemSubitemNeces->__set('cd_tip_unid_item', $_POST['cd_tip_unid_item']);
				$insereItemSubitemNeces->__set('cd_tip_sexo', $_POST['cd_tip_sexo']);
				$insereItemSubitemNeces->__set('cd_estrut_corporal', $_POST['cd_estrut_corporal']);
				$insereItemSubitemNeces->__set('cd_tam_corporal', $_POST['cd_tam_corporal']);
				$insereItemSubitemNeces->__set('nr_corporal', $_POST['nr_corporal']);
				$insereItemSubitemNeces->__set('idade_aparente_pessoa', $_POST['idade_aparente_pessoa']);
				$insereItemSubitemNeces->__set('cd_tempo_idade', $_POST['cd_tempo_idade']);
				$insereItemSubitemNeces->__set('cd_tip_clas_item', $this->view->cd_tip_clas_item);	
				$insereItemSubitemNeces->__set('cd_vlnt_resp_cadas', $_SESSION['id']);
				$insereItemSubitemNeces->__set('cd_tip_evt_neces', $_POST['cd_tip_evt_neces']);
				$insereItemSubitemNeces->insertItemSubitemNeces();

				$this->view->erroValidacao = 1;		

				if ($_POST['cd_tip_evt_neces'] == 1) {
					$cd_tip_evt_neces = 'Próxima Visita';
				} else {
					$cd_tip_evt_neces = 'Eventual';							
				}

				// Buscar dados Família
				$dadosFamilia = Container::getModel('TbFml');
				$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
				$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

				// Data Atual
				$dt_atual = new \DateTime();
				$dt_atual = $dt_atual->format("Y-m-d");

				// Avança uma semana na data atual
				$periodo = new \Dateinterval("P7D");
				$dt_atual_d7 = new \DateTime();
				$dt_atual_d7->add($periodo);
				$dt_atual_d7 = $dt_atual_d7->format("Y-m-d");

				$dt_atual = Funcoes::formatarNumeros('data', $dt_atual_d7, 10, "AMD");	

				$this->view->dadosNecessidade = array (
						'cd_grp' => $_POST['cb_grupo_escolhido'], 
						'nm_grp' => $_POST['nm_grp'], 
						'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
						'nm_sbgrp' => $_POST['nm_sbgrp'], 
						'cd_fml' => $_POST['cb_familia_escolhida'], 
						'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
						'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
						'dt_prev_disponib_item' => $dt_atual,
						'cd_setor_resp' => '1',
						'obs_sobre_item' => '',
						'dsc_item_neces' => '',
						'cd_tip_sexo' => '0',
						'cd_estrut_corporal' => '0',
						'cd_tam_corporal' => '0',
						'nr_corporal' => '',						
						'idade_aparente_pessoa' => '',
						'cd_tempo_idade' => '0',
						'cd_tip_clas_item' => '0',
						'qtd_item_neces' => '1',
						'cd_tip_unid_item' => '2',
						'vlr_neces' => ''
				);

				$this->view->codItem = $this->view->item;
				$this->view->codSubitem = $this->view->subitem;

				$this->render('fNIncluirNecesEventualMenu');

			} else {

				$this->view->erroValidacao = 3;		

				$this->retornoErroInclusaoNeces();		
			}										
		}
	}	// Fim da function fNIncluirNecesEventualBase


// ====================================================== //	
	
	public function retornoErroInclusaoNeces() {
			
		$this->view->dadosNecessidade = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $_POST['nm_grp'], 
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
				'nm_sbgrp' => $_POST['nm_sbgrp'], 
				'cd_fml' => $_POST['cb_familia_escolhida'], 
				'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
				'cd_tip_evt_neces' => $_POST['cd_tip_evt_neces'], 
				'dt_prev_disponib_item' => $_POST['dt_prev_disponib_item'], 
				'cd_setor_resp' => $_POST['cd_setor_resp'], 
				'obs_sobre_item' => $_POST['obs_sobre_item'], 
				'dsc_item_neces' => $_POST['dsc_item_neces'], 
				'cd_tip_sexo' => $_POST['cd_tip_sexo'], 
				'cd_estrut_corporal' => $_POST['cd_estrut_corporal'], 
				'cd_tam_corporal' => $_POST['cd_tam_corporal'], 
				'nr_corporal' => $_POST['nr_corporal'], 
				'idade_aparente_pessoa' => $_POST['idade_aparente_pessoa'], 
				'cd_tempo_idade' => $_POST['cd_tempo_idade'], 
				'cd_tip_clas_item' => $this->view->cd_tip_clas_item, 
				'qtd_item_neces' => $_POST['qtd_item_neces'], 
				'cd_tip_unid_item' => $_POST['cd_tip_unid_item'], 
				'vlr_neces' => $this->view->vlr_neces
		);

		$this->view->codItem = $this->view->item;
		$this->view->codSubitem = $this->view->subitem;

		$this->render('fNIncluirNecesEventualMenu');

		exit;

	}	// Fim da function retornoErroInclusaoNeces

}	//	Fim da classe

?>
				