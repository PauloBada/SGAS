<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 28/08/2019
   Objetivo:  Contém os Sqls para a tabela tb_vncl_vlnt_acomp_fml
*/

namespace App\Models;

use MF\Model\Model;

class TbVnclVlntAcompFml extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_vltnID;
	private $cd_fmlID;
	private $seql_acompID;
	private $cd_atua_vlnt_acomp;
	private $ts_atua_vlnt_acomp;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function insertTVVAF() {

		$query = "
				insert into tb_vncl_vlnt_acomp_fml
				(cd_vlntID,
				cd_fmlID,
				seql_acompID,
				cd_atua_vlnt_acomp,
				ts_atua_vlnt_acomp)

				values 

				(:cd_vlnt,
				:cd_fml,
				:seql_acomp,
				:cd_atua_vlnt_acomp,
				CURRENT_TIMESTAMP)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue(':cd_atua_vlnt_acomp', $this->__get('cd_atua_vlnt_acomp'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function insertTVVAF

// ====================================================== //

	public function getQtdVinculoTriagemVisita() {
		$query = "
				select count(*) as qtde
				from tb_vncl_vlnt_acomp_fml 
				where cd_fmlID           = :cd_fml
				and   seql_acompID       = :seql_acomp
				and   cd_atua_vlnt_acomp = :cd_atua_vlnt_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_atua_vlnt_acomp', $this->__get('cd_atua_vlnt_acomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getVoluntariosVinculoAcomp() {
		$query = "
				select b.cd_vlntID as cd_vlnt, 
					   b.nm_vlnt as nm_vlnt,
					   a.cd_atua_vlnt_acomp as cd_atua_vlnt_acomp
				from  tb_vncl_vlnt_acomp_fml a,
				      tb_vlnt b
				where a.cd_fmlID           = :cd_fml
				and   a.seql_acompID       = :seql_acomp
				and   a.cd_vlntID          = b.cd_vlntID
				and   a.cd_atua_vlnt_acomp = :cd_atua_vlnt_acomp
				order by b.nm_vlnt";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_atua_vlnt_acomp', $this->__get('cd_atua_vlnt_acomp'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

// ====================================================== //

	public function getDadosVoluntarioVinculoAcomp() {
		$query = "
				select b.cd_vlntID as cd_vlnt, 
					   b.nm_vlnt as nm_vlnt,
					   a.cd_atua_vlnt_acomp as cd_atua_vlnt_acomp
				from  tb_vncl_vlnt_acomp_fml a,
				      tb_vlnt b
				where a.cd_vlntID    = :cd_vlnt
				and   a.cd_fmlID     = :cd_fml
				and   a.seql_acompID = :seql_acomp
				and   a.cd_vlntID    = b.cd_vlntID";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

// =================================================== //

	public function deleteTVVAF() {

		$query = "
				delete from	tb_vncl_vlnt_acomp_fml
				where   cd_vlntID    = :cd_vlnt
				and     cd_fmlID     = :cd_fml
				and     seql_acompID = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function deleteTVVAF

// ====================================================== //

	public function getAtuacaoVoluntarioAcomp() {
		$query = "
			select cd_atua_vlnt_acomp as cod_atuacao_acomp
			from  tb_vncl_vlnt_acomp_fml
			where cd_vlntID    = :cd_vlnt
			and   cd_fmlID     = :cd_fml
			and   seql_acompID = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

// ====================================================== //

	public function getQtdeVoluntarioAcomp() {
		$query = "
			select count(*) as qtde
			from  tb_vncl_vlnt_acomp_fml
			where cd_fmlID     = :cd_fml
			and   seql_acompID = :seql_acomp
			and   cd_atua_vlnt_acomp = 3";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

// ====================================================== //

	public function getQtdeVoluntarioAcompEspecifico() {
		$query = "
			select count(*) as qtde
			from  tb_vncl_vlnt_acomp_fml
			where cd_vlntID    = :cd_vlnt
			and   cd_fmlID     = :cd_fml
			and   seql_acompID = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

// ====================================================== //

	public function updateVoluntarioAcompEspecifico() {
		$query = "
			update tb_vncl_vlnt_acomp_fml
			set   ts_atua_vlnt_acomp = CURRENT_TIMESTAMP,
			      cd_atua_vlnt_acomp = :cd_atua_vlnt_acomp
			where cd_vlntID    = :cd_vlnt
			and   cd_fmlID     = :cd_fml
			and   seql_acompID = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_atua_vlnt_acomp', $this->__get('cd_atua_vlnt_acomp'));
		$stmt->execute();

		return $this;
	}

// ====================================================== //

	public function getQtdeVoluntarioAcompEspecificoRevisor() {
		$query = "
			select count(*) as qtde
			from  tb_vncl_vlnt_acomp_fml
			where cd_vlntID          =  :cd_vlnt
			and   cd_fmlID           =  :cd_fml
			and   seql_acompID       =  :seql_acomp
			and   cd_atua_vlnt_acomp <> :cd_atua_vlnt_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_atua_vlnt_acomp', $this->__get('cd_atua_vlnt_acomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

// ====================================================== //

	public function getVoluntariosVinculoAcompAll() {
		$query = "
				select b.cd_vlntID as cd_vlnt, 
					   b.nm_vlnt as nm_vlnt,
					   a.cd_atua_vlnt_acomp as cd_atua_vlnt_acomp
				from  tb_vncl_vlnt_acomp_fml a,
				      tb_vlnt b
				where a.cd_fmlID           = :cd_fml
				and   a.seql_acompID       = :seql_acomp
				and   a.cd_vlntID          = b.cd_vlntID
				order by b.nm_vlnt";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}



} 	// FIm da classe 
?>