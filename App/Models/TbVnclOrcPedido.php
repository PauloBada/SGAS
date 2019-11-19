<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 28/08/2019
   Objetivo:  Contém os Sqls para a tabela tb_vncl_vlnt_acomp_fml
*/

namespace App\Models;

use MF\Model\Model;

class TbVnclOrcPedido extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_grpID;
	private $cd_sbgrpID;
	private $seql_pedido_finanID;
	private $seql_orcID;
	private $vlr_orc_pedido;
	private $cd_est_vncl;
	
	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function insertVnclOrcPedido() {

		$query = "
				insert into tb_vncl_orc_pedido
				(cd_grpID,
				cd_sbgrpID,
				seql_pedido_finanID,
				seql_orcID,
				vlr_orc_pedido,
				cd_est_vncl)

				values 

				(:cd_grp,
				:cd_sbgrp,
				:seql_pedido_finan,
				:seql_orc,
				:vlr_orc_pedido,
				:cd_est_vncl)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('seql_orc', $this->__get('seql_orc'));
		$stmt->bindValue('vlr_orc_pedido', $this->__get('vlr_orc_pedido'));
		$stmt->bindValue('cd_est_vncl', 1);		//1-Vigente
		$stmt->execute();
		
		return $this;	

	}	//	Fim function insertVnclOrcPedido

// =================================================== //

	public function getVnclOrcPedido() {

		$query = "
				select seql_orcID as seql_orc,
				       vlr_orc_pedido
				from   tb_vncl_orc_pedido
				where  cd_grpID            = :cd_grp
				and    cd_sbgrpID          = :cd_sbgrp
				and    seql_pedido_finanID = :seql_pedido_finan
				and    cd_est_vncl         = :cd_est_vncl
				order by seql_orcID desc";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_est_vncl', 1);		//1-Vigente
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getVnclOrcPedido

// =================================================== //

	public function updateVnclOrcPedido() {

		$query = "
				update tb_vncl_orc_pedido
				set    cd_est_vncl         = :cd_est_vncl
				where  cd_grpID            = :cd_grp
				and    cd_sbgrpID          = :cd_sbgrp
				and    seql_pedido_finanID = :seql_pedido_finan
				and    seql_orcID          = :seql_orc";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('seql_orc', $this->__get('seql_orc'));
		$stmt->bindValue('cd_est_vncl', 2);		//2-Cancelado
		$stmt->execute();
		
		return $this;	

	}	//	Fim function updateVnclOrcPedido

// =================================================== //

	public function updateVnclOrcPedidoAll() {

		$query = "
				update tb_vncl_orc_pedido
				set    cd_est_vncl         = :cd_est_vncl
				where  cd_grpID            = :cd_grp
				and    cd_sbgrpID          = :cd_sbgrp
				and    seql_pedido_finanID = :seql_pedido_finan
				and    cd_est_vncl         = :cd_est_vncl_1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->bindValue('cd_est_vncl', $this->__get('cd_est_vncl'));
		$stmt->bindValue('cd_est_vncl_1', $this->__get('cd_est_vncl_1'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function updateVnclOrcPedidoAll


} 	// FIm da classe 
?>