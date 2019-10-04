<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbRegAdm extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_reg_admID;
	private $nm_reg_adm;
	private $dt_inc_vgc_reg_adm;
	private $dt_fim_vgc_reg_adm;
	private $cd_est_reg_adm;
	private $uf_reg_adm;


	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

	public function getNomeRA() {

		//echo $this->__get('nomeRA_pesq');

		$query = "
				select count(*) as qtde
					from tb_reg_adm
					where nm_reg_adm      = :nm_reg_adm
					and   uf_reg_adm      = :uf_reg_adm
					and   cd_est_reg_adm  = :cd_est_reg_adm";
		$stmt = $this->db->prepare($query);
		//$stmt->bindValue(':nm_reg_adm', '%'.$this->__get('nomeRA_pesq').'%');
		$stmt->bindValue(':nm_reg_adm', $this->__get('nomeRA_pesq'));
		$stmt->bindValue(':uf_reg_adm', $this->__get('UF_pesq'));
		$stmt->bindValue(':cd_est_reg_adm', 1);
		$stmt->execute();
		
		$nr_registros = $stmt->fetch(\PDO::FETCH_ASSOC);	
		
		$this->__set ('qtde_nome_ra', $nr_registros['qtde']);

	}	//	Fim function getNomeRA

	// Incluir dados no banco de dados (persistência)
	public function insereRA() {
		$query = "
				insert into tb_reg_adm 
				(nm_reg_adm, 
				dt_inc_vgc_reg_adm, 
				dt_fim_vgc_reg_adm, 
				cd_est_reg_adm,
				uf_reg_adm) 
				values 
				(:nm_reg_adm, 
				now(),
				str_to_date('31/12/9998', '%d/%m/%Y'),
				:cd_est_reg_adm, 
				:uf_reg_adm)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_reg_adm', $this->__get('nomeRA_insere'));
		$stmt->bindValue(':cd_est_reg_adm', 1);
		$stmt->bindValue(':uf_reg_adm', $this->__get('ufRA_insere'));
		$stmt->execute();

		return $this;
	}	// Fim function InsereRA

	public function getDadosRAAll() {
		$query = "
				select 	cd_reg_admID as cod_ra,
						nm_reg_adm as nome_ra,
						dt_inc_vgc_reg_adm as dt_inc_ra,
						dt_fim_vgc_reg_adm as dt_fim_ra,
						cd_est_reg_adm as cd_est_ra,
						uf_reg_adm as uf_ra
					from tb_reg_adm
					where cd_est_reg_adm = :cd_est_reg_adm
					order by nm_reg_adm";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_est_reg_adm', 1);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosRAAll

	public function getDadosRAAll2() {
		$query = "
				select 	nm_reg_adm as nome_ra,
						uf_reg_adm as uf_ra
					from tb_reg_adm
					where cd_reg_admID = :cd_reg_adm";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_reg_adm', $this->__get('cd_reg_adm'));
		$stmt->execute();

		$registro = $stmt->fetch(\PDO::FETCH_ASSOC);	

		$this->__set ('nome_ra', $registro['nome_ra']);
		$this->__set ('uf_ra', $registro['uf_ra']);

	}	//	Fim function getDadosRAAll

	// Alterar dados no banco de dados (persistência)
	public function alteraRA() {
		$query = "
				update tb_reg_adm 
				set nm_reg_adm = :nm_reg_adm, 
				uf_reg_adm = :uf_reg_adm 
				where cd_reg_admID = :cd_reg_adm";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_reg_adm', $this->__get('cdRA_altera'));
		$stmt->bindValue(':nm_reg_adm', $this->__get('nomeRA_altera'));
		$stmt->bindValue(':uf_reg_adm', $this->__get('ufRA_altera'));
		$stmt->execute();

		return $this;
		
	}	// Fim function alteraRA

	// Alterar dados no banco de dados (persistência)
	public function encerraRA() {
		$query = "
				update tb_reg_adm 
				set cd_est_reg_adm = :cd_est_reg_adm,
					dt_fim_vgc_reg_adm = now()
				where cd_reg_admID = :cd_reg_adm";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_reg_adm', $this->__get('cdRA_altera'));
		$stmt->bindValue(':cd_est_reg_adm', 2);
		//$stmt->bindValue(':dt_fim_vgc_reg_adm', str_to_date(now(), "%d/%m/%Y"));
		$stmt->execute();

		return $this;
		
	}	// Fim function encerraRA

	public function consultaOutrasTabelasRA() {
		$query = "
			select  count(*) as qtde
			from 	tb_fml
			where 	cd_reg_adm = :cd_reg_adm
			union all
			select  count(*) as qtde
			from	tb_sbgrp
			where 	cd_reg_adm = :cd_reg_adm
			union all
			select	count(*) as qtde
			from    tb_vlnt
			where 	cd_reg_adm = :cd_reg_adm";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_reg_adm', $this->__get('cdRA_altera'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function consultaOutrasTabelasRA


	public function getDadosRAAll3() {
		$query = "
				select 	cd_reg_admID as cod_ra,
						nm_reg_adm as nome_ra,
						dt_inc_vgc_reg_adm as dt_inc_ra,
						dt_fim_vgc_reg_adm as dt_fim_ra,
						cd_est_reg_adm as cd_est_ra,
						uf_reg_adm as uf_ra
					from tb_reg_adm					
					order by nm_reg_adm";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosRAAll3

	public function getDadosRAAll4() {
		$query = "
				select 	cd_reg_admID as cod_ra,
						nm_reg_adm as nome_ra,
						dt_inc_vgc_reg_adm as dt_inc_ra,
						dt_fim_vgc_reg_adm as dt_fim_ra,
						cd_est_reg_adm as cd_est_ra,
						uf_reg_adm as uf_ra
				from tb_reg_adm					
				where cd_reg_admID = :cd_reg_adm";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_reg_adm', $this->__get('cd_reg_adm'));
		$stmt->execute();

		$registro = $stmt->fetch(\PDO::FETCH_ASSOC);	

		$this->__set ('cod_ra', $registro['cod_ra']);
		$this->__set ('nome_ra', $registro['nome_ra']);
		$this->__set ('dt_inc_ra', $registro['dt_inc_ra']);
		$this->__set ('dt_fim_ra', $registro['dt_fim_ra']);
		$this->__set ('cd_est_ra', $registro['cd_est_ra']);
		$this->__set ('uf_ra', $registro['uf_ra']);

	}	//	Fim function getDadosRAAll3


} 	// FIm da classe
?>