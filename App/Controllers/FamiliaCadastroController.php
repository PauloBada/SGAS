<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class FamiliaCadastroController extends Action {

// ================================================== //

	public function validaAutenticacao() {
		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}

// ================================================== //
//          Início de Cadastro de Família             //
// ================================================== //

	public function familiaCadastro() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		$this->render('familiaCadastro');
	}

// ================================================== //

	public function familiaCadastroIncluir() {

		if (isset($this->view->familia_atendida_anteriormente)) {
			if (empty($this->view->familia_atendida_anteriormente)) {
				$cd_familia_atendida_anteriormente = '';
			} else {
				$cd_familia_atendida_anteriormente = $this->view->familia_atendida_anteriormente;
			}
		} else {
			$cd_familia_atendida_anteriormente = $_POST['familia_atendida_anteriormente'];
		}

		if (!empty($cd_familia_atendida_anteriormente)) {
			// Buscar dados Família
			$dadosFamilia = Container::getModel('TbFml');
			$dadosFamilia->__set('codFamilia', $cd_familia_atendida_anteriormente);
			$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

			if ($dadosFamiliaBase['cd_est_situ_fml'] < 4) {
				$this->view->erroValidacao = 2;

				$this->view->nm_familia_atendida_anteriormente = $dadosFamiliaBase['nm_grp_fmlr'];				

				$this->view->preInclusao = array (
					'nm_astd_pre' => $_POST['nm_astd_pre'],
					'cpf_pre' => $_POST['cpf_pre']
				);

				$nm_familia_atendida_anteriormente = $dadosFamiliaBase['nm_grp_fmlr'];

				$this->render('familiaCadastroPreIncluir');
			} else {
				$nm_familia_atendida_anteriormente = $dadosFamiliaBase['nm_grp_fmlr'];
			}
		} else {
			$nm_familia_atendida_anteriormente = '';
		}

		// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();

		$this->view->regioes = $regioesBase;

		$this->view->familia = array (
				    'nome' => $_POST['nm_astd_pre'],
				    'nomeAssistidoPrincipal' => $_POST['nm_astd_pre'],
				    'endereco' => '',
				    'cpf' => $_POST['cpf_pre'],
				    'ra_escolhida' => '',
				    'pontoReferencia' => '',
				    'fonecontato1' => '',
				    'fonecontato2' => '',
				    'fonecontato3' => "",
				    'condicaoSaude' => '',
				    'condicaoSustento' => '',
				    'criterioEngajamento' => '',
				    'tipoResidencia' => '',
				    'tipoEdificacaoResidencia' => '',
				    'anotacaoAtendimentoFraterno' => '',
				    'responsavelEncaminhamentoDAO' => '',
				    'rendaAproximada' => '',
				    'sexo' => 'feminino',
				    'escolaridade' => '',
				    'estudo' => 'estudando',
				    'trabalho' => 'trabalhando',
				    'atividadeAtual' => '',
				    'tipoIncapacidade' => '',
				    'nridentidade' => '',
				    'dtnasc' => '',
				    'codFamiliaAnterior' => $cd_familia_atendida_anteriormente,
				    'nomeFamiliaAnterior' => $nm_familia_atendida_anteriormente
		);

		$this->view->erroValidacao = 10;
		$this->render('familiaCadastroIncluir');

	}	// Fim da function familiaCadastroIncluir

// ================================================== //

	public function familiaCadastroIncluirBase() {
		
		$this->validaAutenticacao();

		$this->view->erroValidacao = 0;
		$processa_insert = 1;

		$trata_cpf = str_replace(".", "", $_POST['cpf']);
		$trata_cpf = str_replace("-", "", $trata_cpf);

		$trata_valor_renda = str_replace(".", "", $_POST['rendaAproximada']);
		$trata_valor_renda = str_replace(",", ".", $trata_valor_renda);

		$trata_fone_contato1 = str_replace(" ", "", $_POST['fonecontato1']);
		$trata_fone_contato1 = str_replace("(", "", $trata_fone_contato1);
		$trata_fone_contato1 = str_replace(")", "", $trata_fone_contato1);
		$trata_fone_contato1 = str_replace("-", "", $trata_fone_contato1);

		$trata_fone_contato2 = str_replace(" ", "", $_POST['fonecontato2']);
		$trata_fone_contato2 = str_replace("(", "", $trata_fone_contato2);
		$trata_fone_contato2 = str_replace(")", "", $trata_fone_contato2);
		$trata_fone_contato2 = str_replace("-", "", $trata_fone_contato2);

		$trata_fone_contato3 = str_replace(" ", "", $_POST['fonecontato3']);
		$trata_fone_contato3 = str_replace("(", "", $trata_fone_contato3);
		$trata_fone_contato3 = str_replace(")", "", $trata_fone_contato3);
		$trata_fone_contato3 = str_replace("-", "", $trata_fone_contato3);

		if ($_POST['sexo'] == 'masculino') {
			$trata_sexo = 1;
		} else {
			$trata_sexo = 2;
		}

		if ($_POST['estudo'] == 'estudando') {
			$trata_estudo = 1;
		} else {
			$trata_estudo = 2;
		}

		if ($_POST['trabalho'] == 'trabalhando') {
			$trata_trabalho = 1;
		} else {
			$trata_trabalho = 2;
		}

		if(! empty($_POST['cpf'])) {
			$cpf_valida = Funcoes::validaCPF($trata_cpf);

			if ($cpf_valida == 3 || $cpf_valida == 4) {
				$processa_insert = 0;

				$this->view->erroValidacao = 1;

				// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
				$regioesAdm = Container::getModel('TbRegAdm');
				$regioesBase = $regioesAdm->getDadosRAAll();

				$this->view->regioes = $regioesBase;

				$this->view->familia = array (
						    'nome' => $_POST['nome'],
						    'nomeAssistidoPrincipal' => $_POST['nomeAssistidoPrincipal'],
						    'endereco' => $_POST['endereco'],
						    'cpf' => $_POST['cpf'],
						    'ra_escolhida' => $_POST['ra_escolhida'],
						    'pontoReferencia' => $_POST['pontoReferencia'],
						    'fonecontato1' => $_POST['fonecontato1'],
						    'fonecontato2' => $_POST['fonecontato2'],
						    'fonecontato3' => $_POST['fonecontato3'],
						    'condicaoSaude' => $_POST['condicaoSaude'],
						    'condicaoSustento' => $_POST['condicaoSustento'],
						    'criterioEngajamento' => $_POST['criterioEngajamento'],
						    'tipoResidencia' => $_POST['tipoResidencia'],
						    'tipoEdificacaoResidencia' => $_POST['tipoEdificacaoResidencia'],
						    'anotacaoAtendimentoFraterno' => $_POST['anotacaoAtendimentoFraterno'],
						    'responsavelEncaminhamentoDAO' => $_POST['responsavelEncaminhamentoDAO'],
						    'rendaAproximada' => $trata_valor_renda,
						    'sexo' => $_POST['sexo'],
						    'escolaridade' => $_POST['escolaridade'],
						    'estudo' => $_POST['estudo'],
						    'trabalho' => $_POST['trabalho'],
						    'atividadeAtual' => $_POST['atividadeAtual'],
						    'tipoIncapacidade' => $_POST['tipoIncapacidade'],
						    'nridentidade' => $_POST['nridentidade'],
						    'dtnasc' => $_POST['dtnasc'],
						    'codFamiliaAnterior' => $_POST['codFamiliaAnterior'],
							'nomeFamiliaAnterior' => $_POST['nomeFamiliaAnterior']
				);

				$this->render('familiaCadastroIncluir');				
			}
		}

		// Validar se data nascimento válida
		$valida_data = Funcoes::ValidaData($_POST['dtnasc']);
		if ($valida_data == 0) {
			$processa_insert = 0;
			
			$this->view->erroValidacao = 2;

			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->view->familia = array (
					    'nome' => $_POST['nome'],
					    'nomeAssistidoPrincipal' => $_POST['nomeAssistidoPrincipal'],
					    'endereco' => $_POST['endereco'],
					    'cpf' => $_POST['cpf'],
					    'ra_escolhida' => $_POST['ra_escolhida'],
					    'pontoReferencia' => $_POST['pontoReferencia'],
					    'fonecontato1' => $_POST['fonecontato1'],
					    'fonecontato2' => $_POST['fonecontato2'],
					    'fonecontato3' => $_POST['fonecontato3'],
					    'condicaoSaude' => $_POST['condicaoSaude'],
					    'condicaoSustento' => $_POST['condicaoSustento'],
					    'criterioEngajamento' => $_POST['criterioEngajamento'],
					    'tipoResidencia' => $_POST['tipoResidencia'],
					    'tipoEdificacaoResidencia' => $_POST['tipoEdificacaoResidencia'],
					    'anotacaoAtendimentoFraterno' => $_POST['anotacaoAtendimentoFraterno'],
					    'responsavelEncaminhamentoDAO' => $_POST['responsavelEncaminhamentoDAO'],
					    'rendaAproximada' => $trata_valor_renda,
					    'sexo' => $_POST['sexo'],
					    'escolaridade' => $_POST['escolaridade'],
					    'estudo' => $_POST['estudo'],
					    'trabalho' => $_POST['trabalho'],
					    'atividadeAtual' => $_POST['atividadeAtual'],
					    'tipoIncapacidade' => $_POST['tipoIncapacidade'],
					    'nridentidade' => $_POST['nridentidade'],
					    'dtnasc' => $_POST['dtnasc'],
					    'codFamiliaAnterior' => $_POST['codFamiliaAnterior'],
						'nomeFamiliaAnterior' => $_POST['nomeFamiliaAnterior']
			);

			$this->render('familiaCadastroIncluir');				
		}

		if ($processa_insert == 1) {
			try {
				// Buscar o próximo número de Família
				$busca_prox_cd_fml = Container::getModel('TbFml');
				$busca_prox_cd_disp = $busca_prox_cd_fml->obtemProxCdFml();	
						
				if ($busca_prox_cd_disp['max_cd_fml'] == null) {
					$prox_cd_fml = 1;
				} else {
					$prox_cd_fml = $busca_prox_cd_disp['max_cd_fml'] + 1;
				}

				// Inserir na tabela tb_fml
				$insereFamilia = Container::getModel('TbFml');
				$insereFamilia->__set('cd_fml', $prox_cd_fml);
				$insereFamilia->__set('nm_grp_fmlr', $_POST['nome']);
				$insereFamilia->__set('nm_astd_prin', $_POST['nomeAssistidoPrincipal']);
				$insereFamilia->__set('dsc_end', $_POST['endereco']);
				$insereFamilia->__set('cd_reg_adm', $_POST['ra_escolhida']);
				$insereFamilia->__set('dsc_pto_refe', $_POST['pontoReferencia']);
				$insereFamilia->__set('cd_atndt_ant_fml', $_POST['codFamiliaAnterior']);
				$insereFamilia->__set('fone_1', $trata_fone_contato1);
				$insereFamilia->__set('fone_2', $trata_fone_contato2);
				$insereFamilia->__set('fone_3', $trata_fone_contato3);
				$insereFamilia->__set('dsc_cndc_saude', $_POST['condicaoSaude']);
				$insereFamilia->__set('dsc_sust_fml', $_POST['condicaoSustento']);
				$insereFamilia->__set('cd_crit_engjto', $_POST['criterioEngajamento']);
				$insereFamilia->__set('cd_tip_resid', $_POST['tipoResidencia']);
				$insereFamilia->__set('cd_tip_edif_resid', $_POST['tipoEdificacaoResidencia']);
				$insereFamilia->__set('dsc_anot_atnd_fraterno', $_POST['anotacaoAtendimentoFraterno']);
				$insereFamilia->__set('rsp_enca_DAO', $_POST['responsavelEncaminhamentoDAO']);
				$insereFamilia->__set('vlr_aprox_renda_mensal_fml', $trata_valor_renda);
				$insereFamilia->__set('cd_vlnt_resp_cadastro', $_SESSION['id']);

				$insereFamilia->insertFamilia();			

				// Inserir na tabela tb_integ_fml
				$insereIntegranteFamilia = Container::getModel('TbIntegFml');
				$insereIntegranteFamilia->__set('cd_fml', $prox_cd_fml);
				$insereIntegranteFamilia->__set('nm_integ', $_POST['nomeAssistidoPrincipal']);
				$insereIntegranteFamilia->__set('cpf', $trata_cpf);
				$insereIntegranteFamilia->__set('nr_doc_ident', $_POST['nridentidade']);
				$insereIntegranteFamilia->__set('dt_nasc', $_POST['dtnasc']);
				$insereIntegranteFamilia->__set('cd_sexo', $trata_sexo);
				$insereIntegranteFamilia->__set('cd_situ_estudo', $trata_estudo);
				$insereIntegranteFamilia->__set('cd_escolar', $_POST['escolaridade']);
				$insereIntegranteFamilia->__set('cd_situ_trab', $trata_trabalho);
				$insereIntegranteFamilia->__set('dsc_atvd_atual', $_POST['atividadeAtual']);
				$insereIntegranteFamilia->__set('cd_tip_incapacidade', $_POST['tipoIncapacidade']);
				$insereIntegranteFamilia->__set('cd_rlc_com_astd_prin', 1);
				$insereIntegranteFamilia->insertIntegranteFamilia();

				// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
				$regioesAdm = Container::getModel('TbRegAdm');
				$regioesBase = $regioesAdm->getDadosRAAll();

				$this->view->regioes = $regioesBase;

				$this->view->familia = array (
						    'nome' => '',
						    'nomeAssistidoPrincipal' => '',
						    'endereco' => '',
						    'cpf' => '',
						    'ra_escolhida' => '',
						    'pontoReferencia' => '',
						    'fonecontato1' => '',
						    'fonecontato2' => '',
						    'fonecontato3' => "",
						    'condicaoSaude' => '',
						    'condicaoSustento' => '',
						    'criterioEngajamento' => '',
						    'tipoResidencia' => '',
						    'tipoEdificacaoResidencia' => '',
						    'anotacaoAtendimentoFraterno' => '',
						    'responsavelEncaminhamentoDAO' => '',
						    'rendaAproximada' => '',
						    'sexo' => 'feminino',
						    'escolaridade' => '',
						    'estudo' => 'estudando',
						    'trabalho' => 'trabalhando',
						    'atividadeAtual' => '',
						    'tipoIncapacidade' => '',
						    'nridentidade' => '',
						    'dtnasc' => '',
						    'codFamiliaAnterior' => '',
						    'nomeFamiliaAnterior' => ''
				);

				$this->view->erroValidacao = 2;
				$this->view->codigoInclusao = $prox_cd_fml;
				$this->view->nomeInclusao = $_POST['nome'];
			
				$this->render('familiaCadastro');
			
			} catch (Exception $e) {
				$this->view->erroValidacao = 9;
				$this->view->erroException = $e;

				$this->render('familiaCadastro');
			}
		}

	}	// Fim da function familiaCadastroIncluirBase

// ================================================== //

	public function familiaCadastroAlterar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('familiaCadastro');				
		} else {
			// Buscar todas as familias aptas a serem alteradas
			$familiasAll = Container::getModel('TbFml');
			$familiasBase = $familiasAll->getDadosFamiliaAll2();

			$this->view->familias = $familiasBase;

			$this->view->erroValidacao = 0;
			$this->render('familiaCadastroAlterar');
		}

	}	// Fim da function familiaCadastroAlterar

// ================================================== //

	public function familiaCadastroAlterarMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 0;

		// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();

		$this->view->regioes = $regioesBase;

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		if (! empty($dadosFamiliaBase['fone_1'])) {
			$fone_1_formatado = Funcoes::formatarNumeros('fone', $dadosFamiliaBase['fone_1'], 10, "");
		} else {
			$fone_1_formatado = $dadosFamiliaBase['fone_1'];
		}

		if (! empty($dadosFamiliaBase['fone_2'])) {
			$fone_2_formatado = Funcoes::formatarNumeros('fone', $dadosFamiliaBase['fone_2'], 10, "");
		} else {
			$fone_2_formatado = $dadosFamiliaBase['fone_2'];
		}

		if (! empty($dadosFamiliaBase['fone_3'])) {
			$fone_3_formatado = Funcoes::formatarNumeros('fone', $dadosFamiliaBase['fone_3'], 11, "");
		} else {
			$fone_3_formatado = $dadosFamiliaBase['fone_3'];
		}

	    if ($dadosFamiliaBase['cd_atndt_ant_fml'] > 0) {
	    	// Buscar dados Família Anterior
			$dadosFamilia_1 = Container::getModel('TbFml');
			$dadosFamilia_1->__set('codFamilia', $dadosFamiliaBase['cd_atndt_ant_fml']);
			$dadosFamiliaBase_1 = $dadosFamilia_1->getDadosFamilia();

			$nm_fml_atndt_ant = $dadosFamiliaBase_1['nm_grp_fmlr'];
		} else {
			$nm_fml_atndt_ant = '';
		}

		$this->view->familia = array (
				'cd_fml' => $dadosFamiliaBase['cd_fmlID'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'], 
				'nm_astd_prin' => $dadosFamiliaBase['nm_astd_prin'], 
				'dsc_end' => $dadosFamiliaBase['dsc_end'], 
				'cd_reg_adm' => $dadosFamiliaBase['cd_reg_adm'], 
				'dsc_pto_refe' => $dadosFamiliaBase['dsc_pto_refe'], 
				'cd_atndt_ant_fml' => $dadosFamiliaBase['cd_atndt_ant_fml'],
				'fone_1' => $fone_1_formatado,
				'fone_2' => $fone_2_formatado,
				'fone_3' => $fone_3_formatado,
				'dsc_cndc_saude' => $dadosFamiliaBase['dsc_cndc_saude'], 
				'dsc_sust_fml' => $dadosFamiliaBase['dsc_sust_fml'], 
				'cd_crit_engjto' => $dadosFamiliaBase['cd_crit_engjto'], 
				'cd_tip_resid' => $dadosFamiliaBase['cd_tip_resid'], 
				'cd_tip_edif_resid' => $dadosFamiliaBase['cd_tip_edif_resid'], 
				'dsc_anot_atnd_fraterno' => $dadosFamiliaBase['dsc_anot_atnd_fraterno'], 
				'rsp_enca_DAO' => $dadosFamiliaBase['rsp_enca_DAO'], 
				'vlr_aprox_renda_mensal_fml' => $dadosFamiliaBase['vlr_aprox_renda_mensal_fml'],
				'assistidoprincipal' => $dadosFamiliaBase['nm_astd_prin'],
				'estadoSituacaoFamilia' => $dadosFamiliaBase['cd_est_situ_fml'],
				'nm_fml_atndt_ant' => $nm_fml_atndt_ant
		);

		$this->render('familiaCadastroAlterarMenu');


	}	// Fim da function familiaCadastroAlterarMenu

// ================================================== //

	public function familiaCadastroAlterarBase() {
		
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;

		$trata_valor_renda = str_replace(".", "", $_POST['vlr_aprox_renda_mensal_fml']);
		$trata_valor_renda = str_replace(",", ".", $trata_valor_renda);

		$trata_fone_1 = str_replace(" ", "", $_POST['fone_1']);
		$trata_fone_1 = str_replace("(", "", $trata_fone_1);
		$trata_fone_1 = str_replace(")", "", $trata_fone_1);
		$trata_fone_1 = str_replace("-", "", $trata_fone_1);

		$trata_fone_2 = str_replace(" ", "", $_POST['fone_2']);
		$trata_fone_2 = str_replace("(", "", $trata_fone_2);
		$trata_fone_2 = str_replace(")", "", $trata_fone_2);
		$trata_fone_2 = str_replace("-", "", $trata_fone_2);

		$trata_fone_3 = str_replace(" ", "", $_POST['fone_3']);
		$trata_fone_3 = str_replace("(", "", $trata_fone_3);
		$trata_fone_3 = str_replace(")", "", $trata_fone_3);
		$trata_fone_3 = str_replace("-", "", $trata_fone_3);

		try {
			// Alterar na tabela tb_fml
			$alteraFamilia = Container::getModel('TbFml');
			$alteraFamilia->__set('cd_fml', $_POST['cd_fml']);
			$alteraFamilia->__set('nm_grp_fmlr', $_POST['nm_grp_fmlr']);
			$alteraFamilia->__set('nm_astd_prin', $_POST['nm_astd_prin']);
			$alteraFamilia->__set('dsc_end', $_POST['dsc_end']);
			$alteraFamilia->__set('cd_reg_adm', $_POST['cd_reg_adm']);
			$alteraFamilia->__set('dsc_pto_refe', $_POST['dsc_pto_refe']);
			$alteraFamilia->__set('fone_1', $trata_fone_1);
			$alteraFamilia->__set('fone_2', $trata_fone_2);
			$alteraFamilia->__set('fone_3', $trata_fone_3);
			$alteraFamilia->__set('dsc_cndc_saude', $_POST['dsc_cndc_saude']);
			$alteraFamilia->__set('dsc_sust_fml', $_POST['dsc_sust_fml']);
			$alteraFamilia->__set('cd_crit_engjto', $_POST['cd_crit_engjto']);
			$alteraFamilia->__set('cd_tip_resid', $_POST['cd_tip_resid']);
			$alteraFamilia->__set('cd_tip_edif_resid', $_POST['cd_tip_edif_resid']);
			$alteraFamilia->__set('dsc_anot_atnd_fraterno', $_POST['dsc_anot_atnd_fraterno']);
			$alteraFamilia->__set('rsp_enca_DAO', $_POST['rsp_enca_DAO']);
			$alteraFamilia->__set('vlr_aprox_renda_mensal_fml', $trata_valor_renda);
			$alteraFamilia->__set('cd_vlnt_resp_cadastro', $_SESSION['id']);
			$alteraFamilia->updateFamilia();			

			// Alterar Nome do assistido principal em tb_integ_fml
			if ($_POST['assistidoPrincipal'] != $_POST['nm_astd_prin'])  {
				$alteraIntegranteFamilia = Container::getModel('TbIntegFml');
				$alteraIntegranteFamilia->__set('cd_fml', $_POST['cd_fml']);
				$alteraIntegranteFamilia->__set('nm_integ', $_POST['nm_astd_prin']);
				$alteraIntegranteFamilia->updateIntegranteFamilia();							
			}

			// Buscar todas as familias aptas a serem alteradas
			$familiasAll = Container::getModel('TbFml');
			$familiasBase = $familiasAll->getDadosFamiliaAll2();

			$this->view->familias = $familiasBase;

			$this->view->erroValidacao = 1;
			$this->view->codigoAlteracao = $_POST['cd_fml'];
			$this->view->nomeAlteracao = $_POST['nm_grp_fmlr'];

			$this->render('familiaCadastroAlterar');	

		} catch (Exception $e) {
			$this->view->erroValidacao = 9;
			$this->view->erroException = $e;

			$this->render('familiaCadastroAlterar');	
		}
	}	// Fim da function familiaCadastroAlterarBase

// ================================================== //

	public function familiaCadastroConsultar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('familiaCadastro');				
		} else {
			// Buscar todas as familias
			$familiasAll = Container::getModel('TbFml');
			$familiasBase = $familiasAll->getDadosFamiliaAll3();

			$this->view->familias = $familiasBase;

			$this->view->nomeGrupoFamiliar = '';			

			$this->view->erroValidacao = 0;
			$this->render('familiaCadastroConsultar');
		}

	}	// Fim da function familiaCadastroConsultar

// ================================================== //

	public function familiaCadastroConsultarMenu() {
		
		$this->validaAutenticacao();	
		
		$this->view->erroValidacao = 0;

		// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();

		$this->view->regioes = $regioesBase;

		$nm_astd_pre = '';
		$cpf_pre = '';
		$dt_inc = '';
		$dt_fim = '';
		$cb_grupo_escolhido = '';
		$cb_subgrupo_escolhido = '';
		$rota = '';
										
		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');

		// $rota:
		//       rota_01 => Consultar Família  
		//       rota_02 => Consultar Vínculo Família e Subgrupo
		//       rota_03 => Consultar Familias Sem Vínculo com Subgrupo
		//       rota_04 => Consultar Integrantes da Familia
		//       rota_05 => Pré Incluir/Incluir Família
		//       rota_06 => Consultar Família por nome da família (clone da Pré Incluir/Incluir Família)

		// $_POST ==> "familiaCadastroConsulta.phtml"
		if ($_POST['rota'] == "rota_01") {
			$dadosFamilia->__set('codFamilia', $_POST['familia_escolhida']);
			$rota = $_POST['rota'];
		}

		// Novo $_POST "subgrupoConsultarVinculoFamiliaMenu.phtml" (era $_GET)
		if ($_POST['rota'] == "rota_02") {
			$dadosFamilia->__set('codFamilia', $_POST['familia_escolhida']);
			$rota = $_POST['rota'];
			$dt_inc = $_POST['dt_inc'];
			$dt_fim = $_POST['dt_fim'];
			$cb_grupo_escolhido = $_POST['cb_grupo_escolhido'];
			$cb_subgrupo_escolhido = $_POST['cb_subgrupo_escolhido'];
		}

		// Novo $_POST "subgrupoConsultarSemVinculoFamilia.phtml" (era $_GET)
		if ($_POST['rota'] == "rota_03") {			
			$dadosFamilia->__set('codFamilia', $_POST['familia_escolhida']);
			$rota = $_POST['rota'];
		}

		// Novo $_POST "familiaCadastroPreIncluirMenu.phtml" (era $_GET)
		if ($_POST['rota'] == "rota_05") {			
			$dadosFamilia->__set('codFamilia', $_POST['familia_escolhida']);
			$rota = $_POST['rota'];
			$nm_astd_pre = $_POST['nm_astd_pre'];
			$cpf_pre = $_POST['cpf_pre'];
		}

		// Novo $_POST "familiaCadastroPreIncluirMenu.phtml" (era $_GET)
		if ($_POST['rota'] == "rota_06") {			
			$dadosFamilia->__set('codFamilia', $_POST['familia_escolhida']);
			$rota = $_POST['rota'];
			$nm_astd_pre = $_POST['nm_astd_pre'];
			$cpf_pre = $_POST['cpf_pre'];
		}

		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia2();

		// $qtd_linhas = 1 => Não tem grupo/subgrupo => "a" = a e "b" = b
		// $qtd_linhas = 2 => Tem grupo/subgrupo => segunda linha "a" = nome grupo e "b" = nome subgrupo
		$qtd_linhas = count($dadosFamiliaBase); 

		if ($qtd_linhas == 1) {
			$vetor = 0;
		} else {
			$vetor = 1;
		}

		if ($dadosFamiliaBase[$vetor]['a'] == 'a') {
			$nm_grp = 'Aguardando definição de Grupo';
		} else {
			$nm_grp = $dadosFamiliaBase[$vetor]['a'];
		}

		if ($dadosFamiliaBase[$vetor]['b'] == 'b') {
			$nm_sbgrp = 'Aguardando definição de Subgrupo';
		} else {
			$nm_sbgrp = $dadosFamiliaBase[$vetor]['b'];
		}

		if (! empty($dadosFamiliaBase[$vetor]['fone_1'])) {
			$fone_1_formatado = Funcoes::formatarNumeros('fone', $dadosFamiliaBase[$vetor]['fone_1'], 10, "");
		} else {
			$fone_1_formatado = $dadosFamiliaBase[$vetor]['fone_1'];
		}

		if (! empty($dadosFamiliaBase[$vetor]['fone_2'])) {
			$fone_2_formatado = Funcoes::formatarNumeros('fone', $dadosFamiliaBase[$vetor]['fone_2'], 10, "");
		} else {
			$fone_2_formatado = $dadosFamiliaBase[$vetor]['fone_2'];
		}

		if (! empty($dadosFamiliaBase[$vetor]['fone_3'])) {
			$fone_3_formatado = Funcoes::formatarNumeros('fone', $dadosFamiliaBase[$vetor]['fone_3'], 11, "");
		} else {
			$fone_3_formatado = $dadosFamiliaBase[$vetor]['fone_3'];
		}

		// Formatar para aparecer na tela o significado
		switch ($dadosFamiliaBase[$vetor]['cd_est_situ_fml'])
	    	{
	        case 1:
	            {
	               	$cd_est_situ_fml = 'Aguardando definição de grupo/subgrupo';
	                break;
	            }

	        case 2:
	            {
	               	$cd_est_situ_fml = 'Aguardando Triagem';
	                break;
	            }

	        case 3:
	            {
	               	$cd_est_situ_fml = 'Em atendimento pela DPS';
	                break;
	            }

	        case 4:
	            {
	               	$cd_est_situ_fml = 'Atendimento realizado e encerrado';
	                break;
	            }

	        case 5:
	            {
	               	$cd_est_situ_fml = 'Atendimento não realizado por impossibilidade triagem';
	                break;
	            }

	        case 6:
	            {
	               	$cd_est_situ_fml = 'Atendimento não realizado por família não necessitar';
	                break;
	            }
	        }

	    if ($dadosFamiliaBase[$vetor]['cd_atndt_ant_fml'] > 0) {
	    	// Buscar dados Família Anterior
			$dadosFamilia_1 = Container::getModel('TbFml');
			$dadosFamilia_1->__set('codFamilia', $dadosFamiliaBase[$vetor]['cd_atndt_ant_fml']);
			$dadosFamiliaBase_1 = $dadosFamilia_1->getDadosFamilia();

			$nm_fml_atndt_ant = $dadosFamiliaBase_1['nm_grp_fmlr'];
		} else {
			$nm_fml_atndt_ant = '';
		}

		$this->view->familia = array (
				'cd_fml' => $dadosFamiliaBase[$vetor]['cd_fmlID'], 
				'nm_grp_fmlr' => $dadosFamiliaBase[$vetor]['nm_grp_fmlr'], 
				'nm_astd_prin' => $dadosFamiliaBase[$vetor]['nm_astd_prin'], 
				'dsc_end' => $dadosFamiliaBase[$vetor]['dsc_end'], 
				'cd_reg_adm' => $dadosFamiliaBase[$vetor]['cd_reg_adm'], 
				'dsc_pto_refe' => $dadosFamiliaBase[$vetor]['dsc_pto_refe'], 
				'cd_atndt_ant_fml' => $dadosFamiliaBase[$vetor]['cd_atndt_ant_fml'],
				'fone_1' => $fone_1_formatado,
				'fone_2' => $fone_2_formatado,
				'fone_3' => $fone_3_formatado,
				'dsc_cndc_saude' => $dadosFamiliaBase[$vetor]['dsc_cndc_saude'], 
				'dsc_sust_fml' => $dadosFamiliaBase[$vetor]['dsc_sust_fml'], 
				'cd_crit_engjto' => $dadosFamiliaBase[$vetor]['cd_crit_engjto'], 
				'cd_tip_resid' => $dadosFamiliaBase[$vetor]['cd_tip_resid'], 
				'cd_tip_edif_resid' => $dadosFamiliaBase[$vetor]['cd_tip_edif_resid'], 
				'dsc_anot_atnd_fraterno' => $dadosFamiliaBase[$vetor]['dsc_anot_atnd_fraterno'], 
				'rsp_enca_DAO' => $dadosFamiliaBase[$vetor]['rsp_enca_DAO'], 
				'vlr_aprox_renda_mensal_fml' => $dadosFamiliaBase[$vetor]['vlr_aprox_renda_mensal_fml'],
				'rota' => $rota,
				'nm_astd_pre' => $nm_astd_pre,
				'cpf_pre' => $cpf_pre,
				'cd_est_situ_fml' => $cd_est_situ_fml,
				'dt_inc' => $dt_inc,
				'dt_fim' => $dt_fim,
				'cb_grupo_escolhido' => $cb_grupo_escolhido,
				'cb_subgrupo_escolhido' => $cb_subgrupo_escolhido,
				'nm_grp' => $nm_grp,
				'nm_sbgrp' => $nm_sbgrp,
				'nm_fml_atndt_ant' => $nm_fml_atndt_ant,
				'ptc_atendto_fml' => $dadosFamiliaBase[$vetor]['ptc_atendto_fml'],
				'pos_ranking_atendto_fml' => $dadosFamiliaBase[$vetor]['pos_ranking_atendto_fml']

		);

		$this->render('familiaCadastroConsultarMenu');

	}	// Fim da function familiaCadastroConsultarMenu

// ================================================== //

	public function familiaCadastroIncluirIntegrante() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('familiaCadastro');				
		} else {
			// Buscar todas as familias aptas a serem alteradas
			$familiasAll = Container::getModel('TbFml');
			$familiasBase = $familiasAll->getDadosFamiliaAll2();

			$this->view->familias = $familiasBase;

			$this->view->erroValidacao = 0;
			$this->render('familiaCadastroIncluirIntegrante');
		}

	}	// Fim da function familiaCadastroIncluirIntegrante

// ================================================== //

	public function familiaCadastroIncluirIntegranteMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 10;

		// Buscar dados Família
		$dadosFamilia = Container::getModel('TbFml');
		$dadosFamilia->__set('codFamilia', $_POST['familia_escolhida']);
		$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

		$this->view->familia = array (
				'cd_fml' => $dadosFamiliaBase['cd_fmlID'], 
				'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'], 
				'nm_integ' => '', 
				'cd_rlc_com_astd_prin' => '', 
				'cpf' => '',
				'nr_doc_ident' => '',
				'dt_nasc' => '',
				'cd_sexo' => 'feminino',		
				'cd_situ_estudo' => 'estudando',
				'cd_escolar' => '',
				'cd_situ_trab' => 'trabalhando',
				'dsc_atvd_atual' => '',
				'cd_tip_incapacidade' => ''
		);

		$this->render('familiaCadastroIncluirIntegranteMenu');

	}	// Fim da function familiaCadastroIncluirIntegranteMenu

// ================================================== //

	public function familiaCadastroIncluirIntegranteBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;
		$processa_insert = 1;

		$trata_cpf = str_replace(".", "", $_POST['cpf']);
		$trata_cpf = str_replace("-", "", $trata_cpf);

		if ($_POST['cd_sexo'] == 'masculino') {
			$trata_sexo = 1;
		} else {
			$trata_sexo = 2;
		}

		if ($_POST['cd_situ_estudo'] == 'estudando') {
			$trata_estudo = 1;
		} else {
			$trata_estudo = 2;
		}

		if ($_POST['cd_situ_trab'] == 'trabalhando') {
			$trata_trabalho = 1;
		} else {
			$trata_trabalho = 2;
		}

		if(! empty($_POST['cpf'])) {
			$cpf_valida = Funcoes::validaCPF($trata_cpf);

			if ($cpf_valida == 3 || $cpf_valida == 4) {
				$processa_insert = 0;

				$this->view->erroValidacao = 1;

				$this->view->familia = array (
						'cd_fml' => $_POST['cd_fml'], 
						'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
						'nm_integ' => $_POST['nm_integ'], 
						'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
						'cpf' => $_POST['cpf'], 
						'nr_doc_ident' => $_POST['nr_doc_ident'], 
						'dt_nasc' => $_POST['dt_nasc'], 
						'cd_sexo' => $_POST['cd_sexo'], 
						'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
						'cd_escolar' => $_POST['cd_escolar'], 
						'cd_situ_trab' => $_POST['cd_situ_trab'], 
						'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
						'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'] 
				);

				$this->render('familiaCadastroIncluirIntegranteMenu');				
			} else {
				// Verificar se cpf já não existe na base, para qualquer família com cd_est_situ_fml = 1, 2, 3 
				$verificaCPF = Container::getModel('TbIntegFml');
				$verificaCPF->__set('cpf', $trata_cpf);
				$verificaCPFBase = $verificaCPF->getQtdCPFIntegrante();
				
				if ($verificaCPFBase['qtde'] > 0) {
					$processa_insert = 0;
					
					$this->view->erroValidacao = 2;

					$this->view->familia = array (
							'cd_fml' => $_POST['cd_fml'], 
							'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
							'nm_integ' => $_POST['nm_integ'], 
							'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
							'cpf' => $_POST['cpf'], 
							'nr_doc_ident' => $_POST['nr_doc_ident'], 
							'dt_nasc' => $_POST['dt_nasc'], 
							'cd_sexo' => $_POST['cd_sexo'], 
							'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
							'cd_escolar' => $_POST['cd_escolar'], 
							'cd_situ_trab' => $_POST['cd_situ_trab'], 
							'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
							'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'] 
					);

					$this->render('familiaCadastroIncluirIntegranteMenu');				
				}
			}
		}

		// Verificar se o nome já existe nesta família
		$verificaNome = Container::getModel('TbIntegFml');
		$verificaNome->__set('cd_fml', $_POST['cd_fml']);
		$verificaNome->__set('nm_integ', $_POST['nm_integ']);
		$verificaNomeBase = $verificaNome->getQtdNomeIntegrante();
		
		if ($verificaNomeBase['qtde'] > 0) {
			$processa_insert = 0;
			
			$this->view->erroValidacao = 3;

			$this->view->familia = array (
					'cd_fml' => $_POST['cd_fml'], 
					'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
					'nm_integ' => $_POST['nm_integ'], 
					'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
					'cpf' => $_POST['cpf'], 
					'nr_doc_ident' => $_POST['nr_doc_ident'], 
					'dt_nasc' => $_POST['dt_nasc'], 
					'cd_sexo' => $_POST['cd_sexo'], 
					'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
					'cd_escolar' => $_POST['cd_escolar'], 
					'cd_situ_trab' => $_POST['cd_situ_trab'], 
					'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
					'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'] 
			);

			$this->render('familiaCadastroIncluirIntegranteMenu');				
		}


		// Validar se data nascimento válida
		$valida_data = Funcoes::ValidaData($_POST['dt_nasc']);
		if ($valida_data == 0) {
			$processa_insert = 0;
			
			$this->view->erroValidacao = 4;

			$this->view->familia = array (
					'cd_fml' => $_POST['cd_fml'], 
					'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
					'nm_integ' => $_POST['nm_integ'], 
					'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
					'cpf' => $_POST['cpf'], 
					'nr_doc_ident' => $_POST['nr_doc_ident'], 
					'dt_nasc' => $_POST['dt_nasc'], 
					'cd_sexo' => $_POST['cd_sexo'], 
					'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
					'cd_escolar' => $_POST['cd_escolar'], 
					'cd_situ_trab' => $_POST['cd_situ_trab'], 
					'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
					'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'] 
			);

			$this->render('familiaCadastroIncluirIntegranteMenu');				
		}

		if ($processa_insert == 1) {
			try {
				// Inserir na tabela tb_integ_fml
				$insereIntegranteFamilia = Container::getModel('TbIntegFml');
				$insereIntegranteFamilia->__set('cd_fml', $_POST['cd_fml']);
				$insereIntegranteFamilia->__set('nm_integ', $_POST['nm_integ']);
				$insereIntegranteFamilia->__set('cd_rlc_com_astd_prin', $_POST['cd_rlc_com_astd_prin']);
				$insereIntegranteFamilia->__set('cpf', $trata_cpf);
				$insereIntegranteFamilia->__set('nr_doc_ident', $_POST['nr_doc_ident']);
				$insereIntegranteFamilia->__set('dt_nasc', $_POST['dt_nasc']);
				$insereIntegranteFamilia->__set('cd_sexo', $trata_sexo);
				$insereIntegranteFamilia->__set('cd_situ_estudo', $trata_estudo);
				$insereIntegranteFamilia->__set('cd_escolar', $_POST['cd_escolar']);
				$insereIntegranteFamilia->__set('cd_situ_trab', $trata_trabalho);
				$insereIntegranteFamilia->__set('dsc_atvd_atual', $_POST['dsc_atvd_atual']);
				$insereIntegranteFamilia->__set('cd_tip_incapacidade', $_POST['cd_tip_incapacidade']);
				$insereIntegranteFamilia->insertIntegranteFamilia();

				$this->view->familia = array (
						'cd_fml' => $_POST['cd_fml'],
						'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
						'nm_integ' => '', 
						'cd_rlc_com_astd_prin' => '', 
						'cpf' => '',
						'nr_doc_ident' => '',
						'dt_nasc' => '',
						'cd_sexo' => '',		
						'cd_situ_estudo' => '',
						'cd_escolar' => '',
						'cd_situ_trab' => '',
						'dsc_atvd_atual' => '',
						'cd_tip_incapacidade' => ''
				);

				$this->view->erroValidacao = 0;
				$this->view->codigoFmlInclusao = $_POST['cd_fml'];
				$this->view->nomeFmlInclusao = $_POST['nm_grp_fmlr'];
				$this->view->nomeIntegranteInclusao = $_POST['nm_integ'];
				
				$this->render('familiaCadastroIncluirIntegranteMenu');
			
			} catch (Exception $e) {
				$this->view->familia = array (
						'cd_fml' => $_POST['cd_fml'],
						'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
						'nm_integ' => '', 
						'cd_rlc_com_astd_prin' => '', 
						'cpf' => '',
						'nr_doc_ident' => '',
						'dt_nasc' => '',
						'cd_sexo' => '',		
						'cd_situ_estudo' => '',
						'cd_escolar' => '',
						'cd_situ_trab' => '',
						'dsc_atvd_atual' => '',
						'cd_tip_incapacidade' => ''
				);

				$this->view->erroValidacao = 9;
				$this->view->erroException = $e;

				$this->render('familiaCadastroIncluirIntegranteMenu');
			}
		}
	}	// Fim da function familiaCadastroIncluirIntegranteBase

// ================================================== //

	public function familiaCadastroAlterarIntegrante() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('familiaCadastro');				
		} else {
			$this->view->erroValidacao = 0;

			$this->render('familiaCadastroAlterarIntegrante');
		}

	}	// Fim da function familiaCadastroAlterarIntegrante

// ================================================== //

	public function familiaCadastroAlterarIntegranteMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 10;

		// Testar se o família e integrante estão escolhidos
		if (!is_numeric($_POST['cb_familia_escolhida']) || !is_numeric($_POST['cb_integrante_escolhido']) ) {
			$this->view->erroValidacao = 3;
			$this->render('familiaCadastroAlterarIntegrante');
		} else {
			// Buscar dados Integrante
			$dadosIntegranteFamilia = Container::getModel('TbIntegFml');
			$dadosIntegranteFamilia->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosIntegranteFamilia->__set('seql_integ', $_POST['cb_integrante_escolhido']);
			$dadosIntegranteFmlBase = $dadosIntegranteFamilia->getDadosIntegranteFamilia();

			// Formatar dados para exibição formatada na tela
			if (! empty($dadosIntegranteFmlBase['cpf'])) {
				$cpf_formatado = Funcoes::formatarNumeros('cpf', $dadosIntegranteFmlBase['cpf'], 11, "");
			} else {
				$cpf_formatado = $dadosIntegranteFmlBase['cpf'];
			}

			if ($dadosIntegranteFmlBase['cd_sexo'] == 1) {
				$sexo_formatado = 'masculino';
			} else {
				$sexo_formatado = 'feminino';
			}

			if ($dadosIntegranteFmlBase['cd_situ_estudo'] == 1) {
				$situ_estudo_formatado = 'estudando';
			} else {
				$situ_estudo_formatado = 'naoestudando';
			}

			if ($dadosIntegranteFmlBase['cd_situ_trab'] == 1) {
				$situ_trab_formatado = 'trabalhando';
			} else {
				$situ_trab_formatado = 'naotrabalhando';
			}

			// AAAA-MM-DD para DD/MM/AAAA
			if (! empty($dadosIntegranteFmlBase['dt_nasc'])) {
				$datanasc_formatado = Funcoes::formatarNumeros('data', $dadosIntegranteFmlBase['dt_nasc'], 10, "AMD");
			} else {
				$datanasc_formatado = $dadosIntegranteFmlBase['dt_nasc'];
			}

			$this->view->integranteFamilia = array (
					'cd_fml' => $dadosIntegranteFmlBase['cd_fmlID'], 
					'seql_integ' => $dadosIntegranteFmlBase['seql_integID'], 
					'nm_grp_fmlr' => $dadosIntegranteFmlBase['nm_grp_fmlr'], 
					'nm_integ' =>  $dadosIntegranteFmlBase['nm_integ'],
					'cd_rlc_com_astd_prin' => $dadosIntegranteFmlBase['cd_rlc_com_astd_prin'],
					'cpf' => $cpf_formatado,
					'nr_doc_ident' => $dadosIntegranteFmlBase['nr_doc_ident'],
					'dt_nasc' => $datanasc_formatado,
					'cd_sexo' => $sexo_formatado,
					'cd_situ_estudo' => $situ_estudo_formatado,
					'cd_escolar' => $dadosIntegranteFmlBase['cd_escolar'],
					'cd_situ_trab' => $situ_trab_formatado,
					'dsc_atvd_atual' => $dadosIntegranteFmlBase['dsc_atvd_atual'],
					'cd_tip_incapacidade' => $dadosIntegranteFmlBase['cd_tip_incapacidade'],
					'cpf_compara' => $cpf_formatado
			);

			$this->render('familiaCadastroAlterarIntegranteMenu');
		}
	}	// Fim da function familiaCadastroAlterarIntegranteMenu

// ================================================== //
	
	public function familiaCadastroAlterarIntegranteBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;
		$processa_update = 1;

		$grava_fml_atendida_antes = 0;
		$cd_fml_atendida_antes = '';
		
		$trata_cpf = str_replace(".", "", $_POST['cpf']);
		$trata_cpf = str_replace("-", "", $trata_cpf);

		$trata_cpf_compara = str_replace(".", "", $_POST['cpf_compara']);
		$trata_cpf_compara = str_replace("-", "", $trata_cpf_compara);

		if ($_POST['cd_sexo'] == 'masculino') {
			$trata_sexo = 1;
		} else {
			$trata_sexo = 2;
		}

		if ($_POST['cd_situ_estudo'] == 'estudando') {
			$trata_estudo = 1;
		} else {
			$trata_estudo = 2;
		}

		if ($_POST['cd_situ_trab'] == 'trabalhando') {
			$trata_trabalho = 1;
		} else {
			$trata_trabalho = 2;
		}

		if(! empty($_POST['cpf'])) {
			$cpf_valida = Funcoes::validaCPF($trata_cpf);

			// CPF inválido
			if ($cpf_valida == 3 || $cpf_valida == 4) {
				$processa_update = 0;

				$this->view->erroValidacao = 1;
				
				$this->view->erroCPF = $_POST['cpf'];

				$this->view->integranteFamilia = array (
						'cd_fml' => $_POST['cd_fml'], 
						'seql_integ' => $_POST['seql_integ'], 
						'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
						'nm_integ' => $_POST['nm_integ'], 
						'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
						'cpf' => $_POST['cpf_compara'],
						'nr_doc_ident' => $_POST['nr_doc_ident'], 
						'dt_nasc' => $_POST['dt_nasc'], 
						'cd_sexo' => $_POST['cd_sexo'], 
						'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
						'cd_escolar' => $_POST['cd_escolar'], 
						'cd_situ_trab' => $_POST['cd_situ_trab'], 
						'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
						'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'],
						'cpf_compara' => $_POST['cpf_compara'] 
				);

				$this->render('familiaCadastroAlterarIntegranteMenu');				
			} else {
				// Somente verifica se houve alteração no CPF (de um para outro ou de nenhum para outro)
				if ($trata_cpf_compara != $trata_cpf) {
					// Verificar se cpf já não existe na base, para qualquer família com cd_est_situ_fml = 1, 2, 3 
					$verificaCPF = Container::getModel('TbIntegFml');
					$verificaCPF->__set('cpf', $trata_cpf);
					$verificaCPFBase = $verificaCPF->getQtdCPFIntegrante();
					
					// CPF já existe na base
					if ($verificaCPFBase['qtde'] > 0) {
						$processa_update = 0;
						
						$this->view->erroValidacao = 2;

						$this->view->erroCPF = $_POST['cpf'];

						$this->view->integranteFamilia = array (
								'cd_fml' => $_POST['cd_fml'], 
								'seql_integ' => $_POST['seql_integ'], 							
								'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
								'nm_integ' => $_POST['nm_integ'], 
								'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
								'cpf' => $_POST['cpf_compara'],
								'nr_doc_ident' => $_POST['nr_doc_ident'], 
								'dt_nasc' => $_POST['dt_nasc'], 
								'cd_sexo' => $_POST['cd_sexo'], 
								'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
								'cd_escolar' => $_POST['cd_escolar'], 
								'cd_situ_trab' => $_POST['cd_situ_trab'], 
								'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
								'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'],
								'cpf_compara' => $_POST['cpf_compara']  
						);

						$this->render('familiaCadastroAlterarIntegranteMenu');				
					} else {
						// Procurar se o CPF existe na base, para qualquer família com cd_est_situ_fml = 4 
						$verificaCPF4 = Container::getModel('TbIntegFml');
						$verificaCPF4->__set('cpf', $trata_cpf);
						$verificaCPFBase4 = $verificaCPF4->getCPFIntegrante4();
						
						// CPF existe na base (foi atendido em outra família/momento pela DPS)
						if ($verificaCPFBase4['maxFamilia'] > 0) {
							// Buscar dados Família Atual para pegar informação de família atendida anteriormente
							$dadosFamilia4 = Container::getModel('TbFml');
							$dadosFamilia4->__set('codFamilia', $_POST['cd_fml']);
							$dadosFamiliaBase4 = $dadosFamilia4->getDadosFamilia();

							// Se não houver informação de família desse integrante assistidada antes, colocar esta
							if ($dadosFamiliaBase4['cd_atndt_ant_fml'] == 0) {
								$grava_fml_atendida_antes = 1;
								$cd_fml_atendida_antes = $verificaCPFBase4['maxFamilia'];
							}
						}
					}
				}
			}
		}

		// Verificar se o nome já existe nesta família
		$verificaNome = Container::getModel('TbIntegFml');
		$verificaNome->__set('cd_fml', $_POST['cd_fml']);
		$verificaNome->__set('seql_integ', $_POST['seql_integ']);
		$verificaNome->__set('nm_integ', $_POST['nm_integ']);
		$verificaNomeBase = $verificaNome->getQtdNomeIntegrante2();
		
		if ($verificaNomeBase['qtde'] > 0) {
			$processa_update = 0;
			
			$this->view->erroValidacao = 3;

			$this->view->integranteFamilia = array (
					'cd_fml' => $_POST['cd_fml'], 
					'seql_integ' => $_POST['seql_integ'], 
					'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
					'nm_integ' => $_POST['nm_integ'], 
					'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
					'cpf' => $_POST['cpf'], 
					'nr_doc_ident' => $_POST['nr_doc_ident'], 
					'dt_nasc' => $_POST['dt_nasc'], 
					'cd_sexo' => $_POST['cd_sexo'], 
					'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
					'cd_escolar' => $_POST['cd_escolar'], 
					'cd_situ_trab' => $_POST['cd_situ_trab'], 
					'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
					'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'],
					'cpf_compara' => $_POST['cpf_compara'] 					 
			);

			$this->render('familiaCadastroAlterarIntegranteMenu');				
		}

		// Validar se data nascimento válida
		$valida_data = Funcoes::ValidaData($_POST['dt_nasc']);
		if ($valida_data == 0) {
			$processa_update = 0;
			
			$this->view->erroValidacao = 4;

			$this->view->integranteFamilia = array (
					'cd_fml' => $_POST['cd_fml'], 
					'seql_integ' => $_POST['seql_integ'], 
					'nm_grp_fmlr' => $_POST['nm_grp_fmlr'], 
					'nm_integ' => $_POST['nm_integ'], 
					'cd_rlc_com_astd_prin' => $_POST['cd_rlc_com_astd_prin'], 
					'cpf' => $_POST['cpf'], 
					'nr_doc_ident' => $_POST['nr_doc_ident'], 
					'dt_nasc' => $_POST['dt_nasc'], 
					'cd_sexo' => $_POST['cd_sexo'], 
					'cd_situ_estudo' => $_POST['cd_situ_estudo'], 
					'cd_escolar' => $_POST['cd_escolar'], 
					'cd_situ_trab' => $_POST['cd_situ_trab'], 
					'dsc_atvd_atual' => $_POST['dsc_atvd_atual'], 
					'cd_tip_incapacidade' => $_POST['cd_tip_incapacidade'],
					'cpf_compara' => $_POST['cpf_compara'] 					 
			);

			$this->render('familiaCadastroAlterarIntegranteMenu');				
		}

		if ($processa_update == 1) {
			try {
				// Altera na tabela tb_integ_fml
				$alteraIntegranteFamilia = Container::getModel('TbIntegFml');
				$alteraIntegranteFamilia->__set('cd_fml', $_POST['cd_fml']);
				$alteraIntegranteFamilia->__set('seql_integ', $_POST['seql_integ']);
				$alteraIntegranteFamilia->__set('nm_integ', $_POST['nm_integ']);
				$alteraIntegranteFamilia->__set('cd_rlc_com_astd_prin', $_POST['cd_rlc_com_astd_prin']);
				$alteraIntegranteFamilia->__set('cpf', $trata_cpf);
				$alteraIntegranteFamilia->__set('nr_doc_ident', $_POST['nr_doc_ident']);
				$alteraIntegranteFamilia->__set('dt_nasc', $_POST['dt_nasc']);
				$alteraIntegranteFamilia->__set('cd_sexo', $trata_sexo);
				$alteraIntegranteFamilia->__set('cd_situ_estudo', $trata_estudo);
				$alteraIntegranteFamilia->__set('cd_escolar', $_POST['cd_escolar']);
				$alteraIntegranteFamilia->__set('cd_situ_trab', $trata_trabalho);
				$alteraIntegranteFamilia->__set('dsc_atvd_atual', $_POST['dsc_atvd_atual']);
				$alteraIntegranteFamilia->__set('cd_tip_incapacidade', $_POST['cd_tip_incapacidade']);
				$alteraIntegranteFamilia->updateIntegranteFamilia2();

				// Atualizar Família atendida antes //
				if ($grava_fml_atendida_antes == 1) {
					$alteraSituacaoFamilia4 = Container::getModel('TbFml');
					$alteraSituacaoFamilia4->__set('codFamilia',  $_POST['cd_fml']);
					$alteraSituacaoFamilia4->__set('codFamiliaAnterior',  $cd_fml_atendida_antes);
					$alteraSituacaoFamilia4->updateFamiliaAnterior4();
				}
								
				$this->view->erroValidacao = 1;
				$this->view->codigoFmlAlteracao = $_POST['cd_fml'];
				$this->view->nomeFmlAlteracao = $_POST['nm_grp_fmlr'];
				$this->view->nomeIntegranteAlteracao = $_POST['nm_integ'];
				$this->view->seqlIntegranteAlteracao = $_POST['seql_integ'];
				
				$this->render('familiaCadastroAlterarIntegrante');
			
			} catch (Exception $e) {
				$this->view->familia = array (
						'cd_fml' => $_POST['cd_fml'],
						'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
						'nm_integ' => '', 
						'cd_rlc_com_astd_prin' => '', 
						'cpf' => '',
						'nr_doc_ident' => '',
						'dt_nasc' => '',
						'cd_sexo' => '',		
						'cd_situ_estudo' => '',
						'cd_escolar' => '',
						'cd_situ_trab' => '',
						'dsc_atvd_atual' => '',
						'cd_tip_incapacidade' => ''
				);

				$this->view->erroValidacao = 9;
				$this->view->erroException = $e;

				$this->render('familiaCadastroAlterarIntegrante');
			}

		}
		
	}	// Fim da function familiaCadastroAlterarIntegranteBase

// ================================================== //

	public function familiaCadastroEncerrarIntegrante() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('familiaCadastro');				
		} else {
			$this->view->erroValidacao = 0;

			$this->render('familiaCadastroEncerrarIntegrante');
		}

	}	// Fim da function familiaCadastroEncerrarIntegrante

// ================================================== //

	public function familiaCadastroEncerrarIntegranteMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 10;

		// Testar se o família e integrante estão escolhidos
		if (!is_numeric($_POST['cb_familia_escolhida']) || !is_numeric($_POST['cb_integrante_escolhido']) ) {
			$this->view->erroValidacao = 3;
			$this->render('familiaCadastroEncerrarIntegrante');
		} else {
			// Buscar dados Integrante
			$dadosIntegranteFamilia = Container::getModel('TbIntegFml');
			$dadosIntegranteFamilia->__set('cd_fml', $_POST['cb_familia_escolhida']);
			$dadosIntegranteFamilia->__set('seql_integ', $_POST['cb_integrante_escolhido']);
			$dadosIntegranteFmlBase = $dadosIntegranteFamilia->getDadosIntegranteFamilia();

			// Formatar dados para exibição formatada na tela
			if (! empty($dadosIntegranteFmlBase['cpf'])) {
				$cpf_formatado = Funcoes::formatarNumeros('cpf', $dadosIntegranteFmlBase['cpf'], 11, "");
			} else {
				$cpf_formatado = $dadosIntegranteFmlBase['cpf'];
			}

			if ($dadosIntegranteFmlBase['cd_sexo'] == 1) {
				$sexo_formatado = 'masculino';
			} else {
				$sexo_formatado = 'feminino';
			}

			if ($dadosIntegranteFmlBase['cd_situ_estudo'] == 1) {
				$situ_estudo_formatado = 'estudando';
			} else {
				$situ_estudo_formatado = 'naoestudando';
			}

			if ($dadosIntegranteFmlBase['cd_situ_trab'] == 1) {
				$situ_trab_formatado = 'trabalhando';
			} else {
				$situ_trab_formatado = 'naotrabalhando';
			}

			// AAAA-MM-DD para DD/MM/AAAA
			if (! empty($dadosIntegranteFmlBase['dt_nasc'])) {
				$datanasc_formatado = Funcoes::formatarNumeros('data', $dadosIntegranteFmlBase['dt_nasc'], 10, "AMD");
			} else {
				$datanasc_formatado = $dadosIntegranteFmlBase['dt_nasc'];
			}

			$this->view->integranteFamilia = array (
					'cd_fml' => $dadosIntegranteFmlBase['cd_fmlID'], 
					'seql_integ' => $dadosIntegranteFmlBase['seql_integID'], 
					'nm_grp_fmlr' => $dadosIntegranteFmlBase['nm_grp_fmlr'], 
					'nm_integ' =>  $dadosIntegranteFmlBase['nm_integ'],
					'cd_rlc_com_astd_prin' => $dadosIntegranteFmlBase['cd_rlc_com_astd_prin'],
					'cpf' => $cpf_formatado,
					'nr_doc_ident' => $dadosIntegranteFmlBase['nr_doc_ident'],
					'dt_nasc' => $datanasc_formatado,
					'cd_sexo' => $sexo_formatado,
					'cd_situ_estudo' => $situ_estudo_formatado,
					'cd_escolar' => $dadosIntegranteFmlBase['cd_escolar'],
					'cd_situ_trab' => $situ_trab_formatado,
					'dsc_atvd_atual' => $dadosIntegranteFmlBase['dsc_atvd_atual'],
					'cd_tip_incapacidade' => $dadosIntegranteFmlBase['cd_tip_incapacidade'],
					'cpf_compara' => $cpf_formatado
			);

			$this->render('familiaCadastroEncerrarIntegranteMenu');
		}
	}	// Fim da function familiaCadastroEncerrarIntegranteMenu

// ================================================== //
	
	public function familiaCadastroEncerrarIntegranteBase() {
		
		$this->validaAutenticacao();		

		$this->view->erroValidacao = 0;

		try {
			// Altera na tabela tb_integ_fml
			$alteraIntegranteFamilia = Container::getModel('TbIntegFml');
			$alteraIntegranteFamilia->__set('cd_fml', $_POST['cd_fml']);
			$alteraIntegranteFamilia->__set('seql_integ', $_POST['seql_integ']);
			$alteraIntegranteFamilia->updateIntegranteFamilia3();

			$this->view->erroValidacao = 1;
			$this->view->codigoFmlAlteracao = $_POST['cd_fml'];
			$this->view->nomeFmlAlteracao = $_POST['nm_grp_fmlr'];
			$this->view->nomeIntegranteAlteracao = $_POST['nm_integ'];
			$this->view->seqlIntegranteAlteracao = $_POST['seql_integ'];
			
			$this->render('familiaCadastroEncerrarIntegrante');
		
		} catch (Exception $e) {
			$this->view->familia = array (
					'cd_fml' => $_POST['cd_fml'],
					'nm_grp_fmlr' => $_POST['nm_grp_fmlr'],
					'nm_integ' => '', 
					'cd_rlc_com_astd_prin' => '', 
					'cpf' => '',
					'nr_doc_ident' => '',
					'dt_nasc' => '',
					'cd_sexo' => '',		
					'cd_situ_estudo' => '',
					'cd_escolar' => '',
					'cd_situ_trab' => '',
					'dsc_atvd_atual' => '',
					'cd_tip_incapacidade' => ''
			);

			$this->view->erroValidacao = 9;
			$this->view->erroException = $e;

			$this->render('familiaCadastroEncerrarIntegrante');
		}
	}	// Fim da function familiaCadastroEncerrarIntegranteBase

// ================================================== //

	public function familiaCadastroConsultarIntegrante() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('familiaCadastro');				
		} else {
			$this->view->erroValidacao = 0;

			$this->render('familiaCadastroConsultarIntegrante');
		}

	}	// Fim da function familiaCadastroConsultarIntegrante

// ================================================== //

	public function familiaCadastroConsultarIntegranteMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 10;

		$nomeAssistidoPre = '';
		$cpfPre = '';
		$rota = '';


		// Novo $_POST "familiaCadastroPreIncluirIntegrantesMenu" (era $_GET) //
		if ($_POST['rota'] == "rota_01" || $_POST['rota'] == "rota_02" || $_POST['rota'] == "rota_03" || $_POST['rota'] == "rota_05" || $_POST['rota'] == "rota_06") {
			$cd_familia = $_POST['cod_familia'];
			$seql_integrante = $_POST['seql_integrante'];
			$rota = $_POST['rota'];
			$nomeAssistidoPre = $_POST['nm_astd_pre'];
			$cpfPre = $_POST['cpf_pre'];
		}

		// $_POST "familiaCadastroConsultarIntegrante" //
		if ($_POST['rota'] == "rota_04") {						
				$cd_familia = $_POST['cb_familia_escolhida'];
				$seql_integrante = $_POST['cb_integrante_escolhido'];
				//$rota = 'post';
				$rota = $_POST['rota'];
		} 

		// Testar se o família e integrante estão escolhidos
		if (!is_numeric($cd_familia) || !is_numeric($seql_integrante) ) {
			$this->view->erroValidacao = 3;
			$this->render('familiaCadastroConsultarIntegrante');
		} else {
			// Buscar dados Integrante
			$dadosIntegranteFamilia = Container::getModel('TbIntegFml');
			$dadosIntegranteFamilia->__set('cd_fml', $cd_familia);
			$dadosIntegranteFamilia->__set('seql_integ', $seql_integrante);
			$dadosIntegranteFmlBase = $dadosIntegranteFamilia->getDadosIntegranteFamilia();

			// Formatar dados para exibição formatada na tela
			if (! empty($dadosIntegranteFmlBase['cpf'])) {
				$cpf_formatado = Funcoes::formatarNumeros('cpf', $dadosIntegranteFmlBase['cpf'], 11, "");
			} else {
				$cpf_formatado = $dadosIntegranteFmlBase['cpf'];
			}

			if ($dadosIntegranteFmlBase['cd_sexo'] == 1) {
				$sexo_formatado = 'masculino';
			} else {
				$sexo_formatado = 'feminino';
			}

			if ($dadosIntegranteFmlBase['cd_situ_estudo'] == 1) {
				$situ_estudo_formatado = 'estudando';
			} else {
				$situ_estudo_formatado = 'naoestudando';
			}

			if ($dadosIntegranteFmlBase['cd_situ_trab'] == 1) {
				$situ_trab_formatado = 'trabalhando';
			} else {
				$situ_trab_formatado = 'naotrabalhando';
			}

			// AAAA-MM-DD para DD/MM/AAAA
			if (! empty($dadosIntegranteFmlBase['dt_nasc'])) {
				$datanasc_formatado = Funcoes::formatarNumeros('data', $dadosIntegranteFmlBase['dt_nasc'], 10, "AMD");
			} else {
				$datanasc_formatado = $dadosIntegranteFmlBase['dt_nasc'];
			}

			if (! empty($dadosIntegranteFmlBase['dt_inc_integ'])) {
				$datainc_formatado = Funcoes::formatarNumeros('data', $dadosIntegranteFmlBase['dt_inc_integ'], 10, "AMD");
			} else {
				$datainc_formatado = $dadosIntegranteFmlBase['dt_inc_integ'];
			}

			if (! empty($dadosIntegranteFmlBase['dt_term_integ'])) {
				$dataterm_formatado = Funcoes::formatarNumeros('data', $dadosIntegranteFmlBase['dt_term_integ'], 10, "AMD");
			} else {
				$dataterm_formatado = $dadosIntegranteFmlBase['dt_term_integ'];
			}

			if ($dadosIntegranteFmlBase['cd_est_integ_fml'] == 1) {
				$est_integrante = 'Ativo';
			} else {
				$est_integrante = 'Inativo';
			}

			$this->view->integranteFamilia = array (
					'cd_fml' => $dadosIntegranteFmlBase['cd_fmlID'], 
					'seql_integ' => $dadosIntegranteFmlBase['seql_integID'], 
					'nm_grp_fmlr' => $dadosIntegranteFmlBase['nm_grp_fmlr'], 
					'nm_integ' =>  $dadosIntegranteFmlBase['nm_integ'],
					'cd_rlc_com_astd_prin' => $dadosIntegranteFmlBase['cd_rlc_com_astd_prin'],
					'cpf' => $cpf_formatado,
					'nr_doc_ident' => $dadosIntegranteFmlBase['nr_doc_ident'],
					'dt_nasc' => $datanasc_formatado,
					'cd_sexo' => $sexo_formatado,
					'cd_situ_estudo' => $situ_estudo_formatado,
					'cd_escolar' => $dadosIntegranteFmlBase['cd_escolar'],
					'cd_situ_trab' => $situ_trab_formatado,
					'dsc_atvd_atual' => $dadosIntegranteFmlBase['dsc_atvd_atual'],
					'cd_tip_incapacidade' => $dadosIntegranteFmlBase['cd_tip_incapacidade'],
					'cpf_compara' => $cpf_formatado,
					'dt_inc_integ' => $datainc_formatado,
					'dt_term_integ' => $dataterm_formatado,
					'cd_est_integ_fml' => $est_integrante,
					'rota' => $rota,
					'nm_astd_pre' => $nomeAssistidoPre,
					'cpf_pre' => $cpfPre
			);		

			$this->render('familiaCadastroConsultarIntegranteMenu');
			
		}
	}	// Fim da function familiaCadastroConsultarIntegranteMenu

// ================================================== //

	public function familiaCadastroPreIncluir() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);
		
		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];

			$this->render('familiaCadastro');				

		} else {

			$this->view->preInclusao = array (
							'nm_astd_pre' => '',
							'cpf_pre' => '',
							'rota_05'
			);

			$this->view->erroValidacao = 10;
			$this->render('familiaCadastroPreIncluir');
		}

	}	// Fim da function familiaCadastroPreIncluir

// ================================================== //

	public function familiaCadastroPreIncluirMenu() {
		
		$this->validaAutenticacao();		

		$processa_insert = 1;

		$tem_cpf = 0;

		$trata_nome = str_replace(" ", "%", $_POST['nm_astd_pre']);

		$trata_cpf = str_replace(".", "", $_POST['cpf_pre']);
		$trata_cpf = str_replace("-", "", $trata_cpf);

		if(! empty($_POST['cpf_pre'])) {
			$tem_cpf = 1;

			$cpf_valida = Funcoes::validaCPF($trata_cpf);

			if ($cpf_valida == 3 || $cpf_valida == 4) {
				$processa_insert = 0;

				$this->view->erroValidacao = 1;

				$this->view->preInclusao = array (
								'nm_astd_pre' => $_POST['nm_astd_pre'],
								'cpf_pre' => $_POST['cpf_pre']
				);

				$this->render('familiaCadastroPreIncluir');				
			}
		} 

		if ($processa_insert == 1) {
			// Pesquisar para verificar se existem nome ou cpf na base
			if ($tem_cpf == 1) { 
				$pesqAtendAnterior = Container::getModel('TbFml');
				$pesqAtendAnterior->__set('nm_astd_prin', $trata_nome);
				$pesqAtendAnterior->__set('cpf', $trata_cpf);
				$pesqAtendAnteriorBase = $pesqAtendAnterior->getPreInclusaoFamilia2();
			} else {
				$pesqAtendAnterior = Container::getModel('TbFml');
				$pesqAtendAnterior->__set('nm_astd_prin', $trata_nome);
				$pesqAtendAnteriorBase = $pesqAtendAnterior->getPreInclusaoFamilia3();
			}

			if (!empty($pesqAtendAnteriorBase)) {
				// Buscar dados Famílias

				$this->view->familia = array ();

				foreach ($pesqAtendAnteriorBase as $index => $dadosPesq) {
					$dadosFamilia = Container::getModel('TbFml');
					$dadosFamilia->__set('codFamilia', $dadosPesq['cd_fmlID']);
					$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

					// Formatar para aparecer na tela o significado
					switch ($dadosFamiliaBase['cd_est_situ_fml'])
				    	{
				        case 1:
				            {
				               	$cd_est_situ_fml = 'Aguardando definição de grupo/subgrupo';
				                break;
				            }

				        case 2:
				            {
				               	$cd_est_situ_fml = 'Aguardando Triagem';
				                break;
				            }

				        case 3:
				            {
				               	$cd_est_situ_fml = 'Em atendimento pela DPS';
				                break;
				            }

				        case 4:
				            {
				               	$cd_est_situ_fml = 'Atendimento realizado e encerrado';
				                break;
				            }

				        case 5:
				            {
				               	$cd_est_situ_fml = 'Atendimento não realizado por impossibilidade triagem';
				                break;
				            }

				        case 6:
				            {
				               	$cd_est_situ_fml = 'Atendimento não realizado por família não necessitar';
				                break;
				            }
				        }

					array_push($this->view->familia, array (
									'cd_fml' => $dadosPesq['cd_fmlID'],
									'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
									'dt_inc_acomp' => $dadosFamiliaBase['dt_inc_acomp'],
									'dt_enct_acomp' => $dadosFamiliaBase['dt_enct_acomp'],
									'cd_est_situ_fml' => $cd_est_situ_fml,
									'nm_astd_pre' => $_POST['nm_astd_pre'],
									'cpf_pre' => $_POST['cpf_pre'],
									'rota' => $_POST['rota']
					));
				}

				$this->view->erroValidacao = 10;
				$this->render('familiaCadastroPreIncluirMenu');

			} else {
				// Não achou nada parecido e redireciona direto para Inclusão de Família

				$this->view->cpf_pre = $_POST['cpf_pre'];
				$this->view->nm_astd_pre = $_POST['nm_astd_pre'];
				$this->view->familia_atendida_anteriormente = '';

				$this->familiaCadastroIncluir();	

			}		
		}		
	}	// Fim da function familiaCadastroPreIncluir

// ================================================== //

	public function familiaCadastroPreIncluirIntegrantesMenu() {
	
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 10;
	
		$this->view->codGrupoEscolhido = '';
		$this->view->codSubgrupoEscolhido = '';
		$this->view->dtInc = '';
		$this->view->dtFim = '';
		
		// Buscar dados Integrante
		$dadosIntegrantesFamilia = Container::getModel('TbIntegFml');
		$dadosIntegrantesFamilia->__set('cd_fml', $_POST['cd_fml']);
		$dadosIntegrantesFmlBase = $dadosIntegrantesFamilia->getDadosIntegrantesAllFamilia();

		$this->view->integrantesFamilia = array ();

		foreach ($dadosIntegrantesFmlBase as $index => $dado) {
			// Formatar dados para exibição formatada na tela
			if (! empty($dado['cpf'])) {
				$cpf_formatado = Funcoes::formatarNumeros('cpf', $dado['cpf'], 11, "");
			} else {
				$cpf_formatado = $dado['cpf'];
			}

			if ($dado['cd_sexo'] == 1) {
				$sexo_formatado = 'masculino';
			} else {
				$sexo_formatado = 'feminino';
			}

			// AAAA-MM-DD para DD/MM/AAAA
			if (! empty($dado['dt_nasc'])) {
				$datanasc_formatado = Funcoes::formatarNumeros('data', $dado['dt_nasc'], 10, "AMD");
			} else {
				$datanasc_formatado = $dado['dt_nasc'];
			}

			if ($dado['cd_est_integ_fml'] == 1) {
				$est_integrante = 'Ativo';
			} else {
				$est_integrante = 'Inativo';
			}

			switch ($dado['cd_tip_incapacidade'])
		    	{
		        case 1:
		            {
		               	$cd_tip_incapacidade = 'Nenhuma Incapacidade';
		                break;
		            }

		        case 2:
		            {
		               	$cd_tip_incapacidade = 'Portador de Necessidade Especial';
		                break;
		            }

		        case 3:
		            {
		               	$cd_tip_incapacidade = 'Portado de Doença Incapacitante';
		                break;
		            }

		        case 4:
		            {
		               	$cd_tip_incapacidade = 'Outras';
		                break;
		            }

		    }

			switch ($dado['cd_rlc_com_astd_prin'])
		    	{
		        case 1:
		            {
		               	$cd_rlc_com_astd_prin = 'O(a) Próprio(a)';
		                break;
		            }

		        case 2:
		            {
		               	$cd_rlc_com_astd_prin = 'Conjugê';
		                break;
		            }

		        case 3:
		            {
		               	$cd_rlc_com_astd_prin = 'Filho(a)';
		                break;
		            }

		        case 4:
		            {
		               	$cd_rlc_com_astd_prin = 'Enteado(a)';
		                break;
		            }

		        case 5:
		            {
		               	$cd_rlc_com_astd_prin = 'Neto(a)';
		                break;
		            }

		        case 6:
		            {
		               	$cd_rlc_com_astd_prin = 'Sobrinho(a)';
		                break;
		            }
		        
		        case 7:
		            {
		               	$cd_rlc_com_astd_prin = 'Irmão(â)';
		                break;
		            }

		        case 8:
		            {
		               	$cd_rlc_com_astd_prin = 'Mãe';
		                break;
		            }

		        case 9:
		            {
		               	$cd_rlc_com_astd_prin = 'Pai';
		                break;
		            }

		        case 10:
		            {
		               	$cd_rlc_com_astd_prin = 'Tio(a)';
		                break;
		            }

		        case 11:
		            {
		               	$cd_rlc_com_astd_prin = 'Avô(ó)';
		                break;
		            }

		        case 12:
		            {
		               	$cd_rlc_com_astd_prin = 'Outros';
		                break;
		            }
		       }

			array_push($this->view->integrantesFamilia, array (
					'cd_fml' => $dado['cd_fmlID'], 
					'seql_integ' => $dado['seql_integID'], 
					'nm_grp_fmlr' => $dado['nm_grp_fmlr'], 
					'nm_integ' =>  $dado['nm_integ'],
					'cd_rlc_com_astd_prin' => $cd_rlc_com_astd_prin,
					'cpf' => $cpf_formatado,
					'dt_nasc' => $datanasc_formatado,
					'cd_sexo' => $sexo_formatado,
					'cd_tip_incapacidade' => $cd_tip_incapacidade,
					'cd_est_integ_fml' => $est_integrante,
					'rota' => $_POST['rota']
			));
		}

		$this->view->codFamilia = $_POST['cd_fml'];
		$this->view->nomeFamilia = $_POST['nm_grp_fmlr'];

		$this->view->nomeAssistidoPre = $_POST['nm_astd_pre'];
		$this->view->cpfPre = $_POST['cpf_pre'];

		// Precisa na hora que é chamado pelo "familiaCadastroConsultarMenu"		
		if ($_POST['rota'] == 'rota_02') {
			$this->view->codGrupoEscolhido = $_POST['cb_grupo_escolhido'];
			$this->view->codSubgrupoEscolhido = $_POST['cb_subgrupo_escolhido'];
			$this->view->dtInc = $_POST['dt_inc'];
			$this->view->dtFim = $_POST['dt_fim'];
		}

		$this->render('familiaCadastroPreIncluirIntegrantesMenu');

	}	// Fim da function familiaCadastroPreIncluirIntegrantesMenu

// ================================================== //

	// Criado para fazer pesquisa de família pelo nome na pesquisa
	public function familiaCadastroConsultaPesquisaMenu() {
		
		$this->validaAutenticacao();		

		$processa_insert = 1;

		if (empty($_POST['nome_grupo_fmlr'])) {
			$this->view->erroValidacao = 1;

			$processa_insert = 0;

			// Buscar todas as familias aptas a serem alteradas
			$familiasAll = Container::getModel('TbFml');
			$familiasBase = $familiasAll->getDadosFamiliaAll2();

			$this->view->familias = $familiasBase;

			$this->view->nomeGrupoFamiliar = '';			
			
			$this->render('familiaCadastroConsultar');
		} 

		$trata_nome = str_replace(" ", "%", $_POST['nome_grupo_fmlr']);

		if ($processa_insert == 1) {
			$pesqFamilia = Container::getModel('TbFml');
			$pesqFamilia->__set('nm_grp_fmlr', $trata_nome);
			$pesqFamiliaBase = $pesqFamilia->getPreInclusaoFamilia4();


			if (!empty($pesqFamiliaBase)) {
				// Buscar dados Famílias

				$this->view->familia = array ();

				foreach ($pesqFamiliaBase as $index => $dadosPesq) {
					$dadosFamilia = Container::getModel('TbFml');
					$dadosFamilia->__set('codFamilia', $dadosPesq['cd_fmlID']);
					$dadosFamiliaBase = $dadosFamilia->getDadosFamilia();

					// Formatar para aparecer na tela o significado
					switch ($dadosFamiliaBase['cd_est_situ_fml'])
				    	{
				        case 1:
				            {
				               	$cd_est_situ_fml = 'Aguardando definição de grupo/subgrupo';
				                break;
				            }

				        case 2:
				            {
				               	$cd_est_situ_fml = 'Aguardando Triagem';
				                break;
				            }

				        case 3:
				            {
				               	$cd_est_situ_fml = 'Em atendimento pela DPS';
				                break;
				            }

				        case 4:
				            {
				               	$cd_est_situ_fml = 'Atendimento realizado e encerrado';
				                break;
				            }

				        case 5:
				            {
				               	$cd_est_situ_fml = 'Atendimento não realizado por impossibilidade triagem';
				                break;
				            }

				        case 6:
				            {
				               	$cd_est_situ_fml = 'Atendimento não realizado por família não necessitar';
				                break;
				            }
				        }

					array_push($this->view->familia, array (
									'cd_fml' => $dadosPesq['cd_fmlID'],
									'nm_grp_fmlr' => $dadosFamiliaBase['nm_grp_fmlr'],
									'dt_inc_acomp' => $dadosFamiliaBase['dt_inc_acomp'],
									'dt_enct_acomp' => $dadosFamiliaBase['dt_enct_acomp'],
									'cd_est_situ_fml' => $cd_est_situ_fml,
									'nm_astd_pre' => $_POST['nome_grupo_fmlr'],
									'cpf_pre' => '',

									'rota' => 'rota_06'
									// 'rota' => $_POST['rota']
					));
				}

				$this->view->erroValidacao = 10;
				$this->render('familiaCadastroConsultaPesquisaMenu');

			} else {
				$this->view->erroValidacao = 3;

				// Buscar todas as familias aptas a serem alteradas
				$familiasAll = Container::getModel('TbFml');
				$familiasBase = $familiasAll->getDadosFamiliaAll2();

				$this->view->familias = $familiasBase;

				$this->view->nomeGrupoFamiliar = '';			
				
				$this->render('familiaCadastroConsultar');
			}		
		}		
	}	// Fim da function familiaCadastroConsultaPesquisaMenu



}	//	Fim da classe

?>
