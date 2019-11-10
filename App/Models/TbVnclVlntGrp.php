<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbVnclVlntGrp extends Model {
	
	// colunas da tabela tb_reg_adm
	private $cd_vltnID;
	private $cd_grpID;
	private $seql_vnclID;
	private $dt_inc_vncl;
	private $dt_fim_vncl;
	private $cd_est_vncl;
	private $cd_atu_vlnt;
	private $cd_sbgrpID;


	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}	

// =================================================== //

	public function getQtdSubgrupoVinculoVoluntario() {

		$query = "
				select count(*) as qtde
					from tb_vncl_vlnt_grp
					where cd_grpID    = :cd_grp
					and   cd_sbgrpID  = :cd_sbgrp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
		$stmt->execute();
		
		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getQtdSubgrupoVinculoVoluntario

// =================================================== //

	public function getQtdGrupoVoluntario() {


		if (empty($this->__get('codSubgrupo_pesq'))) {

				$query = "
						select count(*) as qtde
							from tb_vncl_vlnt_grp
							where cd_grpID    = :cd_grp
							and   cd_sbgrpID  is null
							and   cd_vlntID   = :cd_vlnt
							and   cd_est_vncl = 1";
				$stmt = $this->db->prepare($query);
				$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
				$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario_pesq'));
				$stmt->execute();
				
				return $stmt->fetch(\PDO::FETCH_ASSOC);	

		} else {
		
			$query = "
					select count(*) as qtde
						from tb_vncl_vlnt_grp
						where cd_grpID    = :cd_grp
						and  cd_sbgrpID   = :cd_sbgrp
						and   cd_vlntID   = :cd_vlnt
						and   cd_est_vncl = 1";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
			$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
			$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario_pesq'));
			$stmt->execute();
			
			return $stmt->fetch(\PDO::FETCH_ASSOC);	

		}
	}	//	Fim function getQtdGrupoVoluntario

// =================================================== //

	public function getCodSubgrupoVoluntario() {

		$query = "
				select cd_sbgrpID as subgrupo_base,
					   seql_vnclID as sequencial_base	
					from tb_vncl_vlnt_grp
					where cd_grpID    = :cd_grp
					and   cd_vlntID   = :cd_vlnt
					and   cd_est_vncl = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
		$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario_pesq'));
		$stmt->execute();
		
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getCodSubgrupoVoluntario


// =================================================== //

	public function updateSubgrupoVoluntario() {

		$query = "
				update tb_vncl_vlnt_grp
				set    cd_sbgrpID  = :cd_sbgrp,
					   cd_atu_vlnt = :cd_atu_vlnt
				where  cd_vlntID   = :cd_vlnt
				and    cd_grpID    = :cd_grp
				and    seql_vnclID = :seql_vncl";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario'));
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue(':seql_vncl', $this->__get('seqltabela'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo'));
		$stmt->bindValue(':cd_atu_vlnt', $this->__get('codAtuacao'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function updateSubgrupoVoluntario

// =================================================== //

	public function insertVinculo() {

		// Para gravar nulo em subgrupo, caso não haja o mesmo
		if (empty($this->__get('codSubgrupo'))) {
			$codSubgrupo = null;
		} else {
			$codSubgrupo = $this->__get('codSubgrupo');
		}

		$this->getProximoSequencial();

		$query = "
				insert into tb_vncl_vlnt_grp
				(cd_vlntID,
				cd_grpID,
				seql_vnclID,
				dt_inc_vncl,
				dt_fim_vncl,
				cd_est_vncl,
				cd_atu_vlnt,
				cd_sbgrpID)

				values 

				(:cd_vlnt,
				:cd_grp,
				:seql_vncl,
				now(),
				str_to_date('31/12/9998', '%d/%m/%Y'),
				1,
				:cd_atu_vlnt,
				:cd_sbgrp)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario'));
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue(':seql_vncl', $this->__get('seql_max'));
		$stmt->bindValue(':cd_atu_vlnt', $this->__get('codAtuacao'));
		$stmt->bindValue(':cd_sbgrp', $codSubgrupo);
		$stmt->execute();
		
		return $this;	

	}	//	Fim function insereVinculo

// =================================================== //

	public function getProximoSequencial() {
		$query0 = "select count(*) + 1 as qtde
					from tb_vncl_vlnt_grp
					where cd_vlntID = :cd_vlnt
					and   cd_grpID  = :cd_grp";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue(':cd_vlnt', $this->__get('codVoluntario'));
		$stmt0->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "select max(seql_vnclID) + 1 as qtde
						from tb_vncl_vlnt_grp
						where cd_vlntID = :cd_vlnt
						and   cd_grpID  = :cd_grp";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue(':cd_vlnt', $this->__get('codVoluntario'));
			$stmt1->bindValue(':cd_grp', $this->__get('codGrupo'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_max', $nr_registros_1['qtde']);
		}
	}


// =================================================== //

	public function getDadosVVG() {

		// Para gravar nulo no campos quando não houver informação
		if (empty($this->__get('codSubgrupo_pesq'))) {
			$query = "
					select seql_vnclID as sequencial_base,
						   DATE_FORMAT(dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
						   DATE_FORMAT(dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
						   case when cd_est_vncl = 1 THEN 'Ativo'
						        when cd_est_vncl = 2 THEN 'Encerrado'
						   end as estado_vinculo, 	
						   case when cd_atu_vlnt = 1 THEN 'Coordenador Cadastral'
							    when cd_atu_vlnt = 2 THEN 'Coordenador Financeiro'
							    when cd_atu_vlnt = 3 THEN 'Coordenador Revisor'
							    when cd_atu_vlnt = 4 THEN 'Coordenador Geral'
							    when cd_atu_vlnt = 5 THEN 'Voluntário'
						   end as cod_atuacao_base						
						from tb_vncl_vlnt_grp
						where cd_vlntID  = :cd_vlnt
						and   cd_grpID   = :cd_grp
						and   cd_sbgrpID is null
						and   cd_est_vncl = 1";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario_pesq'));
			$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
			$stmt->execute();
		} else {
			$query = "
					select seql_vnclID as sequencial_base,					
						   DATE_FORMAT(dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
						   DATE_FORMAT(dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
						   case when cd_est_vncl = 1 THEN 'Ativo'
							    when cd_est_vncl = 2 THEN 'Encerrado'
						   end as estado_vinculo,	
						   case when cd_atu_vlnt = 1 THEN 'Coordenador Cadastral'
							    when cd_atu_vlnt = 2 THEN 'Coordenador Financeiro'
							    when cd_atu_vlnt = 3 THEN 'Coordenador Revisor'
							    when cd_atu_vlnt = 4 THEN 'Coordenador Geral'
							    when cd_atu_vlnt = 5 THEN 'Voluntário'
						   end as cod_atuacao_base
						from tb_vncl_vlnt_grp
						where cd_vlntID  = :cd_vlnt
						and   cd_grpID   = :cd_grp
						and   cd_sbgrpID = :cd_sbgrp
						and   cd_est_vncl = 1";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario_pesq'));
			$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
			$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
			$stmt->execute();
		}

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosVVG

// =================================================== //

	public function encerraVVG() {

		$query = "
				update tb_vncl_vlnt_grp
				set    cd_est_vncl  = 2,
					   dt_fim_vncl = now()
				where  cd_vlntID   = :cd_vlnt
				and    cd_grpID    = :cd_grp
				and    seql_vnclID = :seql_vncl";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario'));
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue(':seql_vncl', $this->__get('sequencial'));
		$stmt->execute();
		
		return $this;	

	}	//	Fim function encerraVVG

// =================================================== //

	public function getDadosVVGAll() {

		if (empty($this->__get('codSubgrupo_pesq'))) {
			$query = "
					select seql_vnclID as sequencial_base,
						   DATE_FORMAT(dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
						   DATE_FORMAT(dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
						   case when cd_est_vncl = 1 THEN 'Ativo'
						        when cd_est_vncl = 2 THEN 'Encerrado'
						   end as estado_vinculo, 	
						   case when cd_atu_vlnt = 1 THEN 'Coordenador Cadastral'
							    when cd_atu_vlnt = 2 THEN 'Coordenador Financeiro'
							    when cd_atu_vlnt = 3 THEN 'Coordenador Revisor'
							    when cd_atu_vlnt = 4 THEN 'Coordenador Geral'
							    when cd_atu_vlnt = 5 THEN 'Voluntário'
						   end as cod_atuacao_base						
						from tb_vncl_vlnt_grp
						where cd_vlntID  = :cd_vlnt
						and   cd_grpID   = :cd_grp
						and   cd_sbgrpID is null
						order by seql_vnclID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario_pesq'));
			$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
			$stmt->execute();
		} else {
			$query = "
					select seql_vnclID as sequencial_base,					
						   DATE_FORMAT(dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
						   DATE_FORMAT(dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
						   case when cd_est_vncl = 1 THEN 'Ativo'
							    when cd_est_vncl = 2 THEN 'Encerrado'
						   end as estado_vinculo,	
						   case when cd_atu_vlnt = 1 THEN 'Coordenador Cadastral'
							    when cd_atu_vlnt = 2 THEN 'Coordenador Financeiro'
							    when cd_atu_vlnt = 3 THEN 'Coordenador Revisor'
							    when cd_atu_vlnt = 4 THEN 'Coordenador Geral'
							    when cd_atu_vlnt = 5 THEN 'Voluntário'
						   end as cod_atuacao_base
						from tb_vncl_vlnt_grp
						where cd_vlntID  = :cd_vlnt
						and   cd_grpID   = :cd_grp
						and   cd_sbgrpID = :cd_sbgrp
						order by seql_vnclID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario_pesq'));
			$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
			$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo_pesq'));
			$stmt->execute();
		}

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosVVGAll

// ====================================================== //

	public function getNivelAtuacao() {
		$query = "
			select cd_atu_vlnt as cod_atuacao 
			from tb_vncl_vlnt_grp
			where cd_vlntID   = :cd_vlnt
			and   cd_grpID    = :cd_grp
			and   cd_sbgrpID  = :cd_sbgrp
			and   cd_est_vncl = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario'));
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue(':cd_sbgrp', $this->__get('codSubgrupo'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}

// ====================================================== //

	public function getVoluntariosGrupoSubgrupo() {

		$query = "
				select b.cd_vlntID as cd_vlnt, 
					   b.nm_vlnt as nm_vlnt
				from  tb_vncl_vlnt_grp a,
				      tb_vlnt b
				where a.cd_grpID    = :cd_grp
				and   a.cd_sbgrpID  = :cd_sbgrp
				and   a.cd_atu_vlnt = :cd_atu_vlnt
				and   a.cd_vlntID   = b.cd_vlntID
				and   a.cd_est_vncl = 1

				and   a.cd_vlntID not in (select cd_vlntID
										  from  tb_vncl_vlnt_acomp_fml
										  where cd_fmlID     = :cd_fml
										  and   seql_acompID = :seql_acomp)				
			
				order by b.nm_vlnt";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		//TU $stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		//TU $stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_atu_vlnt', $this->__get('cd_atu_vlnt'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

// ====================================================== //

	public function getVoluntariosGrupoSubgrupoAcomp() {

		$query = "
				select b.cd_vlntID as cd_vlnt, 
					   b.nm_vlnt as nm_vlnt,
					   'n' as simNao
				from  tb_vncl_vlnt_grp a,
				      tb_vlnt b
				where a.cd_grpID    = :cd_grp
				and   a.cd_sbgrpID  = :cd_sbgrp
				and   a.cd_atu_vlnt = :cd_atu_vlnt
				and   a.cd_vlntID   = b.cd_vlntID
				and   a.cd_est_vncl = 1

				and   a.cd_vlntID not in (select cd_vlntID
										  from  tb_vncl_vlnt_acomp_fml
										  where cd_fmlID     = :cd_fml
										  and   seql_acompID = :seql_acomp)				

				and    :cd_fml in (select distinct cd_fmlID
								   from tb_acomp_fml
								   where cd_atvd_acomp = 1)

				union all

				select b.cd_vlntID as cd_vlnt, 
					   b.nm_vlnt as nm_vlnt,
					   's' as simNao
				from  tb_vncl_vlnt_grp a,
				      tb_vlnt b
				where a.cd_grpID    = :cd_grp
				and   a.cd_sbgrpID  = :cd_sbgrp
				and   a.cd_atu_vlnt = :cd_atu_vlnt
				and   a.cd_vlntID   = b.cd_vlntID
				and   a.cd_est_vncl = 1

				and   a.cd_vlntID in (select cd_vlntID
									  from  tb_vncl_vlnt_acomp_fml
									  where cd_fmlID     = :cd_fml
									  and   seql_acompID = :seql_acomp)				

				and    :cd_fml in (select distinct cd_fmlID
								   from tb_acomp_fml
								   where cd_atvd_acomp = 1)

				order by 3, 2";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_atu_vlnt', $this->__get('cd_atu_vlnt'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

// =================================================== //

	public function getDadosVVGAll_2() {

		if (empty($this->__get('codSubgrupo_pesq'))) {
			$query = "
					select a.seql_vnclID as sequencial_base,					
						   a.cd_vlntID as cd_vlnt,
                           b.nm_vlnt,
						   DATE_FORMAT(a.dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
						   DATE_FORMAT(a.dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
						   case when a.cd_est_vncl = 1 THEN 'Ativo'
							    when a.cd_est_vncl = 2 THEN 'Encerrado'
						   end as estado_vinculo,	
						   case when a.cd_atu_vlnt = 1 THEN 'Coordenador Cadastral'
							    when a.cd_atu_vlnt = 2 THEN 'Coordenador Financeiro'
							    when a.cd_atu_vlnt = 3 THEN 'Coordenador Revisor'
							    when a.cd_atu_vlnt = 4 THEN 'Coordenador Geral'
							    when a.cd_atu_vlnt = 5 THEN 'Voluntário'
						   end as cod_atuacao_base,
               c.nm_sbgrp as nm_sbgrp
						from tb_vncl_vlnt_grp a,
                             tb_vlnt b,
                             tb_sbgrp c
						where a.cd_grpID   = :cd_grp
              and (a.dt_inc_vncl between str_to_date(:dt_inc_vncl, '%d/%m/%Y') and 
							   				        str_to_date(:dt_fim_vncl, '%d/%m/%Y')
					     or a.dt_fim_vncl > str_to_date(:dt_fim_vncl, '%d/%m/%Y'))                                                
              and a.cd_vlntID  = b.cd_vlntID
              and a.cd_grpID   = c.cd_grpID
              and a.cd_sbgrpID = c.cd_sbgrpID                     

					union all

					select a.seql_vnclID as sequencial_base,					
						   a.cd_vlntID as cd_vlnt,
                           b.nm_vlnt,
						   DATE_FORMAT(a.dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
						   DATE_FORMAT(a.dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
						   case when a.cd_est_vncl = 1 THEN 'Ativo'
							    when a.cd_est_vncl = 2 THEN 'Encerrado'
						   end as estado_vinculo,	
						   case when a.cd_atu_vlnt = 1 THEN 'Coordenador Cadastral'
							    when a.cd_atu_vlnt = 2 THEN 'Coordenador Financeiro'
							    when a.cd_atu_vlnt = 3 THEN 'Coordenador Revisor'
							    when a.cd_atu_vlnt = 4 THEN 'Coordenador Geral'
							    when a.cd_atu_vlnt = 5 THEN 'Voluntário'
						   end as cod_atuacao_base,
               '*' as nm_sbgrp
						from tb_vncl_vlnt_grp a,
                             tb_vlnt b
						where a.cd_grpID   = :cd_grp
              and (a.dt_inc_vncl between str_to_date(:dt_inc_vncl, '%d/%m/%Y') and 
							   				        str_to_date(:dt_fim_vncl, '%d/%m/%Y')
					     or a.dt_fim_vncl > str_to_date(:dt_fim_vncl, '%d/%m/%Y'))                                                
              and a.cd_vlntID  = b.cd_vlntID                     
              and a.cd_sbgrpID is null                       
                                                
            order by 3, 8, 1;";

			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':cd_grp', $this->__get('codGrupo_pesq'));
			$stmt->bindValue('dt_inc_vncl', $this->__get('dataInicio_pesq'));
			$stmt->bindValue('dt_fim_vncl', $this->__get('dataFim_pesq'));
			$stmt->execute();
		
		} else {
			$query = "
					select a.seql_vnclID as sequencial_base,					
						   a.cd_vlntID as cd_vlnt,
                           b.nm_vlnt,
						   DATE_FORMAT(a.dt_inc_vncl, '%d/%m/%Y') as data_inicio_vinculo,
						   DATE_FORMAT(a.dt_fim_vncl, '%d/%m/%Y') as data_fim_vinculo,
						   case when a.cd_est_vncl = 1 THEN 'Ativo'
							    when a.cd_est_vncl = 2 THEN 'Encerrado'
						   end as estado_vinculo,	
						   case when a.cd_atu_vlnt = 1 THEN 'Coordenador Cadastral'
							    when a.cd_atu_vlnt = 2 THEN 'Coordenador Financeiro'
							    when a.cd_atu_vlnt = 3 THEN 'Coordenador Revisor'
							    when a.cd_atu_vlnt = 4 THEN 'Coordenador Geral'
							    when a.cd_atu_vlnt = 5 THEN 'Voluntário'
						   end as cod_atuacao_base,
						   '' as nm_sbgrp
						from tb_vncl_vlnt_grp a,
                             tb_vlnt b
						where a.cd_grpID   = :cd_grp
						and   a.cd_sbgrpID = :cd_sbgrp
                        and  (a.dt_inc_vncl between str_to_date(:dt_inc_vncl, '%d/%m/%Y') and 
							   				        str_to_date(:dt_fim_vncl, '%d/%m/%Y')
					    or    a.dt_fim_vncl > str_to_date(:dt_fim_vncl, '%d/%m/%Y'))                                                
                        and   a.cd_vlntID  = b.cd_vlntID
                        order by b.nm_vlnt, a.seql_vnclID";
			$stmt = $this->db->prepare($query);
			$stmt->bindValue('cd_grp', $this->__get('codGrupo_pesq'));
			$stmt->bindValue('cd_sbgrp', $this->__get('codSubgrupo_pesq'));
			$stmt->bindValue('dt_inc_vncl', $this->__get('dataInicio_pesq'));
			$stmt->bindValue('dt_fim_vncl', $this->__get('dataFim_pesq'));
			$stmt->execute();
		}

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	//	Fim function getDadosVVGAll_2

// ====================================================== //

	public function getNivelAtuacaoGrupo() {
		$query = "
			select count(*) as qtde
			from tb_vncl_vlnt_grp
			where cd_vlntID   = :cd_vlnt
			and   cd_grpID    = :cd_grp
			and   cd_est_vncl = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_vlnt', $this->__get('codVoluntario'));
		$stmt->bindValue(':cd_grp', $this->__get('codGrupo'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);

	}



} 	// FIm da classe 
?>