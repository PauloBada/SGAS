<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbCadLoginSess extends Model {
	
	// colunas da tabela tb_cad_login_sess
	private $seql_cad_loginID;
	private $cd_vlntID;
	private $cd_nivel_ace;
	private $dt_cadastr;
	private $dt_inc_vgc;
	private $dt_fim_vgc;
	private $senha;
	private $cd_situ_cad_login;
	private $ts_cadastr;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //

	// Buscar informações para autenticar usuário
	public function autenticar() {
		$query = "
			select a.seql_cad_loginID as sequencial, 
					a.cd_vlntID as id, 
					a.senha, 
					b.email, 
				   	b.nm_vlnt as nome
			from tb_cad_login_sess a,
				 tb_vlnt b 
			where b.email = :email 
			and   a.senha = :senha
			and   a.cd_vlntID = b.cd_vlntID";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':senha', $this->__get('senha'));
		$stmt->execute();

		//                fetch somente, pois retornará um registro apenas
		$usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

		if ($usuario['id'] != '' && $usuario['nome'] != '' ) {
			$this->__set ('id_vlnt', $usuario['id']);
			$this->__set ('nome', $usuario['nome']);
			$this->__set ('seql_cad_login', $usuario['sequencial']);
		}	

		return $this;
	}

// ====================================================== //

	// Buscar Senha do voluntário logado
	public function getSenha() {
		$query = "
			select senha
			from tb_cad_login_sess
			where seql_cad_loginID = :seql_cad_login";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
		$stmt->execute();

		$senha_login = $stmt->fetch(\PDO::FETCH_ASSOC);

		if ($senha_login['senha'] != '') {
			$this->__set ('senhaBase', $senha_login['senha']);
		}	

		return $this;
	}

// ====================================================== //

	public function validarCadastro() {
		$valido = 0;
		
		if (strlen($this->__get('senhaAtualVerifica')) < 3) {
			$valido = 1;
		} else if (strlen($this->__get('senhaNovaVerifica')) < 3) {
			$valido = 2;
		} else if (strlen($this->__get('senhaNovaRedigitaVerifica')) < 3) {
			$valido = 3;
		}

		return $valido; 
	}

	// Gravar nova Senha para o voluntário logado ou com senha alterada por administrador
	public function gravaNovaSenha() {
		$query = "
			update tb_cad_login_sess
			set senha = :senha
			where seql_cad_loginID = :seql_cad_login";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
		$stmt->bindValue(':senha', $this->__get('senhaNova'));
		$stmt->execute();

		return $this;
	}

// ====================================================== //

	// Buscar Nível de acesso do voluntário logado
	public function getNivelAcesso() {
		$query = "
			select cd_nivel_ace_login as nivel
			from tb_cad_login_sess
			where seql_cad_loginID = :seql_cad_login";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
		$stmt->execute();

		$senha_login = $stmt->fetch(\PDO::FETCH_ASSOC);

		if ($senha_login['nivel'] != '') {
			$this->__set ('nivelAcesso', $senha_login['nivel']);
		}	

		return $this;
	}

// ====================================================== //
	
	// Salvar dados no banco de dados (persistência)
	public function insertCadLoginSess() {

		$query = "
				insert into tb_cad_login_sess
				(cd_vlntID, 
				cd_nivel_ace_login, 
				dt_cadastr,
				dt_inc_vgc,
				dt_fim_vgc,
				senha,
				cd_situ_cad_login,
				ts_cadastr)

				values 
				
				(:cd_vlnt, 
				:cd_nivel_ace_login, 
				now(),
				now(),
				str_to_date('31/12/9998', '%d/%m/%Y'),
				:senha,
				:cd_situ_cad_login,
				current_timestamp())";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue(':cd_nivel_ace_login', $this->__get('cd_nivel_ace_login'));
		$stmt->bindValue(':senha', md5($this->__get('senha')));
		$stmt->bindValue(':cd_situ_cad_login', $this->__get('cd_situ_cad_login'));
		$stmt->execute();

		return $this;
	}

// ====================================================== //

	// Gravar novo Nível de acesso para o voluntário alterado por administrador
	public function gravaNovoNivel() {
		$query = "
			update tb_cad_login_sess
			set cd_nivel_ace_login = :cd_nivel_ace_login
			where seql_cad_loginID = :seql_cad_login";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':seql_cad_login', $this->__get('seql_cad_login'));
		$stmt->bindValue(':cd_nivel_ace_login', $this->__get('cd_nivel_ace_login'));
		$stmt->execute();

		return $this;
	}
}

?>