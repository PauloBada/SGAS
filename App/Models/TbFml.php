<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 03/07/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbFml extends Model {
	
	// colunas da tabela
	private $cd_fmlID;             
	private $nm_grp_fmlr;          
	private $nm_astd_prin;         
	private $dsc_end;      
	private $cd_reg_adm;           
	private $dsc_pto_refe;         
	private $cd_atndt_ant_fml;     
	private $fone_1;               
	private $fone_2;               
	private $fone_3;               
	private $dsc_cndc_saude;       
	private $dsc_sust_fml;         
	private $cd_crit_engjto;       
	private $cd_tip_resid;         
	private $cd_tip_edif_resid;    
	private $dsc_anot_atnd_fraterno;
	private $rsp_enca_DAO;         
	private $dt_cadastro_fml;      
	private $dt_inc_acomp;         
	private $dt_prev_term_acomp;   
	private $dt_enct_acomp;        
	private $cd_est_situ_fml;      
	private $cd_atendto_fml_subs;  
	private $ptc_atendto_fml;      
	private $pos_ranking_atendto_fml; 
	private $cd_vlnt_resp_cadastro; 
	private $vlr_aprox_renda_mensal_fml; 

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //
	                
	public function getDadosFamiliasAll() {
		$query = "
				select cd_fmlID, 
				       nm_grp_fmlr,
				       nm_astd_prin,
				       DATE_FORMAT(dt_cadastro_fml, '%d/%m/%Y') as data_cadastro,
				       cd_reg_adm
				from  tb_fml
				where cd_est_situ_fml = 1
				order by nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
	}	// Fim function getDadosVoluntariosAll



// ====================================================== //
	
	public function updateSituFamilia() {
		$query = "
				update  tb_fml
				set     cd_est_situ_fml = :cd_est_situ_fml
				where   cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt->bindValue(':cd_est_situ_fml', $this->__get('situFamilia'));
		$stmt->execute();

		return $this;	
	}	// Fim function updateSituFamilia

// ====================================================== //

	public function obtemProxCdFml() {
		$query = "
				select max(cd_fmlID) as max_cd_fml
				from  tb_fml";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function insertFamilia() {

		// Para gravar nulo nos campos quando não houver informação
		if (empty($this->__get('fone_1'))) {
			$fone_1 = null;
		} else {
			$fone_1 = $this->__get('fone_1');
		}

		if (empty($this->__get('fone_2'))) {
			$fone_2 = null;
		} else {
			$fone_2 = $this->__get('fone_2');
		}

		if (empty($this->__get('fone_3'))) {
			$fone_3 = null;
		} else {
			$fone_3 = $this->__get('fone_3');
		}

		if (empty($this->__get('dsc_cndc_saude'))) {
			$dsc_cndc_saude = null;
		} else {
			$dsc_cndc_saude = $this->__get('dsc_cndc_saude');
		}

		if (empty($this->__get('dsc_sust_fml'))) {
			$dsc_sust_fml = null;
		} else {
			$dsc_sust_fml = $this->__get('dsc_sust_fml');
		}

		if (empty($this->__get('dsc_anot_atnd_fraterno'))) {
			$dsc_anot_atnd_fraterno = null;
		} else {
			$dsc_anot_atnd_fraterno = $this->__get('dsc_anot_atnd_fraterno');
		}

		if (empty($this->__get('rsp_enca_DAO'))) {
			$rsp_enca_DAO = null;
		} else {
			$rsp_enca_DAO = $this->__get('rsp_enca_DAO');
		}

		if (empty($this->__get('vlr_aprox_renda_mensal_fml'))) {
			$vlr_aprox_renda_mensal_fml = 0;
		} else {
			$vlr_aprox_renda_mensal_fml = $this->__get('vlr_aprox_renda_mensal_fml');
		}

		if (empty($this->__get('cd_atndt_ant_fml'))) {
			$cd_atndt_ant_fml = 0;
		} else {
			$cd_atndt_ant_fml = $this->__get('cd_atndt_ant_fml');
		}

		$query = "
				insert into tb_fml
				(cd_fmlID,
				nm_grp_fmlr,
				nm_astd_prin,
				dsc_end,
				cd_reg_adm,
				dsc_pto_refe,
				cd_atndt_ant_fml,
				fone_1,
				fone_2,
				fone_3,
				dsc_cndc_saude,
				dsc_sust_fml,
				cd_crit_engjto,
				cd_tip_resid,
				cd_tip_edif_resid,
				dsc_anot_atnd_fraterno,
				rsp_enca_DAO,
				dt_cadastro_fml,
				dt_inc_acomp,
				dt_prev_term_acomp,
				dt_enct_acomp,
				cd_est_situ_fml,
				cd_atendto_fml_subs,
				ptc_atendto_fml,
				pos_ranking_atendto_fml,
				cd_vlnt_resp_cadastro,
				vlr_aprox_renda_mensal_fml) 

				values 

				(:cd_fml,
				:nm_grp_fmlr,
				:nm_astd_prin,
				:dsc_end,
				:cd_reg_adm,
				:dsc_pto_refe,
				:cd_atndt_ant_fml,
				:fone_1,
				:fone_2,
				:fone_3,
				:dsc_cndc_saude,
				:dsc_sust_fml,
				:cd_crit_engjto,
				:cd_tip_resid,
				:cd_tip_edif_resid,
				:dsc_anot_atnd_fraterno,
				:rsp_enca_DAO,
				now(),
				null,
				null,
				null,
				:cd_est_situ_fml,
				:cd_atendto_fml_subs,
				:ptc_atendto_fml,
				:pos_ranking_atendto_fml,
				:cd_vlnt_resp_cadastro,
				:vlr_aprox_renda_mensal_fml)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':nm_grp_fmlr', $this->__get('nm_grp_fmlr'));
		$stmt->bindValue(':nm_astd_prin', $this->__get('nm_astd_prin'));
		$stmt->bindValue(':dsc_end', $this->__get('dsc_end'));
		$stmt->bindValue(':cd_reg_adm', $this->__get('cd_reg_adm'));
		$stmt->bindValue(':dsc_pto_refe', $this->__get('dsc_pto_refe'));
		$stmt->bindValue(':cd_atndt_ant_fml', $cd_atndt_ant_fml);
		$stmt->bindValue(':fone_1', $fone_1);
		$stmt->bindValue(':fone_2', $fone_2);
		$stmt->bindValue(':fone_3', $fone_3);
		$stmt->bindValue(':dsc_cndc_saude', $dsc_cndc_saude);
		$stmt->bindValue(':dsc_sust_fml', $dsc_sust_fml);
		$stmt->bindValue(':cd_crit_engjto', $this->__get('cd_crit_engjto'));
		$stmt->bindValue(':cd_tip_resid', $this->__get('cd_tip_resid'));
		$stmt->bindValue(':cd_tip_edif_resid', $this->__get('cd_tip_edif_resid'));
		$stmt->bindValue(':dsc_anot_atnd_fraterno', $dsc_anot_atnd_fraterno);
		$stmt->bindValue(':rsp_enca_DAO', $rsp_enca_DAO);
		$stmt->bindValue(':cd_est_situ_fml', 1);
		$stmt->bindValue(':cd_atendto_fml_subs', null);
		$stmt->bindValue(':ptc_atendto_fml', 0);
		$stmt->bindValue(':pos_ranking_atendto_fml', 0);
		$stmt->bindValue(':cd_vlnt_resp_cadastro', $this->__get('cd_vlnt_resp_cadastro'));
		$stmt->bindValue(':vlr_aprox_renda_mensal_fml', $vlr_aprox_renda_mensal_fml);
		$stmt->execute();

		return $this;

	}	// Fim function insertFamilia

// ====================================================== //

	public function getDadosFamiliaAll2() {
		$query = "
				select 	cd_fmlID, 
						nm_grp_fmlr
				from  tb_fml
				where cd_est_situ_fml between 1 and 3
				order by nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}	// Fim function getDadosFamiliasAll2

// ====================================================== //

	public function updateFamilia() {

		// Para gravar nulo nos campos quando não houver informação
		if (empty($this->__get('fone_1'))) {
			$fone_1 = null;
		} else {
			$fone_1 = $this->__get('fone_1');
		}

		if (empty($this->__get('fone_2'))) {
			$fone_2 = null;
		} else {
			$fone_2 = $this->__get('fone_2');
		}

		if (empty($this->__get('fone_3'))) {
			$fone_3 = null;
		} else {
			$fone_3 = $this->__get('fone_3');
		}

		if (empty($this->__get('dsc_cndc_saude'))) {
			$dsc_cndc_saude = null;
		} else {
			$dsc_cndc_saude = $this->__get('dsc_cndc_saude');
		}

		if (empty($this->__get('dsc_sust_fml'))) {
			$dsc_sust_fml = null;
		} else {
			$dsc_sust_fml = $this->__get('dsc_sust_fml');
		}

		if (empty($this->__get('dsc_anot_atnd_fraterno'))) {
			$dsc_anot_atnd_fraterno = null;
		} else {
			$dsc_anot_atnd_fraterno = $this->__get('dsc_anot_atnd_fraterno');
		}

		if (empty($this->__get('rsp_enca_DAO'))) {
			$rsp_enca_DAO = null;
		} else {
			$rsp_enca_DAO = $this->__get('rsp_enca_DAO');
		}

		if (empty($this->__get('vlr_aprox_renda_mensal_fml'))) {
			$vlr_aprox_renda_mensal_fml = 0;
		} else {
			$vlr_aprox_renda_mensal_fml = $this->__get('vlr_aprox_renda_mensal_fml');
		}

		$query = "
				update tb_fml
				set	nm_grp_fmlr 				= :nm_grp_fmlr,
					nm_astd_prin				= :nm_astd_prin,
					dsc_end						= :dsc_end,
					cd_reg_adm					= :cd_reg_adm,
					dsc_pto_refe				= :dsc_pto_refe,
					fone_1						= :fone_1,
					fone_2						= :fone_2,
					fone_3						= :fone_3,
					dsc_cndc_saude				= :dsc_cndc_saude,
					dsc_sust_fml				= :dsc_sust_fml,
					cd_crit_engjto				= :cd_crit_engjto,
					cd_tip_resid				= :cd_tip_resid,
					cd_tip_edif_resid			= :cd_tip_edif_resid,
					dsc_anot_atnd_fraterno		= :dsc_anot_atnd_fraterno,
					rsp_enca_DAO				= :rsp_enca_DAO,
					cd_vlnt_resp_cadastro		= :cd_vlnt_resp_cadastro,
					vlr_aprox_renda_mensal_fml	= :vlr_aprox_renda_mensal_fml
				where cd_fmlID 					= :cd_fml";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':nm_grp_fmlr', $this->__get('nm_grp_fmlr'));
		$stmt->bindValue(':nm_astd_prin', $this->__get('nm_astd_prin'));
		$stmt->bindValue(':dsc_end', $this->__get('dsc_end'));
		$stmt->bindValue(':cd_reg_adm', $this->__get('cd_reg_adm'));
		$stmt->bindValue(':dsc_pto_refe', $this->__get('dsc_pto_refe'));
		$stmt->bindValue(':fone_1', $fone_1);
		$stmt->bindValue(':fone_2', $fone_2);
		$stmt->bindValue(':fone_3', $fone_3);
		$stmt->bindValue(':dsc_cndc_saude', $dsc_cndc_saude);
		$stmt->bindValue(':dsc_sust_fml', $dsc_sust_fml);
		$stmt->bindValue(':cd_crit_engjto', $this->__get('cd_crit_engjto'));
		$stmt->bindValue(':cd_tip_resid', $this->__get('cd_tip_resid'));
		$stmt->bindValue(':cd_tip_edif_resid', $this->__get('cd_tip_edif_resid'));
		$stmt->bindValue(':dsc_anot_atnd_fraterno', $dsc_anot_atnd_fraterno);
		$stmt->bindValue(':rsp_enca_DAO', $rsp_enca_DAO);
		$stmt->bindValue(':cd_vlnt_resp_cadastro', $this->__get('cd_vlnt_resp_cadastro'));
		$stmt->bindValue(':vlr_aprox_renda_mensal_fml', $vlr_aprox_renda_mensal_fml);
		$stmt->execute();

		return $this;

	}	// Fim function updateFamilia


// ====================================================== //
	                
	public function getQtdFamiliasSemVFS() {
		$query = "
				select count(*) as qtde
				from  tb_fml
				where cd_est_situ_fml = 1";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
			
	}	// Fim function getQtdFamiliasSemVFS

// ====================================================== //
	                
	public function getPreInclusaoFamilia2() {
		$query = "
				select cd_fmlID
				from   tb_fml
				where cd_est_situ_fml between 1 and 4
				and   nm_astd_prin like :nm_astd_prin
				     
				union 

				select cd_fmlID
				from   tb_integ_fml
				where  nm_integ like :nm_astd_prin
				and    cd_fmlID in (select cd_fmlID
									from tb_fml
									where cd_est_situ_fml between 1 and 4)
				or     cpf       = :cpf
				order by 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_astd_prin', '%'.$this->__get('nm_astd_prin').'%');
		$stmt->bindValue(':cpf', $this->__get('cpf'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
	}	// Fim function getPreInclusaoFamilia2

// ====================================================== //
	                
	public function getPreInclusaoFamilia3() {
		$query = "
				select cd_fmlID
				from   tb_fml
				where cd_est_situ_fml between 1 and 4
				and   nm_astd_prin like :nm_astd_prin
				     
				union 

				select cd_fmlID
				from   tb_integ_fml
				where  nm_integ like :nm_astd_prin
				and    cd_fmlID in (select cd_fmlID
									from tb_fml
									where cd_est_situ_fml between 1 and 4)
				order by 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_astd_prin', '%'.$this->__get('nm_astd_prin').'%');
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
	}	// Fim function getPreInclusaoFamilia3

// ====================================================== //

	public function getDadosFamilia() {
		$query = "
				select cd_fmlID, 
				       nm_grp_fmlr,
				       nm_astd_prin,
				       dsc_end,
				       cd_reg_adm,
				       dsc_pto_refe,
				       cd_atndt_ant_fml,
				       fone_1,
				       fone_2, 
				       fone_3,
				       dsc_cndc_saude,
				       dsc_sust_fml,
				       cd_crit_engjto,
				       cd_tip_resid,
				       cd_tip_edif_resid,
				       dsc_anot_atnd_fraterno,
				       rsp_enca_DAO,
				       dt_cadastro_fml,
				       dt_inc_acomp,
				       dt_prev_term_acomp,
				       dt_enct_acomp,
				       cd_est_situ_fml,
				       cd_atendto_fml_subs,
				       ptc_atendto_fml,
				       pos_ranking_atendto_fml,
				       cd_vlnt_resp_cadastro,
				       vlr_aprox_renda_mensal_fml
				from  tb_fml
				where cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('codFamilia'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}	// Fim function getDadosFamilia

// ====================================================== //

	// Pega o nome do Grupo e Subgrupo na segunda linha, quando houver
	public function getDadosFamilia2() {
		$query = "
				select cd_fmlID, 
				       nm_grp_fmlr,
				       nm_astd_prin,
				       dsc_end,
				       cd_reg_adm,
				       dsc_pto_refe,
				       cd_atndt_ant_fml,
				       fone_1,
				       fone_2, 
				       fone_3,
				       dsc_cndc_saude,
				       dsc_sust_fml,
				       cd_crit_engjto,
				       cd_tip_resid,
				       cd_tip_edif_resid,
				       dsc_anot_atnd_fraterno,
				       rsp_enca_DAO,
				       dt_cadastro_fml,
				       dt_inc_acomp,
				       dt_prev_term_acomp,
				       dt_enct_acomp,
				       cd_est_situ_fml,
				       cd_atendto_fml_subs,
				       ptc_atendto_fml,
				       pos_ranking_atendto_fml,
				       cd_vlnt_resp_cadastro,
				       vlr_aprox_renda_mensal_fml,
				       'a',
				       'b'
				from  tb_fml
				where cd_fmlID = :cd_fml

				union

				select a.cd_fmlID, 
				       a.nm_grp_fmlr,
				       a.nm_astd_prin,
				       a.dsc_end,
				       a.cd_reg_adm,
				       a.dsc_pto_refe,
				       a.cd_atndt_ant_fml,
				       a.fone_1,
				       a.fone_2, 
				       a.fone_3,
				       a.dsc_cndc_saude,
				       a.dsc_sust_fml,
				       a.cd_crit_engjto,
				       a.cd_tip_resid,
				       a.cd_tip_edif_resid,
				       a.dsc_anot_atnd_fraterno,
				       a.rsp_enca_DAO,
				       a.dt_cadastro_fml,
				       a.dt_inc_acomp,
				       a.dt_prev_term_acomp,
				       a.dt_enct_acomp,
				       a.cd_est_situ_fml,
				       a.cd_atendto_fml_subs,
				       a.ptc_atendto_fml,
				       a.pos_ranking_atendto_fml,
				       a.cd_vlnt_resp_cadastro,
				       a.vlr_aprox_renda_mensal_fml,
                       c.nm_grp, 
                       d.nm_sbgrp
				from  tb_fml a,
					  tb_vncl_fml_sbgrp b,
                      tb_grp c,
                      tb_sbgrp d
				where a.cd_fmlID    = :cd_fml
                and   a.cd_fmlID    = b.cd_fmlID
                and   b.cd_est_vncl = 1
                and   b.cd_grpID    = c.cd_grpID
                and   b.cd_grpID    = d.cd_grpID
                and	  b.cd_sbgrpID  = d.cd_sbgrpID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}	// Fim function getDadosFamilia2

// ====================================================== //
	                
	public function getPreInclusaoFamilia4() {
		$query = "
				select cd_fmlID
				from   tb_fml
				where nm_grp_fmlr like :nm_grp_fmlr
				order by nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':nm_grp_fmlr', '%'.$this->__get('nm_grp_fmlr').'%');
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
	}	// Fim function getPreInclusaoFamilia4

// ====================================================== //
	
	public function updateFamiliaAnterior4() {
		$query = "
				update  tb_fml
				set     cd_atndt_ant_fml = :cd_atndt_ant_fml
				where   cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt->bindValue(':cd_atndt_ant_fml', $this->__get('codFamiliaAnterior'));
		$stmt->execute();

		return $this;	
	}	// Fim function updateFamiliaAnterior4

// ====================================================== //
	
	public function getFamiliaPorGrupoRT() {
		$query = "
				select 	*
				from tb_fml
				where  cd_fmlID in (select cd_fmlID
									from  tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp
									and   cd_est_vncl = 1)
				and cd_est_situ_fml = :cd_est_situ_fml

				and cd_fmlID not in (select cd_fmlID
						   			 from tb_acomp_fml
									 where  cd_est_acomp = 2)

				order by nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue('cd_sbgrp', $this->__get('codSubgrupo'));
		$stmt->bindValue('cd_est_situ_fml', $this->__get('codEstSituFml'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getFamiliaPorGrupoRT

// ====================================================== //
	
	public function getFamiliaPorGrupoAcompanhamento() {
		$query = "
				select 	*
				from tb_fml
				where  cd_fmlID in (select cd_fmlID
									from  tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp
									and   cd_est_vncl = 1)

				and    cd_fmlID in (select cd_fmlID
									from  tb_acomp_fml
									where cd_atvd_acomp = :cd_atvd_acomp
									and   cd_est_acomp  between :cd_ini_acomp and :cd_fim_acomp)			

				and cd_est_situ_fml = :cd_est_situ_fml
				order by nm_grp_fmlr";
				
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_atvd_acomp', $this->__get('cd_atvd_acomp'));
		$stmt->bindValue('cd_ini_acomp', $this->__get('codInicialAcomp'));
		$stmt->bindValue('cd_fim_acomp', $this->__get('codFinalAcomp'));
		$stmt->bindValue('cd_est_situ_fml', $this->__get('codEstSituFml'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getFamiliaPorGrupoAcompanhamento

// ====================================================== //
	                
	public function getFamiliasEmAtendimento() {
		$query = "
				select cd_fmlID as cd_fml,
					   nm_grp_fmlr as nm_grp_fmlr
				from   tb_fml 
				where  cd_est_situ_fml = 3
				and    cd_fmlID in (select cd_fmlID
									from tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp
									and   cd_est_vncl = 1)
				order by nm_grp_fmlr";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
	}	// Fim function getFamiliasEmAtendimento

// ====================================================== //
	
	public function updateCritEngajamentoFamilia() {
		$query = "
				update  tb_fml
				set     cd_crit_engjto = :cd_crit_engjto
				where   cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_crit_engjto', $this->__get('cd_crit_engjto'));
		$stmt->execute();

		return $this;	
		
	}	// Fim function updateIncioAcompanhamentoFamilia

// ====================================================== //
	
	public function updateEstSituFamilia() {
		$query = "
				update  tb_fml
				set     cd_est_situ_fml = :cd_est_situ_fml
				where   cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_est_situ_fml', $this->__get('cd_est_situ_fml'));
		$stmt->execute();

		return $this;	
		
	}	// Fim function updateEstSituFamilia


// ====================================================== //
	
	public function updateInicioAcompanhamentoFamilia() {
		$query = "
				update  tb_fml
				set     dt_inc_acomp        = now(),
						dt_prev_term_acomp  = str_to_date(:dt_prev_term_acomp, '%d/%m/%Y'), 
						cd_atendto_fml_subs = :cd_atendto_fml_subs

				where   cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('dt_prev_term_acomp', $this->__get('dt_prev_term_acomp'));
		$stmt->bindValue('cd_atendto_fml_subs', $this->__get('cd_atendto_fml_subs'));
		$stmt->execute();

		return $this;	
		
	}	// Fim function updateInicioAcompanhamentoFamilia

// ====================================================== //
	
	public function getFamiliaPorGrupoRD() {
		$query = "
				select 	*
				from tb_fml
				where  cd_fmlID in (select cd_fmlID
									from  tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp
									and   cd_est_vncl = 1)
				and cd_est_situ_fml = :cd_est_situ_fml
				order by nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('codGrupo'));
		$stmt->bindValue('cd_sbgrp', $this->__get('codSubgrupo'));
		$stmt->bindValue('cd_est_situ_fml', $this->__get('codEstSituFml'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getFamiliaPorGrupoRD

// ====================================================== //
	
	public function updateTerminoAcompanhamentoFamilia() {
		$query = "
				update  tb_fml
				set     dt_enct_acomp   = now(),
						cd_est_situ_fml = :cd_est_situ_fml
				where   cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_est_situ_fml', 4);
		$stmt->execute();

		return $this;	
		
	}	// Fim function updateTerminoAcompanhamentoFamilia


// ====================================================== //
	                
	public function getFamiliaSubstituida() {
		$query = "
				select cd_fmlID, 
					   nm_grp_fmlr
				from   tb_fml
				where  cd_atendto_fml_subs = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);
			
	}	// Fim function getFamiliaSubstituida

// xxxxx

// ====================================================== //
	
	public function getConsultaFamiliasAcompanhamento() {
		$query = "
				select 	*
				from tb_fml
				where  cd_fmlID in (select cd_fmlID
									from  tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp
									and   cd_est_vncl = 1)

				and    cd_fmlID in (select cd_fmlID
									from  tb_acomp_fml
									where cd_atvd_acomp in (1, 2))						
				order by nm_grp_fmlr";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getConsultaFamiliasAcompanhamento

// ====================================================== //
	
	public function getDadosConsulta1RankingFml() {
		$query = "
				select	a.cd_fmlID as cd_fml, 
						a.nm_grp_fmlr as nm_grp_fmlr, 
						a.dt_cadastro_fml as dt_cadastro_fml,  
						c.cd_grpid as cd_grp, 
						c.nm_grp as nm_grp, 
						d.cd_sbgrpid as cd_sbgrp, 
						d.nm_sbgrp as nm_sbgrp
				from	tb_fml a,
						tb_vncl_fml_sbgrp b,
						tb_grp c,
						tb_sbgrp d
				where	a.cd_est_situ_fml	=	2
				and		a.cd_fmlID			=	b.cd_fmlID
				and		b.cd_est_vncl		=	1
				and		b.cd_grpID			=	c.cd_grpID
				and		b.cd_grpID			=	d.cd_grpID
				and		b.cd_sbgrpID		=	d.cd_sbgrpID
				order by c.cd_grpid, d.cd_sbgrpid, a.dt_cadastro_fml";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getDadosConsulta1RankingFml

// ====================================================== //
	
	public function getDadosConsulta2RankingFml() {
		$query = "
				select	a.cd_fmlID as cd_fml, 
						a.nm_grp_fmlr as nm_grp_fmlr, 
						a.dt_cadastro_fml as dt_cadastro_fml,  
						c.cd_grpid as cd_grp, 
						c.nm_grp as nm_grp, 
						d.cd_sbgrpid as cd_sbgrp, 
						d.nm_sbgrp as nm_sbgrp
				from	tb_fml a,
						tb_vncl_fml_sbgrp b,
						tb_grp c,
						tb_sbgrp d
				where	a.cd_est_situ_fml	=	2
				and		a.cd_fmlID			=	b.cd_fmlID
				and		b.cd_est_vncl		=	1
				and		b.cd_grpID			=	c.cd_grpID
				and		b.cd_grpID			=	d.cd_grpID
				and		b.cd_sbgrpID		=	d.cd_sbgrpID
				and		b.cd_grpID          =   :cd_grp
				order by c.cd_grpID, d.cd_sbgrpID, a.dt_cadastro_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getDadosConsulta2RankingFml

// ====================================================== //
	
	public function getDadosConsulta3RankingFml() {
		$query = "
				select	a.cd_fmlID as cd_fml, 
						a.nm_grp_fmlr as nm_grp_fmlr, 
						a.dt_cadastro_fml as dt_cadastro_fml,  
						c.cd_grpid as cd_grp, 
						c.nm_grp as nm_grp, 
						d.cd_sbgrpid as cd_sbgrp, 
						d.nm_sbgrp as nm_sbgrp
				from	tb_fml a,
						tb_vncl_fml_sbgrp b,
						tb_grp c,
						tb_sbgrp d
				where	a.cd_est_situ_fml	=	2
				and		a.cd_fmlID			=	b.cd_fmlID
				and		b.cd_est_vncl		=	1
				and		b.cd_grpID			=	c.cd_grpID
				and		b.cd_grpID			=	d.cd_grpID
				and		b.cd_sbgrpID		=	d.cd_sbgrpID
				and		b.cd_grpID          =   :cd_grp
				and		b.cd_sbgrpID        =   :cd_sbgrp
				order by c.cd_grpID, d.cd_sbgrpID, a.dt_cadastro_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getDadosConsulta3RankingFml

// ====================================================== //
	
	public function updatePontosPosicaoRanking() {
		$query = "
				update  tb_fml
				set     ptc_atendto_fml         = :ptc_atendto_fml,
						pos_ranking_atendto_fml = :pos_ranking_atendto_fml
				where   cd_fmlID = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('ptc_atendto_fml', $this->__get('ptc_atendto_fml'));
		$stmt->bindValue('pos_ranking_atendto_fml', $this->__get('pos_ranking_atendto_fml'));
		$stmt->execute();

		return $this;	

	}	// Fim function updatePontosPosicaoRanking

// ====================================================== //

	public function getDadosFamiliaAll3() {
		$query = "
				select 	cd_fmlID, 
						nm_grp_fmlr
				from  tb_fml
				order by nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
			
	}	// Fim function getDadosFamiliaAll3

// ====================================================== //
	
	public function getConsultaFamiliasNeces() {
		$query = "
				select 	*
				from tb_fml
				where  cd_fmlID in (select cd_fmlID
									from  tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp
									and   cd_est_vncl = 1)
				and     cd_est_situ_fml in (2, 3)
				order by nm_grp_fmlr";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}	// Fim function getConsultaFamiliasNeces

}	// Fim da Classe

?>