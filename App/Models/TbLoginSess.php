<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbLoginSess extends Model {
	
	// colunas da tabela tb_cad_login_sess
	private $seql_cad_loginID;
	private $seql_loginID;
	private $ts_login_ent;
	private $ts_login_sai;
	private $status_login;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

	// Salvar dados no banco de dados (persistência)
	public function iniciarSessao() {

		$this->getProximoSequencialLogin();
				
		$query = "insert into tb_login_sess 
				(seql_cad_loginID, 
				seql_loginID, 
				ts_login_ent, 
				ts_login_sai, 
				status_login) 
				values 
				(:seql_cad_login, 
				:seql_login, 
				current_timestamp(),
				current_timestamp(),
				:status)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
		$stmt->bindValue(':seql_login', $this->__get('seql_login_max'));
		$stmt->bindValue(':status', 1);
		$stmt->execute();

		return $this;
	}

	public function getProximoSequencialLogin() {
		$query0 = "select count(*) + 1 as qtde
					from tb_login_sess 
					where seql_cad_loginID = :seql_cad_login";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_login_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "select max(seql_LoginID) + 1 as qtde
						from tb_login_sess 
						where seql_cad_loginID = :seql_cad_login";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_login_max', $nr_registros_1['qtde']);
		}
	}

	public function encerrarSessao() {
			
		$query = "update tb_login_sess 
				set status_login = :status_encerra													
				where seql_cad_loginID = :seql_cad_login 
				and   status_login = :status_atual";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
		$stmt->bindValue(':status_encerra', 2);
		$stmt->bindValue(':status_atual', 1);
		$stmt->execute();

		return $this;
	}
}

?>