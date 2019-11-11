<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class RecFinanFamiliaController extends Action {

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

				$this->retornoValidaAcesso = 1;
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

						$this->retornoValidaAcesso = 2;
					}
				}
			}
		}

	}	// Fim da function validaAcesso

// ================================================== //
//      Início de Recuros Financeiros das Famílias
// ================================================== //

	public function familiaFinanceiro() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		$this->render('familiaFinanceiro');
	}

// ====================================================== //	
		
	public function recFinanFamiliaPreSolicitar() {
		
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

			$this->render('familiaFinanceiro');				

		} else {

			$this->view->erroValidacao = 0;

			$this->render('recFinanFamiliaPreSolicitar');
		}

	}	// Fim da function recFinanFamiliaPreSolicitar

// ====================================================== //	
	
	public function recFinanFamiliaSolicitar() {

		$this->validaAutenticacao();		

		$this->view->codVoluntario = $_SESSION['id'];					

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		// Valida se Grupo e subgrupo foram escolhidos
		if ($_POST['cb_grupo_escolhido'] == "Escolha Grupo" || 
			$_POST['cb_subgrupo_escolhido'] == "Escolha Subgrupo") {
			$this->view->erroValidacao = 2;

			$this->render('recFinanFamiliaPreSolicitar');

		} else {

			// Para inclusão de Solicitação de Recurso Financeiro, somente se estiver em grupo/subgrupo e
			// cd_atu_vlnt = 2-Coordenador Financeiro na tabela tb_vncl_vlnt_grp
			$this->nivel_atuacao_requerido = 2;
			
			$this->validaAcesso();

			// Não está na tabela de vinculo de grupo e subgrupo
			if ($this->retornoValidaAcesso == 1) {
				$this->render('recFinanFamiliaPreSolicitar');				

			// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
			} else if ($this->retornoValidaAcesso == 2) {
				$this->render('recFinanFamiliaPreSolicitar');				

			} else {

				// Buscar Nome de Grupo e Subgrupo
				$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
				$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
				$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
				$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
				
				$nomeGrupo = $dadosGS['nome_grupo'];
				$nomeSubgrupo = $dadosGS['nome_subgrupo'];

				// Buscar Pedidos Aguardando autorização => cd_est_pedido = 1
				$pedidoRecurFinanBase = Container::getModel('TbPedidoRecurFinan');
				$pedidoRecurFinanBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$pedidoRecurFinanBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
				$pedidoRecurFinanBase->__set('cd_est_pedido1',  1);
				$pedidoRecurFinanBase->__set('cd_est_pedido2',  1);
				$pedidoRecurFinan = $pedidoRecurFinanBase->getDadosPedidoRecurFinanAll();

				$this->view->pedidoRecFinan = array ();

				if (count($pedidoRecurFinan) > 0) {
					foreach ($pedidoRecurFinan as $index => $arr) {
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
								'cd_grp' => $_POST['cb_grupo_escolhido'], 
								'nm_grp' => $nomeGrupo, 
								'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
								'nm_sbgrp' => $nomeSubgrupo, 
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
								'nm_tip_enquadra_format' => $arr['cd_tip_enquadra_pedido_format'],
								'cd_est_pedido' => $arr['cd_est_pedido'],
								'nm_est_pedido_format' => $arr['cd_est_pedido_format'],
								'cd_situ_envio_ressar_pedido' => $arr['cd_situ_envio_ressar_pedido'],
								'nm_situ_envio_ressar_pedido_format' => $arr['cd_situ_envio_ressar_pedido_format'],
								'dir_guarda_arq' => $arr['dir_guarda_arq'],
								'pedidoRF' => $_POST['cb_grupo_escolhido'].';'.$_POST['cb_subgrupo_escolhido'].';'.$arr['seql_pedido_finan']
						));

					} 
				
				}
				
				// Para compor os dados do Grupo e Subgrupo acima da tabela
				$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
				$this->view->nomeGrupo = $nomeGrupo;
				$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
				$this->view->nomeSubgrupo = $nomeSubgrupo;

				$this->render('recFinanFamiliaSolicitar');	

			}	
		}
	
	}	// Fim da function recFinanFamiliaSolicitar


// ====================================================== //	
	
	public function recFinanFamiliaSolicitarMenu() {

/*
     Este programa também é chamado por 
     "recFinan1GerenciaAutorizacao.phtml contido em "recFinanDPSGerenciaController.php", 
     com origem='gerenciamentoDPS/gerenciamentoDPSCA/gerenciamentoDPSC' 

*/
		
		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		$this->view->fmlPedidoFinan = 0;

		if ($_POST['origem'] == 'inclusao') {		
			// Para nortear p/onde ir, erroValidacao == 4, é inclusão correta e deve seguir como gerenciamento
			if ($this->view->erroValidacao == 4) {
				$trata_dados = 'g';
				$origem = 'gerenciamento';
			} else {
				$trata_dados = 'i';		
				$origem = $_POST['origem'];		
			}
		
		} else {

			// Oriundo de "recFinanDPSGerenciaAutorizacao", Autorizar Solicitação
			if ($_POST['origem'] == 'gerenciamentoDPS') {		
				$origem = 'gerenciamentoDPS';

			// Oriundo de "recFinanDPSGerenciaCancelaAutorizacao"
			} else if ($_POST['origem'] == 'gerenciamentoDPSCA') {						
				$origem = 'gerenciamentoDPSCA';

			// Oriundo de "recFinanDPSGerenciAautorizacao", Cancelar Solicitação		
			} else if ($_POST['origem'] == 'gerenciamentoDPSC') {						
				$origem = 'gerenciamentoDPSC';

			} else {
				$origem = 'gerenciamento';
			}

			$trata_dados = 'g';
		}


		if ($trata_dados == 'i') {

			// Testes para o caso de inclusão com Erro, mostrar os dados na tela
			if (isset($_POST['dsc_sucinta_pedido'])) {
				$dsc_sucinta_pedido = $_POST['dsc_sucinta_pedido'];
			} else {
				$dsc_sucinta_pedido = '';
			}

			if (isset($_POST['dsc_resum_pedido'])) {
				$dsc_resum_pedido = $_POST['dsc_resum_pedido'];
			} else {
				$dsc_resum_pedido = '';
			}

			if (isset($_POST['menor_vlr_encontra'])) {
				$menor_vlr_encontra = $_POST['menor_vlr_encontra'];
			} else {
				$menor_vlr_encontra = 0;
			}

			$data_hoje 		= 	new \DateTime();
			$data_hoje_f1	= 	$data_hoje->format("Y-m-d");
			$data_hoje_f2	= 	$data_hoje->format("d/m/Y");

			$this->view->pedidoRecFinan = array (
					'cd_grp' => $_POST['cb_grupo_escolhido'], 
					'nm_grp' => $_POST['nome_grupo'], 
					'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
					'nm_sbgrp' => $_POST['nome_subgrupo'], 
					'seql_pedido_finan' => 0,
					'dsc_sucinta_pedido' => $dsc_sucinta_pedido,
					'dsc_resum_pedido' => $dsc_resum_pedido,
					'menor_vlr_encontra' => $menor_vlr_encontra,
					'arq_orc_pedido' => '',
					'arq_compara_preco_pedido' => '',
					'dt_incl_pedido' => $data_hoje_f1,
					'dt_incl_pedido_format' => $data_hoje_f2,
					'cd_vlnt_resp_pedido' => '',
					'nm_vlnt_resp_pedido' => '',
					'dt_autoriza_pedido' => '',
					'dt_autoriza_pedido_format' => '',
					'cd_vlnt_resp_autoriza' => '',
					'cd_tip_enquadra' => '',
					'nm_tip_enquadra_format' => '',
					'cd_est_pedido' => 1,
					'nm_est_pedido_format' => 'Aguardando Autorização',
					'cd_situ_envio_ressar_pedido' => 1,
					'nm_situ_envio_ressar_pedido_format' => 'Não Enviado',
					'dir_guarda_arq' => '',			

					'origem' => 'inclusao',
					
					'dir_arq_orc' => '',
					'arq_orc_pedido_atual' => ''
			);

		} else {

			if (isset($_POST['seql_pedido_escolhido'])) {
				$seql_escolhido = $_POST['seql_pedido_escolhido'];
			} else {
				if (isset($this->view->proximoSeqlRF)) {
					$seql_escolhido = $this->view->proximoSeqlRF;	
				} else {
					$seql_escolhido = $_POST['seql_pedido_finan'];
				}
			}

			// Buscar dados do Pedido
			$dadospedidoRFBase = Container::getModel('TbPedidoRecurFinan');
			$dadospedidoRFBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$dadospedidoRFBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$dadospedidoRFBase->__set('seql_pedido_finan', $seql_escolhido);
			$dadospedidoRF = $dadospedidoRFBase->getDadosPedidoRecurFinan();

			// Buscar Nome do Voluntário
			$nomeVlntBase = Container::getModel('TbVlnt');
			$nomeVlntBase->__set('id', $dadospedidoRF['cd_vlnt_resp_pedido']);
			$nomeVlnt = $nomeVlntBase->getInfoVoluntario();

			if ($dadospedidoRF['arq_orc_pedido'] == null) {
				$arq_orc_pedido = '';
				$diretorio_arquivo = '';
			} else {
				$arq_orc_pedido = $dadospedidoRF['arq_orc_pedido'];
				$diretorio_arquivo = $dadospedidoRF['dir_guarda_arq'].'/'.$dadospedidoRF['arq_orc_pedido'];			
			}

			$this->view->pedidoRecFinan = array (
					'cd_grp' => $_POST['cb_grupo_escolhido'], 
					'nm_grp' => $_POST['nome_grupo'], 
					'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
					'nm_sbgrp' => $_POST['nome_subgrupo'], 
					'seql_pedido_finan' => $seql_escolhido,
					'dsc_sucinta_pedido' => $dadospedidoRF['dsc_sucinta_pedido'],
					'dsc_resum_pedido' => $dadospedidoRF['dsc_resum_pedido'],
					'menor_vlr_encontra' => $dadospedidoRF['menor_vlr_encontra'],
					
					'arq_orc_pedido' => $arq_orc_pedido,
					
					'arq_compara_preco_pedido' => $dadospedidoRF['arq_compara_preco_pedido'],
					'dt_incl_pedido' => $dadospedidoRF['dt_incl_pedido'],
					'dt_incl_pedido_format' => $dadospedidoRF['dt_incl_pedido_format'],
					'cd_vlnt_resp_pedido' => $dadospedidoRF['cd_vlnt_resp_pedido'],
					'nm_vlnt_resp_pedido' => $nomeVlnt['nm_vlnt'],
					'dt_autoriza_pedido' => $dadospedidoRF['dt_autoriza_pedido'],
					'dt_autoriza_pedido_format' => $dadospedidoRF['dt_autoriza_pedido_format'],
					'cd_vlnt_resp_autoriza' => $dadospedidoRF['cd_vlnt_resp_autoriza'],
					'cd_tip_enquadra' => $dadospedidoRF['cd_tip_enquadra_pedido'],
					'nm_tip_enquadra_format' => $dadospedidoRF['cd_tip_enquadra_pedido_format'],
					'cd_est_pedido' => $dadospedidoRF['cd_est_pedido'],
					'nm_est_pedido_format' => $dadospedidoRF['nm_est_pedido_format'],
					'cd_situ_envio_ressar_pedido' => $dadospedidoRF['cd_situ_envio_ressar_pedido'],
					'nm_situ_envio_ressar_pedido_format' => $dadospedidoRF['nm_situ_envio_ressar_pedido_format'],				
					'dir_guarda_arq' => $dadospedidoRF['dir_guarda_arq'],

					'origem' => $origem,

					'dir_arq_orc' => $diretorio_arquivo,
					'arq_orc_pedido_atual' => $arq_orc_pedido

			);

			// Verificar se há famílias no pedido
			$verificaFmlPedidoBase = Container::getModel('TbFmlPedidoFinan');
			$verificaFmlPedidoBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$verificaFmlPedidoBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$verificaFmlPedidoBase->__set('seql_pedido_finan', $seql_escolhido);
			$verificaFmlPedidoBase->__set('cd_est_fml_pedido', 1);  // 1 = Vigente
			$verificaFmlPedido = $verificaFmlPedidoBase->getQtdFmlPedidoFinan();

			if ($verificaFmlPedido['qtde'] > 0) {
				$this->view->fmlPedidoFinan = 1;
			}
		}

		$this->render('recFinanFamiliaSolicitarMenu');				

	}	// Fim da function recFinanFamiliaSolicitarMenu


// ====================================================== //	
	
	public function recFinanFamiliaSolicitarIncluirBase() {
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;
		
		$msg = '';
		$ha_arquivo_PDF = 0;

		// Obtém os dados vindos via $_FILES
		$arqPDF   = $_FILES["arq_orc_pedido"];	
		
	 	// Há arquivo PDF
	 	if (!empty($arqPDF["name"])) {  
	 		$ha_arquivo_PDF = 1;

			if ($arqPDF["error"] > 0) {
				$this->view->msg = "Arquivo PDF com problemas ou não é válido!";
				$this->view->erroValidacao = 11;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();
				exit;
			} 

			// Tamanho máximo do arquivo em bytes 
			$tamanho = 150000; //100000;

			// Verifica se o tamanho da imagem é maior que o tamanho permitido 
			if($arqPDF["size"] > $tamanho) { 
				$this->view->msg = "O arquivo deve ter no máximo ".$tamanho." bytes. Ele tem: ".$arqPDF["size"]." bytes"; 
				$this->view->erroValidacao = 12;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();
				exit;
			}

			// Largura máxima em pixels 
			$largura = 256; //256; 
			// Altura máxima em pixels 
			$altura = 256; //256; 

			// Pega as dimensões da imagem 
			$dimensoes = getimagesize($arqPDF["tmp_name"]);
	 
			// Verifica se a largura da imagem é maior que a largura permitida 
			if($dimensoes[0] > $largura) { 
				$this->view->msg = "A largura do arquivo não deve ultrapassar ".$largura." pixels"; 
				$this->view->erroValidacao = 13;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();			
				exit;
			}
	 
			// Verifica se a altura da imagem é maior que a altura permitida 
			if($dimensoes[1] > $altura) { 
				$this->view->msg = "A altura do arquivo não deve ultrapassar ".$altura." pixels"; 
				$this->view->erroValidacao = 14;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();
				exit;				
			}
	 
			// Pega extensão da imagem 
			$ext = 'pdf';

			// Gera um nome único para a imagem 
			$nome_pdf = md5(uniqid(time())) . "." . $ext;   

			// Diretório de onde ficará a imagem 
			$dirname = "ArqFinanSGAS";
			
			// Se não existir o diretório, cria
			if (!file_exists($dirname)) {
    	        // usa-se o numero e true para permissao de pasta
	            mkdir($dirname, 0775, true);
        	}

			$caminho_upload = $dirname .'/'. $nome_pdf;		

		} else {
			$nome_pdf = '';
			$dirname = '';
		}

		// Obtem Próximo Sequencial de Pedido Financeiro
		$proxSeqlRF = Container::getModel('TbPedidoRecurFinan');
		$proxSeqlRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$proxSeqlRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$proxSeqlRF->getProximoSequencial();			

		$this->view->proximoSeqlRF = $proxSeqlRF->__get('seql_max');

		// Obtém o VMR
		$obtemVMRBase = Container::getModel('TbVlrMinimoRefOrc');
		$obtemVMRBase->__set('cd_est_vlr_minimo_ref1', 1);
		$obtemVMRBase->__set('cd_est_vlr_minimo_ref2', 1);
		$obtemVMR = $obtemVMRBase->getDadosVMR2();			

		$menor_vlr = str_replace('.','', $_POST['menor_vlr_encontra']);
		$menor_vlr = str_replace(',','.', $menor_vlr);

		if ($obtemVMR[0]['vlr_minimo_ref'] >= $menor_vlr) {
			$cd_tip_enquadra_pedido = 1;
		} else {
			$cd_tip_enquadra_pedido = 2;
		}

		// Inserir na tabela tb_pedido_recur_finan
		$inserePRF = Container::getModel('TbPedidoRecurFinan');
		$inserePRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$inserePRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$inserePRF->__set('seql_pedido_finan', $this->view->proximoSeqlRF);
		$inserePRF->__set('dsc_sucinta_pedido', $_POST['dsc_sucinta_pedido']);
		$inserePRF->__set('dsc_resum_pedido', $_POST['dsc_resum_pedido']);
		$inserePRF->__set('menor_vlr_encontra', $menor_vlr);
		$inserePRF->__set('arq_orc_pedido', $nome_pdf);
		$inserePRF->__set('cd_vlnt_resp_pedido', $_SESSION['id']);
		$inserePRF->__set('cd_tip_enquadra_pedido', $cd_tip_enquadra_pedido);
		$inserePRF->__set('cd_est_pedido', 1);
		$inserePRF->__set('cd_situ_envio_ressar_pedido', 1);
		$inserePRF->__set('dir_guarda_arq', $dirname);
		$inserePRF->insertPedidoRecurFinan();

		// Para fazer o Upload do arquivo
		if ($ha_arquivo_PDF == 1) {
			move_uploaded_file($arqPDF["tmp_name"], $caminho_upload);   
		}

		$this->view->erroValidacao = 4;

		session_write_close();
		$this->recFinanFamiliaSolicitarMenu();

	}	// Fim da function recFinanFamiliaSolicitarIncluirBase

// ====================================================== //	

	public function recFinanFamiliaSolicitarAtualizarBase() {

		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		$msg = '';
		$ha_arquivo_PDF = 0;
		$nao_mexe_arquivo_atual = 0;

		// Diretório de onde ficará o arquivo PDF
		$dirname = "ArqFinanSGAS";

		// Obtém os dados vindos via $_FILES
		$arqPDF   = $_FILES["arq_orc_pedido"];	
		
	 	// Há arquivo PDF
	 	if (!empty($arqPDF["name"])) {  
	 		$ha_arquivo_PDF = 1;

			if ($arqPDF["error"] > 0) {
				$this->view->msg = "Arquivo PDF com problemas ou não é válido!";
				$this->view->erroValidacao = 11;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();
				exit;
			} 

			// Tamanho máximo do arquivo em bytes 
			$tamanho = 150000; //100000;

			// Verifica se o tamanho da imagem é maior que o tamanho permitido 
			if($arqPDF["size"] > $tamanho) { 
				$this->view->msg = "O arquivo deve ter no máximo ".$tamanho." bytes. Ele tem: ".$arqPDF["size"]; 
				$this->view->erroValidacao = 12;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();
				exit;
			}

			// Largura máxima em pixels 
			$largura = 256; //256; 
			// Altura máxima em pixels 
			$altura = 256; //256; 

			// Pega as dimensões da imagem 
			$dimensoes = getimagesize($arqPDF["tmp_name"]);
	 
			// Verifica se a largura da imagem é maior que a largura permitida 
			if($dimensoes[0] > $largura) { 
				$this->view->msg = "A largura do arquivo não deve ultrapassar ".$largura." pixels"; 
				$this->view->erroValidacao = 13;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();			
				exit;
			}
	 
			// Verifica se a altura da imagem é maior que a altura permitida 
			if($dimensoes[1] > $altura) { 
				$this->view->msg = "A altura do arquivo não deve ultrapassar ".$altura." pixels"; 
				$this->view->erroValidacao = 14;

				session_write_close();
				$this->recFinanFamiliaSolicitarMenu();
				exit;				
			}
	 
			// Pega extensão da imagem 
			$ext = 'pdf';

			// Gera um nome único para a imagem 
			$nome_pdf = md5(uniqid(time())) . "." . $ext;   
			
			// Se não existir o diretório, cria
			if (!file_exists($dirname)) {
    	        // usa-se o numero e true para permissao de pasta
	            mkdir($dirname, 0775, true);
        	}

			$caminho_upload = $dirname .'/'. $nome_pdf;		
		} 

		// Há arquivo PDF atualmente na base
		if (!empty($_POST['arq_orc_pedido_atual'])) {
			// Deletar o arquivo atual
			if ($ha_arquivo_PDF == 1) {
				$caminho_unlink = $dirname .'/'. $_POST['arq_orc_pedido_atual'];		

				unlink($caminho_unlink);
			
			} else {
				$nao_mexe_arquivo_atual = 1;
				//$nome_pdf = $_POST['arq_orc_pedido_atual'];
			}

		} else {
			if ($ha_arquivo_PDF == 0) {
				$nao_mexe_arquivo_atual = 1;
			}						
		}

		// Obtém o VMR
		$obtemVMRBase = Container::getModel('TbVlrMinimoRefOrc');
		$obtemVMRBase->__set('cd_est_vlr_minimo_ref1', 1);
		$obtemVMRBase->__set('cd_est_vlr_minimo_ref2', 1);
		$obtemVMR = $obtemVMRBase->getDadosVMR2();			

		$menor_vlr = str_replace('.','', $_POST['menor_vlr_encontra']);
		$menor_vlr = str_replace(',','.', $menor_vlr);

		if ($obtemVMR[0]['vlr_minimo_ref'] >= $menor_vlr) {
			$cd_tip_enquadra_pedido = 1;
		} else {
			$cd_tip_enquadra_pedido = 2;
		}

		// Arquivo atual mudou, então se dará update em nome e diretório, caso contrário, não, para evitar lixo nos campos
		if ($nao_mexe_arquivo_atual == 0) {
			// Alterar na tabela tb_pedido_recur_finan
			$alteraPRF = Container::getModel('TbPedidoRecurFinan');
			$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);
			$alteraPRF->__set('dsc_sucinta_pedido', $_POST['dsc_sucinta_pedido']);
			$alteraPRF->__set('dsc_resum_pedido', $_POST['dsc_resum_pedido']);
			$alteraPRF->__set('menor_vlr_encontra', $menor_vlr);
			
			$alteraPRF->__set('arq_orc_pedido', $nome_pdf);
			
			$alteraPRF->__set('cd_vlnt_resp_pedido', $_SESSION['id']);
			$alteraPRF->__set('cd_tip_enquadra_pedido', $cd_tip_enquadra_pedido);
			
			$alteraPRF->__set('dir_guarda_arq', $dirname);
			$alteraPRF->updatePRFComNovoArqPDF();
		
		} else {
			// Alterar na tabela tb_pedido_recur_finan
			$alteraPRF = Container::getModel('TbPedidoRecurFinan');
			$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
			$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
			$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);
			$alteraPRF->__set('dsc_sucinta_pedido', $_POST['dsc_sucinta_pedido']);
			$alteraPRF->__set('dsc_resum_pedido', $_POST['dsc_resum_pedido']);
			$alteraPRF->__set('menor_vlr_encontra', $menor_vlr);
			$alteraPRF->__set('cd_vlnt_resp_pedido', $_SESSION['id']);
			$alteraPRF->__set('cd_tip_enquadra_pedido', $cd_tip_enquadra_pedido);
			$alteraPRF->updatePRFSemNovoArqPDF();
		}

		// Para fazer o Upload do arquivo
		if ($ha_arquivo_PDF == 1) {
			move_uploaded_file($arqPDF["tmp_name"], $caminho_upload);   
		}

		$this->view->erroValidacao = 4;

		session_write_close();
		$this->recFinanFamiliaSolicitarMenu();

	}	// Fim da function recFinanFamiliaSolicitarAtualizarBase

// ====================================================== //	

	public function recFinanFamiliaVincular() {
	
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		//$this->fAAlterarRTVoluntarioPOST();
		$this->recFinanFamiliaVincularPOST();

	} // Fim da function recFinanFamiliaVincular

// ====================================================== //	

	public function recFinanFamiliaVincularPOST() {

		// Buscar Famílias que estejam atreladas ao Grupo/Subgrupo e que estejam em atendimento
		$familiasEmPedidoRFBase = Container::getModel('TbFml');
		$familiasEmPedidoRFBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$familiasEmPedidoRFBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$familiasEmPedidoRFBase->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);
		$familiasEmPedidoRF = $familiasEmPedidoRFBase->getFamiliasPedidoFinan();

		$this->view->dadosFamilias = array ();

		// Para dar mensagem de que não há Famílias para Vincular ao Pedido Financeiro
		if (count($familiasEmPedidoRF) == 0) {
			$this->view->erroValidacao = 2;
		}

		foreach ($familiasEmPedidoRF as $index => $arr) {
			if ($arr['simNao'] == 's') {
				$vinculado = 'Sim';
			} else {
				$vinculado = 'Não';
			}

			array_push($this->view->dadosFamilias, array (
						'cd_fml' => $arr['cd_fml'],
						'nm_grp_fmlr' => $arr['nm_grp_fmlr'],
						'vinculado' => $vinculado,
						'cd_fml_sn' => $arr['cd_fml'].';'.$arr['simNao']
			));

		}

		// Para compor os dados do Grupo e Subgrupo acima da tabela
		$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
		$this->view->nomeGrupo = $_POST['nome_grupo'];
		$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
		$this->view->nomeSubgrupo = $_POST['nome_subgrupo'];
		$this->view->seqlPedidoFinan = $_POST['seql_pedido_finan'];
		$this->view->origem = $_POST['origem'];
		
		$this->render('recFinanFamiliaVincular');

	} // Fim da function recFinanFamiliaVincularPOST


// ====================================================== //	

	public function recFinanFamiliaVincularBase() {

		$this->validaAutenticacao();	

		// Famílias escolhidas para inclusão ou cancelamento 
		$familias = explode(',', $_POST['familia_escolhida']);

		if ($_POST['situacao'] == 'inclui') {
			for ($i = 0; $i < count($familias); $i++) {
				$familia_e = explode(';', $familias[$i]);
				$familia_g = $familia_e[0];

				// Inserir Família em tb_fml_pedido_finan
				$insereFmlPRF = Container::getModel('TbFmlPedidoFinan');
				$insereFmlPRF->__set('cd_fml', $familia_g);
				$insereFmlPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$insereFmlPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
				$insereFmlPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);
				$insereFmlPRF->insertFmlPedidoFinan();
			}

			$this->view->erroValidacao = 1;
		
		} else {

			for ($i = 0; $i < count($familias); $i++) {
				$familia_e = explode(';', $familias[$i]);
				$familia_g = $familia_e[0];

				// Excluir Família em tb_fml_pedido_finan
				$excluiFmlPRF = Container::getModel('TbFmlPedidoFinan');
				$excluiFmlPRF->__set('cd_fml', $familia_g);
				$excluiFmlPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
				$excluiFmlPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
				$excluiFmlPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);
				$excluiFmlPRF->deleteFmlPedidoFinan();
			}			

				$this->view->erroValidacao = 3;
		}

		$this->recFinanFamiliaVincularPOST();

	} // Fim da function recFinanFamiliaVincularBase

// ====================================================== //	

	public function recFinanFamiliaSolicitarCancelarBase() {

		$this->validaAutenticacao();	

		// Alterar na tabela tb_pedido_recur_finan para cd_est_pedido = 4 (Cancelado)
		$alteraPRF = Container::getModel('TbPedidoRecurFinan');
		$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraPRF->__set('cd_est_pedido', 4);
		$alteraPRF->__set('cd_vlnt_resp_pedido', $_SESSION['id']);
		$alteraPRF->updatePRFCdEstado();

		$this->view->erroValidacao = 2;

		session_write_close();
		$this->recFinanFamiliaSolicitar();

	}	// Fim da function recFinanFamiliaSolicitarCancelarBase

// ====================================================== //	

	public function recFinanFamiliaSolicitarConcluirBase() {

		$this->validaAutenticacao();	

		// Alterar na tabela tb_pedido_recur_finan para cd_est_pedido = 4 (Cancelado)
		$alteraPRF = Container::getModel('TbPedidoRecurFinan');
		$alteraPRF->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$alteraPRF->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$alteraPRF->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);	
		$alteraPRF->__set('cd_est_pedido', 2);
		$alteraPRF->__set('cd_vlnt_resp_pedido', $_SESSION['id']);
		$alteraPRF->updatePRFCdEstado();

		$this->view->erroValidacao = 3;

		session_write_close();
		$this->recFinanFamiliaSolicitar();

	}	// Fim da function recFinanFamiliaSolicitarConcluirBase



// PAREI AQUI - VOLTAR A VER OU USAR ESTAS FUNÇÕES APÓS O FINANCEIRO DPS - ALTERAR O QUE ESTÁ ABAIXO

// ====================================================== //	
	
	public function recFinanFamiliaPreConsultar() {

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

			$this->render('familiaFinanceiro');				

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

			$this->render('recFinanFamiliaPreConsultar');
		}
	}	// Fim da function recFinanFamiliaConsultar

// ====================================================== //	

	public function recFinanFamiliaConsultar() {
		
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

			$this->view->codVoluntario = $_SESSION['id'];		

			$this->render('recFinanFamiliaPreConsultar');	

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
				
				$this->view->codVoluntario = $_SESSION['id'];		

				$this->render('recFinanFamiliaPreConsultar');	
			
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

					$this->view->codVoluntario = $_SESSION['id'];		
					
					$this->render('recFinanFamiliaPreConsultar');	

				} else {

					// Para ALTERAÇÃO de Solicitação de Recurso Financeiro, somente se estiver em grupo/subgrupo e
					// cd_atu_vlnt = 2-Coordenador Financeiro na tabela tb_vncl_vlnt_grp
					$this->nivel_atuacao_requerido = 2;
					
					$this->validaAcesso();

					// Não está na tabela de vinculo de grupo e subgrupo
					if ($this->retornoValidaAcesso == 1) {
						$this->view->datas = array (
							'data_inicial' => $_POST['dt_inc'],
							'data_final' => $_POST['dt_fim'],
							'rota' => $_POST['rota']
						);

						$this->view->codVoluntario = $_SESSION['id'];		

						$this->render('recFinanFamiliaPreConsultar');				

					// Está na tabela de vínculo de grupo e subgrupo, mas não tem o nível Requerido
					} else if ($this->retornoValidaAcesso == 2) {
						$this->view->datas = array (
							'data_inicial' => $_POST['dt_inc'],
							'data_final' => $_POST['dt_fim'],
							'rota' => $_POST['rota']
						);

						$this->view->codVoluntario = $_SESSION['id'];		

						$this->render('recFinanFamiliaPreConsultar');				

					} else {

						// Buscar Nome de Grupo e Subgrupo
						$dadosGrupoSubgrupo = Container::getModel('TbSbgrp');
						$dadosGrupoSubgrupo->__set('codGrupo_pesq', $_POST['cb_grupo_escolhido']);
						$dadosGrupoSubgrupo->__set('codSubgrupo_pesq', $_POST['cb_subgrupo_escolhido']);
						$dadosGS = $dadosGrupoSubgrupo->getDadosSubgrupo();
						
						$nomeGrupo = $dadosGS['nome_grupo'];
						$nomeSubgrupo = $dadosGS['nome_subgrupo'];

						// Buscar Todos os Pedidos 
						$pedidoRecurFinanBase = Container::getModel('TbPedidoRecurFinan');
						$pedidoRecurFinanBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
						$pedidoRecurFinanBase->__set('cd_sbgrp',  $_POST['cb_subgrupo_escolhido']);
						$pedidoRecurFinanBase->__set('cd_est_pedido1', 1);
						$pedidoRecurFinanBase->__set('cd_est_pedido4', 4);
						$pedidoRecurFinanBase->__set('dt_inicio', $_POST['dt_inc']);
						$pedidoRecurFinanBase->__set('dt_fim', $_POST['dt_fim']);
						$pedidoRecurFinan = $pedidoRecurFinanBase->getDadosPedidoRecurFinanAllConsulta();

						$this->view->pedidoRecFinan = array ();

						if (count($pedidoRecurFinan) > 0) {
							foreach ($pedidoRecurFinan as $index => $arr) {
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
										'cd_grp' => $_POST['cb_grupo_escolhido'], 
										'nm_grp' => $nomeGrupo, 
										'cd_sbgrp' => $_POST['cb_subgrupo_escolhido'], 
										'nm_sbgrp' => $nomeSubgrupo, 
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
										'nm_tip_enquadra_format' => $arr['cd_tip_enquadra_pedido_format'],
										'cd_est_pedido' => $arr['cd_est_pedido'],
										'nm_est_pedido_format' => $arr['cd_est_pedido_format'],
										'cd_situ_envio_ressar_pedido' => $arr['cd_situ_envio_ressar_pedido'],
										'nm_situ_envio_ressar_pedido_format' => $arr['cd_situ_envio_ressar_pedido_format'],
										'dir_guarda_arq' => $arr['dir_guarda_arq'],
										'pedidoRF' => $_POST['cb_grupo_escolhido'].';'.$_POST['cb_subgrupo_escolhido'].';'.$arr['seql_pedido_finan']
								));

							} 
						
						}
						
						// Para compor os dados do Grupo e Subgrupo acima da tabela
						$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
						$this->view->nomeGrupo = $nomeGrupo;
						$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
						$this->view->nomeSubgrupo = $nomeSubgrupo;

						$this->render('recFinanFamiliaConsultar');
					}
				}
			}
		}

	}	// Fim da function recFinanFamiliaConsultar

// ====================================================== //	

	// Para atender consulta de Famílias no pedido quando da Autorização do mesmo

	public function recFinanFamiliaConsultarVinculo() {
	
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		// Buscar Famílias que estejam em tb_fml_pedido_finan
		$familiasEmPedidoRFBase = Container::getModel('TbFmlPedidoFinan');
		$familiasEmPedidoRFBase->__set('cd_grp', $_POST['cb_grupo_escolhido']);
		$familiasEmPedidoRFBase->__set('cd_sbgrp', $_POST['cb_subgrupo_escolhido']);
		$familiasEmPedidoRFBase->__set('seql_pedido_finan', $_POST['seql_pedido_finan']);
		$familiasEmPedidoRF = $familiasEmPedidoRFBase->getFmlPedidoFinan();

		$this->view->dadosFamilias = array ();

		foreach ($familiasEmPedidoRF as $index => $arr) {
			array_push($this->view->dadosFamilias, array (
						'cd_fml' => $arr['cd_fml'],
						'nm_grp_fmlr' => $arr['nm_grp_fmlr']
			));
		}

		// Para compor os dados do Grupo e Subgrupo acima da tabela
		$this->view->codGrupo = $_POST['cb_grupo_escolhido'];
		$this->view->nomeGrupo = $_POST['nome_grupo'];
		$this->view->codSubgrupo = $_POST['cb_subgrupo_escolhido'];
		$this->view->nomeSubgrupo = $_POST['nome_subgrupo'];
		$this->view->seqlPedidoFinan = $_POST['seql_pedido_finan'];
		$this->view->origem = $_POST['origem'];
		
		$this->render('recFinanFamiliaConsultarVinculo');

	} // Fim da function recFinanFamiliaConsultarVinculo












}	//	Fim da classe

?>
				