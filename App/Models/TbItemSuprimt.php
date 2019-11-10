<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbItemSuprimt extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_itemID;
	private $nm_item;
	private $cd_tip_evt_suprimt;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function getNomeItemInclusao() {

		//echo $this->__get('nomeRA_pesq');

		$query = "
				select count(*) as qtde
					from tb_item_suprimt
					where nm_item = :nm_item";
					/* where nm_item like :nm_item"; */
		$stmt = $this->db->prepare($query);
		/* $stmt->bindValue(':nm_item', '%'.$this->__get('nomeItem_pesq').'%'); */
		$stmt->bindValue(':nm_item', $this->__get('nomeItem_pesq'));
		$stmt->execute();
		
		$nr_registros = $stmt->fetch(\PDO::FETCH_ASSOC);	
		
		$this->__set ('qtde_nome_item', $nr_registros['qtde']);

	}	//	Fim function getNomeItemInclusao

// =================================================== //

	public function getNomeItemAlteracao() {

		//echo $this->__get('nomeRA_pesq');

		$query = "
				select count(*) as qtde
					from tb_item_suprimt
					where nm_item = :nm_item
					and   cd_tip_evt_suprimt = :cd_tip_evt_suprimt";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_item', $this->__get('nomeItem_pesq'));
		$stmt->bindValue(':cd_tip_evt_suprimt', $this->__get('evento_pesq'));
		$stmt->execute();
		
		$nr_registros = $stmt->fetch(\PDO::FETCH_ASSOC);	
		
		$this->__set ('qtde_nome_item', $nr_registros['qtde']);

	}	//	Fim function getNomeItemAlteracao

// =================================================== //

	// Incluir dados no banco de dados (persistência)
	public function insereItem() {
		$query = "
				insert into tb_item_suprimt
				(nm_item, 
				cd_tip_evt_suprimt) 
				values 
				(:nm_item, 
				:cd_tip_evt_suprimt)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_item', $this->__get('nomeItem_insere'));
		$stmt->bindValue(':cd_tip_evt_suprimt', $this->__get('evento_insere'));
		$stmt->execute();

		return $this;
	}	// Fim function InsereItem

// =================================================== //

	public function getDadosItemAll() {
		$query = "
				select 	cd_itemID as cod_item,
						nm_item as nome_item,
						cd_tip_evt_suprimt as cod_evento
					from tb_item_suprimt
					order by nm_item";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosItemAll

// =================================================== //

	public function getDadosItemAll2() {
		$query = "
				select 	nm_item as nome_item,
						cd_tip_evt_suprimt as cd_evento
					from tb_item_suprimt
					where cd_itemID = :cd_item";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cd_item'));
		$stmt->execute();

		$registro = $stmt->fetch(\PDO::FETCH_ASSOC);	

		$this->__set ('nome_item', $registro['nome_item']);
		$this->__set ('evento', $registro['cd_evento']);

	}	//	Fim function getDadositemAll2

// =================================================== //

	// Alterar dados no banco de dados (persistência)
	public function alteraItem() {
		$query = "
				update tb_item_suprimt
				set nm_item        = :nm_item, 
				cd_tip_evt_suprimt = :cd_tip_evt_suprimt 
				where cd_itemID    = :cd_item";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_item', $this->__get('cdItem_altera'));
		$stmt->bindValue(':nm_item', $this->__get('nomeItem_altera'));
		$stmt->bindValue(':cd_tip_evt_suprimt', $this->__get('evento_altera'));
		$stmt->execute();

		return $this;
		
	}	// Fim function altealteraItemraRA

// =================================================== //

	public function getDadosItemAll3() {
		$query = "
				select 	cd_itemID as cod_item,
						nm_item as nome_item,
						cd_tip_evt_suprimt as cod_evento
					from tb_item_suprimt
					order by nm_item";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosItemAll3

// =================================================== //

	public function getDadosItemEspecifico() {
		$query = "
				select 	nm_item as nome_item,
						cd_tip_evt_suprimt as cd_evento
					from tb_item_suprimt
					where cd_itemID = :cd_item";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosItemEspecifico



} 	// FIm da classe
?>