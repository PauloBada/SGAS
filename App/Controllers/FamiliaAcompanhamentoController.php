<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class FamiliaAcompanhamentoController extends Action {

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
	
	public function validaAcessoAcompanhamentoRelatorio() {

		$this->retornoValidaAcessoAcompanhamentoRelatorio = 0;

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		$atuacaoVoluntarioAcompBase = Container::getModel('TbVnclVlntAcompFml');
		$atuacaoVoluntarioAcompBase->__set('cd_vlnt', $_SESSION['id']);
		$atuacaoVoluntarioAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$atuacaoVoluntarioAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$atuacaoVoluntarioAcomp = $atuacaoVoluntarioAcompBase->getAtuacaoVoluntarioAcomp();

		// Não está na tabela de vinculo Acompanhamento
		if (empty($atuacaoVoluntarioAcomp['cod_atuacao_acomp'])) { 				
			$this->view->erroValidacao = 1;

			$this->view->Familia = $_POST['cb_familia_escolhida'];

			$this->retornoValidaAcessoAcompanhamentoRelatorio = 1;

		} else {										

			// Não tem o nível Requerido
			if ($atuacaoVoluntarioAcomp['cod_atuacao_acomp'] != $this->nivel_atuacao_acomp_requerido) { 
				$this->view->erroValidacao = 2;

				$this->view->Familia = $_POST['cb_familia_escolhida'];

				if ($atuacaoVoluntarioAcomp['cod_atuacao_acomp'] == 1) {
					$this->view->atuacaoLogado = 'Revisor Relatório';	
				}	else if ($atuacaoVoluntarioAcomp['cod_atuacao_acomp'] == 2) {
					$this->view->atuacaoLogado = 'Visitador e Relator Relatório';
				} else {
					$this->view->atuacaoLogado = 'Visitador';
				}

				if ($this->nivel_atuacao_acomp_requerido == 1) {
					$this->view->atuacaoRequerida = 'Revisor Relatório';	
				}	else if ($this->nivel_atuacao_acomp_requerido == 2) {
					$this->view->atuacaoRequerida = 'Visitador e Relator Relatório';
				}else {
					$this->view->atuacaoRequerida = 'Visitador';
				}

				$this->view->Familia = $_POST['cb_familia_escolhida'];

				$this->retornoValidaAcessoAcompanhamentoRelatorio = 2;
			}
		}

	}	// Fim da function validaAcessoAcompanhamentoRelatorio

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

	// ====================================================== //	

	public function obtemDataAcompanhamento($fml) {	

		$atividade_acompanhamento = 1;  // Triagem
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $fml);
		$seqlAcomp->getSequencial();			

		// Para saber se já há acompanhamento cadastrado
		if (empty($seqlAcomp->__get('seql_max'))) {
			$this->dataAcompanhamento = '';
		} else {
			// Obter dados acompanhamento
			$dataAcompBase = Container::getModel('TbAcompFml');
			$dataAcompBase->__set('cd_fml', $fml);
			$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
			$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
			$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
			$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

			// Para o caso de haver registro na tabela, devido a nova triagem definida no relatório de revisão
			if (empty($dataAcomp['dt_acomp'])) {
				$this->dataAcompanhamento = '';
			} else {
				$this->dataAcompanhamento = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");	
			}
		}

	}	// Fim da function obtemDataAcompanhamento

	// ====================================================== //	

	public function geraKitHabitual($fml, $seql_acomp, $prox_data_visita) {	

		// $prox_data_visita => formato AAAA-MM-DD

		// Obter Quantidade de Integrantes da Família
		$qtdIntegrantes = Container::getModel('TbIntegFml');
		$qtdIntegrantes->__set('cd_fml', $fml);
		$qtdIntegrantesBase = $qtdIntegrantes->getQtdIntegrantes();
		
		if ($qtdIntegrantesBase['qtde'] > 0) {
			// O Cálculo do kit Habitual (Cesta Básica e Kit de Higiene/Limpeza) leva em consideração a relação de 7 integrantes.
			// Integrantes <= 7 						          ==> 1 Kit
			// Integrantes > 7 e Integrantes <= 14 		==> 2 Kits
			// Integrantes> 14 e Integrantes <= 21	  ==> 3 Kits
			// e assim sucessivamente....

			$total_int		=	(int) ($qtdIntegrantesBase['qtde'] / 7);
			$total_resto	=	($qtdIntegrantesBase['qtde'] % 7);

			if ($total_resto > 0) {
				$total_kits	=	$total_int + 1;
			} else {
				$total_kits	=	$total_int;
			}

			// Obtem o número de crianças até 7 anos para o Kit Habitual de Leite
			$data_hoje 		= 	new \DateTime();
			$data_hoje 		= 	$data_hoje->format("d/m/Y");

			$data_sete_anos 	= 	new \DateTime();

			// Para calcular 7 anos de intervalo
			$periodo = new \Dateinterval("P7Y");

			// subtrai do dia de hoje o período que se quer
			$data_sete_anos ->sub($periodo);
			$data_sete_anos = 	$data_sete_anos->format("d/m/Y");

			$qtdIntegrantesMenor7Anos = Container::getModel('TbIntegFml');
			$qtdIntegrantesMenor7Anos->__set('cd_fml', $fml);
			$qtdIntegrantesMenor7Anos->__set('data_7_anos', $data_sete_anos);
			$qtdIntegrantesMenor7Anos->__set('data_atual', $data_hoje);
			$qtdIntegrantesMenor7AnosBase = $qtdIntegrantesMenor7Anos->getQtdIntegrantesMenor7Anos();

			// Gerar Cesta Básica e Kit de Higiene/Limpeza
			for ($i = 1; $i < 3; $i++) {
				$item	=	$i;

				// Verificar se Item e Subitem já estão cadastrados, pois podem ter sido cadastrados através do Menu Principal
				$qtdItemSubitem = Container::getModel('TbItemNecesFml');
				$qtdItemSubitem->__set('cd_fml', $fml);
				$qtdItemSubitem->__set('cd_item', $item);
				$qtdItemSubitem->__set('cd_sbitem', 1);
				$qtdItemSubitem->__set('dt_prev_disponib_item', $prox_data_visita);
				$qtdItemSubitemBase = $qtdItemSubitem->getQtdItemSubitemNecessidade();

				if ($qtdItemSubitemBase['qtde'] == 0) {
					// Insere registros
					$insereItemSubitemNeces = Container::getModel('TbItemNecesFml');
					$insereItemSubitemNeces->__set('cd_fml', $fml);
					$insereItemSubitemNeces->__set('cd_item', $item);
					$insereItemSubitemNeces->__set('cd_sbitem', 1);
					$insereItemSubitemNeces->__set('seql_acomp', $seql_acomp);
					$insereItemSubitemNeces->__set('cd_setor_resp', 1);
					$insereItemSubitemNeces->__set('obs_sobre_item', 'Kit Habitual gerado automat p/próxima visita');
					$insereItemSubitemNeces->__set('dt_prev_disponib_item', $prox_data_visita);
					$insereItemSubitemNeces->__set('cd_disponib_item', 2);
					$insereItemSubitemNeces->__set('dt_disponib_item_entrega', $prox_data_visita);
					$insereItemSubitemNeces->__set('dsc_item_neces', 'Kit Habitual');
					$insereItemSubitemNeces->__set('qtd_item_neces', $total_kits);
					$insereItemSubitemNeces->__set('vlr_neces', 0);
					$insereItemSubitemNeces->__set('cd_situ_item_solicitado', 2);
					$insereItemSubitemNeces->__set('cd_tip_unid_item', 2);
					$insereItemSubitemNeces->__set('cd_vlnt_resp_cadas', $_SESSION['id']);
					$insereItemSubitemNeces->__set('cd_tip_evt_neces', 1);
					$insereItemSubitemNeces->insertItemSubitemNeces();
				}
			}

			// Gerar Kit de Leite
			if ($qtdIntegrantesMenor7AnosBase['qtde'] > 0) {
				$total_int_menor		=	(int) ($qtdIntegrantesMenor7AnosBase['qtde'] / 7);
				$total_resto_menor	=	($qtdIntegrantesMenor7AnosBase['qtde'] % 7);

				if ($total_resto_menor > 0) {
					$total_kits_menor	=	$total_int_menor + 1;
				} else {
					$total_kits_menor	=	$total_int_menor;
				}

				// Verificar se Item e Subitem já estão cadastrados, pois podem ter sido cadastrados através do Menu Principal
				$qtdItemSubitem = Container::getModel('TbItemNecesFml');
				$qtdItemSubitem->__set('cd_fml', $fml);
				$qtdItemSubitem->__set('cd_item', 3);
				$qtdItemSubitem->__set('cd_sbitem', 1);
				$qtdItemSubitem->__set('dt_prev_disponib_item', $prox_data_visita);
				$qtdItemSubitemBase = $qtdItemSubitem->getQtdItemSubitemNecessidade();

				if ($qtdItemSubitemBase['qtde'] == 0) {
					// Insere registros
					$insereItemSubitemNeces = Container::getModel('TbItemNecesFml');
					$insereItemSubitemNeces->__set('cd_fml', $fml);
					$insereItemSubitemNeces->__set('cd_item', 3);
					$insereItemSubitemNeces->__set('cd_sbitem', 1);
					$insereItemSubitemNeces->__set('seql_acomp', $seql_acomp);
					$insereItemSubitemNeces->__set('cd_setor_resp', 1);
					$insereItemSubitemNeces->__set('obs_sobre_item', 'Kit Habitual gerado automat p/próxima visita');
					$insereItemSubitemNeces->__set('dt_prev_disponib_item', $prox_data_visita);
					$insereItemSubitemNeces->__set('cd_disponib_item', 2);
					$insereItemSubitemNeces->__set('dt_disponib_item_entrega', $prox_data_visita);
					$insereItemSubitemNeces->__set('dsc_item_neces', 'Kit Habitual Leite');
					$insereItemSubitemNeces->__set('qtd_item_neces', $total_kits_menor);
					$insereItemSubitemNeces->__set('vlr_neces', 0);
					$insereItemSubitemNeces->__set('cd_situ_item_solicitado', 2);
					$insereItemSubitemNeces->__set('cd_tip_unid_item', 2);
					$insereItemSubitemNeces->__set('cd_vlnt_resp_cadas', $_SESSION['id']);
					$insereItemSubitemNeces->__set('cd_tip_evt_neces', 1);					
					$insereItemSubitemNeces->insertItemSubitemNeces();
				}
			}
		}
	}	// Fim da function geraKitHabitual

	// ====================================================== //	

	public function atualizaqtdPendenciasRelatorios() {
		// Busca Relatórios Pendentes de Revisão ou Fomalização
		$qtdRelatoriosPendentesBase = Container::getModel('TbAcompFml');
		$qtdRelatoriosPendentes = $qtdRelatoriosPendentesBase->getQtdRelatoriosPendentes();

		$this->view->qtdPendenciasRelatorios = $qtdRelatoriosPendentes['qtde'];
		
	}	// Fim da atualizaqtdPendenciasRelatorios geraKitHabitual

// ================================================== //
//          Início de Acompanhamento de Família             //
// ================================================== //

	public function familiaAcompanhamento() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		$this->atualizaqtdPendenciasRelatorios();

		$this->render('familiaAcompanhamento');
	}

// ====================================================== //	
	
	public function familiaAcompanhamentoPreIncluirRelTriagem() {
		
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

			$this->render('familiaAcompanhamentoPreIncluirRelTriagem');		
		}
	}	// Fim da function familiaAcompanhamentoPreIncluirRelTriagem

// ====================================================== //	
	
	public function familiaAcompanhamentoIncluirRelTriagem() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Valida se Grupo foi escolhido
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('familiaAcompanhamentoPreIncluirRelTriagem');

		} else {

			$this->nivel_atuacao_requerido = 5;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela tb_vncl_vlnt_grp
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('familiaAcompanhamentoPreIncluirRelTriagem');				

			// Não tem a atuação necessária
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('familiaAcompanhamentoPreIncluirRelTriagem');				

			} else {
				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];

				// Buscar Familias atreladas ao Grupo e Subgrupo e que estejam com cd_est_situ_fml = 2 (Aguardando Triagem)
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('codEstSituFml',  2);				
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoRT();

				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado

						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

					  $dataCadastro_f = Funcoes::formatarNumeros('data', $arr['dt_cadastro_fml'], 10, "AMD");

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'dt_cadastro_fml' => $dataCadastro_f,
								'cd_est_situ_fml' => $cd_est_situ_fml,
								'ptc_atendto_fml' => $arr['ptc_atendto_fml'],
								'pos_ranking_atendto_fml' => $arr['pos_ranking_atendto_fml']
						));
					}

					// Para compor os dados do Grupo e Subgrupo acima da tabela
					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('familiaAcompanhamentoIncluirRelTriagem');	
				} else {
					$this->view->erroValidacao = 3;

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('familiaAcompanhamentoPreIncluirRelTriagem');
				}
			}
		}	
	
	}	// Fim da function familiaAcompanhamentoIncluirRelTriagem

// ====================================================== //	

	public function familiaAcompanhamentoIncluirRelTriagemMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 0;

		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $nomeGrupo, 
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
				'nm_sbgrp' => $nomeSubgrupo, 
				'cd_fml' => $_POST['cb_familia_escolhida'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'] 
		);

		$this->view->segmento1 = 0;
		$this->view->segmento2 = 0;
		$this->view->segmento3 = 0;
		$this->view->segmento4 = 0;
		$this->view->segmento5 = 0;
		$this->view->segmento6 = 0;
		$this->view->segmento7 = 0;
		$this->view->segmento8 = 0;
		$this->view->vnclVlntAcomp = 0;

		$atividade_acompanhamento = 1;  							// Triagem
		$estado_acompanhamento_ini = 1;								// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;								// Pendente de Término de registro de Triagem/Visita
		$atuacao_voluntario_acompanhamento = 3;				// Visitador

		// Verificar se há Relatório de Triagem em Andamento //
		$verificaTriagemVisita0 = Container::getModel('TbAcompFml');
		$verificaTriagemVisita0->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$verificaTriagemVisita0->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita0->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita0->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase0 = $verificaTriagemVisita0->getQtdTriagemVisita();

		if ($verificaTriagemVisitaBase0['seql_acomp'] > 0) {
			for ($i = 1; $i <= 8; $i++) {
				// Verificar se a Triagem atual está em Andamento //
				$verificaTriagem0 = Container::getModel('TbSegmtoTriagemFml');
				$verificaTriagem0->__set('codFamilia', $_POST['cb_familia_escolhida']);
				$verificaTriagem0->__set('seqlAcomp', $verificaTriagemVisitaBase0['seql_acomp']);
				$verificaTriagem0->__set('codSegmtoTriagem', $i);
				$verificaTriagemBase0 = $verificaTriagem0->getQtdSegmentoTriagem();

				if ($verificaTriagemBase0['qtde'] > 0) {
					switch ($i) {
						case 1:
							$this->view->segmento1 = 1;
							break;
						case 2:
							$this->view->segmento2 = 1;
							break;
						case 3:
							$this->view->segmento3 = 1;
							break;
						case 4:
							$this->view->segmento4 = 1;
							break;
						case 5:
							$this->view->segmento5 = 1;
							break;
						case 6:
							$this->view->segmento6 = 1;
							break;
						case 7:
							$this->view->segmento7 = 1;
							break;
						case 8:
							$this->view->segmento8 = 1;
							break;
					}
				}
			}
		}			

		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
		}

		$this->render('familiaAcompanhamentoIncluirRelTriagemMenu');

	}	// Fim da function familiaAcompanhamentoIncluirRelTriagemMenu

// ====================================================== //	

	public function familiaAcompIncRelTriagemEducacaoMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'cd_freq_crianca_adoles_escola' => '',
			'dsc_mtvo_freq_escolar' => '',
			'dsc_desemp_estudo' => '',
			'cd_interes_motiva_voltar_estudar' => '',
			'dsc_curso_interes_fml' => '',
			'dsc_prgm_trab' => ''
		);

		$this->render('familiaAcompIncRelTriagemEducacaoMenu');
		

	}	// Fim da function familiaAcompIncRelTriagemEducacaoMenu		


// ====================================================== //	

	public function familiaAcompIncRelTriagemEducacaoBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {
			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('cd_freq_crianca_adoles_escola', $_POST['cd_freq_crianca_adoles_escola']);
			$insereTbSegmtoTriagemFml->__set('dsc_mtvo_freq_escolar', $_POST['dsc_mtvo_freq_escolar']);
			$insereTbSegmtoTriagemFml->__set('dsc_desemp_estudo', $_POST['dsc_desemp_estudo']);
			$insereTbSegmtoTriagemFml->__set('cd_interes_motiva_voltar_estudar', $_POST['cd_interes_motiva_voltar_estudar']);
			$insereTbSegmtoTriagemFml->__set('dsc_curso_interes_fml', $_POST['dsc_curso_interes_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			// Atualiza timestamp e data de Acompanhamento de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();		
		}

		$this->familiaAcompIncRelTriagemRetorno();
	
	}	// Fim da function familiaAcompIncRelTriagemEducacaoBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemReligiosidadeMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'dsc_religiao_fml' => '',
			'dsc_institu_religiosa_freqtd' => '',
			'dsc_freq_institu_religiosa' => '',
			'habito_prece_oracao' => 'nao',
			'evangelho_lar' => 'nao',
			'conhece_espiritismo' => 'nao',
			'vont_aprox_espiritismo' => 'nao',
			'dsc_prgm_trab' => ''
		);

		$this->render('familiaAcompIncRelTriagemReligiosidadeMenu');
		

	}	// Fim da function familiaAcompIncRelTriagemReligiosidadeMenu		

// ====================================================== //	

	public function familiaAcompIncRelTriagemReligiosidadeBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {
		
			if ($_POST['habito_prece_oracao'] == 'sim') {
				$habito_prece_oracao = 'S'; 
			} else {
				$habito_prece_oracao = 'N'; 
			}

			if ($_POST['evangelho_lar'] == 'sim') {
				$evangelho_lar = 'S'; 
			} else {
				$evangelho_lar = 'N'; 
			}
			if ($_POST['conhece_espiritismo'] == 'sim') {
				$conhece_espiritismo = 'S'; 
			} else {
				$conhece_espiritismo = 'N'; 
			}
			
			if ($_POST['vont_aprox_espiritismo'] == 'sim') {
				$vont_aprox_espiritismo = 'S'; 
			} else {
				$vont_aprox_espiritismo = 'N'; 
			}

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);			
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_religiao_fml', $_POST['dsc_religiao_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_institu_religiosa_freqtd', $_POST['dsc_institu_religiosa_freqtd']);
			$insereTbSegmtoTriagemFml->__set('dsc_freq_institu_religiosa', $_POST['dsc_freq_institu_religiosa']);
			$insereTbSegmtoTriagemFml->__set('habito_prece_oracao', $habito_prece_oracao);
			$insereTbSegmtoTriagemFml->__set('evangelho_lar', $evangelho_lar);
			$insereTbSegmtoTriagemFml->__set('conhece_espiritismo', $conhece_espiritismo);
			$insereTbSegmtoTriagemFml->__set('vont_aprox_espiritismo', $vont_aprox_espiritismo);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();		
		}

		$this->familiaAcompIncRelTriagemRetorno();

	}	// Fim da function familiaAcompIncRelTriagemReligiosidadeBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemMoradiaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'dsc_casa' => '',
			'exist_anim_inset_insal_perig' => 'nao',
			'dsc_anim_inset_insal_perig' => '',
			'exist_anim_estima' => 'nao',
			'dsc_anim_estima' => '',
			'vacina_anti_rabica_anim_estima' => 'naoseaplica',
			'dsc_prgm_trab' => '',
			'cd_agua_moradia' => '1',
			'cd_esgoto_moradia' => '1'

		);

		$this->render('familiaAcompIncRelTriagemMoradiaMenu');
		

	}	// Fim da function familiaAcompIncRelTriagemMoradiaMenu		

// ====================================================== //	

	public function familiaAcompIncRelTriagemMoradiaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {		

			if ($_POST['exist_anim_inset_insal_perig'] == 'sim') {
				$exist_anim_inset_insal_perig = 'S'; 
			} else {
				$exist_anim_inset_insal_perig = 'N'; 
			}

			if ($_POST['exist_anim_estima'] == 'sim') {
				$exist_anim_estima = 'S'; 
			} else {
				$exist_anim_estima = 'N'; 
			}

			if ($_POST['vacina_anti_rabica_anim_estima'] == 'sim') {
				$vacina_anti_rabica_anim_estima = 'S'; 
			} else if ($_POST['vacina_anti_rabica_anim_estima'] == 'nao') {
				$vacina_anti_rabica_anim_estima = 'N'; 
			} else if ($_POST['vacina_anti_rabica_anim_estima'] == 'naoseaplica') {
				$vacina_anti_rabica_anim_estima = 'NA'; 
			}

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_casa', $_POST['dsc_casa']);
			$insereTbSegmtoTriagemFml->__set('exist_anim_inset_insal_perig', $exist_anim_inset_insal_perig);
			$insereTbSegmtoTriagemFml->__set('dsc_anim_inset_insal_perig', $_POST['dsc_anim_inset_insal_perig']);
			$insereTbSegmtoTriagemFml->__set('exist_anim_estima', $exist_anim_estima);
			$insereTbSegmtoTriagemFml->__set('dsc_anim_estima', $_POST['dsc_anim_estima']);
			$insereTbSegmtoTriagemFml->__set('vacina_anti_rabica_anim_estima', $vacina_anti_rabica_anim_estima);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->__set('cd_agua_moradia', $_POST['cd_agua_moradia']);
			$insereTbSegmtoTriagemFml->__set('cd_esgoto_moradia', $_POST['cd_esgoto_moradia']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);		
			$atualizaTSTbAcompFml->updateTS();		
		}

		$this->familiaAcompIncRelTriagemRetorno();

	}	// Fim da function familiaAcompIncRelTriagemMoradiaBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemSaudeMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);		

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'dsc_cndc_saude_membros_fml' => '',
			'dsc_carteira_vacina_crianca' => '',
			'dsc_doenca_cronica_fml' => '',
			'dsc_restricao_alimentar' => '',
			'dsc_higiene_pessoal' => '',
			'dsc_prgm_trab' => ''
		);

		$this->render('familiaAcompIncRelTriagemSaudeMenu');
		

	}	// Fim da function familiaAcompIncRelTriagemSaudeMenu		

// ====================================================== //	

	public function familiaAcompIncRelTriagemSaudeBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {
			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_cndc_saude_membros_fml', $_POST['dsc_cndc_saude_membros_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_carteira_vacina_crianca', $_POST['dsc_carteira_vacina_crianca']);
			$insereTbSegmtoTriagemFml->__set('dsc_doenca_cronica_fml', $_POST['dsc_doenca_cronica_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_restricao_alimentar', $_POST['dsc_restricao_alimentar']);
			$insereTbSegmtoTriagemFml->__set('dsc_higiene_pessoal', $_POST['dsc_higiene_pessoal']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);			
			$atualizaTSTbAcompFml->updateTS();		
		}

		$this->familiaAcompIncRelTriagemRetorno();

	}	// Fim da function familiaAcompIncRelTriagemSaudeBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemDespesaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);		

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'cd_tip_moradia' => '',
			'dsc_dono_cedente_moradia' => '',
			'vlr_desp_agua' => '',
			'vlr_desp_energia' => '',
			'vlr_desp_iptu' => '',
			'vlr_desp_gas' => '',
			'vlr_desp_condominio' => '',
			'vlr_desp_outra_manut' => '',
			'dsc_desp_outra_manut' => '',
			'dsc_desp_saude_medicamento' => '',
			'dsc_desp_educ_creche_cuidadora' => '',
			'dsc_desp_transporte' => '',
			'dsc_desp_alimenta_especial' => '',
			'dsc_outra_desp_geral' => '',
			'dsc_prgm_trab' => ''
		);

		$this->render('familiaAcompIncRelTriagemDespesaMenu');

	}	// Fim da function familiaAcompIncRelTriagemDespesaMenu		

// ====================================================== //	

	public function familiaAcompIncRelTriagemDespesaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('cd_tip_moradia', $_POST['cd_tip_moradia']);
			$insereTbSegmtoTriagemFml->__set('dsc_dono_cedente_moradia', $_POST['dsc_dono_cedente_moradia']);
			$insereTbSegmtoTriagemFml->__set('vlr_desp_agua', $_POST['vlr_desp_agua']);
			$insereTbSegmtoTriagemFml->__set('vlr_desp_energia', $_POST['vlr_desp_energia']);
			$insereTbSegmtoTriagemFml->__set('vlr_desp_iptu', $_POST['vlr_desp_iptu']);
			$insereTbSegmtoTriagemFml->__set('vlr_desp_gas', $_POST['vlr_desp_gas']);
			$insereTbSegmtoTriagemFml->__set('vlr_desp_condominio', $_POST['vlr_desp_condominio']);
			$insereTbSegmtoTriagemFml->__set('vlr_desp_outra_manut', $_POST['vlr_desp_outra_manut']);
			$insereTbSegmtoTriagemFml->__set('dsc_desp_outra_manut', $_POST['dsc_desp_outra_manut']);
			$insereTbSegmtoTriagemFml->__set('dsc_desp_saude_medicamento', $_POST['dsc_desp_saude_medicamento']);
			$insereTbSegmtoTriagemFml->__set('dsc_desp_educ_creche_cuidadora', $_POST['dsc_desp_educ_creche_cuidadora']);
			$insereTbSegmtoTriagemFml->__set('dsc_desp_transporte', $_POST['dsc_desp_transporte']);
			$insereTbSegmtoTriagemFml->__set('dsc_desp_alimenta_especial', $_POST['dsc_desp_alimenta_especial']);
			$insereTbSegmtoTriagemFml->__set('dsc_outra_desp_geral', $_POST['dsc_outra_desp_geral']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();
		
			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);			
			$atualizaTSTbAcompFml->updateTS();
		}

		$this->familiaAcompIncRelTriagemRetorno();

	}	// Fim da function familiaAcompIncRelTriagemDespesaBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemRendaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);		

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'cd_tip_trab' => '',
			'vlr_renda_tip_trab' => '',
			'dsc_tip_beneficio' => '',
			'vlr_renda_tip_beneficio' => '',
			'dsc_prgm_trab' => ''
		);

		$this->render('familiaAcompIncRelTriagemRendaMenu');
		

	}	// Fim da function familiaAcompIncRelTriagemRendaMenu		

// ====================================================== //	

	public function familiaAcompIncRelTriagemRendaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {
			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('cd_tip_trab', $_POST['cd_tip_trab']);
			$insereTbSegmtoTriagemFml->__set('vlr_renda_tip_trab', $_POST['vlr_renda_tip_trab']);
			$insereTbSegmtoTriagemFml->__set('dsc_tip_beneficio', $_POST['dsc_tip_beneficio']);
			$insereTbSegmtoTriagemFml->__set('vlr_renda_tip_beneficio', $_POST['vlr_renda_tip_beneficio']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);			
			$atualizaTSTbAcompFml->updateTS();		
		}

		$this->familiaAcompIncRelTriagemRetorno();

	}	// Fim da function familiaAcompIncRelTriagemRendaBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemCapProfissionalMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);		

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'dsc_expect_fml_capacit_profi' => '',
			'dsc_curso_intere_profi_tecnico' => '',
			'dsc_projeto_gera_renda_extra' => '',
			'dsc_prgm_trab' => ''
		);

		$this->render('familiaAcompIncRelTriagemCapProfissionalMenu');
		

	}	// Fim da function familiaAcompIncRelTriagemCapProfissionalMenu		

// ====================================================== //	

	public function familiaAcompIncRelTriagemCapProfissionalBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {
			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_expect_fml_capacit_profi', $_POST['dsc_expect_fml_capacit_profi']);
			$insereTbSegmtoTriagemFml->__set('dsc_curso_intere_profi_tecnico', $_POST['dsc_curso_intere_profi_tecnico']);
			$insereTbSegmtoTriagemFml->__set('dsc_projeto_gera_renda_extra', $_POST['dsc_projeto_gera_renda_extra']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();
		
			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);			
			$atualizaTSTbAcompFml->updateTS();
		}

		$this->familiaAcompIncRelTriagemRetorno();

	}	// Fim da function familiaAcompIncRelTriagemCapProfissionalBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemAspectoIntMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->obtemDataAcompanhamento($_POST['cd_fml']);		

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $this->dataAcompanhamento,
			'dsc_aspecto_intimo' => '',
			'dsc_prgm_trab' => ''
		);

		$this->render('familiaAcompIncRelTriagemAspectoIntMenu');
		

	}	// Fim da function familiaAcompIncRelTriagemAspectoIntMenu		

// ====================================================== //	

	public function familiaAcompIncRelTriagemAspectoIntBase() {
		
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemTratamento();

		if ($this->view->retorno['triagem_cadastrada'] == 0) {
			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $this->view->retorno['segmto_triagem']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_aspecto_intimo', $_POST['dsc_aspecto_intimo']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);			
			$atualizaTSTbAcompFml->updateTS();
		}

		$this->familiaAcompIncRelTriagemRetorno();

	}	// Fim da function familiaAcompIncRelTriagemAspectoIntBase		

// ====================================================== //	

	public function familiaAcompIncRelTriagemTratamento() {
		$atividade_acompanhamento = 1;  // Triagem
		$avalia_triagem = 1;  					// Em processo de Triagem
		$segmento_triagem = $_POST['cd_segmto'];  		
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita

		$ha_relatorio_cadastrado = 0;
		$ha_triagem_cadastrada = 0;

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

		switch ($_POST['cd_segmto']) {
			case 1:
				$this->view->nomeSegmento = "Educação";
				break;
			case 2:
				$this->view->nomeSegmento = "Religiosidade";
				break;
			case 3:
				$this->view->nomeSegmento = "Moradia";
				break;
			case 4:
				$this->view->nomeSegmento = "Saúde";
				break;
			case 5:
				$this->view->nomeSegmento = "Despesa";
				break;
			case 6:
				$this->view->nomeSegmento = "Renda";
				break;
			case 7:
				$this->view->nomeSegmento = "Capacitação Profissional";
				break;
			case 8:
				$this->view->nomeSegmento = "Aspectos Íntimos";
				break;
		}
		
		// Verificar se há Relatório em Andamento //
		$verificaTriagemVisita = Container::getModel('TbAcompFml');
		$verificaTriagemVisita->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagemVisita->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase = $verificaTriagemVisita->getQtdTriagemVisita();

		if ($verificaTriagemVisitaBase['seql_acomp'] > 0) {
			$ha_relatorio_cadastrado = 1;

			// Verificar se a Triagem atual está em Andamento //
			$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
			$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
			$verificaTriagem->__set('seqlAcomp', $verificaTriagemVisitaBase['seql_acomp']);
			$verificaTriagem->__set('codSegmtoTriagem', $segmento_triagem);
			$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

			if ($verificaTriagemBase['qtde'] > 0) {
				$ha_triagem_cadastrada = 1;
			}
		}			

		if ($ha_relatorio_cadastrado == 0) { 
			// Obtem Próximo Sequencial de Acompanhamento
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getProximoSequencial();			

			// Insere na tabela tb_acomp_fml
			$insereTbAcompFml = Container::getModel('TbAcompFml');
			$insereTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereTbAcompFml->__set('cd_atvd_acomp', $atividade_acompanhamento);
			$insereTbAcompFml->__set('cd_avalia_triagem', $avalia_triagem);
			$insereTbAcompFml->__set('dt_acomp', $dataAcomp_formatada);
			$insereTbAcompFml->insertAcompanhamentoFamilia();	

			// Inserir Voluntário na tabela tb_vncl_vlnt_acomp_fml
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Obtem Sequencial de Acompanhamento Atual
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getSequencial();			
		}

		$this->view->retorno = array (
				'triagem_cadastrada' => $ha_triagem_cadastrada,
				'seqlAcomp' => $seqlAcomp->__get('seql_max'),
				'segmto_triagem' => $segmento_triagem,
				'dataAcomp_formatada' => $dataAcomp_formatada
		);

	}	// Fim da function familiaAcompIncRelTriagemTratamento		

// ====================================================== //	

	public function familiaAcompIncRelTriagemRetorno() {

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cd_grp'], 
				'cb_grupo_escolhido' => $_POST['cd_grp'], 
				'nm_grp' => $_POST['nm_grp'], 
				'cd_sbgrp' => $_POST['cd_sbgrp'], 
				'cb_subgrupo_escolhido' => $_POST['cd_sbgrp'], 
				'nm_sbgrp' => $_POST['nm_sbgrp'], 
				'cd_fml' => $_POST['cd_fml'], 
				'cb_familia_escolhida' => $_POST['cd_fml'], 
				'nm_grp_fmlr' => $_POST['nm_grp_fmlr']  
		);

		$this->view->erroValidacao = 2;

		$this->view->segmento1 = 0;
		$this->view->segmento2 = 0;
		$this->view->segmento3 = 0;
		$this->view->segmento4 = 0;
		$this->view->segmento5 = 0;
		$this->view->segmento6 = 0;
		$this->view->segmento7 = 0;
		$this->view->segmento8 = 0;
		$this->view->vnclVlntAcomp = 0;

		$atividade_acompanhamento = 1;  // Triagem
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$atuacao_voluntario_acompanhamento = 3;				// Visitador

		// Verificar se há Relatório de Triagem em Andamento //
		$verificaTriagemVisita0 = Container::getModel('TbAcompFml');
		$verificaTriagemVisita0->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$verificaTriagemVisita0->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita0->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita0->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase0 = $verificaTriagemVisita0->getQtdTriagemVisita();

		if ($verificaTriagemVisitaBase0['seql_acomp'] > 0) {
			for ($i = 1; $i <= 8; $i++) {
				// Verificar se a Triagem atual está em Andamento //
				$verificaTriagem0 = Container::getModel('TbSegmtoTriagemFml');
				$verificaTriagem0->__set('codFamilia', $_POST['cb_familia_escolhida']);
				$verificaTriagem0->__set('seqlAcomp', $verificaTriagemVisitaBase0['seql_acomp']);
				$verificaTriagem0->__set('codSegmtoTriagem', $i);
				$verificaTriagemBase0 = $verificaTriagem0->getQtdSegmentoTriagem();

				if ($verificaTriagemBase0['qtde'] > 0) {
					switch ($i) {
						case 1:
							$this->view->segmento1 = 1;
							break;
						case 2:
							$this->view->segmento2 = 1;
							break;
						case 3:
							$this->view->segmento3 = 1;
							break;
						case 4:
							$this->view->segmento4 = 1;
							break;
						case 5:
							$this->view->segmento5 = 1;
							break;
						case 6:
							$this->view->segmento6 = 1;
							break;
						case 7:
							$this->view->segmento7 = 1;
							break;
						case 8:
							$this->view->segmento8 = 1;
							break;
					}

				}
			}
		}			

		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
		}

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->render('familiaAcompanhamentoIncluirRelTriagemMenu');
	
	}	// Fim da function familiaAcompIncRelTriagemRetorno		

// ====================================================== //	

	public function familiaAcompIncRelTriagemVoluntario() {
	
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		$this->familiaAcompIncRelTriagemVoluntarioPOST();

	} // Fim da function familiaAcompIncRelTriagemVoluntario

// ====================================================== //	

	public function familiaAcompIncRelTriagemVoluntarioBase() {

		$this->validaAutenticacao();	

		$voluntarios = explode(',', $_POST['cb_voluntario_escolhido']);

		for ($i = 0; $i < count($voluntarios); $i++) {
			// Inserir Voluntário em tb_vncl_vlnt_acomp_fml
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $voluntarios[$i]);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 3);
			$insereVoluntarioTVVAF->insertTVVAF();
		}

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 1;

		$this->view->atuacao = 'Visitador';
		
		$this->familiaAcompIncRelTriagemVoluntarioPOST();

	} // Fim da function familiaAcompIncRelTriagemVoluntarioBase


// ====================================================== //	

	public function familiaAcompIncRelTriagemVoluntarioPOST() {

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		// Para saber se já há acompanhamento cadastrado
		if (empty($seqlAcomp->__get('seql_max'))) {
			$this->view->dadosVoluntarios = array ();
			
			$this->view->erroValidacao = 2;

		} else {

			// Buscar Voluntários em tb_vncl_vlnt_grp com cd_atu_vlnt = 5 (Somente Voluntários)
			$obtemVoluntariosGSBase = Container::getModel('TbVnclVlntGrp');
			$obtemVoluntariosGSBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$obtemVoluntariosGSBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$obtemVoluntariosGSBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$obtemVoluntariosGSBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$obtemVoluntariosGSBase->__set('cd_atu_vlnt', 5);

			$obtemVoluntariosGS = $obtemVoluntariosGSBase->getVoluntariosGrupoSubgrupo();

			$this->view->dadosVoluntarios = array ();

			foreach ($obtemVoluntariosGS as $index => $arr) {
				// Para nao aparecer o voluntário que está logado
				if ($arr['cd_vlnt'] != $_SESSION['id']) {
					array_push($this->view->dadosVoluntarios, array (
								'cd_vlnt' => $arr['cd_vlnt'],
								'nm_vlnt' => $arr['nm_vlnt'],
								'opcaoAtuacao' => ''
					));
				}
			}
		}

		// Para compor os dados do Grupo e Subgrupo acima da tabela
		$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
		$this->view->nomeGrupo = $_POST['nm_grp'];
		$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
		$this->view->nomeSubgrupo = $_POST['nm_sbgrp'];
		$this->view->codFamilia = $_POST['cb_familia_escolhida'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
		$this->view->seqlAcomp = $seqlAcomp->__get('seql_max');
		$this->view->origem = $_POST['origem'];

		$this->render('familiaAcompIncRelTriagemVoluntario');

	} // Fim da function familiaAcompIncRelTriagemVoluntarioPOST

// ====================================================== //	
	
	public function fAPreAlterarRT() {
		
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

			$this->render('fAPreAlterarRT');
		}
	}	// Fim da function fAPreAlterarRT

// ====================================================== //	
	
	public function fAAlterarRT() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$atividade_acompanhamento = 1;  // Triagem
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_situacao_fml = 2;  			// Aguardando Triagem

		// Valida se Grupo e Subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreAlterarRT');

		} else {

			$this->nivel_atuacao_requerido = 5;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela tb_vncl_vlnt_grp
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreAlterarRT');				

			// Não tem a atuação necessária
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreAlterarRT');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];

				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp',  $atividade_acompanhamento);
				$familiasVnclGrupoBase->__set('codInicialAcomp',  $estado_acompanhamento_ini);
				$familiasVnclGrupoBase->__set('codFinalAcomp',  $estado_acompanhamento_fim);
				$familiasVnclGrupoBase->__set('codEstSituFml',  $estado_situacao_fml);
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompanhamento();

				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado

						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			

						// Obter dados acompanhamento //
						$dataAcompBase = Container::getModel('TbAcompFml');
						$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
						$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
						$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
						$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
						$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
						$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

					  $dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'dt_cadastro_fml' => $dataAcomp_f,
								'cd_est_situ_fml' => $cd_est_situ_fml,
								'ptc_atendto_fml' => $arr['ptc_atendto_fml'],
								'pos_ranking_atendto_fml' => $arr['pos_ranking_atendto_fml'],
								'origem' => 'alteracaoRelatorio'
						));
					}

					// Para compor os dados do Grupo e Subgrupo acima da tabela
					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAAlterarRT');	
				} else {
					$this->view->erroValidacao = 3;

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreAlterarRT');
				}
			}	
		}	
	}	// Fim da function fAAlterarRT

// ====================================================== //	

	public function fAAlterarRTMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 0;

		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		if (!isset($_POST['origem'])) {
			$origem = 'alteracaoRelatorio';
		} else {
			$origem = $_POST['origem'];
		}

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $nomeGrupo, 
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
				'nm_sbgrp' => $nomeSubgrupo, 
				'cd_fml' => $_POST['cb_familia_escolhida'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
				'origem' => $origem
		);

		$this->view->segmento1 = 0;
		$this->view->segmento2 = 0;
		$this->view->segmento3 = 0;
		$this->view->segmento4 = 0;
		$this->view->segmento5 = 0;
		$this->view->segmento6 = 0;
		$this->view->segmento7 = 0;
		$this->view->segmento8 = 0;
		$this->view->vnclVlntAcomp = 0;

		$atividade_acompanhamento = 1;  // Triagem
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$atuacao_voluntario_acompanhamento = 3;				// Visitador

		// Verificar se há Relatório de Triagem em Andamento //
		$verificaTriagemVisita0 = Container::getModel('TbAcompFml');
		$verificaTriagemVisita0->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$verificaTriagemVisita0->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita0->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita0->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase0 = $verificaTriagemVisita0->getQtdTriagemVisita();

		if ($verificaTriagemVisitaBase0['seql_acomp'] > 0) {
			for ($i = 1; $i <= 8; $i++) {
				// Verificar se a Triagem atual está em Andamento //
				$verificaTriagem0 = Container::getModel('TbSegmtoTriagemFml');
				$verificaTriagem0->__set('codFamilia', $_POST['cb_familia_escolhida']);
				$verificaTriagem0->__set('seqlAcomp', $verificaTriagemVisitaBase0['seql_acomp']);
				$verificaTriagem0->__set('codSegmtoTriagem', $i);
				$verificaTriagemBase0 = $verificaTriagem0->getQtdSegmentoTriagem();

				if ($verificaTriagemBase0['qtde'] > 0) {
					switch ($i) {
						case 1:
							$this->view->segmento1 = 1;
							break;
						case 2:
							$this->view->segmento2 = 1;
							break;
						case 3:
							$this->view->segmento3 = 1;
							break;
						case 4:
							$this->view->segmento4 = 1;
							break;
						case 5:
							$this->view->segmento5 = 1;
							break;
						case 6:
							$this->view->segmento6 = 1;
							break;
						case 7:
							$this->view->segmento7 = 1;
							break;
						case 8:
							$this->view->segmento8 = 1;
							break;
					}
				}
			}
		}			

		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
		}

		$this->render('fAAlterarRTMenu');
			
		
	}	// Fim da function fAAlterarRTMenu

// ====================================================== //	

	public function fAAlterarRTEducacaoMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 1;

		$this->fAObtemDadosTriagem();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'cd_freq_crianca_adoles_escola' => $this->obtemDadosTriagemBase['cd_freq_crianca_adoles_escola'],
			'dsc_mtvo_freq_escolar' => $this->obtemDadosTriagemBase['dsc_mtvo_freq_escolar'],
			'dsc_desemp_estudo' => $this->obtemDadosTriagemBase['dsc_desemp_estudo'],
			'cd_interes_motiva_voltar_estudar' => $this->obtemDadosTriagemBase['cd_interes_motiva_voltar_estudar'],
			'dsc_curso_interes_fml' => $this->obtemDadosTriagemBase['dsc_curso_interes_fml'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'origem' => $_POST['origem']
		);

		$this->render('fAAlterarRTEducacaoMenu');

	}	// Fim da function fAAlterarRTEducacaoMenu		

// ====================================================== //	

	public function fAAlterarRTEducacaoBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();

		// Atualiza na tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
		$alteraTbSegmtoTriagemFml->__set('cd_freq_crianca_adoles_escola', $_POST['cd_freq_crianca_adoles_escola']);
		$alteraTbSegmtoTriagemFml->__set('dsc_mtvo_freq_escolar', $_POST['dsc_mtvo_freq_escolar']);
		$alteraTbSegmtoTriagemFml->__set('dsc_desemp_estudo', $_POST['dsc_desemp_estudo']);
		$alteraTbSegmtoTriagemFml->__set('cd_interes_motiva_voltar_estudar', $_POST['cd_interes_motiva_voltar_estudar']);
		$alteraTbSegmtoTriagemFml->__set('dsc_curso_interes_fml', $_POST['dsc_curso_interes_fml']);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();

		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$atualizaTSTbAcompFml->updateTS();		

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTEducacaoBase		

// ====================================================== //	

	public function fAAlterarRTReligiosidadeMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 2;

		$this->fAObtemDadosTriagem();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		if ($this->obtemDadosTriagemBase['habito_prece_oracao'] == "S") {
			$habito_prece_oracao = 'sim';
		} else {
			$habito_prece_oracao = 'nao';
		}

		if ($this->obtemDadosTriagemBase['evangelho_lar'] == "S") {
			$evangelho_lar = 'sim';
		} else {
			$evangelho_lar = 'nao';
		}

		if ($this->obtemDadosTriagemBase['conhece_espiritismo'] == "S") {
			$conhece_espiritismo = 'sim';
		} else {
			$conhece_espiritismo = 'nao';
		}

		if ($this->obtemDadosTriagemBase['vont_aprox_espiritismo'] == "S") {
			$vont_aprox_espiritismo = 'sim';
		} else {
			$vont_aprox_espiritismo = 'nao';
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'dsc_religiao_fml' => $this->obtemDadosTriagemBase['dsc_religiao_fml'],
			'dsc_institu_religiosa_freqtd' => $this->obtemDadosTriagemBase['dsc_institu_religiosa_freqtd'],
			'dsc_freq_institu_religiosa' => $this->obtemDadosTriagemBase['dsc_freq_institu_religiosa'],
			'habito_prece_oracao' => $habito_prece_oracao,
			'evangelho_lar' => $evangelho_lar,
			'conhece_espiritismo' => $conhece_espiritismo,
			'vont_aprox_espiritismo' => $vont_aprox_espiritismo,
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'origem' => $_POST['origem']
		);

		$this->render('fAAlterarRTReligiosidadeMenu');

	}	// Fim da function fAAlterarRTReligiosidadeMenu		

// ====================================================== //	

	public function fAAlterarRTReligiosidadeBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();
		
		if ($_POST['habito_prece_oracao'] == 'sim') {
			$habito_prece_oracao = 'S'; 
		} else {
			$habito_prece_oracao = 'N'; 
		}

		if ($_POST['evangelho_lar'] == 'sim') {
			$evangelho_lar = 'S'; 
		} else {
			$evangelho_lar = 'N'; 
		}
		if ($_POST['conhece_espiritismo'] == 'sim') {
			$conhece_espiritismo = 'S'; 
		} else {
			$conhece_espiritismo = 'N'; 
		}
		
		if ($_POST['vont_aprox_espiritismo'] == 'sim') {
			$vont_aprox_espiritismo = 'S'; 
		} else {
			$vont_aprox_espiritismo = 'N'; 
		}

		// Altera tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);			
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
		$alteraTbSegmtoTriagemFml->__set('dsc_religiao_fml', $_POST['dsc_religiao_fml']);
		$alteraTbSegmtoTriagemFml->__set('dsc_institu_religiosa_freqtd', $_POST['dsc_institu_religiosa_freqtd']);
		$alteraTbSegmtoTriagemFml->__set('dsc_freq_institu_religiosa', $_POST['dsc_freq_institu_religiosa']);
		$alteraTbSegmtoTriagemFml->__set('habito_prece_oracao', $habito_prece_oracao);
		$alteraTbSegmtoTriagemFml->__set('evangelho_lar', $evangelho_lar);
		$alteraTbSegmtoTriagemFml->__set('conhece_espiritismo', $conhece_espiritismo);
		$alteraTbSegmtoTriagemFml->__set('vont_aprox_espiritismo', $vont_aprox_espiritismo);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();

		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);		
		$atualizaTSTbAcompFml->updateTS();		

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTReligiosidadeBase		


// ====================================================== //	

	public function fAAlterarRTMoradiaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 3;

		$this->fAObtemDadosTriagem();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		if ($this->obtemDadosTriagemBase['exist_anim_inset_insal_perig'] == "S") {
			$exist_anim_inset_insal_perig = 'sim';
		} else {
			$exist_anim_inset_insal_perig = 'nao';
		}

		if ($this->obtemDadosTriagemBase['exist_anim_estima'] == "S") {
			$exist_anim_estima = 'sim';
		} else {
			$exist_anim_estima = 'nao';
		}

		if ($this->obtemDadosTriagemBase['vacina_anti_rabica_anim_estima'] == "S") {
			$vacina_anti_rabica_anim_estima = 'sim';
		} else if ($this->obtemDadosTriagemBase['vacina_anti_rabica_anim_estima'] == "N") {
			$vacina_anti_rabica_anim_estima = 'nao';
		} else {
			$vacina_anti_rabica_anim_estima = 'naoseaplica';
		}


		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,		
			'dsc_casa' => $this->obtemDadosTriagemBase['dsc_casa'],
			'exist_anim_inset_insal_perig' => $exist_anim_inset_insal_perig,
			'dsc_anim_inset_insal_perig' => $this->obtemDadosTriagemBase['dsc_anim_inset_insal_perig'],
			'exist_anim_estima' => $exist_anim_estima,
			'dsc_anim_estima' => $this->obtemDadosTriagemBase['dsc_anim_estima'],
			'vacina_anti_rabica_anim_estima' => $vacina_anti_rabica_anim_estima,
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'cd_agua_moradia' => $this->obtemDadosTriagemBase['cd_agua_moradia'],
			'cd_esgoto_moradia' => $this->obtemDadosTriagemBase['cd_esgoto_moradia'],
			'origem' => $_POST['origem']
		);

		$this->render('fAAlterarRTMoradiaMenu');

	}	// Fim da function fAAlterarRTMoradiaMenu		

// ====================================================== //	

	public function fAAlterarRTMoradiaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();

		if ($_POST['exist_anim_inset_insal_perig'] == 'sim') {
			$exist_anim_inset_insal_perig = 'S'; 
		} else {
			$exist_anim_inset_insal_perig = 'N'; 
		}

		if ($_POST['exist_anim_estima'] == 'sim') {
			$exist_anim_estima = 'S'; 
		} else {
			$exist_anim_estima = 'N'; 
		}

		if ($_POST['vacina_anti_rabica_anim_estima'] == 'sim') {
			$vacina_anti_rabica_anim_estima = 'S'; 
		} else if ($_POST['vacina_anti_rabica_anim_estima'] == 'nao') {
			$vacina_anti_rabica_anim_estima = 'N'; 
		} else if ($_POST['vacina_anti_rabica_anim_estima'] == 'naoseaplica') {
			$vacina_anti_rabica_anim_estima = 'NA'; 
		}

		// Altera na tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
		$alteraTbSegmtoTriagemFml->__set('dsc_casa', $_POST['dsc_casa']);
		$alteraTbSegmtoTriagemFml->__set('exist_anim_inset_insal_perig', $exist_anim_inset_insal_perig);
		$alteraTbSegmtoTriagemFml->__set('dsc_anim_inset_insal_perig', $_POST['dsc_anim_inset_insal_perig']);
		$alteraTbSegmtoTriagemFml->__set('exist_anim_estima', $exist_anim_estima);
		$alteraTbSegmtoTriagemFml->__set('dsc_anim_estima', $_POST['dsc_anim_estima']);
		$alteraTbSegmtoTriagemFml->__set('vacina_anti_rabica_anim_estima', $vacina_anti_rabica_anim_estima);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->__set('cd_agua_moradia', $_POST['cd_agua_moradia']);
		$alteraTbSegmtoTriagemFml->__set('cd_esgoto_moradia', $_POST['cd_esgoto_moradia']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();

		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$atualizaTSTbAcompFml->updateTS();		

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTMoradiaBase		

// ====================================================== //	

	public function fAAlterarRTSaudeMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 4;

		$this->fAObtemDadosTriagem();
		
		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],		
			'dt_acomp' => $data_f,
			'dsc_cndc_saude_membros_fml' => $this->obtemDadosTriagemBase['dsc_cndc_saude_membros_fml'],
			'dsc_carteira_vacina_crianca' => $this->obtemDadosTriagemBase['dsc_carteira_vacina_crianca'],
			'dsc_doenca_cronica_fml' => $this->obtemDadosTriagemBase['dsc_doenca_cronica_fml'],
			'dsc_restricao_alimentar' => $this->obtemDadosTriagemBase['dsc_restricao_alimentar'],
			'dsc_higiene_pessoal' => $this->obtemDadosTriagemBase['dsc_higiene_pessoal'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'origem' => $_POST['origem']			
		);

		$this->render('fAAlterarRTSaudeMenu');

	}	// Fim da function fAAlterarRTSaudeMenu		

// ====================================================== //	

	public function fAAlterarRTSaudeBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();

		// Altera tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);		
		$alteraTbSegmtoTriagemFml->__set('dsc_cndc_saude_membros_fml', $_POST['dsc_cndc_saude_membros_fml']);
		$alteraTbSegmtoTriagemFml->__set('dsc_carteira_vacina_crianca', $_POST['dsc_carteira_vacina_crianca']);
		$alteraTbSegmtoTriagemFml->__set('dsc_doenca_cronica_fml', $_POST['dsc_doenca_cronica_fml']);
		$alteraTbSegmtoTriagemFml->__set('dsc_restricao_alimentar', $_POST['dsc_restricao_alimentar']);
		$alteraTbSegmtoTriagemFml->__set('dsc_higiene_pessoal', $_POST['dsc_higiene_pessoal']);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();

		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$atualizaTSTbAcompFml->updateTS();		

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTSaudeBase		

// ====================================================== //	

	public function fAAlterarRTDespesaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 5;

		$this->fAObtemDadosTriagem();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			
			'cd_tip_moradia' => $this->obtemDadosTriagemBase['cd_tip_moradia'],
			'dsc_dono_cedente_moradia' => $this->obtemDadosTriagemBase['dsc_dono_cedente_moradia'],
			'vlr_desp_agua' => $this->obtemDadosTriagemBase['vlr_desp_agua'],
			'vlr_desp_energia' => $this->obtemDadosTriagemBase['vlr_desp_energia'],
			'vlr_desp_iptu' => $this->obtemDadosTriagemBase['vlr_desp_iptu'],
			'vlr_desp_gas' => $this->obtemDadosTriagemBase['vlr_desp_gas'],
			'vlr_desp_condominio' => $this->obtemDadosTriagemBase['vlr_desp_condominio'],
			'vlr_desp_outra_manut' => $this->obtemDadosTriagemBase['vlr_desp_outra_manut'],
			'dsc_desp_outra_manut' => $this->obtemDadosTriagemBase['dsc_desp_outra_manut'],
			'dsc_desp_saude_medicamento' => $this->obtemDadosTriagemBase['dsc_desp_saude_medicamento'],
			'dsc_desp_educ_creche_cuidadora' => $this->obtemDadosTriagemBase['dsc_desp_educ_creche_cuidadora'],
			'dsc_desp_transporte' => $this->obtemDadosTriagemBase['dsc_desp_transporte'],
			'dsc_desp_alimenta_especial' => $this->obtemDadosTriagemBase['dsc_desp_alimenta_especial'],
			'dsc_outra_desp_geral' => $this->obtemDadosTriagemBase['dsc_outra_desp_geral'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'origem' => $_POST['origem']			
		);

		$this->render('fAAlterarRTDespesaMenu');

	}	// Fim da function fAAlterarRTDespesaMenu		

// ====================================================== //	

	public function fAAlterarRTDespesaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();

		// Altera na tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
		$alteraTbSegmtoTriagemFml->__set('cd_tip_moradia', $_POST['cd_tip_moradia']);
		$alteraTbSegmtoTriagemFml->__set('dsc_dono_cedente_moradia', $_POST['dsc_dono_cedente_moradia']);
		$alteraTbSegmtoTriagemFml->__set('vlr_desp_agua', $_POST['vlr_desp_agua']);
		$alteraTbSegmtoTriagemFml->__set('vlr_desp_energia', $_POST['vlr_desp_energia']);
		$alteraTbSegmtoTriagemFml->__set('vlr_desp_iptu', $_POST['vlr_desp_iptu']);
		$alteraTbSegmtoTriagemFml->__set('vlr_desp_gas', $_POST['vlr_desp_gas']);
		$alteraTbSegmtoTriagemFml->__set('vlr_desp_condominio', $_POST['vlr_desp_condominio']);
		$alteraTbSegmtoTriagemFml->__set('vlr_desp_outra_manut', $_POST['vlr_desp_outra_manut']);
		$alteraTbSegmtoTriagemFml->__set('dsc_desp_outra_manut', $_POST['dsc_desp_outra_manut']);
		$alteraTbSegmtoTriagemFml->__set('dsc_desp_saude_medicamento', $_POST['dsc_desp_saude_medicamento']);
		$alteraTbSegmtoTriagemFml->__set('dsc_desp_educ_creche_cuidadora', $_POST['dsc_desp_educ_creche_cuidadora']);
		$alteraTbSegmtoTriagemFml->__set('dsc_desp_transporte', $_POST['dsc_desp_transporte']);
		$alteraTbSegmtoTriagemFml->__set('dsc_desp_alimenta_especial', $_POST['dsc_desp_alimenta_especial']);
		$alteraTbSegmtoTriagemFml->__set('dsc_outra_desp_geral', $_POST['dsc_outra_desp_geral']);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();
	
		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$atualizaTSTbAcompFml->updateTS();

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTDespesaBase		

// ====================================================== //	

	public function fAAlterarRTRendaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 6;

		$this->fAObtemDadosTriagem();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'cd_tip_trab' => $this->obtemDadosTriagemBase['cd_tip_trab'],
			'vlr_renda_tip_trab' => $this->obtemDadosTriagemBase['vlr_renda_tip_trab'],
			'dsc_tip_beneficio' => $this->obtemDadosTriagemBase['dsc_tip_beneficio'],
			'vlr_renda_tip_beneficio' => $this->obtemDadosTriagemBase['vlr_renda_tip_beneficio'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'origem' => $_POST['origem']			
		);

		$this->render('fAAlterarRTRendaMenu');

	}	// Fim da function fAAlterarRTRendaMenu		

// ====================================================== //	

	public function fAAlterarRTRendaBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();
		
		// Altera na tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);		
		$alteraTbSegmtoTriagemFml->__set('cd_tip_trab', $_POST['cd_tip_trab']);
		$alteraTbSegmtoTriagemFml->__set('vlr_renda_tip_trab', $_POST['vlr_renda_tip_trab']);
		$alteraTbSegmtoTriagemFml->__set('dsc_tip_beneficio', $_POST['dsc_tip_beneficio']);
		$alteraTbSegmtoTriagemFml->__set('vlr_renda_tip_beneficio', $_POST['vlr_renda_tip_beneficio']);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();

		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$atualizaTSTbAcompFml->updateTS();		
	

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTRendaBase		

// ====================================================== //	

	public function fAAlterarRTCapProfissionalMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 7;

		$this->fAObtemDadosTriagem();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],		
			'dt_acomp' => $data_f,
			'dsc_expect_fml_capacit_profi' => $this->obtemDadosTriagemBase['dsc_expect_fml_capacit_profi'],
			'dsc_curso_intere_profi_tecnico' => $this->obtemDadosTriagemBase['dsc_curso_intere_profi_tecnico'],
			'dsc_projeto_gera_renda_extra' => $this->obtemDadosTriagemBase['dsc_projeto_gera_renda_extra'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'origem' => $_POST['origem']			
		);

		$this->render('fAAlterarRTCapProfissionalMenu');

	}	// Fim da function fAAlterarRTCapProfissionalMenu		

// ====================================================== //	

	public function fAAlterarRTCapProfissionalBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();

		// Altera na tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
		$alteraTbSegmtoTriagemFml->__set('dsc_expect_fml_capacit_profi', $_POST['dsc_expect_fml_capacit_profi']);
		$alteraTbSegmtoTriagemFml->__set('dsc_curso_intere_profi_tecnico', $_POST['dsc_curso_intere_profi_tecnico']);
		$alteraTbSegmtoTriagemFml->__set('dsc_projeto_gera_renda_extra', $_POST['dsc_projeto_gera_renda_extra']);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();
	
		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$atualizaTSTbAcompFml->updateTS();
		

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTCapProfissionalBase		

// ====================================================== //	

	public function fAAlterarRTAspectoIntMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 8;

		$this->fAObtemDadosTriagem();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'dsc_aspecto_intimo' => $this->obtemDadosTriagemBase['dsc_aspecto_intimo'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'origem' => $_POST['origem']			
		);

		$this->render('fAAlterarRTAspectoIntMenu');
		

	}	// Fim da function fAAlterarRTAspectoIntMenu		

// ====================================================== //	

	public function fAAlterarRTAspectoIntBase() {
		
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTTratamento();

		// Altera na tabela tb_segmto_triagem_fml
		$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
		$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
		$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
		$alteraTbSegmtoTriagemFml->__set('dsc_aspecto_intimo', $_POST['dsc_aspecto_intimo']);
		$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
		$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();

		// Atualiza timestamp de tb_acomp_fml
		$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
		$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
		$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$atualizaTSTbAcompFml->updateTS();
		

		$this->fAAlterarRTRetorno();

	}	// Fim da function fAAlterarRTAspectoIntBase		

// ====================================================== //	

	public function fAAlterarRTVoluntario() {
	
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		$this->fAAlterarRTVoluntarioPOST();

	} // Fim da function fAAlterarRTVoluntario

// ====================================================== //	

	public function fAAlterarRTVoluntarioBase() {

		$this->validaAutenticacao();	

		// Busca Nome Voluntário
		$nomeVoluntario = Container::getModel('TbVlnt');
		$nomeVoluntario->__set('id', $_POST['cb_voluntario_escolhido']);
		$nomeVlnt = $nomeVoluntario->getInfoVoluntario();

		// Excluir Voluntário em tb_vncl_vlnt_acomp_fml
		$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$alteraVoluntarioTVVAF->__set('cd_vlnt', $_POST['cb_voluntario_escolhido']);
		$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
		$alteraVoluntarioTVVAF->deleteTVVAF();

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 1;

		$this->view->atuacao = 'Visitador';
		$this->view->voluntario = $_POST['cb_voluntario_escolhido'];
		$this->view->nomeVoluntario = $nomeVlnt['nm_vlnt'];
		
		$this->fAAlterarRTVoluntarioPOST();

	} // Fim da function fAAlterarRTVoluntarioBase

// ====================================================== //	

	public function fAAlterarRTVoluntarioPOST() {

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		// Buscar Voluntários Vinculados no acompanhamento com cd_atua_vlnt_acomp = 3 (Voluntário)
		$obtemVoluntariosBase = Container::getModel('TbVnclVlntAcompFml');
		$obtemVoluntariosBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$obtemVoluntariosBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$obtemVoluntariosBase->__set('cd_atua_vlnt_acomp', 3);
		$obtemVoluntarios = $obtemVoluntariosBase->getVoluntariosVinculoAcomp();

		$this->view->dadosVoluntarios = array ();

		foreach ($obtemVoluntarios as $index => $arr) {
			if ($arr['cd_atua_vlnt_acomp'] == 3) {
				$cd_atua_vlnt_acomp = 'Visitador';
			} else if ($arr['cd_atua_vlnt_acomp'] == 2) {
				$cd_atua_vlnt_acomp = 'Visitador e Relator Relatório';
			} else {
				$cd_atua_vlnt_acomp = 'Revisor Relatório';
			}

			array_push($this->view->dadosVoluntarios, array (
					'cd_vlnt' => $arr['cd_vlnt'],
					'nm_vlnt' => $arr['nm_vlnt'],
					'cd_atua_vlnt_acomp' => $arr['cd_atua_vlnt_acomp'],
					'cd_atua_vlnt_acompD' => $cd_atua_vlnt_acomp
			));                               
		}

		// Para compor os dados do Grupo e Subgrupo acima da tabela
		$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
		$this->view->nomeGrupo = $_POST['nm_grp'];
		$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
		$this->view->nomeSubgrupo = $_POST['nm_sbgrp'];
		$this->view->codFamilia = $_POST['cb_familia_escolhida'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
		$this->view->seqlAcomp = $seqlAcomp->__get('seql_max');
		$this->view->origem = $_POST['origem'];

		$this->render('fAAlterarRTVoluntario');

	} // Fim da function fAAlterarRTVoluntarioPOST

// ====================================================== //	

	public function fAObtemDadosTriagem() {

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Obtem dados da Triagem
		$obtemDadosTriagem = Container::getModel('TbSegmtoTriagemFml');
		$obtemDadosTriagem->__set('cd_fml', $_POST['cd_fml']);
		$obtemDadosTriagem->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$obtemDadosTriagem->__set('cd_segmto_triagem', $this->view->segmento);
		$this->obtemDadosTriagemBase = $obtemDadosTriagem->getDadosSegmentoTriagem();

	} // Fim da function fAObtemDadosTriagem

// ====================================================== //	

	public function fAAlterarRTTratamento() {

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

		switch ($_POST['cd_segmto']) {
			case 1:
				$this->view->nomeSegmento = "Educação";
				break;
			case 2:
				$this->view->nomeSegmento = "Religiosidade";
				break;
			case 3:
				$this->view->nomeSegmento = "Moradia";
				break;
			case 4:
				$this->view->nomeSegmento = "Saúde";
				break;
			case 5:
				$this->view->nomeSegmento = "Despesa";
				break;
			case 6:
				$this->view->nomeSegmento = "Renda";
				break;
			case 7:
				$this->view->nomeSegmento = "Capacitação Profissional";
				break;
			case 8:
				$this->view->nomeSegmento = "Aspectos Íntimos";
				break;
		}

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		$this->view->retorno = array (
				'seqlAcomp' => $seqlAcomp->__get('seql_max'),
				'dataAcomp_formatada' => $dataAcomp_formatada
		);

	}	// Fim da function fAAlterarRTTratamento		

// ====================================================== //	

	public function fAAlterarRTRetorno() {

		if (!isset($_POST['origem'])) {
			$origem = 'alteracaoRelatorio';
		} else {
			$origem = $_POST['origem'];
		}

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cd_grp'], 
				'cb_grupo_escolhido' => $_POST['cd_grp'], 
				'nm_grp' => $_POST['nm_grp'], 
				'cd_sbgrp' => $_POST['cd_sbgrp'], 
				'cb_subgrupo_escolhido' => $_POST['cd_sbgrp'], 
				'nm_sbgrp' => $_POST['nm_sbgrp'], 
				'cd_fml' => $_POST['cd_fml'], 
				'cb_familia_escolhida' => $_POST['cd_fml'], 
				'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
				'origem' => $origem
		);

		$this->view->erroValidacao = 2;

		$this->view->segmento1 = 0;
		$this->view->segmento2 = 0;
		$this->view->segmento3 = 0;
		$this->view->segmento4 = 0;
		$this->view->segmento5 = 0;
		$this->view->segmento6 = 0;
		$this->view->segmento7 = 0;
		$this->view->segmento8 = 0;
		$this->view->vnclVlntAcomp = 0;

		$atividade_acompanhamento = 1;  // Triagem
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$atuacao_voluntario_acompanhamento = 3;				// Visitador		

		// Verificar se há Relatório de Triagem em Andamento //
		$verificaTriagemVisita0 = Container::getModel('TbAcompFml');
		$verificaTriagemVisita0->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$verificaTriagemVisita0->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita0->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita0->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase0 = $verificaTriagemVisita0->getQtdTriagemVisita();

		if ($verificaTriagemVisitaBase0['seql_acomp'] > 0) {
			for ($i = 1; $i <= 8; $i++) {
				// Verificar se a Triagem atual está em Andamento //
				$verificaTriagem0 = Container::getModel('TbSegmtoTriagemFml');
				$verificaTriagem0->__set('codFamilia', $_POST['cb_familia_escolhida']);
				$verificaTriagem0->__set('seqlAcomp', $verificaTriagemVisitaBase0['seql_acomp']);
				$verificaTriagem0->__set('codSegmtoTriagem', $i);
				$verificaTriagemBase0 = $verificaTriagem0->getQtdSegmentoTriagem();

				if ($verificaTriagemBase0['qtde'] > 0) {
					switch ($i) {
						case 1:
							$this->view->segmento1 = 1;
							break;
						case 2:
							$this->view->segmento2 = 1;
							break;
						case 3:
							$this->view->segmento3 = 1;
							break;
						case 4:
							$this->view->segmento4 = 1;
							break;
						case 5:
							$this->view->segmento5 = 1;
							break;
						case 6:
							$this->view->segmento6 = 1;
							break;
						case 7:
							$this->view->segmento7 = 1;
							break;
						case 8:
							$this->view->segmento8 = 1;
							break;
					}
				}
			}
		}	

		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
		}

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);			
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$atividade_acompanhamento = 1;  // Triagem

		if ($origem == 'conclusaoRelatorio' || $origem == 'alteracaoRelatorio') {
			$estado_acompanhamento_ini = 1;	// Pendente de Término de revisão
  		$estado_acompanhamento_fim = 1;	// Pendente de Término de revisão
		} else {
			$estado_acompanhamento_ini = 2;	// Pendente de Término de revisão
  		$estado_acompanhamento_fim = 2;	// Pendente de Término de revisão
		}

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
		$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
		$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		// Obter famílias em atendimento passíveis de serem substituídas 
		$familiasEmAtendimentoBase = Container::getModel('TbFml');
		$familiasEmAtendimentoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$familiasEmAtendimentoBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$familiasEmAtendimento = $familiasEmAtendimentoBase->getFamiliasEmAtendimento();
			
		$this->view->familiasPassiveisSubstituicao = $familiasEmAtendimento;

		if ($dataAcomp['dsc_mtv_cd_avalia_triagem'] === null) {
			$dsc_mtv_cd_avalia_triagem = '';
		} else {
			$dsc_mtv_cd_avalia_triagem = $dataAcomp['dsc_mtv_cd_avalia_triagem'];
		}

		if ($dataAcomp['dsc_consid_finais_triagem'] == ' ') {
			$dsc_consid_finais_triagem = '';
		} else {
			$dsc_consid_finais_triagem = $dataAcomp['dsc_consid_finais_triagem'];
		}

		if ($dataAcomp['cd_fml_subs_triagem'] == 0) {
			$cd_fml_subs_triagem = '';
		} else {
			$cd_fml_subs_triagem = $dataAcomp['cd_fml_subs_triagem'];
		}

		$this->view->dadosAcompanhamento = array (
						'cd_grp' => $_POST['cb_grupo_escolhido'], 
						'nm_grp' => $_POST['nm_grp'], 
						'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
						'nm_sbgrp' => $_POST['nm_sbgrp'], 
						'cd_fml' => $_POST['cb_familia_escolhida'],
						'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
						'dt_acomp' => $dataAcomp_f,
						'cd_avalia_triagem' => $dataAcomp['cd_avalia_triagem'],
						'dsc_mtv_cd_avalia_triagem' => $dsc_mtv_cd_avalia_triagem,
						'cd_crit_engjto' => $dadosFamiliaBase['cd_crit_engjto'],
						'dsc_consid_finais_triagem' => $dsc_consid_finais_triagem,
						'cd_fml_subs_triagem' => $cd_fml_subs_triagem,
						'seql_acomp' => $seqlAcomp->__get('seql_max'),
						'origem' => $origem
		);

		if ($origem == 'conclusaoRelatorio') {
			$this->render('fAConcluirRTMenu');		
		} else if ($origem == 'conclusaoRevisao') {
			$this->render('fARevisarRTMenu');					
		} else {
			$this->render('fAAlterarRTMenu');
		}
	
	}	// Fim da function fAAlterarRTRetorno		

// ====================================================== //	
		
	public function fAPreConcluirRT() {
		
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

			$this->render('fAPreConcluirRT');
		}
	}	// Fim da function fAPreConcluirRT


// ====================================================== //	
	
	public function fAConcluirRT() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

		$atividade_acompanhamento = 1;  // Triagem
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_situacao_fml = 2;  			// Aguardando Triagem

		// Valida se Grupo e subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreConcluirRT');

		} else {

			$this->nivel_atuacao_requerido = 5;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreConcluirRT');				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreConcluirRT');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];
				
				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp',  $atividade_acompanhamento);
				$familiasVnclGrupoBase->__set('codInicialAcomp',  $estado_acompanhamento_ini);
				$familiasVnclGrupoBase->__set('codFinalAcomp',  $estado_acompanhamento_fim);
				$familiasVnclGrupoBase->__set('codEstSituFml',  $estado_situacao_fml);

				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompanhamento();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$ha_todos_segmentos = 0;
					
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			
	
						// Verificar se há todos os segmentos cadastrados
						$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
						$verificaTriagem->__set('codFamilia', $arr['cd_fmlID']);
						$verificaTriagem->__set('seqlAcomp', $seqlAcomp->__get('seql_max'));
						$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagemAll();

						if ($verificaTriagemBase['qtde'] == 8) {
							$ha_todos_segmentos = 1;
						}

						// Não há todos os segmentos cadastrados ou vinculo de voluntário Revisor e Relator
						if ($ha_todos_segmentos == 0) {
 							$ha_todos_segmentos = 0;
						
						}	else {

							// Formatar para aparecer na tela o significado
							$this->obtemEstSituFml($arr['cd_est_situ_fml']);
							$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

							// Obtem Sequencial de Acompanhamento Atual
							$seqlAcomp = Container::getModel('TbAcompFml');
							$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
							$seqlAcomp->getSequencial();			

							// Obter dados acompanhamento //
							$dataAcompBase = Container::getModel('TbAcompFml');
							$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
							$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));							
							$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
							$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
							$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
							$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

						  $dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

							array_push($this->view->familia, array (
									'cd_grp' => $_POST['cb_grupo_escolhido'], 
									'nm_grp' => $nomeGrupo, 
									'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
									'nm_sbgrp' => $nomeSubgrupo, 
									'cd_fml' => $arr['cd_fmlID'],
									'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
									'dt_cadastro_fml' => $dataAcomp_f,
									'cd_est_situ_fml' => $cd_est_situ_fml,
									'ptc_atendto_fml' => $arr['ptc_atendto_fml'],
									'pos_ranking_atendto_fml' => $arr['pos_ranking_atendto_fml']
							));
						}

						// Para compor os dados do Grupo e Subgrupo acima da tabela
						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;
					}

					// Verifica se há famílias a terem relatório concluído
					if (count($this->view->familia) == 0) {
						$this->view->erroValidacao = 4;

						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;

						$this->render('fAPreConcluirRT');
					} else {
						$this->render('fAConcluirRT');	
					}
				
				} else {

					if ($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio == 0) {
						$this->view->erroValidacao = 3;
					} else {
						$this->view->erroValidacao = 7;
					}

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreConcluirRT');
				}
			}	
		}
	
	}	// Fim da function fAConcluirRT

// ====================================================== //	
	
	public function fAConcluirRTMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->nivel_atuacao_acomp_requerido = 2;	

		$this->validaAcessoAcompanhamentoRelatorio();

		// Não está na tabela de Vinculo de Acompanhamento
		if ($this->retornoValidaAcessoAcompanhamentoRelatorio == 1) {
 
			// Chama novamente a function para dar refresh na tela
			session_write_close();
			$this->fAConcluirRT();

		// Está na tabela de Vinculo de Acompanhamento, mas não é Revisor
		} else if ($this->retornoValidaAcessoAcompanhamentoRelatorio == 2) {
			// Chama novamente a function para dar refresh na tela
			session_write_close();
			$this->fAConcluirRT();

		} else {

			// Buscar Nome de Grupo e Subgrupo
			$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
			$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
			
			$nomeGrupo = $dadosGS['nome_grupo'];
			$nomeSubgrupo = $dadosGS['nome_subgrupo'];

			// Buscar dados Família
			$dadosFamilia = Container::getModel('TbFml');
			$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
			$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

			// Obtem Sequencial de Acompanhamento Atual
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$seqlAcomp->getSequencial();			

			$atividade_acompanhamento = 1;  // Triagem
			$estado_acompanhamento_ini = 1;	// Pendente de Término de revisão
  			$estado_acompanhamento_fim = 1;	// Pendente de Término de revisão

			// Obter dados acompanhamento //
			$dataAcompBase = Container::getModel('TbAcompFml');
			$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
			$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
			$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
			$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
			$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

			$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

			// Obter famílias em atendimento passíveis de serem substituídas 
			$familiasEmAtendimentoBase = Container::getModel('TbFml');
			$familiasEmAtendimentoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$familiasEmAtendimentoBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$familiasEmAtendimento = $familiasEmAtendimentoBase->getFamiliasEmAtendimento();
				
			$this->view->familiasPassiveisSubstituicao = $familiasEmAtendimento;

			if ($dataAcomp['dsc_mtv_cd_avalia_triagem'] === null) {
				$dsc_mtv_cd_avalia_triagem = '';
			} else {
				$dsc_mtv_cd_avalia_triagem = $dataAcomp['dsc_mtv_cd_avalia_triagem'];
			}

			if ($dataAcomp['dsc_consid_finais_triagem'] == ' ') {
				$dsc_consid_finais_triagem = '';
			} else {
				$dsc_consid_finais_triagem = $dataAcomp['dsc_consid_finais_triagem'];
			}

			if ($dataAcomp['cd_fml_subs_triagem'] == 0) {
				$cd_fml_subs_triagem = '';
			} else {
				$cd_fml_subs_triagem = $dataAcomp['cd_fml_subs_triagem'];
			}

			$this->view->dadosAcompanhamento = array (
							'cd_grp' => $_POST['cb_grupo_escolhido'], 
							'nm_grp' => $nomeGrupo, 
							'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
							'nm_sbgrp' => $nomeSubgrupo, 
							'cd_fml' => $_POST['cb_familia_escolhida'],
							'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
							'dt_acomp' => $dataAcomp_f,
							'cd_avalia_triagem' => $dataAcomp['cd_avalia_triagem'],
							'dsc_mtv_cd_avalia_triagem' => $dsc_mtv_cd_avalia_triagem,
							'cd_crit_engjto' => $dadosFamiliaBase['cd_crit_engjto'],
							'dsc_consid_finais_triagem' => $dsc_consid_finais_triagem,
							'cd_fml_subs_triagem' => $cd_fml_subs_triagem,
							'seql_acomp' => $seqlAcomp->__get('seql_max')
			);

			$this->render('fAConcluirRTMenu');				

		}

	}	// Fim da function fAConcluirRTMenu

// ====================================================== //	
	
	public function fAConcluirRTBaseAtualiza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		if (empty($_POST['cd_fml_subs_triagem'])) {
			$cd_fml_subs_triagem = 0;
		} else {
			$cd_fml_subs_triagem = $_POST['cd_fml_subs_triagem'];
		}

		// Atualiar tb_acomp_fml
		$atualizaTbAcomp = Container::getModel('TbAcompFml');
		$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbAcomp->__set('seql_acomp', $_POST['seql_acomp']);
		$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
		$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
		$atualizaTbAcomp->__set('cd_fml_subs_triagem', $cd_fml_subs_triagem);
		$atualizaTbAcomp->__set('dsc_consid_finais_triagem', $_POST['dsc_consid_finais_triagem']);
		$atualizaTbAcomp->__set('cd_est_acomp', 1);
		$atualizaTbAcomp->updateRTAtualiza();

		// Atualizar tb_fml
		$atualizaTbFml = Container::getModel('TbFml');
		$atualizaTbFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbFml->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
		$atualizaTbFml->updateCritEngajamentoFamilia();
		
		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 3;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAConcluirRT();				

	}	// Fim da function fAConcluirRTBaseAtualiza

// ====================================================== //	
	
	public function fAConcluirRTBaseConclui() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		if (empty($_POST['cd_fml_subs_triagem'])) {
			$cd_fml_subs_triagem = 0;
		} else {
			if ($_POST['cd_fml_subs_triagem'] == 'Nenhuma') {
				$cd_fml_subs_triagem = 0;
			} else {
				$cd_fml_subs_triagem = $_POST['cd_fml_subs_triagem'];
			}
		}

		// Somente gravar informação de família substituida quando for para atendimento
		if ($_POST['cd_avalia_triagem'] != 3) {
			$cd_fml_subs_triagem = 0;
		}

		// Atualiar tb_acomp_fml
		$atualizaTbAcomp = Container::getModel('TbAcompFml');
		$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbAcomp->__set('seql_acomp', $_POST['seql_acomp']);
		$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
		$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
		$atualizaTbAcomp->__set('cd_fml_subs_triagem', $cd_fml_subs_triagem);
		$atualizaTbAcomp->__set('dsc_consid_finais_triagem', $_POST['dsc_consid_finais_triagem']);
		$atualizaTbAcomp->__set('cd_est_acomp', 2);
		$atualizaTbAcomp->updateRTAtualiza();

		// Atualizar tb_fml
		$atualizaTbFml = Container::getModel('TbFml');
		$atualizaTbFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbFml->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
		$atualizaTbFml->updateCritEngajamentoFamilia();

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 4;
		$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 1;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAConcluirRT();				

	}	// Fim da function fAConcluirRTBaseConclui


// ====================================================== //	

	public function fAPreRevisarRT() {
		
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

			$this->render('fAPreRevisarRT');
		}
	}	// Fim da function fAPreRevisarRT

// ====================================================== //	

	public function fARevisarRT() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		}

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaRevisarRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaRevisarRelatorio = 0;
		}

		$atividade_acompanhamento = 1;  // Triagem
		$estado_acompanhamento_ini = 2;	// Pendente de Término de revisão
		$estado_acompanhamento_fim = 2;	// Pendente de Término de revisão
		$estado_situacao_fml = 2;  			// Aguardando Triagem

		// Valida se Grupo e subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreRevisarRT');

		} else {

			$this->nivel_atuacao_requerido = 3;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreRevisarRT');				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreRevisarRT');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];
				
				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp',  $atividade_acompanhamento);
				$familiasVnclGrupoBase->__set('codInicialAcomp',  $estado_acompanhamento_ini);
				$familiasVnclGrupoBase->__set('codFinalAcomp',  $estado_acompanhamento_fim);
				$familiasVnclGrupoBase->__set('codEstSituFml',  $estado_situacao_fml);

				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompanhamento();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$ha_todos_segmentos = 0;
					
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			
	
						// Verificar se há todos os segmentos cadastrados
						$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
						$verificaTriagem->__set('codFamilia', $arr['cd_fmlID']);
						$verificaTriagem->__set('seqlAcomp', $seqlAcomp->__get('seql_max'));
						$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagemAll();

						if ($verificaTriagemBase['qtde'] == 8) {
							$ha_todos_segmentos = 1;
						}

						// Não há todos os segmentos cadastrados ou vinculo de voluntário Revisor e Relator
						if ($ha_todos_segmentos == 0) {
 							$ha_todos_segmentos = 0;
						
						}	else {

							$this->obtemEstSituFml($arr['cd_est_situ_fml']);
							$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

							// Obtem Sequencial de Acompanhamento Atual
							$seqlAcomp = Container::getModel('TbAcompFml');
							$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
							$seqlAcomp->getSequencial();			

							// Obter dados acompanhamento //
							$dataAcompBase = Container::getModel('TbAcompFml');
							$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
							$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));							
							$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
							$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
							$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
							$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

						  	$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

							array_push($this->view->familia, array (
									'cd_grp' => $_POST['cb_grupo_escolhido'], 
									'nm_grp' => $nomeGrupo, 
									'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
									'nm_sbgrp' => $nomeSubgrupo, 
									'cd_fml' => $arr['cd_fmlID'],
									'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
									'dt_cadastro_fml' => $dataAcomp_f,
									'cd_est_situ_fml' => $cd_est_situ_fml,
									'ptc_atendto_fml' => $arr['ptc_atendto_fml'],
									'pos_ranking_atendto_fml' => $arr['pos_ranking_atendto_fml']
							));
						}

						// Para compor os dados do Grupo e Subgrupo acima da tabela
						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;
					}

					// Verifica se há famílias a terem relatório revisado
						if (count($this->view->familia) == 0) {
							$this->view->erroValidacao = 4;

							$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
							$this->view->nomeGrupo = $nomeGrupo;
							$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
							$this->view->nomeSubgrupo = $nomeSubgrupo;

							$this->render('fAPreRevisarRT');
						} else {
							$this->render('fARevisarRT');	
						}
				
				} else {

					if ($this->view->trataMsgQdoNaoHaMaisFmlParaRevisarRelatorio == 0) {
						$this->view->erroValidacao = 3;
					} else {
						$this->view->erroValidacao = 7;
					}

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreRevisarRT');
				}
			}	
		}
	
	}	// Fim da function fARevisarRT


// ====================================================== //	
	
	public function fARevisarRTMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();

		// Verificar se não está na tabela tb_vncl_vlnt_acomp_fml, para o caso de ter sido cadastrada como
		// visitador/visitador relator e depois trocado o vínculo na tabela tb_vncl_vlnt_grp para revisor
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecificoRevisor();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] > 0) {
			$this->view->erroValidacao = 5;

			$this->view->codFamilia = $_POST['cb_familia_escolhida'];

			// Buscar dados Família
			$dadosFamilia = Container::getModel('TbFml');
			$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
			$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

			$this->view->nomeFamilia = $dadosFamiliaBase['nm_grp_fmlr'];
			
			session_write_close();
			$this->fARevisarRT();

		} else {

			// Buscar Nome de Grupo e Subgrupo
			$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
			$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
			
			$nomeGrupo = $dadosGS['nome_grupo'];
			$nomeSubgrupo = $dadosGS['nome_subgrupo'];

			// Buscar dados Família
			$dadosFamilia = Container::getModel('TbFml');
			$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
			$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

			$atividade_acompanhamento = 1;  // Triagem
			$estado_acompanhamento_ini = 2;	// Pendente de Término de revisão
			$estado_acompanhamento_fim = 2;	// Pendente de Término de revisão

			// Obter dados acompanhamento //
			$dataAcompBase = Container::getModel('TbAcompFml');
			$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
			$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
			$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
			$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
			$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

			$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

			// Obter famílias em atendimento passíveis de serem substituídas 
			$familiasEmAtendimentoBase = Container::getModel('TbFml');
			$familiasEmAtendimentoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$familiasEmAtendimentoBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$familiasEmAtendimento = $familiasEmAtendimentoBase->getFamiliasEmAtendimento();
				
			$this->view->familiasPassiveisSubstituicao = $familiasEmAtendimento;

			if ($dataAcomp['dsc_mtv_cd_avalia_triagem'] === null) {
				$dsc_mtv_cd_avalia_triagem = '';
			} else {
				$dsc_mtv_cd_avalia_triagem = $dataAcomp['dsc_mtv_cd_avalia_triagem'];
			}

			if ($dataAcomp['dsc_consid_finais_triagem'] == ' ') {
				$dsc_consid_finais_triagem = '';
			} else {
				$dsc_consid_finais_triagem = $dataAcomp['dsc_consid_finais_triagem'];
			}

			if ($dataAcomp['cd_fml_subs_triagem'] == 0) {
				$cd_fml_subs_triagem = '';
			} else {
				$cd_fml_subs_triagem = $dataAcomp['cd_fml_subs_triagem'];
			}

			$this->view->dadosAcompanhamento = array (
							'cd_grp' => $_POST['cb_grupo_escolhido'], 
							'nm_grp' => $nomeGrupo, 
							'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
							'nm_sbgrp' => $nomeSubgrupo, 
							'cd_fml' => $_POST['cb_familia_escolhida'],
							'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
							'dt_acomp' => $dataAcomp_f,
							'cd_avalia_triagem' => $dataAcomp['cd_avalia_triagem'],
							'dsc_mtv_cd_avalia_triagem' => $dsc_mtv_cd_avalia_triagem,
							'cd_crit_engjto' => $dadosFamiliaBase['cd_crit_engjto'],
							'dsc_consid_finais_triagem' => $dsc_consid_finais_triagem,
							'cd_fml_subs_triagem' => $cd_fml_subs_triagem,
							'seql_acomp' => $seqlAcomp->__get('seql_max')
			);

			$this->render('fARevisarRTMenu');				
		}
	}	// Fim da function fARevisarRTMenu

// ====================================================== //	
	
	public function fARevisarRTBaseAtualiza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		if (empty($_POST['cd_fml_subs_triagem'])) {
			$cd_fml_subs_triagem = 0;
		} else {
			$cd_fml_subs_triagem = $_POST['cd_fml_subs_triagem'];
		}

		// Atualiar tb_acomp_fml
		$atualizaTbAcomp = Container::getModel('TbAcompFml');
		$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbAcomp->__set('seql_acomp', $_POST['seql_acomp']);
		$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
		$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
		$atualizaTbAcomp->__set('cd_fml_subs_triagem', $cd_fml_subs_triagem);
		$atualizaTbAcomp->__set('dsc_consid_finais_triagem', $_POST['dsc_consid_finais_triagem']);
		$atualizaTbAcomp->__set('cd_est_acomp', 2);
		$atualizaTbAcomp->updateRTAtualiza();

		// Atualizar tb_fml
		$atualizaTbFml = Container::getModel('TbFml');
		$atualizaTbFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbFml->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
		$atualizaTbFml->updateCritEngajamentoFamilia();

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Revisor na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 12, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 3;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fARevisarRT();				

	}	// Fim da function fARevisarRTBaseAtualiza

// ====================================================== //	
	
	public function fARevisarRTBaseConclui() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;
		
		if (empty($_POST['cd_fml_subs_triagem'])) {
			$cd_fml_subs_triagem = 0;
		} else {
			if ($_POST['cd_fml_subs_triagem'] == 'Nenhuma') {
				$cd_fml_subs_triagem = 0;
			} else {
				$cd_fml_subs_triagem = $_POST['cd_fml_subs_triagem'];
			}
		}

		// Somente gravar informação de família substituida quando for para atendimento
		if ($_POST['cd_avalia_triagem'] != 3) {
			$cd_fml_subs_triagem = 0;
		}

		// Atualizar tb_acomp_fml
		$atualizaTbAcomp = Container::getModel('TbAcompFml');
		$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbAcomp->__set('seql_acomp', $_POST['seql_acomp']);
		$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
		$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
		$atualizaTbAcomp->__set('cd_fml_subs_triagem', $cd_fml_subs_triagem);
		$atualizaTbAcomp->__set('dsc_consid_finais_triagem', $_POST['dsc_consid_finais_triagem']);
		$atualizaTbAcomp->__set('cd_est_acomp', 3);
		$atualizaTbAcomp->updateRTAtualiza();

		// Atualizar tb_fml - cd_crit_engjto
		$atualizaTbFml = Container::getModel('TbFml');
		$atualizaTbFml->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbFml->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
		$atualizaTbFml->updateCritEngajamentoFamilia();

		// Atualizar tb_fml - cd_est_situ_fml
		if ($_POST['cd_avalia_triagem'] == 2 || 
			  $_POST['cd_avalia_triagem'] == 3 || 
			  $_POST['cd_avalia_triagem'] == 5 ||
			  $_POST['cd_avalia_triagem'] == 6) {

			if ($_POST['cd_avalia_triagem'] == 2) {
				$cd_est_situ_fml = 2;

				// Data Recebida no Formato DD/MM/AAAA
				$data_triagem = $_POST['dt_acomp'];
				$data_triagem = substr($data_triagem, 6, 4).'-'.substr($data_triagem, 3, 2).'-'.substr($data_triagem, 0, 2);

				// Calcular a Próxima Data Visita
				$this->obtemDataProximaVisita($data_triagem, $_POST['cb_grupo_escolhido']);

				// Gerar Kit Habitual
				$this->geraKitHabitual($_POST['cd_fml'], $_POST['seql_acomp'], $this->prox_data_visita);
			
			} else if ($_POST['cd_avalia_triagem'] == 3) {
				$cd_est_situ_fml = 3;

				// Data Recebida no Formato DD/MM/AAAA
				$data_triagem = $_POST['dt_acomp'];
				$data_triagem = substr($data_triagem, 6, 4).'-'.substr($data_triagem, 3, 2).'-'.substr($data_triagem, 0, 2);

				// Calcular a Próxima Data Visita
				$this->obtemDataProximaVisita($data_triagem, $_POST['cb_grupo_escolhido']);

				// Calcular a data prevista para término do acompanhamento (últim visita)
				for ($i = 1; $i <= 6; $i++) {
					if ($i == 1) {
						$data_calculo = $data_triagem;
					} else {
						$data_calculo = $data_prev_term_acomp;
					}
					
					$data_prev_term_acomp = Funcoes::CalculaProximaDataVisita( $data_calculo, $this->semana_atuacao_grupo );
				}

				// Formata data de AAAA-MM-DD para DD/MM/AAAA
				$data_prev_term_acomp_f = Funcoes::formatarNumeros('data', $data_prev_term_acomp, 10, "AMD");

				// Atualizar tb_fml
				$atualizaTbFml1 = Container::getModel('TbFml');
				$atualizaTbFml1->__set('cd_fml', $_POST['cd_fml']);
				$atualizaTbFml1->__set('cd_atendto_fml_subs', $cd_fml_subs_triagem);
				$atualizaTbFml1->__set('dt_prev_term_acomp', $data_prev_term_acomp_f);
				$atualizaTbFml1->updateInicioAcompanhamentoFamilia();

				// Gerar Kit Habitual
				$this->geraKitHabitual($_POST['cd_fml'], $_POST['seql_acomp'], $this->prox_data_visita);

			} else if($_POST['cd_avalia_triagem'] == 5) {
				$cd_est_situ_fml = 5;
			} else {
				$cd_est_situ_fml = 6;
			}

			$atualizaTbFmlSitu = Container::getModel('TbFml');
			$atualizaTbFmlSitu->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTbFmlSitu->__set('cd_est_situ_fml', $cd_est_situ_fml);
			$atualizaTbFmlSitu->updateEstSituFamilia();
		} 

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Revisor na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 1, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 4;
		$this->view->trataMsgQdoNaoHaMaisFmlParaRevisarRelatorio = 1;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fARevisarRT();				

	}	// Fim da function fARevisarRTBaseConclui

// ====================================================== //	

	public function fAConsultarRTVoluntario() {
	
		$this->validaAutenticacao();	

		// Colocado para tratar pesquisa de Relatórios
		if ($_POST['origem'] == 'consultaRelatorios') {
			// Buscar Voluntários Vinculados no acompanhamento 
			$obtemVoluntariosBase = Container::getModel('TbVnclVlntAcompFml');
			$obtemVoluntariosBase->__set('cd_fml', $_POST['cd_fml']);
			$obtemVoluntariosBase->__set('seql_acomp', $_POST['seql_acomp']);
			$obtemVoluntarios = $obtemVoluntariosBase->getVoluntariosVinculoAcompAll();

			$seql_acomp_post = $_POST['seql_acomp'];

		} else {
			// Obtem Sequencial de Acompanhamento Atual
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getSequencial();			

			// Buscar Voluntários Vinculados no acompanhamento 
			$obtemVoluntariosBase = Container::getModel('TbVnclVlntAcompFml');
			$obtemVoluntariosBase->__set('cd_fml', $_POST['cd_fml']);
			$obtemVoluntariosBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$obtemVoluntarios = $obtemVoluntariosBase->getVoluntariosVinculoAcompAll();

			$seql_acomp_post = $seqlAcomp->__get('seql_max');
		}

		$this->view->dadosVoluntarios = array ();

		foreach ($obtemVoluntarios as $index => $arr) {
			if ($arr['cd_atua_vlnt_acomp'] == 3) {
				$cd_atua_vlnt_acomp = 'Visitador';
			} else if ($arr['cd_atua_vlnt_acomp'] == 2) {
				$cd_atua_vlnt_acomp = 'Visitador e Relator Relatório';
			} else {
				$cd_atua_vlnt_acomp = 'Revisor Relatório';
			}

			array_push($this->view->dadosVoluntarios, array (
					'cd_vlnt' => $arr['cd_vlnt'],
					'nm_vlnt' => $arr['nm_vlnt'],
					'cd_atua_vlnt_acomp' => $arr['cd_atua_vlnt_acomp'],
					'cd_atua_vlnt_acompD' => $cd_atua_vlnt_acomp
			));                               
		}

		// Para compor os dados do Grupo e Subgrupo acima da tabela
		$this->view->codGrupo = $_POST['cd_grp'];
		$this->view->nomeGrupo = $_POST['nm_grp'];
		$this->view->codSubgrupo = $_POST['cd_sbgrp'];
		$this->view->nomeSubgrupo = $_POST['nm_sbgrp'];
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
		//$this->view->seqlAcomp = $seqlAcomp->__get('seql_max');
		$this->view->seqlAcomp = $seql_acomp_post;
		$this->view->origem = $_POST['origem'];

		$this->render('fAConsultarRTVoluntario');

	} // Fim da function fAConsultarRTVoluntario

// ====================================================== //	
	
	public function fAPreIncluirRV() {
		
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

			$this->render('fAPreIncluirRV');		
		}
	}	// Fim da function fAPreIncluirRV


// ====================================================== //	
	
	public function fAIncluirRV() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Valida se Grupo foi escolhido
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreIncluirRV');

		} else {

			$this->nivel_atuacao_requerido = 5;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela tb_vncl_vlnt_grp
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreIncluirRV');				

			// Não tem a atuação necessária
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreIncluirRV');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];

				// Buscar Familias atreladas ao Grupo e Subgrupo e que estejam com cd_est_situ_fml = 3 (Em atendimento)
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('codEstSituFml',  3);				
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoRT();

				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {

						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;
						
					  $dataCadastro_f = Funcoes::formatarNumeros('data', $arr['dt_cadastro_fml'], 10, "AMD");

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'dt_cadastro_fml' => $dataCadastro_f,
								'cd_est_situ_fml' => $cd_est_situ_fml
						));
					}

					// Para compor os dados do Grupo e Subgrupo acima da tabela
					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAIncluirRV');	
				} else {
					$this->view->erroValidacao = 3;

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreIncluirRV');
				}
			}
		}	
	
	}	// Fim da function fAIncluirRV

// ====================================================== //	

	public function fAIncluirRVMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 0;

		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $nomeGrupo, 
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
				'nm_sbgrp' => $nomeSubgrupo, 
				'cd_fml' => $_POST['cb_familia_escolhida'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'] 
		);

		$this->view->vnclVlntAcomp = 0;
		$this->view->haAcomp = 0;

		$atividade_acompanhamento = 2;  							// Visita
		$estado_acompanhamento_ini = 1;								// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;								// Pendente de Término de registro de Triagem/Visita
		$atuacao_voluntario_acompanhamento = 3;				// Visitador

		// Verificar se há Relatório de Visita em Andamento //
		$verificaTriagemVisita0 = Container::getModel('TbAcompFml');
		$verificaTriagemVisita0->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$verificaTriagemVisita0->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita0->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita0->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase0 = $verificaTriagemVisita0->getQtdTriagemVisita();

		if ($verificaTriagemVisitaBase0['seql_acomp'] > 0) {
			$this->view->haAcomp = 1;
		}

		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
		}

		$this->render('fAIncluirRVMenu');

	}	// Fim da function fAIncluirRVMenu


// ====================================================== //	

	public function fAIncluirDadosRVMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => '',
			'nr_bolsa_canguru' => '',
			'vlr_doado_finan_comunhao_visita' => '',
			'vlr_doado_sbgrp_visita' => '',
			'dsc_item_doado_comunhao_visita' => '',
			'dsc_item_doado_sbgrp_visita' => '',
			'dsc_resumo_visita' => '',
			'dsc_meta_alcan' => '',
			'dsc_pend_visita' => '',
			'dsc_consid_final' => '',
			'dsc_recado_to_coordenacao' => ''
		);

		$this->render('fAIncluirDadosRVMenu');
	
	}	// Fim da function fAIncluirDadosRVMenu		

// ====================================================== //	

	public function fAIncluirDadosRVBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

 		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		
		$ha_relatorio_cadastrado = 0;
		$this->view->haAcomp = 0;

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");
		
		// Verificar se há Relatório em Andamento //
		$verificaTriagemVisita = Container::getModel('TbAcompFml');
		$verificaTriagemVisita->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagemVisita->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase = $verificaTriagemVisita->getQtdTriagemVisita();

		if (!empty($verificaTriagemVisitaBase['seql_acomp'])) {
			$ha_relatorio_cadastrado = 1;
		}			

		if ($ha_relatorio_cadastrado == 0) { 
			// Obtem Próximo Sequencial de Acompanhamento
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getProximoSequencial();			

			// Insere na tabela tb_acomp_fml
			$insereTbAcompFml = Container::getModel('TbAcompFml');
			$insereTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereTbAcompFml->__set('cd_atvd_acomp', $atividade_acompanhamento);
			$insereTbAcompFml->__set('cd_avalia_triagem', 0);
			$insereTbAcompFml->__set('dt_acomp', $dataAcomp_formatada);
			$insereTbAcompFml->__set('nr_bolsa_canguru', $_POST['nr_bolsa_canguru']);			
			$insereTbAcompFml->__set('vlr_doado_finan_comunhao_visita', $_POST['vlr_doado_finan_comunhao_visita']);			
			$insereTbAcompFml->__set('vlr_doado_sbgrp_visita', $_POST['vlr_doado_sbgrp_visita']);			
			$insereTbAcompFml->__set('dsc_item_doado_comunhao_visita', $_POST['dsc_item_doado_comunhao_visita']);			
			$insereTbAcompFml->__set('dsc_item_doado_sbgrp_visita', $_POST['dsc_item_doado_sbgrp_visita']);			
			$insereTbAcompFml->__set('dsc_resumo_visita', $_POST['dsc_resumo_visita']);			
			$insereTbAcompFml->__set('dsc_meta_alcan', $_POST['dsc_meta_alcan']);			
			$insereTbAcompFml->__set('dsc_pend_visita', $_POST['dsc_pend_visita']);			
			$insereTbAcompFml->__set('dsc_recado_to_coordenacao', $_POST['dsc_recado_to_coordenacao']);			

			$insereTbAcompFml->__set('dsc_consid_final', $_POST['dsc_consid_final']);			
			$insereTbAcompFml->insertAcompanhamentoFamilia();	

			$this->view->haAcomp = 1;

			// Inserir Voluntário na tabela tb_vncl_vlnt_acomp_fml
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Obtem Sequencial de Acompanhamento Atual
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getSequencial();			
		}

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cd_grp'], 
				'cb_grupo_escolhido' => $_POST['cd_grp'], 
				'nm_grp' => $_POST['nm_grp'], 
				'cd_sbgrp' => $_POST['cd_sbgrp'], 
				'cb_subgrupo_escolhido' => $_POST['cd_sbgrp'], 
				'nm_sbgrp' => $_POST['nm_sbgrp'], 
				'cd_fml' => $_POST['cd_fml'], 
				'cb_familia_escolhida' => $_POST['cd_fml'], 
				'nm_grp_fmlr' => $_POST['nm_grp_fmlr']  
		);

		$this->view->erroValidacao = 2;

		$this->view->vnclVlntAcomp = 0;

		$atuacao_voluntario_acompanhamento = 3;				// Visitador

		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
		}

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->render('fAIncluirRVMenu');

	}	// Fim da function fAIncluirDadosRVBase		

// ====================================================== //	
	
	public function fAPreAlterarRV() {
		
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

			$this->render('fAPreAlterarRV');
		}
	}	// Fim da function fAPreAlterarRV

// ====================================================== //	
	
	public function fAAlterarRV() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_situacao_fml = 3;  			// Em atendimento

		// Valida se Grupo e Subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreAlterarRV');

		} else {

			$this->nivel_atuacao_requerido = 5;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela tb_vncl_vlnt_grp
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreAlterarRV');				

			// Não tem a atuação necessária
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreAlterarRV');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];

				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp',  $atividade_acompanhamento);
				$familiasVnclGrupoBase->__set('codInicialAcomp',  $estado_acompanhamento_ini);
				$familiasVnclGrupoBase->__set('codFinalAcomp',  $estado_acompanhamento_fim);
				$familiasVnclGrupoBase->__set('codEstSituFml',  $estado_situacao_fml);
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompanhamento();

				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado

						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			

						// Obter dados acompanhamento //
						$dataAcompBase = Container::getModel('TbAcompFml');
						$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
						$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
						$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
						$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
						$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
						$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

					  $dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'dt_cadastro_fml' => $dataAcomp_f,
								'cd_est_situ_fml' => $cd_est_situ_fml,
								'origem' => 'alteracaoRelatorio'
						));
					}

					// Para compor os dados do Grupo e Subgrupo acima da tabela
					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAAlterarRV');	
				} else {
					$this->view->erroValidacao = 3;

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreAlterarRV');
				}
			}	
		}	
	}	// Fim da function fAAlterarRV


// ====================================================== //	

	public function fAAlterarRVMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 0;

		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		if (!isset($_POST['origem'])) {
			$origem = 'alteracaoRelatorio';
		} else {
			$origem = $_POST['origem'];
		}

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'], 
				'nm_grp' => $nomeGrupo, 
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
				'nm_sbgrp' => $nomeSubgrupo, 
				'cd_fml' => $_POST['cb_familia_escolhida'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
				'origem' => $origem
		);

		$this->view->haAcomp = 0;
		$this->view->vnclVlntAcomp = 0;

		$atividade_acompanhamento = 2;  							// Visita
		$estado_acompanhamento_ini = 1;								// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;								// Pendente de Término de registro de Triagem/Visita
		$atuacao_voluntario_acompanhamento = 3;				// Visitador

		// Verificar se há Relatório de Visita em Andamento //
		$verificaTriagemVisita0 = Container::getModel('TbAcompFml');
		$verificaTriagemVisita0->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$verificaTriagemVisita0->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita0->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita0->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase0 = $verificaTriagemVisita0->getQtdTriagemVisita();


		if ($verificaTriagemVisitaBase0['seql_acomp'] > 0) {
			$this->view->haAcomp = 1;

			// Verificar se há vínculo cadastrado
			$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
			$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$verificaVinculo->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
			$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

			if ($verificaVinculoBase['qtde'] > 0) {
				$this->view->vnclVlntAcomp = 1;
			}
		}	

		$this->render('fAAlterarRVMenu');
	}	// Fim da function fAAlterarRTMenu
			
// ====================================================== //	

	public function fAAlterarDadosRVMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_situacao_fml = 3;  			// Em atendimento


		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cd_fml']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
		$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
		$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		if ($dataAcomp['dsc_item_doado_comunhao_visita'] === null) {
			$dsc_item_doado_comunhao_visita = '';
		} else {
			$dsc_item_doado_comunhao_visita = $dataAcomp['dsc_item_doado_comunhao_visita'];
		}

		if ($dataAcomp['dsc_item_doado_sbgrp_visita'] === null) {
			$dsc_item_doado_sbgrp_visita = '';
		} else {
			$dsc_item_doado_sbgrp_visita = $dataAcomp['dsc_item_doado_sbgrp_visita'];
		}

		if ($dataAcomp['dsc_resumo_visita'] === null) {
			$dsc_resumo_visita = '';
		} else {
			$dsc_resumo_visita = $dataAcomp['dsc_resumo_visita'];
		}

		if ($dataAcomp['dsc_meta_alcan'] === null) {
			$dsc_meta_alcan = '';
		} else {
			$dsc_meta_alcan = $dataAcomp['dsc_meta_alcan'];
		}

		if ($dataAcomp['dsc_pend_visita'] === null) {
			$dsc_pend_visita = '';
		} else {
			$dsc_pend_visita = $dataAcomp['dsc_pend_visita'];
		}

		if ($dataAcomp['dsc_consid_final_visita'] === null) {
			$dsc_consid_final = '';
		} else {
			$dsc_consid_final = $dataAcomp['dsc_consid_final_visita'];
		}

		if ($dataAcomp['dsc_recado_to_coordenacao'] === null) {
			$dsc_recado_to_coordenacao = '';
		} else {
			$dsc_recado_to_coordenacao = $dataAcomp['dsc_recado_to_coordenacao'];
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $dataAcomp_f,
			'nr_bolsa_canguru' => $dataAcomp['nr_bolsa_canguru'],	
			'vlr_doado_finan_comunhao_visita' => $dataAcomp['vlr_doado_finan_comunhao_visita'],
			'vlr_doado_sbgrp_visita' => $dataAcomp['vlr_doado_sbgrp_visita'],
			'dsc_item_doado_comunhao_visita' => $dsc_item_doado_comunhao_visita,
			'dsc_item_doado_sbgrp_visita' => $dsc_item_doado_sbgrp_visita,
			'dsc_resumo_visita' => $dsc_resumo_visita,
			'dsc_meta_alcan' => $dsc_meta_alcan,
			'dsc_pend_visita' => $dsc_pend_visita,
			'dsc_consid_final' => $dsc_consid_final,
			'dsc_recado_to_coordenacao' => $dsc_recado_to_coordenacao,
			'origem' => $_POST['origem']
		);

		$this->render('fAAlterarDadosRVMenu');

	}	// Fim da function fAAlterarDadosRVMenu		

// ====================================================== //	

	public function fAAlterarDadosRVBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Atualiza na tabela tb_acomp_fml
		$alteraTbAcompFml = Container::getModel('TbAcompFml');
		$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$alteraTbAcompFml->__set('dt_acomp', $dataAcomp_formatada);
		$alteraTbAcompFml->__set('nr_bolsa_canguru', $_POST['nr_bolsa_canguru']);
		$alteraTbAcompFml->__set('vlr_doado_finan_comunhao_visita', $_POST['vlr_doado_finan_comunhao_visita']);
		$alteraTbAcompFml->__set('vlr_doado_sbgrp_visita', $_POST['vlr_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_comunhao_visita', $_POST['dsc_item_doado_comunhao_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_sbgrp_visita', $_POST['dsc_item_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_resumo_visita', $_POST['dsc_resumo_visita']);
		$alteraTbAcompFml->__set('dsc_meta_alcan', $_POST['dsc_meta_alcan']);
		$alteraTbAcompFml->__set('dsc_pend_visita', $_POST['dsc_pend_visita']);
		$alteraTbAcompFml->__set('dsc_recado_to_coordenacao', $_POST['dsc_recado_to_coordenacao']);
		$alteraTbAcompFml->__set('dsc_consid_final', $_POST['dsc_consid_final']);
		$alteraTbAcompFml->__set('cd_est_acomp', 1);
		$alteraTbAcompFml->updateRVAtualiza();

		$this->fAAlterarRVRetorno();

	}	// Fim da function fAAlterarDadosRVBase		

	// ====================================================== //	

		public function fAAlterarRVRetorno() {

		if (!isset($_POST['origem'])) {
			$origem = 'alteracaoRelatorio';
		} else {
			$origem = $_POST['origem'];
		}

		$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cd_grp'], 
				'cb_grupo_escolhido' => $_POST['cd_grp'], 
				'nm_grp' => $_POST['nm_grp'], 
				'cd_sbgrp' => $_POST['cd_sbgrp'], 
				'cb_subgrupo_escolhido' => $_POST['cd_sbgrp'], 
				'nm_sbgrp' => $_POST['nm_sbgrp'], 
				'cd_fml' => $_POST['cd_fml'], 
				'cb_familia_escolhida' => $_POST['cd_fml'], 
				'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
				'origem' => $origem
		);

		$this->view->erroValidacao = 2;

		$this->view->haAcomp = 0;
		$this->view->vnclVlntAcomp = 0;

		$atividade_acompanhamento = 2;  							// Visita
		$estado_acompanhamento_ini = 1;								// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;								// Pendente de Término de registro de Triagem/Visita
		$atuacao_voluntario_acompanhamento = 3;				// Visitador		

		// Verificar se há Relatório de Visita em Andamento //
		$verificaTriagemVisita0 = Container::getModel('TbAcompFml');
		$verificaTriagemVisita0->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$verificaTriagemVisita0->__set('codAtvdAcomp', $atividade_acompanhamento);
		$verificaTriagemVisita0->__set('codEstIni', $estado_acompanhamento_ini);
		$verificaTriagemVisita0->__set('codEstFim', $estado_acompanhamento_fim);
		$verificaTriagemVisitaBase0 = $verificaTriagemVisita0->getQtdTriagemVisita();

		if ($verificaTriagemVisitaBase0['seql_acomp'] > 0) {
			$this->view->haAcomp = 1;
		
			// Verificar se há vínculo cadastrado
			$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
			$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$verificaVinculo->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
			$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

			if ($verificaVinculoBase['qtde'] > 0) {
				$this->view->vnclVlntAcomp = 1;
			}
		}	

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $verificaTriagemVisitaBase0['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);			
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		if ($origem == 'conclusaoRelatorio' || $origem == 'alteracaoRelatorio') {
			$estado_acompanhamento_ini = 1;	// Pendente de Término de revisão
  		$estado_acompanhamento_fim = 1;	// Pendente de Término de revisão
		} else {
			$estado_acompanhamento_ini = 2;	// Pendente de Término de revisão
  		$estado_acompanhamento_fim = 2;	// Pendente de Término de revisão
		}

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
		$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
		$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		// Obter famílias em atendimento passíveis de serem substituídas 
		$familiasEmAtendimentoBase = Container::getModel('TbFml');
		$familiasEmAtendimentoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$familiasEmAtendimentoBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$familiasEmAtendimento = $familiasEmAtendimentoBase->getFamiliasEmAtendimento();
			
		$this->view->familiasPassiveisSubstituicao = $familiasEmAtendimento;

		if ($dataAcomp['nr_bolsa_canguru'] === null) {
			$nr_bolsa_canguru = '';
		} else {
			$nr_bolsa_canguru = $dataAcomp['nr_bolsa_canguru'];
		}

		if ($dataAcomp['vlr_doado_finan_comunhao_visita'] === null) {
			$vlr_doado_finan_comunhao_visita = 0;
		} else {
			$vlr_doado_finan_comunhao_visita = $dataAcomp['vlr_doado_finan_comunhao_visita'];
		}

		if ($dataAcomp['vlr_doado_sbgrp_visita'] === null) {
			$vlr_doado_sbgrp_visita = 0;
		} else {
			$vlr_doado_sbgrp_visita = $dataAcomp['vlr_doado_sbgrp_visita'];
		}

		if ($dataAcomp['dsc_item_doado_comunhao_visita'] === null) {
			$dsc_item_doado_comunhao_visita = '';
		} else {
			$dsc_item_doado_comunhao_visita = $dataAcomp['dsc_item_doado_comunhao_visita'];
		}

		if ($dataAcomp['dsc_item_doado_sbgrp_visita'] === null) {
			$dsc_item_doado_sbgrp_visita = '';
		} else {
			$dsc_item_doado_sbgrp_visita = $dataAcomp['dsc_item_doado_sbgrp_visita'];
		}

		if ($dataAcomp['dsc_resumo_visita'] === null) {
			$dsc_resumo_visita = '';
		} else {
			$dsc_resumo_visita = $dataAcomp['dsc_resumo_visita'];
		}

		if ($dataAcomp['dsc_meta_alcan'] === null) {
			$dsc_meta_alcan = '';
		} else {
			$dsc_meta_alcan = $dataAcomp['dsc_meta_alcan'];
		}

		if ($dataAcomp['dsc_pend_visita'] === null) {
			$dsc_pend_visita = '';
		} else {
			$dsc_pend_visita = $dataAcomp['dsc_pend_visita'];
		}

		if ($dataAcomp['dsc_consid_final_visita'] === null) {
			$dsc_consid_final = '';
		} else {
			$dsc_consid_final = $dataAcomp['dsc_consid_final_visita'];
		}

		if ($dataAcomp['dsc_recado_to_coordenacao'] === null) {
			$dsc_recado_to_coordenacao = '';
		} else {
			$dsc_recado_to_coordenacao = $dataAcomp['dsc_recado_to_coordenacao'];
		}

		if ($dataAcomp['dsc_recado_coordenacao_to_sbgrp'] === null) {
			$dsc_recado_coordenacao_to_sbgrp = '';
		} else {
			$dsc_recado_coordenacao_to_sbgrp = $dataAcomp['dsc_recado_coordenacao_to_sbgrp'];
		}
		
		$this->view->dadosAcompanhamento = array (
						'cd_grp' => $_POST['cb_grupo_escolhido'], 
						'nm_grp' => $_POST['nm_grp'], 
						'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
						'nm_sbgrp' => $_POST['nm_sbgrp'], 
						'cd_fml' => $_POST['cb_familia_escolhida'],
						'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
						'dt_acomp' => $dataAcomp_f,
						'nr_bolsa_canguru' => $nr_bolsa_canguru,
						'vlr_doado_finan_comunhao_visita' => $vlr_doado_finan_comunhao_visita,
						'vlr_doado_sbgrp_visita' => $vlr_doado_sbgrp_visita,
						'dsc_item_doado_comunhao_visita' => $dsc_item_doado_comunhao_visita,
						'dsc_item_doado_sbgrp_visita' => $dsc_item_doado_sbgrp_visita,
						'dsc_resumo_visita' => $dsc_resumo_visita,
						'dsc_meta_alcan' => $dsc_meta_alcan,
						'dsc_pend_visita' => $dsc_pend_visita,
						'dsc_consid_final' => $dsc_consid_final,
						'dsc_recado_to_coordenacao' => $dsc_recado_to_coordenacao,
						'dsc_recado_coordenacao_to_sbgrp' => $dsc_recado_coordenacao_to_sbgrp,
						'seql_acomp' => $seqlAcomp->__get('seql_max'),
						'origem' => $origem
		);

		if ($origem == 'conclusaoRelatorio') {
			$this->render('fAConcluirRVMenu');		
		} else if ($origem == 'conclusaoRevisao') {
			$this->render('fARevisarRVMenu');					
		} else {
			$this->render('fAAlterarRVMenu');
		}
	
	}	// Fim da function fAAlterarRVRetorno		

// ====================================================== //	
		
	public function fAPreConcluirRV() {
		
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

			$this->render('fAPreConcluirRV');
		}
	}	// Fim da function fAPreConcluirRV


// ====================================================== //	
	
	public function fAConcluirRV() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_acompanhamento_fim = 1;	// Pendente de Término de registro de Triagem/Visita
		$estado_situacao_fml = 3;  			// Em atendimento

		// Valida se Grupo e subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreConcluirRV');

		} else {

			$this->nivel_atuacao_requerido = 5;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreConcluirRV');				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreConcluirRV');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];
				
				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp',  $atividade_acompanhamento);
				$familiasVnclGrupoBase->__set('codInicialAcomp',  $estado_acompanhamento_ini);
				$familiasVnclGrupoBase->__set('codFinalAcomp',  $estado_acompanhamento_fim);
				$familiasVnclGrupoBase->__set('codEstSituFml',  $estado_situacao_fml);

				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompanhamento();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado
						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			

						// Obter dados acompanhamento //
						$dataAcompBase = Container::getModel('TbAcompFml');
						$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
						$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));							
						$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
						$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
						$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
						$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

					  $dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'dt_cadastro_fml' => $dataAcomp_f,
								'cd_est_situ_fml' => $cd_est_situ_fml
						));

						// Para compor os dados do Grupo e Subgrupo acima da tabela
						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;
					}

					// Verifica se há famílias a terem relatório concluído
					if (count($this->view->familia) == 0) {
						$this->view->erroValidacao = 4;

						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;

						$this->render('fAPreConcluirRV');
					} else {
						$this->render('fAConcluirRV');	
					}
				
				} else {

					if ($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio == 0) {
						$this->view->erroValidacao = 3;
					} else {
						$this->view->erroValidacao = 7;
					}

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreConcluirRV');
				}
			}	
		}
	
	}	// Fim da function fAConcluirRV


// ====================================================== //	
	
	public function fAConcluirRVMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->nivel_atuacao_acomp_requerido = 2;	

		$this->validaAcessoAcompanhamentoRelatorio();

		// Não está na tabela de Vinculo de Acompanhamento
		if ($this->retornoValidaAcessoAcompanhamentoRelatorio == 1) {
 
			// Chama novamente a function para dar refresh na tela
			session_write_close();
			$this->fAConcluirRV();

		// Está na tabela de Vinculo de Acompanhamento, mas não é Revisor
		} else if ($this->retornoValidaAcessoAcompanhamentoRelatorio == 2) {
			// Chama novamente a function para dar refresh na tela
			session_write_close();
			$this->fAConcluirRV();

		} else {

			// Buscar Nome de Grupo e Subgrupo
			$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
			$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
			$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
			$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
			
			$nomeGrupo = $dadosGS['nome_grupo'];
			$nomeSubgrupo = $dadosGS['nome_subgrupo'];

			// Buscar dados Família
			$dadosFamilia = Container::getModel('TbFml');
			$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
			$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

			// Obtem Sequencial de Acompanhamento Atual
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$seqlAcomp->getSequencial();			

			$atividade_acompanhamento = 2;  // Visita
			$estado_acompanhamento_ini = 1;	// Pendente de Término de revisão
  			$estado_acompanhamento_fim = 1;	// Pendente de Término de revisão

			// Obter dados acompanhamento //
			$dataAcompBase = Container::getModel('TbAcompFml');
			$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
			$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
			$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
			$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
			$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

			$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

			if ($dataAcomp['dsc_item_doado_comunhao_visita'] === null) {
				$dsc_item_doado_comunhao_visita = '';
			} else {
				$dsc_item_doado_comunhao_visita = $dataAcomp['dsc_item_doado_comunhao_visita'];
			}

			if ($dataAcomp['dsc_item_doado_sbgrp_visita'] === null) {
				$dsc_item_doado_sbgrp_visita = '';
			} else {
				$dsc_item_doado_sbgrp_visita = $dataAcomp['dsc_item_doado_sbgrp_visita'];
			}

			if ($dataAcomp['dsc_resumo_visita'] === null) {
				$dsc_resumo_visita = '';
			} else {
				$dsc_resumo_visita = $dataAcomp['dsc_resumo_visita'];
			}

			if ($dataAcomp['dsc_meta_alcan'] === null) {
				$dsc_meta_alcan = '';
			} else {
				$dsc_meta_alcan = $dataAcomp['dsc_meta_alcan'];
			}

			if ($dataAcomp['dsc_pend_visita'] === null) {
				$dsc_pend_visita = '';
			} else {
				$dsc_pend_visita = $dataAcomp['dsc_pend_visita'];
			}

			if ($dataAcomp['dsc_consid_final_visita'] === null) {
				$dsc_consid_final = '';
			} else {
				$dsc_consid_final = $dataAcomp['dsc_consid_final_visita'];
			}

			if ($dataAcomp['dsc_recado_to_coordenacao'] === null) {
				$dsc_recado_to_coordenacao = '';
			} else {
				$dsc_recado_to_coordenacao = $dataAcomp['dsc_recado_to_coordenacao'];
			}

			$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'],
				'nm_grp' => $_POST['nome_grupo'],
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'],
				'nm_sbgrp' => $_POST['nome_subgrupo'],
				'cd_fml' => $_POST['cb_familia_escolhida'],
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
				'dt_acomp' => $dataAcomp_f,
				'nr_bolsa_canguru' => $dataAcomp['nr_bolsa_canguru'],	
				'vlr_doado_finan_comunhao_visita' => $dataAcomp['vlr_doado_finan_comunhao_visita'],
				'vlr_doado_sbgrp_visita' => $dataAcomp['vlr_doado_sbgrp_visita'],
				'dsc_item_doado_comunhao_visita' => $dsc_item_doado_comunhao_visita,
				'dsc_item_doado_sbgrp_visita' => $dsc_item_doado_sbgrp_visita,
				'dsc_resumo_visita' => $dsc_resumo_visita,
				'dsc_meta_alcan' => $dsc_meta_alcan,
				'dsc_pend_visita' => $dsc_pend_visita,
				'dsc_consid_final' => $dsc_consid_final,
				'dsc_recado_to_coordenacao' => $dsc_recado_to_coordenacao,
				'origem' => $_POST['origem']
			);

			$this->render('fAConcluirRVMenu');				

		}
	}	// Fim da function fAConcluirRVMenu

// ====================================================== //	
	
	public function fAConcluirRVBaseAtualiza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Atualiza na tabela tb_acomp_fml
		$alteraTbAcompFml = Container::getModel('TbAcompFml');
		$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$alteraTbAcompFml->__set('dt_acomp', $dataAcomp_formatada);
		$alteraTbAcompFml->__set('nr_bolsa_canguru', $_POST['nr_bolsa_canguru']);
		$alteraTbAcompFml->__set('vlr_doado_finan_comunhao_visita', $_POST['vlr_doado_finan_comunhao_visita']);
		$alteraTbAcompFml->__set('vlr_doado_sbgrp_visita', $_POST['vlr_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_comunhao_visita', $_POST['dsc_item_doado_comunhao_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_sbgrp_visita', $_POST['dsc_item_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_resumo_visita', $_POST['dsc_resumo_visita']);
		$alteraTbAcompFml->__set('dsc_meta_alcan', $_POST['dsc_meta_alcan']);
		$alteraTbAcompFml->__set('dsc_pend_visita', $_POST['dsc_pend_visita']);
		$alteraTbAcompFml->__set('dsc_recado_to_coordenacao', $_POST['dsc_recado_to_coordenacao']);
		$alteraTbAcompFml->__set('dsc_consid_final', $_POST['dsc_consid_final']);
		$alteraTbAcompFml->__set('cd_est_acomp', 1);
		$alteraTbAcompFml->updateRVAtualiza();

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 3;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAConcluirRV();				

	}	// Fim da function fAConcluirRVBaseAtualiza


// ====================================================== //	
	
	public function fAConcluirRVBaseConclui() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Atualiza na tabela tb_acomp_fml
		$alteraTbAcompFml = Container::getModel('TbAcompFml');
		$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$alteraTbAcompFml->__set('dt_acomp', $dataAcomp_formatada);
		$alteraTbAcompFml->__set('nr_bolsa_canguru', $_POST['nr_bolsa_canguru']);
		$alteraTbAcompFml->__set('vlr_doado_finan_comunhao_visita', $_POST['vlr_doado_finan_comunhao_visita']);
		$alteraTbAcompFml->__set('vlr_doado_sbgrp_visita', $_POST['vlr_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_comunhao_visita', $_POST['dsc_item_doado_comunhao_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_sbgrp_visita', $_POST['dsc_item_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_resumo_visita', $_POST['dsc_resumo_visita']);
		$alteraTbAcompFml->__set('dsc_meta_alcan', $_POST['dsc_meta_alcan']);
		$alteraTbAcompFml->__set('dsc_pend_visita', $_POST['dsc_pend_visita']);
		$alteraTbAcompFml->__set('dsc_recado_to_coordenacao', $_POST['dsc_recado_to_coordenacao']);
		$alteraTbAcompFml->__set('dsc_consid_final', $_POST['dsc_consid_final']);
		$alteraTbAcompFml->__set('cd_est_acomp', 2);
		$alteraTbAcompFml->updateRVAtualiza();

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 4;
		$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 1;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAConcluirRV();				

	}	// Fim da function fAConcluirRVBaseConclui

// ====================================================== //	

	public function fAConsultarRVVoluntario() {
	
		$this->validaAutenticacao();	

		// Colocado para tratar pesquisa de Relatórios
		if ($_POST['origem'] == 'consultaRelatorios') {
			// Buscar Voluntários Vinculados no acompanhamento 
			$obtemVoluntariosBase = Container::getModel('TbVnclVlntAcompFml');
			$obtemVoluntariosBase->__set('cd_fml', $_POST['cd_fml']);
			$obtemVoluntariosBase->__set('seql_acomp', $_POST['seql_acomp']);
			$obtemVoluntarios = $obtemVoluntariosBase->getVoluntariosVinculoAcompAll();

			$seql_acomp_post = $_POST['seql_acomp'];

		} else {
			// Obtem Sequencial de Acompanhamento Atual
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getSequencial();			

			// Buscar Voluntários Vinculados no acompanhamento 
			$obtemVoluntariosBase = Container::getModel('TbVnclVlntAcompFml');
			$obtemVoluntariosBase->__set('cd_fml', $_POST['cd_fml']);
			$obtemVoluntariosBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$obtemVoluntarios = $obtemVoluntariosBase->getVoluntariosVinculoAcompAll();

			$seql_acomp_post = $seqlAcomp->__get('seql_max');
		}

		$this->view->dadosVoluntarios = array ();

		foreach ($obtemVoluntarios as $index => $arr) {
			if ($arr['cd_atua_vlnt_acomp'] == 3) {
				$cd_atua_vlnt_acomp = 'Visitador';
			} else if ($arr['cd_atua_vlnt_acomp'] == 2) {
				$cd_atua_vlnt_acomp = 'Visitador e Relator Relatório';
			} else {
				$cd_atua_vlnt_acomp = 'Revisor Relatório';
			}

			array_push($this->view->dadosVoluntarios, array (
					'cd_vlnt' => $arr['cd_vlnt'],
					'nm_vlnt' => $arr['nm_vlnt'],
					'cd_atua_vlnt_acomp' => $arr['cd_atua_vlnt_acomp'],
					'cd_atua_vlnt_acompD' => $cd_atua_vlnt_acomp
			));                               
		}

		// Para compor os dados do Grupo e Subgrupo acima da tabela
		$this->view->codGrupo = $_POST['cd_grp'];
		$this->view->nomeGrupo = $_POST['nm_grp'];
		$this->view->codSubgrupo = $_POST['cd_sbgrp'];
		$this->view->nomeSubgrupo = $_POST['nm_sbgrp'];
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
		//$this->view->seqlAcomp = $seqlAcomp->__get('seql_max');
		$this->view->seqlAcomp = $seql_acomp_post;
		$this->view->origem = $_POST['origem'];

		$this->render('fAConsultarRVVoluntario');

	} // Fim da function fAConsultarRVVoluntario

// ====================================================== //	
		
	public function fAPreRevisarRV() {
		
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

			$this->render('fAPreRevisarRV');
		}
	}	// Fim da function fAPreRevisarRV


// ====================================================== //	
	
	public function fARevisarRV() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 2;	// Pendente de Término de revisão
		$estado_acompanhamento_fim = 2;	// Pendente de Término de revisão
		$estado_situacao_fml = 3;  			// Em atendimento

		// Valida se Grupo e subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreRevisarRV');

		} else {

			$this->nivel_atuacao_requerido = 3;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreRevisarRV');				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreRevisarRV');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];
				
				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp',  $atividade_acompanhamento);
				$familiasVnclGrupoBase->__set('codInicialAcomp',  $estado_acompanhamento_ini);
				$familiasVnclGrupoBase->__set('codFinalAcomp',  $estado_acompanhamento_fim);
				$familiasVnclGrupoBase->__set('codEstSituFml',  $estado_situacao_fml);

				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompanhamento();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado
						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			

						// Obter dados acompanhamento //
						$dataAcompBase = Container::getModel('TbAcompFml');
						$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
						$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));							
						$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
						$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
						$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
						$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

					  $dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'dt_cadastro_fml' => $dataAcomp_f,
								'cd_est_situ_fml' => $cd_est_situ_fml
						));

						// Para compor os dados do Grupo e Subgrupo acima da tabela
						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;
					}

					// Verifica se há famílias a terem relatório concluído
					if (count($this->view->familia) == 0) {
						$this->view->erroValidacao = 4;

						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;

						$this->render('fAPreRevisarRV');
					} else {
						$this->render('fARevisarRV');	
					}
				
				} else {

					if ($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio == 0) {
						$this->view->erroValidacao = 3;
					} else {
						$this->view->erroValidacao = 7;
					}

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreRevisarRV');
				}
			}	
		}
	
	}	// Fim da function fARevisarRV


// ====================================================== //	
	
	public function fARevisarRVMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;
	
		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 2;	// Pendente de Término de revisão
		$estado_acompanhamento_fim = 2;	// Pendente de Término de revisão

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
		$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
		$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		if ($dataAcomp['dsc_item_doado_comunhao_visita'] === null) {
			$dsc_item_doado_comunhao_visita = '';
		} else {
			$dsc_item_doado_comunhao_visita = $dataAcomp['dsc_item_doado_comunhao_visita'];
		}

		if ($dataAcomp['dsc_item_doado_sbgrp_visita'] === null) {
			$dsc_item_doado_sbgrp_visita = '';
		} else {
			$dsc_item_doado_sbgrp_visita = $dataAcomp['dsc_item_doado_sbgrp_visita'];
		}

		if ($dataAcomp['dsc_resumo_visita'] === null) {
			$dsc_resumo_visita = '';
		} else {
			$dsc_resumo_visita = $dataAcomp['dsc_resumo_visita'];
		}

		if ($dataAcomp['dsc_meta_alcan'] === null) {
			$dsc_meta_alcan = '';
		} else {
			$dsc_meta_alcan = $dataAcomp['dsc_meta_alcan'];
		}

		if ($dataAcomp['dsc_pend_visita'] === null) {
			$dsc_pend_visita = '';
		} else {
			$dsc_pend_visita = $dataAcomp['dsc_pend_visita'];
		}

		if ($dataAcomp['dsc_consid_final_visita'] === null) {
			$dsc_consid_final = '';
		} else {
			$dsc_consid_final = $dataAcomp['dsc_consid_final_visita'];
		}

		if ($dataAcomp['dsc_recado_to_coordenacao'] === null) {
			$dsc_recado_to_coordenacao = '';
		} else {
			$dsc_recado_to_coordenacao = $dataAcomp['dsc_recado_to_coordenacao'];
		}

		if ($dataAcomp['dsc_recado_coordenacao_to_sbgrp'] === null) {
			$dsc_recado_coordenacao_to_sbgrp = '';
		} else {
			$dsc_recado_coordenacao_to_sbgrp = $dataAcomp['dsc_recado_coordenacao_to_sbgrp'];
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cb_grupo_escolhido'],
			'nm_grp' => $_POST['nome_grupo'],
			'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'],
			'nm_sbgrp' => $_POST['nome_subgrupo'],
			'cd_fml' => $_POST['cb_familia_escolhida'],
			'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
			'dt_acomp' => $dataAcomp_f,
			'nr_bolsa_canguru' => $dataAcomp['nr_bolsa_canguru'],	
			'vlr_doado_finan_comunhao_visita' => $dataAcomp['vlr_doado_finan_comunhao_visita'],
			'vlr_doado_sbgrp_visita' => $dataAcomp['vlr_doado_sbgrp_visita'],
			'dsc_item_doado_comunhao_visita' => $dsc_item_doado_comunhao_visita,
			'dsc_item_doado_sbgrp_visita' => $dsc_item_doado_sbgrp_visita,
			'dsc_resumo_visita' => $dsc_resumo_visita,
			'dsc_meta_alcan' => $dsc_meta_alcan,
			'dsc_pend_visita' => $dsc_pend_visita,
			'dsc_consid_final' => $dsc_consid_final,
			'dsc_recado_to_coordenacao' => $dsc_recado_to_coordenacao,
			'dsc_recado_coordenacao_to_sbgrp' => $dsc_recado_coordenacao_to_sbgrp,
			'origem' => $_POST['origem']
		);

		$this->render('fARevisarRVMenu');				

	}	// Fim da function fARevisarRVMenu

// ====================================================== //	
	
	public function fARevisarRVBaseAtualiza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Atualiza na tabela tb_acomp_fml
		$alteraTbAcompFml = Container::getModel('TbAcompFml');
		$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$alteraTbAcompFml->__set('dt_acomp', $dataAcomp_formatada);
		$alteraTbAcompFml->__set('nr_bolsa_canguru', $_POST['nr_bolsa_canguru']);
		$alteraTbAcompFml->__set('vlr_doado_finan_comunhao_visita', $_POST['vlr_doado_finan_comunhao_visita']);
		$alteraTbAcompFml->__set('vlr_doado_sbgrp_visita', $_POST['vlr_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_comunhao_visita', $_POST['dsc_item_doado_comunhao_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_sbgrp_visita', $_POST['dsc_item_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_resumo_visita', $_POST['dsc_resumo_visita']);
		$alteraTbAcompFml->__set('dsc_meta_alcan', $_POST['dsc_meta_alcan']);
		$alteraTbAcompFml->__set('dsc_pend_visita', $_POST['dsc_pend_visita']);
		$alteraTbAcompFml->__set('dsc_recado_to_coordenacao', $_POST['dsc_recado_to_coordenacao']);
		$alteraTbAcompFml->__set('dsc_recado_coordenacao_to_sbgrp', $_POST['dsc_recado_coordenacao_to_sbgrp']);
		$alteraTbAcompFml->__set('dsc_consid_final', $_POST['dsc_consid_final']);
		$alteraTbAcompFml->__set('cd_est_acomp', 2);
		$alteraTbAcompFml->updateRVAtualiza();

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 3;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fARevisarRV();				

	}	// Fim da function fARevisarRVBaseAtualiza


// ====================================================== //	
	
	public function fARevisarRVBaseConclui() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Atualiza na tabela tb_acomp_fml
		$alteraTbAcompFml = Container::getModel('TbAcompFml');
		$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$alteraTbAcompFml->__set('dt_acomp', $dataAcomp_formatada);
		$alteraTbAcompFml->__set('nr_bolsa_canguru', $_POST['nr_bolsa_canguru']);
		$alteraTbAcompFml->__set('vlr_doado_finan_comunhao_visita', $_POST['vlr_doado_finan_comunhao_visita']);
		$alteraTbAcompFml->__set('vlr_doado_sbgrp_visita', $_POST['vlr_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_comunhao_visita', $_POST['dsc_item_doado_comunhao_visita']);
		$alteraTbAcompFml->__set('dsc_item_doado_sbgrp_visita', $_POST['dsc_item_doado_sbgrp_visita']);
		$alteraTbAcompFml->__set('dsc_resumo_visita', $_POST['dsc_resumo_visita']);
		$alteraTbAcompFml->__set('dsc_meta_alcan', $_POST['dsc_meta_alcan']);
		$alteraTbAcompFml->__set('dsc_pend_visita', $_POST['dsc_pend_visita']);
		$alteraTbAcompFml->__set('dsc_recado_to_coordenacao', $_POST['dsc_recado_to_coordenacao']);
		$alteraTbAcompFml->__set('dsc_recado_coordenacao_to_sbgrp', $_POST['dsc_recado_coordenacao_to_sbgrp']);
		$alteraTbAcompFml->__set('dsc_consid_final', $_POST['dsc_consid_final']);
		$alteraTbAcompFml->__set('cd_est_acomp', 3);
		$alteraTbAcompFml->updateRVAtualiza(); 

		// Data Recebida no Formato DD/MM/AAAA
		$data_visita = $_POST['dt_acomp'];
		$data_visita = substr($data_visita, 6, 4).'-'.substr($data_visita, 3, 2).'-'.substr($data_visita, 0, 2);

		// Calcular a Próxima Data Visita
		$this->obtemDataProximaVisita($data_visita, $_POST['cb_grupo_escolhido']);

		// Gerar Kit Habitual
		$this->geraKitHabitual($_POST['cd_fml'], $seqlAcomp->__get('seql_max'), $this->prox_data_visita); 

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$insereVoluntarioTVVAF->insertTVVAF(); 
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 4;
		$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 1;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fARevisarRV();				

	}	// Fim da function fARevisarRVBaseConclui

// ====================================================== //	
	
	public function fAPreIncluirRD() {
		
		$this->validaAutenticacao();		

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

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

			$this->render('fAPreIncluirRD');		
		}
	}	// Fim da function fAPreIncluirRD


// ====================================================== //	
	
	public function fAIncluirRD() {
		
		$this->validaAutenticacao();		

		// Para qdo chamar esta função após ter atualizado o relatório de Desligamento e mostrar mensagem
		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}


		// Valida se Grupo foi escolhido
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreIncluirRD');

		} else {

			$this->nivel_atuacao_requerido = 3;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela tb_vncl_vlnt_grp
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreIncluirRD');				

			// Não tem a atuação necessária
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreIncluirRD');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];

				// Buscar Familias atreladas ao Grupo e Subgrupo e que estejam com cd_est_situ_fml = 3 (Em atendimento)
				// (estava dt_prev_term_acomp <= data atual, mas retirei, pois pode ser que familias possam ser substituidas
				// antes ou depois da previsão. O sistema fica flexível desse modo).
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('codEstSituFml',  3);				
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoRD();

				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			
												
						// Obter dados acompanhamento //
						$dataAcompBase = Container::getModel('TbAcompFml');
						$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
						$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
						$dataAcompBase->__set('cd_atvd_acomp', 2);
						$dataAcompBase->__set('cd_est_ini', 3);
						$dataAcompBase->__set('cd_est_fim', 3);
						$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();
						
						if (!empty($dataAcomp['cd_fmlID'])) {
							$dataInicioAcomp_f = Funcoes::formatarNumeros('data', $arr['dt_inc_acomp'], 10, "AMD");
							  
							$dataTerminoAcomp_f = Funcoes::formatarNumeros('data', $arr['dt_prev_term_acomp'], 10, "AMD");

							array_push($this->view->familia, array (
									'cd_grp' => $_POST['cb_grupo_escolhido'], 
									'nm_grp' => $nomeGrupo, 
									'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
									'nm_sbgrp' => $nomeSubgrupo, 
									'cd_fml' => $arr['cd_fmlID'],
									'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
									'dt_inc_acomp' => $dataInicioAcomp_f,
									'dt_prev_term_acomp' => $dataTerminoAcomp_f
							));
						}						
					}

					// Para compor os dados do Grupo e Subgrupo acima da tabela
					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					// Verifica se há famílias passíveis de desligamento
					if (count($this->view->familia) == 0) {
						$this->view->erroValidacao = 4;

						$this->render('fAPreIncluirRD');
					} else {
						$this->render('fAIncluirRD');	
					}

				} else {

					if ($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio == 0) {
						$this->view->erroValidacao = 3;
					} else {
						$this->view->erroValidacao = 7;
					}

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreIncluirRD');
				}
			}
		}	
	
	}	// Fim da function fAIncluirRD

// ====================================================== //	
	
	public function fAConcluirRDMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;
	
		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 3;	// Acompanhamento Concluído (Revisado)
		$estado_acompanhamento_fim = 3;	// Acompanhamento Concluído (Revisado)

		// Obter dados acompanhamento 
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
		$dataAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
		$dataAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		$data_acomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		if ($dataAcomp['dsc_situ_antes_depois_acomp'] === null) {
			$dsc_situ_antes_depois_acomp = '';
		} else {
			$dsc_situ_antes_depois_acomp = $dataAcomp['dsc_situ_antes_depois_acomp'];
		}

		if ($dataAcomp['dsc_objtvo_alcan_final_acomp'] === null) {
			$dsc_objtvo_alcan_final_acomp = '';
		} else {
			$dsc_objtvo_alcan_final_acomp = $dataAcomp['dsc_objtvo_alcan_final_acomp'];
		}

		if ($dataAcomp['dsc_acao_realzda_acomp'] === null) {
			$dsc_acao_realzda_acomp = '';
		} else {
			$dsc_acao_realzda_acomp = $dataAcomp['dsc_acao_realzda_acomp'];
		}

		if ($dataAcomp['dsc_dificuldade_encont_acomp'] === null) {
			$dsc_dificuldade_encont_acomp = '';
		} else {
			$dsc_dificuldade_encont_acomp = $dataAcomp['dsc_dificuldade_encont_acomp'];
		}

		if ($dataAcomp['dsc_consid_final_acomp'] === null) {
			$dsc_consid_final_acomp = '';
		} else {
			$dsc_consid_final_acomp = $dataAcomp['dsc_consid_final_acomp'];
		}

		// Buscar somatório de valores
		$somatorioAcompBase = Container::getModel('TbAcompFml');
		$somatorioAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$somatorioAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
		$somatorioAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
		$somatorioAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
		$somatorioAcomp = $somatorioAcompBase->getSomaValoresVisita();

		$somatorio_comunhao = number_format($somatorioAcomp['soma_comunhao'], 2, ',', '.');
		$somatorio_subgrupo = number_format($somatorioAcomp['soma_subgrupo'], 2, ',', '.');

		// Buscar informação da família que está substituindo esta
		$buscaSubstitutoFml = Container::getModel('TbFml');
		$buscaSubstitutoFml->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$buscaSubstitutoFmlBase = $buscaSubstitutoFml->getFamiliaSubstituida();

		if (!empty($buscaSubstitutoFmlBase['cd_fmlID'])) {
			$cd_familia_substituindo = $buscaSubstitutoFmlBase['cd_fmlID'];
			$nm_familia_substituindo = $buscaSubstitutoFmlBase['nm_grp_fmlr'];
		} else {
			$cd_familia_substituindo = '';
			$nm_familia_substituindo = 'Atenção! Sem família substituta!';
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cb_grupo_escolhido'],
			'nm_grp' => $_POST['nome_grupo'],
			'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'],
			'nm_sbgrp' => $_POST['nome_subgrupo'],
			'cd_fml' => $_POST['cb_familia_escolhida'],
			'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
			'dt_acomp' => $data_acomp_f,
			'dsc_situ_antes_depois_acomp' => $dsc_situ_antes_depois_acomp,
			'dsc_objtvo_alcan_final_acomp' => $dsc_objtvo_alcan_final_acomp,
			'dsc_acao_realzda_acomp' => $dsc_acao_realzda_acomp,
			'dsc_dificuldade_encont_acomp' => $dsc_dificuldade_encont_acomp,
			'dsc_consid_final_acomp' => $dsc_consid_final_acomp,
			'vlr_doado_finan_comunhao_visita' => $somatorio_comunhao,
			'vlr_doado_sbgrp_visita' => $somatorio_subgrupo,
			'cd_atendto_fml_subs' => $cd_familia_substituindo,
			'nm_atendto_fml_subs' => $nm_familia_substituindo
		);

		$this->render('fAConcluirRDMenu');				

	}	// Fim da function fAConcluirRDMenu


// ====================================================== //	
	
	public function fAConcluirRDBaseAtualiza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Atualiza na tabela tb_acomp_fml
		$alteraTbAcompFml = Container::getModel('TbAcompFml');
		$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$alteraTbAcompFml->__set('dsc_situ_antes_depois_acomp', $_POST['dsc_situ_antes_depois_acomp']);
		$alteraTbAcompFml->__set('dsc_objtvo_alcan_final_acomp', $_POST['dsc_objtvo_alcan_final_acomp']);
		$alteraTbAcompFml->__set('dsc_acao_realzda_acomp', $_POST['dsc_acao_realzda_acomp']);
		$alteraTbAcompFml->__set('dsc_dificuldade_encont_acomp', $_POST['dsc_dificuldade_encont_acomp']);
		$alteraTbAcompFml->__set('dsc_consid_final_acomp', $_POST['dsc_consid_final_acomp']);
		$alteraTbAcompFml->updateRDAtualiza();

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		$this->view->erroValidacao = 2;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAIncluirRD();				

	}	// Fim da function fAConcluirRDBaseAtualiza

// ====================================================== //	
	
	public function fAConcluirRDBaseFormaliza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Atualiza na tabela tb_acomp_fml
		$alteraTbAcompFml = Container::getModel('TbAcompFml');
		$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$alteraTbAcompFml->__set('dsc_situ_antes_depois_acomp', $_POST['dsc_situ_antes_depois_acomp']);
		$alteraTbAcompFml->__set('dsc_objtvo_alcan_final_acomp', $_POST['dsc_objtvo_alcan_final_acomp']);
		$alteraTbAcompFml->__set('dsc_acao_realzda_acomp', $_POST['dsc_acao_realzda_acomp']);
		$alteraTbAcompFml->__set('dsc_dificuldade_encont_acomp', $_POST['dsc_dificuldade_encont_acomp']);
		$alteraTbAcompFml->__set('dsc_consid_final_acomp', $_POST['dsc_consid_final_acomp']);
		$alteraTbAcompFml->updateRDAtualiza();

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$insereVoluntarioTVVAF->insertTVVAF(); 
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 1);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		// Atualizar a dt_enct_acomp = data atual e cd_est_situ_fml = 4
		$encerraFml = Container::getModel('TbFml');
		$encerraFml->__set('cd_fml', $_POST['cd_fml']);
		$encerraFml->updateTerminoAcompanhamentoFamilia();

		// Excluir Kit Habitual
		// Data Recebida no Formato DD/MM/AAAA
		$data_visita = $_POST['dt_acomp'];
		$data_visita = substr($data_visita, 6, 4).'-'.substr($data_visita, 3, 2).'-'.substr($data_visita, 0, 2);

		// Calcular a Próxima Data Visita
		$this->obtemDataProximaVisita($data_visita, $_POST['cb_grupo_escolhido']);

		// Exclui Kit Habitual gerado automaticamente
		for ($i = 1; $i < 4; $i++) {
			$item	=	$i;

			// Verificar se há Item e Subitem do kit Habitual e se houver, exclui
			$qtdItemSubitem = Container::getModel('TbItemNecesFml');
			$qtdItemSubitem->__set('cd_fml', $_POST['cd_fml']);
			$qtdItemSubitem->__set('cd_item', $item);
			$qtdItemSubitem->__set('cd_sbitem', 1);
			$qtdItemSubitem->__set('dt_prev_disponib_item', $this->prox_data_visita);
			$qtdItemSubitemBase = $qtdItemSubitem->getQtdItemSubitemNecessidade();

			if ($qtdItemSubitemBase['qtde'] > 0) {
				// Exclui registro
				$deleteItemSubitemNeces = Container::getModel('TbItemNecesFml');
				$deleteItemSubitemNeces->__set('cd_fml', $_POST['cd_fml']);
				$deleteItemSubitemNeces->__set('cd_item', $item);
				$deleteItemSubitemNeces->__set('cd_sbitem', 1);
				$deleteItemSubitemNeces->__set('dt_prev_disponib_item', $this->prox_data_visita);
				$deleteItemSubitemNeces->deleteItemSubitemNeces();
			}
		}

		$this->view->erroValidacao = 3;
		$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 1;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAIncluirRD();				

	}	// Fim da function fAConcluirRDBaseFormaliza

// ====================================================== //	
		
	public function fAPreConsultarRTRV() {
		
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

			$this->render('fAPreConsultarRTRV');
		}
	}	// Fim da function fAPreConsultarRTRV

// ====================================================== //	
	
	public function fAConsultarRTRV() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

		$estado_acompanhamento = 3;	// Revisão de Triagem/Visita concluída

		// Valida se Grupo e subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('fAPreConsultarRTRV');

		} else {

			// Para todos poderem consultar, apenas deverão estar cadastrados em grupo/subgrupo (exceto nível acesso geral 1 e 2)
			$this->nivel_atuacao_requerido = 99;
			
			$this->validaAcessoAcompanhamento();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->render('fAPreConsultarRTRV');				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			// Esta validação foi retirada nesta opção de consulta
			} else if ($this->retornoValidaAcessoAcompanhamento == 2) {
				$this->render('fAPreConsultarRTRV');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];
				
				// Buscar Familias que estejam nas tabelas tb_fml e tb_acomp_fml, em qualquer estado de acompanhamento
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				//$familiasVnclGrupoBase->__set('cd_est_acomp',  $estado_acompanhamento);

				$familiasVnclGrupo = $familiasVnclGrupoBase->getConsultaFamiliasAcompanhamento();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$ha_todos_segmentos = 0;
					
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado
						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'cd_est_situ_fml' => $cd_est_situ_fml
						));

						// Para compor os dados do Grupo e Subgrupo acima da tabela
						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;
					}

					// Verifica se há famílias a terem relatório consultado
					if (count($this->view->familia) == 0) {
						$this->view->erroValidacao = 4;

						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;

						$this->render('fAPreConsultarRTRV');
					} else {
						$this->render('fAConsultarRTRV');	
					}
				
				} else {

					$this->view->erroValidacao = 3;

					$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
					$this->view->nomeGrupo = $nomeGrupo;
					$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
					$this->view->nomeSubgrupo = $nomeSubgrupo;

					$this->render('fAPreConsultarRTRV');
				}
			}	
		}
	}	// Fim da function fAConsultarRTRV

// ====================================================== //	
	
	public function fAConsultarRTRVMenu() {

		$this->validaAutenticacao();		

		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();
		
		// Buscar dados de Acompanhamento - Buscará todos os acompanhamentos e não só os fomalizados
		$familiasAcompanhamentoBase = Container::getModel('TbAcompFml');
		$familiasAcompanhamentoBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		//$familiasAcompanhamentoBase->__set('cd_est_acomp', 3);
		$familiasAcompanhamento = $familiasAcompanhamentoBase->getDadosConsultaAcompanhamento();
		
		$this->view->acompanhamento = array ();

		foreach ($familiasAcompanhamento as $index => $arr) {
			if ($arr['cd_atvd_acomp'] == 1) {
				$cd_atvd_acomp = 'Triagem';
			} else {
				$cd_atvd_acomp = 'Visita';
			}

			$seql_atvd = $arr['seql_acompID'].';'.$arr['cd_atvd_acomp'];

			array_push($this->view->acompanhamento, array (
					'seql_acomp' => $arr['seql_acompID'],
					'dt_acomp' => $arr['dt_acomp'],
					'cd_atvd_acomp' => $cd_atvd_acomp,
					'seql_atvd' => $seql_atvd,
					'cd_est_acomp' => $arr['cd_est_acomp']
			));
		}

		// Para compor os dados do Grupo e Subgrupo acima da tabela
		$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
		$this->view->nomeGrupo = $nomeGrupo;
		$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
		$this->view->nomeSubgrupo = $nomeSubgrupo;
		$this->view->codFamilia = $_POST['cb_familia_escolhida'];
		$this->view->nomeFamilia = $dadosFamiliaBase['nm_grp_fmlr'];

		$this->render('fAConsultarRTRVMenu');	

	}	// Fim da function fAConsultarRTRVMenu

// ====================================================== //	
	
	public function fAConsultarRTMenu() {
		
		$this->validaAutenticacao();		

		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();
	
		// Obter dados acompanhamento 
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $_POST['seql_acomp']);
		$dataAcomp = $dataAcompBase->getDadosConsultaAcompanhamentoEspecifico();

		$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		if ($dataAcomp['dsc_mtv_cd_avalia_triagem'] === null) {
			$dsc_mtv_cd_avalia_triagem = '';
		} else {
			$dsc_mtv_cd_avalia_triagem = $dataAcomp['dsc_mtv_cd_avalia_triagem'];
		}

		if ($dataAcomp['dsc_consid_finais_triagem'] == ' ') {
			$dsc_consid_finais_triagem = '';
		} else {
			$dsc_consid_finais_triagem = $dataAcomp['dsc_consid_finais_triagem'];
		}

		if ($dataAcomp['cd_fml_subs_triagem'] == 0) {
			$cd_fml_subs_triagem = 0;
		} else {
			$cd_fml_subs_triagem = $dataAcomp['cd_fml_subs_triagem'];
		}

		$this->view->dadosAcompanhamento = array (
						'cd_grp' => $_POST['cb_grupo_escolhido'], 
						'nm_grp' => $nomeGrupo, 
						'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
						'nm_sbgrp' => $nomeSubgrupo, 
						'cd_fml' => $_POST['cb_familia_escolhida'],
						'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
						'dt_acomp' => $dataAcomp_f,
						'cd_avalia_triagem' => $dataAcomp['cd_avalia_triagem'],
						'dsc_mtv_cd_avalia_triagem' => $dsc_mtv_cd_avalia_triagem,
						'cd_crit_engjto' => $dadosFamiliaBase['cd_crit_engjto'],
						'dsc_consid_finais_triagem' => $dsc_consid_finais_triagem,
						'cd_fml_subs_triagem' => $cd_fml_subs_triagem,
						'seql_acomp' => $_POST['seql_acomp']
		);

		$this->render('fAConsultarRTMenu');				

	}	// Fim da function fAConsultarRTMenu	

// ====================================================== //	

	public function fAConsultarRTEducacaoMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 1;

		$this->fAObtemDadosTriagemConsulta();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'cd_freq_crianca_adoles_escola' => $this->obtemDadosTriagemBase['cd_freq_crianca_adoles_escola'],
			'dsc_mtvo_freq_escolar' => $this->obtemDadosTriagemBase['dsc_mtvo_freq_escolar'],
			'dsc_desemp_estudo' => $this->obtemDadosTriagemBase['dsc_desemp_estudo'],
			'cd_interes_motiva_voltar_estudar' => $this->obtemDadosTriagemBase['cd_interes_motiva_voltar_estudar'],
			'dsc_curso_interes_fml' => $this->obtemDadosTriagemBase['dsc_curso_interes_fml'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTEducacaoMenu');

	}	// Fim da function fAConsultarRTEducacaoMenu		

//xxxxxxxxxxxxxxx

// ====================================================== //	

	public function fAConsultarRTReligiosidadeMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 2;

		$this->fAObtemDadosTriagemConsulta();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		if ($this->obtemDadosTriagemBase['habito_prece_oracao'] == "S") {
			$habito_prece_oracao = 'sim';
		} else {
			$habito_prece_oracao = 'nao';
		}

		if ($this->obtemDadosTriagemBase['evangelho_lar'] == "S") {
			$evangelho_lar = 'sim';
		} else {
			$evangelho_lar = 'nao';
		}

		if ($this->obtemDadosTriagemBase['conhece_espiritismo'] == "S") {
			$conhece_espiritismo = 'sim';
		} else {
			$conhece_espiritismo = 'nao';
		}

		if ($this->obtemDadosTriagemBase['vont_aprox_espiritismo'] == "S") {
			$vont_aprox_espiritismo = 'sim';
		} else {
			$vont_aprox_espiritismo = 'nao';
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'dsc_religiao_fml' => $this->obtemDadosTriagemBase['dsc_religiao_fml'],
			'dsc_institu_religiosa_freqtd' => $this->obtemDadosTriagemBase['dsc_institu_religiosa_freqtd'],
			'dsc_freq_institu_religiosa' => $this->obtemDadosTriagemBase['dsc_freq_institu_religiosa'],
			'habito_prece_oracao' => $habito_prece_oracao,
			'evangelho_lar' => $evangelho_lar,
			'conhece_espiritismo' => $conhece_espiritismo,
			'vont_aprox_espiritismo' => $vont_aprox_espiritismo,
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTReligiosidadeMenu');

	}	// Fim da function fAConsultarRTReligiosidadeMenu		

// ====================================================== //	

	public function fAConsultarRTMoradiaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 3;

		$this->fAObtemDadosTriagemConsulta();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		if ($this->obtemDadosTriagemBase['exist_anim_inset_insal_perig'] == "S") {
			$exist_anim_inset_insal_perig = 'sim';
		} else {
			$exist_anim_inset_insal_perig = 'nao';
		}

		if ($this->obtemDadosTriagemBase['exist_anim_estima'] == "S") {
			$exist_anim_estima = 'sim';
		} else {
			$exist_anim_estima = 'nao';
		}

		if ($this->obtemDadosTriagemBase['vacina_anti_rabica_anim_estima'] == "S") {
			$vacina_anti_rabica_anim_estima = 'sim';
		} else if ($this->obtemDadosTriagemBase['vacina_anti_rabica_anim_estima'] == "N") {
			$vacina_anti_rabica_anim_estima = 'nao';
		} else {
			$vacina_anti_rabica_anim_estima = 'naoseaplica';
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,		
			'dsc_casa' => $this->obtemDadosTriagemBase['dsc_casa'],
			'exist_anim_inset_insal_perig' => $exist_anim_inset_insal_perig,
			'dsc_anim_inset_insal_perig' => $this->obtemDadosTriagemBase['dsc_anim_inset_insal_perig'],
			'exist_anim_estima' => $exist_anim_estima,
			'dsc_anim_estima' => $this->obtemDadosTriagemBase['dsc_anim_estima'],
			'vacina_anti_rabica_anim_estima' => $vacina_anti_rabica_anim_estima,
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'cd_agua_moradia' => $this->obtemDadosTriagemBase['cd_agua_moradia'],
			'cd_esgoto_moradia' => $this->obtemDadosTriagemBase['cd_esgoto_moradia'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTMoradiaMenu');

	}	// Fim da function fAConsultarRTMoradiaMenu		

// ====================================================== //	

	public function fAConsultarRTSaudeMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 4;

		$this->fAObtemDadosTriagemConsulta();
		
		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],		
			'dt_acomp' => $data_f,
			'dsc_cndc_saude_membros_fml' => $this->obtemDadosTriagemBase['dsc_cndc_saude_membros_fml'],
			'dsc_carteira_vacina_crianca' => $this->obtemDadosTriagemBase['dsc_carteira_vacina_crianca'],
			'dsc_doenca_cronica_fml' => $this->obtemDadosTriagemBase['dsc_doenca_cronica_fml'],
			'dsc_restricao_alimentar' => $this->obtemDadosTriagemBase['dsc_restricao_alimentar'],
			'dsc_higiene_pessoal' => $this->obtemDadosTriagemBase['dsc_higiene_pessoal'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTSaudeMenu');

	}	// Fim da function fAConsultarRTSaudeMenu		

// ====================================================== //	

	public function fAConsultarRTDespesaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 5;

		$this->fAObtemDadosTriagemConsulta();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'cd_tip_moradia' => $this->obtemDadosTriagemBase['cd_tip_moradia'],
			'dsc_dono_cedente_moradia' => $this->obtemDadosTriagemBase['dsc_dono_cedente_moradia'],
			'vlr_desp_agua' => $this->obtemDadosTriagemBase['vlr_desp_agua'],
			'vlr_desp_energia' => $this->obtemDadosTriagemBase['vlr_desp_energia'],
			'vlr_desp_iptu' => $this->obtemDadosTriagemBase['vlr_desp_iptu'],
			'vlr_desp_gas' => $this->obtemDadosTriagemBase['vlr_desp_gas'],
			'vlr_desp_condominio' => $this->obtemDadosTriagemBase['vlr_desp_condominio'],
			'vlr_desp_outra_manut' => $this->obtemDadosTriagemBase['vlr_desp_outra_manut'],
			'dsc_desp_outra_manut' => $this->obtemDadosTriagemBase['dsc_desp_outra_manut'],
			'dsc_desp_saude_medicamento' => $this->obtemDadosTriagemBase['dsc_desp_saude_medicamento'],
			'dsc_desp_educ_creche_cuidadora' => $this->obtemDadosTriagemBase['dsc_desp_educ_creche_cuidadora'],
			'dsc_desp_transporte' => $this->obtemDadosTriagemBase['dsc_desp_transporte'],
			'dsc_desp_alimenta_especial' => $this->obtemDadosTriagemBase['dsc_desp_alimenta_especial'],
			'dsc_outra_desp_geral' => $this->obtemDadosTriagemBase['dsc_outra_desp_geral'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTDespesaMenu');

	}	// Fim da function fAConsultarRTDespesaMenu		

// ====================================================== //	

	public function fAConsultarRTRendaMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 6;

		$this->fAObtemDadosTriagemConsulta();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'cd_tip_trab' => $this->obtemDadosTriagemBase['cd_tip_trab'],
			'vlr_renda_tip_trab' => $this->obtemDadosTriagemBase['vlr_renda_tip_trab'],
			'dsc_tip_beneficio' => $this->obtemDadosTriagemBase['dsc_tip_beneficio'],
			'vlr_renda_tip_beneficio' => $this->obtemDadosTriagemBase['vlr_renda_tip_beneficio'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTRendaMenu');

	}	// Fim da function fAConsultarRTRendaMenu		

// ====================================================== //	

	public function fAConsultarRTCapProfissionalMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 7;

		$this->fAObtemDadosTriagemConsulta();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],		
			'dt_acomp' => $data_f,
			'dsc_expect_fml_capacit_profi' => $this->obtemDadosTriagemBase['dsc_expect_fml_capacit_profi'],
			'dsc_curso_intere_profi_tecnico' => $this->obtemDadosTriagemBase['dsc_curso_intere_profi_tecnico'],
			'dsc_projeto_gera_renda_extra' => $this->obtemDadosTriagemBase['dsc_projeto_gera_renda_extra'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTCapProfissionalMenu');

	}	// Fim da function fAConsultarRTCapProfissionalMenu		


// ====================================================== //	

	public function fAConsultarRTAspectoIntMenu() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		$this->view->segmento = 8;

		$this->fAObtemDadosTriagemConsulta();

		$data_f = Funcoes::formatarNumeros('data', $this->obtemDadosTriagemBase['dt_reg_seg_triagem'], 10, "AMD");

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cd_grp'],
			'nm_grp' => $_POST['nm_grp'],
			'cd_sbgrp' => $_POST['cd_sbgrp'],
			'nm_sbgrp' => $_POST['nm_sbgrp'],
			'cd_fml' => $_POST['cd_fml'],
			'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
			'dt_acomp' => $data_f,
			'dsc_aspecto_intimo' => $this->obtemDadosTriagemBase['dsc_aspecto_intimo'],
			'dsc_prgm_trab' => $this->obtemDadosTriagemBase['dsc_prgm_trab'],
			'seql_acomp' => $_POST['seql_acomp']			
		);

		$this->render('fAConsultarRTAspectoIntMenu');

	}	// Fim da function fAConsultarRTAspectoIntMenu		

// ====================================================== //	

	public function fAObtemDadosTriagemConsulta() {
		// Obtem dados da Triagem
		$obtemDadosTriagem = Container::getModel('TbSegmtoTriagemFml');
		$obtemDadosTriagem->__set('cd_fml', $_POST['cd_fml']);
		$obtemDadosTriagem->__set('seql_acomp', $_POST['seql_acomp']);
		$obtemDadosTriagem->__set('cd_segmto_triagem', $this->view->segmento);
		$this->obtemDadosTriagemBase = $obtemDadosTriagem->getDadosSegmentoTriagem();

	} // Fim da function fAObtemDadosTriagemConsulta

// ====================================================== //	
	
	public function fAConsultarRVMenu() {
		
		$this->validaAutenticacao();		
	
		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();
		
		// Obter dados acompanhamento 
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $_POST['seql_acomp']);
		$dataAcomp = $dataAcompBase->getDadosConsultaAcompanhamentoEspecifico();

		$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		if ($dataAcomp['dsc_item_doado_comunhao_visita'] === null) {
			$dsc_item_doado_comunhao_visita = '';
		} else {
			$dsc_item_doado_comunhao_visita = $dataAcomp['dsc_item_doado_comunhao_visita'];
		}

		if ($dataAcomp['dsc_item_doado_sbgrp_visita'] === null) {
			$dsc_item_doado_sbgrp_visita = '';
		} else {
			$dsc_item_doado_sbgrp_visita = $dataAcomp['dsc_item_doado_sbgrp_visita'];
		}

		if ($dataAcomp['dsc_resumo_visita'] === null) {
			$dsc_resumo_visita = '';
		} else {
			$dsc_resumo_visita = $dataAcomp['dsc_resumo_visita'];
		}

		if ($dataAcomp['dsc_meta_alcan'] === null) {
			$dsc_meta_alcan = '';
		} else {
			$dsc_meta_alcan = $dataAcomp['dsc_meta_alcan'];
		}

		if ($dataAcomp['dsc_pend_visita'] === null) {
			$dsc_pend_visita = '';
		} else {
			$dsc_pend_visita = $dataAcomp['dsc_pend_visita'];
		}

		if ($dataAcomp['dsc_consid_final_visita'] === null) {
			$dsc_consid_final = '';
		} else {
			$dsc_consid_final = $dataAcomp['dsc_consid_final_visita'];
		}

		if ($dataAcomp['dsc_recado_to_coordenacao'] === null) {
			$dsc_recado_to_coordenacao = '';
		} else {
			$dsc_recado_to_coordenacao = $dataAcomp['dsc_recado_to_coordenacao'];
		}

		if ($dataAcomp['dsc_recado_coordenacao_to_sbgrp'] === null) {
			$dsc_recado_coordenacao_to_sbgrp = '';
		} else {
			$dsc_recado_coordenacao_to_sbgrp = $dataAcomp['dsc_recado_coordenacao_to_sbgrp'];
		}

		// Para verificar se família já está encerrada e se o sequencial pesquisado é o último
		// Obtem Sequencial de Acompanhamento Atual (Último acompanhamento)
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		if ($dadosFamiliaBase['cd_est_situ_fml'] == 4 && $seqlAcomp->__get('seql_max') == $_POST['seql_acomp']) {
			$mostra_rel_desligamento = 1;
		}	else {
			$mostra_rel_desligamento = 0;
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cb_grupo_escolhido'],
			'nm_grp' => $nomeGrupo,
			'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'],
			'nm_sbgrp' => $nomeSubgrupo,
			'cd_fml' => $_POST['cb_familia_escolhida'],
			'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
			'dt_acomp' => $dataAcomp_f,
			'nr_bolsa_canguru' => $dataAcomp['nr_bolsa_canguru'],	
			'vlr_doado_finan_comunhao_visita' => $dataAcomp['vlr_doado_finan_comunhao_visita'],
			'vlr_doado_sbgrp_visita' => $dataAcomp['vlr_doado_sbgrp_visita'],
			'dsc_item_doado_comunhao_visita' => $dsc_item_doado_comunhao_visita,
			'dsc_item_doado_sbgrp_visita' => $dsc_item_doado_sbgrp_visita,
			'dsc_resumo_visita' => $dsc_resumo_visita,
			'dsc_meta_alcan' => $dsc_meta_alcan,
			'dsc_pend_visita' => $dsc_pend_visita,
			'dsc_consid_final' => $dsc_consid_final,
			'dsc_recado_to_coordenacao' => $dsc_recado_to_coordenacao,
			'dsc_recado_coordenacao_to_sbgrp' => $dsc_recado_coordenacao_to_sbgrp,
			'seql_acomp' => $_POST['seql_acomp'],
			'mostra_rel_desligamento' => $mostra_rel_desligamento
		);

		$this->render('fAConsultarRVMenu');				

	}	// Fim da function fAConsultarRVMenu

// ====================================================== //	
	
	public function fAConsultarRDMenu() {
		
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;
	
		// Buscar Nome de Grupo e Subgrupo
		$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
		$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
		$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
		$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
		
		$nomeGrupo = $dadosGS['nome_grupo'];
		$nomeSubgrupo = $dadosGS['nome_subgrupo'];

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['cb_familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		// Obter dados acompanhamento 
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $_POST['seql_acomp']);
		$dataAcomp = $dataAcompBase->getDadosConsultaAcompanhamentoEspecifico();

		$dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

		if ($dataAcomp['dsc_situ_antes_depois_acomp'] === null) {
			$dsc_situ_antes_depois_acomp = '';
		} else {
			$dsc_situ_antes_depois_acomp = $dataAcomp['dsc_situ_antes_depois_acomp'];
		}

		if ($dataAcomp['dsc_objtvo_alcan_final_acomp'] === null) {
			$dsc_objtvo_alcan_final_acomp = '';
		} else {
			$dsc_objtvo_alcan_final_acomp = $dataAcomp['dsc_objtvo_alcan_final_acomp'];
		}

		if ($dataAcomp['dsc_acao_realzda_acomp'] === null) {
			$dsc_acao_realzda_acomp = '';
		} else {
			$dsc_acao_realzda_acomp = $dataAcomp['dsc_acao_realzda_acomp'];
		}

		if ($dataAcomp['dsc_dificuldade_encont_acomp'] === null) {
			$dsc_dificuldade_encont_acomp = '';
		} else {
			$dsc_dificuldade_encont_acomp = $dataAcomp['dsc_dificuldade_encont_acomp'];
		}

		if ($dataAcomp['dsc_consid_final_acomp'] === null) {
			$dsc_consid_final_acomp = '';
		} else {
			$dsc_consid_final_acomp = $dataAcomp['dsc_consid_final_acomp'];
		}

		$atividade_acompanhamento = 2;  // Visita
		$estado_acompanhamento_ini = 3;	// Acompanhamento Concluído (Revisado)
		$estado_acompanhamento_fim = 3;	// Acompanhamento Concluído (Revisado)

		// Buscar somatório de valores
		$somatorioAcompBase = Container::getModel('TbAcompFml');
		$somatorioAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$somatorioAcompBase->__set('cd_atvd_acomp', $atividade_acompanhamento);
		$somatorioAcompBase->__set('cd_est_ini', $estado_acompanhamento_ini);
		$somatorioAcompBase->__set('cd_est_fim', $estado_acompanhamento_fim);
		$somatorioAcomp = $somatorioAcompBase->getSomaValoresVisita();

		$somatorio_comunhao = number_format($somatorioAcomp['soma_comunhao'], 2, ',', '.');
		$somatorio_subgrupo = number_format($somatorioAcomp['soma_subgrupo'], 2, ',', '.');

		// Buscar informação da família que está substiuindo esta
		$buscaSubstitutoFml = Container::getModel('TbFml');
		$buscaSubstitutoFml->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$buscaSubstitutoFmlBase = $buscaSubstitutoFml->getFamiliaSubstituida();

		if (!empty($buscaSubstitutoFmlBase['cd_fmlID'])) {
			$cd_familia_substituindo = $buscaSubstitutoFmlBase['cd_fmlID'];
			$nm_familia_substituindo = $buscaSubstitutoFmlBase['nm_grp_fmlr'];
		} else {
			$cd_familia_substituindo = '';
			$nm_familia_substituindo = '';
		}

		$this->view->dadosAcompanhamento = array (
			'cd_grp' => $_POST['cb_grupo_escolhido'],
			'nm_grp' => $nomeGrupo,
			'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'],
			'nm_sbgrp' => $nomeSubgrupo,
			'cd_fml' => $_POST['cb_familia_escolhida'],
			'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
			'dt_acomp' => $dataAcomp_f,
			'dsc_situ_antes_depois_acomp' => $dsc_situ_antes_depois_acomp,
			'dsc_objtvo_alcan_final_acomp' => $dsc_objtvo_alcan_final_acomp,
			'dsc_acao_realzda_acomp' => $dsc_acao_realzda_acomp,
			'dsc_dificuldade_encont_acomp' => $dsc_dificuldade_encont_acomp,
			'dsc_consid_final_acomp' => $dsc_consid_final_acomp,
			'vlr_doado_finan_comunhao_visita' => $somatorio_comunhao,
			'vlr_doado_sbgrp_visita' => $somatorio_subgrupo,
			'cd_atendto_fml_subs' => $cd_familia_substituindo,
			'nm_atendto_fml_subs' => $nm_familia_substituindo,
			'seql_acomp' => $_POST['seql_acomp']
		);

		$this->render('fAConsultarRDMenu');				

	}	// Fim da function fAConsultarRDMenu

// ====================================================== //	
		
	public function fAPreConsultarRTRVPendentes() {
		
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

			$this->render('fAPreConsultarRTRVPendentes');
		}
	}	// Fim da function fAPreConsultarRTRVPendentes


// ====================================================== //	
	
	public function fAConsultarRTRVPendentes() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Sem Grupo/Subgrupo => acesso somente para cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" && 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			
			$this->nivel_atuacao_requerido = 99;
			
			$this->validaAcessoAcompanhamento();

			// Não Tem o cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->view->erroValidacao = 6;
				$this->render('fAPreConsultarRTRVPendentes');	
			}	else {
				$tipo_tratamento_consulta = 1;
			}
		} else {
			// Com Grupo somente  => acesso somente para cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
			if ($_POST['cb_grupo_escolhido'] != "Escolha Grupo" && 
						$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
				
				$this->nivel_atuacao_requerido = 99;
				
				$this->validaAcessoAcompanhamento();

				// Não Tem o cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
				if ($this->retornoValidaAcessoAcompanhamento == 1) {
					$this->view->erroValidacao = 6;
					$this->render('fAPreConsultarRTRVPendentes');	
				} else {
					$tipo_tratamento_consulta = 2;
				}			
			} else {
				// Com Grupo/Subgrupo => se estiver em grupo/subgrupo ou cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
				if ($_POST['cb_grupo_escolhido'] != "Escolha Grupo" && 
							$_POST['cb_subgrupo_escolhido'] != "Escolha Subgrupo") {
					
					$this->nivel_atuacao_requerido = 99;

					$this->validaAcessoAcompanhamento();

					// Não está na tabela de vinculo de grupo e subgrupo
					if ($this->retornoValidaAcessoAcompanhamento == 1) {
						$this->render('fAPreConsultarRTRVPendentes');				
					} else {
						$tipo_tratamento_consulta = 3;
					}
				}
			}
		}

		if ($this->view->erroValidacao == 0) {
			switch ($tipo_tratamento_consulta) {
        case 1:
          {
						// Buscar Dados 1
						$dadosConsultaBase = Container::getModel('TbAcompFml');
						$dadosConsulta = $dadosConsultaBase->getDadosConsulta1RelatoriosPendentes();
						break;
					}

        case 2:
          {
						// Buscar Dados 2 
						$dadosConsultaBase = Container::getModel('TbAcompFml');
						$dadosConsultaBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
						$dadosConsulta = $dadosConsultaBase->getDadosConsulta2RelatoriosPendentes();
						break;					
					}

        case 3:
          {
						// Buscar Dados 3 
						$dadosConsultaBase = Container::getModel('TbAcompFml');
						$dadosConsultaBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
						$dadosConsultaBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
						$dadosConsulta = $dadosConsultaBase->getDadosConsulta3RelatoriosPendentes();
						break;					
					}
      }

			if  (count($dadosConsulta) > 0) {
				$this->view->dadosPendentes = array ();

				foreach ($dadosConsulta as $index => $arr) {
					array_push($this->view->dadosPendentes, array (
							'dt_acomp' => $arr['dt_acomp'],
							'cd_grp' => $arr['cd_grp'].' - '.$arr['nm_grp'],
							'cd_sbgrp' => $arr['cd_sbgrp'].' - '.$arr['nm_sbgrp'],
							'cd_fml' => $arr['cd_fml'].' - '.$arr['nm_grp_fmlr'],
							'cd_est_acomp' => $arr['cd_est_acomp'],
							'cd_atvd_acomp' => $arr['cd_atvd_acomp']
					));
				}
		
				$this->render('fAConsultarRTRVPendentes');	
		
			} else {

				$this->view->erroValidacao = 3;

				$this->render('fAPreConsultarRTRVPendentes');
			}  
		}

	}	// Fim da function fAConsultarRTRVPendentes

// ====================================================== //	
		
	public function fAPreConsultarRankingFml() {
		
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

			$this->render('fAPreConsultarRankingFml');
		}
	}	// Fim da function fAPreConsultarRankingFml


// ====================================================== //	
	
	public function fAConsultarRankingFml() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Sem Grupo/Subgrupo => acesso somente para cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" && 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			
			$this->nivel_atuacao_requerido = 99;
			
			$this->validaAcessoAcompanhamento();

			// Não Tem o cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
			if ($this->retornoValidaAcessoAcompanhamento == 1) {
				$this->view->erroValidacao = 6;
				$this->render('fAPreConsultarRankingFml');	
			}	else {
				$tipo_tratamento_consulta = 1;
			}
		} else {
			// Com Grupo somente  => acesso somente para cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
			if ($_POST['cb_grupo_escolhido'] != "Escolha Grupo" && 
						$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
				
				$this->nivel_atuacao_requerido = 99;
				
				$this->validaAcessoAcompanhamento();

				// Não Tem o cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
				if ($this->retornoValidaAcessoAcompanhamento == 1) {
					$this->view->erroValidacao = 6;
					$this->render('fAPreConsultarRankingFml');	
				} else {
					$tipo_tratamento_consulta = 2;
				}			
			} else {
				// Com Grupo/Subgrupo => se estiver em grupo/subgrupo ou cd_nível_ace_login 1 ou 2 na tabela tb_cad_login_sess
				if ($_POST['cb_grupo_escolhido'] != "Escolha Grupo" && 
							$_POST['cb_subgrupo_escolhido'] != "Escolha Subgrupo") {
					
					$this->nivel_atuacao_requerido = 99;

					$this->validaAcessoAcompanhamento();

					// Não está na tabela de vinculo de grupo e subgrupo
					if ($this->retornoValidaAcessoAcompanhamento == 1) {
						$this->render('fAPreConsultarRankingFml');				
					} else {
						$tipo_tratamento_consulta = 3;
					}
				}
			}
		}

		if ($this->view->erroValidacao == 0) {
			switch ($tipo_tratamento_consulta) {
        case 1:
          {
						// Buscar Dados 1
						$dadosConsultaBase = Container::getModel('TbFml');
						$dadosConsulta = $dadosConsultaBase->getDadosConsulta1RankingFml();
						break;
					}

        case 2:
          {
						// Buscar Dados 2 
						$dadosConsultaBase = Container::getModel('TbFml');
						$dadosConsultaBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
						$dadosConsulta = $dadosConsultaBase->getDadosConsulta2RankingFml();
						break;					
					}

        case 3:
          {
						// Buscar Dados 3 
						$dadosConsultaBase = Container::getModel('TbFml');
						$dadosConsultaBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
						$dadosConsultaBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
						$dadosConsulta = $dadosConsultaBase->getDadosConsulta3RankingFml();
						break;					
					}
      }

			if  (count($dadosConsulta) > 0) {
				$this->view->dadosRanking = array ();

				foreach ($dadosConsulta as $index => $arr) {
					// Obtém o número de atendimentos que a família teve na comunhão
					$total_assistencia = 0;
					$total_pontos = 0;
					$atributo_faseI = 0;
					$pontos_faseII = 0;
					$pontos_renda = 0;
					$pontos_reatendimento = 0;

					for ($i = 0; $i < 20; $i+=1) {
						if ($i == 0) {
						  $parm_fml = $arr['cd_fml'];
						} else {
						  $parm_fml = $fml_anterior;
						}

						$dadosFamiliaEBase = Container::getModel('TbFml');
						$dadosFamiliaEBase->__set('codFamilia', $parm_fml);
						$dadosFamiliaE = $dadosFamiliaEBase->getDadosFamilia();

						if ($dadosFamiliaE['cd_atndt_ant_fml'] == 0) {      
						  break;
						} else {
							$fml_anterior = $dadosFamiliaE['cd_atndt_ant_fml'];
						  $total_assistencia = $total_assistencia + 1;
						}           
					}

					switch ($total_assistencia) { 
						case 0:
							$atributo_faseI = 0;
							break;
						case 1:
							$atributo_faseI = 1;
							break;
						case 2:
							$atributo_faseI = 2;
							break;
						default:			
							$atributo_faseI = 2;
							break;	
					}

					$dadosFamiliaBase = Container::getModel('TbFml');
					$dadosFamiliaBase->__set('codFamilia', $arr['cd_fml']);
					$dadosFamilia = $dadosFamiliaBase->getDadosFamilia();

					// Obtém o tempo atual de registro da Família na Comunhão
					$data_inicial = $arr['dt_cadastro_fml'];

					// Data Atual
					$data_final = date('Y-m-d');

					// Calcula a diferença em segundos entre as datas
					$diferenca = strtotime($data_final) - strtotime($data_inicial);
					// Calcula a diferença em dias
					$nr_dias = floor($diferenca / (60 * 60 * 24));

					if ($nr_dias >= 0 && $nr_dias <= 90) {
						$pontos_faseII		=	5;
					} else if ($nr_dias >= 91 && $nr_dias <= 120) {
						$pontos_faseII		=	10;
					} else if ($nr_dias >= 121 && $nr_dias <= 180) {
						$pontos_faseII		=	15;
					} else if ($nr_dias >= 181 && $nr_dias <= 210) {
						$pontos_faseII		=	20;
					} else {
						$pontos_faseII		=	25;
					}

					$total_pontos = $total_pontos + $pontos_faseII;
				
					// Obtém a Renda Per Capita da Família
					$renda_familia	=	$dadosFamilia['vlr_aprox_renda_mensal_fml'];

					// Obter Quantidade de Integrantes da Família
					$qtdIntegrantes = Container::getModel('TbIntegFml');
					$qtdIntegrantes->__set('cd_fml', $arr['cd_fml']);
					$qtdIntegrantesBase = $qtdIntegrantes->getQtdIntegrantes();
					
					$total_integrantes = $qtdIntegrantesBase['qtde'];

					$renda_per_capita	=	round($renda_familia / $total_integrantes, 2);

					if ($renda_per_capita >= 0 && $renda_per_capita <= 49.99) {
						$pontos_renda		=	50;
					} else if ($renda_per_capita >= 50 && $renda_per_capita <= 99.99) {
						$pontos_renda		=	45;
					} else if ($renda_per_capita >= 100 && $renda_per_capita <= 149.99) {
						$pontos_renda		=	40;
					} else if ($renda_per_capita >= 150 && $renda_per_capita <= 199.99) {
						$pontos_renda		=	35;
					} else {
						$pontos_renda		=	30;
					}

					$total_pontos = $total_pontos + $pontos_renda;

					// Obter Quantidade de vezes que o Integrante principal esteve em outras famílias
					$qtdIntegranteRanking = Container::getModel('TbIntegFml');
					$qtdIntegranteRanking->__set('cd_fml', $arr['cd_fml']);
					$qtdIntegranteRanking->__set('nm_integ', $dadosFamilia['nm_astd_prin']);
					$qtdIntegranteRankingBase = $qtdIntegranteRanking->getQtdNomeIntegranteRanking();
					
					$qtd_atendimentos = $qtdIntegranteRankingBase['qtde'];

					switch ($qtd_atendimentos) { 
							case 0:
								$pontos_reatendimento = 30;
								break;
							case 1:
								$pontos_reatendimento = 0;
								break;
							default:			
								$pontos_reatendimento = -30;
								break;	
					}

					$total_pontos = $total_pontos + $pontos_reatendimento;

					$dataCadastro_f = Funcoes::formatarNumeros('data', $arr['dt_cadastro_fml'], 10, "AMD");	

					array_push($this->view->dadosRanking, array (
							'dt_cadastro_fml' => $dataCadastro_f,						
							'cd_grp_s' => $arr['cd_grp'],
							'cd_grp' => $arr['cd_grp'].' - '.$arr['nm_grp'],
							'cd_sbgrp_s' => $arr['cd_sbgrp'],
							'cd_sbgrp' => $arr['cd_sbgrp']. ' - '.$arr['nm_sbgrp'],
							'cd_fml' => $arr['cd_fml'].' - '.$arr['nm_grp_fmlr'],						
							'nrAssistencia' => $atributo_faseI,
							'tempoRegistro' => $pontos_faseII,
							'rendaPerCapita' => $pontos_renda,
							'reatendimento' => $pontos_reatendimento,
							'totalPontos' => $total_pontos,
							'posicaoRanking' => 0
					));

				}	// Fim foreach()

				// Ordenar tabela 
				$clause =  'cd_grp_s ASC, totalPontos DESC, cd_sbgrp_s ASC';
				Funcoes::orderByArray( $this->view->dadosRanking, $clause );

				$cnt = 0;
				$cont = 0;
				$grp = 0;
				$subgrp = 0;

				// Trata posição da família no ranking
				foreach ($this->view->dadosRanking as $index => $row) {
					if ($grp != $row['cd_grp_s']) {
						$cont = 1;
						$grp = $row['cd_grp_s'];
						$subgrp = $row['cd_sbgrp_s'];
					} else {
						if ($subgrp == $row['cd_sbgrp_s']) {
							$cont = $cont + 1;
						}	else {
							$cont = 1;
						}
						
						$grp = $row['cd_grp_s'];
						$subgrp = $row['cd_sbgrp_s'];
					} 

					// Atualiza pontuação e a posição da família no ranking do subgrupo
					$atualizaPtsPosicao = Container::getModel('TbFml');
					$atualizaPtsPosicao->__set('cd_fml', $row['cd_fml']);
					$atualizaPtsPosicao->__set('ptc_atendto_fml', $row['totalPontos']);
					$atualizaPtsPosicao->__set('pos_ranking_atendto_fml', $cont);
					$atualizaPtsPosicao->updatePontosPosicaoRanking();

					$this->view->dadosRanking[$cnt]['posicaoRanking'] = $cont;

					$cnt = $cnt + 1;
				}

				$this->render('fAConsultarRankingFml');	
		
			} else {

				$this->view->erroValidacao = 3;

				$this->render('fAPreConsultarRankingFml');
			}  
		}

	}	// Fim da function fAConsultarRankingFml


}	//	Fim da classe

?>
				