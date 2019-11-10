<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 28/08/2019
   Objetivo:  Contém os Sqls para a tabela tb_vncl_vlnt_acomp_fml
*/

namespace App\Models;

use MF\Model\Model;

class TbRessarPedidoRecurFinan extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_grpID;
	private $cd_sbgrpID;
	private $seql_pedido_finanID;
	private $seql_ressar_pedido_finanID;
	private $cd_tip_doc_ressar;
	private $dt_incl_ressar;
	private $dt_doc_ressar;
	private $vlr_doc_ressar;
	private $bco_cred_ressar;
	private $ag_cred_ressar;
	private $cta_cred_ressar;
	private $dig_verifica_cta_cred_ressar;
	private $cpf_cred_ressar;
	private $cnpj_cred_ressar;
	private $dt_envio_ressar_daf;
	private $dt_efetiva_cred_ressar_daf;
	private $cd_est_ressar;
	private $dsc_mtvo_indefer_ressar_daf;
	private $cd_vlnt_resp_incl_ressar;
	private $cd_vlnt_resp_envio_daf;
	private $cd_vlnt_resp_baixa_ressar;
	private $ts_ressar;
	
	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function insertRessarPRF() {

		if (null !== ($this->__get('cpf_cred_ressar'))) {
			if (empty($this->__get('cpf_cred_ressar'))) {
				$cpf_cred_ressar = null;
			} else {
				$cpf_cred_ressar = $this->__get('cpf_cred_ressar');
			}
		} else {
			$cpf_cred_ressar = null;
		}

		if (null !== ($this->__get('cnpj_cred_ressar'))) {
			if (empty($this->__get('cnpj_cred_ressar'))) {
				$cnpj_cred_ressar = null;
			} else {
				$cnpj_cred_ressar = $this->__get('cnpj_cred_ressar');
			}
		} else {
			$cnpj_cred_ressar = null;
		}

		if (null !== ($this->__get('dt_envio_ressar_daf'))) {
			if (empty($this->__get('dt_envio_ressar_daf'))) {
				$dt_envio_ressar_daf = null;
			} else {
				$dt_envio_ressar_daf = $this->__get('dt_envio_ressar_daf');
			}
		} else {
			$dt_envio_ressar_daf = null;
		}

		if (null !== ($this->__get('dt_efetiva_cred_ressar_daf'))) {
			if (empty($this->__get('dt_efetiva_cred_ressar_daf'))) {
				$dt_efetiva_cred_ressar_daf = null;
			} else {
				$dt_efetiva_cred_ressar_daf = $this->__get('dt_efetiva_cred_ressar_daf');
			}
		} else {
			$dt_efetiva_cred_ressar_daf = null;
		}

		if (null !== ($this->__get('dsc_mtvo_indefer_ressar_daf'))) {
			if (empty($this->__get('dsc_mtvo_indefer_ressar_daf'))) {
				$dsc_mtvo_indefer_ressar_daf = null;
			} else {
				$dsc_mtvo_indefer_ressar_daf = $this->__get('dsc_mtvo_indefer_ressar_daf');
			}
		} else {
			$dsc_mtvo_indefer_ressar_daf = null;
		}

		if (null !== ($this->__get('cd_vlnt_resp_envio_daf'))) {
			if (empty($this->__get('cd_vlnt_resp_envio_daf'))) {
				$cd_vlnt_resp_envio_daf = null;
			} else {
				$cd_vlnt_resp_envio_daf = $this->__get('cd_vlnt_resp_envio_daf');
			}
		} else {
			$cd_vlnt_resp_envio_daf = null;
		}

		if (null !== ($this->__get('cd_vlnt_resp_baixa_ressar'))) {
			if (empty($this->__get('cd_vlnt_resp_baixa_ressar'))) {
				$cd_vlnt_resp_baixa_ressar = null;
			} else {
				$cd_vlnt_resp_baixa_ressar = $this->__get('cd_vlnt_resp_baixa_ressar');
			}
		} else {
			$cd_vlnt_resp_baixa_ressar = null;
		}


		$this->getProximoSequencial();

		/*
		echo '<br>';
		echo 'cd_grp '. $this->__get('cd_grp');
		echo '<br>';
		echo 'cd_sbgrp '. $this->__get('cd_sbgrp');
		echo '<br>';		
		echo 'seql_pedido_finan '. $this->__get('seql_pedido_finan');
		echo '<br>';
		echo 'seql_ressar_pedido_finan '. $this->__get('seql_max');
		echo '<br>';
		echo 'cd_tip_doc_ressar '. $this->__get('cd_tip_doc_ressar');
		echo '<br>';
		echo 'dt_doc_ressar '. $this->__get('dt_doc_ressar');
		echo '<br>';
		echo 'vlr_doc_ressar '. $this->__get('vlr_doc_ressar');
		echo '<br>';
		echo 'bco_cred_ressar '. $this->__get('bco_cred_ressar');
		echo '<br>';
		echo 'ag_cred_ressar '. $this->__get('ag_cred_ressar');
		echo '<br>';
		echo 'cta_cred_ressar '. $this->__get('cta_cred_ressar');
		echo '<br>';
		echo 'dig_verifica_cta_cred_ressar '. $this->__get('dig_verifica_cta_cred_ressar');	
		echo '<br>';
		echo 'cpf_cred_ressar '. $cpf_cred_ressar;
		echo '<br>';
		echo 'cnpj_cred_ressar '. $cnpj_cred_ressar;
		echo '<br>';
		echo 'dt_envio_ressar_daf '. $dt_envio_ressar_daf;
		echo '<br>';
		echo 'dt_efetiva_cred_ressar_daf '. $dt_efetiva_cred_ressar_daf;
		echo '<br>';
		echo 'cd_est_ressar '. $this->__get('cd_est_ressar');
		echo '<br>';
		echo 'dsc_mtvo_indefer_ressar_daf '. $dsc_mtvo_indefer_ressar_daf;
		echo '<br>';
		echo 'cd_vlnt_resp_incl_ressar '. $this->__get('cd_vlnt_resp_incl_ressar');
		echo '<br>';
		echo 'cd_vlnt_resp_envio_daf '. $cd_vlnt_resp_envio_daf;
		echo '<br>';
		echo 'cd_vlnt_resp_baixa_ressar '. $cd_vlnt_resp_baixa_ressar;
		*/
	
		$query = "
				insert into tb_ressar_pedido_recur_finan
				(cd_grpID,
				cd_sbgrpID,
				seql_pedido_finanID,
				seql_ressar_pedido_finanID,
				cd_tip_doc_ressar,
				dt_incl_ressar,
				dt_doc_ressar,
				vlr_doc_ressar,
				bco_cred_ressar,
				ag_cred_ressar,
				cta_cred_ressar,
				dig_verifica_cta_cred_ressar,
				cpf_cred_ressar,
				cnpj_cred_ressar,
				dt_envio_ressar_daf,
				dt_efetiva_cred_ressar_daf,
				cd_est_ressar,
				dsc_mtvo_indefer_ressar_daf,
				cd_vlnt_resp_incl_ressar,
				cd_vlnt_resp_envio_daf,
				cd_vlnt_resp_baixa_ressar,
				ts_ressar)

				values 

				(:cd_grp,
				:cd_sbgrp,
				:seql_pedido_finan,
				:seql_ressar_pedido_finan,
				:cd_tip_doc_ressar,
				now(),
				str_to_date(:dt_doc_ressar, '%d/%m/%Y'),   
				:vlr_doc_ressar,
				:bco_cred_ressar,
				:ag_cred_ressar,
				:cta_cred_ressar,
				:dig_verifica_cta_cred_ressar,
				:cpf_cred_ressar,
				:cnpj_cred_ressar,
				:dt_envio_ressar_daf,
				:dt_efetiva_cred_ressar_daf,
				:cd_est_ressar,
				:dsc_mtvo_indefer_ressar_daf,
				:cd_vlnt_resp_incl_ressar,
				:cd_vlnt_resp_envio_daf,
				:cd_vlnt_resp_baixa_ressar,
				CURRENT_TIMESTAMP)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('seql_ressar_pedido_finan', $this->__get('seql_max'));
		$stmt->bindValue('cd_tip_doc_ressar', $this->__get('cd_tip_doc_ressar'));
		$stmt->bindValue('dt_doc_ressar', $this->__get('dt_doc_ressar'));
		$stmt->bindValue('vlr_doc_ressar', $this->__get('vlr_doc_ressar'));
		$stmt->bindValue('bco_cred_ressar', $this->__get('bco_cred_ressar'));
		$stmt->bindValue('ag_cred_ressar', $this->__get('ag_cred_ressar'));
		$stmt->bindValue('cta_cred_ressar', $this->__get('cta_cred_ressar'));
		$stmt->bindValue('dig_verifica_cta_cred_ressar', $this->__get('dig_verifica_cta_cred_ressar'));	
		$stmt->bindValue('cpf_cred_ressar', $cpf_cred_ressar);
		$stmt->bindValue('cnpj_cred_ressar', $cnpj_cred_ressar);
		$stmt->bindValue('dt_envio_ressar_daf', $dt_envio_ressar_daf);
		$stmt->bindValue('dt_efetiva_cred_ressar_daf', $dt_efetiva_cred_ressar_daf);
		$stmt->bindValue('cd_est_ressar', $this->__get('cd_est_ressar'));
		$stmt->bindValue('dsc_mtvo_indefer_ressar_daf', $dsc_mtvo_indefer_ressar_daf);
		$stmt->bindValue('cd_vlnt_resp_incl_ressar', $this->__get('cd_vlnt_resp_incl_ressar'));
		$stmt->bindValue('cd_vlnt_resp_envio_daf', $cd_vlnt_resp_envio_daf);
		$stmt->bindValue('cd_vlnt_resp_baixa_ressar', $cd_vlnt_resp_baixa_ressar);
		$stmt->execute();
		
		return $this;	

	}	//	Fim function insertRessarPRF

// =================================================== //

	public function getProximoSequencial() {
		$query0 = "
				select count(*) + 1 as qtde
				from  tb_ressar_pedido_recur_finan
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   seql_pedido_finanID = :seql_pedido_finan";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt0->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt0->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "
				select max(seql_ressar_pedido_finanID) + 1 as qtde
				from  tb_ressar_pedido_recur_finan
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   seql_pedido_finanID = :seql_pedido_finan";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue('cd_grp', $this->__get('cd_grp'));
			$stmt1->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
			$stmt1->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_max', $nr_registros_1['qtde']);
		}
	}	//	Fim getProximoSequencial 

// =================================================== //

	public function getSequencial() {
		$query = "
				select max(seql_ressar_pedido_finanID) as seql_max
				from  tb_ressar_pedido_recur_finan
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   seql_pedido_finanID = :seql_pedido_finan
				and   cd_est_ressar       = :cd_est_ressar";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_est_ressar', $this->__get('cd_est_ressar'));		
		$stmt->execute();
		
		$seql = $stmt->fetch(\PDO::FETCH_ASSOC);		

		$this->__set ('seql_max', $seql['seql_max']);
	
	}	//	Fim getSequencial 

// =================================================== //

	public function getCountRPRF() {
		$query = "
				select count(*) as qtde
				from  tb_ressar_pedido_recur_finan
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   seql_pedido_finanID = :seql_pedido_finan
				and   cd_est_ressar       = :cd_est_ressar";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_est_ressar', $this->__get('cd_est_ressar'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);		
	
	}	//	Fim getCountRPRF 

// =================================================== //

	public function getDadosRPRF() {
		$query = "
				select 	cd_grpID as cd_grp,
						cd_sbgrpID as cd_sbgrp,
						seql_pedido_finanID as seql_pedido_finan,
						seql_ressar_pedido_finanID as seql_ressar_pedido_finan,
						
						cd_tip_doc_ressar,
						CASE WHEN cd_tip_doc_ressar = 1 THEN 'Nota Fiscal'
						     WHEN cd_tip_doc_ressar = 2 THEN 'Recibo'
					   	END	as nm_cd_tip_doc_ressar,

						dt_incl_ressar,
						DATE_FORMAT(dt_incl_ressar, '%d/%m/%Y') as dt_incl_ressar_format,
						
						dt_doc_ressar,
						DATE_FORMAT(dt_doc_ressar, '%d/%m/%Y') as dt_doc_ressar_format,

						vlr_doc_ressar,
						bco_cred_ressar,
						ag_cred_ressar,
						cta_cred_ressar,
						dig_verifica_cta_cred_ressar,
						cpf_cred_ressar,
						cnpj_cred_ressar,

						dt_envio_ressar_daf,
						DATE_FORMAT(dt_envio_ressar_daf, '%d/%m/%Y') as dt_envio_ressar_daf_format,

						dt_efetiva_cred_ressar_daf,
						DATE_FORMAT(dt_efetiva_cred_ressar_daf, '%d/%m/%Y') as dt_efetiva_cred_ressar_daf_format,

						cd_est_ressar,
						CASE WHEN cd_est_ressar = 1 THEN 'Aguardando envio ao DAF'
						     WHEN cd_est_ressar = 2 THEN 'Aguardando Ressarcimento'
						     WHEN cd_est_ressar = 3 THEN 'Ressarcimento Realizado'
						     WHEN cd_est_ressar = 4 THEN 'Ressarcimento Indeferido'
						     WHEN cd_est_ressar = 5 THEN 'Ressarcimento Cancelado'
					   	END	as nm_cd_est_ressar,

						dsc_mtvo_indefer_ressar_daf,
						cd_vlnt_resp_incl_ressar,
						cd_vlnt_resp_envio_daf,
						cd_vlnt_resp_baixa_ressar,
						ts_ressar

				from  tb_ressar_pedido_recur_finan
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   seql_pedido_finanID = :seql_pedido_finan
				and   cd_est_ressar       = :cd_est_ressar";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_est_ressar', $this->__get('cd_est_ressar'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);		
	
	}	//	Fim getDadosRPRF 

// =================================================== //

	public function updateRessarPRF() {

		if (null !== ($this->__get('cpf_cred_ressar'))) {
			if (empty($this->__get('cpf_cred_ressar'))) {
				$cpf_cred_ressar = null;
			} else {
				$cpf_cred_ressar = $this->__get('cpf_cred_ressar');
			}
		} else {
			$cpf_cred_ressar = null;
		}

		if (null !== ($this->__get('cnpj_cred_ressar'))) {
			if (empty($this->__get('cnpj_cred_ressar'))) {
				$cnpj_cred_ressar = null;
			} else {
				$cnpj_cred_ressar = $this->__get('cnpj_cred_ressar');
			}
		} else {
			$cnpj_cred_ressar = null;
		}
	
		$query = "
				update tb_ressar_pedido_recur_finan
				set cd_tip_doc_ressar            = :cd_tip_doc_ressar,
					dt_doc_ressar                = str_to_date(:dt_doc_ressar, '%d/%m/%Y'),   
					bco_cred_ressar              = :bco_cred_ressar,
					ag_cred_ressar               = :ag_cred_ressar,
					cta_cred_ressar              = :cta_cred_ressar,
					dig_verifica_cta_cred_ressar = :dig_verifica_cta_cred_ressar,
					cpf_cred_ressar              = :cpf_cred_ressar,
					cnpj_cred_ressar             = :cnpj_cred_ressar,
					cd_vlnt_resp_incl_ressar     = :cd_vlnt_resp_incl_ressar,
					ts_ressar                    = CURRENT_TIMESTAMP
				where cd_grpID                   = :cd_grp
				and   cd_sbgrpID                 = :cd_sbgrp
				and   seql_pedido_finanID        = :seql_pedido_finan
				and   seql_ressar_pedido_finanID = :seql_ressar_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('seql_ressar_pedido_finan', $this->__get('seql_ressar_pedido_finan'));
		$stmt->bindValue('cd_tip_doc_ressar', $this->__get('cd_tip_doc_ressar'));
		$stmt->bindValue('dt_doc_ressar', $this->__get('dt_doc_ressar'));
		$stmt->bindValue('bco_cred_ressar', $this->__get('bco_cred_ressar'));
		$stmt->bindValue('ag_cred_ressar', $this->__get('ag_cred_ressar'));
		$stmt->bindValue('cta_cred_ressar', $this->__get('cta_cred_ressar'));
		$stmt->bindValue('dig_verifica_cta_cred_ressar', $this->__get('dig_verifica_cta_cred_ressar'));	
		$stmt->bindValue('cpf_cred_ressar', $cpf_cred_ressar);
		$stmt->bindValue('cnpj_cred_ressar', $cnpj_cred_ressar);
		$stmt->bindValue('cd_vlnt_resp_incl_ressar', $this->__get('cd_vlnt_resp_incl_ressar'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function updateRessarPRF

// =================================================== //

	public function updateEstadoRessarPRF() {
		$query = "
				update tb_ressar_pedido_recur_finan
				set cd_est_ressar            = :cd_est_ressar,
					cd_vlnt_resp_incl_ressar = :cd_vlnt_resp_incl_ressar,
					ts_ressar                = CURRENT_TIMESTAMP
				where cd_grpID                   = :cd_grp
				and   cd_sbgrpID                 = :cd_sbgrp
				and   seql_pedido_finanID        = :seql_pedido_finan
				and   seql_ressar_pedido_finanID = :seql_ressar_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('seql_ressar_pedido_finan', $this->__get('seql_ressar_pedido_finan'));
		$stmt->bindValue('cd_est_ressar', $this->__get('cd_est_ressar'));
		$stmt->bindValue('cd_vlnt_resp_incl_ressar', $this->__get('cd_vlnt_resp_incl_ressar'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function updateEstadoRessarPRF

// =================================================== //

	public function updateAutorizaRessarPRF() {
		$query = "
				update tb_ressar_pedido_recur_finan
				set dt_envio_ressar_daf    = now(),  
					cd_est_ressar          = :cd_est_ressar,
					cd_vlnt_resp_envio_daf = :cd_vlnt_resp_envio_daf,
					ts_ressar              = CURRENT_TIMESTAMP
				where cd_grpID                   = :cd_grp
				and   cd_sbgrpID                 = :cd_sbgrp
				and   seql_pedido_finanID        = :seql_pedido_finan
				and   seql_ressar_pedido_finanID = :seql_ressar_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('seql_ressar_pedido_finan', $this->__get('seql_ressar_pedido_finan'));
		$stmt->bindValue('cd_est_ressar', $this->__get('cd_est_ressar'));
		$stmt->bindValue('cd_vlnt_resp_envio_daf', $this->__get('cd_vlnt_resp_envio_daf'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function updateAutorizaRessarPRF


} 	// Fim da classe 
?>