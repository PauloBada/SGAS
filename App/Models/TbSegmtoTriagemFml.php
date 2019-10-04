<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 07/08/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;

class TbSegmtoTriagemFml extends Model {
	
	// colunas da tabela
	private $cd_fmlID;
	private $seql_acompID;
	private $cd_segmto_triagemID;
	private $dt_reg_seg_triagem;
	private $cd_freq_crianca_adoles_escola;
	private $dsc_mtvo_freq_escolar;
	private $dsc_desemp_estudo;
	private $cd_interes_motiva_voltar_estudar;
	private $dsc_curso_interes_fml;
	private $dsc_religiao_fml;
	private $dsc_institu_religiosa_freqtd;
	private $dsc_freq_institu_religiosa;
	private $habito_prece_oracao;
	private $evangelho_lar;
	private $conhece_espiritismo;
	private $vont_aprox_espiritismo;
	private $dsc_casa;
	private $exist_anim_inset_insal_perig;
	private $dsc_anim_inset_insal_perig;
	private $exist_anim_estima;
	private $dsc_anim_estima;
	private $vacina_anti_rabica_anim_estima;
	private $dsc_cndc_saude_membros_fml;
	private $dsc_carteira_vacina_crianca;
	private $dsc_doenca_cronica_fml;
	private $dsc_restricao_alimentar;
	private $dsc_higiene_pessoal;
	private $cd_tip_moradia;
	private $dsc_dono_cedente_moradia;
	private $vlr_desp_agua;
	private $vlr_desp_energia;
	private $vlr_desp_iptu;
	private $vlr_desp_gas;
	private $vlr_desp_condominio;
	private $vlr_desp_outra_manut;
	private $dsc_desp_outra_manut;
	private $dsc_desp_saude_medicamento;
	private $dsc_desp_educ_creche_cuidadora;
	private $dsc_desp_transporte;
	private $dsc_desp_alimenta_especial;
	private $dsc_outra_desp_geral;
	private $cd_tip_trab;
	private $vlr_renda_tip_trab;
	private $dsc_tip_beneficio;
	private $vlr_renda_tip_beneficio;
	private $dsc_expect_fml_capacit_profi;
	private $dsc_curso_intere_profi_tecnico;
	private $dsc_projeto_gera_renda_extra;
	private $dsc_aspecto_intimo;
	private $dsc_prgm_trab;
	private $cd_agua_moradia;
	private $cd_esgoto_moradia;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //

	public function getQtdSegmentoTriagem() {
		$query = "
				select count(*) as qtde
				from tb_segmto_triagem_fml 
				where cd_fmlID            = :cd_fml
				and   seql_acompID        = :seql_acomp
				and   cd_segmto_triagemID = :cd_segmto_triagem";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('codFamilia'));
		$stmt->bindValue(':seql_acomp', $this->__get('seqlAcomp'));
		$stmt->bindValue(':cd_segmto_triagem', $this->__get('codSegmtoTriagem'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function getQtdSegmentoTriagemAll() {
		$query = "
				select count(*) as qtde
				from tb_segmto_triagem_fml 
				where cd_fmlID            = :cd_fml
				and   seql_acompID        = :seql_acomp
				and   cd_segmto_triagemID between 1 and 8";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('codFamilia'));
		$stmt->bindValue('seql_acomp', $this->__get('seqlAcomp'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function insertSegmtoTriagemFml() {

		// Para gravar nulo nos campos quando não houver informação
		if (null !== ($this->__get('cd_freq_crianca_adoles_escola'))) {
			if (empty($this->__get('cd_freq_crianca_adoles_escola'))) {
				$cd_freq_crianca_adoles_escola = 0;
			} else {
				$cd_freq_crianca_adoles_escola = $this->__get('cd_freq_crianca_adoles_escola');
			}
		} else {
			$cd_freq_crianca_adoles_escola = 0;
		}

		if (null !== ($this->__get('dsc_mtvo_freq_escolar'))) {
			if (empty($this->__get('dsc_mtvo_freq_escolar'))) {
				$dsc_mtvo_freq_escolar = null;
			} else {
				$dsc_mtvo_freq_escolar = $this->__get('dsc_mtvo_freq_escolar');
			}
		} else {
			$dsc_mtvo_freq_escolar = null;
		}

		if (null !== ($this->__get('dsc_desemp_estudo'))) {
			if (empty($this->__get('dsc_desemp_estudo'))) {
				$dsc_desemp_estudo = null;
			} else {
				$dsc_desemp_estudo = $this->__get('dsc_desemp_estudo');
			}
		} else {
			$dsc_desemp_estudo = null;
		}

		if (null !== ($this->__get('cd_interes_motiva_voltar_estudar'))) {
			if (empty($this->__get('cd_interes_motiva_voltar_estudar'))) {
				$cd_interes_motiva_voltar_estudar = 0;
			} else {
				$cd_interes_motiva_voltar_estudar = $this->__get('cd_interes_motiva_voltar_estudar');
			}
		} else {
			$cd_interes_motiva_voltar_estudar = 0;
		}

		if (null !== ($this->__get('dsc_curso_interes_fml'))) {
			if (empty($this->__get('dsc_curso_interes_fml'))) {
				$dsc_curso_interes_fml = null;
			} else {
				$dsc_curso_interes_fml = $this->__get('dsc_curso_interes_fml');
			}
		} else {
			$dsc_curso_interes_fml = null;
		}

		if (null !== ($this->__get('dsc_religiao_fml'))) {
			if (empty($this->__get('dsc_religiao_fml'))) {
				$dsc_religiao_fml = null;
			} else {
				$dsc_religiao_fml = $this->__get('dsc_religiao_fml');
			}
		} else {
			$dsc_religiao_fml = null;
		}

		if (null !== ($this->__get('dsc_institu_religiosa_freqtd'))) {
			if (empty($this->__get('dsc_institu_religiosa_freqtd'))) {
				$dsc_institu_religiosa_freqtd = null;
			} else {
				$dsc_institu_religiosa_freqtd = $this->__get('dsc_institu_religiosa_freqtd');
			}
		} else {
			$dsc_institu_religiosa_freqtd = null;
		}

		if (null !== ($this->__get('dsc_freq_institu_religiosa'))) {
			if (empty($this->__get('dsc_freq_institu_religiosa'))) {
				$dsc_freq_institu_religiosa = null;
			} else {
				$dsc_freq_institu_religiosa = $this->__get('dsc_freq_institu_religiosa');
			}
		} else {
			$dsc_freq_institu_religiosa = null;
		}

		if (null !== ($this->__get('habito_prece_oracao'))) {
			if (empty($this->__get('habito_prece_oracao'))) {
				$habito_prece_oracao = null;
			} else {
				$habito_prece_oracao = $this->__get('habito_prece_oracao');
			}
		} else {
			$habito_prece_oracao = null;
		}

		if (null !== ($this->__get('evangelho_lar'))) {
			if (empty($this->__get('evangelho_lar'))) {
				$evangelho_lar = null;
			} else {
				$evangelho_lar = $this->__get('evangelho_lar');
			}
		} else {
			$evangelho_lar = null;
		}

		if (null !== ($this->__get('conhece_espiritismo'))) {
			if (empty($this->__get('conhece_espiritismo'))) {
				$conhece_espiritismo = null;
			} else {
				$conhece_espiritismo = $this->__get('conhece_espiritismo');
			}
		} else {
			$conhece_espiritismo = null;
		}

		if (null !== ($this->__get('vont_aprox_espiritismo'))) {
			if (empty($this->__get('vont_aprox_espiritismo'))) {
				$vont_aprox_espiritismo = null;
			} else {
				$vont_aprox_espiritismo = $this->__get('vont_aprox_espiritismo');
			}
		} else {
			$vont_aprox_espiritismo = null;
		}

		if (null !== ($this->__get('dsc_casa'))) {
			if (empty($this->__get('dsc_casa'))) {
				$dsc_casa = null;
			} else {
				$dsc_casa = $this->__get('dsc_casa');
			}
		} else {
			$dsc_casa = null;
		}

		if (null !== ($this->__get('exist_anim_inset_insal_perig'))) {
			if (empty($this->__get('exist_anim_inset_insal_perig'))) {
				$exist_anim_inset_insal_perig = null;
			} else {
				$exist_anim_inset_insal_perig = $this->__get('exist_anim_inset_insal_perig');
			}
		} else {
			$exist_anim_inset_insal_perig = null;
		}

		if (null !== ($this->__get('dsc_anim_inset_insal_perig'))) {
			if (empty($this->__get('dsc_anim_inset_insal_perig'))) {
				$dsc_anim_inset_insal_perig = null;
			} else {
				$dsc_anim_inset_insal_perig = $this->__get('dsc_anim_inset_insal_perig');
			}
		} else {
			$dsc_anim_inset_insal_perig = null;
		}

		if (null !== ($this->__get('exist_anim_estima'))) {
			if (empty($this->__get('exist_anim_estima'))) {
				$exist_anim_estima = null;
			} else {
				$exist_anim_estima = $this->__get('exist_anim_estima');
			}
		} else {
			$exist_anim_estima = null;
		}

		if (null !== ($this->__get('dsc_anim_estima'))) {
			if (empty($this->__get('dsc_anim_estima'))) {
				$dsc_anim_estima = null;
			} else {
				$dsc_anim_estima = $this->__get('dsc_anim_estima');
			}
		} else {
			$dsc_anim_estima = null;
		}

		if (null !== ($this->__get('vacina_anti_rabica_anim_estima'))) {
			if (empty($this->__get('vacina_anti_rabica_anim_estima'))) {
				$vacina_anti_rabica_anim_estima = null;
			} else {
				$vacina_anti_rabica_anim_estima = $this->__get('vacina_anti_rabica_anim_estima');
			}
		} else {
			$vacina_anti_rabica_anim_estima = null;
		}

		if (null !== ($this->__get('dsc_cndc_saude_membros_fml'))) {
			if (empty($this->__get('dsc_cndc_saude_membros_fml'))) {
				$dsc_cndc_saude_membros_fml = null;
			} else {
				$dsc_cndc_saude_membros_fml = $this->__get('dsc_cndc_saude_membros_fml');
			}
		} else {
			$dsc_cndc_saude_membros_fml = null;
		}

		if (null !== ($this->__get('dsc_carteira_vacina_crianca'))) {
			if (empty($this->__get('dsc_carteira_vacina_crianca'))) {
				$dsc_carteira_vacina_crianca = null;
			} else {
				$dsc_carteira_vacina_crianca = $this->__get('dsc_carteira_vacina_crianca');
			}
		} else {
			$dsc_carteira_vacina_crianca = null;
		}

		if (null !== ($this->__get('dsc_doenca_cronica_fml'))) {
			if (empty($this->__get('dsc_doenca_cronica_fml'))) {
				$dsc_doenca_cronica_fml = null;
			} else {
				$dsc_doenca_cronica_fml = $this->__get('dsc_doenca_cronica_fml');
			}
		} else {
			$dsc_doenca_cronica_fml = null;
		}

		if (null !== ($this->__get('dsc_restricao_alimentar'))) {
			if (empty($this->__get('dsc_restricao_alimentar'))) {
				$dsc_restricao_alimentar = null;
			} else {
				$dsc_restricao_alimentar = $this->__get('dsc_restricao_alimentar');
			}
		} else {
			$dsc_restricao_alimentar = null;
		}

		if (null !== ($this->__get('dsc_higiene_pessoal'))) {
			if (empty($this->__get('dsc_higiene_pessoal'))) {
				$dsc_higiene_pessoal = null;
			} else {
				$dsc_higiene_pessoal = $this->__get('dsc_higiene_pessoal');
			}
		} else {
			$dsc_higiene_pessoal = null;
		}

		if (null !== ($this->__get('cd_tip_moradia'))) {
			if (empty($this->__get('cd_tip_moradia'))) {
				$cd_tip_moradia = null;
			} else {
				$cd_tip_moradia = $this->__get('cd_tip_moradia');
			}
		} else {
			$cd_tip_moradia = null;
		}

		if (null !== ($this->__get('dsc_dono_cedente_moradia'))) {
			if (empty($this->__get('dsc_dono_cedente_moradia'))) {
				$dsc_dono_cedente_moradia = null;
			} else {
				$dsc_dono_cedente_moradia = $this->__get('dsc_dono_cedente_moradia');
			}
		} else {
			$dsc_dono_cedente_moradia = null;
		}

		if (null !== ($this->__get('vlr_desp_agua'))) {
			if (empty($this->__get('vlr_desp_agua'))) {
				$vlr_desp_agua = 0;
			} else {
				$vlr_desp_agua = str_replace('.','', $this->__get('vlr_desp_agua'));
				$vlr_desp_agua = str_replace(',','.', $vlr_desp_agua);
			}
		} else {
			$vlr_desp_agua = 0;
		}

		if (null !== ($this->__get('vlr_desp_energia'))) {
			if (empty($this->__get('vlr_desp_energia'))) {
				$vlr_desp_energia = 0;
			} else {
				$vlr_desp_energia = str_replace('.','', $this->__get('vlr_desp_energia'));
				$vlr_desp_energia = str_replace(',','.', $vlr_desp_energia);
			}
		} else {
			$vlr_desp_energia = 0;
		}

		if (null !== ($this->__get('vlr_desp_iptu'))) {
			if (empty($this->__get('vlr_desp_iptu'))) {
				$vlr_desp_iptu = 0;
			} else {
				$vlr_desp_iptu = str_replace('.','', $this->__get('vlr_desp_iptu'));
				$vlr_desp_iptu = str_replace(',','.', $vlr_desp_iptu);
			}
		} else {
			$vlr_desp_iptu = 0;
		}

		if (null !== ($this->__get('vlr_desp_gas'))) {
			if (empty($this->__get('vlr_desp_gas'))) {
				$vlr_desp_gas = 0;
			} else {
				$vlr_desp_gas = str_replace('.','', $this->__get('vlr_desp_gas'));
				$vlr_desp_gas = str_replace(',','.', $vlr_desp_gas);
			}
		} else {
			$vlr_desp_gas = 0;
		}

		if (null !== ($this->__get('vlr_desp_condominio'))) {
			if (empty($this->__get('vlr_desp_condominio'))) {
				$vlr_desp_condominio = 0;
			} else {
				$vlr_desp_condominio = str_replace('.','', $this->__get('vlr_desp_condominio'));
				$vlr_desp_condominio = str_replace(',','.', $vlr_desp_condominio);
			}
		} else {
			$vlr_desp_condominio = 0;
		}

		if (null !== ($this->__get('vlr_desp_outra_manut'))) {
			if (empty($this->__get('vlr_desp_outra_manut'))) {
				$vlr_desp_outra_manut = 0;
			} else {
				$vlr_desp_outra_manut = str_replace('.','', $this->__get('vlr_desp_outra_manut'));
				$vlr_desp_outra_manut = str_replace(',','.', $vlr_desp_outra_manut);				
			}
		} else {
			$vlr_desp_outra_manut = 0;
		}

		if (null !== ($this->__get('dsc_desp_outra_manut'))) {
			if (empty($this->__get('dsc_desp_outra_manut'))) {
				$dsc_desp_outra_manut = null;
			} else {
				$dsc_desp_outra_manut = $this->__get('dsc_desp_outra_manut');
			}
		} else {
			$dsc_desp_outra_manut = null;
		}

		if (null !== ($this->__get('dsc_desp_saude_medicamento'))) {
			if (empty($this->__get('dsc_desp_saude_medicamento'))) {
				$dsc_desp_saude_medicamento = null;
			} else {
				$dsc_desp_saude_medicamento = $this->__get('dsc_desp_saude_medicamento');
			}
		} else {
			$dsc_desp_saude_medicamento = null;
		}

		if (null !== ($this->__get('dsc_desp_educ_creche_cuidadora'))) {
			if (empty($this->__get('dsc_desp_educ_creche_cuidadora'))) {
				$dsc_desp_educ_creche_cuidadora = null;
			} else {
				$dsc_desp_educ_creche_cuidadora = $this->__get('dsc_desp_educ_creche_cuidadora');
			}
		} else {
			$dsc_desp_educ_creche_cuidadora = null;
		}

		if (null !== ($this->__get('dsc_desp_transporte'))) {
			if (empty($this->__get('dsc_desp_transporte'))) {
				$dsc_desp_transporte = null;
			} else {
				$dsc_desp_transporte = $this->__get('dsc_desp_transporte');
			}
		} else {
			$dsc_desp_transporte = null;
		}

		if (null !== ($this->__get('dsc_desp_alimenta_especial'))) {
			if (empty($this->__get('dsc_desp_alimenta_especial'))) {
				$dsc_desp_alimenta_especial = null;
			} else {
				$dsc_desp_alimenta_especial = $this->__get('dsc_desp_alimenta_especial');
			}
		} else {
			$dsc_desp_alimenta_especial = null;
		}

		if (null !== ($this->__get('dsc_outra_desp_geral'))) {
			if (empty($this->__get('dsc_outra_desp_geral'))) {
				$dsc_outra_desp_geral = null;
			} else {
				$dsc_outra_desp_geral = $this->__get('dsc_outra_desp_geral');
			}
		} else {
			$dsc_outra_desp_geral = null;
		}

		if (null !== ($this->__get('cd_tip_trab'))) {
			if (empty($this->__get('cd_tip_trab'))) {
				$cd_tip_trab = 0;
			} else {
				$cd_tip_trab = $this->__get('cd_tip_trab');
			}
		} else {
			$cd_tip_trab = 0;
		}

		if (null !== ($this->__get('vlr_renda_tip_trab'))) {
			if (empty($this->__get('vlr_renda_tip_trab'))) {
				$vlr_renda_tip_trab = 0;
			} else {
				$vlr_renda_tip_trab = str_replace('.','', $this->__get('vlr_renda_tip_trab'));
				$vlr_renda_tip_trab = str_replace(',','.', $vlr_renda_tip_trab);
			}
		} else {
			$vlr_renda_tip_trab = 0;
		}

		if (null !== ($this->__get('dsc_tip_beneficio'))) {
			if (empty($this->__get('dsc_tip_beneficio'))) {
				$dsc_tip_beneficio = null;
			} else {
				$dsc_tip_beneficio = $this->__get('dsc_tip_beneficio');
			}
		} else {
			$dsc_tip_beneficio = null;
		}

		if (null !== ($this->__get('vlr_renda_tip_beneficio'))) {
			if (empty($this->__get('vlr_renda_tip_beneficio'))) {
				$vlr_renda_tip_beneficio = 0;
			} else {
				$vlr_renda_tip_beneficio = str_replace('.','', $this->__get('vlr_renda_tip_beneficio'));
				$vlr_renda_tip_beneficio = str_replace(',','.', $vlr_renda_tip_beneficio);
			}
		} else {
			$vlr_renda_tip_beneficio = 0;
		}

		if (null !== ($this->__get('dsc_expect_fml_capacit_profi'))) {
			if (empty($this->__get('dsc_expect_fml_capacit_profi'))) {
				$dsc_expect_fml_capacit_profi = null;
			} else {
				$dsc_expect_fml_capacit_profi = $this->__get('dsc_expect_fml_capacit_profi');
			}
		} else {
			$dsc_expect_fml_capacit_profi = null;
		}

		if (null !== ($this->__get('dsc_curso_intere_profi_tecnico'))) {
			if (empty($this->__get('dsc_curso_intere_profi_tecnico'))) {
				$dsc_curso_intere_profi_tecnico = null;
			} else {
				$dsc_curso_intere_profi_tecnico = $this->__get('dsc_curso_intere_profi_tecnico');
			}
		} else {
			$dsc_curso_intere_profi_tecnico = null;
		}

		if (null !== ($this->__get('dsc_projeto_gera_renda_extra'))) {
			if (empty($this->__get('dsc_projeto_gera_renda_extra'))) {
				$dsc_projeto_gera_renda_extra = null;
			} else {
				$dsc_projeto_gera_renda_extra = $this->__get('dsc_projeto_gera_renda_extra');
			}
		} else {
			$dsc_projeto_gera_renda_extra = null;
		}

		if (null !== ($this->__get('dsc_aspecto_intimo'))) {
			if (empty($this->__get('dsc_aspecto_intimo'))) {
				$dsc_aspecto_intimo = null;
			} else {
				$dsc_aspecto_intimo = $this->__get('dsc_aspecto_intimo');
			}
		} else {
			$dsc_aspecto_intimo = null;
		}

		if (null !== ($this->__get('cd_agua_moradia'))) {
			if (empty($this->__get('cd_agua_moradia'))) {
				$cd_agua_moradia = 0;
			} else {
				$cd_agua_moradia = $this->__get('cd_agua_moradia');
			}
		} else {
			$cd_agua_moradia = 0;
		}

		if (null !== ($this->__get('cd_esgoto_moradia'))) {
			if (empty($this->__get('cd_esgoto_moradia'))) {
				$cd_esgoto_moradia = 0;
			} else {
				$cd_esgoto_moradia = $this->__get('cd_esgoto_moradia');
			}
		} else {
			$cd_esgoto_moradia = 0;
		}

		$query = "
				insert into tb_segmto_triagem_fml
				(cd_fmlID,
				seql_acompID,
				cd_segmto_triagemID,
				dt_reg_seg_triagem,
				cd_freq_crianca_adoles_escola,
				dsc_mtvo_freq_escolar,
				dsc_desemp_estudo,
				cd_interes_motiva_voltar_estudar,
				dsc_curso_interes_fml,
				dsc_religiao_fml,
				dsc_institu_religiosa_freqtd,
				dsc_freq_institu_religiosa,
				habito_prece_oracao,
				evangelho_lar,
				conhece_espiritismo,
				vont_aprox_espiritismo,
				dsc_casa,
				exist_anim_inset_insal_perig,
				dsc_anim_inset_insal_perig,
				exist_anim_estima,
				dsc_anim_estima,
				vacina_anti_rabica_anim_estima,
				dsc_cndc_saude_membros_fml,
				dsc_carteira_vacina_crianca,
				dsc_doenca_cronica_fml,
				dsc_restricao_alimentar,
				dsc_higiene_pessoal,
				cd_tip_moradia,
				dsc_dono_cedente_moradia,
				vlr_desp_agua,
				vlr_desp_energia,
				vlr_desp_iptu,
				vlr_desp_gas,
				vlr_desp_condominio,
				vlr_desp_outra_manut,
				dsc_desp_outra_manut,
				dsc_desp_saude_medicamento,
				dsc_desp_educ_creche_cuidadora,
				dsc_desp_transporte,
				dsc_desp_alimenta_especial,
				dsc_outra_desp_geral,
				cd_tip_trab,
				vlr_renda_tip_trab,
				dsc_tip_beneficio,
				vlr_renda_tip_beneficio,
				dsc_expect_fml_capacit_profi,
				dsc_curso_intere_profi_tecnico,
				dsc_projeto_gera_renda_extra,
				dsc_aspecto_intimo,
				dsc_prgm_trab,
				cd_agua_moradia,
				cd_esgoto_moradia)

				values 

				(:cd_fml,
				:seql_acomp,
				:cd_segmto_triagem,
				str_to_date(:dt_reg_seg_triagem, '%d/%m/%Y'),   
				:cd_freq_crianca_adoles_escola,
				:dsc_mtvo_freq_escolar,
				:dsc_desemp_estudo,
				:cd_interes_motiva_voltar_estudar,
				:dsc_curso_interes_fml,
				:dsc_religiao_fml,
				:dsc_institu_religiosa_freqtd,
				:dsc_freq_institu_religiosa,
				:habito_prece_oracao,
				:evangelho_lar,
				:conhece_espiritismo,
				:vont_aprox_espiritismo,
				:dsc_casa,
				:exist_anim_inset_insal_perig,
				:dsc_anim_inset_insal_perig,
				:exist_anim_estima,
				:dsc_anim_estima,
				:vacina_anti_rabica_anim_estima,
				:dsc_cndc_saude_membros_fml,
				:dsc_carteira_vacina_crianca,
				:dsc_doenca_cronica_fml,
				:dsc_restricao_alimentar,
				:dsc_higiene_pessoal,
				:cd_tip_moradia,
				:dsc_dono_cedente_moradia,
				:vlr_desp_agua,
				:vlr_desp_energia,
				:vlr_desp_iptu,
				:vlr_desp_gas,
				:vlr_desp_condominio,
				:vlr_desp_outra_manut,
				:dsc_desp_outra_manut,
				:dsc_desp_saude_medicamento,
				:dsc_desp_educ_creche_cuidadora,
				:dsc_desp_transporte,
				:dsc_desp_alimenta_especial,
				:dsc_outra_desp_geral,
				:cd_tip_trab,
				:vlr_renda_tip_trab,
				:dsc_tip_beneficio,
				:vlr_renda_tip_beneficio,
				:dsc_expect_fml_capacit_profi,
				:dsc_curso_intere_profi_tecnico,
				:dsc_projeto_gera_renda_extra,
				:dsc_aspecto_intimo,
				:dsc_prgm_trab,
				:cd_agua_moradia,
				:cd_esgoto_moradia)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':seql_acomp', $this->__get('seql_acomp'));			
		$stmt->bindValue(':cd_segmto_triagem', $this->__get('cd_segmto_triagem'));			
		$stmt->bindValue(':dt_reg_seg_triagem', $this->__get('dt_reg_seg_triagem'));
		$stmt->bindValue(':cd_freq_crianca_adoles_escola', $cd_freq_crianca_adoles_escola);
		$stmt->bindValue(':dsc_mtvo_freq_escolar', $dsc_mtvo_freq_escolar);
		$stmt->bindValue(':dsc_desemp_estudo', $dsc_desemp_estudo);
		$stmt->bindValue(':cd_interes_motiva_voltar_estudar', $cd_interes_motiva_voltar_estudar);
		$stmt->bindValue(':dsc_curso_interes_fml', $dsc_curso_interes_fml);
		$stmt->bindValue(':dsc_religiao_fml', $dsc_religiao_fml);
		$stmt->bindValue(':dsc_institu_religiosa_freqtd', $dsc_institu_religiosa_freqtd);
		$stmt->bindValue(':dsc_freq_institu_religiosa', $dsc_freq_institu_religiosa);
		$stmt->bindValue(':habito_prece_oracao', $habito_prece_oracao);
		$stmt->bindValue(':evangelho_lar', $evangelho_lar);
		$stmt->bindValue(':conhece_espiritismo', $conhece_espiritismo);
		$stmt->bindValue(':vont_aprox_espiritismo', $vont_aprox_espiritismo);
		$stmt->bindValue(':dsc_casa', $dsc_casa);
		$stmt->bindValue(':exist_anim_inset_insal_perig', $exist_anim_inset_insal_perig);
		$stmt->bindValue(':dsc_anim_inset_insal_perig', $dsc_anim_inset_insal_perig);
		$stmt->bindValue(':exist_anim_estima', $exist_anim_estima);
		$stmt->bindValue(':dsc_anim_estima', $dsc_anim_estima);
		$stmt->bindValue(':vacina_anti_rabica_anim_estima', $vacina_anti_rabica_anim_estima);
		$stmt->bindValue(':dsc_cndc_saude_membros_fml', $dsc_cndc_saude_membros_fml);
		$stmt->bindValue(':dsc_carteira_vacina_crianca', $dsc_carteira_vacina_crianca);
		$stmt->bindValue(':dsc_doenca_cronica_fml', $dsc_doenca_cronica_fml);
		$stmt->bindValue(':dsc_restricao_alimentar', $dsc_restricao_alimentar);
		$stmt->bindValue(':dsc_higiene_pessoal', $dsc_higiene_pessoal);
		$stmt->bindValue(':cd_tip_moradia', $cd_tip_moradia);
		$stmt->bindValue(':dsc_dono_cedente_moradia', $dsc_dono_cedente_moradia);
		$stmt->bindValue(':vlr_desp_agua', $vlr_desp_agua);
		$stmt->bindValue(':vlr_desp_energia', $vlr_desp_energia);
		$stmt->bindValue(':vlr_desp_iptu', $vlr_desp_iptu);
		$stmt->bindValue(':vlr_desp_gas', $vlr_desp_gas);
		$stmt->bindValue(':vlr_desp_condominio', $vlr_desp_condominio);
		$stmt->bindValue(':vlr_desp_outra_manut', $vlr_desp_outra_manut);
		$stmt->bindValue(':dsc_desp_outra_manut', $dsc_desp_outra_manut);
		$stmt->bindValue(':dsc_desp_saude_medicamento', $dsc_desp_saude_medicamento);
		$stmt->bindValue(':dsc_desp_educ_creche_cuidadora', $dsc_desp_educ_creche_cuidadora);
		$stmt->bindValue(':dsc_desp_transporte', $dsc_desp_transporte);
		$stmt->bindValue(':dsc_desp_alimenta_especial', $dsc_desp_alimenta_especial);
		$stmt->bindValue(':dsc_outra_desp_geral', $dsc_outra_desp_geral);
		$stmt->bindValue(':cd_tip_trab', $cd_tip_trab);
		$stmt->bindValue(':vlr_renda_tip_trab', $vlr_renda_tip_trab);
		$stmt->bindValue(':dsc_tip_beneficio', $dsc_tip_beneficio);
		$stmt->bindValue(':vlr_renda_tip_beneficio', $vlr_renda_tip_beneficio);
		$stmt->bindValue(':dsc_expect_fml_capacit_profi', $dsc_expect_fml_capacit_profi);
		$stmt->bindValue(':dsc_curso_intere_profi_tecnico', $dsc_curso_intere_profi_tecnico);
		$stmt->bindValue(':dsc_projeto_gera_renda_extra', $dsc_projeto_gera_renda_extra);
		$stmt->bindValue(':dsc_aspecto_intimo', $dsc_aspecto_intimo);
		$stmt->bindValue(':dsc_prgm_trab', $this->__get('dsc_prgm_trab'));
		$stmt->bindValue(':cd_agua_moradia', $cd_agua_moradia);
		$stmt->bindValue(':cd_esgoto_moradia', $cd_esgoto_moradia);
		$stmt->execute();

		return $this;

	}	// Fim function insertSegmtoTriagemFml

// ====================================================== //

	public function getDadosSegmentoTriagem() {
		$query = "
				select *
				from  tb_segmto_triagem_fml 
				where cd_fmlID            = :cd_fml
				and   seql_acompID        = :seql_acomp
				and   cd_segmto_triagemID = :cd_segmto_triagem";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':seql_acomp', $this->__get('seql_acomp'));
		$stmt->bindValue(':cd_segmto_triagem', $this->__get('cd_segmto_triagem'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function updateSegmtoTriagemFml() {

		// Para gravar nulo nos campos quando não houver informação
		if (null !== ($this->__get('cd_freq_crianca_adoles_escola'))) {
			if (empty($this->__get('cd_freq_crianca_adoles_escola'))) {
				$cd_freq_crianca_adoles_escola = 0;
			} else {
				$cd_freq_crianca_adoles_escola = $this->__get('cd_freq_crianca_adoles_escola');
			}
		} else {
			$cd_freq_crianca_adoles_escola = 0;
		}

		if (null !== ($this->__get('dsc_mtvo_freq_escolar'))) {
			if (empty($this->__get('dsc_mtvo_freq_escolar'))) {
				$dsc_mtvo_freq_escolar = null;
			} else {
				$dsc_mtvo_freq_escolar = $this->__get('dsc_mtvo_freq_escolar');
			}
		} else {
			$dsc_mtvo_freq_escolar = null;
		}

		if (null !== ($this->__get('dsc_desemp_estudo'))) {
			if (empty($this->__get('dsc_desemp_estudo'))) {
				$dsc_desemp_estudo = null;
			} else {
				$dsc_desemp_estudo = $this->__get('dsc_desemp_estudo');
			}
		} else {
			$dsc_desemp_estudo = null;
		}

		if (null !== ($this->__get('cd_interes_motiva_voltar_estudar'))) {
			if (empty($this->__get('cd_interes_motiva_voltar_estudar'))) {
				$cd_interes_motiva_voltar_estudar = 0;
			} else {
				$cd_interes_motiva_voltar_estudar = $this->__get('cd_interes_motiva_voltar_estudar');
			}
		} else {
			$cd_interes_motiva_voltar_estudar = 0;
		}

		if (null !== ($this->__get('dsc_curso_interes_fml'))) {
			if (empty($this->__get('dsc_curso_interes_fml'))) {
				$dsc_curso_interes_fml = null;
			} else {
				$dsc_curso_interes_fml = $this->__get('dsc_curso_interes_fml');
			}
		} else {
			$dsc_curso_interes_fml = null;
		}

		if (null !== ($this->__get('dsc_religiao_fml'))) {
			if (empty($this->__get('dsc_religiao_fml'))) {
				$dsc_religiao_fml = null;
			} else {
				$dsc_religiao_fml = $this->__get('dsc_religiao_fml');
			}
		} else {
			$dsc_religiao_fml = null;
		}

		if (null !== ($this->__get('dsc_institu_religiosa_freqtd'))) {
			if (empty($this->__get('dsc_institu_religiosa_freqtd'))) {
				$dsc_institu_religiosa_freqtd = null;
			} else {
				$dsc_institu_religiosa_freqtd = $this->__get('dsc_institu_religiosa_freqtd');
			}
		} else {
			$dsc_institu_religiosa_freqtd = null;
		}

		if (null !== ($this->__get('dsc_freq_institu_religiosa'))) {
			if (empty($this->__get('dsc_freq_institu_religiosa'))) {
				$dsc_freq_institu_religiosa = null;
			} else {
				$dsc_freq_institu_religiosa = $this->__get('dsc_freq_institu_religiosa');
			}
		} else {
			$dsc_freq_institu_religiosa = null;
		}

		if (null !== ($this->__get('habito_prece_oracao'))) {
			if (empty($this->__get('habito_prece_oracao'))) {
				$habito_prece_oracao = null;
			} else {
				$habito_prece_oracao = $this->__get('habito_prece_oracao');
			}
		} else {
			$habito_prece_oracao = null;
		}

		if (null !== ($this->__get('evangelho_lar'))) {
			if (empty($this->__get('evangelho_lar'))) {
				$evangelho_lar = null;
			} else {
				$evangelho_lar = $this->__get('evangelho_lar');
			}
		} else {
			$evangelho_lar = null;
		}

		if (null !== ($this->__get('conhece_espiritismo'))) {
			if (empty($this->__get('conhece_espiritismo'))) {
				$conhece_espiritismo = null;
			} else {
				$conhece_espiritismo = $this->__get('conhece_espiritismo');
			}
		} else {
			$conhece_espiritismo = null;
		}

		if (null !== ($this->__get('vont_aprox_espiritismo'))) {
			if (empty($this->__get('vont_aprox_espiritismo'))) {
				$vont_aprox_espiritismo = null;
			} else {
				$vont_aprox_espiritismo = $this->__get('vont_aprox_espiritismo');
			}
		} else {
			$vont_aprox_espiritismo = null;
		}

		if (null !== ($this->__get('dsc_casa'))) {
			if (empty($this->__get('dsc_casa'))) {
				$dsc_casa = null;
			} else {
				$dsc_casa = $this->__get('dsc_casa');
			}
		} else {
			$dsc_casa = null;
		}

		if (null !== ($this->__get('exist_anim_inset_insal_perig'))) {
			if (empty($this->__get('exist_anim_inset_insal_perig'))) {
				$exist_anim_inset_insal_perig = null;
			} else {
				$exist_anim_inset_insal_perig = $this->__get('exist_anim_inset_insal_perig');
			}
		} else {
			$exist_anim_inset_insal_perig = null;
		}

		if (null !== ($this->__get('dsc_anim_inset_insal_perig'))) {
			if (empty($this->__get('dsc_anim_inset_insal_perig'))) {
				$dsc_anim_inset_insal_perig = null;
			} else {
				$dsc_anim_inset_insal_perig = $this->__get('dsc_anim_inset_insal_perig');
			}
		} else {
			$dsc_anim_inset_insal_perig = null;
		}

		if (null !== ($this->__get('exist_anim_estima'))) {
			if (empty($this->__get('exist_anim_estima'))) {
				$exist_anim_estima = null;
			} else {
				$exist_anim_estima = $this->__get('exist_anim_estima');
			}
		} else {
			$exist_anim_estima = null;
		}

		if (null !== ($this->__get('dsc_anim_estima'))) {
			if (empty($this->__get('dsc_anim_estima'))) {
				$dsc_anim_estima = null;
			} else {
				$dsc_anim_estima = $this->__get('dsc_anim_estima');
			}
		} else {
			$dsc_anim_estima = null;
		}

		if (null !== ($this->__get('vacina_anti_rabica_anim_estima'))) {
			if (empty($this->__get('vacina_anti_rabica_anim_estima'))) {
				$vacina_anti_rabica_anim_estima = null;
			} else {
				$vacina_anti_rabica_anim_estima = $this->__get('vacina_anti_rabica_anim_estima');
			}
		} else {
			$vacina_anti_rabica_anim_estima = null;
		}

		if (null !== ($this->__get('dsc_cndc_saude_membros_fml'))) {
			if (empty($this->__get('dsc_cndc_saude_membros_fml'))) {
				$dsc_cndc_saude_membros_fml = null;
			} else {
				$dsc_cndc_saude_membros_fml = $this->__get('dsc_cndc_saude_membros_fml');
			}
		} else {
			$dsc_cndc_saude_membros_fml = null;
		}

		if (null !== ($this->__get('dsc_carteira_vacina_crianca'))) {
			if (empty($this->__get('dsc_carteira_vacina_crianca'))) {
				$dsc_carteira_vacina_crianca = null;
			} else {
				$dsc_carteira_vacina_crianca = $this->__get('dsc_carteira_vacina_crianca');
			}
		} else {
			$dsc_carteira_vacina_crianca = null;
		}

		if (null !== ($this->__get('dsc_doenca_cronica_fml'))) {
			if (empty($this->__get('dsc_doenca_cronica_fml'))) {
				$dsc_doenca_cronica_fml = null;
			} else {
				$dsc_doenca_cronica_fml = $this->__get('dsc_doenca_cronica_fml');
			}
		} else {
			$dsc_doenca_cronica_fml = null;
		}

		if (null !== ($this->__get('dsc_restricao_alimentar'))) {
			if (empty($this->__get('dsc_restricao_alimentar'))) {
				$dsc_restricao_alimentar = null;
			} else {
				$dsc_restricao_alimentar = $this->__get('dsc_restricao_alimentar');
			}
		} else {
			$dsc_restricao_alimentar = null;
		}

		if (null !== ($this->__get('dsc_higiene_pessoal'))) {
			if (empty($this->__get('dsc_higiene_pessoal'))) {
				$dsc_higiene_pessoal = null;
			} else {
				$dsc_higiene_pessoal = $this->__get('dsc_higiene_pessoal');
			}
		} else {
			$dsc_higiene_pessoal = null;
		}

		if (null !== ($this->__get('cd_tip_moradia'))) {
			if (empty($this->__get('cd_tip_moradia'))) {
				$cd_tip_moradia = null;
			} else {
				$cd_tip_moradia = $this->__get('cd_tip_moradia');
			}
		} else {
			$cd_tip_moradia = null;
		}

		if (null !== ($this->__get('dsc_dono_cedente_moradia'))) {
			if (empty($this->__get('dsc_dono_cedente_moradia'))) {
				$dsc_dono_cedente_moradia = null;
			} else {
				$dsc_dono_cedente_moradia = $this->__get('dsc_dono_cedente_moradia');
			}
		} else {
			$dsc_dono_cedente_moradia = null;
		}

		if (null !== ($this->__get('vlr_desp_agua'))) {
			if (empty($this->__get('vlr_desp_agua'))) {
				$vlr_desp_agua = 0;
			} else {
				$vlr_desp_agua = str_replace('.','', $this->__get('vlr_desp_agua'));
				$vlr_desp_agua = str_replace(',','.', $vlr_desp_agua);
			}
		} else {
			$vlr_desp_agua = 0;
		}

		if (null !== ($this->__get('vlr_desp_energia'))) {
			if (empty($this->__get('vlr_desp_energia'))) {
				$vlr_desp_energia = 0;
			} else {
				$vlr_desp_energia = str_replace('.','', $this->__get('vlr_desp_energia'));
				$vlr_desp_energia = str_replace(',','.', $vlr_desp_energia);
			}
		} else {
			$vlr_desp_energia = 0;
		}

		if (null !== ($this->__get('vlr_desp_iptu'))) {
			if (empty($this->__get('vlr_desp_iptu'))) {
				$vlr_desp_iptu = 0;
			} else {
				$vlr_desp_iptu = str_replace('.','', $this->__get('vlr_desp_iptu'));
				$vlr_desp_iptu = str_replace(',','.', $vlr_desp_iptu);
			}
		} else {
			$vlr_desp_iptu = 0;
		}

		if (null !== ($this->__get('vlr_desp_gas'))) {
			if (empty($this->__get('vlr_desp_gas'))) {
				$vlr_desp_gas = 0;
			} else {
				$vlr_desp_gas = str_replace('.','', $this->__get('vlr_desp_gas'));
				$vlr_desp_gas = str_replace(',','.', $vlr_desp_gas);
			}
		} else {
			$vlr_desp_gas = 0;
		}

		if (null !== ($this->__get('vlr_desp_condominio'))) {
			if (empty($this->__get('vlr_desp_condominio'))) {
				$vlr_desp_condominio = 0;
			} else {
				$vlr_desp_condominio = str_replace('.','', $this->__get('vlr_desp_condominio'));
				$vlr_desp_condominio = str_replace(',','.', $vlr_desp_condominio);				
			}
		} else {
			$vlr_desp_condominio = 0;
		}

		if (null !== ($this->__get('vlr_desp_outra_manut'))) {
			if (empty($this->__get('vlr_desp_outra_manut'))) {
				$vlr_desp_outra_manut = 0;
			} else {
				$vlr_desp_outra_manut = str_replace('.','', $this->__get('vlr_desp_outra_manut'));
				$vlr_desp_outra_manut = str_replace(',','.', $vlr_desp_outra_manut);			
			}
		} else {
			$vlr_desp_outra_manut = 0;
		}

		if (null !== ($this->__get('dsc_desp_outra_manut'))) {
			if (empty($this->__get('dsc_desp_outra_manut'))) {
				$dsc_desp_outra_manut = null;
			} else {
				$dsc_desp_outra_manut = $this->__get('dsc_desp_outra_manut');
			}
		} else {
			$dsc_desp_outra_manut = null;
		}

		if (null !== ($this->__get('dsc_desp_saude_medicamento'))) {
			if (empty($this->__get('dsc_desp_saude_medicamento'))) {
				$dsc_desp_saude_medicamento = null;
			} else {
				$dsc_desp_saude_medicamento = $this->__get('dsc_desp_saude_medicamento');
			}
		} else {
			$dsc_desp_saude_medicamento = null;
		}

		if (null !== ($this->__get('dsc_desp_educ_creche_cuidadora'))) {
			if (empty($this->__get('dsc_desp_educ_creche_cuidadora'))) {
				$dsc_desp_educ_creche_cuidadora = null;
			} else {
				$dsc_desp_educ_creche_cuidadora = $this->__get('dsc_desp_educ_creche_cuidadora');
			}
		} else {
			$dsc_desp_educ_creche_cuidadora = null;
		}

		if (null !== ($this->__get('dsc_desp_transporte'))) {
			if (empty($this->__get('dsc_desp_transporte'))) {
				$dsc_desp_transporte = null;
			} else {
				$dsc_desp_transporte = $this->__get('dsc_desp_transporte');
			}
		} else {
			$dsc_desp_transporte = null;
		}

		if (null !== ($this->__get('dsc_desp_alimenta_especial'))) {
			if (empty($this->__get('dsc_desp_alimenta_especial'))) {
				$dsc_desp_alimenta_especial = null;
			} else {
				$dsc_desp_alimenta_especial = $this->__get('dsc_desp_alimenta_especial');
			}
		} else {
			$dsc_desp_alimenta_especial = null;
		}

		if (null !== ($this->__get('dsc_outra_desp_geral'))) {
			if (empty($this->__get('dsc_outra_desp_geral'))) {
				$dsc_outra_desp_geral = null;
			} else {
				$dsc_outra_desp_geral = $this->__get('dsc_outra_desp_geral');
			}
		} else {
			$dsc_outra_desp_geral = null;
		}

		if (null !== ($this->__get('cd_tip_trab'))) {
			if (empty($this->__get('cd_tip_trab'))) {
				$cd_tip_trab = 0;
			} else {
				$cd_tip_trab = $this->__get('cd_tip_trab');
			}
		} else {
			$cd_tip_trab = 0;
		}

		if (null !== ($this->__get('vlr_renda_tip_trab'))) {
			if (empty($this->__get('vlr_renda_tip_trab'))) {
				$vlr_renda_tip_trab = 0;
			} else {
				$vlr_renda_tip_trab = str_replace('.','', $this->__get('vlr_renda_tip_trab'));
				$vlr_renda_tip_trab = str_replace(',','.', $vlr_renda_tip_trab);				
			}
		} else {
			$vlr_renda_tip_trab = 0;
		}

		if (null !== ($this->__get('dsc_tip_beneficio'))) {
			if (empty($this->__get('dsc_tip_beneficio'))) {
				$dsc_tip_beneficio = null;
			} else {
				$dsc_tip_beneficio = $this->__get('dsc_tip_beneficio');
			}
		} else {
			$dsc_tip_beneficio = null;
		}

		if (null !== ($this->__get('vlr_renda_tip_beneficio'))) {
			if (empty($this->__get('vlr_renda_tip_beneficio'))) {
				$vlr_renda_tip_beneficio = 0;
			} else {
				$vlr_renda_tip_beneficio = str_replace('.','', $this->__get('vlr_renda_tip_beneficio'));
				$vlr_renda_tip_beneficio = str_replace(',','.', $vlr_renda_tip_beneficio);				
			}
		} else {
			$vlr_renda_tip_beneficio = 0;
		}

		if (null !== ($this->__get('dsc_expect_fml_capacit_profi'))) {
			if (empty($this->__get('dsc_expect_fml_capacit_profi'))) {
				$dsc_expect_fml_capacit_profi = null;
			} else {
				$dsc_expect_fml_capacit_profi = $this->__get('dsc_expect_fml_capacit_profi');
			}
		} else {
			$dsc_expect_fml_capacit_profi = null;
		}

		if (null !== ($this->__get('dsc_curso_intere_profi_tecnico'))) {
			if (empty($this->__get('dsc_curso_intere_profi_tecnico'))) {
				$dsc_curso_intere_profi_tecnico = null;
			} else {
				$dsc_curso_intere_profi_tecnico = $this->__get('dsc_curso_intere_profi_tecnico');
			}
		} else {
			$dsc_curso_intere_profi_tecnico = null;
		}

		if (null !== ($this->__get('dsc_projeto_gera_renda_extra'))) {
			if (empty($this->__get('dsc_projeto_gera_renda_extra'))) {
				$dsc_projeto_gera_renda_extra = null;
			} else {
				$dsc_projeto_gera_renda_extra = $this->__get('dsc_projeto_gera_renda_extra');
			}
		} else {
			$dsc_projeto_gera_renda_extra = null;
		}

		if (null !== ($this->__get('dsc_aspecto_intimo'))) {
			if (empty($this->__get('dsc_aspecto_intimo'))) {
				$dsc_aspecto_intimo = null;
			} else {
				$dsc_aspecto_intimo = $this->__get('dsc_aspecto_intimo');
			}
		} else {
			$dsc_aspecto_intimo = null;
		}

		if (null !== ($this->__get('cd_agua_moradia'))) {
			if (empty($this->__get('cd_agua_moradia'))) {
				$cd_agua_moradia = 0;
			} else {
				$cd_agua_moradia = $this->__get('cd_agua_moradia');
			}
		} else {
			$cd_agua_moradia = 0;
		}

		if (null !== ($this->__get('cd_esgoto_moradia'))) {
			if (empty($this->__get('cd_esgoto_moradia'))) {
				$cd_esgoto_moradia = 0;
			} else {
				$cd_esgoto_moradia = $this->__get('cd_esgoto_moradia');
			}
		} else {
			$cd_esgoto_moradia = 0;
		}

		$query = "
				update tb_segmto_triagem_fml
				set dt_reg_seg_triagem 					= str_to_date(:dt_reg_seg_triagem, '%d/%m/%Y'),   
				    cd_freq_crianca_adoles_escola 		= :cd_freq_crianca_adoles_escola,
				    dsc_mtvo_freq_escolar 				= :dsc_mtvo_freq_escolar,
				    dsc_desemp_estudo 					= :dsc_desemp_estudo,
				    cd_interes_motiva_voltar_estudar 	= :cd_interes_motiva_voltar_estudar,
				    dsc_curso_interes_fml 				= :dsc_curso_interes_fml,
				    dsc_religiao_fml 					= :dsc_religiao_fml,
				    dsc_institu_religiosa_freqtd 		= :dsc_institu_religiosa_freqtd,
				    dsc_freq_institu_religiosa 			= :dsc_freq_institu_religiosa,
				    habito_prece_oracao 				= :habito_prece_oracao,
				    evangelho_lar 						= :evangelho_lar,
				    conhece_espiritismo 				= :conhece_espiritismo,
				    vont_aprox_espiritismo 				= :vont_aprox_espiritismo,
				    dsc_casa 							= :dsc_casa,
				    exist_anim_inset_insal_perig 		= :exist_anim_inset_insal_perig,
				    dsc_anim_inset_insal_perig 			= :dsc_anim_inset_insal_perig,
					exist_anim_estima 					= :exist_anim_estima,
					dsc_anim_estima 					= :dsc_anim_estima,
					vacina_anti_rabica_anim_estima 		= :vacina_anti_rabica_anim_estima,
					dsc_cndc_saude_membros_fml 			= :dsc_cndc_saude_membros_fml,
					dsc_carteira_vacina_crianca 		= :dsc_carteira_vacina_crianca,
					dsc_doenca_cronica_fml 				= :dsc_doenca_cronica_fml,
					dsc_restricao_alimentar 			= :dsc_restricao_alimentar,
					dsc_higiene_pessoal 				= :dsc_higiene_pessoal,
					cd_tip_moradia 						= :cd_tip_moradia,
					dsc_dono_cedente_moradia 			= :dsc_dono_cedente_moradia,
					vlr_desp_agua 						= :vlr_desp_agua,
					vlr_desp_energia 					= :vlr_desp_energia,
					vlr_desp_iptu 						= :vlr_desp_iptu,
					vlr_desp_gas 						= :vlr_desp_gas,
					vlr_desp_condominio 				= :vlr_desp_condominio,
					vlr_desp_outra_manut 				= :vlr_desp_outra_manut,
					dsc_desp_outra_manut 				= :dsc_desp_outra_manut,
					dsc_desp_saude_medicamento 			= :dsc_desp_saude_medicamento,
					dsc_desp_educ_creche_cuidadora 		= :dsc_desp_educ_creche_cuidadora,
					dsc_desp_transporte 				= :dsc_desp_transporte,
					dsc_desp_alimenta_especial 			= :dsc_desp_alimenta_especial,
					dsc_outra_desp_geral 				= :dsc_outra_desp_geral,
					cd_tip_trab 						= :cd_tip_trab,
					vlr_renda_tip_trab 					= :vlr_renda_tip_trab,
					dsc_tip_beneficio 					= :dsc_tip_beneficio,
					vlr_renda_tip_beneficio 			= :vlr_renda_tip_beneficio,
					dsc_expect_fml_capacit_profi 		= :dsc_expect_fml_capacit_profi,
					dsc_curso_intere_profi_tecnico 		= :dsc_curso_intere_profi_tecnico,
					dsc_projeto_gera_renda_extra 		= :dsc_projeto_gera_renda_extra,
					dsc_aspecto_intimo 					= :dsc_aspecto_intimo,
					dsc_prgm_trab 						= :dsc_prgm_trab,
					cd_agua_moradia 					= :cd_agua_moradia,
					cd_esgoto_moradia 					= :cd_esgoto_moradia
				where cd_fmlID     			= :cd_fml
				and   seql_acompID 			= :seql_acomp
				and   cd_segmto_triagemID 	= :cd_segmto_triagem";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue(':cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue(':seql_acomp', $this->__get('seql_acomp'));			
		$stmt->bindValue(':cd_segmto_triagem', $this->__get('cd_segmto_triagem'));			
		$stmt->bindValue(':dt_reg_seg_triagem', $this->__get('dt_reg_seg_triagem'));
		$stmt->bindValue(':cd_freq_crianca_adoles_escola', $cd_freq_crianca_adoles_escola);
		$stmt->bindValue(':dsc_mtvo_freq_escolar', $dsc_mtvo_freq_escolar);
		$stmt->bindValue(':dsc_desemp_estudo', $dsc_desemp_estudo);
		$stmt->bindValue(':cd_interes_motiva_voltar_estudar', $cd_interes_motiva_voltar_estudar);
		$stmt->bindValue(':dsc_curso_interes_fml', $dsc_curso_interes_fml);
		$stmt->bindValue(':dsc_religiao_fml', $dsc_religiao_fml);
		$stmt->bindValue(':dsc_institu_religiosa_freqtd', $dsc_institu_religiosa_freqtd);
		$stmt->bindValue(':dsc_freq_institu_religiosa', $dsc_freq_institu_religiosa);
		$stmt->bindValue(':habito_prece_oracao', $habito_prece_oracao);
		$stmt->bindValue(':evangelho_lar', $evangelho_lar);
		$stmt->bindValue(':conhece_espiritismo', $conhece_espiritismo);
		$stmt->bindValue(':vont_aprox_espiritismo', $vont_aprox_espiritismo);
		$stmt->bindValue(':dsc_casa', $dsc_casa);
		$stmt->bindValue(':exist_anim_inset_insal_perig', $exist_anim_inset_insal_perig);
		$stmt->bindValue(':dsc_anim_inset_insal_perig', $dsc_anim_inset_insal_perig);
		$stmt->bindValue(':exist_anim_estima', $exist_anim_estima);
		$stmt->bindValue(':dsc_anim_estima', $dsc_anim_estima);
		$stmt->bindValue(':vacina_anti_rabica_anim_estima', $vacina_anti_rabica_anim_estima);
		$stmt->bindValue(':dsc_cndc_saude_membros_fml', $dsc_cndc_saude_membros_fml);
		$stmt->bindValue(':dsc_carteira_vacina_crianca', $dsc_carteira_vacina_crianca);
		$stmt->bindValue(':dsc_doenca_cronica_fml', $dsc_doenca_cronica_fml);
		$stmt->bindValue(':dsc_restricao_alimentar', $dsc_restricao_alimentar);
		$stmt->bindValue(':dsc_higiene_pessoal', $dsc_higiene_pessoal);
		$stmt->bindValue(':cd_tip_moradia', $cd_tip_moradia);
		$stmt->bindValue(':dsc_dono_cedente_moradia', $dsc_dono_cedente_moradia);
		$stmt->bindValue(':vlr_desp_agua', $vlr_desp_agua);
		$stmt->bindValue(':vlr_desp_energia', $vlr_desp_energia);
		$stmt->bindValue(':vlr_desp_iptu', $vlr_desp_iptu);
		$stmt->bindValue(':vlr_desp_gas', $vlr_desp_gas);
		$stmt->bindValue(':vlr_desp_condominio', $vlr_desp_condominio);
		$stmt->bindValue(':vlr_desp_outra_manut', $vlr_desp_outra_manut);
		$stmt->bindValue(':dsc_desp_outra_manut', $dsc_desp_outra_manut);
		$stmt->bindValue(':dsc_desp_saude_medicamento', $dsc_desp_saude_medicamento);
		$stmt->bindValue(':dsc_desp_educ_creche_cuidadora', $dsc_desp_educ_creche_cuidadora);
		$stmt->bindValue(':dsc_desp_transporte', $dsc_desp_transporte);
		$stmt->bindValue(':dsc_desp_alimenta_especial', $dsc_desp_alimenta_especial);
		$stmt->bindValue(':dsc_outra_desp_geral', $dsc_outra_desp_geral);
		$stmt->bindValue(':cd_tip_trab', $cd_tip_trab);
		$stmt->bindValue(':vlr_renda_tip_trab', $vlr_renda_tip_trab);
		$stmt->bindValue(':dsc_tip_beneficio', $dsc_tip_beneficio);
		$stmt->bindValue(':vlr_renda_tip_beneficio', $vlr_renda_tip_beneficio);
		$stmt->bindValue(':dsc_expect_fml_capacit_profi', $dsc_expect_fml_capacit_profi);
		$stmt->bindValue(':dsc_curso_intere_profi_tecnico', $dsc_curso_intere_profi_tecnico);
		$stmt->bindValue(':dsc_projeto_gera_renda_extra', $dsc_projeto_gera_renda_extra);
		$stmt->bindValue(':dsc_aspecto_intimo', $dsc_aspecto_intimo);
		$stmt->bindValue(':dsc_prgm_trab', $this->__get('dsc_prgm_trab'));
		$stmt->bindValue(':cd_agua_moradia', $cd_agua_moradia);
		$stmt->bindValue(':cd_esgoto_moradia', $cd_esgoto_moradia);
		$stmt->execute();

		return $this;

	}	// Fim function updateSegmtoTriagemFml





}	// Fim da Classe

?>