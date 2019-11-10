<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbVnclFmlSbgrp extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_grpID;
	private $cd_sbgrpID;
	private $cd_fmlID;
	private $seql_vnclID;
	private $dt_inc_vncl;
	private $dt_fim_vncl;
	private $cd_est_vncl;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function getQtdSubgrupoVinculoFamilia() {

		$query = "
				select count(*) as qtde
					from tb_vncl_fml_sbgrp	
					where cd_fmlID    = :cd_fml
					and   cd_est_vncl = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia_pesq'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getQtdSubgrupoVinculoFamilia

// =================================================== //

	public function getDadosVinculoFamilia() {

		$query = "
				select 	cd_grpID,
						cd_sbgrpID,
						seql_vnclID
					from tb_vncl_fml_sbgrp	
					where cd_fmlID    = :cd_fml
					and   cd_est_vncl = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia_pesq'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosVinculoFamilia

// =================================================== //

	public function insertVinculo() {

		$this->getProximoSequencial();

		$query = "
				insert into tb_vncl_fml_sbgrp
				(cd_grpID,
				cd_sbgrpID,
				cd_fmlID,
				seql_vnclID,
				dt_inc_vncl,
				dt_fim_vncl,
				cd_est_vncl)

				values 

				(:cd_grp,
				:cd_sbgrp,
				:cd_fml,
				:seql_vncl,
				now(),
				str_to_date('31/12/9998', '%d/%m/%Y'),
				1)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo'));
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt->bindValue(':seql_vncl', $this->__get('seql_max'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function insereVinculo

// =================================================== //

	public function getProximoSequencial() {
		$query0 = "select count(*) + 1 as qtde
					from  tb_vncl_fml_sbgrp
					where cd_grpID    = :cd_grp
					and   cd_sbgrpID  = :cd_sbgrp
					and   cd_fmlID    = :cd_fml";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt0->bindValue(':cd_sbgrp', $this->__get('codSubgrupo'));
		$stmt0->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "select max(seql_vnclID) + 1 as qtde
					from  tb_vncl_fml_sbgrp
					where cd_grpID    = :cd_grp
					and   cd_sbgrpID  = :cd_sbgrp
					and   cd_fmlID    = :cd_fml";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue(':cd_grp', $this->__get('codGrupo'));
			$stmt1->bindValue(':cd_sbgrp', $this->__get('codSubgrupo'));
			$stmt1->bindValue(':cd_fml', $this->__get('codFamilia'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_max', $nr_registros_1['qtde']);
		}
	}

// =================================================== //

	public function encerraVFS() {

		$query = "
				update tb_vncl_fml_sbgrp
				set    cd_est_vncl  = 2,
					   dt_fim_vncl = now()
				where  cd_grpID    = :cd_grp
				and    cd_sbgrpID  = :cd_sbgrp
				and    cd_fmlID    = :cd_fml
				and    seql_vnclID = :seql_vncl";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo'));
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt->bindValue(':seql_vncl', $this->__get('sequencial'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function encerraVFS


// =================================================== //

	public function getDadosVFSAll() {

		$query = "
				select a.cd_fmlID as codigo_familia,
					   a.seql_vnclID as sequencial_base,					
					   DATE_FORMAT(a.dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
					   DATE_FORMAT(a.dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
					   case when a.cd_est_vncl = 1 THEN 'Ativo'
						    when a.cd_est_vncl = 2 THEN 'Encerrado'
					   end as estado_vinculo,
                       case when b.cd_est_situ_fml = 1 THEN 'Aguardando definição de grupo/subgrupo'
						    when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem'
							when b.cd_est_situ_fml = 3 THEN 'Em atendimento pela DPS'
							when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
							when b.cd_est_situ_fml = 5 THEN 'Não atendida por não encontrado endereço'
                            when b.cd_est_situ_fml = 6 THEN 'Não atendida por não necessitar'
					   end as estado_situacao_familia
					from tb_vncl_fml_sbgrp a,
					     tb_fml b
					where a.cd_grpID   = :cd_grp
					and   a.cd_sbgrpID = :cd_sbgrp
					and   (a.dt_inc_vncl between str_to_date(:dt_inc_vncl, '%d/%m/%Y') and 
							   				     str_to_date(:dt_fim_vncl, '%d/%m/%Y')
					or    a.dt_fim_vncl > str_to_date(:dt_fim_vncl, '%d/%m/%Y'))
					and   a.cd_fmlID   = b.cd_fmlID
					order by a.cd_grpID, a.cd_sbgrpID, a.cd_fmlID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->bindValue('cd_sbgrp', $this->__get('codSubgrupo_pesq'));
		$stmt->bindValue('dt_inc_vncl', $this->__get('dataInicio_pesq'));
		$stmt->bindValue('dt_fim_vncl', $this->__get('dataFim_pesq'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosVFSAll


} 	// FIm da classe 
?>