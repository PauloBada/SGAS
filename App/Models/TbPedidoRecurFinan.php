<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 31/10/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbPedidoRecurFinan extends Model {
	
	// colunas da tabela
	private $cd_grpID;             
	private $cd_sbgrpID;             
	private $seql_pedido_finanID;          
	private $dsc_sucinta_pedido;
	private $dsc_resum_pedido;
	private $menor_vlr_encontra;
	private $arq_orc_pedido;         
	private $arq_compara_preco_pedido;
	private $dt_incl_pedido;              
	private $cd_vlnt_resp_pedido;        
	private $dt_autoriza_pedido;
	private $cd_vlnt_resp_autoriza;
	private $cd_tip_enquadra_pedido;
	private $cd_est_pedido;
	private $cd_situ_envio_ressar_pedido;
	private $ts_pedido;
	private $dir_guarda_arq;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}


// ====================================================== //

	public function getDadosPedidoRecurFinan() {
		$query = "
				select 	cd_grpID as cd_grp,
						cd_sbgrpID as cd_sbgrp,
						seql_pedido_finanID as seql_pedido_finan,
						dsc_sucinta_pedido,
						dsc_resum_pedido,
						menor_vlr_encontra,
						arq_orc_pedido,
						arq_compara_preco_pedido,
						
						dt_incl_pedido,
						DATE_FORMAT(dt_incl_pedido, '%d/%m/%Y') as dt_incl_pedido_format,
						
						cd_vlnt_resp_pedido,
  					   	
  					   	dt_autoriza_pedido,
  					   	DATE_FORMAT(dt_autoriza_pedido, '%d/%m/%Y') as dt_autoriza_pedido_format,
						
						cd_vlnt_resp_autoriza,
						
						cd_tip_enquadra_pedido,
						CASE WHEN cd_tip_enquadra_pedido = 1 THEN 'Sim'
						     WHEN cd_tip_enquadra_pedido = 2 THEN 'Não'
					   	END	as cd_tip_enquadra_pedido_format,

						cd_est_pedido,
						CASE WHEN cd_est_pedido = 1 THEN 'Aguardando término Inclusão'
						     WHEN cd_est_pedido = 2 THEN 'Aguardando Autorização'
						     WHEN cd_est_pedido = 3 THEN 'Autorizado'
						     WHEN cd_est_pedido = 4 THEN 'Cancelado'
					   	END	as nm_est_pedido_format,
						
						cd_situ_envio_ressar_pedido,
						CASE WHEN cd_situ_envio_ressar_pedido = 1 THEN 'Não Enviado'
						     WHEN cd_situ_envio_ressar_pedido = 2 THEN 'Enviado'
					   	END	as nm_situ_envio_ressar_pedido_format,

						ts_pedido,
						dir_guarda_arq	

				from  tb_pedido_recur_finan 
				where cd_grpID             = :cd_grp
				and   cd_sbgrpID           = :cd_sbgrp
				and   seql_pedido_finanID  = :seql_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosPedidoRecurFinan

// ====================================================== //

	public function getDadosPedidoRecurFinanAll() {
		$query = "
				select 	cd_grpID as cd_grp,
						cd_sbgrpID as cd_sbgrp,
						seql_pedido_finanID as seql_pedido_finan,
						dsc_sucinta_pedido,
						dsc_resum_pedido,
						menor_vlr_encontra,
						arq_orc_pedido,
						arq_compara_preco_pedido,
						
						dt_incl_pedido,
						DATE_FORMAT(dt_incl_pedido, '%d/%m/%Y') as dt_incl_pedido_format,
						
						cd_vlnt_resp_pedido,
  					   	
  					   	dt_autoriza_pedido,
  					   	DATE_FORMAT(dt_autoriza_pedido, '%d/%m/%Y') as dt_autoriza_pedido_format,
						
						cd_vlnt_resp_autoriza,
						
						cd_tip_enquadra_pedido,
						CASE WHEN cd_tip_enquadra_pedido = 1 THEN 'Sim'
						     WHEN cd_tip_enquadra_pedido = 2 THEN 'Não'
					   	END	as cd_tip_enquadra_pedido_format,

						cd_est_pedido,
						CASE WHEN cd_est_pedido = 1 THEN 'Aguardando término Inclusão'
						     WHEN cd_est_pedido = 2 THEN 'Aguardando Autorização'
						     WHEN cd_est_pedido = 3 THEN 'Autorizado'
						     WHEN cd_est_pedido = 4 THEN 'Cancelado'
					   	END	as cd_est_pedido_format,
						
						cd_situ_envio_ressar_pedido,
						CASE WHEN cd_situ_envio_ressar_pedido = 1 THEN 'Não Enviado'
						     WHEN cd_situ_envio_ressar_pedido = 2 THEN 'Enviado'
					   	END	as cd_situ_envio_ressar_pedido_format,

						ts_pedido,
						dir_guarda_arq				

				from  tb_pedido_recur_finan 
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   cd_est_pedido between :cd_est_pedido1 and :cd_est_pedido2
                order by seql_pedido_finanID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_est_pedido1', $this->__get('cd_est_pedido1'));
		$stmt->bindValue('cd_est_pedido2', $this->__get('cd_est_pedido2'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosPedidoRecurFinanAll

// ====================================================== //

	public function insertPedidoRecurFinan() {
		// Para gravar nulo nos campos quando não houver informação
		
		if (null !== ($this->__get('arq_orc_pedido'))) {
			if (empty($this->__get('arq_orc_pedido'))) {
				$arq_orc_pedido = null;
			} else {
				$arq_orc_pedido = $this->__get('arq_orc_pedido');
			}
		} else {
			$arq_orc_pedido = null;
		}

		if (null !== ($this->__get('dir_guarda_arq'))) {
			if (empty($this->__get('dir_guarda_arq'))) {
				$dir_guarda_arq = null;
			} else {
				$dir_guarda_arq = $this->__get('dir_guarda_arq');
			}
		} else {
			$dir_guarda_arq = null;
		}

		if (null !== ($this->__get('arq_compara_preco_pedido'))) {
			if (empty($this->__get('arq_compara_preco_pedido'))) {
				$arq_compara_preco_pedido = null;
			} else {
				$arq_compara_preco_pedido = $this->__get('arq_compara_preco_pedido');
			}
		} else {
			$arq_compara_preco_pedido = null;
		}

		if (null !== ($this->__get('dt_autoriza_pedido'))) {
			if (empty($this->__get('dt_autoriza_pedido'))) {
				$dt_autoriza_pedido = null;
			} else {
				$dt_autoriza_pedido = $this->__get('dt_autoriza_pedido');
			}
		} else {
			$dt_autoriza_pedido = null;
		}

		if (null !== ($this->__get('cd_vlnt_resp_autoriza'))) {
			if (empty($this->__get('cd_vlnt_resp_autoriza'))) {
				$cd_vlnt_resp_autoriza = null;
			} else {
				$cd_vlnt_resp_autoriza = $this->__get('cd_vlnt_resp_autoriza');
			}
		} else {
			$cd_vlnt_resp_autoriza = null;
		}

/*
		echo 'cd_grp '. $this->__get('cd_grp');  
		echo '<br>';           
		echo 'cd_sbgrp '. $this->__get('cd_sbgrp');             
		echo '<br>';           
		echo 'seql_pedido_finan '. $this->__get('seql_pedido_finan');
		echo '<br>';           		
		echo 'dsc_sucinta_pedido '. $this->__get('dsc_sucinta_pedido');
		echo '<br>';           		
		echo 'dsc_resum_pedido '. $this->__get('dsc_resum_pedido');
		echo '<br>';           		
		echo 'menor_vlr_encontra '. $this->__get('menor_vlr_encontra');
		echo '<br>';           		
		echo 'arq_orc_pedido '. $arq_orc_pedido;         
		echo '<br>';           		
		echo 'arq_compara_preco_pedido '. $arq_compara_preco_pedido;
		echo '<br>';           		
		echo 'cd_vlnt_resp_pedido '. $this->__get('cd_vlnt_resp_pedido');        
		echo '<br>';           		
		echo 'dt_autoriza_pedido '. $dt_autoriza_pedido;
		echo '<br>';           		
		echo 'cd_vlnt_resp_autoriza '. $cd_vlnt_resp_autoriza;
		echo '<br>';           		
		echo 'cd_tip_enquadra_pedido '. $this->__get('cd_tip_enquadra_pedido');
		echo '<br>';           
		echo 'cd_est_pedido '. $this->__get('cd_est_pedido');
		echo '<br>';           
		echo 'cd_situ_envio_ressar_pedido '. $this->__get('cd_situ_envio_ressar_pedido');
		echo '<br>';           		
		echo 'dir_guarda_arq '. $this->__get('dir_guarda_arq');
*/
		$query = "          
				insert into tb_pedido_recur_finan
				(cd_grpID,             
				cd_sbgrpID,             
				seql_pedido_finanID,          
				dsc_sucinta_pedido,
				dsc_resum_pedido,
				menor_vlr_encontra,
				arq_orc_pedido,         
				arq_compara_preco_pedido,
				dt_incl_pedido,              
				cd_vlnt_resp_pedido,        
				dt_autoriza_pedido,
				cd_vlnt_resp_autoriza,
				cd_tip_enquadra_pedido,
				cd_est_pedido,
				cd_situ_envio_ressar_pedido,
				ts_pedido,
				dir_guarda_arq)

				values 

				(:cd_grp,             
				:cd_sbgrp,             
				:seql_pedido_finan,          
				:dsc_sucinta_pedido,
				:dsc_resum_pedido,
				:menor_vlr_encontra,
				:arq_orc_pedido,         
				:arq_compara_preco_pedido,
				now(),              
				:cd_vlnt_resp_pedido,        
				:dt_autoriza_pedido,
				:cd_vlnt_resp_autoriza,
				:cd_tip_enquadra_pedido,
				:cd_est_pedido,
				:cd_situ_envio_ressar_pedido,
				CURRENT_TIMESTAMP,
				:dir_guarda_arq)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));             
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));             
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('dsc_sucinta_pedido', $this->__get('dsc_sucinta_pedido'));
		$stmt->bindValue('dsc_resum_pedido', $this->__get('dsc_resum_pedido'));
		$stmt->bindValue('menor_vlr_encontra', $this->__get('menor_vlr_encontra'));
		$stmt->bindValue('arq_orc_pedido', $arq_orc_pedido);         
		$stmt->bindValue('arq_compara_preco_pedido', $arq_compara_preco_pedido);
		$stmt->bindValue('cd_vlnt_resp_pedido', $this->__get('cd_vlnt_resp_pedido'));        
		$stmt->bindValue('dt_autoriza_pedido', $dt_autoriza_pedido);
		$stmt->bindValue('cd_vlnt_resp_autoriza', $cd_vlnt_resp_autoriza);
		$stmt->bindValue('cd_tip_enquadra_pedido', $this->__get('cd_tip_enquadra_pedido'));
		$stmt->bindValue('cd_est_pedido', $this->__get('cd_est_pedido'));
		$stmt->bindValue('cd_situ_envio_ressar_pedido', $this->__get('cd_situ_envio_ressar_pedido'));
		$stmt->bindValue('dir_guarda_arq', $this->__get('dir_guarda_arq'));
		$stmt->execute();

		return $this;

	} // Fim function insertPedidoRecurFinan

// =================================================== //

	public function getProximoSequencial() {
		$query0 = "select count(*) + 1 as qtde
					from  tb_pedido_recur_finan
					where cd_grpID    = :cd_grp
					and   cd_sbgrpID  = :cd_sbgrp";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt0->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "select max(seql_pedido_finanID) + 1 as qtde
					from  tb_pedido_recur_finan
					where cd_grpID    = :cd_grp
					and   cd_sbgrpID  = :cd_sbgrp";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue('cd_grp', $this->__get('cd_grp'));
			$stmt1->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_max', $nr_registros_1['qtde']);
		}
	}	// Fim function getProximoSequencial

// =================================================== //

	public function getSequencial() {
		$query = "
				select max(seql_pedido_finanID) as seql_max
				from  tb_pedido_recur_finan
				where cd_grpID    = :cd_grp
				and   cd_sbgrpID  = :cd_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->execute();
		
		$seql = $stmt->fetch(\PDO::FETCH_ASSOC);		

		$this->__set ('seql_max', $seql['seql_max']);
	}	// Fim function getSequencial

// =================================================== //

	public function updatePRFComNovoArqPDF() {
		$query = "
				update tb_pedido_recur_finan
				set dsc_sucinta_pedido          = :dsc_sucinta_pedido, 
					dsc_resum_pedido            = :dsc_resum_pedido, 
					menor_vlr_encontra          = :menor_vlr_encontra, 
					arq_orc_pedido              = :arq_orc_pedido,          
					cd_vlnt_resp_pedido         = :cd_vlnt_resp_pedido,         
					cd_tip_enquadra_pedido      = :cd_tip_enquadra_pedido,
					ts_pedido                   = CURRENT_TIMESTAMP, 
					dir_guarda_arq              = :dir_guarda_arq 

				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				AND   seql_pedido_finanID = :seql_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('dsc_sucinta_pedido', $this->__get('dsc_sucinta_pedido'));
		$stmt->bindValue('dsc_resum_pedido', $this->__get('dsc_resum_pedido'));
		$stmt->bindValue('menor_vlr_encontra', $this->__get('menor_vlr_encontra'));
		$stmt->bindValue('arq_orc_pedido', $this->__get('arq_orc_pedido'));
		$stmt->bindValue('cd_vlnt_resp_pedido', $this->__get('cd_vlnt_resp_pedido'));        
		$stmt->bindValue('cd_tip_enquadra_pedido', $this->__get('cd_tip_enquadra_pedido'));
		$stmt->bindValue('dir_guarda_arq', $this->__get('dir_guarda_arq'));
		$stmt->execute();

		return $this;
	
	}	// Fim function updatePRFComNovoArqPDF

// =================================================== //

	public function updatePRFSemNovoArqPDF() {
		$query = "
				update tb_pedido_recur_finan
				set dsc_sucinta_pedido          = :dsc_sucinta_pedido, 
					dsc_resum_pedido            = :dsc_resum_pedido, 
					menor_vlr_encontra          = :menor_vlr_encontra, 
					cd_vlnt_resp_pedido         = :cd_vlnt_resp_pedido,         
					cd_tip_enquadra_pedido      = :cd_tip_enquadra_pedido,
					ts_pedido                   = CURRENT_TIMESTAMP 
	
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				AND   seql_pedido_finanID = :seql_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('dsc_sucinta_pedido', $this->__get('dsc_sucinta_pedido'));
		$stmt->bindValue('dsc_resum_pedido', $this->__get('dsc_resum_pedido'));
		$stmt->bindValue('menor_vlr_encontra', $this->__get('menor_vlr_encontra'));
		$stmt->bindValue('cd_vlnt_resp_pedido', $this->__get('cd_vlnt_resp_pedido'));        
		$stmt->bindValue('cd_tip_enquadra_pedido', $this->__get('cd_tip_enquadra_pedido'));
		$stmt->execute();
		
		return $this;

	}	// Fim function updatePRFSemNovoArqPDF

// =================================================== //

	public function updatePRFCdEstado() {
		$query = "
				update tb_pedido_recur_finan
				set cd_est_pedido       = :cd_est_pedido,
					cd_vlnt_resp_pedido = :cd_vlnt_resp_pedido,         
					ts_pedido           = CURRENT_TIMESTAMP 
	
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				AND   seql_pedido_finanID = :seql_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_est_pedido', $this->__get('cd_est_pedido'));        
		$stmt->bindValue('cd_vlnt_resp_pedido', $this->__get('cd_vlnt_resp_pedido'));        
		$stmt->execute();
		
		return $this;

	}	// Fim function updatePRFCdEstado

// ====================================================== //

	public function getDadosPedidoRecurFinanAllConsulta() {
		$query = "
				select 	cd_grpID as cd_grp,
						cd_sbgrpID as cd_sbgrp,
						seql_pedido_finanID as seql_pedido_finan,
						dsc_sucinta_pedido,
						dsc_resum_pedido,
						menor_vlr_encontra,
						arq_orc_pedido,
						arq_compara_preco_pedido,
						
						dt_incl_pedido,
						DATE_FORMAT(dt_incl_pedido, '%d/%m/%Y') as dt_incl_pedido_format,
						
						cd_vlnt_resp_pedido,
  					   	
  					   	dt_autoriza_pedido,
  					   	DATE_FORMAT(dt_autoriza_pedido, '%d/%m/%Y') as dt_autoriza_pedido_format,
						
						cd_vlnt_resp_autoriza,
						
						cd_tip_enquadra_pedido,
						CASE WHEN cd_tip_enquadra_pedido = 1 THEN 'Sim'
						     WHEN cd_tip_enquadra_pedido = 2 THEN 'Não'
					   	END	as cd_tip_enquadra_pedido_format,

						cd_est_pedido,
						CASE WHEN cd_est_pedido = 1 THEN 'Aguardando término Inclusão'
						     WHEN cd_est_pedido = 2 THEN 'Aguardando Autorização'
						     WHEN cd_est_pedido = 3 THEN 'Autorizado'
						     WHEN cd_est_pedido = 4 THEN 'Cancelado'
					   	END	as cd_est_pedido_format,
						
						cd_situ_envio_ressar_pedido,
						CASE WHEN cd_situ_envio_ressar_pedido = 1 THEN 'Não Enviado'
						     WHEN cd_situ_envio_ressar_pedido = 2 THEN 'Enviado'
					   	END	as cd_situ_envio_ressar_pedido_format,

						ts_pedido,
						dir_guarda_arq				

				from  tb_pedido_recur_finan 
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   cd_est_pedido between :cd_est_pedido1 and :cd_est_pedido4
				and   dt_incl_pedido between str_to_date(:dt_inicio, '%d/%m/%Y')
				                         and str_to_date(:dt_fim, '%d/%m/%Y')

                order by seql_pedido_finanID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_est_pedido1', $this->__get('cd_est_pedido1'));
		$stmt->bindValue('cd_est_pedido4', $this->__get('cd_est_pedido4'));
		$stmt->bindValue('dt_inicio', $this->__get('dt_inicio'));
		$stmt->bindValue('dt_fim', $this->__get('dt_fim'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosPedidoRecurFinanAllConsulta

// ====================================================== //

	public function getDadosPedidoRecurFinanAutorizacao() {
		$query = "
				select 	cd_grpID as cd_grp,
						cd_sbgrpID as cd_sbgrp,
						seql_pedido_finanID as seql_pedido_finan,
						dsc_sucinta_pedido,
						dsc_resum_pedido,
						menor_vlr_encontra,
						arq_orc_pedido,
						arq_compara_preco_pedido,
						
						dt_incl_pedido,
						DATE_FORMAT(dt_incl_pedido, '%d/%m/%Y') as dt_incl_pedido_format,
						
						cd_vlnt_resp_pedido,
  					   	
  					   	dt_autoriza_pedido,
  					   	DATE_FORMAT(dt_autoriza_pedido, '%d/%m/%Y') as dt_autoriza_pedido_format,
						
						cd_vlnt_resp_autoriza,
						
						cd_tip_enquadra_pedido,
						CASE WHEN cd_tip_enquadra_pedido = 1 THEN 'Sim'
						     WHEN cd_tip_enquadra_pedido = 2 THEN 'Não'
					   	END	as nm_tip_enquadra_pedido,

						cd_est_pedido,
						CASE WHEN cd_est_pedido = 1 THEN 'Aguardando término Inclusão'
						     WHEN cd_est_pedido = 2 THEN 'Aguardando Autorização'
						     WHEN cd_est_pedido = 3 THEN 'Autorizado'
						     WHEN cd_est_pedido = 4 THEN 'Cancelado'
					   	END	as nm_est_pedido,
						
						cd_situ_envio_ressar_pedido,
						CASE WHEN cd_situ_envio_ressar_pedido = 1 THEN 'Não Enviado'
						     WHEN cd_situ_envio_ressar_pedido = 2 THEN 'Enviado'
					   	END	as nm_situ_envio_ressar_pedido,

						ts_pedido,
						dir_guarda_arq				

				from  tb_pedido_recur_finan 
				where cd_est_pedido between :cd_est_pedido1 and :cd_est_pedido2
                order by menor_vlr_encontra, seql_pedido_finanID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_est_pedido1', $this->__get('cd_est_pedido1'));
		$stmt->bindValue('cd_est_pedido2', $this->__get('cd_est_pedido2'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosPedidoRecurFinanAutorizacao

// =================================================== //

	public function updatePRFCdEstadoAutoriza() {

		if (null !== ($this->__get('cd_vlnt_resp_autoriza'))) {
			if (empty($this->__get('cd_vlnt_resp_autoriza'))) {
				$cd_vlnt_resp_autoriza = null;
				$dt_autoriza_pedido = null;
			} else {
				$cd_vlnt_resp_autoriza = $this->__get('cd_vlnt_resp_autoriza');
				$data_hoje = new \DateTime();
				$dt_autoriza_pedido	= 	$data_hoje->format("d/m/Y");
			}
		} else {
			$cd_vlnt_resp_autoriza = null;
			$dt_autoriza_pedido = null;			
		}

		if ($dt_autoriza_pedido === null) {
			$query = "
					update tb_pedido_recur_finan
					set   cd_est_pedido         = :cd_est_pedido,
						  cd_vlnt_resp_autoriza = :cd_vlnt_resp_autoriza,
						  dt_autoriza_pedido    = :dt_autoriza_pedido   
					where cd_grpID            = :cd_grp
					and   cd_sbgrpID          = :cd_sbgrp
					AND   seql_pedido_finanID = :seql_pedido_finan";
		} else {
			$query = "
					update tb_pedido_recur_finan
					set   cd_est_pedido         = :cd_est_pedido,
						  cd_vlnt_resp_autoriza = :cd_vlnt_resp_autoriza,
						  dt_autoriza_pedido    = str_to_date(:dt_autoriza_pedido, '%d/%m/%Y')   
					where cd_grpID            = :cd_grp
					and   cd_sbgrpID          = :cd_sbgrp
					AND   seql_pedido_finanID = :seql_pedido_finan";
		}

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_est_pedido', $this->__get('cd_est_pedido'));        
		$stmt->bindValue('cd_vlnt_resp_autoriza', $cd_vlnt_resp_autoriza);        
		$stmt->bindValue('dt_autoriza_pedido', $dt_autoriza_pedido);        
		$stmt->execute();
		
		return $this;

	}	// Fim function updatePRFCdEstadoAutoriza

// =================================================== //

	public function updatePRFRemeteDAF() {
		$query = "
				update tb_pedido_recur_finan
				set   cd_situ_envio_ressar_pedido = :cd_situ_envio_ressar_pedido	
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				AND   seql_pedido_finanID = :seql_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_situ_envio_ressar_pedido', $this->__get('cd_situ_envio_ressar_pedido'));        
		$stmt->execute();
		
		return $this;

	}	// Fim function updatePRFRemeteDAF

// ====================================================== //

	public function getDadosPedidoRecurFinanCancelaAutorizacao() {
		$query = "
				select 	cd_grpID as cd_grp,
						cd_sbgrpID as cd_sbgrp,
						seql_pedido_finanID as seql_pedido_finan,
						dsc_sucinta_pedido,
						dsc_resum_pedido,
						menor_vlr_encontra,
						arq_orc_pedido,
						arq_compara_preco_pedido,
						
						dt_incl_pedido,
						DATE_FORMAT(dt_incl_pedido, '%d/%m/%Y') as dt_incl_pedido_format,
						
						cd_vlnt_resp_pedido,
  					   	
  					   	dt_autoriza_pedido,
  					   	DATE_FORMAT(dt_autoriza_pedido, '%d/%m/%Y') as dt_autoriza_pedido_format,
						
						cd_vlnt_resp_autoriza,
						
						cd_tip_enquadra_pedido,
						CASE WHEN cd_tip_enquadra_pedido = 1 THEN 'Sim'
						     WHEN cd_tip_enquadra_pedido = 2 THEN 'Não'
					   	END	as nm_tip_enquadra_pedido,

						cd_est_pedido,
						CASE WHEN cd_est_pedido = 1 THEN 'Aguardando término Inclusão'
						     WHEN cd_est_pedido = 2 THEN 'Aguardando Autorização'
						     WHEN cd_est_pedido = 3 THEN 'Autorizado'
						     WHEN cd_est_pedido = 4 THEN 'Cancelado'
					   	END	as nm_est_pedido,
						
						cd_situ_envio_ressar_pedido,
						CASE WHEN cd_situ_envio_ressar_pedido = 1 THEN 'Não Enviado'
						     WHEN cd_situ_envio_ressar_pedido = 2 THEN 'Enviado'
					   	END	as nm_situ_envio_ressar_pedido,

						ts_pedido,
						dir_guarda_arq				

				from  tb_pedido_recur_finan 
				where cd_est_pedido                     = :cd_est_pedido
				and   cd_situ_envio_ressar_pedido between :cd_situ_envio_ressar_pedido1 and :cd_situ_envio_ressar_pedido2
                order by cd_grpID, cd_sbgrpID, seql_pedido_finanID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_est_pedido', $this->__get('cd_est_pedido'));
		$stmt->bindValue('cd_situ_envio_ressar_pedido1', $this->__get('cd_situ_envio_ressar_pedido1'));
		$stmt->bindValue('cd_situ_envio_ressar_pedido2', $this->__get('cd_situ_envio_ressar_pedido2'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosPedidoRecurFinanCancelaAutorizacao

// ====================================================== //

	public function getDadosPedidoRecurFinanRessarcimento() {
		$query = "
				select 	cd_grpID as cd_grp,
						cd_sbgrpID as cd_sbgrp,
						seql_pedido_finanID as seql_pedido_finan,
						dsc_sucinta_pedido,
						dsc_resum_pedido,
						menor_vlr_encontra,
						arq_orc_pedido,
						arq_compara_preco_pedido,
						
						dt_incl_pedido,
						DATE_FORMAT(dt_incl_pedido, '%d/%m/%Y') as dt_incl_pedido_format,
						
						cd_vlnt_resp_pedido,
  					   	
  					   	dt_autoriza_pedido,
  					   	DATE_FORMAT(dt_autoriza_pedido, '%d/%m/%Y') as dt_autoriza_pedido_format,
						
						cd_vlnt_resp_autoriza,
						
						cd_tip_enquadra_pedido,
						CASE WHEN cd_tip_enquadra_pedido = 1 THEN 'Sim'
						     WHEN cd_tip_enquadra_pedido = 2 THEN 'Não'
					   	END	as nm_tip_enquadra_pedido,

						cd_est_pedido,
						CASE WHEN cd_est_pedido = 1 THEN 'Aguardando término Inclusão'
						     WHEN cd_est_pedido = 2 THEN 'Aguardando Autorização'
						     WHEN cd_est_pedido = 3 THEN 'Autorizado'
						     WHEN cd_est_pedido = 4 THEN 'Cancelado'
					   	END	as nm_est_pedido,
						
						cd_situ_envio_ressar_pedido,
						CASE WHEN cd_situ_envio_ressar_pedido = 1 THEN 'Não Enviado'
						     WHEN cd_situ_envio_ressar_pedido = 2 THEN 'Enviado'
					   	END	as nm_situ_envio_ressar_pedido,

						ts_pedido,
						dir_guarda_arq				

				from  tb_pedido_recur_finan 
				where cd_est_pedido  = :cd_est_pedido
				and   cd_situ_envio_ressar_pedido in (:cd_situ_envio_ressar_pedido1, :cd_situ_envio_ressar_pedido2)
                order by menor_vlr_encontra, seql_pedido_finanID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_est_pedido', $this->__get('cd_est_pedido'));
		$stmt->bindValue('cd_situ_envio_ressar_pedido1', $this->__get('cd_situ_envio_ressar_pedido1'));
		$stmt->bindValue('cd_situ_envio_ressar_pedido2', $this->__get('cd_situ_envio_ressar_pedido2'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosPedidoRecurFinanRessarcimento

// ====================================================== //

	public function getSumVlrAutorizado() {
		$query = "
				select 	sum(menor_vlr_encontra) as saldo_vlr_autorizado
				from  tb_pedido_recur_finan 
				where cd_est_pedido  = :cd_est_pedido";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_est_pedido', $this->__get('cd_est_pedido'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	
	} // Fim function getSumVlrAutorizado



}	// Fim da Classe

?>