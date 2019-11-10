<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class VoluntarioController extends Action {

// ================================================== //

	public function validaAutenticacao() {
		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || 
			!isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');	
		}
	}

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

// ====================================================== //	

	}	// Fim da function obtemDataProximaVisita

	public function voluntario() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;
		
		$this->render('voluntario');
	}

// ================================================== //

	public function voluntarioSenha() {

		$this->validaAutenticacao();

		$this->view->erroSenha = 7;
		
		$this->view->senha = array (
			'senhaAtual' => '',
			'senhaNova' => '',
			'senhaNovaRedigita' => ''
		);
		
		$this->render('voluntarioSenha');
	}

// ================================================== //

	public function voluntarioSenhaNova() {
		$this->validaAutenticacao();		
		
		// Pesquisar senha em tb_login_sess e verificar se está correta
		$senhaVoluntario = Container::getModel('TbCadLoginSess');
		$senhaVoluntario->__set('seql_cad_login', $_SESSION['seql_cad_login']);
		$senhaVoluntario->getSenha();
		
		// Para validar se senhas preenchidas - sem md5(), pois com md5() entende que tem valor
		$senhaVoluntario->__set('senhaAtualVerifica', $_POST['senhaAtual']);
		$senhaVoluntario->__set('senhaNovaVerifica', $_POST['senhaNova']);
		$senhaVoluntario->__set('senhaNovaRedigitaVerifica', $_POST['senhaNovaRedigita']);
		$valida = $senhaVoluntario->validarCadastro();
		
		if ($valida == 0) {
			// Comparação da senha atual com a senha da base
			if ($senhaVoluntario->__get('senhaBase') != md5($_POST['senhaAtual'])) {
				$this->view->erroSenha = 4;
				$this->render('voluntarioSenha');				
			} else {
				// Compara Senha Nova com a Senha Nova Confirmada
				if (md5($_POST['senhaNova']) != md5($_POST['senhaNovaRedigita'])) {
					$this->view->erroSenha = 5;
					$this->render('voluntarioSenha');
				} else {
					// Comparação da Senha Nova com a senha da base
					if (md5($_POST['senhaNova']) == $senhaVoluntario->__get('senhaBase')) {
						$this->view->erroSenha = 6;
						$this->render('voluntarioSenha');
					} else {
						// Gravar na base
						$senhaVoluntario->__set('seql_cad_login', $_SESSION['seql_cad_login']);
						$senhaVoluntario->__set('senhaNova', md5($_POST['senhaNova']));
						$senhaVoluntario->gravaNovaSenha();

						$this->view->erroSenha = 0;
						$this->render('voluntarioSenha');
						}
					}
			}
		} else {
			switch ($valida) {
				case 1:
					$this->view->erroSenha = 1;
					break;
				case 2:
					$this->view->erroSenha = 2;
					break;
				case 3:
					$this->view->erroSenha = 3;
					break;
				default:
					$this->view->erroSenha = 7;
					break;
			}
			
			$this->render('voluntarioSenha');
		}
	}	// Fim da funcition voluntarioSenhaNova

// ================================================== //

	public function voluntarioIncluir() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);
		
		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('voluntario');				
		} else {
			// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
			$regioesAdm = Container::getModel('TbRegAdm');
			$regioesBase = $regioesAdm->getDadosRAAll();

			$this->view->regioes = $regioesBase;

			$this->view->voluntario = array (
					    'nome' => '',
					    'nomeforum' => '',
					    'nrsocio' => '',
					    'nridentidade' => '',
					    'cpf' => '',
					    'dtnasc' => '',
					    'naturalidade' => '',
					    'estadoUF' => "",
					    'sexo' => 'feminino',
					    'fonecomercial' => '',
					    'foneresidencial' => '',
					    'celular' => '',
					    'email' => '',
					    'cep' => '',
					    'endereco' => '',
					    'ra_escolhida' => "",
					    'escolaridade' => "",
					    'profissao' => '',
					    'esde' => 'nao',
					    'descfaseesde' => '',
					    'diasemana' => '',
					    'horario' => '',
					    'outraatividade' => '',
					    'conhecimentoespecifico' => '',
					    'atividadepreferencia' => '',
					    'habilidades' => '',
					    'observacao' => ''
			);

			$this->view->erroValidacao = 10;
			$this->render('voluntarioIncluir');
		}

	}	// Fim da function voluntarioIncluir

// ================================================== //

	public function voluntarioIncluirBase() {
		
		$this->validaAutenticacao();		

		$cpf_preenchido = 0;
		$qtd_retorno_cpf   = 0;
		$qtd_retorno_email = 0;
		$mostra_retorno = 0;
		$this->view->erroValidacao = 0;
		$processa_insert = 1;

		$trata_cpf = str_replace(".", "", $_POST['cpf']);
		$trata_cpf = str_replace("-", "", $trata_cpf);

		$trata_cep = str_replace(".", "", $_POST['cep']);
		$trata_cep = str_replace("-", "", $trata_cep);

		$trata_fone_comercial = str_replace(" ", "", $_POST['fonecomercial']);
		$trata_fone_comercial = str_replace("(", "", $trata_fone_comercial);
		$trata_fone_comercial = str_replace(")", "", $trata_fone_comercial);
		$trata_fone_comercial = str_replace("-", "", $trata_fone_comercial);

		$trata_fone_residencial = str_replace(" ", "", $_POST['foneresidencial']);
		$trata_fone_residencial = str_replace("(", "", $trata_fone_residencial);
		$trata_fone_residencial = str_replace(")", "", $trata_fone_residencial);
		$trata_fone_residencial = str_replace("-", "", $trata_fone_residencial);

		$trata_fone_celular = str_replace(" ", "", $_POST['celular']);
		$trata_fone_celular = str_replace("(", "", $trata_fone_celular);
		$trata_fone_celular = str_replace(")", "", $trata_fone_celular);
		$trata_fone_celular = str_replace("-", "", $trata_fone_celular);

		if ($_POST['sexo'] == 'masculino') {
			$trata_sexo = 1;
		} else {
			$trata_sexo = 2;
		}
		
		if ($_POST['esde'] == 'sim') {
			$trata_esde = 'S';
		} else {
			$trata_esde = 'N';
		}

		// Validação do CPF
		if(! empty($_POST['cpf'])) {

			$cpf_preenchido = 1;

			$cpf_valida = Funcoes::validaCPF($trata_cpf);

			if ($cpf_valida == 3 || $cpf_valida == 4) {
				$processa_insert = 0;

				$this->view->erroValidacao = 1;

				$this->retornoValidacao();

				$this->render('voluntarioIncluir');				
			}
		}
		
		if ($cpf_preenchido == 1) {
			// Pesquisar Voluntário já não está na base
			$qtdVoluntario = Container::getModel('TbVlnt');

			// Tirei a pesquisa por nome, pois pode ter homônimo	

			$qtdVoluntario->__set('cpf_pesq', $trata_cpf);
			$qtd_retorno_cpf = $qtdVoluntario->getQtdVoluntario2();

			$qtdVoluntario->__set('email_pesq', $_POST['email']);
			$qtd_retorno_email = $qtdVoluntario->getQtdVoluntario3();
		} else {
			// Pesquisar email Voluntário já não está na base
			$qtdVoluntario = Container::getModel('TbVlnt');
			$qtdVoluntario->__set('email_pesq', $_POST['email']);
			$qtd_retorno_email = $qtdVoluntario->getQtdVoluntario3();
		}

		if ($qtd_retorno_cpf['qtde'] > 0 || $qtd_retorno_email['qtde'] > 0) {
			if ($qtd_retorno_cpf['qtde'] > 0) {
				$mostra_retorno = 3;
			}

			if ($qtd_retorno_email['qtde'] > 0) {
				$mostra_retorno = 4;
			}

			if ($qtd_retorno_cpf['qtde'] > 0 && $qtd_retorno_email['qtde'] > 0) {
				$mostra_retorno = 7;
			}

			$processa_insert = 0;

			$this->view->erroValidacao = $mostra_retorno;

			$this->retornoValidacao();

			$this->render('voluntarioIncluir');				
		} 

		// Validar se data nascimento válida
		$valida_data = Funcoes::ValidaData($_POST['dtnasc']);
		if ($valida_data == 0) {
			$processa_insert = 0;

			$this->view->erroValidacao = 2;

			$this->retornoValidacao();

			$this->render('voluntarioIncluir');				
		}

		if ($processa_insert == 1) {
			try {
				// Buscar o próximo número de Voluntário 
				$busca_prox_cd_vlnt = Container::getModel('TbVlnt');
				$busca_prox_cd_disp = $busca_prox_cd_vlnt->obtemProxCdVlnt();	
						
				if ($busca_prox_cd_disp['max_cd_vlnt'] == null) {
					$prox_cd_vlnt = 1;
				} else {
					$prox_cd_vlnt = $busca_prox_cd_disp['max_cd_vlnt'] + 1;
				}

				// Senha aleatória
				$senha_aleatoria = rand(6, 1000000);

				// Inserir na tabela tb_vlnt
				$insereVoluntario = Container::getModel('TbVlnt');
				$insereVoluntario->__set('cd_vlnt', $prox_cd_vlnt);
				$insereVoluntario->__set('nm_vlnt', $_POST['nome']);
				$insereVoluntario->__set('dt_nasc', $_POST['dtnasc']);
				$insereVoluntario->__set('dsc_natural', $_POST['naturalidade']);
				$insereVoluntario->__set('cd_sexo', $trata_sexo);
				$insereVoluntario->__set('nr_socio', $_POST['nrsocio']);
				$insereVoluntario->__set('dsc_end', $_POST['endereco']);
				$insereVoluntario->__set('cd_reg_adm', $_POST['ra_escolhida']);
				$insereVoluntario->__set('cep', $trata_cep);
				$insereVoluntario->__set('uf', $_POST['estadoUF']);
				$insereVoluntario->__set('nr_doc_ident', $_POST['nridentidade']);
				$insereVoluntario->__set('cpf', $trata_cpf);
				$insereVoluntario->__set('fone_rsdl', $trata_fone_residencial);
				$insereVoluntario->__set('fone_cmrl', $trata_fone_comercial);
				$insereVoluntario->__set('fone_cel', $trata_fone_celular);
				$insereVoluntario->__set('email', $_POST['email']);
				$insereVoluntario->__set('esde', $trata_esde);
				$insereVoluntario->__set('dsc_fase_ESDE', $_POST['descfaseesde']);
				$insereVoluntario->__set('dsc_dia_semana', $_POST['diasemana']);
				$insereVoluntario->__set('dsc_horario', $_POST['horario']);
				$insereVoluntario->__set('dsc_escolar', $_POST['escolaridade']);
				$insereVoluntario->__set('dsc_profissao', $_POST['profissao']);
				$insereVoluntario->__set('dsc_conhec_especif', $_POST['conhecimentoespecifico']);
				$insereVoluntario->__set('dsc_habilidade', $_POST['habilidades']);
				$insereVoluntario->__set('dsc_trab_vlnt_outro', $_POST['outraatividade']);
				$insereVoluntario->__set('dsc_obs', $_POST['observacao']);
				$insereVoluntario->__set('dsc_prefer_atvd_vlnt', $_POST['atividadepreferencia']);
				$insereVoluntario->__set('nm_vlnt_forum', $_POST['nomeforum']);
				$insereVoluntario->__set('cd_vlnt_resp_cadastro', $_SESSION['id']);
				$insereVoluntario->insertVoluntario();			

				// Inserir na tabela tb_cad_login_sess
				$insereCadLoginSess = Container::getModel('TbCadLoginSess');
				$insereCadLoginSess->__set('cd_vlnt', $prox_cd_vlnt);
				$insereCadLoginSess->__set('cd_nivel_ace_login', 3);
				$insereCadLoginSess->__set('senha', $senha_aleatoria);
				$insereCadLoginSess->__set('cd_situ_cad_login', 1);
				$insereCadLoginSess->insertCadLoginSess();
				
				// ATENÇÃO: Retirar estas 2 linhas abaixo e desasteriscar as outras qdo for para produção
				$envia_email = 0;
				$this->view->situacao_envio_email = "E-mail enviado ao voluntário";

				/*   
				// Remeter email
				$envia_email = Funcoes::enviaEmailCadastro($_POST['nome'], $_POST['email'], $senha_aleatoria);

				if ($envia_email == 0) {
					$this->view->situacao_envio_email = "E-mail enviado ao voluntário";	
				} else {
					$this->view->situacao_envio_email = "Erro no envio de E-mail ao voluntário";	
				}
				*/

				// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
				$regioesAdm = Container::getModel('TbRegAdm');
				$regioesBase = $regioesAdm->getDadosRAAll();

				$this->view->regioes = $regioesBase;

				$this->view->voluntario = array (
						    'nome' => '',
						    'nomeforum' => '',
						    'nrsocio' => '',
						    'nridentidade' => '',
						    'cpf' => '',
						    'dtnasc' => '',
						    'naturalidade' => '',
						    'estadoUF' => "",
						    'sexo' => '',
						    'fonecomercial' => '',
						    'foneresidencial' => '',
						    'celular' => '',
						    'email' => '',
						    'cep' => '',
						    'endereco' => '',
						    'ra_escolhida' => "",
						    'escolaridade' => "",
						    'profissao' => '',
						    'esde' => '',
						    'descfaseesde' => '',
						    'diasemana' => '',
						    'horario' => '',
						    'outraatividade' => '',
						    'conhecimentoespecifico' => '',
						    'atividadepreferencia' => '',
						    'habilidades' => '',
						    'observacao' => ''
				);

				$this->view->erroValidacao = 0;
				$this->view->nomeInclusao = $_POST['nome'];
				
				$this->render('voluntarioIncluir');
			
			} catch (Exception $e) {
				$this->view->erroValidacao = 9;
				$this->view->erroException = $e;

				$this->render('voluntarioIncluir');
			}
			
		}
	}	// Fim da function voluntarioIncluirBase

// ================================================== //

	public function voluntarioAlterar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 2;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('voluntario');				
		} else {
			// Buscar todos os voluntários da base
			$voluntariosAll = Container::getModel('TbVlnt');
			$voluntariosBase = $voluntariosAll->getDadosVoluntariosAll();

			$this->view->voluntarios = $voluntariosBase;

			$this->view->erroValidacao = 0;
			$this->render('voluntarioAlterar');
		}
		// */
	}	// Fim da function voluntarioAlterar

// ================================================== //

	public function voluntarioAlterarMenu() {
		
		$this->validaAutenticacao();		
	
		$this->view->erroValidacao = 0;

		// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();

		$this->view->regioes = $regioesBase;

		// Buscar o nome da RA e a uf
		$dadosVoluntario = Container::getModel('TbVlnt');
		$dadosVoluntario->__set('cd_vlnt', $_POST['voluntario_escolhido']);
		$dadosVoluntarioBase = $dadosVoluntario->getDadosVoluntario();

		// Formatar dados para exibição formatada na tela
		if ($dadosVoluntarioBase['cd_sexo'] == 1) {
			$sexo = 'masculino';
		} else {
			$sexo = 'feminino';
		}

		if ($dadosVoluntarioBase['esde'] == 1) {
			$esde = 'sim';
		} else {
			$esde = 'nao';
		}

		if (! empty($dadosVoluntarioBase['cpf'])) {
			$cpf_formatado = Funcoes::formatarNumeros('cpf', $dadosVoluntarioBase['cpf'], 11, "");
		} else {
			$cpf_formatado = $dadosVoluntarioBase['cpf'];
		}

		if (! empty($dadosVoluntarioBase['cep'])) {
			$cep_formatado = Funcoes::formatarNumeros('cep', $dadosVoluntarioBase['cep'], 8, "");
		} else {
			$cep_formatado = $dadosVoluntarioBase['cep'];
		}

		if (! empty($dadosVoluntarioBase['fone_cmrl'])) {
			$fonecomercial_formatado = Funcoes::formatarNumeros('fone', $dadosVoluntarioBase['fone_cmrl'], 10, "");
		} else {
			$fonecomercial_formatado = $dadosVoluntarioBase['fone_cmrl'];
		}

		if (! empty($dadosVoluntarioBase['fone_rsdl'])) {
			$foneresidencial_formatado = Funcoes::formatarNumeros('fone', $dadosVoluntarioBase['fone_rsdl'], 10, "");
		} else {
			$foneresidencial_formatado = $dadosVoluntarioBase['fone_rsdl'];
		}

		if (! empty($dadosVoluntarioBase['fone_cel'])) {
			$fonecelular_formatado = Funcoes::formatarNumeros('fone', $dadosVoluntarioBase['fone_cel'], 11, "");
		} else {
			$fonecelular_formatado = $dadosVoluntarioBase['fone_cel'];
		}

		// AAAA-MM-DD
		if (! empty($dadosVoluntarioBase['dt_nasc'])) {
			$datanasc_formatado = Funcoes::formatarNumeros('data', $dadosVoluntarioBase['dt_nasc'], 10, "AMD");
		} else {
			$datanasc_formatado = $dadosVoluntarioBase['dt_nasc'];
		}

		$this->view->voluntario = array (
			    'codigo' => $dadosVoluntarioBase['cd_vlntID'],
			    'nome' => $dadosVoluntarioBase['nm_vlnt'],
			    'nomeforum' => $dadosVoluntarioBase['nm_vlnt_forum'],
			    'nrsocio' => $dadosVoluntarioBase['nr_socio'],
			    'nridentidade' => $dadosVoluntarioBase['nr_doc_ident'],
			    'cpf' => $cpf_formatado, 									// $dadosVoluntarioBase['cpf'],
			    'dtnasc' => $datanasc_formatado,								// $dadosVoluntarioBase['dt_nasc'],
			    'naturalidade' => $dadosVoluntarioBase['dsc_natural'],
			    'estadoUF' => $dadosVoluntarioBase['uf'],
			    'sexo' => $sexo,
			    'fonecomercial' => $fonecomercial_formatado, 				// $dadosVoluntarioBase['fone_cmrl'], 
			    'foneresidencial' => $foneresidencial_formatado,			// $dadosVoluntarioBase['fone_rsdl'],
			    'celular' => $fonecelular_formatado,						// $dadosVoluntarioBase['fone_cel'],
			    'email' => $dadosVoluntarioBase['email'],
			    'cep' => $cep_formatado, 									// $dadosVoluntarioBase['cep'], 
			    'endereco' => $dadosVoluntarioBase['dsc_end'],
			    'ra_escolhida' => $dadosVoluntarioBase['cd_reg_adm'],
			    'escolaridade' => $dadosVoluntarioBase['dsc_escolar'],
			    'profissao' => $dadosVoluntarioBase['dsc_profissao'],
			    'esde' => $esde,
			    'descfaseesde' => $dadosVoluntarioBase['dsc_fase_ESDE'],
			    'diasemana' => $dadosVoluntarioBase['dsc_dia_semana'],
			    'horario' => $dadosVoluntarioBase['dsc_horario'],
			    'outraatividade' => $dadosVoluntarioBase['dsc_trab_vlnt_outro'],
			    'conhecimentoespecifico' => $dadosVoluntarioBase['dsc_conhec_especif'],
			    'atividadepreferencia' => $dadosVoluntarioBase['dsc_prefer_atvd_vlnt'],
			    'habilidades' => $dadosVoluntarioBase['dsc_habilidade'],
			    'observacao' => $dadosVoluntarioBase['dsc_obs'],
			    'quemChamou' => 'voluntario'
		);

		$this->render('voluntarioAlterarMenu');

		// */		
	}	// Fim da function voluntarioAlterarMenu

// ================================================== //

	public function voluntarioAlterarBase() {
		
		$this->validaAutenticacao();

		$cpf_preenchido = 0;
		$qtd_retorno_cpf   = 0;
		$qtd_retorno_email = 0;
		$mostra_retorno = 0;
		$this->view->erroValidacao = 0;
		$processa_update = 1;

		$trata_cpf = str_replace(".", "", $_POST['cpf']);
		$trata_cpf = str_replace("-", "", $trata_cpf);

		$trata_cep = str_replace(".", "", $_POST['cep']);
		$trata_cep = str_replace("-", "", $trata_cep);

		$trata_fone_comercial = str_replace(" ", "", $_POST['fonecomercial']);
		$trata_fone_comercial = str_replace("(", "", $trata_fone_comercial);
		$trata_fone_comercial = str_replace(")", "", $trata_fone_comercial);
		$trata_fone_comercial = str_replace("-", "", $trata_fone_comercial);

		$trata_fone_residencial = str_replace(" ", "", $_POST['foneresidencial']);
		$trata_fone_residencial = str_replace("(", "", $trata_fone_residencial);
		$trata_fone_residencial = str_replace(")", "", $trata_fone_residencial);
		$trata_fone_residencial = str_replace("-", "", $trata_fone_residencial);

		$trata_fone_celular = str_replace(" ", "", $_POST['celular']);
		$trata_fone_celular = str_replace("(", "", $trata_fone_celular);
		$trata_fone_celular = str_replace(")", "", $trata_fone_celular);
		$trata_fone_celular = str_replace("-", "", $trata_fone_celular);

		if ($_POST['sexo'] == 'masculino') {
			$trata_sexo = 1;
		} else {
			$trata_sexo = 2;
		}
		
		if ($_POST['esde'] == 'sim') {
			$trata_esde = 'S';
		} else {
			$trata_esde = 'N';
		}

		// Validação do CPF
		if(! empty($_POST['cpf'])) {

			$cpf_preenchido = 1;

			$cpf_valida = Funcoes::validaCPF($trata_cpf);

			if ($cpf_valida == 3 || $cpf_valida == 4) {
				$processa_update = 0;

				$this->view->erroValidacao = 1;

				$this->retornoValidacao();

				$this->render('voluntarioAlterarMenu');				
			}
		}
		
		if ($cpf_preenchido == 1) {
			// Pesquisar Voluntário já não está na base
			$qtdVoluntario = Container::getModel('TbVlnt');

			// Tirei a pesquisa por nome, pois pode ter homõnimo	

			$qtdVoluntario->__set('cd_vlnt_pesq', $_POST['codigo']);
			$qtdVoluntario->__set('cpf_pesq', $trata_cpf);
			$qtd_retorno_cpf = $qtdVoluntario->getQtdVoluntario4();

			$qtdVoluntario->__set('email_pesq', $_POST['email']);
			$qtd_retorno_email = $qtdVoluntario->getQtdVoluntario5();
		} else {
			// Pesquisar Voluntário já não está na base
			$qtdVoluntario = Container::getModel('TbVlnt');
			$qtdVoluntario->__set('cd_vlnt_pesq', $_POST['codigo']);
			$qtdVoluntario->__set('email_pesq', $_POST['email']);
			$qtd_retorno_email = $qtdVoluntario->getQtdVoluntario5();
		}

		if ($qtd_retorno_cpf['qtde'] > 0 || $qtd_retorno_email['qtde'] > 0) {
			if ($qtd_retorno_cpf['qtde'] > 0) {
				$mostra_retorno = 3;
			}

			if ($qtd_retorno_email['qtde'] > 0) {
				$mostra_retorno = 4;
			}

			if ($qtd_retorno_cpf['qtde'] > 0 && $qtd_retorno_email['qtde'] > 0) {
				$mostra_retorno = 7;
			}

			$processa_update = 0;

			$this->view->erroValidacao = $mostra_retorno;

			$this->retornoValidacao();

			$this->render('voluntarioAlterarMenu');				
		} 

		// Validar se data nascimento válida
		$valida_data = Funcoes::ValidaData($_POST['dtnasc']);
		if ($valida_data == 0) {
			$processa_update = 0;

			$this->view->erroValidacao = 2;

			if ($_POST['quemChamou'] == 'cadastro') {
				$this->retornoValidacao_1();
			} else {
				$this->retornoValidacao();
			}

			$this->render('voluntarioAlterarMenu');		
		}

		if ($processa_update == 1) {
			try {
				// Alterar na tabela tb_vlnt
				$alteraVoluntario = Container::getModel('TbVlnt');
				$alteraVoluntario->__set('cd_vlnt', $_POST['codigo']);
				$alteraVoluntario->__set('nm_vlnt', $_POST['nome']);
				$alteraVoluntario->__set('dt_nasc', $_POST['dtnasc']);
				$alteraVoluntario->__set('dsc_natural', $_POST['naturalidade']);
				$alteraVoluntario->__set('cd_sexo', $trata_sexo);
				$alteraVoluntario->__set('nr_socio', $_POST['nrsocio']);
				$alteraVoluntario->__set('dsc_end', $_POST['endereco']);
				$alteraVoluntario->__set('cd_reg_adm', $_POST['ra_escolhida']);
				$alteraVoluntario->__set('cep', $trata_cep);
				$alteraVoluntario->__set('uf', $_POST['estadoUF']);
				$alteraVoluntario->__set('nr_doc_ident', $_POST['nridentidade']);
				$alteraVoluntario->__set('cpf', $trata_cpf);
				$alteraVoluntario->__set('fone_rsdl', $trata_fone_residencial);
				$alteraVoluntario->__set('fone_cmrl', $trata_fone_comercial);
				$alteraVoluntario->__set('fone_cel', $trata_fone_celular);
				$alteraVoluntario->__set('email', $_POST['email']);
				$alteraVoluntario->__set('esde', $trata_esde);
				$alteraVoluntario->__set('dsc_fase_ESDE', $_POST['descfaseesde']);
				$alteraVoluntario->__set('dsc_dia_semana', $_POST['diasemana']);
				$alteraVoluntario->__set('dsc_horario', $_POST['horario']);
				$alteraVoluntario->__set('dsc_escolar', $_POST['escolaridade']);
				$alteraVoluntario->__set('dsc_profissao', $_POST['profissao']);
				$alteraVoluntario->__set('dsc_conhec_especif', $_POST['conhecimentoespecifico']);
				$alteraVoluntario->__set('dsc_habilidade', $_POST['habilidades']);
				$alteraVoluntario->__set('dsc_trab_vlnt_outro', $_POST['outraatividade']);
				$alteraVoluntario->__set('dsc_obs', $_POST['observacao']);
				$alteraVoluntario->__set('dsc_prefer_atvd_vlnt', $_POST['atividadepreferencia']);
				$alteraVoluntario->__set('nm_vlnt_forum', $_POST['nomeforum']);
				$alteraVoluntario->__set('cd_vlnt_resp_cadastro', $_SESSION['id']);
				$alteraVoluntario->updateVoluntario();			

				// Para mostrar no menu voluntario, quando for acionado por este menu
				$this->view->erroValidacao = 1;
				$this->view->codigoAlteracao = $_POST['codigo'];
				$this->view->nomeAlteracao = $_POST['nome'];

				// Buscar todos os voluntários da base
				$voluntariosAll = Container::getModel('TbVlnt');
				$voluntariosBase = $voluntariosAll->getDadosVoluntariosAll();

				$this->view->voluntarios = $voluntariosBase;

				if ($_POST['quemChamou'] == 'cadastro') {
					header('Location: /menuPrincipal'); 
				} else {
					$this->render('voluntarioAlterar');	
				}

			} catch (Exception $e) {
				$this->view->erroValidacao = 9;
				$this->view->erroException = $e;

				if ($_POST['quemChamou'] == 'cadastro') {
					header('Location: /menuPrincipal');
				} else {
					$this->render('voluntarioAlterar');	
				}
			}
			
		}
		// */
	}	// Fim da function voluntarioAlterarBase

	// */

// ================================================== //

	public function voluntarioConsultar() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 3;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('voluntario');				
		} else {
			// Buscar todos os voluntários da base
			$voluntariosAll = Container::getModel('TbVlnt');
			$voluntariosBase = $voluntariosAll->getDadosVoluntariosAll();

			$this->view->voluntarios = $voluntariosBase;

			$this->view->erroValidacao = 0;
			$this->render('voluntarioConsultar');
		}
	}	// Fim da function voluntarioConsultar


// ================================================== //

	public function voluntarioConsultarMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 0;

		// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();

		$this->view->regioes = $regioesBase;

		// Buscar o nome da RA e a uf
		$dadosVoluntario = Container::getModel('TbVlnt');
		$dadosVoluntario->__set('cd_vlnt', $_POST['voluntario_escolhido']);
		$dadosVoluntarioBase = $dadosVoluntario->getDadosVoluntario();

		// Formatar dados para exibição formatada na tela
		if ($dadosVoluntarioBase['cd_sexo'] == 1) {
			$sexo = 'masculino';
		} else {
			$sexo = 'feminino';
		}

		if ($dadosVoluntarioBase['esde'] == 1) {
			$esde = 'sim';
		} else {
			$esde = 'nao';
		}

		if (! empty($dadosVoluntarioBase['cpf'])) {
			$cpf_formatado = Funcoes::formatarNumeros('cpf', $dadosVoluntarioBase['cpf'], 11, "");
		} else {
			$cpf_formatado = $dadosVoluntarioBase['cpf'];
		}

		if (! empty($dadosVoluntarioBase['cep'])) {
			$cep_formatado = Funcoes::formatarNumeros('cep', $dadosVoluntarioBase['cep'], 8, "");
		} else {
			$cep_formatado = $dadosVoluntarioBase['cep'];
		}

		if (! empty($dadosVoluntarioBase['fone_cmrl'])) {
			$fonecomercial_formatado = Funcoes::formatarNumeros('fone', $dadosVoluntarioBase['fone_cmrl'], 10, "");
		} else {
			$fonecomercial_formatado = $dadosVoluntarioBase['fone_cmrl'];
		}

		if (! empty($dadosVoluntarioBase['fone_rsdl'])) {
			$foneresidencial_formatado = Funcoes::formatarNumeros('fone', $dadosVoluntarioBase['fone_rsdl'], 10, "");
		} else {
			$foneresidencial_formatado = $dadosVoluntarioBase['fone_rsdl'];
		}

		if (! empty($dadosVoluntarioBase['fone_cel'])) {
			$fonecelular_formatado = Funcoes::formatarNumeros('fone', $dadosVoluntarioBase['fone_cel'], 11, "");
		} else {
			$fonecelular_formatado = $dadosVoluntarioBase['fone_cel'];
		}

		// AAAA-MM-DD
		if (! empty($dadosVoluntarioBase['dt_nasc'])) {
			$datanasc_formatado = Funcoes::formatarNumeros('data', $dadosVoluntarioBase['dt_nasc'], 10, "AMD");
		} else {
			$datanasc_formatado = $dadosVoluntarioBase['dt_nasc'];
		}
		
		if (isset($_POST['rotaV'])) {
			$rotaV = $_POST['rotaV'];
		} else {
			$rotaV = 'cv';
		}

		$this->view->voluntario = array (
			    'codigo' => $dadosVoluntarioBase['cd_vlntID'],
			    'nome' => $dadosVoluntarioBase['nm_vlnt'],
			    'nomeforum' => $dadosVoluntarioBase['nm_vlnt_forum'],
			    'nrsocio' => $dadosVoluntarioBase['nr_socio'],
			    'nridentidade' => $dadosVoluntarioBase['nr_doc_ident'],
			    'cpf' => $cpf_formatado, 									// $dadosVoluntarioBase['cpf'],
			    'dtnasc' => $datanasc_formatado,								// $dadosVoluntarioBase['dt_nasc'],
			    'naturalidade' => $dadosVoluntarioBase['dsc_natural'],
			    'estadoUF' => $dadosVoluntarioBase['uf'],
			    'sexo' => $sexo,
			    'fonecomercial' => $fonecomercial_formatado, 				// $dadosVoluntarioBase['fone_cmrl'], 
			    'foneresidencial' => $foneresidencial_formatado,			// $dadosVoluntarioBase['fone_rsdl'],
			    'celular' => $fonecelular_formatado,						// $dadosVoluntarioBase['fone_cel'],
			    'email' => $dadosVoluntarioBase['email'],
			    'cep' => $cep_formatado, 									// $dadosVoluntarioBase['cep'], 
			    'endereco' => $dadosVoluntarioBase['dsc_end'],
			    'ra_escolhida' => $dadosVoluntarioBase['cd_reg_adm'],
			    'escolaridade' => $dadosVoluntarioBase['dsc_escolar'],
			    'profissao' => $dadosVoluntarioBase['dsc_profissao'],
			    'esde' => $esde,
			    'descfaseesde' => $dadosVoluntarioBase['dsc_fase_ESDE'],
			    'diasemana' => $dadosVoluntarioBase['dsc_dia_semana'],
			    'horario' => $dadosVoluntarioBase['dsc_horario'],
			    'outraatividade' => $dadosVoluntarioBase['dsc_trab_vlnt_outro'],
			    'conhecimentoespecifico' => $dadosVoluntarioBase['dsc_conhec_especif'],
			    'atividadepreferencia' => $dadosVoluntarioBase['dsc_prefer_atvd_vlnt'],
			    'habilidades' => $dadosVoluntarioBase['dsc_habilidade'],
			    'observacao' => $dadosVoluntarioBase['dsc_obs'],
			    'rotaV' => $rotaV		// cv = consulta voluntario / cvvgs = consulta vinculo voluntario grupo subgrupo

		);

		$this->render('voluntarioConsultarMenu');

		// */		
	}	// Fim da function voluntarioConsultarMenu
	
// ================================================== //

	public function voluntarioAlterarNAS() {
		
		$this->validaAutenticacao();		

		$nivel_acesso_requerido = 1;

		$autenticar_acesso = AuthController::verificaNivelAcesso($nivel_acesso_requerido);

		// Para validar se Voluntário tem o nível adequado para fazer a operação
		if ($autenticar_acesso['autorizado'] == 0) {
			$this->view->erroValidacao = 1;
			$this->view->nivelRequerido = $nivel_acesso_requerido;
			$this->view->nivelLogado = $autenticar_acesso['nivelVoluntario'];
			$this->render('voluntario');				
		} else {
			// Buscar todos os voluntários da base
			$voluntariosAll = Container::getModel('TbVlnt');
			$voluntariosBase = $voluntariosAll->getDadosVoluntariosAll();

			$this->view->voluntarios = $voluntariosBase;

			$this->view->erroValidacao = 0;
			$this->render('voluntarioAlterarNAS');
		}
		// */
	}	// Fim da function voluntarioAlterarNAS


// ================================================== //

	public function voluntarioAlterarNASMenu() {
		
		$this->validaAutenticacao();		
		
		$this->view->erroValidacao = 0;

		// Buscar o nome da RA e a uf
		$dadosVoluntario = Container::getModel('TbVlnt');
		$dadosVoluntario->__set('cd_vlnt', $_POST['voluntario_escolhido']);
		$dadosVoluntarioBase = $dadosVoluntario->getDadosVoluntarioNivel();

		// Formatar dados para exibição formatada na tela
		if ($dadosVoluntarioBase['nivel_login'] == 1) {
			$nivel_formatado = 'Administrador Geral';
		} else if ($dadosVoluntarioBase['nivel_login'] == 2) {
			$nivel_formatado = 'Administrador';
		} else if ($dadosVoluntarioBase['nivel_login'] == 3) {
			$nivel_formatado = 'Usuario';
		}

		$this->view->voluntario = array (
			    'codigo' => $_POST['voluntario_escolhido'],
			    'nome' => $dadosVoluntarioBase['nome_login'],
			    'nivelAcesso' => $nivel_formatado, 									
			    'nivelAcessoCompara' => $dadosVoluntarioBase['nivel_login'], 									
			    'sequencialLogin' => $dadosVoluntarioBase['seql_login'],
			    'email' => $dadosVoluntarioBase['email_login']
		);

		$this->render('voluntarioAlterarNASMenu');

		// */		
	}	// Fim da function voluntarioAlterarNASMenu

// ================================================== //

	public function voluntarioAlterarNASBase() {
		
		$this->validaAutenticacao();	

		$this->view->erroValidacao = 0;
		$processa_update = 1;
		$grava_senha = 0;
		$grava_nivel = 0;
		$soma_msg = 0;

		if ($_POST['nivelAcesso'] == 'Administrador Geral') {
			$trata_nivel = 1;
		} else  if ($_POST['nivelAcesso'] == 'Administrador'){
			$trata_nivel = 2;
		} else  if ($_POST['nivelAcesso'] == 'Usuario'){
			$trata_nivel = 3;
		}
		
		// Validação de senha
		if(! empty($_POST['senha']) || ! empty($_POST['senhaConfirma'])) {
			$grava_senha = 1;

			if ($_POST['senha'] != $_POST['senhaConfirma']) {
				$processa_update = 0;

				$this->view->erroValidacao = 1;

				$this->retornoValidacaoNAS();

				$this->render('voluntarioAlterarNASMenu');				
			} else {
				if ($trata_nivel != $_POST['nivelAcessoCompara']) {
					$grava_nivel = 1;
				}
			}
		} else {
			if ($trata_nivel == $_POST['nivelAcessoCompara']) {
				$processa_update = 0;

				$this->view->erroValidacao = 2;

				$this->retornoValidacaoNAS();

				$this->render('voluntarioAlterarNASMenu');				
			} else {
				$grava_nivel = 1;
			}
		}

		if ($processa_update == 1) {
			try {
				// Alterar na tabela tb_cad_login_sess
				if($grava_senha == 1) {
					$alteraCadLoginSessao = Container::getModel('TbCadLoginSess');
					$alteraCadLoginSessao->__set('seql_cad_login', $_POST['sequencialLogin']);
					$alteraCadLoginSessao->__set('senhaNova', md5($_POST['senha']));
					$alteraCadLoginSessao->gravaNovaSenha();			
					$soma_msg = $soma_msg + 1;
				}

				if($grava_nivel == 1) {
					$alteraCadLoginSessao = Container::getModel('TbCadLoginSess');
					$alteraCadLoginSessao->__set('seql_cad_login', $_POST['sequencialLogin']);
					$alteraCadLoginSessao->__set('cd_nivel_ace_login', $trata_nivel);
					$alteraCadLoginSessao->gravaNovoNivel();			
					$soma_msg = $soma_msg + 2;
				}

				$this->view->erroValidacao = $soma_msg;

				if ($grava_senha == 1) {

					// ATENÇÃO: Retirar estas 2 linhas abaixo e desasteriscar as outras qdo for para produção
					$envia_email = 0;
					$this->view->situacao_envio_email = "E-mail enviado ao voluntário";

					/*
					// Remeter email
					$envia_email = Funcoes::enviaEmailCadastro($_POST['nome'], $_POST['email'], $_POST['senha']);

					if ($envia_email == 0) {
						$this->view->situacao_envio_email = " - E-mail enviado ao voluntário";	
					} else {
						$this->view->situacao_envio_email = " - Erro no envio de E-mail ao voluntário";	
					}
					*/

				} else {
					$this->view->situacao_envio_email = "";	
				}
				

				$this->view->codigoAlteracao = $_POST['codigo'];
				$this->view->nomeAlteracao = $_POST['nome'];

				// Buscar todos os voluntários da base
				$voluntariosAll = Container::getModel('TbVlnt');
				$voluntariosBase = $voluntariosAll->getDadosVoluntariosAll();

				$this->view->voluntarios = $voluntariosBase;

				$this->render('voluntarioAlterarNAS');	


			} catch (Exception $e) {
				$this->view->erroValidacao = 9;
				$this->view->erroException = $e;

				$this->render('voluntarioAlterarNAS');	

			}
			
		}
		// */
	}	// Fim da function voluntarioAlterarNASBase

// ====================================================== //	
	
	public function retornoValidacao() {

		// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();
		
		$this->view->regioes = $regioesBase;

		if (isset($_POST['codigo'])) {
			$codigo = $_POST['codigo'];
		} else  {
			$codigo = '';
		}
		
		$this->view->voluntario = array (
			    'codigo' => $codigo,
			    'nome' => $_POST['nome'],
			    'nomeforum' => $_POST['nomeforum'],
			    'nrsocio' => $_POST['nrsocio'],
			    'nridentidade' => $_POST['nridentidade'],
			    'cpf' => $_POST['cpf'],
			    'dtnasc' => $_POST['dtnasc'],
			    'naturalidade' => $_POST['naturalidade'],
			    'estadoUF' => $_POST['estadoUF'],
			    'sexo' => $_POST['sexo'],
			    'fonecomercial' => $_POST['fonecomercial'],
			    'foneresidencial' => $_POST['foneresidencial'],
			    'celular' => $_POST['celular'],
			    'email' => $_POST['email'],
			    'cep' => $_POST['cep'],
			    'endereco' => $_POST['endereco'],
			    'ra_escolhida' => $_POST['ra_escolhida'],
			    'escolaridade' => $_POST['escolaridade'],
			    'profissao' => $_POST['profissao'],
			    'esde' => $_POST['esde'],
			    'descfaseesde' => $_POST['descfaseesde'],
			    'diasemana' => $_POST['diasemana'],
			    'horario' => $_POST['horario'],
			    'outraatividade' => $_POST['outraatividade'],
			    'conhecimentoespecifico' => $_POST['conhecimentoespecifico'],
			    'atividadepreferencia' => $_POST['atividadepreferencia'],
			    'habilidades' => $_POST['habilidades'],
			    'observacao' => $_POST['observacao'],
			    'quemChamou' => 'voluntario'
		);

	}	// Fim da function retornoValidacao

// ====================================================== //	

	public function retornoValidacao_1() {
		// Buscar todas as Regiões Administrativas vigentes e remeter array para montar o combobox
		$regioesAdm = Container::getModel('TbRegAdm');
		$regioesBase = $regioesAdm->getDadosRAAll();
		
		$this->view->regioes = $regioesBase;
		
		$this->view->voluntario = array (
			    'codigo' => $_POST['codigo'],
			    'nome' => $_POST['nome'],
			    'nomeforum' => $_POST['nomeforum'],
			    'nrsocio' => $_POST['nrsocio'],
			    'nridentidade' => $_POST['nridentidade'],
			    'cpf' => $_POST['cpf'],
			    'dtnasc' => $_POST['dtnasc'],
			    'naturalidade' => $_POST['naturalidade'],
			    'estadoUF' => $_POST['estadoUF'],
			    'sexo' => $_POST['sexo'],
			    'fonecomercial' => $_POST['fonecomercial'],
			    'foneresidencial' => $_POST['foneresidencial'],
			    'celular' => $_POST['celular'],
			    'email' => $_POST['email'],
			    'cep' => $_POST['cep'],
			    'endereco' => $_POST['endereco'],
			    'ra_escolhida' => $_POST['ra_escolhida'],
			    'escolaridade' => $_POST['escolaridade'],
			    'profissao' => $_POST['profissao'],
			    'esde' => $_POST['esde'],
			    'descfaseesde' => $_POST['descfaseesde'],
			    'diasemana' => $_POST['diasemana'],
			    'horario' => $_POST['horario'],
			    'outraatividade' => $_POST['outraatividade'],
			    'conhecimentoespecifico' => $_POST['conhecimentoespecifico'],
			    'atividadepreferencia' => $_POST['atividadepreferencia'],
			    'habilidades' => $_POST['habilidades'],
			    'observacao' => $_POST['observacao'],
			    'quemChamou' => 'cadastro'
		);

	}	// Fim da function retornoValidacao_1

// ====================================================== //	

	public function retornoValidacaoNAS() {
	
		$this->view->voluntario = array (
			    'codigo' => $_POST['codigo'],
			    'nome' => $_POST['nome'],
			   	'email' => $_POST['email'],
			    'nivelAcesso' => $_POST['nivelAcesso'], 									
			    'nivelAcessoCompara' => $_POST['nivelAcessoCompara'], 									
			    'sequencialLogin' => $_POST['sequencialLogin']
		);


	}	// Fim da function retornoValidacaoNAS

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
			$this->render('voluntario');				
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
			$this->render('voluntario');				
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
			$this->render('voluntario');				

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

// ====================================================== //	

	public function voluntarioPerfilAtuacaoMenu() {
		
		$this->validaAutenticacao();		

		// Busca Perfil de Atuacao com subgrupo preenchido
		$perfilAtuacao = Container::getModel('TbVlnt');
		$perfilAtuacao->__set('cd_vlnt', $_SESSION['id']);
		$perfilAtuacaoBase = $perfilAtuacao->getDadosVoluntarioAtuacao();

		$this->view->dadosPerfilVlnt = array ();

		foreach ($perfilAtuacaoBase as $index => $arr) {
			if ($index == 0) {
				$this->view->nomeVoluntario = $arr['nm_vlnt'];
				$this->view->nivelAcessoVoluntarioSistema = $arr['nivel_acesso_sistema'];
			}

			// Somente buscar data de Próxima Visita qdo houver subgrupo
			if (empty($arr['cd_sbgrp'])) {
				$dt_proxima_visita = '';

			} else {

				// Data Atual
				$dt_atual = new \DateTime();
				$dt_atual = $dt_atual->format("Y-m-d");

				// Retroagem um mês na data atual
				$periodo = new \Dateinterval("P1M");
				$dt_atual_m1 = new \DateTime();
				$dt_atual_m1->sub($periodo);
				$dt_atual_m1 = $dt_atual_m1->format("Y-m-d");

				// Calcular a Próxima Data Visita com um mês a menos
				$this->obtemDataProximaVisita($dt_atual_m1, $arr['cd_grp']);

				// Formata as datas para efeito de comparação de valor
				$ano_mes_da = str_replace('-','', $dt_atual);
				$ano_mes_pv = str_replace('-','', $this->prox_data_visita);

				// Se data da Próxima Visita calculada com data atual menos um mês é menor que a data atual, ou seja, já passou a data, calcula com a data atual normal
				if ($ano_mes_da > $ano_mes_pv) {
					$this->obtemDataProximaVisita($dt_atual, $arr['cd_grp']);
				} 

				$dt_proxima_visita = Funcoes::formatarNumeros('data', $this->prox_data_visita, 10, "AMD");	
			}		
			array_push($this->view->dadosPerfilVlnt, array (
					'cdNomeGrp' => $arr['cd_grp'].'-'.$arr['nm_grp'],
					'cdNomeSbgrp' => $arr['cd_sbgrp'].'-'.$arr['nm_sbgrp'],
					'cdAtuaVlntSbgrp' => $arr['cod_atuacao_grupoSubgrupo'],
					'cdNomeFml' => $arr['cd_fml'].'-'.$arr['nm_grp_fmlr'],
					'sitFml' => $arr['situacao_familia'],
					'proxDataTriagemVisita' => $dt_proxima_visita
			));
		}	

		$this->view->codVoluntario = $_SESSION['id'];

		$this->render('voluntarioPerfilAtuacaoMenu');

	}	// Fim da function voluntarioPerfilAtuacaoMenu


}	//	Fim da classe

?>
