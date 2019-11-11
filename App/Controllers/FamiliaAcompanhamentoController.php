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

		// Esta pesquisa na maioria das vezesserá feita neste controller, pois na entrada se está filtrando 
		// por grupo/subgrupo, exceto consultas, onde o voluntário está vinculado, ou seja, se não estiver vinculado não 
		// aparecerá para ele pesquisar

		// Busca o cd_atu_vlnt no grupo/subgrupo
		$atuacaoVoluntarioBase = Container::getModel('TbVnclVlntGrp');
		$atuacaoVoluntarioBase->__set('codVoluntario', $_SESSION['id']);
		$atuacaoVoluntarioBase->__set('codGrupo', $_POST['cb_grupo_escolhido']);
		$atuacaoVoluntarioBase->__set('codSubgrupo',  $_POST['cb_subgrupo_escolhido']);
		$atuacaoVoluntario = $atuacaoVoluntarioBase->getNivelAtuacao();

		// Não está na tabela tb_vncl_vlnt_grp, ou seja, não está atrelado ao grupo/subgrupo
		if (empty($atuacaoVoluntario['cod_atuacao'])) { 
			// Nível 1 e 2 tem acesso
			$nivel_acesso_requerido = 2;
			$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

			// Não tem Nível 1 ou 2
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

			// Está na tabela tb_vncl_vlnt_grp, mas não tem o nível Atuação Requerido
			if ($atuacaoVoluntario['cod_atuacao'] != $this->nivel_atuacao_requerido) { 
				// Coordenador Geral acessa todas as funções
				if ($atuacaoVoluntario['cod_atuacao'] != 4) {
					//  99 abre acesso para todos do grupo (consultas)
					if ($this->nivel_atuacao_requerido != 99) {

						$this->view->erroValidacao = 6;

						// Buscar Nome de Grupo e Subgrupo
						$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
						$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
						$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
						$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();

						$this->view->grupoTratado = $dadosGS['nome_grupo'];
						$this->view->subgrupoTratado = $dadosGS['nome_subgrupo'];

						$this->view->atuacaoLogado = '';
						$this->view->atuacaoRequerida = '';

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
		}

	}	// Fim da function validaAcessoAcompanhamento

// ================================================== //
//          Início de Acompanhamento de Família             //
// ================================================== //

	public function familiaAcompanhamento() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		// Atualiza relação de família com pendêcias de relatório
		$this->atualizaqtdPendenciasRelatorios();

		// Atualiza relação de ranking de famílias aguardando atendimento
		$this->atualizaQtdRankingFamilias();

		$this->render('familiaAcompanhamento');
	}

// ====================================================== //	
		
	public function fAPreConcluirRT() {
		
		$this->validaAutenticacao();

		$this->view->codVoluntario = $_SESSION['id'];		

		// Verifica se tem nível de acesso requerido
		$nivel_acesso_requerido = 3;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdPendenciasRelatorios();			

			$this->atualizaQtdRankingFamilias();

			$this->render('familiaAcompanhamento');				
		
		} else {
			$this->view->erroValidacao = 0;

			$this->render('fAPreConcluirRT');
		}
	}	// Fim da function fAPreConcluirRT

// ====================================================== //	
	
	public function fAConcluirRT() {

		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

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

				$cd_atvd_acomp      = 1;  		// Triagem
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
				$cd_est_acomp3      = 3;			// Revisão da Triagem concluída
				$cd_avalia_triagem2 = 2;			// Necessita de outra triagem 
				$cd_avalia_triagem4 = 4;			// Deverá retornar a fila de espera
				$cd_est_situ_fml    = 2;  		// Aguardando Triagem
				
				// Buscar Familias que estejam nas tabelas tb_fml e tb_vncl_fml_sbgrp
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
				$familiasVnclGrupoBase->__set('cd_est_acomp1', $cd_est_acomp1);
				$familiasVnclGrupoBase->__set('cd_est_acomp3', $cd_est_acomp3);
				$familiasVnclGrupoBase->__set('cd_avalia_triagem2', $cd_avalia_triagem2);
				$familiasVnclGrupoBase->__set('cd_avalia_triagem4', $cd_avalia_triagem4);
				$familiasVnclGrupoBase->__set('cd_est_situ_fml', $cd_est_situ_fml);
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompanhamento();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
						// Formatar para aparecer na tela o significado da situação da família
						$this->obtemEstSituFml($arr['cd_est_situ_fml']);
						$cd_est_situ_fml =  $this->view->cd_est_situ_fml;

						// Obtem Sequencial de Acompanhamento Atual
						$seqlAcomp = Container::getModel('TbAcompFml');
						$seqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
						$seqlAcomp->getSequencial();			

						// Não há nenhum acompanhamento cadastrado para a família
						if (empty($seqlAcomp->__get('seql_max'))) {
							//$seql_acomp = 0;
							$cadastro = 'n';
							$dataAcomp_f	= '';
						  $pontuacao_fml = 0;
						  $posicao_ranking_fml = 0;

						} else {

							// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
							$dadosAcompBase = Container::getModel('TbAcompFml');
							$dadosAcompBase->__set('cd_fml', $arr['cd_fmlID']);
							$dadosAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
							$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

							// Já houve triagem anterior
							if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
								$cadastro = 'n';
								$dataAcomp_f	= '';
							  $pontuacao_fml = 0;
							  $posicao_ranking_fml = 0;

							} else {

								// Obter dados acompanhamento //
								$dataAcompBase = Container::getModel('TbAcompFml');
								$dataAcompBase->__set('cd_fml', $arr['cd_fmlID']);
								$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));							
								$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
								$dataAcompBase->__set('cd_est_ini', $cd_est_acomp1);
								$dataAcompBase->__set('cd_est_fim', $cd_est_acomp1);
								$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

							  $dataAcomp_f = Funcoes::formatarNumeros('data', $dataAcomp['dt_acomp'], 10, "AMD");

							  $pontuacao_fml = $arr['ptc_atendto_fml'];
							  $posicao_ranking_fml = $arr['pos_ranking_atendto_fml'];
							}
						}

						array_push($this->view->familia, array (
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
								'cd_fml' => $arr['cd_fmlID'],
								'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
								'dt_cadastro_fml' => $dataAcomp_f,
								'cd_est_situ_fml' => $cd_est_situ_fml,
								'ptc_atendto_fml' => $pontuacao_fml,
								'pos_ranking_atendto_fml' => $posicao_ranking_fml
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

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;	
		}

		// TU
		$cd_atvd_acomp = 1;  											// Triagem
		$cd_est_acomp1 = 1;												// Pendente de Término de registro de triagem
		$atuacao_voluntario_acompanhamento = 3;		// Visitador

		$this->view->segmento1 = 0;
		$this->view->segmento2 = 0;
		$this->view->segmento3 = 0;
		$this->view->segmento4 = 0;
		$this->view->segmento5 = 0;
		$this->view->segmento6 = 0;
		$this->view->segmento7 = 0;
		$this->view->segmento8 = 0;
		$this->view->vnclVlntAcomp = 0;

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

		// LÓGICA NOVA COMEÇA

		// TU
		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		// Não há nenhum acompanhamento cadastrado ainda
		if (empty($seqlAcomp->__get('seql_max'))) {
			$data_hoje 		= 	new \DateTime();
			$dataAcomp_f	= 	$data_hoje->format("d/m/Y");

			$dsc_mtv_cd_avalia_triagem = '';
			$dsc_consid_finais_triagem = '';
			$cd_fml_subs_triagem = '';
			$cd_avalia_triagem = '2';
			$cd_crit_engjto = '1';
			$seql_acomp = 0;

		} else {

			$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$data_hoje 		= 	new \DateTime();
				$dataAcomp_f	= 	$data_hoje->format("d/m/Y");

				$dsc_mtv_cd_avalia_triagem = '';
				$dsc_consid_finais_triagem = '';
				$cd_fml_subs_triagem = '';
				$cd_avalia_triagem = '2';
				$cd_crit_engjto = '1';
				$seql_acomp = 0;

			} else {		// É a triagem atual em andamento
				// Obter dados acompanhamento //
				$dataAcompBase = Container::getModel('TbAcompFml');
				$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
				$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
				$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
				$dataAcompBase->__set('cd_est_ini', $cd_est_acomp1);
				$dataAcompBase->__set('cd_est_fim', $cd_est_acomp1);
				$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

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
					$cd_fml_subs_triagem = '';
				} else {
					$cd_fml_subs_triagem = $dataAcomp['cd_fml_subs_triagem'];
				}

				$cd_avalia_triagem = $dataAcomp['cd_avalia_triagem'];
				$cd_crit_engjto = $dadosFamiliaBase['cd_crit_engjto'];
				$seql_acomp = $seqlAcomp->__get('seql_max');

				// Verificar se há Segmentos Cadastrados
				for ($i = 1; $i <= 8; $i++) {
					$verificaTriagem0 = Container::getModel('TbSegmtoTriagemFml');
					$verificaTriagem0->__set('codFamilia', $_POST['cb_familia_escolhida']);
					$verificaTriagem0->__set('seqlAcomp', $seqlAcomp->__get('seql_max'));			
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

				// Verificar se há vínculo Voluntário cadastrado
				$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
				$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
				$verificaVinculo->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
				$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
				$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

				if ($verificaVinculoBase['qtde'] > 1) {
					$this->view->vnclVlntAcomp = 1;
				}

				$seql_acomp = $seqlAcomp->__get('seql_max');
			}
		}

		$this->view->dadosAcompanhamento = array (
						'cd_grp' => $_POST['cb_grupo_escolhido'], 
						'nm_grp' => $nomeGrupo, 
						'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
						'nm_sbgrp' => $nomeSubgrupo, 
						'cd_fml' => $_POST['cb_familia_escolhida'],
						'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
						'dt_acomp' => $dataAcomp_f,
						'cd_avalia_triagem' => $cd_avalia_triagem,
						'dsc_mtv_cd_avalia_triagem' => $dsc_mtv_cd_avalia_triagem,
						'cd_crit_engjto' => $cd_crit_engjto,
						'dsc_consid_finais_triagem' => $dsc_consid_finais_triagem,
						'seql_acomp' => $seql_acomp
		);

		$this->render('fAConcluirRTMenu');				

	}	// Fim da function fAConcluirRTMenu

// ====================================================== //	
	
	public function fAConcluirRTBaseAtualiza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;
		
		// Pode ter havido inclusão de algum segmento/voluntário antes de atualizar a conclusão, o que fez inclusão na tabela tb_acomp_fml
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// Não há nenhum acompanhamento cadastrado ainda
		if (empty($seqlAcomp->__get('seql_max'))) {
			// Obtem Próximo Sequencial de Acompanhamento
			$proxSeqlAcomp = Container::getModel('TbAcompFml');
			$proxSeqlAcomp->__set('cd_fml', $arr['cd_fmlID']);
			$proxSeqlAcomp->getProximoSequencial();			

			// Insere na tabela tb_acomp_fml
			$insereTbAcompFml = Container::getModel('TbAcompFml');
			$insereTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbAcompFml->__set('seql_acomp', $proxSeqlAcomp->__get('seql_max'));
			$insereTbAcompFml->__set('cd_atvd_acomp', 1);
			$insereTbAcompFml->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
			$insereTbAcompFml->__set('dt_acomp', $_POST['dt_acomp']);
			$insereTbAcompFml->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
			$insereTbAcompFml->__set('dsc_consid_finais_triagem', $_POST['dsc_consid_finais_triagem']);
			$insereTbAcompFml->insertAcompanhamentoFamilia();	

			// Atualizar tb_fml
			$atualizaTbFml = Container::getModel('TbFml');
			$atualizaTbFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTbFml->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
			$atualizaTbFml->updateCritEngajamentoFamilia();

			// Inserir Voluntário na tabela tb_vncl_vlnt_acomp_fml
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $proxSeqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		
		} else {

			$cd_est_acomp1 = 1;			// Pendente de Término de registro de Triagem/Visita

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				// Obtem Próximo Sequencial de Acompanhamento
				$proxSeqlAcomp = Container::getModel('TbAcompFml');
				$proxSeqlAcomp->__set('cd_fml', $_POST['cd_fml']);
				$proxSeqlAcomp->getProximoSequencial();			

				// Insere na tabela tb_acomp_fml
				$insereTbAcompFml = Container::getModel('TbAcompFml');
				$insereTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
				$insereTbAcompFml->__set('seql_acomp', $proxSeqlAcomp->__get('seql_max'));
				$insereTbAcompFml->__set('cd_atvd_acomp', 1);
				$insereTbAcompFml->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
				$insereTbAcompFml->__set('dt_acomp', $_POST['dt_acomp']);
				$insereTbAcompFml->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
				$insereTbAcompFml->__set('dsc_consid_finais_triagem', $_POST['dsc_consid_finais_triagem']);
				$insereTbAcompFml->insertAcompanhamentoFamilia();	

				// Atualizar tb_fml
				$atualizaTbFml = Container::getModel('TbFml');
				$atualizaTbFml->__set('cd_fml', $_POST['cd_fml']);
				$atualizaTbFml->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
				$atualizaTbFml->updateCritEngajamentoFamilia();

				// Inserir Voluntário na tabela tb_vncl_vlnt_acomp_fml
				$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
				$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
				$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
				$insereVoluntarioTVVAF->__set('seql_acomp', $proxSeqlAcomp->__get('seql_max'));
				$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
				$insereVoluntarioTVVAF->insertTVVAF();

			} else {

				// Atualiar tb_acomp_fml
				$atualizaTbAcomp = Container::getModel('TbAcompFml');
				$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
				$atualizaTbAcomp->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
				$atualizaTbAcomp->__set('dt_acomp', $_POST['dt_acomp']);			
				$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
				$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
				$atualizaTbAcomp->__set('dsc_consid_finais_triagem', $_POST['dsc_consid_finais_triagem']);
				$atualizaTbAcomp->__set('cd_est_acomp', 1);
				$atualizaTbAcomp->updateRTAtualiza_D();

				// Atualizar tb_fml
				$atualizaTbFml = Container::getModel('TbFml');
				$atualizaTbFml->__set('cd_fml', $_POST['cd_fml']);
				$atualizaTbFml->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
				$atualizaTbFml->updateCritEngajamentoFamilia();
				
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
			}
		}

		// Remete mensagem de atualização realizada com sucesso
		$this->view->erroValidacao = 4;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAConcluirRTMenu();				

	}	// Fim da function fAConcluirRTBaseAtualiza

// ====================================================== //	
	
	public function fAConcluirRTBaseConclui() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// TU
		$ha_todos_segmentos = 1;
		$ha_voluntarios = 1;

		// Verificar se há todos os segmentos cadastrados
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $_POST['seql_acomp']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagemAll();

		if ($verificaTriagemBase['qtde'] < 8) {
			$ha_todos_segmentos = 0;
		}

		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cd_fml']);
		$verificaVinculo->__set('seql_acomp', $_POST['seql_acomp']);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', 3);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] == 0) {
			$ha_voluntarios = 0;
		}

		if ($ha_todos_segmentos == 1 && $ha_voluntarios == 1) {
			// Atualiar tb_acomp_fml
			$atualizaTbAcomp = Container::getModel('TbAcompFml');
			$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTbAcomp->__set('seql_acomp', $_POST['seql_acomp']);
			$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
			$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
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
				// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
				$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
				$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
				$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
				$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
				$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
				$insereVoluntarioTVVAF->insertTVVAF();
			} else {
				// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
				$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
				$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
				$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
				$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
				$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
				$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
			}
		
			$this->view->erroValidacao = 4;
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 1;
			$this->view->codFamilia = $_POST['cd_fml'];
			$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

			session_write_close();
			$this->fAConcluirRT();				

		} else {
			
			$this->view->erroValidacao = 2;			
			
			session_write_close();			
			$this->fAConcluirRTMenu();
		}

	}	// Fim da function fAConcluirRTBaseConclui


// ====================================================== //	

	public function fAPreRevisarRT() {
		
		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];		

		// Verifica se tem nível de acesso requerido
		$nivel_acesso_requerido = 3;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdPendenciasRelatorios();

			$this->atualizaQtdRankingFamilias();

			$this->render('familiaAcompanhamento');				

		} else {
			$this->view->erroValidacao = 0;

			$this->render('fAPreRevisarRT');
		}
	}	// Fim da function fAPreRevisarRT

// ====================================================== //	

	public function fARevisarRT() {

		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];		

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

				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompRevisaoRT();
                                                     
				if  (count($familiasVnclGrupo) > 0) {
					$this->view->familia = array ();

					foreach ($familiasVnclGrupo as $index => $arr) {
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
						//}

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

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

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

			$cd_atvd_acomp  = 1;  	// Triagem
			$cd_est_acomp2  = 2;	// Pendente de Término de revisão

			// Obter dados acompanhamento //
			$dataAcompBase = Container::getModel('TbAcompFml');
			$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
			$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
			$dataAcompBase->__set('cd_est_ini', $cd_est_acomp2);
			$dataAcompBase->__set('cd_est_fim', $cd_est_acomp2);
			$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

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
							'seql_acomp' => $seqlAcomp->__get('seql_max')
			);

			$this->render('fARevisarRTMenu');				
		}
	}	// Fim da function fARevisarRTMenu

// ====================================================== //	
	
	public function fARevisarRTBaseAtualiza() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Atualiar tb_acomp_fml
		$atualizaTbAcomp = Container::getModel('TbAcompFml');
		$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbAcomp->__set('seql_acomp', $_POST['seql_acomp']);
		$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
		$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
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

		$this->view->erroValidacao = 4;

		session_write_close();
		$this->fARevisarRTMenu();				

	}	// Fim da function fARevisarRTBaseAtualiza

// ====================================================== //	
	
	public function fARevisarRTBaseConclui() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Atualizar tb_acomp_fml
		$atualizaTbAcomp = Container::getModel('TbAcompFml');
		$atualizaTbAcomp->__set('cd_fml', $_POST['cd_fml']);
		$atualizaTbAcomp->__set('seql_acomp', $_POST['seql_acomp']);
		$atualizaTbAcomp->__set('cd_avalia_triagem', $_POST['cd_avalia_triagem']);
		$atualizaTbAcomp->__set('dsc_mtv_cd_avalia_triagem', $_POST['dsc_mtv_cd_avalia_triagem']);
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
		
	public function fAPreConcluirRV() {
		
		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];		

		// Verifica se tem nível de acesso requerido
		$nivel_acesso_requerido = 3;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdPendenciasRelatorios();

			$this->atualizaQtdRankingFamilias();

			$this->render('familiaAcompanhamento');				

		} else {
			$this->view->erroValidacao = 0;

			$this->render('fAPreConcluirRV');
		}
	}	// Fim da function fAPreConcluirRV


// ====================================================== //	
	
	public function fAConcluirRV() {

		$this->validaAutenticacao();	

		$this->view->codVoluntario = $_SESSION['id'];					

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

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

				$cd_atvd_acomp1 		= 1;  			// Triagem
				$cd_atvd_acomp2 		= 2;  			// Visita
				$cd_est_acomp1  		= 1;			// Pendente de Término de registro de Triagem/Visita
				$cd_est_acomp3  		= 3;			// Revisão concluída
				$estado_situacao_fml 	= 3;  			// Em atendimento
				
				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp1',  $cd_atvd_acomp1);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp2',  $cd_atvd_acomp2);
				$familiasVnclGrupoBase->__set('cd_est_acomp1',  $cd_est_acomp1);
				$familiasVnclGrupoBase->__set('cd_est_acomp3',  $cd_est_acomp3);
				$familiasVnclGrupoBase->__set('cd_est_situ_fml',  $estado_situacao_fml);
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompConclusaoRV();
                                                     
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
						$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp2);
						$dataAcompBase->__set('cd_est_ini', $cd_est_acomp1);
						$dataAcompBase->__set('cd_est_fim', $cd_est_acomp1);
						$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

						// TU
						// Há acompanhamento cadastrado
						if  (!empty($dataAcomp)) {						
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
									'cadastro' => $arr['cd_fmlID'].';s'
							));

						} else {

							array_push($this->view->familia, array (
									'cd_grp' => $_POST['cb_grupo_escolhido'], 
									'nm_grp' => $nomeGrupo, 
									'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
									'nm_sbgrp' => $nomeSubgrupo, 
									'cd_fml' => $arr['cd_fmlID'],
									'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
									'dt_cadastro_fml' => '',
									'cd_est_situ_fml' => $cd_est_situ_fml,
									'cadastro' => $arr['cd_fmlID'].';n'
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

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		$this->view->vnclVlntAcomp = 0;

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

		// Já há visita cadastrada na tabela tb_acomp_fml
		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$seqlAcomp->getSequencial();			

		$cd_atvd_acomp = 2;  	// Visita
		$cd_est_acomp1 = 1;		// Pendente de Término de revisão

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
		$dataAcompBase->__set('cd_est_ini', $cd_est_acomp1);
		$dataAcompBase->__set('cd_est_fim', $cd_est_acomp1);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		// Há acompanhamento cadastrado
		if  (!empty($dataAcomp)) {						
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
				'origem' => $_POST['origem'],
				'cadastro' => 's',
				'seql_acomp' => $seqlAcomp->__get('seql_max')
			);

			// Verificar se há vínculo de voluntários cadastrados como visitador = 3
			$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
			$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$verificaVinculo->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$verificaVinculo->__set('cd_atua_vlnt_acomp', 3);
			$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

			if ($verificaVinculoBase['qtde'] > 0) {
				$this->view->vnclVlntAcomp = 1;
			}

		} else {

			$data_hoje 		= 	new \DateTime();
			$data_hoje 		= 	$data_hoje->format("Y-m-d");

			$dataAcomp_f = Funcoes::formatarNumeros('data', $data_hoje, 10, "AMD");

			$this->view->dadosAcompanhamento = array (
				'cd_grp' => $_POST['cb_grupo_escolhido'],
				'nm_grp' => $_POST['nome_grupo'],
				'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'],
				'nm_sbgrp' => $_POST['nome_subgrupo'],
				'cd_fml' => $_POST['cb_familia_escolhida'],
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
				'dt_acomp' => $dataAcomp_f,
				'nr_bolsa_canguru' =>'',	
				'vlr_doado_finan_comunhao_visita' => '',
				'vlr_doado_sbgrp_visita' => '',
				'dsc_item_doado_comunhao_visita' => '',
				'dsc_item_doado_sbgrp_visita' => '',
				'dsc_resumo_visita' => '',
				'dsc_meta_alcan' => '',
				'dsc_pend_visita' => '',
				'dsc_consid_final' => '',
				'dsc_recado_to_coordenacao' => '',
				'origem' => $_POST['origem'],
				'cadastro' => 'n',
				'seql_acomp' => 0
			);
		}

		$this->render('fAConcluirRVMenu');				

	}	// Fim da function fAConcluirRVMenu

// ====================================================== //	
	
	public function fAConcluirRVBaseAtualiza() {
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		// TU
		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		$cd_atvd_acomp = 2;  	// Visita
		$cd_est_acomp1 = 1;		// Pendente de Término de revisão

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cd_fml']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
		$dataAcompBase->__set('cd_est_ini', $cd_est_acomp1);
		$dataAcompBase->__set('cd_est_fim', $cd_est_acomp1);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		// Há acompanhamento cadastrado
		if  (!empty($dataAcomp)) {						
			// Atualiza na tabela tb_acomp_fml
			$alteraTbAcompFml = Container::getModel('TbAcompFml');
			$alteraTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$alteraTbAcompFml->__set('dt_acomp', $_POST['dt_acomp']);
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
		
		} else {

			// Obtem Próximo Sequencial de Acompanhamento
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getProximoSequencial();			

			// Insere na tabela tb_acomp_fml
			$insereTbAcompFml = Container::getModel('TbAcompFml');
			$insereTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbAcompFml->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereTbAcompFml->__set('cd_atvd_acomp', 2);
			$insereTbAcompFml->__set('cd_avalia_triagem', 0);
			$insereTbAcompFml->__set('dt_acomp', $_POST['dt_acomp']);
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
			$insereTbAcompFml->__set('cd_est_acomp', 1);
			$insereTbAcompFml->insertAcompanhamentoFamilia();	

			// Inserir Voluntário na tabela tb_vncl_vlnt_acomp_fml
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();			
		}

		$this->view->erroValidacao = 4;

		session_write_close();
		$this->fAConcluirRVMenu();				

	}	// Fim da function fAConcluirRVBaseAtualiza

// ====================================================== //	
	
	public function fAConcluirRVBaseConclui() {
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		$cd_atvd_acomp = 2;  	// Visita
		$cd_est_acomp1 = 1;		// Pendente de Término de revisão

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cd_fml']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
		$dataAcompBase->__set('cd_est_ini', $cd_est_acomp1);
		$dataAcompBase->__set('cd_est_fim', $cd_est_acomp1);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

		// Não pode concluir revisão sem haver atualizado a mesma	
		if (empty($dataAcomp)) {						
			$this->view->erroValidacao = 5;
			$this->view->codFamilia = $_POST['cd_fml'];
			$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

			session_write_close();
			$this->fAConcluirRVMenu();	
			exit;			
		}

		// Não pode concluir revisão sem haver ao menos um voluntário cadastrado na visita
		// Há acompanhamento de Visita Cadastrado
		if (!empty($dataAcomp)) {						
			// Verificar se há vínculo cadastrado
			$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
			$verificaVinculo->__set('cd_fml', $_POST['cd_fml']);
			$verificaVinculo->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$verificaVinculo->__set('cd_atua_vlnt_acomp', 3);
			$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

			if ($verificaVinculoBase['qtde'] == 0) {
				$this->view->erroValidacao = 6;
				$this->view->codFamilia = $_POST['cd_fml'];
				$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

				session_write_close();
				$this->fAConcluirRVMenu();	
				exit;			
			}
		}

		// DD/MM/AAAA
		$dataAcomp_formatada = Funcoes::formatarNumeros('data', $_POST['dt_acomp'], 10, "DMA");

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
		
	public function fAPreRevisarRV() {
		
		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];		

		// Verifica se tem nível de acesso requerido
		$nivel_acesso_requerido = 3;
		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->atualizaqtdPendenciasRelatorios();

			$this->atualizaQtdRankingFamilias();

			$this->render('familiaAcompanhamento');				

		} else {

			$this->view->erroValidacao = 0;

			$this->render('fAPreRevisarRV');
		}

	}	// Fim da function fAPreRevisarRV

// ====================================================== //	
	
	public function fARevisarRV() {

		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];					

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		if (!isset($this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio)) {
			$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 0;
		}

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

				$cd_atvd_acomp2 = 2;  		// Visita
				$cd_est_acomp2  = 2;		// Pendente de Término de revisão
				$estado_situacao_fml = 3;  	// Em atendimento
				
				// Buscar Familias que estejam nas tabelas tb_fml, tb_vncl_fml_sbgrp e tb_acomp_fml, com:
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp2',  $cd_atvd_acomp2);
				$familiasVnclGrupoBase->__set('cd_est_acomp2',  $cd_est_acomp2);
				$familiasVnclGrupoBase->__set('cd_est_situ_fml',  $estado_situacao_fml);
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompRevisaoRV();
                                                     
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
						$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp2);
						$dataAcompBase->__set('cd_est_ini', $cd_est_acomp2);
						$dataAcompBase->__set('cd_est_fim', $cd_est_acomp2);
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

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

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

		$cd_atvd_acomp = 2;  // Visita
		$cd_est_acomp2 = 2;	// Pendente de Término de revisão

		// Obter dados acompanhamento //
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
		$dataAcompBase->__set('cd_est_ini', $cd_est_acomp2);
		$dataAcompBase->__set('cd_est_fim', $cd_est_acomp2);
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


		$this->view->erroValidacao = 4;

		session_write_close();
		$this->fARevisarRVMenu();				

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

		$this->view->codVoluntario = $_SESSION['id'];		

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

			$this->atualizaQtdRankingFamilias();

			$this->render('familiaAcompanhamento');				
		
		} else {
			$this->view->erroValidacao = 0;

			$this->render('fAPreIncluirRD');		
		}
	}	// Fim da function fAPreIncluirRD


// ====================================================== //	
	
	public function fAIncluirRD() {
		
		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];		

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

				$cd_atvd_acomp2 = 2;  			// Visita
				$cd_est_acomp3  = 3;				// Revisão Concluída
				$estado_situacao_fml = 3;  	// Em atendimento
				
				// Buscar Familias atreladas ao Grupo e Subgrupo e que estejam com cd_est_situ_fml = 3 (Em atendimento)
				// (estava dt_prev_term_acomp <= data atual, mas retirei, pois pode ser que familias possam ser substituidas
				// antes ou depois da previsão. O sistema fica flexível desse modo).
				$familiasVnclGrupoBase = Container::getModel('TbFml');
				$familiasVnclGrupoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$familiasVnclGrupoBase->__set('cd_atvd_acomp2',  $cd_atvd_acomp2);
				$familiasVnclGrupoBase->__set('cd_est_acomp3',  $cd_est_acomp3);
				$familiasVnclGrupoBase->__set('cd_est_situ_fml',  $estado_situacao_fml);
				$familiasVnclGrupo = $familiasVnclGrupoBase->getFamiliaPorGrupoAcompConcluirRD();

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
						$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp2);
						$dataAcompBase->__set('cd_est_ini', $cd_est_acomp3);
						$dataAcompBase->__set('cd_est_fim', $cd_est_acomp3);
						$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();
						
						// Buscar dados Família
						$dadosFamilia = Container::getModel('TbFml');
						$dadosFamilia->__set('codFamilia', $arr['cd_fmlID']);
						$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

						$dataInicioAcomp_f = Funcoes::formatarNumeros('data', $dadosFamiliaBase['dt_inc_acomp'], 10, "AMD");
						  
						$dataTerminoAcomp_f = Funcoes::formatarNumeros('data', $dadosFamiliaBase['dt_prev_term_acomp'], 10, "AMD");

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

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 
	
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

		$cd_atvd_acomp2 = 2;  // Visita
		$cd_est_acomp3 = 3;		// Acompanhamento Concluído (Revisado)

		// Obter dados acompanhamento 
		$dataAcompBase = Container::getModel('TbAcompFml');
		$dataAcompBase->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$dataAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));			
		$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp2);
		$dataAcompBase->__set('cd_est_ini', $cd_est_acomp3);
		$dataAcompBase->__set('cd_est_fim', $cd_est_acomp3);
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
		$somatorioAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp2);
		$somatorioAcompBase->__set('cd_est_ini', $cd_est_acomp3);
		$somatorioAcompBase->__set('cd_est_fim', $cd_est_acomp3);
		$somatorioAcomp = $somatorioAcompBase->getSomaValoresVisita();

		$somatorio_comunhao = number_format($somatorioAcomp['soma_comunhao'], 2, ',', '.');
		$somatorio_subgrupo = number_format($somatorioAcomp['soma_subgrupo'], 2, ',', '.');

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
			'vlr_doado_sbgrp_visita' => $somatorio_subgrupo
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

		$this->view->erroValidacao = 4;

		session_write_close();
		$this->fAConcluirRDMenu();				

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

		// Nova Regra - Busca a família melhor rankeada pelo RA e poe em espera de triagem e informa que ela substituiu
		// Buscar Dados das Famílias ainda sem atendimento e também sem grupo/subgrupo escolhidos
		$dadosConsultaBase = Container::getModel('TbFml');
		$this->view->dadosConsulta = $dadosConsultaBase->getDadosConsulta1RankingFml();

		if  (count($this->view->dadosConsulta) > 0) {
			$this->geraRankingFmlParaAtendimento();

			// Buscar dados Família Atual sendo Desligada
			$dadosFamilia = Container::getModel('TbFml');
			$dadosFamilia->__set('codFamilia', $_POST['cd_fml']);
			$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

			$regAdm = $dadosFamiliaBase['cd_reg_adm'];

			// Buscar Família Melhor rankeada na Região Administrativa da Família Desligada
			$fmlMelhorRankeada = Container::getModel('TbFml');
			$fmlMelhorRankeada->__set('cd_reg_adm', $regAdm);
			$fmlMelhorRankeadaBase = $fmlMelhorRankeada->getFmlMelhorNoRanking();

			$fmlSubstituta = $fmlMelhorRankeadaBase['cd_fml_substituta'];

			// Atualiza Família substituta com o cd_atendto_fml_subs = $_POST['cd_fml'] e altera o cd_est_situ_fml para 2
			$atualizaFmlSubstituta = Container::getModel('TbFml');
			$atualizaFmlSubstituta->__set('cd_fml_substituta', $fmlSubstituta);
			$atualizaFmlSubstituta->__set('cd_fml_substituida', $_POST['cd_fml']);
			$atualizaFmlSubstituta->updateFmlSubstituida();

			// Busca grupo e subgrupo da Família substituída
			$pesqGrpSbgrp = Container::getModel('TbVnclFmlSbgrp');
			$pesqGrpSbgrp->__set('codFamilia_pesq', $_POST['cd_fml']);
			$pesqGrpSbgrpBase = $pesqGrpSbgrp->getDadosVinculoFamilia();

			// Vincula Família substituta com grupo e subgrupo da família substituída
			$insertVnclFmlGrpSbgrp = Container::getModel('TbVnclFmlSbgrp');
			$insertVnclFmlGrpSbgrp->__set('codGrupo', $pesqGrpSbgrpBase['cd_grpID']);
			$insertVnclFmlGrpSbgrp->__set('codSubgrupo', $pesqGrpSbgrpBase['cd_sbgrpID']);
			$insertVnclFmlGrpSbgrp->__set('codFamilia', $fmlSubstituta);
			$insertVnclFmlGrpSbgrp->insertVinculo();
		} 

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

		// Verificar se há Item e Subitem Pendente para ser entregue
		$qtdItemSubitemNaoKH = Container::getModel('TbItemNecesFml');
		$qtdItemSubitemNaoKH->__set('cd_fml', $_POST['cd_fml']);
		$qtdItemSubitemNaoKHBase = $qtdItemSubitemNaoKH->getQtdItemSubitemNecesNaoKH();

		// Cancela os itens solicitados que não sejam Kit Habitual ainda não atendidos
		if ($qtdItemSubitemNaoKHBase['qtde'] > 0) {
			$cancelaItemSubitemNeces = Container::getModel('TbItemNecesFml');
			$cancelaItemSubitemNeces->__set('cd_fml', $_POST['cd_fml']);
			$cancelaItemSubitemNeces->cancelaItemSubitemNecesNaoKH();
		}

		$this->view->erroValidacao = 3;
		$this->view->trataMsgQdoNaoHaMaisFmlParaConcluirRelatorio = 1;
		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		session_write_close();
		$this->fAIncluirRD();				

	}	// Fim da function fAConcluirRDBaseFormaliza

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
			'origem' => $_POST['origem'],
			'seql_acomp' => $_POST['seql_acomp']
		);

		$this->render('fAAlterarRTEducacaoMenu');

	}	// Fim da function fAAlterarRTEducacaoMenu		

// ====================================================== //	

	public function fAAlterarRTEducacaoBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		// Busca o sequencial atual, data acomp ($_POST) formatada e nome dos segmentos
		$this->fAAlterarRTTratamento();

		// TU 
		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('cd_freq_crianca_adoles_escola', $_POST['cd_freq_crianca_adoles_escola']);
			$insereTbSegmtoTriagemFml->__set('dsc_mtvo_freq_escolar', $_POST['dsc_mtvo_freq_escolar']);
			$insereTbSegmtoTriagemFml->__set('dsc_desemp_estudo', $_POST['dsc_desemp_estudo']);
			$insereTbSegmtoTriagemFml->__set('cd_interes_motiva_voltar_estudar', $_POST['cd_interes_motiva_voltar_estudar']);
			$insereTbSegmtoTriagemFml->__set('dsc_curso_interes_fml', $_POST['dsc_curso_interes_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequencial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}
		
		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Atualiza na tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
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
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();		

		} else {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('cd_freq_crianca_adoles_escola', $_POST['cd_freq_crianca_adoles_escola']);
			$insereTbSegmtoTriagemFml->__set('dsc_mtvo_freq_escolar', $_POST['dsc_mtvo_freq_escolar']);
			$insereTbSegmtoTriagemFml->__set('dsc_desemp_estudo', $_POST['dsc_desemp_estudo']);
			$insereTbSegmtoTriagemFml->__set('cd_interes_motiva_voltar_estudar', $_POST['cd_interes_motiva_voltar_estudar']);
			$insereTbSegmtoTriagemFml->__set('dsc_curso_interes_fml', $_POST['dsc_curso_interes_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();
		}

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

		// Busca o sequencial atual, data acomp ($_POST) formatada e nome dos segmentos
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

		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);			
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

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}

		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Altera tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
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
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);			
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);		
			$atualizaTSTbAcompFml->updateTS();		

		} else {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);			
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
		}

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

		// TU		
		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
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

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}

		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Altera na tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
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
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();		

		} else {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
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
		}

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

		// TU		
		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);		
			$insereTbSegmtoTriagemFml->__set('dsc_cndc_saude_membros_fml', $_POST['dsc_cndc_saude_membros_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_carteira_vacina_crianca', $_POST['dsc_carteira_vacina_crianca']);
			$insereTbSegmtoTriagemFml->__set('dsc_doenca_cronica_fml', $_POST['dsc_doenca_cronica_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_restricao_alimentar', $_POST['dsc_restricao_alimentar']);
			$insereTbSegmtoTriagemFml->__set('dsc_higiene_pessoal', $_POST['dsc_higiene_pessoal']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;			

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}

		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Altera tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
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
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();		

		} else {
			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);		
			$insereTbSegmtoTriagemFml->__set('dsc_cndc_saude_membros_fml', $_POST['dsc_cndc_saude_membros_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_carteira_vacina_crianca', $_POST['dsc_carteira_vacina_crianca']);
			$insereTbSegmtoTriagemFml->__set('dsc_doenca_cronica_fml', $_POST['dsc_doenca_cronica_fml']);
			$insereTbSegmtoTriagemFml->__set('dsc_restricao_alimentar', $_POST['dsc_restricao_alimentar']);
			$insereTbSegmtoTriagemFml->__set('dsc_higiene_pessoal', $_POST['dsc_higiene_pessoal']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();
		}

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

		// TU		
		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {		
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
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

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}

		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Altera na tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
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
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();

		} else {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
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
		}

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

		// TU		
		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);		
			$insereTbSegmtoTriagemFml->__set('cd_tip_trab', $_POST['cd_tip_trab']);
			$insereTbSegmtoTriagemFml->__set('vlr_renda_tip_trab', $_POST['vlr_renda_tip_trab']);
			$insereTbSegmtoTriagemFml->__set('dsc_tip_beneficio', $_POST['dsc_tip_beneficio']);
			$insereTbSegmtoTriagemFml->__set('vlr_renda_tip_beneficio', $_POST['vlr_renda_tip_beneficio']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}

		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Altera na tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
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
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();		

		} else {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);		
			$insereTbSegmtoTriagemFml->__set('cd_tip_trab', $_POST['cd_tip_trab']);
			$insereTbSegmtoTriagemFml->__set('vlr_renda_tip_trab', $_POST['vlr_renda_tip_trab']);
			$insereTbSegmtoTriagemFml->__set('dsc_tip_beneficio', $_POST['dsc_tip_beneficio']);
			$insereTbSegmtoTriagemFml->__set('vlr_renda_tip_beneficio', $_POST['vlr_renda_tip_beneficio']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();
		}

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

		// TU		
		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_expect_fml_capacit_profi', $_POST['dsc_expect_fml_capacit_profi']);
			$insereTbSegmtoTriagemFml->__set('dsc_curso_intere_profi_tecnico', $_POST['dsc_curso_intere_profi_tecnico']);
			$insereTbSegmtoTriagemFml->__set('dsc_projeto_gera_renda_extra', $_POST['dsc_projeto_gera_renda_extra']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}

		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Altera na tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
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
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();

		} else {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_expect_fml_capacit_profi', $_POST['dsc_expect_fml_capacit_profi']);
			$insereTbSegmtoTriagemFml->__set('dsc_curso_intere_profi_tecnico', $_POST['dsc_curso_intere_profi_tecnico']);
			$insereTbSegmtoTriagemFml->__set('dsc_projeto_gera_renda_extra', $_POST['dsc_projeto_gera_renda_extra']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();
		}

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

		// TU		
		// Não há acompanhamento cadastrado
		if ($this->view->retorno['seqlAcomp'] == 0) {
			$this->insereAcompanhamentoRT();

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->prox_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_aspecto_intimo', $_POST['dsc_aspecto_intimo']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();

			$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $this->view->retorno['seqlAcomp']);
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$this->insereAcompanhamentoRT();

				$this->view->novo_seql_acomp = $this->view->prox_seql_acomp;

			} else {
				$this->view->novo_seql_acomp = $this->view->retorno['seqlAcomp'];
			}
		}

		// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
		$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
		$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
		$verificaTriagem->__set('seqlAcomp', $this->view->novo_seql_acomp);
		$verificaTriagem->__set('codSegmtoTriagem', $_POST['cd_segmto']);
		$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

		if ($verificaTriagemBase['qtde'] > 0) {
			// Altera na tabela tb_segmto_triagem_fml
			$alteraTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$alteraTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$alteraTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$alteraTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$alteraTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$alteraTbSegmtoTriagemFml->__set('dsc_aspecto_intimo', $_POST['dsc_aspecto_intimo']);
			$alteraTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$alteraTbSegmtoTriagemFml->updateSegmtoTriagemFml();

			// Atualiza timestamp de tb_acomp_fml
			$atualizaTSTbAcompFml = Container::getModel('TbAcompFml');
			$atualizaTSTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
			$atualizaTSTbAcompFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$atualizaTSTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
			$atualizaTSTbAcompFml->updateTS();

		} else {

			// Insere na tabela tb_segmto_triagem_fml
			$insereTbSegmtoTriagemFml = Container::getModel('TbSegmtoTriagemFml');
			$insereTbSegmtoTriagemFml->__set('cd_fml', $_POST['cd_fml']);
			$insereTbSegmtoTriagemFml->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereTbSegmtoTriagemFml->__set('cd_segmto_triagem', $_POST['cd_segmto']);
			$insereTbSegmtoTriagemFml->__set('dt_reg_seg_triagem', $this->view->retorno['dataAcomp_formatada']);
			$insereTbSegmtoTriagemFml->__set('dsc_aspecto_intimo', $_POST['dsc_aspecto_intimo']);
			$insereTbSegmtoTriagemFml->__set('dsc_prgm_trab', $_POST['dsc_prgm_trab']);
			$insereTbSegmtoTriagemFml->insertSegmtoTriagemFml();
		}

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

		$voluntarios = explode(',', $_POST['cb_voluntario_escolhido']);

		if ($_POST['situacao'] == 'inclui') {
			for ($i = 0; $i < count($voluntarios); $i++) {
				$voluntario_e = explode(';', $voluntarios[$i]);
				$voluntario_g = $voluntario_e[0];

				// Inserir Voluntário em tb_vncl_vlnt_acomp_fml
				$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
				$insereVoluntarioTVVAF->__set('cd_vlnt', $voluntario_g);
				$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
				$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
				$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 3);
				$insereVoluntarioTVVAF->insertTVVAF();
			}

			$this->view->erroValidacao = 1;
		
		} else {

			for ($i = 0; $i < count($voluntarios); $i++) {			
				$voluntario_e = explode(';', $voluntarios[$i]);
				$voluntario_g = $voluntario_e[0];

				// Excluir Voluntário em tb_vncl_vlnt_acomp_fml
				$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
				$alteraVoluntarioTVVAF->__set('cd_vlnt', $voluntario_g);
				$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
				$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
				$alteraVoluntarioTVVAF->deleteTVVAF();
			}			

				$this->view->erroValidacao = 3;
		}

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vnlc_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cd_fml']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $_POST['seql_acomp']);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}
	
		$this->fAAlterarRTVoluntarioPOST();

	} // Fim da function fAAlterarRTVoluntarioBase

// ====================================================== //	

	public function fAAlterarRTVoluntarioPOST() {

		// TU
		if ($_POST['seql_acomp'] == 0) {
			$this->view->erroValidacao = 2;

			$this->view->dadosVoluntarios = array ();

			// Para compor os dados do Grupo e Subgrupo acima da tabela
			$this->view->codGrupo = $_POST['cd_grp'];
			$this->view->nomeGrupo = $_POST['nm_grp'];
			$this->view->codSubgrupo = $_POST['cd_sbgrp'];
			$this->view->nomeSubgrupo = $_POST['nm_sbgrp'];
			$this->view->codFamilia = $_POST['cd_fml'];
			$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
			$this->view->seqlAcomp = 0;
			$this->view->origem = $_POST['origem'];

		} else {

			// Obtem Sequencial de Acompanhamento Atual
			$seqlAcomp = Container::getModel('TbAcompFml');
			$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
			$seqlAcomp->getSequencial();			

			// Buscar Voluntários em tb_vncl_vlnt_grp com cd_atu_vlnt = 5 (Somente Voluntários)
			$obtemVoluntariosGSBase = Container::getModel('TbVnclVlntGrp');
			$obtemVoluntariosGSBase->__set('cd_grp', $_POST['cd_grp']);
			$obtemVoluntariosGSBase->__set('cd_sbgrp', $_POST['cd_sbgrp']);
			$obtemVoluntariosGSBase->__set('cd_fml', $_POST['cd_fml']);
			$obtemVoluntariosGSBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$obtemVoluntariosGSBase->__set('cd_atu_vlnt', 5);
			$obtemVoluntariosGS = $obtemVoluntariosGSBase->getVoluntariosGrupoSubgrupoAcomp();

			$this->view->dadosVoluntarios = array ();

			// Para dar mensagem de que não há voluntários para tratamento
			if (count($obtemVoluntariosGS) == 0) {
				$this->view->erroValidacao = 2;
			}

			foreach ($obtemVoluntariosGS as $index => $arr) {
				if ($arr['simNao'] == 's') {
					$vinculado = 'Sim';
				} else {
					$vinculado = 'Não';
				}

				if ($arr['cd_vlnt'] != $_SESSION['id']) {
					array_push($this->view->dadosVoluntarios, array (
								'cd_vlnt' => $arr['cd_vlnt'],
								'nm_vlnt' => $arr['nm_vlnt'],
								'vinculado' => $vinculado,
								'cd_vlnt_sn' => $arr['cd_vlnt'].';'.$arr['simNao']
					));
				}
			}

			// Para compor os dados do Grupo e Subgrupo acima da tabela
			$this->view->codGrupo = $_POST['cd_grp'];
			$this->view->nomeGrupo = $_POST['nm_grp'];
			$this->view->codSubgrupo = $_POST['cd_sbgrp'];
			$this->view->nomeSubgrupo = $_POST['nm_sbgrp'];
			$this->view->codFamilia = $_POST['cd_fml'];
			$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];
			$this->view->seqlAcomp = $seqlAcomp->__get('seql_max');
			$this->view->origem = $_POST['origem'];
		}
		
		$this->render('fAAlterarRTVoluntario');

	} // Fim da function fAAlterarRTVoluntarioPOST

// ====================================================== //	

	public function fAObtemDadosTriagem() {

		// Obtem Sequencial de Acompanhamento Atual
		$seqlAcomp = Container::getModel('TbAcompFml');
		$seqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$seqlAcomp->getSequencial();			

		// TU
		// Não há acompanhamento cadastrado ainda
		if (empty($seqlAcomp->__get('seql_max'))) {
			$data_hoje 		= 	new \DateTime();
			$data_hoje 		= 	$data_hoje->format("Y-m-d");
			
			switch ($this->view->segmento)
		  	{
		       case 1:  // Educação
		         {
		         	$this->obtemDadosTriagemBase = array (
										'dt_reg_seg_triagem' => $data_hoje,
										'cd_freq_crianca_adoles_escola' => 1,
										'dsc_mtvo_freq_escolar' => '',
										'dsc_desemp_estudo' => '',
										'cd_interes_motiva_voltar_estudar' => 1,
										'dsc_curso_interes_fml' => '',
										'dsc_prgm_trab' => ''
							);
	            break;
	          }

		       case 2:  // Religiosidade
		         {
		         	$this->obtemDadosTriagemBase = array (		         	
										'dt_reg_seg_triagem' => $data_hoje,
										'dsc_religiao_fml' => '',
										'dsc_institu_religiosa_freqtd' => '',
										'dsc_freq_institu_religiosa' => '',
										'habito_prece_oracao' => 'S',
										'evangelho_lar' => 'S',
										'conhece_espiritismo' => 'S',
										'vont_aprox_espiritismo' => 'S',
										'dsc_prgm_trab' => ''
							);
	            break;
	          }

		       case 3:  // Moradia
		         {
		         	$this->obtemDadosTriagemBase = array (		         	
										'dt_reg_seg_triagem' => $data_hoje,							
										'dsc_casa' => '',
										'exist_anim_inset_insal_perig' => 'N',
										'dsc_anim_inset_insal_perig' => '',
										'exist_anim_estima' => 'N',
										'dsc_anim_estima' => '',
										'vacina_anti_rabica_anim_estima' => 'N',
										'cd_agua_moradia' => 0,
										'cd_esgoto_moradia' => 0,
										'dsc_prgm_trab' => ''
							);
	            break;
	          }	            

		       case 4:  // Saude
		         {
		         	$this->obtemDadosTriagemBase = array (		         	
										'dt_reg_seg_triagem' => $data_hoje,							
										'dsc_cndc_saude_membros_fml' => '',
										'dsc_carteira_vacina_crianca' => '',
										'dsc_doenca_cronica_fml' => '',
										'dsc_restricao_alimentar' => '',
										'dsc_higiene_pessoal' => '',
										'dsc_prgm_trab' => ''
							);
	            break;
	          }

		       case 5:  // Despesa
		         {
		         	$this->obtemDadosTriagemBase = array (		         	
										'dt_reg_seg_triagem' => $data_hoje,									         	
										'cd_tip_moradia' => 1,
										'dsc_dono_cedente_moradia' => '',
										'vlr_desp_agua' => 0,
										'vlr_desp_energia' => 0,
										'vlr_desp_iptu' => 0,
										'vlr_desp_gas' => 0,
										'vlr_desp_condominio' => 0,
										'vlr_desp_outra_manut' => 0,
										'dsc_desp_outra_manut' => '',
										'dsc_desp_saude_medicamento' => '',
										'dsc_desp_educ_creche_cuidadora' => '',
										'dsc_desp_transporte' => '',
										'dsc_desp_alimenta_especial' => '',
										'dsc_outra_desp_geral' => '',
										'dsc_prgm_trab' => ''
							);	             
	            break;
	          }

		       case 6:  // Renda
		         {
		         	$this->obtemDadosTriagemBase = array (		         	
										'dt_reg_seg_triagem' => $data_hoje,									         	
										'cd_tip_trab' => 1,
										'vlr_renda_tip_trab' => 0,
										'dsc_tip_beneficio' => '',
										'vlr_renda_tip_beneficio' => 0,
										'dsc_prgm_trab' => ''
							);	
	            break;
	          }

		       case 7:  // Capacitação Profissional
		         {
		         	$this->obtemDadosTriagemBase = array (		         	
										'dt_reg_seg_triagem' => $data_hoje,									         		         	
										'dsc_expect_fml_capacit_profi' => '',
										'dsc_curso_intere_profi_tecnico' => '',
										'dsc_projeto_gera_renda_extra' => '',
										'dsc_prgm_trab' => ''
							);		             
	            break;
	          }

		       case 8:  // Aspectos Intimos
		         {
		         	$this->obtemDadosTriagemBase = array (		         	
										'dt_reg_seg_triagem' => $data_hoje,									         		         		         	
										'dsc_aspecto_intimo' => '',
										'dsc_prgm_trab' => ''
							);		             	             
	            break;
	          }
		  	}

		} else {

			// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
			if ($_POST['origem'] == 'conclusaoRelatorio') {
				$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
			} else {					// == 'conclusaoRevisao'
				$cd_est_acomp1      = 2;			// Pendente de Término de revisão
			}

			// Obtem Atividade e Estado do Acompanhamento do Sequancial de Acompanhamento Atual
			$dadosAcompBase = Container::getModel('TbAcompFml');
			$dadosAcompBase->__set('cd_fml', $_POST['cd_fml']);
			$dadosAcompBase->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
			$dadosAcomp =  $dadosAcompBase->getAtvdAcomp();			

			// Já houve triagem anterior e é a que está na base atualmente. Tem que se criar uma nova
			if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
				$data_hoje 		= 	new \DateTime();
				$data_hoje 		= 	$data_hoje->format("Y-m-d");
				
				switch ($this->view->segmento)
			  	{
			       case 1:  // Educação
			         {
			         	$this->obtemDadosTriagemBase = array (
											'dt_reg_seg_triagem' => $data_hoje,
											'cd_freq_crianca_adoles_escola' => 1,
											'dsc_mtvo_freq_escolar' => '',
											'dsc_desemp_estudo' => '',
											'cd_interes_motiva_voltar_estudar' => 1,
											'dsc_curso_interes_fml' => '',
											'dsc_prgm_trab' => ''
								);
		            break;
		          }

			       case 2:  // Religiosidade
			         {
			         	$this->obtemDadosTriagemBase = array (		         	
											'dt_reg_seg_triagem' => $data_hoje,
											'dsc_religiao_fml' => '',
											'dsc_institu_religiosa_freqtd' => '',
											'dsc_freq_institu_religiosa' => '',
											'habito_prece_oracao' => 'S',
											'evangelho_lar' => 'S',
											'conhece_espiritismo' => 'S',
											'vont_aprox_espiritismo' => 'S',
											'dsc_prgm_trab' => ''
								);
		            break;
		          }

			       case 3:  // Moradia
			         {
			         	$this->obtemDadosTriagemBase = array (		         	
											'dt_reg_seg_triagem' => $data_hoje,							
											'dsc_casa' => '',
											'exist_anim_inset_insal_perig' => 'N',
											'dsc_anim_inset_insal_perig' => '',
											'exist_anim_estima' => 'N',
											'dsc_anim_estima' => '',
											'vacina_anti_rabica_anim_estima' => 'N',
											'cd_agua_moradia' => 0,
											'cd_esgoto_moradia' => 0,
											'dsc_prgm_trab' => ''
								);
		            break;
		          }	            

			       case 4:  // Saude
			         {
			         	$this->obtemDadosTriagemBase = array (		         	
											'dt_reg_seg_triagem' => $data_hoje,							
											'dsc_cndc_saude_membros_fml' => '',
											'dsc_carteira_vacina_crianca' => '',
											'dsc_doenca_cronica_fml' => '',
											'dsc_restricao_alimentar' => '',
											'dsc_higiene_pessoal' => '',
											'dsc_prgm_trab' => ''
								);
		            break;
		          }

			       case 5:  // Despesa
			         {
			         	$this->obtemDadosTriagemBase = array (		         	
											'dt_reg_seg_triagem' => $data_hoje,									         	
											'cd_tip_moradia' => 1,
											'dsc_dono_cedente_moradia' => '',
											'vlr_desp_agua' => 0,
											'vlr_desp_energia' => 0,
											'vlr_desp_iptu' => 0,
											'vlr_desp_gas' => 0,
											'vlr_desp_condominio' => 0,
											'vlr_desp_outra_manut' => 0,
											'dsc_desp_outra_manut' => '',
											'dsc_desp_saude_medicamento' => '',
											'dsc_desp_educ_creche_cuidadora' => '',
											'dsc_desp_transporte' => '',
											'dsc_desp_alimenta_especial' => '',
											'dsc_outra_desp_geral' => '',
											'dsc_prgm_trab' => ''
								);	             
		            break;
		          }

			       case 6:  // Renda
			         {
			         	$this->obtemDadosTriagemBase = array (		         	
											'dt_reg_seg_triagem' => $data_hoje,									         	
											'cd_tip_trab' => 1,
											'vlr_renda_tip_trab' => 0,
											'dsc_tip_beneficio' => '',
											'vlr_renda_tip_beneficio' => 0,
											'dsc_prgm_trab' => ''
								);	
		            break;
		          }

			       case 7:  // Capacitação Profissional
			         {
			         	$this->obtemDadosTriagemBase = array (		         	
											'dt_reg_seg_triagem' => $data_hoje,									         		         	
											'dsc_expect_fml_capacit_profi' => '',
											'dsc_curso_intere_profi_tecnico' => '',
											'dsc_projeto_gera_renda_extra' => '',
											'dsc_prgm_trab' => ''
								);		             
		            break;
		          }

			       case 8:  // Aspectos Intimos
			         {
			         	$this->obtemDadosTriagemBase = array (		         	
											'dt_reg_seg_triagem' => $data_hoje,									         		         		         	
											'dsc_aspecto_intimo' => '',
											'dsc_prgm_trab' => ''
								);		             	             
		            break;
		          }
			  	}

			} else {

				// Verificar se há dados de Triagem, pois pode ser que foi gravado somente tb_acomp_fml, por enquanto
				$verificaTriagem = Container::getModel('TbSegmtoTriagemFml');
				$verificaTriagem->__set('codFamilia', $_POST['cd_fml']);
				$verificaTriagem->__set('seqlAcomp', $seqlAcomp->__get('seql_max'));
				$verificaTriagem->__set('codSegmtoTriagem', $this->view->segmento);
				$verificaTriagemBase = $verificaTriagem->getQtdSegmentoTriagem();

				if ($verificaTriagemBase['qtde'] > 0) {
					// Obtem dados da Triagem
					$obtemDadosTriagem = Container::getModel('TbSegmtoTriagemFml');
					$obtemDadosTriagem->__set('cd_fml', $_POST['cd_fml']);
					$obtemDadosTriagem->__set('seql_acomp', $seqlAcomp->__get('seql_max'));
					$obtemDadosTriagem->__set('cd_segmto_triagem', $this->view->segmento);
					$this->obtemDadosTriagemBase = $obtemDadosTriagem->getDadosSegmentoTriagem();

				} else {

					$data_hoje 		= 	new \DateTime();
					$data_hoje 		= 	$data_hoje->format("Y-m-d");
					
					switch ($this->view->segmento)
				  {
				       case 1:  // Educação
				         {
				         	$this->obtemDadosTriagemBase = array (
												'dt_reg_seg_triagem' => $data_hoje,
												'cd_freq_crianca_adoles_escola' => 1,
												'dsc_mtvo_freq_escolar' => '',
												'dsc_desemp_estudo' => '',
												'cd_interes_motiva_voltar_estudar' => 1,
												'dsc_curso_interes_fml' => '',
												'dsc_prgm_trab' => ''
									);
			            break;
			          }

				       case 2:  // Religiosidade
				         {
				         	$this->obtemDadosTriagemBase = array (		         	
												'dt_reg_seg_triagem' => $data_hoje,
												'dsc_religiao_fml' => '',
												'dsc_institu_religiosa_freqtd' => '',
												'dsc_freq_institu_religiosa' => '',
												'habito_prece_oracao' => 'S',
												'evangelho_lar' => 'S',
												'conhece_espiritismo' => 'S',
												'vont_aprox_espiritismo' => 'S',
												'dsc_prgm_trab' => ''
									);
			            break;
			          }

				       case 3:  // Moradia
				         {
				         	$this->obtemDadosTriagemBase = array (		         	
												'dt_reg_seg_triagem' => $data_hoje,							
												'dsc_casa' => '',
												'exist_anim_inset_insal_perig' => 'N',
												'dsc_anim_inset_insal_perig' => '',
												'exist_anim_estima' => 'N',
												'dsc_anim_estima' => '',
												'vacina_anti_rabica_anim_estima' => 'N',
												'cd_agua_moradia' => 0,
												'cd_esgoto_moradia' => 0,
												'dsc_prgm_trab' => ''
									);
			            break;
			          }	            

				       case 4:  // Saude
				         {
				         	$this->obtemDadosTriagemBase = array (		         	
												'dt_reg_seg_triagem' => $data_hoje,							
												'dsc_cndc_saude_membros_fml' => '',
												'dsc_carteira_vacina_crianca' => '',
												'dsc_doenca_cronica_fml' => '',
												'dsc_restricao_alimentar' => '',
												'dsc_higiene_pessoal' => '',
												'dsc_prgm_trab' => ''
									);
			            break;
			          }

				       case 5:  // Despesa
				         {
				         	$this->obtemDadosTriagemBase = array (		         	
												'dt_reg_seg_triagem' => $data_hoje,									         	
												'cd_tip_moradia' => 1,
												'dsc_dono_cedente_moradia' => '',
												'vlr_desp_agua' => 0,
												'vlr_desp_energia' => 0,
												'vlr_desp_iptu' => 0,
												'vlr_desp_gas' => 0,
												'vlr_desp_condominio' => 0,
												'vlr_desp_outra_manut' => 0,
												'dsc_desp_outra_manut' => '',
												'dsc_desp_saude_medicamento' => '',
												'dsc_desp_educ_creche_cuidadora' => '',
												'dsc_desp_transporte' => '',
												'dsc_desp_alimenta_especial' => '',
												'dsc_outra_desp_geral' => '',
												'dsc_prgm_trab' => ''
									);	             
			            break;
			          }

				       case 6:  // Renda
				         {
				         	$this->obtemDadosTriagemBase = array (		         	
												'dt_reg_seg_triagem' => $data_hoje,									         	
												'cd_tip_trab' => 1,
												'vlr_renda_tip_trab' => 0,
												'dsc_tip_beneficio' => '',
												'vlr_renda_tip_beneficio' => 0,
												'dsc_prgm_trab' => ''
									);	
			            break;
			          }

				       case 7:  // Capacitação Profissional
				         {
				         	$this->obtemDadosTriagemBase = array (		         	
												'dt_reg_seg_triagem' => $data_hoje,									         		         	
												'dsc_expect_fml_capacit_profi' => '',
												'dsc_curso_intere_profi_tecnico' => '',
												'dsc_projeto_gera_renda_extra' => '',
												'dsc_prgm_trab' => ''
									);		             
			            break;
			          }

				       case 8:  // Aspectos Intimos
				         {
				         	$this->obtemDadosTriagemBase = array (		         	
												'dt_reg_seg_triagem' => $data_hoje,									         		         		         	
												'dsc_aspecto_intimo' => '',
												'dsc_prgm_trab' => ''
									);		             	             
			            break;
			          }
					} // switch ($this->view->segmento)
				}	// if ($verificaTriagemBase['qtde'] > 0) {
			}	// if ($dadosAcomp['cd_est_acomp'] != $cd_est_acomp1) {
		}	// if (empty($seqlAcomp->__get('seql_max'))) {

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

		// TU
		// Para saber se já há acompanhamento cadastrado
		if (empty($seqlAcomp->__get('seql_max'))) {
			$seql_acomp = 0;

		} else {
			$seql_acomp = $seqlAcomp->__get('seql_max');
		}

		$this->view->retorno = array (
				// TU 'seqlAcomp' => $seqlAcomp->__get('seql_max'),
				'seqlAcomp' => $seql_acomp,
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

		$this->view->erroValidacao = 0;

		$this->view->segmento1 = 0;
		$this->view->segmento2 = 0;
		$this->view->segmento3 = 0;
		$this->view->segmento4 = 0;
		$this->view->segmento5 = 0;
		$this->view->segmento6 = 0;
		$this->view->segmento7 = 0;
		$this->view->segmento8 = 0;
		$this->view->vnclVlntAcomp = 0;

		$cd_atvd_acomp = 1;  											// Triagem
		$atuacao_voluntario_acompanhamento = 3;		// Visitador		

		for ($i = 1; $i <= 8; $i++) {
			// Verificar se a Triagem atual está em Andamento //
			$verificaTriagem0 = Container::getModel('TbSegmtoTriagemFml');
			$verificaTriagem0->__set('codFamilia', $_POST['cb_familia_escolhida']);
			$verificaTriagem0->__set('seqlAcomp', $this->view->novo_seql_acomp);
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

		// Verificar se há vínculo de Voluntário cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $this->view->novo_seql_acomp);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
		}

		// Pesquisar se voluntário Cadastrando está cadastrado em tb_vncl_vncl_acomp_fml
		$pesquisaVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$pesquisaVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$pesquisaVoluntarioTVVAF->__set('cd_fml',  $_POST['cb_familia_escolhida']);
		$pesquisaVoluntarioTVVAF->__set('seql_acomp', $this->view->novo_seql_acomp);
		$pesquisaVoluntarioTVVAFBase = $pesquisaVoluntarioTVVAF->getQtdeVoluntarioAcompEspecifico();

		// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
		if ($_POST['origem'] == 'conclusaoRelatorio') {
			$cd_atua_vlnt_acomp      = 2;			// Visitador e Relator Relatório
		} else {					// == 'conclusaoRevisao'
			$cd_atua_vlnt_acomp      = 1;			// Revisor
		}

		if ($pesquisaVoluntarioTVVAFBase['qtde'] == 0) {
			// Inserir Voluntário como Voluntário Relator na tabela tb_vncl_vlnt_acomp_fml 
			$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$insereVoluntarioTVVAF->__set('seql_acomp', $this->view->novo_seql_acomp);
			$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', $cd_atua_vlnt_acomp);
			$insereVoluntarioTVVAF->insertTVVAF();
		} else {
			// Update ts_atua_vlnt_acomp e cd_atua_vlnt_acomp com 2, da tabela tb_vncl_vlnt_acomp_fml 
			$alteraVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
			$alteraVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
			$alteraVoluntarioTVVAF->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$alteraVoluntarioTVVAF->__set('seql_acomp', $this->view->novo_seql_acomp);
			$alteraVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', $cd_atua_vlnt_acomp);			
			$alteraVoluntarioTVVAF->updateVoluntarioAcompEspecifico();
		}

		// Esta Section é chamada tanto na Conclusão do Relatório, quanto na revisão
		if ($_POST['origem'] == 'conclusaoRelatorio') {
			$cd_est_acomp1      = 1;			// Pendente de Término de registro de Triagem/Visita
		} else {					// == 'conclusaoRevisao'
			$cd_est_acomp1      = 2;			// Pendente de Término de revisão
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
		$dataAcompBase->__set('cd_atvd_acomp', $cd_atvd_acomp);
		$dataAcompBase->__set('cd_est_ini', $cd_est_acomp1);
		$dataAcompBase->__set('cd_est_fim', $cd_est_acomp1);
		$dataAcomp = $dataAcompBase->getDadosAcompTriagemVisita();

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

		$this->view->erroValidacao = 3;

		if ($origem == 'conclusaoRelatorio') {
			$this->render('fAConcluirRTMenu');		
		} else if ($origem == 'conclusaoRevisao') {
			$this->render('fARevisarRTMenu');					
		}
	
	}	// Fim da function fAAlterarRTRetorno		

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

			$this->atualizaQtdRankingFamilias();

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

		$this->view->segmento1 = 0;
		$this->view->segmento2 = 0;
		$this->view->segmento3 = 0;
		$this->view->segmento4 = 0;
		$this->view->segmento5 = 0;
		$this->view->segmento6 = 0;
		$this->view->segmento7 = 0;
		$this->view->segmento8 = 0;
		$this->view->vnclVlntAcomp = 0;
		$atuacao_voluntario_acompanhamento = 3;				// Visitador
	
		for ($i = 1; $i <= 8; $i++) {
			// Verificar se a Triagem está em Andamento //
			$verificaTriagem0 = Container::getModel('TbSegmtoTriagemFml');
			$verificaTriagem0->__set('codFamilia', $_POST['cb_familia_escolhida']);
			$verificaTriagem0->__set('seqlAcomp', $_POST['seql_acomp']);
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
		
		// Verificar se há vínculo cadastrado
		$verificaVinculo = Container::getModel('TbVnclVlntAcompFml');
		$verificaVinculo->__set('cd_fml', $_POST['cb_familia_escolhida']);
		$verificaVinculo->__set('seql_acomp', $_POST['seql_acomp']);
		$verificaVinculo->__set('cd_atua_vlnt_acomp', $atuacao_voluntario_acompanhamento);
		$verificaVinculoBase = $verificaVinculo->getQtdVinculoTriagemVisita();

		if ($verificaVinculoBase['qtde'] > 0) {
			$this->view->vnclVlntAcomp = 1;
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

			$this->atualizaQtdRankingFamilias();

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
	
	public function fAConsultarRankingFml() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
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

			$this->atualizaQtdRankingFamilias();

			$this->render('familiaAcompanhamento');				

			exit;
		} 

		// Buscar Dados das Famílias ainda sem atendimento e também sem grupo/subgrupo escolhidos
		$dadosConsultaBase = Container::getModel('TbFml');
		$this->view->dadosConsulta = $dadosConsultaBase->getDadosConsulta1RankingFml();

		if  (count($this->view->dadosConsulta) > 0) {
			$this->geraRankingFmlParaAtendimento();

			$this->render('fAConsultarRankingFml');	
	
		} else {

			$this->view->erroValidacao = 3;

			$this->atualizaqtdPendenciasRelatorios();

			$this->atualizaQtdRankingFamilias();

			$this->render('familiaAcompanhamento');
		}  

	}	// Fim da function fAConsultarRankingFml

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

// =======================================================================//
//                    Início das funções compartilhadas
// =======================================================================//
	
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
		
	}	// Fim da atualizaqtdPendenciasRelatorios

	// ====================================================== //	

	public function atualizaQtdRankingFamilias() {
		// Busca famílias rankeadas para atendimento
		$qtdRankingFmlBase = Container::getModel('TbFml');
		$qtdRankingFml = $qtdRankingFmlBase->getQtdRankingFml();

		$this->view->qtdRankingFamilias = $qtdRankingFml['qtde'];
		
	}	// Fim da atualizaQtdRankingFamilias

	// ====================================================== //	

	public function insereAcompanhamentoRT() {
		// Obtem Próximo Sequencial de Acompanhamento
		$proxSeqlAcomp = Container::getModel('TbAcompFml');
		$proxSeqlAcomp->__set('cd_fml', $_POST['cd_fml']);
		$proxSeqlAcomp->getProximoSequencial();			

		// Insere na tabela tb_acomp_fml
		$insereTbAcompFml = Container::getModel('TbAcompFml');
		$insereTbAcompFml->__set('cd_fml', $_POST['cd_fml']);
		$insereTbAcompFml->__set('seql_acomp', $proxSeqlAcomp->__get('seql_max'));
		$insereTbAcompFml->__set('cd_atvd_acomp', 1);
		$insereTbAcompFml->__set('cd_avalia_triagem', 1);
		$insereTbAcompFml->__set('dt_acomp', $this->view->retorno['dataAcomp_formatada']);
		$insereTbAcompFml->insertAcompanhamentoFamilia();	

		// Inserir Voluntário na tabela tb_vncl_vlnt_acomp_fml
		$insereVoluntarioTVVAF = Container::getModel('TbVnclVlntAcompFml');
		$insereVoluntarioTVVAF->__set('cd_vlnt', $_SESSION['id']);
		$insereVoluntarioTVVAF->__set('cd_fml', $_POST['cd_fml']);
		$insereVoluntarioTVVAF->__set('seql_acomp', $proxSeqlAcomp->__get('seql_max'));
		$insereVoluntarioTVVAF->__set('cd_atua_vlnt_acomp', 2);
		$insereVoluntarioTVVAF->insertTVVAF();

		$this->view->prox_seql_acomp = $proxSeqlAcomp->__get('seql_max');

	}	// Fim da insereAcompanhamentoRT
	
	// ====================================================== //	

	public function geraRankingFmlParaAtendimento() {

		$this->view->dadosRanking = array ();

		foreach ($this->view->dadosConsulta as $index => $arr) {
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
					'cd_fml' => $arr['cd_fml'].' - '.$arr['nm_grp_fmlr'],	
					'reg_adm' => $arr['cd_reg_adm'].' - '.$arr['nm_reg_adm'],	
					'nrAssistencia' => $atributo_faseI,
					'tempoRegistro' => $pontos_faseII,
					'rendaPerCapita' => $pontos_renda,
					'reatendimento' => $pontos_reatendimento,
					'totalPontos' => $total_pontos,
					'posicaoRanking' => 0
			));

		}	// Fim foreach()

		// Ordenar tabela 
		$clause =  'totalPontos DESC, dt_cadastro_fml ASC';
		Funcoes::orderByArray( $this->view->dadosRanking, $clause );

		$cnt = 0;
		$cont = 1;

		// Trata posição da família no ranking
		foreach ($this->view->dadosRanking as $index => $row) {
			// Atualiza pontuação e a posição da família no ranking do subgrupo
			$atualizaPtsPosicao = Container::getModel('TbFml');
			$atualizaPtsPosicao->__set('cd_fml', $row['cd_fml']);
			$atualizaPtsPosicao->__set('ptc_atendto_fml', $row['totalPontos']);
			$atualizaPtsPosicao->__set('pos_ranking_atendto_fml', $cont);
			$atualizaPtsPosicao->updatePontosPosicaoRanking();

			$this->view->dadosRanking[$cnt]['posicaoRanking'] = $cont;

			$cnt = $cnt + 1;
			$cont = $cont + 1;
		}
	}	// Fim da geraRankingFmlParaAtendimento

}	//	Fim da classe

?>
				