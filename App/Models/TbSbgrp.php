<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbSbgrp extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_grpID;
	private $cd_sbgrpID;
	private $nm_sbgrp;
	private $dsc_atvd;
	private $cd_reg_adm;
	private $cd_horario_atu;
	private $dt_cric_sbgrp;
	private $dt_enct_sbgrp;
	private $cd_est_sbgrp;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function getNomeSubrupoInclusao() {

		$query = "
				select count(*) as qtde
					from tb_sbgrp
					where cd_grpID = :cd_grp
					and   nm_sbgrp like :nm_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp',$this->__get('cdGrupo_pesq'));
		$stmt->bindValue(':nm_sbgrp','%'.$this->__get('nomeSubgrupo_pesq').'%');
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getNomeGrupoInclusao

// =================================================== //

	public function obtemNrMaxSbgrupo() {
		$query = "
				select max(cd_sbgrpID) as max_seql
				from tb_sbgrp
				where cd_grpID = :cd_grp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('cdGrupo_insere'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// =================================================== //

	public function insereSubgrupo() {
		$query = "
				insert into tb_sbgrp
				(cd_grpID,
				cd_sbgrpID,
				nm_sbgrp,
				dsc_atvd,
				cd_reg_adm,
				dsc_horario_atu,
				dt_cric_sbgrp,
				dt_enct_sbgrp,
				cd_est_sbgrp)

				values 

				(:cd_grp, 
				:cd_sbgrp,
				:nm_sbgrp,
				:dsc_atvd,
				:cd_reg_adm,
				:dsc_horario_atu,
				now(),
				str_to_date('31/12/9998', '%d/%m/%Y'),
				1)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('cdGrupo_insere'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('cdSubgrupo_insere'));
		$stmt->bindValue(':nm_sbgrp', $this->__get('nomeSubgrupo_insere'));
		$stmt->bindValue(':dsc_atvd', $this->__get('descricaoSubgrupo_insere'));
		$stmt->bindValue(':cd_reg_adm', $this->__get('ra_insere'));
		$stmt->bindValue(':dsc_horario_atu', $this->__get('descricaoHorario_insere'));
		$stmt->execute();

		return $this;
	}	// Fim function insereSubgrupo

// =================================================== //

	public function getDadosSubgrupo() {
		$query = "
				select 	a.cd_grpID as cod_grupo,
						a.nm_grp as nome_grupo,
						b.cd_sbgrpID as cod_subgrupo,
						b.nm_sbgrp as nome_subgrupo,
						b.dsc_atvd as desc_subgrupo,
						b.cd_reg_adm as ra_subgrupo,
						b.dsc_horario_atu as desc_horario,
						'altera_ra'
				from tb_grp a,
					 tb_sbgrp b
				where b.cd_grpID   = :cd_grp
				and   b.cd_sbgrpID = :cd_sbgrp
				and   b.cd_grpID   = a.cd_grpID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// =================================================== //

	public function getQtdSubgrupoVinculoFamilia() {

		$query = "
				select count(*) as qtde
					from tb_vncl_fml_sbgrp
					where cd_grpID   = :cd_grp
					and   cd_sbgrpID = :cd_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getQtdSubgrupoVinculoFamilia

// =================================================== //

	public function getQtdSubgrupoFinanceiro() {

		$query = "
				select count(*) as qtde
					from tb_pedido_recur_finan
					where cd_grpID   = :cd_grp
					and   cd_sbgrpID = :cd_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getQtdSubgrupoFinanceiro

// =================================================== //

	public function getNomeSubrupoAlteracao() {

		$query = "
				select count(*) as qtde
					from tb_sbgrp
					where cd_grpID   =  :cd_grp
					and   cd_sbgrpID <> :cd_sbgrp
					and   nm_sbgrp like :nm_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp',$this->__get('codGrupo_pesq'));
		$stmt->bindValue(':cd_sbgrp',$this->__get('codSubgrupo_pesq'));
		$stmt->bindValue(':nm_sbgrp','%'.$this->__get('nomeSubgrupo_pesq').'%');
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getNomeSubrupoAlteracao

// =================================================== //
	
	public function alteraSubgrupo() {
		$query = "
				update tb_sbgrp
				set nm_sbgrp        = :nm_sbgrp,
					dsc_atvd        = :dsc_atvd,
					cd_reg_adm      = :cd_reg_adm,
					dsc_horario_atu = :dsc_horario_atu
				where cd_grpID   = :cd_grp
				and   cd_sbgrpID = :cd_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_altera'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_altera'));
		$stmt->bindValue(':nm_sbgrp', $this->__get('nomeSubgrupo_altera'));
		$stmt->bindValue(':dsc_atvd', $this->__get('descricaoSubgrupo_altera'));
		$stmt->bindValue(':cd_reg_adm', $this->__get('ra_altera'));
		$stmt->bindValue(':dsc_horario_atu', $this->__get('descricaoHorario_altera'));
		$stmt->execute();

		return $this;
	}	// Fim function alteraSubgrupo

// =================================================== //
	
	public function encerraSubgrupo() {
		$query = "
				update tb_sbgrp
				set dt_enct_sbgrp = now(),
					cd_est_sbgrp  = 2
				where cd_grpID   = :cd_grp
				and   cd_sbgrpID = :cd_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_altera'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_altera'));
		$stmt->execute();

		return $this;
	}	// Fim function encerraSubgrupo

// =================================================== //

	public function getDadosSubgrupoAll() {
		$query = "
				select 	a.cd_grpID as cod_grupo,
						a.nm_grp as nome_grupo,
						b.cd_sbgrpID as cod_subgrupo,
						b.nm_sbgrp as nome_subgrupo,
						b.dsc_atvd as desc_subgrupo,
						b.cd_reg_adm as ra_subgrupo,
						b.dsc_horario_atu as desc_horario_subgrupo,
						DATE_FORMAT(b.dt_cric_sbgrp, '%d/%m/%Y') as data_criacao_subgrupo,
						DATE_FORMAT(b.dt_enct_sbgrp, '%d/%m/%Y') as data_encerramento_subgrupo,
						b.cd_est_sbgrp, case  when b.cd_est_sbgrp = 1 THEN 'Ativo'
										 	  when b.cd_est_sbgrp = 2 THEN 'Inativo'
										end	as cod_estado_subgrupo
				from tb_grp a,
					 tb_sbgrp b
				where b.cd_grpID   = :cd_grp
				and   b.cd_sbgrpID = :cd_sbgrp
				and   b.cd_grpID   = a.cd_grpID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}


} 	// FIm da classe 
?>