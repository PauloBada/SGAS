<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Controller para opções do menu Cadastro do menu Principal
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

class RecFinanDPSRecursoController extends Action {

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

	public function recFinanDPSRecurso() {

		$this->validaAutenticacao();

		$this->view->erroApoio = 2;
		$this->view->erroValidacao = 10;
		$this->view->nivelRequerido = 2;
		$this->view->nivelLogado = 0;

		// AQUI - 

/*		
		// Buscar Dados da tabela tb_orc_dps
		$dadosOrcBase = Container::getModel('TbOrcDps');
		$dadosOrcBase->__set('cd_situ_recur_orc1', 1);  // 1-Orçamento com saldo
		$dadosOrcBase->__set('cd_situ_recur_orc2', 1);	 
		$dadosOrc = $dadosOrcBase->getDadosOrcDps2();
	
		$this->view->dadosORC = array ();

		if (count($dadosOrc) > 0) {
			foreach ($dadosOrc as $index => $arr) {

				// Cálculo dos dias restantes para glosagem do orçamento
				$data_hoje = new \DateTime();

				// Transforma a data em string
				$data_inicial = $data_hoje->format(DATE_RFC2822);

				$data_final = str_replace('/', '-', $arr['dt_venc_recur_orc_format']);

				// Calcula a diferença em segundos entre as datas
				$diferenca = strtotime($data_final) - strtotime($data_inicial);

				//Calcula a diferença em dias
				$dias = floor($diferenca / (60 * 60 * 24 + 1));

				if ($dias < 0) {
					// Alterar tb_orc_dps (Será glosado o vlr_sdo_recur_orc, para efeito de relatório) 
					// a) cd_situ_recur_orc => 4 (Orcamento Glosado) onde cd_situ_recur_orc = 1 (Orcamento com Saldo)

					// Alterar tb_vncl_orc_pedido 
					// cd_est_vncl => 3 (Glosado), onde:
					//    tb_pedido_recur_finan: 
					//    - cd_est_pedido = 3 (Autorizado) e cd_situ_envio_ressar_pedido = 1 (Nao Enviado) 
					//    tb_vncl_orc_pedido:
					//    - cd_est_vncl = 1 (Provisionado)

					// Alterar tb_pedido_recur_finan (similar ao cancelamento de solicitação de recurso por família)
					// cd_est_pedido => 5 (Aguardando nova autorização devido a recurso glosado), onde:
					//    - cd_est_pedido = 3 (Autorizado) e cd_situ_envio_ressar_pedido = 1 (Nao Enviado)
					//    tb_vncl_orc_pedido:
					//    - cd_est_vncl = 1 (Provisionado)
					
					// Caso esteja em tb_ressar_pedido_recur_finan
					// Cancelar solicitação de ressarcimento (igual a esse cancelamento)
					// cd_est_ressar => 5 (Cancelado)

				}

			}
		}

*/

		$this->render('recFinanDPSRecurso');
	}

// ================================================== //
	
	public function recFinanDPSRecursoVMR() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 
		
		$this->validaAcesso();

		if ($this->retornoValidaAcesso == 1) {
			$this->render('recFinanDPSRecurso');				

		} else {

			// Buscar Dados da tabela tb_vlr_minimo_ref_orc
			$dadosVMRBase = Container::getModel('TbVlrMinimoRefOrc');
			$dadosVMRBase->__set('cd_est_vlr_minimo_ref1', 1);  // 1-Vigente
			$dadosVMRBase->__set('cd_est_vlr_minimo_ref2', 2);	// 2-Não Vigente
			$dadosVMR = $dadosVMRBase->getDadosVMR2();
		
			$this->view->dadosVMR = array ();

			if (count($dadosVMR) > 0) {
				foreach ($dadosVMR as $index => $arr) {
					// Obter dados VMR
					$dadosVMREspecificoBase = Container::getModel('TbVlrMinimoRefOrc');
					$dadosVMREspecificoBase->__set('seql_vlr_minimo_ref', $arr['seql_vlr_minimo_ref']);	
					$dadosVMREspecifico = $dadosVMREspecificoBase->getDadosVMR();

					// Buscar Nome do Voluntário
					$nomeVlntBase = Container::getModel('TbVlnt');
					$nomeVlntBase->__set('id', $arr['cd_vlnt_resp_incl']);
					$nomeVlnt = $nomeVlntBase->getInfoVoluntario();

					$VMR = number_format($arr['vlr_minimo_ref'], 2, ',', '.');

					array_push($this->view->dadosVMR, array (
							'seql_vlr_minimo_ref' => $arr['seql_vlr_minimo_ref'],
							'vlr_minimo_ref' => $VMR,
							'dt_inc_vgc' => $arr['dt_inc_vgc_format'],
							'dt_fim_vgc' => $arr['dt_fim_vgc_format'],
							'obs' => $arr['obs'],
							'nm_vlnt_resp_incl' => $nomeVlnt['nm_vlnt'],
							'nm_est_vlr_minimo_ref' => $arr['nm_est_vlr_minimo_ref_format']
					));
				} 
			
			}
			
			$this->render('recFinanDPSRecursoVMR');

		}	
			
	}	// Fim function recFinanDPSRecursoVMR

// ================================================== //
	
	public function recFinanDPSRecursoVMRMenu() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		$data_hoje 		= 	new \DateTime();
		$data_hoje_f	= 	$data_hoje->format("d/m/Y");

		$this->view->infoVMR = array (
					'dt_inc_vgc' => $data_hoje_f,
					'obs' => '',
					'vlr_minimo_ref' => 0
		);

		$this->render('recFinanDPSRecursoVMRMenu');

	}	// Fim function recFinanDPSRecursoVMRMenu

// ================================================== //
	
	public function recFinanDPSRecursoVMRBase() {

		$this->validaAutenticacao();		

		// Fomatar valor para inclusão
		$vlr_minimo_ref = str_replace('.','', $_POST['vlr_minimo_ref']);
		$vlr_minimo_ref = str_replace(',','.', $vlr_minimo_ref);

		// Verifica se já há outro valor na base, e pega o mais recente
		$seqlMax = Container::getModel('TbVlrMinimoRefOrc');
		$seqlMax->getSequencial();			

		// Há Valor Mínimo já cadastrado
		if (!empty($seqlMax->__get('seql_max'))) {
			
			// Para obter data fim de vigência, pois pode ter havido uma inclusão no mesmo dia
			$dt_fim_vgc	=	new \DateTime();
			
			// Subtrai um dia
			$periodo = new \Dateinterval("P1D");
			$dt_fim_vgc->sub($periodo);
			$dt_fim_vgc_f	= 	$dt_fim_vgc->format("d/m/Y");

			// Para data ficar AAAAMMDD
			$data_hoje 	= 	new \DateTime();
			$data_hoje1 = $data_hoje->format('Y-m-d');
			$data_hoje1 = explode('-', $data_hoje1);
			$data_hoje1 = implode("", $data_hoje1);

			$dt_fim_vgc1 = $dt_fim_vgc->format('Y-m-d');
			$dt_fim_vgc1 = explode('-', $dt_fim_vgc1);
			$dt_fim_vgc1 = implode("", $dt_fim_vgc1);

			// Se Data Fim Vigência for menor que a data atual, fica a data atual como fim de vigência
			if ($data_hoje1 > $dt_fim_vgc1) {
				$dt_fim_vgc	=	new \DateTime();	
				$dt_fim_vgc_f	= 	$dt_fim_vgc->format("d/m/Y");
			}

			// Obtém valor mínimo anterior e comparar com o atual, para ver se não são iguais
			$obtemDadosVMRBase = Container::getModel('TbVlrMinimoRefOrc');
			$obtemDadosVMRBase->__set('seql_vlr_minimo_ref', $seqlMax->__get('seql_max'));
			$obtemDadosVMR = $obtemDadosVMRBase->getDadosVMR();

			// Testar se valor atual é igual ao valor impostado
			if ($vlr_minimo_ref != $obtemDadosVMR['vlr_minimo_ref']) {
				// Altera cd_est_vlr_minimo_ref e data_fim_vgc do registro anterior
				$alteraVMR = Container::getModel('TbVlrMinimoRefOrc');
				$alteraVMR->__set('seql_vlr_minimo_ref', $seqlMax->__get('seql_max'));
				$alteraVMR->__set('dt_fim_vgc', $dt_fim_vgc_f);
				$alteraVMR->updateVMR();

				// Insere na tabela tb_vlr_minimo_ref_orc
				$insereVMR = Container::getModel('TbVlrMinimoRefOrc');
				$insereVMR->__set('vlr_minimo_ref', $vlr_minimo_ref);
				$insereVMR->__set('obs', $_POST['obs']);
				$insereVMR->__set('cd_vlnt_resp_incl', $_SESSION['id']);
				$insereVMR->insertVMR();

				$this->view->erroValidacao = 2;

			} else {
				$this->view->erroValidacao = 3;
			}

		} else {
			// Insere na tabela tb_vlr_minimo_ref_orc
			$insereVMR = Container::getModel('TbVlrMinimoRefOrc');
			$insereVMR->__set('vlr_minimo_ref', $vlr_minimo_ref);
			$insereVMR->__set('obs', $_POST['obs']);
			$insereVMR->__set('cd_vlnt_resp_incl', $_SESSION['id']);
			$insereVMR->insertVMR();

			$this->view->erroValidacao = 2;			
		}

		session_write_close();
		$this->recFinanDPSRecursoVMR();

	}	// Fim function recFinanDPSRecursoVMRBase

// ================================================== //
	
	public function recFinanDPSRecursoReal() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 
		
		$this->validaAcesso();

		if ($this->retornoValidaAcesso == 1) {
			$this->render('recFinanDPSRecurso');				

		} else {

			$this->view->saldoORC = 0;	

			// Buscar Dados da tabela tb_orc_dps
			$dadosOrcBase = Container::getModel('TbOrcDps');
			$dadosOrcBase->__set('cd_situ_recur_orc1', 1);  // 1-Orçamento com saldo
			$dadosOrcBase->__set('cd_situ_recur_orc2', 1);	 
			$dadosOrc = $dadosOrcBase->getDadosOrcDps2();
		
			$this->view->dadosORC = array ();

			if (count($dadosOrc) > 0) {
				// Obtem o saldo Atual de Recursos
				$saldoOrcBase = Container::getModel('TbOrcDps');
				$saldoOrc = $saldoOrcBase->getSaldoOrcDps();

				$this->view->saldoORC = $saldoOrc['saldo_atual_orc'];	
				$this->view->saldoORC = number_format($this->view->saldoORC, 2, ',', '.');				

				foreach ($dadosOrc as $index => $arr) {
					// Obter dados ORC
					$dadosOrcEspecificoBase = Container::getModel('TbOrcDps');
					$dadosOrcEspecificoBase->__set('seql_orc', $arr['seql_orc']);	
					$dadosOrcEspecifico = $dadosOrcEspecificoBase->getDadosOrcDps();

					// Buscar Nome do Voluntário
					$nomeVlntBase = Container::getModel('TbVlnt');
					$nomeVlntBase->__set('id', $arr['cd_vlnt_resp_incl']);
					$nomeVlnt = $nomeVlntBase->getInfoVoluntario();

					$vlr_recur_orc = number_format($arr['vlr_recur_orc'], 2, ',', '.');
					$vlr_sdo_recur_orc = number_format($arr['vlr_sdo_recur_orc'], 2, ',', '.');

					// Cálculo dos dias restantes para glosagem do orçamento
					$data_hoje = new \DateTime();

					// Transforma a data em string
					$data_inicial = $data_hoje->format(DATE_RFC2822);

					$data_final = str_replace('/', '-', $arr['dt_venc_recur_orc_format']);

					// Calcula a diferença em segundos entre as datas
					$diferenca = strtotime($data_final) - strtotime($data_inicial);

					//Calcula a diferença em dias
					$dias = floor($diferenca / (60 * 60 * 24) + 1);

					array_push($this->view->dadosORC, array (
							'seql_orc' => $arr['seql_orc'],
							'dt_recur_orc' => $arr['dt_recur_orc_format'],
							'dt_venc_recur_orc' => $arr['dt_venc_recur_orc_format'],
							'vlr_recur_orc' => $vlr_recur_orc,							
							'vlr_sdo_recur_orc' => $vlr_sdo_recur_orc,							
							'obs' => $arr['obs'],
							'nm_vlnt_resp_incl' => $nomeVlnt['nm_vlnt'],
							'dias_para_glosa' => $dias
					));
				} 

				$this->view->temRecurso = 1;

			} else {
				$this->view->temRecurso = 0;
			}
			
			$this->render('recFinanDPSRecursoReal');

		}	
			
	}	// Fim function recFinanDPSRecursoReal

// ================================================== //
	
	public function recFinanDPSRecursoRealMenu() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		$data_hoje 		= 	new \DateTime();
		$data_hoje_f	= 	$data_hoje->format("d/m/Y");

		$this->view->infoORC = array (
					'dt_recur_orc' => $data_hoje_f,
					'obs' => '',
					'vlr_recur_orc' => 0
		);

		$this->render('recFinanDPSRecursoRealMenu');

	}	// Fim function recFinanDPSRecursoRealMenu


// ================================================== //
	
	public function recFinanDPSRecursoRealIncluiBase() {

		$this->validaAutenticacao();		

		// Fomatar valor para inclusão
		$vlr_recur_orc = str_replace('.','', $_POST['vlr_recur_orc']);
		$vlr_recur_orc = str_replace(',','.', $vlr_recur_orc);

		// Calcular três meses para frente para dt_venc_recur_orc
		$data_orc_format = str_replace('/', '-', $_POST['dt_recur_orc']);
		$data_orc = new \DateTime($data_orc_format);		
		$data_orc = $data_orc->format("d/m/Y");
		
		$dataVencOrc = Funcoes::CalculaDataPeriodo($data_orc, 3, 'M', 'A');	

		if (strlen($dataVencOrc) == 1) {
			$this->view->erroValidacao = 3;			
		
		} else {
			// Insere na tabela tb_orc_dps
			$insereORC = Container::getModel('TbOrcDps');
			$insereORC->__set('dt_recur_orc', $_POST['dt_recur_orc']);
			$insereORC->__set('dt_venc_recur_orc', $dataVencOrc);
			$insereORC->__set('vlr_recur_orc', $vlr_recur_orc);
			$insereORC->__set('vlr_sdo_recur_orc', $vlr_recur_orc);
			$insereORC->__set('cd_situ_recur_orc', 1);		// Orçamento com saldo
			$insereORC->__set('cd_vlnt_resp_incl', $_SESSION['id']);
			$insereORC->__set('obs', $_POST['obs']);		
			$insereORC->insertOrcDps();

			$this->view->erroValidacao = 2;			
		}

		session_write_close();
		$this->recFinanDPSRecursoReal();

	}	// Fim function recFinanDPSRecursoRealIncluiBase

// ================================================== //
	
	public function recFinanDPSRecursoRealGerencia() {

		$this->validaAutenticacao();		

		if (!isset($this->view->erroValidacao)) {
			$this->view->erroValidacao = 0;
		} 

		$altera_dados = 1;

		// Obter dados ORC
		$dadosOrcEspecificoBase = Container::getModel('TbOrcDps');
		$dadosOrcEspecificoBase->__set('seql_orc', $_POST['seql_orc']);	
		$dadosOrcEspecifico = $dadosOrcEspecificoBase->getDadosOrcDps();

		if ($dadosOrcEspecifico['vlr_recur_orc'] != $dadosOrcEspecifico['vlr_sdo_recur_orc']) {
			$altera_dados = 0;
		}

		$this->view->infoORC = array (
			'seql_orc' => $_POST['seql_orc'],
			'dt_recur_orc' => $dadosOrcEspecifico['dt_recur_orc_format'],
			'obs' => $dadosOrcEspecifico['obs'],
			'vlr_recur_orc' => $dadosOrcEspecifico['vlr_recur_orc'],
			'vlr_sdo_recur_orc' => $dadosOrcEspecifico['vlr_sdo_recur_orc'],
			'altera_dados' => $altera_dados
		);

		$this->render('recFinanDPSRecursoRealGerencia');

	}	// Fim function recFinanDPSRecursoRealGerencia

// ================================================== //
	
	public function recFinanDPSRecursoRealGerenciaAlteraBase() {

		$this->validaAutenticacao();		

		// Obter dados ORC
		$dadosOrcEspecificoBase = Container::getModel('TbOrcDps');
		$dadosOrcEspecificoBase->__set('seql_orc', $_POST['seql_orc']);	
		$dadosOrcEspecifico = $dadosOrcEspecificoBase->getDadosOrcDps();

		// Não houve utilização do recurso ainda
		if ($dadosOrcEspecifico['vlr_recur_orc'] == $dadosOrcEspecifico['vlr_sdo_recur_orc']) {

			// Fomatar valor para inclusão
			$vlr_recur_orc = str_replace('.','', $_POST['vlr_recur_orc']);
			$vlr_recur_orc = str_replace(',','.', $vlr_recur_orc);

			// Calcular três meses para frente para dt_venc_recur_orc
			$data_orc_format = str_replace('/', '-', $_POST['dt_recur_orc']);
			$data_orc = new \DateTime($data_orc_format);		
			$data_orc = $data_orc->format("d/m/Y");
			
			$dataVencOrc = Funcoes::CalculaDataPeriodo($data_orc, 3, 'M', 'A');	

			// Altera Dados na tabela
			$alteraOrc = Container::getModel('TbOrcDps');
			$alteraOrc->__set('seql_orc', $_POST['seql_orc']);	
			$alteraOrc->__set('dt_recur_orc', $_POST['dt_recur_orc']);	
			$alteraOrc->__set('dt_venc_recur_orc', $dataVencOrc);	
			$alteraOrc->__set('vlr_recur_orc', $vlr_recur_orc);	
			$alteraOrc->__set('vlr_sdo_recur_orc', $vlr_recur_orc);	
			$alteraOrc->__set('obs', $_POST['obs']);	
			$alteraOrc->updateValorObsOrcDps();

			$this->view->erroValidacao = 4;						

			session_write_close();
			$this->recFinanDPSRecursoReal();

		} else {

			$this->view->erroValidacao = 3;			

			session_write_close();
			$this->recFinanDPSRecursoRealGerencia();

		}

	}	// Fim function recFinanDPSRecursoRealGerenciaAlteraBase

// ================================================== //
	
	public function recFinanDPSRecursoRealGerenciaCancelaBase() {

		$this->validaAutenticacao();		

		// Obter dados ORC
		$dadosOrcEspecificoBase = Container::getModel('TbOrcDps');
		$dadosOrcEspecificoBase->__set('seql_orc', $_POST['seql_orc']);	
		$dadosOrcEspecifico = $dadosOrcEspecificoBase->getDadosOrcDps();

		// Não houve utilização do recurso ainda
		if ($dadosOrcEspecifico['vlr_recur_orc'] == $dadosOrcEspecifico['vlr_sdo_recur_orc']) {

			// Altera Dados na tabela
			$alteraOrc = Container::getModel('TbOrcDps');
			$alteraOrc->__set('seql_orc', $_POST['seql_orc']);	
			$alteraOrc->__set('cd_situ_recur_orc', 3);	 // Orçamento Cancelado
			$alteraOrc->updateSituacaoOrcDps();

			$this->view->erroValidacao = 5;						

			session_write_close();
			$this->recFinanDPSRecursoReal();

		} else {

			$this->view->erroValidacao = 4;			

			session_write_close();
			$this->recFinanDPSRecursoRealGerencia();

		}

	}	// Fim function recFinanDPSRecursoRealGerenciaCancelaBase




}	//	Fim da classe

?>
				