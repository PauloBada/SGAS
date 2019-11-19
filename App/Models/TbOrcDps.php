<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 01/11/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbOrcDps extends Model {
	
	// colunas da tabela
	private $seql_orcID;          
	private $dt_recur_orc;              
	private $dt_venc_recur_orc;              
	private $vlr_recur_orc;	
	private $vlr_sdo_recur_orc;        
	private $cd_situ_recur_orc;        
	private $cd_vlnt_resp_incl;        
	private $ts_incl;
	private $obs;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //

	public function getDadosOrcDps() {
		$query = "
				select 	seql_orcID as seql_orc,
				
						dt_recur_orc,
						DATE_FORMAT(dt_recur_orc, '%d/%m/%Y') as dt_recur_orc_format,

						dt_venc_recur_orc,
						DATE_FORMAT(dt_venc_recur_orc, '%d/%m/%Y') as dt_venc_recur_orc_format,

						vlr_recur_orc,
						vlr_sdo_recur_orc,
				
						cd_situ_recur_orc,
						CASE WHEN cd_situ_recur_orc = 1 THEN 'Orçamento com Saldo'
						     WHEN cd_situ_recur_orc = 2 THEN 'Orçamento realizado e sem Saldo'
						     WHEN cd_situ_recur_orc = 3 THEN 'Orçamento cancelado'
							 WHEN cd_situ_recur_orc = 4 THEN 'Orçamento glosado'						     
					   	END	as nm_situ_recur_orc,

					   	cd_vlnt_resp_incl,
					   	obs

				from  tb_orc_dps
				where seql_orcID = :seql_orc";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('seql_orc', $this->__get('seql_orc'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosOrcDps

// ====================================================== //

	public function getDadosOrcDps2() {
		$query = "
				select 	seql_orcID as seql_orc,
				
						dt_recur_orc,
						DATE_FORMAT(dt_recur_orc, '%d/%m/%Y') as dt_recur_orc_format,

						dt_venc_recur_orc,
						DATE_FORMAT(dt_venc_recur_orc, '%d/%m/%Y') as dt_venc_recur_orc_format,

						vlr_recur_orc,
						vlr_sdo_recur_orc,
				
						cd_situ_recur_orc,
						CASE WHEN cd_situ_recur_orc = 1 THEN 'Orçamento com Saldo'
						     WHEN cd_situ_recur_orc = 2 THEN 'Orçamento realizado e sem Saldo'
						     WHEN cd_situ_recur_orc = 3 THEN 'Orçamento cancelado'
						     WHEN cd_situ_recur_orc = 4 THEN 'Orçamento glosado'
					   	END	as nm_situ_recur_orc,

					   	cd_vlnt_resp_incl,
					   	obs

				from  tb_orc_dps
				where cd_situ_recur_orc between :cd_situ_recur_orc1 and :cd_situ_recur_orc2
				order by seql_orcID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_situ_recur_orc1', $this->__get('cd_situ_recur_orc1'));
		$stmt->bindValue('cd_situ_recur_orc2', $this->__get('cd_situ_recur_orc2'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosOrcDps2


// ====================================================== //

	public function insertOrcDps() {
		$query = "
				insert into tb_orc_dps
				(dt_recur_orc,
				dt_venc_recur_orc,
				vlr_recur_orc,              
				vlr_sdo_recur_orc,              
				cd_situ_recur_orc,        
				cd_vlnt_resp_incl,        
				ts_incl,
				obs)

				values 

				(str_to_date(:dt_recur_orc, '%d/%m/%Y'),   
				str_to_date(:dt_venc_recur_orc, '%d/%m/%Y'),   
				:vlr_recur_orc,              
				:vlr_sdo_recur_orc,              
				:cd_situ_recur_orc,        
				:cd_vlnt_resp_incl,        
				CURRENT_TIMESTAMP,
				:obs)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('dt_recur_orc', $this->__get('dt_recur_orc'));             
		$stmt->bindValue('dt_venc_recur_orc', $this->__get('dt_venc_recur_orc'));             
		$stmt->bindValue('vlr_recur_orc', $this->__get('vlr_recur_orc'));
		$stmt->bindValue('vlr_sdo_recur_orc', $this->__get('vlr_sdo_recur_orc'));
		$stmt->bindValue('cd_situ_recur_orc', $this->__get('cd_situ_recur_orc'));
		$stmt->bindValue('cd_vlnt_resp_incl', $this->__get('cd_vlnt_resp_incl'));
		$stmt->bindValue('obs', $this->__get('obs'));
		$stmt->execute();

		return $this;

	} // Fim function insertOrcDps

// =================================================== //

	public function getSequencial() {
		$query = "
				select max(seql_orcID) as seql_max
				from  tb_orc_dps";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		
		$seql = $stmt->fetch(\PDO::FETCH_ASSOC);		

		$this->__set ('seql_max', $seql['seql_max']);
	
	} // Fim function getSequencial

// =================================================== //

	public function updateSituacaoOrcDps() {
		$query = "
				update tb_orc_dps
				   set cd_situ_recur_orc = :cd_situ_recur_orc,
				   	   ts_incl           = CURRENT_TIMESTAMP
				where  seql_orcID = :seql_orc";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('seql_orc', $this->__get('seql_orc'));             
		$stmt->bindValue('cd_situ_recur_orc', $this->__get('cd_situ_recur_orc'));
		$stmt->execute();

		return $this;

	} // Fim function updateSituacaoOrcDps

// =================================================== //

	public function updateSaldoOrcDps() {
		$query = "
				update tb_orc_dps
				   set vlr_sdo_recur_orc = :vlr_sdo_recur_orc,
				   	   ts_incl           = CURRENT_TIMESTAMP
				where  seql_orcID = :seql_orc";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('seql_orc', $this->__get('seql_orc'));             
		$stmt->bindValue('vlr_sdo_recur_orc', $this->__get('vlr_sdo_recur_orc'));
		$stmt->execute();

		return $this;

	} // Fim function updateSaldoOrcDps

// =================================================== //

	public function updateSaldoSituacaoOrcDps() {
		$query = "
				update tb_orc_dps
				   set vlr_sdo_recur_orc = :vlr_sdo_recur_orc,
				       cd_situ_recur_orc = :cd_situ_recur_orc,				   
				   	   ts_incl           = CURRENT_TIMESTAMP
				where  seql_orcID = :seql_orc";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('seql_orc', $this->__get('seql_orc'));             
		$stmt->bindValue('vlr_sdo_recur_orc', $this->__get('vlr_sdo_recur_orc'));
		$stmt->bindValue('cd_situ_recur_orc', $this->__get('cd_situ_recur_orc'));
		$stmt->execute();

		return $this;

	} // Fim function updateSaldoSituacaoOrcDps

// =================================================== //

	public function updateValorObsOrcDps() {
		$query = "
				update tb_orc_dps
				   set dt_recur_orc      = str_to_date(:dt_recur_orc, '%d/%m/%Y'),   
				       dt_venc_recur_orc = str_to_date(:dt_venc_recur_orc, '%d/%m/%Y'),   
				   	   vlr_recur_orc     = :vlr_recur_orc,
				       vlr_sdo_recur_orc = :vlr_sdo_recur_orc,
				   	   ts_incl           = CURRENT_TIMESTAMP,
				   	   obs               = :obs
				where  seql_orcID = :seql_orc";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('seql_orc', $this->__get('seql_orc'));             
		$stmt->bindValue('dt_recur_orc', $this->__get('dt_recur_orc'));
		$stmt->bindValue('dt_venc_recur_orc', $this->__get('dt_venc_recur_orc'));
		$stmt->bindValue('vlr_recur_orc', $this->__get('vlr_recur_orc'));
		$stmt->bindValue('vlr_sdo_recur_orc', $this->__get('vlr_sdo_recur_orc'));
		$stmt->bindValue('obs', $this->__get('obs'));
		$stmt->execute();

		return $this;

	} // Fim function updateValorObsOrcDps

 // =================================================== //

	public function getSaldoOrcDps() {
		$query = "
				select sum(vlr_sdo_recur_orc) as saldo_atual_orc
				from   tb_orc_dps
				where  cd_situ_recur_orc = 1";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);		

	} // Fim function getSaldoOrcDps


}	// Fim da Classe

?>