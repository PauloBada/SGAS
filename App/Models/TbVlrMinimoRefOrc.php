<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 01/11/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbVlrMinimoRefOrc extends Model {
	
	// colunas da tabela
	private $seql_vlr_minimo_refID;          
	private $vlr_minimo_ref;
	private $dt_inc_vgc;              
	private $dt_fim_vgc;              
	private $obs;        
	private $cd_vlnt_resp_incl;        
	private $cd_est_vlr_minimo_ref;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}


// ====================================================== //

	public function getDadosVMR() {
		$query = "
				select 	seql_vlr_minimo_refID as seql_vlr_minimo_ref,
						vlr_minimo_ref,
					
						dt_inc_vgc,
						DATE_FORMAT(dt_inc_vgc, '%d/%m/%Y') as dt_inc_vgc_format,
						
						dt_fim_vgc,
						DATE_FORMAT(dt_fim_vgc, '%d/%m/%Y') as dt_fim_vgc_format,
						
						obs,
						cd_vlnt_resp_incl,
					
						cd_est_vlr_minimo_ref,
						CASE WHEN cd_est_vlr_minimo_ref = 1 THEN 'Vigente'
						     WHEN cd_est_vlr_minimo_ref = 2 THEN 'Não Vigente'
					   	END	as nm_est_vlr_minimo_ref_format

				from  tb_vlr_minimo_ref_orc
				where seql_vlr_minimo_refID = :seql_vlr_minimo_ref";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('seql_vlr_minimo_ref', $this->__get('seql_vlr_minimo_ref'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosVMR

// ====================================================== //

	public function getDadosVMR2() {
		$query = "
				select 	seql_vlr_minimo_refID as seql_vlr_minimo_ref,
						vlr_minimo_ref,
					
						dt_inc_vgc,
						DATE_FORMAT(dt_inc_vgc, '%d/%m/%Y') as dt_inc_vgc_format,
						
						dt_fim_vgc,
						DATE_FORMAT(dt_fim_vgc, '%d/%m/%Y') as dt_fim_vgc_format,
						
						obs,
						cd_vlnt_resp_incl,
					
						cd_est_vlr_minimo_ref,
						CASE WHEN cd_est_vlr_minimo_ref = 1 THEN 'Vigente'
						     WHEN cd_est_vlr_minimo_ref = 2 THEN 'Não Vigente'
					   	END	as nm_est_vlr_minimo_ref_format

				from  tb_vlr_minimo_ref_orc
				where cd_est_vlr_minimo_ref between :cd_est_vlr_minimo_ref1 and :cd_est_vlr_minimo_ref2
				order by seql_vlr_minimo_refID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_est_vlr_minimo_ref1', $this->__get('cd_est_vlr_minimo_ref1'));
		$stmt->bindValue('cd_est_vlr_minimo_ref2', $this->__get('cd_est_vlr_minimo_ref2'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	} // Fim function getDadosVMR2


// ====================================================== //

	public function insertVMR() {
		$query = "
				insert into tb_vlr_minimo_ref_orc
				(vlr_minimo_ref,
				dt_inc_vgc,              
				dt_fim_vgc,              
				obs,        
				cd_vlnt_resp_incl,        
				cd_est_vlr_minimo_ref)

				values 

				(:vlr_minimo_ref,
				now(),              
				str_to_date('31/12/9998', '%d/%m/%Y'),
				:obs,        
				:cd_vlnt_resp_incl,        
				:cd_est_vlr_minimo_ref)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('vlr_minimo_ref', $this->__get('vlr_minimo_ref'));             
		$stmt->bindValue('obs', $this->__get('obs'));
		$stmt->bindValue('cd_vlnt_resp_incl', $this->__get('cd_vlnt_resp_incl'));
		$stmt->bindValue('cd_est_vlr_minimo_ref', 1);
		$stmt->execute();

		return $this;

	} // Fim function insertVMR

// =================================================== //

	public function getSequencial() {
		$query = "
				select max(seql_vlr_minimo_refID) as seql_max
				from  tb_vlr_minimo_ref_orc";
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		
		$seql = $stmt->fetch(\PDO::FETCH_ASSOC);		

		$this->__set ('seql_max', $seql['seql_max']);
	
	} // Fim function getSequencial

// =================================================== //

	public function updateVMR() {
		// Para gravar nulo nos campos quando não houver informação
		
		$query = "
				update tb_vlr_minimo_ref_orc
				   set dt_fim_vgc            = str_to_date(:dt_fim_vgc, '%d/%m/%Y'),
				       cd_est_vlr_minimo_ref = :cd_est_vlr_minimo_ref
				where  seql_vlr_minimo_refID = :seql_vlr_minimo_ref";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('seql_vlr_minimo_ref', $this->__get('seql_vlr_minimo_ref'));             
		$stmt->bindValue('dt_fim_vgc', $this->__get('dt_fim_vgc'));
		$stmt->bindValue('cd_est_vlr_minimo_ref', 2);
		$stmt->execute();

		return $this;

	} // Fim function updateVMR


}	// Fim da Classe

?>