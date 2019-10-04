<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbGrp extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_grpID;
	private $nm_grp;
	private $cd_semn_atu;
	private $dt_cric_grp;
	private $dt_enct_grp;
	private $cd_est_grp;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function getNomeGrupoInclusao() {

		$query = "
				select count(*) as qtde
					from tb_grp
					where nm_grp like :nm_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_grp','%'.$this->__get('nomeGrupo_pesq').'%');
		$stmt->execute();
		
		//$nr_registros = $stmt->fetch(\PDO::FETCH_ASSOC);	
		//$this->__set ('qtde_nome_item', $nr_registros['qtde']);

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getNomeGrupoInclusao

// =================================================== //

	public function insereGrupo() {
		$query = "
				insert into tb_grp
				(nm_grp,
				cd_semn_atu,
				dt_cric_grp,
				dt_enct_grp,
				cd_est_grp) 

				values 

				(:nm_grp, 
				:cd_semn_atu,
				now(),
				str_to_date('31/12/9998', '%d/%m/%Y'),
				1)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_grp', $this->__get('nomeGrupo_insere'));
		$stmt->bindValue(':cd_semn_atu', $this->__get('semana_insere'));
		$stmt->execute();

		return $this;
	}	// Fim function InsereGrupo

// =================================================== //

	public function getDadosGrupoAll() {
		$query = "
				select 	cd_grpID as cod_grupo,
						nm_grp as nome_grupo,
						cd_semn_atu as cod_semana
					from tb_grp
					where cd_est_grp = 1
					order by nm_grp";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosGrupoAll

// =================================================== //

	public function getDadosGrupo() {
		$query = "
				select 	cd_grpID as cod_grupo,
						nm_grp as nome_grupo,
						cd_semn_atu as cod_semana
					from tb_grp
					where cd_grpID = :cd_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('cd_grp'));		
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosGrupo

// =================================================== //

	public function getQtdGrupoVinculoVoluntario() {

		$query = "
				select count(*) as qtde
					from tb_vncl_vlnt_grp
					where cd_grpID = :cd_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getQtdGrupoVinculoVoluntario

// =================================================== //

	public function getQtdGrupoVinculoSubgrupo() {

		$query = "
				select count(*) as qtde
					from tb_sbgr
					where cd_grpID = :cd_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getQtdGrupoVinculoSubgrupo

// =================================================== //

	// Alterar dados no banco de dados (persistência)
	public function alteraGrupo() {
		$query = "
				update tb_grp
				set nm_grp      = :nm_grp, 
				cd_semn_atu     = :cd_semn_atu
				where cd_grpID  = :cd_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('cdGrupo_altera'));
		$stmt->bindValue(':nm_grp', $this->__get('nomeGrupo_altera'));
		$stmt->bindValue(':cd_semn_atu', $this->__get('semanaAtuacao_altera'));
		$stmt->execute();

		return $this;
		
	}	// Fim function alteraGrupo

// =================================================== //

	public function encerraGrupo() {
		$query = "
				update tb_grp
				set cd_est_grp = 2,
					dt_enct_grp = now()
				where cd_grpID  = :cd_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('cdGrupo_altera'));
		$stmt->execute();

		return $this;
		
	}	// Fim function encerraGrupo

// =================================================== //

	public function getDadosGrupoAll2() {
		$query = "
				select 	cd_grpID as cod_grupo,
						nm_grp as nome_grupo,
						cd_semn_atu as cod_semana
					from tb_grp
					order by nm_grp";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosGrupoAll2


// =================================================== //

	public function getDadosGrupoAll3() {
		$query = "
				select 	cd_grpID as cod_grupo,
						nm_grp as nome_grupo,
						cd_semn_atu as cod_semana,
						DATE_FORMAT(dt_cric_grp, '%d/%m/%Y') as data_criacao,
						DATE_FORMAT(dt_enct_grp, '%d/%m/%Y') as data_encerramento,
						cd_est_grp, case when cd_est_grp = 1 THEN 'Ativo'
										 when cd_est_grp = 2 THEN 'Inativo'
									end	as codigo_estado
					from tb_grp
					where cd_grpID = :cd_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('cd_grp'));		
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosGrupoAll3

} 	// FIm da classe
?>