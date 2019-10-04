<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 07/08/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbAcompFml extends Model {
	
	// colunas da tabela
	private $cd_fmlID;             
	private $seql_acompID;             
	private $dt_acomp;          
	private $cd_atvd_acomp;
	private $cd_est_acomp;
	private $cd_avalia_triagem;
	private $dsc_mtv_cd_avalia_triagem;         
	private $dsc_consid_finais_triagem;
	private $dt_reg_visita;              
	private $nr_bolsa_canguru;        
	private $vlr_doado_finan_comunhao_visita;
	private $vlr_doado_sbgrp_visita;
	private $dsc_item_doado_comunhao_visita;    
	private $dsc_item_doado_sbgrp_visita;
	private $dsc_resumo_visita;
	private $dsc_pend_visita;
	private $dsc_meta_alcan;
	private $dsc_consid_final;
	private $dsc_consid_final_visita;
	private $dsc_recado_to_coordenacao;
	private $dsc_recado_coordenacao_to_sbgrp;
	private $ts_est_acomp;
	private $cd_fml_subs_triagem;
	private $pos_ranking_momnto_triagem;
	private $dsc_situ_antes_depois_acomp;
	private $dsc_objtvo_alcan_final_acomp;
	private $dsc_acao_realzda_acomp;
	private $dsc_dificuldade_encont_acomp;
	private $dsc_consid_final_acomp;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //

	public function getQtdTriagemVisita() {
		$query = "
				select seql_acompID as seql_acomp
				from tb_acomp_fml 
				where cd_fmlID      = :cd_fml
				and   cd_atvd_acomp = :cd_atvd_acomp
				and   cd_est_acomp between :cd_est_ini and :cd_est_fim";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt->bindValue(':cd_atvd_acomp', $this->__get('codAtvdAcomp'));
		$stmt->bindValue(':cd_est_ini', $this->__get('codEstIni'));
		$stmt->bindValue(':cd_est_fim', $this->__get('codEstFim'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function insertAcompanhamentoFamilia() {
		// Para gravar nulo nos campos quando não houver informação
		
		if (null !== ($this->__get('cd_avalia_triagem'))) {
			if (empty($this->__get('cd_avalia_triagem'))) {
				$cd_avalia_triagem = 0;
			} else {
				$cd_avalia_triagem = $this->__get('cd_avalia_triagem');
			}
		} else {
			$cd_avalia_triagem = 0;
		}

		if (null !== ($this->__get('dsc_mtv_cd_avalia_triagem'))) {
			if (empty($this->__get('dsc_mtv_cd_avalia_triagem'))) {
				$dsc_mtv_cd_avalia_triagem = null;
			} else {
				$dsc_mtv_cd_avalia_triagem = $this->__get('dsc_mtv_cd_avalia_triagem');
			}
		} else {
			$dsc_mtv_cd_avalia_triagem = null;
		}

		if (null !== ($this->__get('dsc_consid_finais_triagem'))) {
			if (empty($this->__get('dsc_consid_finais_triagem'))) {
				$dsc_consid_finais_triagem = ' ';
			} else {
				$dsc_consid_finais_triagem = $this->__get('dsc_consid_finais_triagem');
			}
		} else {
			$dsc_consid_finais_triagem = ' ';
		}

		if (null !== ($this->__get('nr_bolsa_canguru'))) {
			if (empty($this->__get('nr_bolsa_canguru'))) {
				$nr_bolsa_canguru = 0;
			} else {
				$nr_bolsa_canguru = $this->__get('nr_bolsa_canguru');
			}
		} else {
			$nr_bolsa_canguru = 0;
		}

		if (null !== ($this->__get('vlr_doado_finan_comunhao_visita'))) {
			if (empty($this->__get('vlr_doado_finan_comunhao_visita'))) {
				$vlr_doado_finan_comunhao_visita = 0;
			} else {
				$vlr_doado_finan_comunhao_visita = str_replace('.','', $this->__get('vlr_doado_finan_comunhao_visita'));
				$vlr_doado_finan_comunhao_visita = str_replace(',','.', $vlr_doado_finan_comunhao_visita);
			}
		} else {
			$vlr_doado_finan_comunhao_visita = 0;
		}

		if (null !== ($this->__get('vlr_doado_sbgrp_visita'))) {
			if (empty($this->__get('vlr_doado_sbgrp_visita'))) {
				$vlr_doado_sbgrp_visita = 0;
			} else {
				$vlr_doado_sbgrp_visita = str_replace('.','', $this->__get('vlr_doado_sbgrp_visita'));
				$vlr_doado_sbgrp_visita = str_replace(',','.', $vlr_doado_sbgrp_visita);
			}
		} else {
			$vlr_doado_sbgrp_visita = 0;
		}

		if (null !== ($this->__get('dsc_item_doado_comunhao_visita'))) {
			if (empty($this->__get('dsc_item_doado_comunhao_visita'))) {
				$dsc_item_doado_comunhao_visita = null;
			} else {
				$dsc_item_doado_comunhao_visita = $this->__get('dsc_item_doado_comunhao_visita');
			}
		} else {
			$dsc_item_doado_comunhao_visita = null;
		}

		if (null !== ($this->__get('dsc_item_doado_sbgrp_visita'))) {
			if (empty($this->__get('dsc_item_doado_sbgrp_visita'))) {
				$dsc_item_doado_sbgrp_visita = null;
			} else {
				$dsc_item_doado_sbgrp_visita = $this->__get('dsc_item_doado_sbgrp_visita');
			}
		} else {
			$dsc_item_doado_sbgrp_visita = null;
		}

		if (null !== ($this->__get('dsc_resumo_visita'))) {
			if (empty($this->__get('dsc_resumo_visita'))) {
				$dsc_resumo_visita = null;
			} else {
				$dsc_resumo_visita = $this->__get('dsc_resumo_visita');
			}
		} else {
			$dsc_resumo_visita = null;
		}

		if (null !== ($this->__get('dsc_pend_visita'))) {
			if (empty($this->__get('dsc_pend_visita'))) {
				$dsc_pend_visita = null;
			} else {
				$dsc_pend_visita = $this->__get('dsc_pend_visita');
			}
		} else {
			$dsc_pend_visita = null;
		}

		if (null !== ($this->__get('dsc_meta_alcan'))) {
			if (empty($this->__get('dsc_meta_alcan'))) {
				$dsc_meta_alcan = null;
			} else {
				$dsc_meta_alcan = $this->__get('dsc_meta_alcan');
			}
		} else {
			$dsc_meta_alcan = null;
		}

		if (null !== ($this->__get('dsc_consid_final'))) {
			if (empty($this->__get('dsc_consid_final'))) {
				$dsc_consid_final = null;
			} else {
				$dsc_consid_final = $this->__get('dsc_consid_final');
			}
		} else {
			$dsc_consid_final = null;
		}

		if (null !== ($this->__get('dsc_recado_to_coordenacao'))) {
			if (empty($this->__get('dsc_recado_to_coordenacao'))) {
				$dsc_recado_to_coordenacao = null;
			} else {
				$dsc_recado_to_coordenacao = $this->__get('dsc_recado_to_coordenacao');
			}
		} else {
			$dsc_recado_to_coordenacao = null;
		}

		if (null !== ($this->__get('dsc_recado_coordenacao_to_sbgrp'))) {
			if (empty($this->__get('dsc_recado_coordenacao_to_sbgrp'))) {
				$dsc_recado_coordenacao_to_sbgrp = null;
			} else {
				$dsc_recado_coordenacao_to_sbgrp = $this->__get('dsc_recado_coordenacao_to_sbgrp');
			}
		} else {
			$dsc_recado_coordenacao_to_sbgrp = null;
		}

		if (null !== ($this->__get('cd_fml_subs_triagem'))) {
			if (empty($this->__get('cd_fml_subs_triagem'))) {
				$cd_fml_subs_triagem = 0;
			} else {
				$cd_fml_subs_triagem = $this->__get('cd_fml_subs_triagem');
			}
		} else {
			$cd_fml_subs_triagem = 0;
		}

		if (null !== ($this->__get('pos_ranking_momnto_triagem'))) {
			if (empty($this->__get('pos_ranking_momnto_triagem'))) {
				$pos_ranking_momnto_triagem = 0;
			} else {
				$pos_ranking_momnto_triagem = $this->__get('pos_ranking_momnto_triagem');
			}
		} else {
			$pos_ranking_momnto_triagem = 0;
		}

		if (null !== ($this->__get('dsc_situ_antes_depois_acomp'))) {
			if (empty($this->__get('dsc_situ_antes_depois_acomp'))) {
				$dsc_situ_antes_depois_acomp = '';
			} else {
				$dsc_situ_antes_depois_acomp = $this->__get('dsc_situ_antes_depois_acomp');
			}
		} else {
			$dsc_situ_antes_depois_acomp = '';
		}

		if (null !== ($this->__get('dsc_objtvo_alcan_final_acomp'))) {
			if (empty($this->__get('dsc_objtvo_alcan_final_acomp'))) {
				$dsc_objtvo_alcan_final_acomp = '';
			} else {
				$dsc_objtvo_alcan_final_acomp = $this->__get('dsc_objtvo_alcan_final_acomp');
			}
		} else {
			$dsc_objtvo_alcan_final_acomp = '';
		}

		if (null !== ($this->__get('dsc_acao_realzda_acomp'))) {
			if (empty($this->__get('dsc_acao_realzda_acomp'))) {
				$dsc_acao_realzda_acomp = '';
			} else {
				$dsc_acao_realzda_acomp = $this->__get('dsc_acao_realzda_acomp');
			}
		} else {
			$dsc_acao_realzda_acomp = '';
		}

		if (null !== ($this->__get('dsc_dificuldade_encont_acomp'))) {
			if (empty($this->__get('dsc_dificuldade_encont_acomp'))) {
				$dsc_dificuldade_encont_acomp = '';
			} else {
				$dsc_dificuldade_encont_acomp = $this->__get('dsc_dificuldade_encont_acomp');
			}
		} else {
			$dsc_dificuldade_encont_acomp = '';
		}

		if (null !== ($this->__get('dsc_consid_final_acomp'))) {
			if (empty($this->__get('dsc_consid_final_acomp'))) {
				$dsc_consid_final_acomp = '';
			} else {
				$dsc_consid_final_acomp = $this->__get('dsc_consid_final_acomp');
			}
		} else {
			$dsc_consid_final_acomp = '';
		}

		$query = "
				insert into tb_acomp_fml
				(cd_fmlID,             
				seql_acompID,             
				dt_acomp,          
				cd_atvd_acomp,
				cd_est_acomp,
				cd_avalia_triagem,
				dsc_mtv_cd_avalia_triagem,         
				dsc_consid_finais_triagem,
				dt_reg_visita,              
				nr_bolsa_canguru,        
				vlr_doado_finan_comunhao_visita,
				vlr_doado_sbgrp_visita,
				dsc_item_doado_comunhao_visita,    
				dsc_item_doado_sbgrp_visita,
				dsc_resumo_visita,
				dsc_pend_visita,
				dsc_meta_alcan,
				dsc_consid_final_visita,
				dsc_recado_to_coordenacao,
				dsc_recado_coordenacao_to_sbgrp,
				ts_est_acomp,
				cd_fml_subs_triagem,
				pos_ranking_momnto_triagem,
				dsc_situ_antes_depois_acomp,
				dsc_objtvo_alcan_final_acomp,
				dsc_acao_realzda_acomp,
				dsc_dificuldade_encont_acomp,
				dsc_consid_final_acomp)

				values 

				(:cd_fml,             
				:seql_acomp,             
				str_to_date(:dt_acomp, '%d/%m/%Y'),   
				:cd_atvd_acomp,
				:cd_est_acomp,
				:cd_avalia_triagem,
				:dsc_mtv_cd_avalia_triagem,         
				:dsc_consid_finais_triagem,
				now(),   
				:nr_bolsa_canguru,        
				:vlr_doado_finan_comunhao_visita,
				:vlr_doado_sbgrp_visita,
				:dsc_item_doado_comunhao_visita,    
				:dsc_item_doado_sbgrp_visita,
				:dsc_resumo_visita,
				:dsc_pend_visita,
				:dsc_meta_alcan,
				:dsc_consid_final_visita,
				:dsc_recado_to_coordenacao,
				:dsc_recado_coordenacao_to_sbgrp,
				CURRENT_TIMESTAMP,
				:cd_fml_subs_triagem,
				:pos_ranking_momnto_triagem,
				:dsc_situ_antes_depois_acomp,
				:dsc_objtvo_alcan_final_acomp,
				:dsc_acao_realzda_acomp,
				:dsc_dificuldade_encont_acomp,
				:dsc_consid_final_acomp)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));			
		$stmt->bindValue('dt_acomp', $this->__get('dt_acomp'));
		$stmt->bindValue('cd_atvd_acomp', $this->__get('cd_atvd_acomp'));
		$stmt->bindValue('cd_est_acomp', 1);
		$stmt->bindValue('cd_avalia_triagem', $cd_avalia_triagem);
		$stmt->bindValue('dsc_mtv_cd_avalia_triagem', $dsc_mtv_cd_avalia_triagem);
		$stmt->bindValue('dsc_consid_finais_triagem', $dsc_consid_finais_triagem);
		$stmt->bindValue('nr_bolsa_canguru', $nr_bolsa_canguru);
		$stmt->bindValue('vlr_doado_finan_comunhao_visita', $vlr_doado_finan_comunhao_visita);
		$stmt->bindValue('vlr_doado_sbgrp_visita', $vlr_doado_sbgrp_visita);
		$stmt->bindValue('dsc_item_doado_comunhao_visita', $dsc_item_doado_comunhao_visita);
		$stmt->bindValue('dsc_item_doado_sbgrp_visita', $dsc_item_doado_sbgrp_visita);
		$stmt->bindValue('dsc_resumo_visita', $dsc_resumo_visita);
		$stmt->bindValue('dsc_pend_visita', $dsc_pend_visita);
		$stmt->bindValue('dsc_meta_alcan', $dsc_meta_alcan);
		$stmt->bindValue('dsc_consid_final_visita', $dsc_consid_final);
		$stmt->bindValue('dsc_recado_to_coordenacao', $dsc_recado_to_coordenacao);
		$stmt->bindValue('dsc_recado_coordenacao_to_sbgrp', $dsc_recado_coordenacao_to_sbgrp);
		$stmt->bindValue('cd_fml_subs_triagem', $cd_fml_subs_triagem);
		$stmt->bindValue('pos_ranking_momnto_triagem', $pos_ranking_momnto_triagem);
		$stmt->bindValue('dsc_situ_antes_depois_acomp', $dsc_situ_antes_depois_acomp);
		$stmt->bindValue('dsc_objtvo_alcan_final_acomp', $dsc_objtvo_alcan_final_acomp);
		$stmt->bindValue('dsc_acao_realzda_acomp', $dsc_acao_realzda_acomp);
		$stmt->bindValue('dsc_dificuldade_encont_acomp', $dsc_dificuldade_encont_acomp);
		$stmt->bindValue('dsc_consid_final_acomp', $dsc_consid_final_acomp);
		$stmt->execute();

		return $this;

	}	// Fim function insertAcompanhamentoFamilia

// =================================================== //

	public function getProximoSequencial() {
		$query0 = "select count(*) + 1 as qtde
					from  tb_acomp_fml
					where cd_fmlID    = :cd_fml";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "select max(seql_acompID) + 1 as qtde
					from  tb_acomp_fml
					where cd_fmlID    = :cd_fml";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue('cd_fml', $this->__get('cd_fml'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_max', $nr_registros_1['qtde']);
		}
	}

// =================================================== //

	public function getSequencial() {
		$query = "
				select max(seql_acompID) as seql_max
				from  tb_acomp_fml
				where cd_fmlID    = :cd_fml";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->execute();
		
		$seql = $stmt->fetch(\PDO::FETCH_ASSOC);		

		$this->__set ('seql_max', $seql['seql_max']);
	}

// =================================================== //

	public function updateTS() {
		$query = "
				update tb_acomp_fml
				set    ts_est_acomp = CURRENT_TIMESTAMP,
					   dt_acomp     = str_to_date(:dt_acomp, '%d/%m/%Y')
				where cd_fmlID      = :cd_fml
				and   seql_acompID  = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('dt_acomp', $this->__get('dt_acomp'));
		$stmt->execute();

		return $this;
	}

// ====================================================== //

	public function getDadosAcompTriagemVisita() {
		$query = "
				select *
				from  tb_acomp_fml 
				where cd_fmlID      = :cd_fml
				and   seql_acompID  = :seql_acomp
				and   cd_atvd_acomp = :cd_atvd_acomp
				and   cd_est_acomp between :cd_est_ini and :cd_est_fim";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_atvd_acomp', $this->__get('cd_atvd_acomp'));
		$stmt->bindValue('cd_est_ini', $this->__get('cd_est_ini'));
		$stmt->bindValue('cd_est_fim', $this->__get('cd_est_fim'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// =================================================== //

	public function updateRTAtualiza() {

		if (null !== ($this->__get('dsc_mtv_cd_avalia_triagem'))) {
			if (empty($this->__get('dsc_mtv_cd_avalia_triagem'))) {
				$dsc_mtv_cd_avalia_triagem = null;
			} else {
				$dsc_mtv_cd_avalia_triagem = $this->__get('dsc_mtv_cd_avalia_triagem');
			}
		} else {
			$dsc_mtv_cd_avalia_triagem = null;
		}

		if (null !== ($this->__get('cd_fml_subs_triagem'))) {
			if (empty($this->__get('cd_fml_subs_triagem'))) {
				$cd_fml_subs_triagem = 0;
			} else {
				$cd_fml_subs_triagem = $this->__get('cd_fml_subs_triagem');
			}
		} else {
			$cd_fml_subs_triagem = 0;
		}

		$query = "
				update tb_acomp_fml
				set  cd_est_acomp              = :cd_est_acomp,
					 cd_avalia_triagem         = :cd_avalia_triagem,
					 dsc_mtv_cd_avalia_triagem = :dsc_mtv_cd_avalia_triagem,
					 cd_fml_subs_triagem       = :cd_fml_subs_triagem,
					 dsc_consid_finais_triagem = :dsc_consid_finais_triagem,
				     ts_est_acomp              = CURRENT_TIMESTAMP
				where cd_fmlID     = :cd_fml
				and   seql_acompID = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_est_acomp', $this->__get('cd_est_acomp'));
		$stmt->bindValue('cd_avalia_triagem', $this->__get('cd_avalia_triagem'));
		$stmt->bindValue('dsc_mtv_cd_avalia_triagem', $dsc_mtv_cd_avalia_triagem);
		$stmt->bindValue('cd_fml_subs_triagem', $cd_fml_subs_triagem);
		$stmt->bindValue('dsc_consid_finais_triagem', $this->__get('dsc_consid_finais_triagem'));
		$stmt->execute();

		return $this;
	}

// =================================================== //

	public function updateRVAtualiza() {
		
		if (null !== ($this->__get('nr_bolsa_canguru'))) {
			if (empty($this->__get('nr_bolsa_canguru'))) {
				$nr_bolsa_canguru = 0;
			} else {
				$nr_bolsa_canguru = $this->__get('nr_bolsa_canguru');
			}
		} else {
			$nr_bolsa_canguru = 0;
		}

		if (null !== ($this->__get('vlr_doado_finan_comunhao_visita'))) {
			if (empty($this->__get('vlr_doado_finan_comunhao_visita'))) {
				$vlr_doado_finan_comunhao_visita = 0;
			} else {
				$vlr_doado_finan_comunhao_visita = str_replace('.','', $this->__get('vlr_doado_finan_comunhao_visita'));
				$vlr_doado_finan_comunhao_visita = str_replace(',','.', $vlr_doado_finan_comunhao_visita);
			}
		} else {
			$vlr_doado_finan_comunhao_visita = 0;
		}

		if (null !== ($this->__get('vlr_doado_sbgrp_visita'))) {
			if (empty($this->__get('vlr_doado_sbgrp_visita'))) {
				$vlr_doado_sbgrp_visita = 0;
			} else {
				$vlr_doado_sbgrp_visita = str_replace('.','', $this->__get('vlr_doado_sbgrp_visita'));
				$vlr_doado_sbgrp_visita = str_replace(',','.', $vlr_doado_sbgrp_visita);
			}
		} else {
			$vlr_doado_sbgrp_visita = 0;
		}

		if (null !== ($this->__get('dsc_item_doado_comunhao_visita'))) {
			if (empty($this->__get('dsc_item_doado_comunhao_visita'))) {
				$dsc_item_doado_comunhao_visita = null;
			} else {
				$dsc_item_doado_comunhao_visita = $this->__get('dsc_item_doado_comunhao_visita');
			}
		} else {
			$dsc_item_doado_comunhao_visita = null;
		}

		if (null !== ($this->__get('dsc_item_doado_sbgrp_visita'))) {
			if (empty($this->__get('dsc_item_doado_sbgrp_visita'))) {
				$dsc_item_doado_sbgrp_visita = null;
			} else {
				$dsc_item_doado_sbgrp_visita = $this->__get('dsc_item_doado_sbgrp_visita');
			}
		} else {
			$dsc_item_doado_sbgrp_visita = null;
		}

		if (null !== ($this->__get('dsc_resumo_visita'))) {
			if (empty($this->__get('dsc_resumo_visita'))) {
				$dsc_resumo_visita = null;
			} else {
				$dsc_resumo_visita = $this->__get('dsc_resumo_visita');
			}
		} else {
			$dsc_resumo_visita = null;
		}

		if (null !== ($this->__get('dsc_pend_visita'))) {
			if (empty($this->__get('dsc_pend_visita'))) {
				$dsc_pend_visita = null;
			} else {
				$dsc_pend_visita = $this->__get('dsc_pend_visita');
			}
		} else {
			$dsc_pend_visita = null;
		}

		if (null !== ($this->__get('dsc_meta_alcan'))) {
			if (empty($this->__get('dsc_meta_alcan'))) {
				$dsc_meta_alcan = null;
			} else {
				$dsc_meta_alcan = $this->__get('dsc_meta_alcan');
			}
		} else {
			$dsc_meta_alcan = null;
		}

		if (null !== ($this->__get('dsc_consid_final'))) {
			if (empty($this->__get('dsc_consid_final'))) {
				$dsc_consid_final = null;
			} else {
				$dsc_consid_final = $this->__get('dsc_consid_final');
			}
		} else {
			$dsc_consid_final = null;
		}

		if (null !== ($this->__get('dsc_recado_to_coordenacao'))) {
			if (empty($this->__get('dsc_recado_to_coordenacao'))) {
				$dsc_recado_to_coordenacao = null;
			} else {
				$dsc_recado_to_coordenacao = $this->__get('dsc_recado_to_coordenacao');
			}
		} else {
			$dsc_recado_to_coordenacao = null;
		}

		if (null !== ($this->__get('dsc_recado_coordenacao_to_sbgrp'))) {
			if (empty($this->__get('dsc_recado_coordenacao_to_sbgrp'))) {
				$dsc_recado_coordenacao_to_sbgrp = null;
			} else {
				$dsc_recado_coordenacao_to_sbgrp = $this->__get('dsc_recado_coordenacao_to_sbgrp');
			}
		} else {
			$dsc_recado_coordenacao_to_sbgrp = null;
		}

		$query = "
				update tb_acomp_fml
				set	dt_acomp                        = str_to_date(:dt_acomp, '%d/%m/%Y'),
					cd_est_acomp                    = :cd_est_acomp,
					nr_bolsa_canguru                = :nr_bolsa_canguru,
					vlr_doado_finan_comunhao_visita = :vlr_doado_finan_comunhao_visita,
					vlr_doado_sbgrp_visita          = :vlr_doado_sbgrp_visita,
					dsc_item_doado_comunhao_visita  = :dsc_item_doado_comunhao_visita,
					dsc_item_doado_sbgrp_visita     = :dsc_item_doado_sbgrp_visita,
					dsc_resumo_visita               = :dsc_resumo_visita,
					dsc_meta_alcan                  = :dsc_meta_alcan,
					dsc_pend_visita                 = :dsc_pend_visita,
					dsc_recado_to_coordenacao       = :dsc_recado_to_coordenacao,
					dsc_recado_coordenacao_to_sbgrp = :dsc_recado_coordenacao_to_sbgrp,
					dsc_consid_final_visita         = :dsc_consid_final_visita,
				    ts_est_acomp                    = CURRENT_TIMESTAMP
				where cd_fmlID     = :cd_fml
				and   seql_acompID = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('cd_est_acomp', $this->__get('cd_est_acomp'));
		$stmt->bindValue('dt_acomp', $this->__get('dt_acomp'));
		$stmt->bindValue('nr_bolsa_canguru', $this->__get('nr_bolsa_canguru'));
		$stmt->bindValue('vlr_doado_finan_comunhao_visita', $vlr_doado_finan_comunhao_visita);
		$stmt->bindValue('vlr_doado_sbgrp_visita', $vlr_doado_sbgrp_visita);
		$stmt->bindValue('dsc_item_doado_comunhao_visita', $dsc_item_doado_comunhao_visita);
		$stmt->bindValue('dsc_item_doado_sbgrp_visita', $dsc_item_doado_sbgrp_visita);
		$stmt->bindValue('dsc_resumo_visita', $dsc_resumo_visita);
		$stmt->bindValue('dsc_meta_alcan', $dsc_meta_alcan);
		$stmt->bindValue('dsc_pend_visita', $dsc_pend_visita);
		$stmt->bindValue('dsc_recado_to_coordenacao', $dsc_recado_to_coordenacao);
		$stmt->bindValue('dsc_recado_coordenacao_to_sbgrp', $dsc_recado_coordenacao_to_sbgrp);
		$stmt->bindValue('dsc_consid_final_visita', $dsc_consid_final);
		$stmt->execute();

		return $this;
	}

// =================================================== //

	public function updateRDAtualiza() {
		$query = "
				update tb_acomp_fml
				set	dsc_situ_antes_depois_acomp     = :dsc_situ_antes_depois_acomp,
				    dsc_objtvo_alcan_final_acomp	= :dsc_objtvo_alcan_final_acomp,
				    dsc_acao_realzda_acomp			= :dsc_acao_realzda_acomp,
				    dsc_dificuldade_encont_acomp	= :dsc_dificuldade_encont_acomp,
				    dsc_consid_final_acomp			= :dsc_consid_final_acomp,
				    ts_est_acomp                    = CURRENT_TIMESTAMP
				where cd_fmlID     = :cd_fml
				and   seql_acompID = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue('dsc_situ_antes_depois_acomp', $this->__get('dsc_situ_antes_depois_acomp'));
		$stmt->bindValue('dsc_objtvo_alcan_final_acomp', $this->__get('dsc_objtvo_alcan_final_acomp'));
		$stmt->bindValue('dsc_acao_realzda_acomp', $this->__get('dsc_acao_realzda_acomp'));
		$stmt->bindValue('dsc_dificuldade_encont_acomp', $this->__get('dsc_dificuldade_encont_acomp'));
		$stmt->bindValue('dsc_consid_final_acomp', $this->__get('dsc_consid_final_acomp'));
		$stmt->execute();

		return $this;
	}

// ====================================================== //

	public function getSomaValoresVisita() {
		$query = "
				select sum(vlr_doado_finan_comunhao_visita) as soma_comunhao,
					   sum(vlr_doado_sbgrp_visita) as soma_subgrupo
				from  tb_acomp_fml 
				where cd_fmlID      = :cd_fml
				and   cd_atvd_acomp = :cd_atvd_acomp
				and   cd_est_acomp between :cd_est_ini and :cd_est_fim";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_atvd_acomp', $this->__get('cd_atvd_acomp'));
		$stmt->bindValue('cd_est_ini', $this->__get('cd_est_ini'));
		$stmt->bindValue('cd_est_fim', $this->__get('cd_est_fim'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getDadosConsultaAcompanhamento() {

		/*	Retirado o estado, para poder mostrar todos os relatórios, inclusive os não fomalizados ainda
		$query = "
				select seql_acompID,
					   DATE_FORMAT(dt_acomp, '%d/%m/%Y') as dt_acomp,
					   cd_atvd_acomp
				from  tb_acomp_fml 
				where cd_fmlID      = :cd_fml
				and   cd_est_acomp  = :cd_est_acomp
				order by seql_acompID";
		*/
		
		$query = "
				select seql_acompID,
					   DATE_FORMAT(dt_acomp, '%d/%m/%Y') as dt_acomp,
					   cd_atvd_acomp,
  					   case when cd_est_acomp = 1 THEN 'Pendente de Término de Impostação ou Conclusão/Revisão'
						    when cd_est_acomp = 2 THEN 'Pendente de Término de Revisão'
						    when cd_est_acomp = 3 THEN 'Revisão Concluída'
					   end	as cd_est_acomp

				from  tb_acomp_fml 
				where cd_fmlID      = :cd_fml
						order by seql_acompID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		//$stmt->bindValue('cd_est_acomp', $this->__get('cd_est_acomp'));
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getDadosConsultaAcompanhamentoEspecifico() {
		$query = "
				select *
				from  tb_acomp_fml 
				where cd_fmlID      = :cd_fml
				and   seql_acompID  = :seql_acomp";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('seql_acomp', $this->__get('seql_acomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getDadosConsulta1RelatoriosPendentes() {
		$query = "
				select  DATE_FORMAT(a.dt_acomp, '%d/%m/%Y') as dt_acomp,
						e.cd_grpID as cd_grp,
						e.nm_grp   as nm_grp, 
				        d.cd_sbgrpID as cd_sbgrp,
				        d.nm_sbgrp as nm_sbgrp, 
				        b.cd_fmlID as cd_fml,
				        b.nm_grp_fmlr as nm_grp_fmlr,         
						case when a.cd_est_acomp = 1 THEN 'Pendente de Término de Impostação de dados'
							 when a.cd_est_acomp = 2 THEN 'Pendente de Término de Revisão'
						end	as cd_est_acomp,
						case when a.cd_atvd_acomp = 1 THEN 'Relatório Triagem'
							 when a.cd_atvd_acomp = 2 THEN 'Relatório de Visita'
						end	as cd_atvd_acomp
				from	tb_acomp_fml a,
						tb_fml b,		
						tb_vncl_fml_sbgrp c,
						tb_sbgrp d,
						tb_grp e
				where	a.cd_est_acomp	in (1, 2)
				and		a.cd_fmlID		=	b.cd_fmlID
				and		a.cd_fmlID		=	c.cd_fmlID
				and     c.cd_est_vncl   =   1
				and		c.cd_grpID		=	d.cd_grpID
				and		c.cd_sbgrpID	=	d.cd_sbgrpID
				and		c.cd_grpID		=	e.cd_grpID
				order by a.dt_acomp, d.cd_grpID, c.cd_sbgrpID, b.nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getDadosConsulta2RelatoriosPendentes() {
		$query = "
				select  DATE_FORMAT(a.dt_acomp, '%d/%m/%Y') as dt_acomp,
						e.cd_grpID as cd_grp,
						e.nm_grp   as nm_grp, 
				        d.cd_sbgrpID as cd_sbgrp,
				        d.nm_sbgrp as nm_sbgrp, 
				        b.cd_fmlID as cd_fml,
				        b.nm_grp_fmlr as nm_grp_fmlr,         
						case when a.cd_est_acomp = 1 THEN 'Pendente de Término de Impostação de dados'
							 when a.cd_est_acomp = 2 THEN 'Pendente de Término de Revisão'
						end	as cd_est_acomp,
						case when a.cd_atvd_acomp = 1 THEN 'Relatório Triagem'
							 when a.cd_atvd_acomp = 2 THEN 'Relatório de Visita'
						end	as cd_atvd_acomp
				from	tb_acomp_fml a,
						tb_fml b,		
						tb_vncl_fml_sbgrp c,
						tb_sbgrp d,
						tb_grp e
				where	a.cd_est_acomp	in (1, 2)
				and		e.cd_grpID      =   :cd_grp
				and		a.cd_fmlID		=	b.cd_fmlID
				and		b.cd_fmlID		=	c.cd_fmlID
				and		c.cd_grpID		=	d.cd_grpID
				and		c.cd_sbgrpID	=	d.cd_sbgrpID
				and		c.cd_grpID		=	e.cd_grpID
				order by a.dt_acomp, d.cd_grpID, c.cd_sbgrpID, b.nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));		
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getDadosConsulta3RelatoriosPendentes() {
		$query = "
				select  DATE_FORMAT(a.dt_acomp, '%d/%m/%Y') as dt_acomp,
						e.cd_grpID as cd_grp,
						e.nm_grp   as nm_grp, 
				        d.cd_sbgrpID as cd_sbgrp,
				        d.nm_sbgrp as nm_sbgrp, 
				        b.cd_fmlID as cd_fml,
				        b.nm_grp_fmlr as nm_grp_fmlr,         
						case when a.cd_est_acomp = 1 THEN 'Pendente de Término de Impostação de dados'
							 when a.cd_est_acomp = 2 THEN 'Pendente de Término de Revisão'
						end	as cd_est_acomp,
						case when a.cd_atvd_acomp = 1 THEN 'Relatório Triagem'
							 when a.cd_atvd_acomp = 2 THEN 'Relatório de Visita'
						end	as cd_atvd_acomp
				from	tb_acomp_fml a,
						tb_fml b,		
						tb_vncl_fml_sbgrp c,
						tb_sbgrp d,
						tb_grp e
				where	a.cd_est_acomp	in (1, 2)
				and		e.cd_grpID      =   :cd_grp
				and		d.cd_sbgrpID 	=   :cd_sbgrp
				and		a.cd_fmlID		=	b.cd_fmlID
				and		b.cd_fmlID		=	c.cd_fmlID
				and		c.cd_grpID		=	d.cd_grpID
				and		c.cd_sbgrpID	=	d.cd_sbgrpID
				and		c.cd_grpID		=	e.cd_grpID
				order by a.dt_acomp, d.cd_grpID, c.cd_sbgrpID, b.nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));		
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));		
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getQtdRelatoriosPendentes() {
		$query = "
				select  count(*) as qtde
				from	tb_acomp_fml a,
						tb_fml b,		
						tb_vncl_fml_sbgrp c,
						tb_sbgrp d,
						tb_grp e
				where	a.cd_est_acomp	in (1, 2)
				and		a.cd_fmlID		=	b.cd_fmlID
				and		b.cd_fmlID		=	c.cd_fmlID
				and		c.cd_grpID		=	d.cd_grpID
				and		c.cd_sbgrpID	=	d.cd_sbgrpID
				and		c.cd_grpID		=	e.cd_grpID
				order by a.dt_acomp, d.cd_grpID, c.cd_sbgrpID, b.nm_grp_fmlr";
		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}



}	// Fim da Classe

?>