<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbSbitemSuprimt extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_itemID;
	private $cd_subitemID;
	private $nm_subitem;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function getNomeSubitemInclusao() {

		$query = "
				select count(*) as qtde
					from tb_sbitem_suprimt
					where cd_itemID  = :cd_item
					and   nm_sbitem  = :nm_sbitem";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cdItem_pesq'));
		$stmt->bindValue(':nm_sbitem', $this->__get('nomeSubitem_pesq'));
		$stmt->execute();
		
		$nr_registros = $stmt->fetch(\PDO::FETCH_ASSOC);	
		
		$this->__set ('qtde_nome_subitem', $nr_registros['qtde']);

	}	//	Fim function getNomeSubitemInclusao

// =================================================== //
	
	public function insereSubitem() {
		$query = "
					insert into tb_sbitem_suprimt
					(cd_itemID,
					cd_sbitemID,
					nm_sbitem) 
					values 
					(:cd_item,
					:cd_sbitem,
					:nm_sbitem)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cdItem_insere'));
		$stmt->bindValue(':cd_sbitem', $this->__get('cdSubitem_insere')); 
		$stmt->bindValue(':nm_sbitem', $this->__get('nomeSubitem_insere'));
		$stmt->execute();

		return $this;
	}	// Fim function InsereSubitem

// =================================================== //

	public function obtemNrMaxSbitem() {
		$query = "
				select max(cd_sbitemID) as max_seql
				from tb_sbitem_suprimt
				where cd_itemID = :cd_item";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cdItem_insere'));
		$stmt->execute();

		$proximo_seql = $stmt->fetch(\PDO::FETCH_ASSOC);	
		
		$this->__set ('max_seql_grava', $proximo_seql['max_seql']);
	}

// =================================================== //

	public function getDadosSubitem() {
		$query = "
				select 	a.cd_itemID as cod_item,
						a.cd_sbitemID as cod_subitem,
						a.nm_sbitem as nome_subitem,
						b.nm_item as nome_item
					from  tb_sbitem_suprimt as a,
						  tb_item_suprimt as b
					where a.cd_itemID = :cd_item
					and   a.cd_sbitemID = :cd_sbitem
					and   a.cd_itemID   = b.cd_itemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cdItem_pesq'));
		$stmt->bindValue(':cd_sbitem', $this->__get('cdSubitem_pesq'));
		$stmt->execute();

		$registro = $stmt->fetch(\PDO::FETCH_ASSOC);	

		$this->__set ('cd_item', $registro['cod_item']);
		$this->__set ('cd_subitem', $registro['cod_subitem']);
		$this->__set ('nome_subitem', $registro['nome_subitem']);
		$this->__set ('nome_item', $registro['nome_item']);

	}	//	Fim function getDadosSubitemAll

// =================================================== //

	public function getNomeSubitemAlteracao() {

		$query = "
				select count(*) as qtde
					from tb_sbitem_suprimt
					where nm_sbitem = :nm_sbitem";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_sbitem', $this->__get('nomeSubitem_pesq'));
		$stmt->execute();
		
		$nr_registros = $stmt->fetch(\PDO::FETCH_ASSOC);	
		
		$this->__set ('qtde_nome_subitem', $nr_registros['qtde']);

	}	//	Fim function getNomeSubitemAlteracao

// =================================================== //

	public function alteraSubitem() {
		$query = "
				update tb_sbitem_suprimt
				set    nm_sbitem   = :nm_sbitem 
				where  cd_itemID   = :cd_item
				and    cd_sbitemID = :cd_sbitem";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cdItem_altera'));
		$stmt->bindValue(':cd_sbitem', $this->__get('cdSbitem_altera'));
		$stmt->bindValue(':nm_sbitem', $this->__get('nomeSubitem_altera'));
		$stmt->execute();

		return $this;
		
	}	// Fim function alteraSubitem

// =================================================== //

	public function getDadosSubitemEspecifico() {
		$query = "
				select 	nm_sbitem as nome_subitem
					from  tb_sbitem_suprimt
					where cd_itemID   = :cd_item
					and   cd_sbitemID = :cd_sbitem";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cd_item'));
		$stmt->bindValue(':cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosSubitemEspecifico


} 	// FIm da classe
?>