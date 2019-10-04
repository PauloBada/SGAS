<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbIntegFml extends Model {
	
	// colunas da tabela
	private $cd_fmlID;             
	private $seql_integID;             
	private $nm_integ;          
	private $cd_rlc_com_astd_prin;
	private $cpf;
	private $nr_doc_ident;
	private $dt_nasc;         
	private $cd_sexo;
	private $cd_situ_estudo;              
	private $cd_escolar;        
	private $cd_situ_trab;           
	private $dsc_atvd_atual;       
	private $cd_tip_incapacidade;       
	private $dt_inc_integ;  
	private $dt_term_integ;         
	private $cd_est_integ_fml;    

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //

	public function insertIntegranteFamilia() {

		// Para gravar nulo nos campos quando não houver informação
		if (empty($this->__get('cpf'))) {
			$cpf = null;
		} else {
			$cpf = $this->__get('cpf');
		}

		if (empty($this->__get('nr_doc_ident'))) {
			$nr_doc_ident = null;
		} else {
			$nr_doc_ident = $this->__get('nr_doc_ident');
		}

		if (empty($this->__get('dt_nasc'))) {
			$dt_nasc = null;
		} else {
			$dt_nasc = $this->__get('dt_nasc');
		}

		if (empty($this->__get('dsc_atvd_atual'))) {
			$dsc_atvd_atual = null;
		} else {
			$dsc_atvd_atual = $this->__get('dsc_atvd_atual');
		}

		$this->getProximoSequencial();

		$query = "
				insert into tb_integ_fml
				(cd_fmlID,
				seql_integID,             
				nm_integ,          
				cd_rlc_com_astd_prin,
				cpf,
				nr_doc_ident,
				dt_nasc,         
				cd_sexo,
				cd_situ_estudo,              
				cd_escolar,        
				cd_situ_trab,           
				dsc_atvd_atual,       
				cd_tip_incapacidade,       
				dt_inc_integ,  
				dt_term_integ,         
				cd_est_integ_fml) 

				values 

				(:cd_fml,
				:seql_integ,             
				:nm_integ,          
				:cd_rlc_com_astd_prin,
				:cpf,
				:nr_doc_ident,
				str_to_date(:dt_nasc, '%d/%m/%Y'),   
				:cd_sexo,
				:cd_situ_estudo,              
				:cd_escolar,        
				:cd_situ_trab,           
				:dsc_atvd_atual,       
				:cd_tip_incapacidade,       
				now(),  
				str_to_date('31/12/9998', '%d/%m/%Y'),
				:cd_est_integ_fml)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_integ', $this->__get('seql_max'));			
		$stmt->bindValue('nm_integ', $this->__get('nm_integ'));
		$stmt->bindValue('cd_rlc_com_astd_prin', $this->__get('cd_rlc_com_astd_prin'));
		$stmt->bindValue('cpf', $cpf);
		$stmt->bindValue('nr_doc_ident', $nr_doc_ident);
		$stmt->bindValue('dt_nasc', $dt_nasc);
		$stmt->bindValue('cd_sexo', $this->__get('cd_sexo'));
		$stmt->bindValue('cd_situ_estudo', $this->__get('cd_situ_estudo'));
		$stmt->bindValue('cd_escolar', $this->__get('cd_escolar'));
		$stmt->bindValue('cd_situ_trab', $this->__get('cd_situ_trab'));
		$stmt->bindValue('dsc_atvd_atual', $dsc_atvd_atual);
		$stmt->bindValue('cd_tip_incapacidade', $this->__get('cd_tip_incapacidade'));
		$stmt->bindValue('cd_est_integ_fml', 1);
		$stmt->execute();

		return $this;

	}	// Fim function insertIntegranteFamilia

// =================================================== //

	public function getProximoSequencial() {
		$query0 = "select count(*) + 1 as qtde
					from  tb_integ_fml
					where cd_fmlID    = :cd_fml";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "select max(seql_integID) + 1 as qtde
					from  tb_integ_fml
					where cd_fmlID    = :cd_fml";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue('cd_fml', $this->__get('cd_fml'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_max', $nr_registros_1['qtde']);
		}
	}

// ====================================================== //

	public function updateIntegranteFamilia() {
		$query = "
				update tb_integ_fml
				set    nm_integ = :nm_integ
				where  cd_fmlID = :cd_fml
				and    seql_integID = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('nm_integ', $this->__get('nm_integ'));
		$stmt->execute();

		return $this;	
	}	// Fim function updateIntegranteFamilia

// ====================================================== //

	public function getQtdCPFIntegrante() {
		$query = "
				select count(*) as qtde
				from tb_integ_fml 
				where cpf = :cpf
				and   cd_fmlID in (select cd_fmlID
			                       from  tb_fml
			                       where cd_est_situ_fml between 1 and 3)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cpf', $this->__get('cpf'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getQtdNomeIntegrante() {
		$query = "
				select count(*) as qtde
				from tb_integ_fml 
				where cd_fmlID = :cd_fml
				and   nm_integ = :nm_integ";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('nm_integ', $this->__get('nm_integ'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getQtdNomeIntegrante2() {
		$query = "
				select count(*) as qtde
				from tb_integ_fml 
				where cd_fmlID 	   = :cd_fml
				and   seql_integID <> :seql_integ
				and   nm_integ     = :nm_integ";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_integ', $this->__get('seql_integ'));
		$stmt->bindValue('nm_integ', $this->__get('nm_integ'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getDadosIntegranteFamilia() {
		$query = "
				select a.*,
					   b.nm_grp_fmlr
				from tb_integ_fml a,
					 tb_fml b
				where a.cd_fmlID     = :cd_fml
				and   a.seql_integID = :seql_integ
				and   a.cd_fmlID     = b.cd_fmlID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':seql_integ', $this->__get('seql_integ'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function updateIntegranteFamilia2() {

		// Para gravar nulo nos campos quando não houver informação
		if (empty($this->__get('cpf'))) {
			$cpf = null;
		} else {
			$cpf = $this->__get('cpf');
		}

		if (empty($this->__get('nr_doc_ident'))) {
			$nr_doc_ident = null;
		} else {
			$nr_doc_ident = $this->__get('nr_doc_ident');
		}

		if (empty($this->__get('dt_nasc'))) {
			$dt_nasc = null;
		} else {
			$dt_nasc = $this->__get('dt_nasc');
		}

		if (empty($this->__get('dsc_atvd_atual'))) {
			$dsc_atvd_atual = null;
		} else {
			$dsc_atvd_atual = $this->__get('dsc_atvd_atual');
		}

		$query = "
				update tb_integ_fml
				set nm_integ				=	:nm_integ,          
				 	cd_rlc_com_astd_prin	=	:cd_rlc_com_astd_prin,
					cpf 					=	:cpf,
					nr_doc_ident			=	:nr_doc_ident,
					dt_nasc                 =   str_to_date(:dt_nasc, '%d/%m/%Y'),   
					cd_sexo					=	:cd_sexo,
					cd_situ_estudo			=	:cd_situ_estudo,              
					cd_escolar				=	:cd_escolar,        
					cd_situ_trab			= 	:cd_situ_trab,           
					dsc_atvd_atual			=	:dsc_atvd_atual,       
					cd_tip_incapacidade		=	:cd_tip_incapacidade
				where cd_fmlID     = :cd_fml
				and   seql_integID = :seql_integ";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_integ', $this->__get('seql_integ'));			
		$stmt->bindValue('nm_integ', $this->__get('nm_integ'));
		$stmt->bindValue('cd_rlc_com_astd_prin', $this->__get('cd_rlc_com_astd_prin'));
		$stmt->bindValue('cpf', $cpf);
		$stmt->bindValue('nr_doc_ident', $nr_doc_ident);
		$stmt->bindValue('dt_nasc', $dt_nasc);
		$stmt->bindValue('cd_sexo', $this->__get('cd_sexo'));
		$stmt->bindValue('cd_situ_estudo', $this->__get('cd_situ_estudo'));
		$stmt->bindValue('cd_escolar', $this->__get('cd_escolar'));
		$stmt->bindValue('cd_situ_trab', $this->__get('cd_situ_trab'));
		$stmt->bindValue('dsc_atvd_atual', $dsc_atvd_atual);
		$stmt->bindValue('cd_tip_incapacidade', $this->__get('cd_tip_incapacidade'));
		$stmt->execute();

		return $this;

	}	// Fim function updateIntegranteFamilia2

// ====================================================== //

	public function updateIntegranteFamilia3() {
		$query = "
				update tb_integ_fml
				set    cd_est_integ_fml = 2,
					   dt_term_integ = now()
				where  cd_fmlID     = :cd_fml
				and    seql_integID = :seql_integ";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':seql_integ', $this->__get('seql_integ'));
		$stmt->execute();

		return $this;	
	}	// Fim function updateIntegranteFamilia3

// ====================================================== //

	public function getDadosIntegrantesAllFamilia() {
		$query = "
				select a.*,
					   b.nm_grp_fmlr
				from tb_integ_fml a,
					 tb_fml b
				where a.cd_fmlID     = :cd_fml
				and   a.cd_fmlID     = b.cd_fmlID
				order by cd_rlc_com_astd_prin";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getCPFIntegrante4() {
		$query = "
				select max(cd_fmlID) as maxFamilia
				from tb_integ_fml 
				where cpf = :cpf
				and   cd_fmlID in (select cd_fmlID
			                       from  tb_fml
			                       where cd_est_situ_fml = 4)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cpf', $this->__get('cpf'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getQtdIntegrantes() {
		$query = "
				select count(*) as qtde
				from  tb_integ_fml 
				where cd_fmlID 	       = :cd_fml
				and   cd_est_integ_fml = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getQtdIntegrantesMenor7Anos() {
		$query = "
				select count(*) as qtde
				from  tb_integ_fml 
				where cd_fmlID 	       = :cd_fml
				and   cd_est_integ_fml = 1
				and   dt_nasc          between str_to_date(:data_7_anos, '%d/%m/%Y') and str_to_date(:data_atual, '%d/%m/%Y')";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('data_7_anos', $this->__get('data_7_anos'));
		$stmt->bindValue('data_atual', $this->__get('data_atual'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getQtdNomeIntegranteRanking() {
		$query = "
				select count(*) as qtde
				from tb_integ_fml 
				where cd_fmlID <> :cd_fml
				and   nm_integ = :nm_integ";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('nm_integ', $this->__get('nm_integ'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}


}	// Fim da Classe

?>