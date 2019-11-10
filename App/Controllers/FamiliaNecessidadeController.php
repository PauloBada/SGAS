<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 28/09/2019
   Objetivo:  Controller para opções do menu Necessidades de Famílias do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

//use Mpdf\Mpdf;
use TCPDF;

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
	
	public function validaAcessoAcompanhamento_2() {

		$this->retornoValidaAcessoAcompanhamento = 0;

		$nao_esta_grupo_subgrupo = 0;
		$ha_subgrupo = 0;

		if ($_POST['cb_subgrupo_escolhido'] != 'Escolha Subgrupo') {
			$ha_subgrupo = 1;

			$atuacaoVoluntarioBase = Container::getModel('TbVnclVlntGrp');
			$atuacaoVoluntarioBase->__set('codVoluntario', $_SESSION['id']);
			$atuacaoVoluntarioBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
			$atuacaoVoluntarioBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
			$atuacaoVoluntario = $atuacaoVoluntarioBase->getNivelAtuacao();

			// Não está na tabela tb_vncl_vlnt_grp, ou seja, não está atrelado ao grupo/subgrupo
			if (empty($atuacaoVoluntario['cod_atuacao'])) { 
				$nao_esta_grupo_subgrupo = 1;
			}

		} else {
			$atuacaoVoluntarioBase = Container::getModel('TbVnclVlntGrp');
			$atuacaoVoluntarioBase->__set('codVoluntario', $_SESSION['id']);
			$atuacaoVoluntarioBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
			$atuacaoVoluntario = $atuacaoVoluntarioBase->getNivelAtuacaoGrupo();
		
			if ($atuacaoVoluntario['qtde'] == 0) { 
				$nao_esta_grupo_subgrupo = 1;
			}
		}

		// Não está na tabela tb_vncl_vlnt_grp, ou seja, não está atrelado ao grupo/subgrupo
		if ($nao_esta_grupo_subgrupo == 1) { 
			// Para possibilitar quem tem nível 1 e 2 consultar relatórios sem estar atrelado a grupo/subgrupo
			$nivel_acesso_requerido = 2;
			$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

			// Para validar se Voluntário tem o nível adequado para fazer a operação
			if ($autenticar_acesso['autorizado'] == 0) {
				$this->view->erroValidacao = 5;

				if ($ha_subgrupo == 1) {
					// Buscar Nome de Grupo e Subgrupo
					$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
					$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
					$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
					$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

					$this->view->grupoTratado = $dadosGS['nome_grupo'];
					$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];
				} else {
					// Buscar Nome de Grupo
					$dadosGrupo = Container::getModel('TbGrp');
					$dadosGrupo->__set('cd_grp', $_POST['cb_grupo_escolhido']);
					$dadosG = $dadosGrupo->getDadosGrupo();

					$this->view->grupoTratado = $dadosG['nome_grupo'];
					$this->view->subgrupoTratado = '';						
				}

				$this->retornoValidaAcessoAcompanhamento = 1;
			}
		} 

	}	// Fim da function validaAcessoAcompanhamento_2


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

			$this->render('familiaNecessidade');				

		} else {
			$this->view->erroValidacao = 0;

			$this->view->codVoluntario = $_SESSION['id'];		

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

			$this->view->codVoluntario = $_SESSION['id'];				

			$this->render('fNPreIncluirNeces');

		} else {

			// Para todos poderem consultar, apenas deverão estar cadastrados em grupo/subgrupo (exceto nível acesso geral 1 e 2)
			$this->nivel_atuacao_requerido = 99;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fNPreIncluirNeces');				

				$this->view->codVoluntario = $_SESSION['id'];				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fNPreIncluirNeces');				

				$this->view->codVoluntario = $_SESSION['id'];				
			
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

						$this->view->codVoluntario = $_SESSION['id'];				

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

					$this->view->codVoluntario = $_SESSION['id'];									

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

		// Se data da Próxima Visita calculada com data atual menos um mês é menor que a data atual, ou seja, já passou a data, calcula com a data atual normal
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
				
				//$insereItemSubitemNeces->__set('dt_disponib_item_entrega', $data_pv);
				$insereItemSubitemNeces->__set('dt_disponib_item_entrega', '');
				
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

// ====================================================== //	

	public function fNPreAlterarNeces() {
		
		$this->validaAutenticacao();		

		// Verifica se tem nível de acesso requerido
		$nivel_acesso_requerido = 3;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('familiaNecessidade');				

		} else {
			$this->view->erroValidacao = 0;

			$this->view->codVoluntario = $_SESSION['id'];

			$this->render('fNPreAlterarNeces');
		}
		
	}	// Fim da function fNPreAlterarNeces

// ====================================================== //	
	
	public function geraTabelaNecessidades() {

		$this->validaAutenticacao();		

		$this->view->ha_grupo = 0;
		$this->view->ha_subgrupo = 0;
		$this->view->ha_familia = 0;
		$this->view->ha_setor_responsavel = 0;
		$this->view->ha_item = 0;
		$this->view->ha_sbitem = 0;
		$opcao = 0;

		if ($_POST['cb_grupo_escolhido'] != 'Escolha Grupo') {
			$this->view->ha_grupo = 1;
		}

		if ($_POST['cb_subgrupo_escolhido'] != 'Escolha Subgrupo') {
			$this->view->ha_subgrupo = 1;
		}

		if ($_POST['cb_familia_escolhida'] != 'Escolha Família') {
			$this->view->ha_familia = 1;
		}

		if ($_POST['cd_setor_resp'] != '0') {
			$this->view->ha_setor_responsavel = 1;
		}

		if ($_POST['cb_item_escolhido'] != 'Escolha Item') {
			$this->view->ha_item = 1;
		}

		if ($_POST['cb_subitem_escolhido'] != 'Escolha Subitem') {
			$this->view->ha_sbitem = 1;
		}

		if ($_POST['cb_situ_item_solicitado'] == '1') {
			$cd_situ_item_solicitado = 1;
			$cd_disponib_item = 1;
			$this->view->situacaoItem = 'Pendente';
		
		} else if ($_POST['cb_situ_item_solicitado'] == '2') {
			$cd_situ_item_solicitado = 2;
			$cd_disponib_item = 2;
			$this->view->situacaoItem = 'Atendido';		

		} else {
			$cd_situ_item_solicitado = 3;
			$cd_disponib_item = 3;
			$this->view->situacaoItem = 'Cancelado/Indisponível';
		}

		if ($this->view->ha_subgrupo == 1) {
			// Buscar Nome de Grupo e Subgrupo
			$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
			$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

			$nomeGrupo = $dadosGS['nome_grupo'];
			$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		} else {
			// Buscar Nome de Grupo
			$dadosGrupo = Container::getModel('TbGrp');
			$dadosGrupo->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosG = $dadosGrupo->getDadosGrupo();

			$nomeGrupo = $dadosG['nome_grupo'];
			$nomeSubgrupo = '';
		}

		// Buscar Dados de acordo com a opcao
		$dadosPesquisa = Container::getModel('TbItemNecesFml');
		$dadosPesquisa->__set('cd_situ_item_solicitado', $cd_situ_item_solicitado);
		$dadosPesquisa->__set('cd_disponib_item', $cd_disponib_item);

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 0 && $this->view->ha_sbitem == 0) {
			$opcao = 1;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);		
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_01();

		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 0 && $this->view->ha_sbitem == 0) {
			$opcao = 2;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_02();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 1 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 0 && $this->view->ha_sbitem == 0) {
			$opcao = 3;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_03();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 1 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 0 && $this->view->ha_sbitem == 0) {
			$opcao = 4;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_04();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 1 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 0) {
			$opcao = 5;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_05();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 1 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 1) {
			$opcao = 6;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisa->__set('cd_sbitem', $_POST['cb_subitem_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_06();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 0 && $this->view->ha_sbitem == 0) {
			$opcao = 7;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_07();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 0) {
			$opcao = 8;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);		
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_08();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 1) {
			$opcao = 9;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisa->__set('cd_sbitem', $_POST['cb_subitem_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_09();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 0) {
			$opcao = 10;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_10();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 1) {
			$opcao = 11;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisa->__set('cd_sbitem', $_POST['cb_subitem_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_11();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 1 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 0) {
			$opcao = 12;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);		
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_12();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 1 && $this->view->ha_familia == 1 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 1) {
			$opcao = 13;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadosPesquisa->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisa->__set('cd_sbitem', $_POST['cb_subitem_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_13();			
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 0 && $this->view->ha_sbitem == 0) {
			$opcao = 14;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);		
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_14();		
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 0) {
			$opcao = 15;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_15();		
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 1 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 1) {
			$opcao = 16;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_setor_resp', $_POST['cd_setor_resp']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisa->__set('cd_sbitem', $_POST['cb_subitem_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_16();		
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 0) {
			$opcao = 17;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_17();		
		}

		if ($this->view->ha_grupo == 1 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 1) {
			$opcao = 18;

			$dadosPesquisa->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisa->__set('cd_sbitem', $_POST['cb_subitem_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_18();		
		}

		if ($this->view->ha_grupo == 0 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 0) {
			$opcao = 19;

			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_19();		
		}

		if ($this->view->ha_grupo == 0 && $this->view->ha_subgrupo == 0 && $this->view->ha_familia == 0 && $this->view->ha_setor_responsavel == 0 && $this->view->ha_item == 1 && $this->view->ha_sbitem == 1) {
			$opcao = 20;

			$dadosPesquisa->__set('cd_item', $_POST['cb_item_escolhido']);
			$dadosPesquisa->__set('cd_sbitem', $_POST['cb_subitem_escolhido']);
			$dadosPesquisaBase = $dadosPesquisa->getDadosPesquisaOpcao_20();		
		}

		if ($opcao == 0) {
			$this->view->erroValidacao = 6;

			$this->view->codVoluntario = $_SESSION['id'];				

			$this->render('fNPreAlterarNeces');

			exit;
		}
                                                 
		if (count($dadosPesquisaBase) > 0) {
			$this->view->dadosPesquisa = array ();

			foreach ($dadosPesquisaBase as $index => $arr) {
				// Buscar dados Família
				$dadosFamilia = Container::getModel('TbFml');
				$dadosFamilia->__set('codFamilia', $arr['cd_fmlID']);
				$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

				// Família com atendimento já encerrado
				if ($dadosFamiliaBase['cd_est_situ_fml'] == 4) {
					$fml_atend_encerrado = 1;
				} else {
					$fml_atend_encerrado = 0;
				}

				// Para saber se a data atual é maior que a data prevista para disponibilizar o item
				$data_hoje 		= new \DateTime();
				$data_hoje 		= $data_hoje->format("Ymd");

				$data_disponib_ano = substr($arr['dt_prev_disponib_item'], 6, 4);
				$data_disponib_mes = substr($arr['dt_prev_disponib_item'], 3, 2);
				$data_disponib_dia = substr($arr['dt_prev_disponib_item'], 0, 2);
				$data_disponib = $data_disponib_ano.$data_disponib_mes.$data_disponib_dia;

				//if (strtotime($data_hoje) >= strtotime($data_disponib)) {
				if ($data_hoje >= $data_disponib) {
					$sinaliza_data = 1;

				} else {
					$sinaliza_data = 0;
				}

				if ($opcao <= 18) {
					array_push($this->view->dadosPesquisa, array (
							'cd_grp' => $_POST['cb_grupo_escolhido'], 
							'nm_grp' => $nomeGrupo, 
							'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
							'nm_sbgrp' => $arr['nm_sbgrp'],					
							'cd_fml' => $arr['cd_fmlID'],
							'cd_item' => $arr['cd_itemID'],
							'cd_sbitem' => $arr['cd_sbitemID'],
							'seql_item_neces' => $arr['seql_item_necesID'],
							'cd_setor_resp' => $arr['cd_setor_resp'],
							'nm_setor_resp' => $arr['nm_setor_resp'],
							'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
							'nm_item' => $arr['nm_item'],
							'nm_sbitem' => $arr['nm_sbitem'],
							'nm_sbgrp' => $arr['nm_sbgrp'],
							'dt_prev_disponib_item' => $arr['dt_prev_disponib_item'],
							'cd_tip_evt_neces' => $arr['cd_tip_evt_neces'],
							'nm_tip_evt_neces' => $arr['nm_tip_evt_neces'],

							// Estas informações estarão na linha escolhida no programa "fNAlterarNeces"
							'post' => $arr['cd_fmlID'].';'.$arr['cd_itemID'].';'.$arr['cd_sbitemID'].';'.$arr['seql_item_necesID'].';'.$arr['nm_sbgrp'].';'.$arr['nm_grp_fmlr'].';'.$arr['cd_sbgrp'].';'.$arr['cd_tip_evt_neces'].';'.$fml_atend_encerrado,

							// Estes estarão em todas as linhas do programa "fNAlterarNeces"
							'cb_grupo_escolhido' => $_POST['cb_grupo_escolhido'],
							'cb_subgrupo_escolhido' => $_POST['cb_subgrupo_escolhido'],
							'cb_familia_escolhida' => $_POST['cb_familia_escolhida'],
							'cd_setor_resp' => $_POST['cd_setor_resp'],
							'cb_item_escolhido' => $_POST['cb_item_escolhido'],
							'cb_subitem_escolhido' => $_POST['cb_subitem_escolhido'],
							'cb_situ_item_solicitado' => $_POST['cb_situ_item_solicitado'],
							'sinaliza_data' => $sinaliza_data,
							'cd_est_situ_fml' => $arr['cd_est_situ_fml'],
							'nm_est_situ_fml' => $arr['nm_est_situ_fml']
					));

					// Para compor os dados do Grupo e Subgrupo acima da tabela
					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
				
				} else {

					array_push($this->view->dadosPesquisa, array (
							'cd_grp' =>  $arr['cd_grp'],					
							'nm_grp' => $arr['nm_grp'],					
							'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
							'nm_sbgrp' => $arr['nm_sbgrp'],					
							'cd_fml' => $arr['cd_fmlID'],
							'cd_item' => $arr['cd_itemID'],
							'cd_sbitem' => $arr['cd_sbitemID'],
							'seql_item_neces' => $arr['seql_item_necesID'],
							'cd_setor_resp' => $arr['cd_setor_resp'],
							'nm_setor_resp' => $arr['nm_setor_resp'],
							'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
							'nm_item' => $arr['nm_item'],
							'nm_sbitem' => $arr['nm_sbitem'],
							'nm_sbgrp' => $arr['nm_sbgrp'],
							'dt_prev_disponib_item' => $arr['dt_prev_disponib_item'],
							'cd_tip_evt_neces' => $arr['cd_tip_evt_neces'],
							'nm_tip_evt_neces' => $arr['nm_tip_evt_neces'],

							// Estas informações estarão na linha escolhida no programa "fNAlterarNeces"
							'post' => $arr['cd_fmlID'].';'.$arr['cd_itemID'].';'.$arr['cd_sbitemID'].';'.$arr['seql_item_necesID'].';'.$arr['nm_sbgrp'].';'.$arr['nm_grp_fmlr'].';'.$arr['cd_sbgrp'].';'.$arr['cd_tip_evt_neces'].';'.$fml_atend_encerrado,

							// Estes estarão em todas as linhas do programa "fNAlterarNeces"
							'cb_grupo_escolhido' => $_POST['cb_grupo_escolhido'],
							'cb_subgrupo_escolhido' => $_POST['cb_subgrupo_escolhido'],
							'cb_familia_escolhida' => $_POST['cb_familia_escolhida'],
							'cd_setor_resp' => $_POST['cd_setor_resp'],
							'cb_item_escolhido' => $_POST['cb_item_escolhido'],
							'cb_subitem_escolhido' => $_POST['cb_subitem_escolhido'],
							'cb_situ_item_solicitado' => $_POST['cb_situ_item_solicitado'],
							'sinaliza_data' => $sinaliza_data,
							'cd_est_situ_fml' => $arr['cd_est_situ_fml'],
							'nm_est_situ_fml' => $arr['nm_est_situ_fml']
					));

					// Para compor os dados do Grupo e Subgrupo acima da tabela
					$this->view->codGrupo = '';
					$this->view->nomeGrupo = '';					
				}
			}

			$this->temRegistros = 1;

		} else {
			$this->temRegistros = 0;			
		}

	}	// Fim da function geraTabelaNecessidades

// ====================================================== //	
	
	public function fNAlterarNeces() {

		$this->validaAutenticacao();

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" ) {
			if ($_POST['cb_subgrupo_escolhido'] == 'Escolha Subgrupo' && 
				$_POST['cb_familia_escolhida'] == 'Escolha Família' && 
				$_POST['cd_setor_resp'] == '0'  && 
				$_POST['cb_item_escolhido'] != 'Escolha Item') {

				$this->view->ha_grupo = 0;

			} else {
				$this->view->erroValidacao = 2;

				$this->view->codVoluntario = $_SESSION['id'];				

				$this->render('fNPreAlterarNeces');

				exit;
			}
		} 
	
		// Para todos poderem consultar, apenas deverão estar cadastrados em grupo/subgrupo (exceto nível acesso geral 1 e 2)
		$this->nivel_atuacao_requerido = 99;
		
		$this->validaAcessoAcompanhamento_2();

		// Não está na tabela de vinculo de grupo e subgrupo
		if ($this->retornoValidaAcessoAcompanhamento == 1) {
			
			$this->view->codVoluntario = $_SESSION['id'];				

			$this->render('fNPreAlterarNeces');				

			exit;
		}

		session_write_close();		
		$this->geraTabelaNecessidades();

		if ($this->temRegistros == 1) {
	
			$this->render('fNAlterarNeces');				

		} else {

			$this->view->erroValidacao = 3;

			$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
			$this->view->nomeGrupo = $nomeGrupo;

			$this->view->codVoluntario = $_SESSION['id'];									

			$this->render('fNPreAlterarNeces');
		}

	}	// Fim da function fNAlterarNeces


// ====================================================== //	
	
	public function fNAlterarNecesProxVisitaMenu() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		// Buscar Dados para alteração
		$dadosNecesFml = Container::getModel('TbItemNecesFml');
		$dadosNecesFml->__set('cd_fml', $_POST['cd_fml']);
		$dadosNecesFml->__set('cd_item', $_POST['cd_item']);
		$dadosNecesFml->__set('cd_sbitem', $_POST['cd_sbitem']);
		$dadosNecesFml->__set('seql_item_neces', $_POST['seql_item_neces']);
		$dadosNecesFmlBase = $dadosNecesFml->getDadosNecesEspecifica();

		$dt_proxima_visita = Funcoes::formatarNumeros('data', $dadosNecesFmlBase['dt_prev_disponib_item'], 10, "AMD");	
		$dt_incl_item_neces = Funcoes::formatarNumeros('data', $dadosNecesFmlBase['dt_incl_item_neces'], 10, "AMD");	
		$dt_disponib_item_entrega = Funcoes::formatarNumeros('data', $dadosNecesFmlBase['dt_disponib_item_entrega'], 10, "AMD");	

		// Buscar Nome dos Voluntários cadastrador e disponibilizador do item
		$nomeVlntCadas = Container::getModel('TbVlnt');
		$nomeVlntCadas->__set('id', $dadosNecesFmlBase['cd_vlnt_resp_cadas']);
		$nomeVlntCadasBase = $nomeVlntCadas->getInfoVoluntario();
		$nomeVlntCadasBase = $nomeVlntCadasBase['nm_vlnt'];

		if ($dadosNecesFmlBase['cd_vlnt_resp_disponib'] === null) {
			$nomeVlntDisponibBase = '';
		
		} else {
			$nomeVlntDisponib = Container::getModel('TbVlnt');
			$nomeVlntDisponib->__set('id', $dadosNecesFmlBase['cd_vlnt_resp_disponib']);
			$nomeVlntDisponibBase = $nomeVlntDisponib->getInfoVoluntario();
			$nomeVlntDisponibBase = $nomeVlntDisponibBase['nm_vlnt'];
		}

		// Buscar nome Item
		$nomeItem = Container::getModel('TbItemSuprimt');
		$nomeItem->__set('cd_item', $_POST['cd_item']);
		$nomeItemBase = $nomeItem->getDadosItemEspecifico();

		// Buscar nome Subitem
		$nomeSubitem = Container::getModel('TbSbitemSuprimt');
		$nomeSubitem->__set('cd_item', $_POST['cd_item']);
		$nomeSubitem->__set('cd_sbitem', $_POST['cd_sbitem']);
		$nomeSubitemBase = $nomeSubitem->getDadosSubitemEspecifico();

		$this->view->dadosNecessidade = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $_POST['nomeGrupo'], 				
				'cd_sbgrp' => $_POST['cd_sbgrp'], 
				'nm_sbgrp' => $_POST['nomeSubgrupo'], 
				'cd_fml' => $_POST['cd_fml'], 
				'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
				'cd_tip_evt_neces' => $dadosNecesFmlBase['cd_tip_evt_neces'], 
				'dt_proxima_visita' => $dt_proxima_visita,
				'cd_setor_resp' => $dadosNecesFmlBase['cd_setor_resp'],
				'obs_sobre_item' => $dadosNecesFmlBase['obs_sobre_item'],
				'dsc_item_neces' => $dadosNecesFmlBase['dsc_item_neces'],
				'qtd_item_neces' => $dadosNecesFmlBase['qtd_item_neces'],
				'cd_tip_unid_item' => $dadosNecesFmlBase['cd_tip_unid_item'],
				'vlr_neces' => $dadosNecesFmlBase['vlr_neces'],
				'cd_item' => $_POST['cd_item'],
				'nm_item' => $nomeItemBase['nome_item'],
				'cd_sbitem' => $_POST['cd_sbitem'],
				'nm_sbitem' => $nomeSubitemBase['nome_subitem'],
				'seql_item_neces' => $_POST['seql_item_neces'],
				'dt_disponib_item_entrega' => $dt_disponib_item_entrega,
				'dt_incl_item_neces' => $dt_incl_item_neces,
				'nm_vlnt_resp_cadas' => $nomeVlntCadasBase,
				'nm_vlnt_resp_disponib' => $nomeVlntDisponibBase,
				'nm_situ_item_solicitado' => $dadosNecesFmlBase['nm_situ_item_solicitado']
		);

		$this->render('fNAlterarNecesProxVisitaMenu');

	}	// Fim da function fNAlterarNecesProxVisitaMenu

// ====================================================== //	
	
	public function fNAlterarNecesProxVisitaBase() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		if (!isset($_POST['vlr_neces'])) {
			$vlr_neces = '';
		} else {
			$vlr_neces = $_POST['vlr_neces'];
		}

		// Altera registro
		$alteraItemSubitemNeces = Container::getModel('TbItemNecesFml');
		$alteraItemSubitemNeces->__set('cd_fml', $_POST['cd_fml']);
		$alteraItemSubitemNeces->__set('cd_item', $_POST['cd_item']);
		$alteraItemSubitemNeces->__set('cd_sbitem', $_POST['cd_sbitem']);
		$alteraItemSubitemNeces->__set('seql_item_neces', $_POST['seql_item_neces']);
		$alteraItemSubitemNeces->__set('obs_sobre_item', $_POST['obs_sobre_item']);
		$alteraItemSubitemNeces->__set('dsc_item_neces', $_POST['dsc_item_neces']);
		$alteraItemSubitemNeces->__set('qtd_item_neces', $_POST['qtd_item_neces']);
		$alteraItemSubitemNeces->__set('vlr_neces', $vlr_neces);
		$alteraItemSubitemNeces->__set('cd_tip_unid_item', $_POST['cd_tip_unid_item']);
		$alteraItemSubitemNeces->__set('cd_vlnt_resp_cadas', $_SESSION['id']);
		$alteraItemSubitemNeces->alteraItemSubitemNecesPV();

		$this->view->erroValidacao = 1;

		// Buscar nome Item
		$nomeItem = Container::getModel('TbItemSuprimt');
		$nomeItem->__set('cd_item', $_POST['cd_item']);
		$nomeItemBase = $nomeItem->getDadosItemEspecifico();

		// Buscar nome Subitem
		$nomeSubitem = Container::getModel('TbSbitemSuprimt');
		$nomeSubitem->__set('cd_item', $_POST['cd_item']);
		$nomeSubitem->__set('cd_sbitem', $_POST['cd_sbitem']);
		$nomeSubitemBase = $nomeSubitem->getDadosSubitemEspecifico();

    $this->view->codSubgrupo = $_POST['cd_sbgrp'];
    $this->view->nomeSubgrup = $_POST['nm_sbgrp'];
    $this->view->codFamilia  = $_POST['cd_fml'];
    $this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
    $this->view->codItem     = $_POST['cd_item'];
    $this->view->nomeItem    = $nomeItemBase['nome_item'];
    $this->view->codSubitem  = $_POST['cd_sbitem'];
    $this->view->nomeSubitem = $nomeSubitemBase['nome_subitem'];

		session_write_close();		
		$this->fNAlterarNeces();

	}	// Fim da function fNAlterarNecesProxVisitaBase

// ====================================================== //	
	
	public function fNAlterarNecesEventualMenu() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		// Buscar Dados para alteração
		$dadosNecesFml = Container::getModel('TbItemNecesFml');
		$dadosNecesFml->__set('cd_fml', $_POST['cd_fml']);
		$dadosNecesFml->__set('cd_item', $_POST['cd_item']);
		$dadosNecesFml->__set('cd_sbitem', $_POST['cd_sbitem']);
		$dadosNecesFml->__set('seql_item_neces', $_POST['seql_item_neces']);
		$dadosNecesFmlBase = $dadosNecesFml->getDadosNecesEspecifica();

		$dt_prev_disponib_item = Funcoes::formatarNumeros('data', $dadosNecesFmlBase['dt_prev_disponib_item'], 10, "AMD");	
		$dt_incl_item_neces = Funcoes::formatarNumeros('data', $dadosNecesFmlBase['dt_incl_item_neces'], 10, "AMD");	
		$dt_disponib_item_entrega = Funcoes::formatarNumeros('data', $dadosNecesFmlBase['dt_disponib_item_entrega'], 10, "AMD");	

		// Buscar Nome dos Voluntários cadastrador e disponibilizador do item
		$nomeVlntCadas = Container::getModel('TbVlnt');
		$nomeVlntCadas->__set('id', $dadosNecesFmlBase['cd_vlnt_resp_cadas']);
		$nomeVlntCadasBase = $nomeVlntCadas->getInfoVoluntario();
		$nomeVlntCadasBase = $nomeVlntCadasBase['nm_vlnt'];

		if ($dadosNecesFmlBase['cd_vlnt_resp_disponib'] === null) {
			$nomeVlntDisponibBase = '';
		
		} else {
			$nomeVlntDisponib = Container::getModel('TbVlnt');
			$nomeVlntDisponib->__set('id', $dadosNecesFmlBase['cd_vlnt_resp_disponib']);
			$nomeVlntDisponibBase = $nomeVlntDisponib->getInfoVoluntario();
			$nomeVlntDisponibBase = $nomeVlntDisponibBase['nm_vlnt'];
		}

		// Buscar nome Item
		$nomeItem = Container::getModel('TbItemSuprimt');
		$nomeItem->__set('cd_item', $_POST['cd_item']);
		$nomeItemBase = $nomeItem->getDadosItemEspecifico();

		// Buscar nome Subitem
		$nomeSubitem = Container::getModel('TbSbitemSuprimt');
		$nomeSubitem->__set('cd_item', $_POST['cd_item']);
		$nomeSubitem->__set('cd_sbitem', $_POST['cd_sbitem']);
		$nomeSubitemBase = $nomeSubitem->getDadosSubitemEspecifico();

		$this->view->dadosNecessidade = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $_POST['nomeGrupo'], 				
				'cd_sbgrp' => $_POST['cd_sbgrp'], 
				'nm_sbgrp' => $_POST['nomeSubgrupo'], 
				'cd_fml' => $_POST['cd_fml'], 
				'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 

				'cd_tip_evt_neces' => $dadosNecesFmlBase['cd_tip_evt_neces'], 

				'dt_prev_disponib_item' => $dt_prev_disponib_item,
				'cd_setor_resp' => $dadosNecesFmlBase['cd_setor_resp'],
				'obs_sobre_item' => $dadosNecesFmlBase['obs_sobre_item'],
				'dsc_item_neces' => $dadosNecesFmlBase['dsc_item_neces'],

				'cd_tip_sexo' => $dadosNecesFmlBase['cd_tip_sexo'],
				'cd_estrut_corporal' => $dadosNecesFmlBase['cd_estrut_corporal'],
				'cd_tam_corporal' => $dadosNecesFmlBase['cd_tam_corporal'],
				'nr_corporal' => $dadosNecesFmlBase['nr_corporal'],
				'idade_aparente_pessoa' => $dadosNecesFmlBase['idade_aparente_pessoa'],
				'cd_tempo_idade' => $dadosNecesFmlBase['cd_tempo_idade'],
				'cd_tip_clas_item' => $dadosNecesFmlBase['cd_tip_clas_item'],

				'qtd_item_neces' => $dadosNecesFmlBase['qtd_item_neces'],
				'cd_tip_unid_item' => $dadosNecesFmlBase['cd_tip_unid_item'],
				'vlr_neces' => $dadosNecesFmlBase['vlr_neces'],

				'cd_item' => $_POST['cd_item'],
				'nm_item' => $nomeItemBase['nome_item'],
				'cd_sbitem' => $_POST['cd_sbitem'],
				'nm_sbitem' => $nomeSubitemBase['nome_subitem'],
				'seql_item_neces' => $_POST['seql_item_neces'],
				'dt_disponib_item_entrega' => $dt_disponib_item_entrega,
				'dt_incl_item_neces' => $dt_incl_item_neces,
				'nm_vlnt_resp_cadas' => $nomeVlntCadasBase,
				'nm_vlnt_resp_disponib' => $nomeVlntDisponibBase,
				'nm_situ_item_solicitado' => $dadosNecesFmlBase['nm_situ_item_solicitado']
		);

		$this->render('fNAlterarNecesEventualMenu');

	}	// Fim da function fNAlterarNecesEventualMenu

// ====================================================== //	
	
	public function fNAlterarNecesEventualBase() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		if (!isset($_POST['vlr_neces'])) {
			$vlr_neces = '';
		} else {
			$vlr_neces = $_POST['vlr_neces'];
		}

		// Altera registro
		$alteraItemSubitemNeces = Container::getModel('TbItemNecesFml');
		$alteraItemSubitemNeces->__set('cd_fml', $_POST['cd_fml']);
		$alteraItemSubitemNeces->__set('cd_item', $_POST['cd_item']);
		$alteraItemSubitemNeces->__set('cd_sbitem', $_POST['cd_sbitem']);
		$alteraItemSubitemNeces->__set('seql_item_neces', $_POST['seql_item_neces']);

		$alteraItemSubitemNeces->__set('obs_sobre_item', $_POST['obs_sobre_item']);
		$alteraItemSubitemNeces->__set('dsc_item_neces', $_POST['dsc_item_neces']);
		$alteraItemSubitemNeces->__set('qtd_item_neces', $_POST['qtd_item_neces']);
		$alteraItemSubitemNeces->__set('vlr_neces', $vlr_neces);
		$alteraItemSubitemNeces->__set('cd_tip_unid_item', $_POST['cd_tip_unid_item']);
		$alteraItemSubitemNeces->__set('cd_vlnt_resp_cadas', $_SESSION['id']);


		$alteraItemSubitemNeces->alteraItemSubitemNecesPV();

		$this->view->erroValidacao = 1;

		// Buscar nome Item
		$nomeItem = Container::getModel('TbItemSuprimt');
		$nomeItem->__set('cd_item', $_POST['cd_item']);
		$nomeItemBase = $nomeItem->getDadosItemEspecifico();

		// Buscar nome Subitem
		$nomeSubitem = Container::getModel('TbSbitemSuprimt');
		$nomeSubitem->__set('cd_item', $_POST['cd_item']);
		$nomeSubitem->__set('cd_sbitem', $_POST['cd_sbitem']);
		$nomeSubitemBase = $nomeSubitem->getDadosSubitemEspecifico();

    $this->view->codSubgrupo = $_POST['cd_sbgrp'];
    $this->view->nomeSubgrup = $_POST['nm_sbgrp'];
    $this->view->codFamilia  = $_POST['cd_fml'];
    $this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
    $this->view->codItem     = $_POST['cd_item'];
    $this->view->nomeItem    = $nomeItemBase['nome_item'];
    $this->view->codSubitem  = $_POST['cd_sbitem'];
    $this->view->nomeSubitem = $nomeSubitemBase['nome_subitem'];

		session_write_close();		
		$this->fNAlterarNeces();

	}	// Fim da function fNAlterarNecesProxVisitaBase

// ====================================================== //	
	
	public function fNExcluirNecesBase() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;		

		// Exclui registro Ficamente!
		$excluiItemSubitemNeces = Container::getModel('TbItemNecesFml');
		$excluiItemSubitemNeces->__set('cd_fml', $_POST['cd_fml']);
		$excluiItemSubitemNeces->__set('cd_item', $_POST['cd_item']);
		$excluiItemSubitemNeces->__set('cd_sbitem', $_POST['cd_sbitem']);
		$excluiItemSubitemNeces->__set('seql_item_neces', $_POST['seql_item_neces']);
		$excluiItemSubitemNeces->__set('obs_sobre_item', $_POST['obs_sobre_item']);
		$excluiItemSubitemNeces->__set('dsc_item_neces', $_POST['dsc_item_neces']);
		$excluiItemSubitemNeces->__set('cd_vlnt_resp_disponib', $_SESSION['id']);
		$excluiItemSubitemNeces->excluiLogicamenteItemSubitemNeces();

		$this->view->erroValidacao = 2;

		// Buscar nome Item
		$nomeItem = Container::getModel('TbItemSuprimt');
		$nomeItem->__set('cd_item', $_POST['cd_item']);
		$nomeItemBase = $nomeItem->getDadosItemEspecifico();

		// Buscar nome Subitem
		$nomeSubitem = Container::getModel('TbSbitemSuprimt');
		$nomeSubitem->__set('cd_item', $_POST['cd_item']);
		$nomeSubitem->__set('cd_sbitem', $_POST['cd_sbitem']);
		$nomeSubitemBase = $nomeSubitem->getDadosSubitemEspecifico();

	    $this->view->codSubgrupo = $_POST['cd_sbgrp'];
	    $this->view->nomeSubgrup = $_POST['nm_sbgrp'];
	    $this->view->codFamilia  = $_POST['cd_fml'];
	    $this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
	    $this->view->codItem     = $_POST['cd_item'];
	    $this->view->nomeItem    = $nomeItemBase['nome_item'];
	    $this->view->codSubitem  = $_POST['cd_sbitem'];
	    $this->view->nomeSubitem = $nomeSubitemBase['nome_subitem'];

		session_write_close();		
		$this->fNAlterarNeces();

	}	// Fim da function fNExcluirNecesBase

// ====================================================== //	
	
	public function fNImprimeNeces() {

		$this->geraTabelaNecessidades();

		// Comandos em LARANJA E MAIÚSCULO ESTÃO DEFINIDOS EM 'tecnickcom\tcpdf\config\tcpdf_config.php'

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// Informações que são gravadas (ficam em propriedades do Arquivo, qdo se está atachado no arquivo)
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('SGAS - Sistema de Gestão Auta de Souza');
		$pdf->SetTitle('Relatório das Necessidades');
		$pdf->SetSubject('Relatório das Necessidades da DPS');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING.' de Necessidades', array(0,64,255), array(0,64,128));
		
		$pdf->setFooterData(array(0,64,0), array(0,64,128));
		
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		    require_once(dirname(__FILE__).'/lang/eng.php');
		    $pdf->setLanguageArray($l);
		}

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('dejavusans', '', 14, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		$html = '
			<style>
				table {
					border-collapse: border-collapse;
					border-spacing: 0;
					margin: 0 20px;
				}
				tr {
					padding: 3px 0;
				}
				th {
					background-color: #3E8CCE;
					border: 1px solid #DDDDDD;
					color: #fff;
					font-family: trebuchet MS;
					font-size: 12px;
					padding-bottom: 4px;
					padding-left: 6px;
					padding-top: 5px;
					text-align: center;
				}
				td {
					border: 1px solid #CCCCCC;
					font-size: 10px;
					padding: 3px 7px 2px;
				}
				p {
					color: #3E8CCE;					
					font-size: 10px;
					font-family: trebuchet MS;
					width: 100%;
					padding-left: 6px;
				}
			</style>

			<p align="left">Situação Item: '.$this->view->situacaoItem.'</p><br><br>

			<table width="939" cellspacing="0" cellpadding="1" border="1">
			<tr style="background-color:#FF0000;color:#FFFF00;">
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Cód. Família</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Família</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Situação Família</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Grupo</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Subgrupo</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Item</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Subitem</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Quando Necessário</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Data Necessidade</font></th>
				<th><font face="trebuchet MS, Arial, Helvetica, sans-serif">Setor Responsável</font></th>
			</tr>';

		$total = 0;
		foreach ($this->view->dadosPesquisa as $key => $row) {
			$total = $total + 1;

				$html .= '
				<tr>
					<td>'.$row['cd_fml'].'</td>
					<td>'.$row['nm_grp_fmlr'].'</td>
					<td>'.$row['nm_est_situ_fml'].'</td>
					<td>'.$row['nm_grp'].'</td>
					<td>'.$row['nm_sbgrp'].'</td>
					<td>'.$row['nm_item'].'</td>
					<td>'.$row['nm_sbitem'].'</td>
					<td>'.$row['nm_tip_evt_neces'].'</td>
					<td>'.$row['dt_prev_disponib_item'].'</td>
					<td>'.$row['nm_setor_resp'].'</td>
				</tr>';
		}

		$html .= '</table>';

		$html .= '<p align="left">Total de Itens: '.number_format($total).'</p><br><br>';
		
		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, C, true);

		// Close and output PDF document
		// Comandos para limpar e poder imprimir/gerar PDF. O primeiro também funcionou somente ele
		//ob_end_clean();
		ob_start();
		$pdf->Output('Relatorio_Necessidades.pdf', 'I');
		ob_end_flush();

		session_write_close();		
		$this->fNAlterarNeces();

	}	// Fim da function fNImprimeNeces

}	//	Fim da classe

?>
				