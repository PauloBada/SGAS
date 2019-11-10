<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 28/08/2019
   Objetivo:  Contém os Sqls para a tabela tb_vncl_vlnt_acomp_fml
*/

namespace App\Models;

use MF\Model\Model;

class TbFmlPedidoFinan extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_fmlID;
	private $cd_grpID;
	private $cd_sbgrpID;
	private $seql_pedido_finanID;
	
	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function insertFmlPedidoFinan() {

		$query = "
				insert into tb_fml_pedido_finan
				(cd_fmlID,
				cd_grpID,
				cd_sbgrpID,
				seql_pedido_finanID)

				values 

				(:cd_fml,
				:cd_grp,
				:cd_sbgrp,
				:seql_pedido_finan)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function insertFmlPedidoFinan

// ====================================================== //

	public function deleteFmlPedidoFinan() {
		$query = "
				delete
				from  tb_fml_pedido_finan
				where cd_fmlID            = :cd_fml
				and   cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   seql_pedido_finanID = :seql_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->execute();

		return $this;	

	}	//	Fim function deleteFmlPedidoFinan

// ====================================================== //
                    
	public function getFmlPedidoFinan() {
		$query = "
				select a.cd_fmlID as cd_fml,
					   b.nm_grp_fmlr as nm_grp_fmlr
				from  tb_fml_pedido_finan a,
				      tb_fml b
				where a.cd_grpID            = :cd_grp
				and   a.cd_sbgrpID          = :cd_sbgrp
				and   a.seql_pedido_finanID = :seql_pedido_finan
				and   a.cd_fmlID            = b.cd_fmlID
				order by b.nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	
	}	//	Fim function getFmlPedidoFinan

// ====================================================== //

	public function getQtdFmlPedidoFinan() {
		$query = "
				select count(*) as qtde
				from tb_fml_pedido_finan
				where cd_grpID            = :cd_grp
				and   cd_sbgrpID          = :cd_sbgrp
				and   seql_pedido_finanID = :seql_pedido_finan";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('seql_pedido_finan', $this->__get('seql_pedido_finan'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getQtdFmlPedidoFinan


} 	// FIm da classe 
?>