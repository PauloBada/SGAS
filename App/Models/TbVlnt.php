<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbVlnt extends Model {
	
	// colunas da tabela tb_vlnt
	private $cd_vlntID;
	private $nm_vlnt;
	private $dt_nasc;
	private $dsc_natural;
	private $cd_sexo;
	private $nr_socio;
	private $dt_cadastro;
	private $dsc_end;
	private $cd_reg_adm;
	private $cep;
	private $uf;
	private $nr_doc_ident;
	private $cpf;
	private $fone_rsdl;
	private $fone_cmrl;
	private $fone_cel;
	private $email;
	private $esde;
	private $dsc_fase_ESDE;
	private $dsc_dia_semana;
	private $dsc_horario;
	private $dsc_escolar;
	private $dsc_profissao;
	private $dsc_conhec_especif;
	private $dsc_habilidade;
	private $dsc_trab_vlnt_outro;
	private $dsc_obs;
	private $dsc_prefer_atvd_vlnt;
	private $nm_vlnt_forum;
	private $cd_vlnt_resp_cadastro;

// ====================================================== //

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //

	// Informações do Usuário
	public function getInfoVoluntario() {
		$query = "
				select nm_vlnt 
				from tb_vlnt 
				where cd_vlntID = :id_vlnt";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':id_vlnt', $this->__get('id'));
		$stmt->execute();

		//         	  fetch somente, pois retornará um registro apenas
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}

// ====================================================== //

	// Busca por cpf
	public function getQtdVoluntario2() {
		$query = "
				select count(*) as qtde
				from tb_vlnt 
				where cpf = :cpf";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cpf', $this->__get('cpf_pesq'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	// Busca por email
	public function getQtdVoluntario3() {
		$query = "
				select count(*) as qtde
				from tb_vlnt 
				where email	= :email";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':email', $this->__get('email_pesq'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function obtemProxCdVlnt() {
		$query = "
				select max(cd_vlntID) as max_cd_vlnt
				from  tb_vlnt";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function insertVoluntario() {

		// Para gravar nulo nos campos quando não houver informação
		if (empty($this->__get('dt_nasc'))) {
			$dt_nasc = null;
		} else {
			$dt_nasc = $this->__get('dt_nasc');
		}

		if (empty($this->__get('dsc_natural'))) {
			$dsc_natural = null;
		} else {
			$dsc_natural = $this->__get('dsc_natural');
		}

		if (empty($this->__get('nr_socio'))) {
			$nr_socio = null;
		} else {
			$nr_socio = $this->__get('nr_socio');
		}

		if (empty($this->__get('dsc_end'))) {
			$dsc_end = null;
		} else {
			$dsc_end = $this->__get('dsc_end');
		}

		if (empty($this->__get('cd_reg_adm'))) {
			$cd_reg_adm = null;
		} else {
			$cd_reg_adm = $this->__get('cd_reg_adm');
		}

		if (empty($this->__get('cep'))) {
			$cep = null;
		} else {
			$cep = $this->__get('cep');
		}

		if (empty($this->__get('uf'))) {
			$uf = null;
		} else {
			$uf = $this->__get('uf');
		}

		if (empty($this->__get('nr_doc_ident'))) {
			$nr_doc_ident = null;
		} else {
			$nr_doc_ident = $this->__get('nr_doc_ident');
		}

		if (empty($this->__get('cpf'))) {
			$cpf = null;
		} else {
			$cpf = $this->__get('cpf');
		}

		if (empty($this->__get('fone_rsdl'))) {
			$fone_rsdl = null;
		} else {
			$fone_rsdl = $this->__get('fone_rsdl');
		}

		if (empty($this->__get('fone_cmrl'))) {
			$fone_cmrl = null;
		} else {
			$fone_cmrl = $this->__get('fone_cmrl');
		}

		if (empty($this->__get('fone_cel'))) {
			$fone_cel = null;
		} else {
			$fone_cel = $this->__get('fone_cel');
		}

		if (empty($this->__get('dsc_fase_ESDE'))) {
			$dsc_fase_ESDE = null;
		} else {
			$dsc_fase_ESDE = $this->__get('dsc_fase_ESDE');
		}

		if (empty($this->__get('dsc_dia_semana'))) {
			$dsc_dia_semana = null;
		} else {
			$dsc_dia_semana = $this->__get('dsc_dia_semana');
		}

		if (empty($this->__get('dsc_horario'))) {
			$dsc_horario = null;
		} else {
			$dsc_horario = $this->__get('dsc_horario');
		}

		if (empty($this->__get('dsc_escolar'))) {
			$dsc_escolar = null;
		} else {
			$dsc_escolar = $this->__get('dsc_escolar');
		}

		if (empty($this->__get('dsc_profissao'))) {
			$dsc_profissao = null;
		} else {
			$dsc_profissao = $this->__get('dsc_profissao');
		}

		if (empty($this->__get('dsc_conhec_especif'))) {
			$dsc_conhec_especif = null;
		} else {
			$dsc_conhec_especif = $this->__get('dsc_conhec_especif');
		}

		if (empty($this->__get('dsc_habilidade'))) {
			$dsc_habilidade = null;
		} else {
			$dsc_habilidade = $this->__get('dsc_habilidade');
		}

		if (empty($this->__get('dsc_trab_vlnt_outro'))) {
			$dsc_trab_vlnt_outro = null;
		} else {
			$dsc_trab_vlnt_outro = $this->__get('dsc_trab_vlnt_outro');
		}

		if (empty($this->__get('dsc_obs'))) {
			$dsc_obs = null;
		} else {
			$dsc_obs = $this->__get('dsc_obs');
		}

		if (empty($this->__get('dsc_prefer_atvd_vlnt'))) {
			$dsc_prefer_atvd_vlnt = null;
		} else {
			$dsc_prefer_atvd_vlnt = $this->__get('dsc_prefer_atvd_vlnt');
		}

		if (empty($this->__get('nm_vlnt_forum'))) {
			$nm_vlnt_forum = null;
		} else {
			$nm_vlnt_forum = $this->__get('nm_vlnt_forum');
		}

		$query = "
				insert into tb_vlnt
				(cd_vlntID,
				nm_vlnt,
				dt_nasc,
				dsc_natural,
				cd_sexo,
				nr_socio,
				dt_cadastro,
				dsc_end,
				cd_reg_adm,
				cep,
				uf,
				nr_doc_ident,
				cpf,
				fone_rsdl,
				fone_cmrl,
				fone_cel,
				email,
				esde,
				dsc_fase_ESDE,
				dsc_dia_semana,
				dsc_horario,
				dsc_escolar,
				dsc_profissao,
				dsc_conhec_especif,
				dsc_habilidade,
				dsc_trab_vlnt_outro,
				dsc_obs,
				dsc_prefer_atvd_vlnt,
				nm_vlnt_forum,
				cd_vlnt_resp_cadastro) 

				values 

				(:cd_vlnt,
				:nm_vlnt,
				str_to_date(:dt_nasc, '%d/%m/%Y'),
				:dsc_natural,
				:cd_sexo,
				:nr_socio,
				now(),
				:dsc_end,
				:cd_reg_adm,
				:cep,
				:uf,
				:nr_doc_ident,
				:cpf,
				:fone_rsdl,
				:fone_cmrl,
				:fone_cel,
				:email,
				:esde,
				:dsc_fase_ESDE,
				:dsc_dia_semana,
				:dsc_horario,
				:dsc_escolar,
				:dsc_profissao,
				:dsc_conhec_especif,
				:dsc_habilidade,
				:dsc_trab_vlnt_outro,
				:dsc_obs,
				:dsc_prefer_atvd_vlnt,
				:nm_vlnt_forum,
				:cd_vlnt_resp_cadastro)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue(':nm_vlnt', $this->__get('nm_vlnt'));
		$stmt->bindValue(':dt_nasc', $dt_nasc); 
		$stmt->bindValue(':dsc_natural', $dsc_natural);
		$stmt->bindValue(':cd_sexo', $this->__get('cd_sexo'));
		$stmt->bindValue(':nr_socio', $nr_socio);
		$stmt->bindValue(':dsc_end', $dsc_end);
		$stmt->bindValue(':cd_reg_adm', $cd_reg_adm);
		$stmt->bindValue(':cep', $cep);
		$stmt->bindValue(':uf', $uf);
		$stmt->bindValue(':nr_doc_ident', $nr_doc_ident);
		$stmt->bindValue(':cpf', $cpf);
		$stmt->bindValue(':fone_rsdl', $fone_rsdl);
		$stmt->bindValue(':fone_cmrl', $fone_cmrl);
		$stmt->bindValue(':fone_cel', $fone_cel);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':esde', $this->__get('esde'));
		$stmt->bindValue(':dsc_fase_ESDE', $dsc_fase_ESDE);
		$stmt->bindValue(':dsc_dia_semana', $dsc_dia_semana);
		$stmt->bindValue(':dsc_horario', $dsc_horario);
		$stmt->bindValue(':dsc_escolar', $dsc_escolar);
		$stmt->bindValue(':dsc_profissao', $dsc_profissao);
		$stmt->bindValue(':dsc_conhec_especif', $dsc_conhec_especif);
		$stmt->bindValue(':dsc_habilidade', $dsc_habilidade);
		$stmt->bindValue(':dsc_trab_vlnt_outro', $dsc_trab_vlnt_outro);
		$stmt->bindValue(':dsc_obs', $dsc_obs);
		$stmt->bindValue(':dsc_prefer_atvd_vlnt', $dsc_prefer_atvd_vlnt);
		$stmt->bindValue(':nm_vlnt_forum', $nm_vlnt_forum);
		$stmt->bindValue(':cd_vlnt_resp_cadastro', $this->__get('cd_vlnt_resp_cadastro'));
		$stmt->execute();

		return $this;

	}	// Fim function InsereVoluntario

// ====================================================== //

	public function getDadosVoluntariosAll() {
		$query = "
				select cd_vlntID, nm_vlnt
				from  tb_vlnt
				order by nm_vlnt";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}	// Fim function getDadosVoluntariosAll

// ====================================================== //

	public function getDadosVoluntario() {
		$query = "
				select *
				from  tb_vlnt
				where cd_vlntID = :cd_vlnt";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosVoluntario

// ====================================================== //

	// Busca por cpf
	public function getQtdVoluntario4() {
		$query = "
				select count(*) as qtde
				from tb_vlnt 
				where cpf       = :cpf
				and   cd_vlntID <> :cd_vlnt";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt_pesq'));
		$stmt->bindValue(':cpf', $this->__get('cpf_pesq'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	// Busca por email
	public function getQtdVoluntario5() {
		$query = "
				select count(*) as qtde
				from tb_vlnt 
				where email	    = :email
				and   cd_vlntID <> :cd_vlnt";;
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt_pesq'));
		$stmt->bindValue(':email', $this->__get('email_pesq'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function updateVoluntario() {

		// Para gravar nulo nos campos quando não houver informação
		if (empty($this->__get('dt_nasc'))) {
			$dt_nasc = null;
		} else {
			$dt_nasc = $this->__get('dt_nasc');
		}

		if (empty($this->__get('dsc_natural'))) {
			$dsc_natural = null;
		} else {
			$dsc_natural = $this->__get('dsc_natural');
		}

		if (empty($this->__get('nr_socio'))) {
			$nr_socio = null;
		} else {
			$nr_socio = $this->__get('nr_socio');
		}

		if (empty($this->__get('dsc_end'))) {
			$dsc_end = null;
		} else {
			$dsc_end = $this->__get('dsc_end');
		}

		if (empty($this->__get('cd_reg_adm'))) {
			$cd_reg_adm = null;
		} else {
			$cd_reg_adm = $this->__get('cd_reg_adm');
		}

		if (empty($this->__get('cep'))) {
			$cep = null;
		} else {
			$cep = $this->__get('cep');
		}

		if (empty($this->__get('uf'))) {
			$uf = null;
		} else {
			$uf = $this->__get('uf');
		}

		if (empty($this->__get('nr_doc_ident'))) {
			$nr_doc_ident = null;
		} else {
			$nr_doc_ident = $this->__get('nr_doc_ident');
		}

		if (empty($this->__get('cpf'))) {
			$cpf = null;
		} else {
			$cpf = $this->__get('cpf');
		}

		if (empty($this->__get('fone_rsdl'))) {
			$fone_rsdl = null;
		} else {
			$fone_rsdl = $this->__get('fone_rsdl');
		}

		if (empty($this->__get('fone_cmrl'))) {
			$fone_cmrl = null;
		} else {
			$fone_cmrl = $this->__get('fone_cmrl');
		}

		if (empty($this->__get('fone_cel'))) {
			$fone_cel = null;
		} else {
			$fone_cel = $this->__get('fone_cel');
		}

		if (empty($this->__get('dsc_fase_ESDE'))) {
			$dsc_fase_ESDE = null;
		} else {
			$dsc_fase_ESDE = $this->__get('dsc_fase_ESDE');
		}

		if (empty($this->__get('dsc_dia_semana'))) {
			$dsc_dia_semana = null;
		} else {
			$dsc_dia_semana = $this->__get('dsc_dia_semana');
		}

		if (empty($this->__get('dsc_horario'))) {
			$dsc_horario = null;
		} else {
			$dsc_horario = $this->__get('dsc_horario');
		}

		if (empty($this->__get('dsc_escolar'))) {
			$dsc_escolar = null;
		} else {
			$dsc_escolar = $this->__get('dsc_escolar');
		}

		if (empty($this->__get('dsc_profissao'))) {
			$dsc_profissao = null;
		} else {
			$dsc_profissao = $this->__get('dsc_profissao');
		}

		if (empty($this->__get('dsc_conhec_especif'))) {
			$dsc_conhec_especif = null;
		} else {
			$dsc_conhec_especif = $this->__get('dsc_conhec_especif');
		}

		if (empty($this->__get('dsc_habilidade'))) {
			$dsc_habilidade = null;
		} else {
			$dsc_habilidade = $this->__get('dsc_habilidade');
		}

		if (empty($this->__get('dsc_trab_vlnt_outro'))) {
			$dsc_trab_vlnt_outro = null;
		} else {
			$dsc_trab_vlnt_outro = $this->__get('dsc_trab_vlnt_outro');
		}

		if (empty($this->__get('dsc_obs'))) {
			$dsc_obs = null;
		} else {
			$dsc_obs = $this->__get('dsc_obs');
		}

		if (empty($this->__get('dsc_prefer_atvd_vlnt'))) {
			$dsc_prefer_atvd_vlnt = null;
		} else {
			$dsc_prefer_atvd_vlnt = $this->__get('dsc_prefer_atvd_vlnt');
		}

		if (empty($this->__get('nm_vlnt_forum'))) {
			$nm_vlnt_forum = null;
		} else {
			$nm_vlnt_forum = $this->__get('nm_vlnt_forum');
		}

		$query = "
				update tb_vlnt
				set 
					nm_vlnt = :nm_vlnt,
					dt_nasc = str_to_date(:dt_nasc, '%d/%m/%Y'),
					dsc_natural = :dsc_natural,
					cd_sexo = :cd_sexo,
					nr_socio = :nr_socio,
					dt_cadastro = now(),
					dsc_end = :dsc_end,
					cd_reg_adm = :cd_reg_adm,
					cep = :cep,
					uf = :uf,
					nr_doc_ident = :nr_doc_ident,
					cpf = :cpf,
					fone_rsdl = :fone_rsdl,
					fone_cmrl = :fone_cmrl,
					fone_cel = :fone_cel,
					email = :email,
					esde = :esde,
					dsc_fase_ESDE = :dsc_fase_ESDE,
					dsc_dia_semana = :dsc_dia_semana,
					dsc_horario = :dsc_horario,
					dsc_escolar = :dsc_escolar,
					dsc_profissao = :dsc_profissao,
					dsc_conhec_especif = :dsc_conhec_especif,
					dsc_habilidade = :dsc_habilidade,
					dsc_trab_vlnt_outro = :dsc_trab_vlnt_outro,
					dsc_obs = :dsc_obs,
					dsc_prefer_atvd_vlnt = :dsc_prefer_atvd_vlnt,
					nm_vlnt_forum = :nm_vlnt_forum,
					cd_vlnt_resp_cadastro =  :cd_vlnt_resp_cadastro
				where cd_vlntID = :cd_vlnt";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->bindValue(':nm_vlnt', $this->__get('nm_vlnt'));
		$stmt->bindValue(':dt_nasc', $dt_nasc); 
		$stmt->bindValue(':dsc_natural', $dsc_natural);
		$stmt->bindValue(':cd_sexo', $this->__get('cd_sexo'));
		$stmt->bindValue(':nr_socio', $nr_socio);
		$stmt->bindValue(':dsc_end', $dsc_end);
		$stmt->bindValue(':cd_reg_adm', $cd_reg_adm);
		$stmt->bindValue(':cep', $cep);
		$stmt->bindValue(':uf', $uf);
		$stmt->bindValue(':nr_doc_ident', $nr_doc_ident);
		$stmt->bindValue(':cpf', $cpf);
		$stmt->bindValue(':fone_rsdl', $fone_rsdl);
		$stmt->bindValue(':fone_cmrl', $fone_cmrl);
		$stmt->bindValue(':fone_cel', $fone_cel);
		$stmt->bindValue(':email', $this->__get('email'));
		$stmt->bindValue(':esde', $this->__get('esde'));
		$stmt->bindValue(':dsc_fase_ESDE', $dsc_fase_ESDE);
		$stmt->bindValue(':dsc_dia_semana', $dsc_dia_semana);
		$stmt->bindValue(':dsc_horario', $dsc_horario);
		$stmt->bindValue(':dsc_escolar', $dsc_escolar);
		$stmt->bindValue(':dsc_profissao', $dsc_profissao);
		$stmt->bindValue(':dsc_conhec_especif', $dsc_conhec_especif);
		$stmt->bindValue(':dsc_habilidade', $dsc_habilidade);
		$stmt->bindValue(':dsc_trab_vlnt_outro', $dsc_trab_vlnt_outro);
		$stmt->bindValue(':dsc_obs', $dsc_obs);
		$stmt->bindValue(':dsc_prefer_atvd_vlnt', $dsc_prefer_atvd_vlnt);
		$stmt->bindValue(':nm_vlnt_forum', $nm_vlnt_forum);
		$stmt->bindValue(':cd_vlnt_resp_cadastro', $this->__get('cd_vlnt_resp_cadastro'));
		$stmt->execute();

		return $this;

	}	// Fim function updateVoluntario

// ====================================================== //

	public function getDadosVoluntarioNivel() {
		$query = "
				select 	b.cd_nivel_ace_login as nivel_login, 
						b.seql_cad_loginID as seql_login,
						a.nm_vlnt as nome_login,
						a.email as email_login
				from tb_vlnt as a,
					 tb_cad_login_sess as b
				where a.cd_vlntID = :cd_vlnt
				and   a.cd_vlntID = b.cd_vlntID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('cd_vlnt'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosVoluntario

}	// Fim da Classe

?>