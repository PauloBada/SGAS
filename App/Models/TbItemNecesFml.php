<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 19/09/2019
   Objetivo:  Contém os Sqls para a tabela
*/

namespace App\Models;

use MF\Model\Model;
use App\Controllers\Funcoes;

class TbItemNecesFml extends Model {
	
	// colunas da tabela
	private $cd_fmlID;             
	private $cd_itemID;
	private $cd_sbitemID;
	private $seql_item_necesID;             
	private $seql_acompID;          
	private $seql_acomp;          
	private $cd_setor_resp;
	private $obs_sobre_item;
	private $dt_prev_disponib_item;
	private $cd_disponib_item;
	private $dt_disponib_item_entrega;
	private $dsc_item_neces;
	private $qtd_item_neces;
	private $vlr_neces;
	private $cd_situ_item_solicitado;
	private $cd_tip_sexo;
	private $cd_estrut_corporal;
	private $cd_tam_corporal;
	private $nr_corporal;
	private $idade_aparente_pessoa;
	private $cd_tempo_idade;
	private $cd_tip_clas_item;
	private $cd_tip_unid_item;
	private $seql_integ;
	private $seql_integID;
	private $cd_vlnt_resp_cadas;
	private $cd_vlnt_resp_disponib;
	private $dt_incl_item_neces;
	private $in_neces_pre_triagem;
	private $cd_tip_evt_neces;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		$this->$atributo = $valor;
	}

// ====================================================== //

	public function getQtdItemSubitemNecessidade() {
		$query = "
				select count(*) as qtde
				from  tb_item_neces_fml
				where cd_fmlID                = :cd_fml
				and   cd_itemID               = :cd_item
				and   cd_sbitemID             = :cd_sbitem
				and   dt_prev_disponib_item   = str_to_date(:dt_prev_disponib_item, '%Y-%m-%d')   
				and   cd_situ_item_solicitado in (1, 2)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('dt_prev_disponib_item', $this->__get('dt_prev_disponib_item'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	
	}

// ====================================================== //

	public function insertItemSubitemNeces() {

		// Para gravar nulo nos campos quando não houver informação
		if (null !== ($this->__get('seql_acomp'))) {
			if (empty($this->__get('seql_acomp'))) {
				$seql_acomp = null;
			} else {
				$seql_acomp = $this->__get('seql_acomp');
			}
		} else {
			$seql_acomp = null;
		}

		if (null !== ($this->__get('cd_setor_resp'))) {
			if (empty($this->__get('cd_setor_resp'))) {
				$cd_setor_resp = 1;
			} else {
				$cd_setor_resp = $this->__get('cd_setor_resp');
			}
		} else {
			$cd_setor_resp = 1;
		}

		if (null !== ($this->__get('obs_sobre_item'))) {
			if (empty($this->__get('obs_sobre_item'))) {
				$obs_sobre_item = null;
			} else {
				$obs_sobre_item = $this->__get('obs_sobre_item');
			}
		} else {
			$obs_sobre_item = null;
		}

		if (null !== ($this->__get('dt_prev_disponib_item'))) {
			if (empty($this->__get('dt_prev_disponib_item'))) {
				$dt_prev_disponib_item = new \DateTime('31/12/9998');
				$dt_prev_disponib_item	= $dt_prev_disponib_item->format("d/m/Y");
			} else {
				$dt_prev_disponib_item = $this->__get('dt_prev_disponib_item');
				$dt_prev_disponib_item = Funcoes::formatarNumeros('data', $dt_prev_disponib_item, 10, "AMD");
			}
		} else {
			$dt_prev_disponib_item = new \DateTime('31/12/9998');
			$dt_prev_disponib_item	= $dt_prev_disponib_item->format("d/m/Y");
		}

		if (null !== ($this->__get('cd_disponib_item'))) {
			if (empty($this->__get('cd_disponib_item'))) {
				$cd_disponib_item = 1;
			} else {
				$cd_disponib_item = $this->__get('cd_disponib_item');
			}
		} else {
			$cd_disponib_item = 1;
		}

		if (null !== ($this->__get('dt_disponib_item_entrega'))) {
			if (empty($this->__get('dt_disponib_item_entrega'))) {
				$dt_disponib_item_entrega = new \DateTime('31-12-9998');
				$dt_disponib_item_entrega	= $dt_disponib_item_entrega->format("d/m/Y");
			} else {
				$dt_disponib_item_entrega = $this->__get('dt_disponib_item_entrega');
				$dt_disponib_item_entrega = Funcoes::formatarNumeros('data', $dt_disponib_item_entrega, 10, "AMD");
			}
		} else {
			$dt_disponib_item_entrega = new \DateTime('31-12-9998');
			$dt_disponib_item_entrega	= $dt_disponib_item_entrega->format("d/m/Y");
		}

		if (null !== ($this->__get('dsc_item_neces'))) {
			if (empty($this->__get('dsc_item_neces'))) {
				$dsc_item_neces = null;
			} else {
				$dsc_item_neces = $this->__get('dsc_item_neces');
			}
		} else {
			$dsc_item_neces = null;
		}

		if (null !== ($this->__get('qtd_item_neces'))) {
			if (empty($this->__get('qtd_item_neces'))) {
				$qtd_item_neces = 1;
			} else {
				$qtd_item_neces = $this->__get('qtd_item_neces');
			}
		} else {
			$qtd_item_neces = 1;
		}

		if (null !== ($this->__get('vlr_neces'))) {
			if (empty($this->__get('vlr_neces'))) {
				$vlr_neces = 0;
			} else {
				$vlr_neces = str_replace('.','', $this->__get('vlr_neces'));
				$vlr_neces = str_replace(',','.', $vlr_neces);
			}
		} else {
			$vlr_neces = 0;
		}

		if (null !== ($this->__get('cd_situ_item_solicitado'))) {
			if (empty($this->__get('cd_situ_item_solicitado'))) {
				$cd_situ_item_solicitado = 1;
			} else {
				$cd_situ_item_solicitado = $this->__get('cd_situ_item_solicitado');
			}
		} else {
			$cd_situ_item_solicitado = 1;
		}

		if (null !== ($this->__get('cd_tip_sexo'))) {
			if (empty($this->__get('cd_tip_sexo'))) {
				$cd_tip_sexo = 0;
			} else {
				$cd_tip_sexo = $this->__get('cd_tip_sexo');
			}
		} else {
			$cd_tip_sexo = 0;
		}

		if (null !== ($this->__get('cd_estrut_corporal'))) {
			if (empty($this->__get('cd_estrut_corporal'))) {
				$cd_estrut_corporal = 0;
			} else {
				$cd_estrut_corporal = $this->__get('cd_estrut_corporal');
			}
		} else {
			$cd_estrut_corporal = 0;
		}

		if (null !== ($this->__get('cd_tam_corporal'))) {
			if (empty($this->__get('cd_tam_corporal'))) {
				$cd_tam_corporal = 0;
			} else {
				$cd_tam_corporal = $this->__get('cd_tam_corporal');
			}
		} else {
			$cd_tam_corporal = 0;
		}

		if (null !== ($this->__get('nr_corporal'))) {
			if (empty($this->__get('nr_corporal'))) {
				$nr_corporal = 0;
			} else {
				$nr_corporal = $this->__get('nr_corporal');
			}
		} else {
			$nr_corporal = 0;
		}
		
		if (null !== ($this->__get('idade_aparente_pessoa'))) {
			if (empty($this->__get('idade_aparente_pessoa'))) {
				$idade_aparente_pessoa = null;
			} else {
				$idade_aparente_pessoa = $this->__get('idade_aparente_pessoa');
			}
		} else {
			$idade_aparente_pessoa = null;
		}

		if (null !== ($this->__get('cd_tempo_idade'))) {
			if (empty($this->__get('cd_tempo_idade'))) {
				$cd_tempo_idade = 0;
			} else {
				$cd_tempo_idade = $this->__get('cd_tempo_idade');
			}
		} else {
			$cd_tempo_idade = 0;
		}

		if (null !== ($this->__get('cd_tip_clas_item'))) {
			if (empty($this->__get('cd_tip_clas_item'))) {
				$cd_tip_clas_item = 0;
			} else {
				$cd_tip_clas_item = $this->__get('cd_tip_clas_item');
			}
		} else {
			$cd_tip_clas_item = 0;
		}

		if (null !== ($this->__get('cd_tip_unid_item'))) {
			if (empty($this->__get('cd_tip_unid_item'))) {
 				$cd_tip_unid_item = 2;
			} else {
				$cd_tip_unid_item = $this->__get('cd_tip_unid_item');
			}
		} else {
			$cd_tip_unid_item = 2;
		}

		if (null !== ($this->__get('seql_integ'))) {
			if (empty($this->__get('seql_integ'))) {
				$seql_integ = null;
			} else {
				$seql_integ = $this->__get('seql_integ');
			}
		} else {
			$seql_integ = null;
		}

		if (null !== ($this->__get('cd_vlnt_resp_cadas'))) {
			if (empty($this->__get('cd_vlnt_resp_cadas'))) {
				$cd_vlnt_resp_cadas = $_SESSION['id'];
			} else {
				$cd_vlnt_resp_cadas = $this->__get('cd_vlnt_resp_cadas');
			}
		} else {
			$cd_vlnt_resp_cadas = $_SESSION['id'];
		}

		if (null !== ($this->__get('cd_vlnt_resp_disponib'))) {
			if (empty($this->__get('cd_vlnt_resp_disponib'))) {
				$cd_vlnt_resp_disponib = null;
			} else {
				$cd_vlnt_resp_disponib = $this->__get('cd_vlnt_resp_disponib');
			}
		} else {
			$cd_vlnt_resp_disponib = null;
		}

		if (null !== ($this->__get('in_neces_pre_triagem'))) {
			if (empty($this->__get('in_neces_pre_triagem'))) {
				$in_neces_pre_triagem = 'N';
			} else {
				$in_neces_pre_triagem = $this->__get('in_neces_pre_triagem');
			}
		} else {
			$in_neces_pre_triagem = 'N';
		}

		if (null !== ($this->__get('cd_tip_evt_neces'))) {
			if (empty($this->__get('cd_tip_evt_neces'))) {
				$cd_tip_evt_neces = 0;
			} else {
				$cd_tip_evt_neces = $this->__get('cd_tip_evt_neces');
			}
		} else {
			$cd_tip_evt_neces = 0;
		}

		$this->getProximoSequencial();

		/*
		echo '<br>';
		echo 'cd_fml '.$this->__get('cd_fml');
		echo '<br>';
		echo 'cd_item '. $this->__get('cd_item');
		echo '<br>';
		echo 'cd_sbitem '. $this->__get('cd_sbitem');
		echo '<br>';
		echo 'Sequencial item '.$this->__get('seql_max');
		echo '<br>';
		echo 'seql_acomp '.$seql_acomp;          
		echo '<br>';
		echo 'cd_setor_resp '.$cd_setor_resp;
		echo '<br>';
		echo 'obs_sobre_item '.$obs_sobre_item;
		echo '<br>';		
		echo 'dt_prev_disponib_item '.$dt_prev_disponib_item;
		echo '<br>';		
		echo 'cd_disponib_item '.$cd_disponib_item;
		echo '<br>';		
		echo 'dt_disponib_item_entrega '.$dt_disponib_item_entrega;
		echo '<br>';		
		echo 'dsc_item_neces '.$dsc_item_neces;
		echo '<br>';		
		echo 'qtd_item_neces '.$qtd_item_neces;
		echo '<br>';		
		echo 'vlr_neces '.$vlr_neces;
		echo '<br>';		
		echo 'cd_situ_item_solicitado '.$cd_situ_item_solicitado;
		echo '<br>';		
		echo 'cd_tip_sexo '.$cd_tip_sexo;
		echo '<br>';		
		echo 'cd_estrut_corporal '.$cd_estrut_corporal;
		echo '<br>';		
		echo 'cd_tam_corporal '.$cd_tam_corporal;
		echo '<br>';		
		echo 'nr_corporal '.$nr_corporal;
		echo '<br>';		
		echo 'idade_aparente_pessoa '.$idade_aparente_pessoa;
		echo '<br>';		
		echo 'cd_tempo_idade '.$cd_tempo_idade;
		echo '<br>';		
		echo 'cd_tip_clas_item '.$cd_tip_clas_item;
		echo '<br>';		
		echo 'cd_tip_unid_item '.$cd_tip_unid_item;
		echo '<br>';		
		echo 'seql_integ '.$seql_integ;
		echo '<br>';		
		echo 'cd_vlnt_resp_cadas '.$cd_vlnt_resp_cadas;
		echo '<br>';		
		echo 'cd_vlnt_resp_disponib '.$cd_vlnt_resp_disponib;
		echo '<br>';		
		echo 'in_neces_pre_triagem '.$in_neces_pre_triagem;
		*/

		$query = "
				insert into tb_item_neces_fml
				(cd_fmlID,             
				cd_itemID,
				cd_sbitemID,
				seql_item_necesID,             
				seql_acompID,          
				cd_setor_resp,
				obs_sobre_item,
				dt_prev_disponib_item,
				cd_disponib_item,
				dt_disponib_item_entrega,
				dsc_item_neces,
				qtd_item_neces,
				vlr_neces,
				cd_situ_item_solicitado,
				cd_tip_sexo,
				cd_estrut_corporal,
				cd_tam_corporal,
				nr_corporal,
				idade_aparente_pessoa,
				cd_tempo_idade,
				cd_tip_clas_item,
				cd_tip_unid_item,
				seql_integID,
				cd_vlnt_resp_cadas,
				cd_vlnt_resp_disponib,
				dt_incl_item_neces,
				in_neces_pre_triagem,
				cd_tip_evt_neces) 

				values 

				(:cd_fml,             
				:cd_item,
				:cd_sbitem,
				:seql_item_neces,             
				:seql_acomp,          
				:cd_setor_resp,
				:obs_sobre_item,
				str_to_date(:dt_prev_disponib_item, '%d/%m/%Y'),  
				:cd_disponib_item,
				str_to_date(:dt_disponib_item_entrega, '%d/%m/%Y'),  
				:dsc_item_neces,
				:qtd_item_neces,
				:vlr_neces,
				:cd_situ_item_solicitado,
				:cd_tip_sexo,
				:cd_estrut_corporal,
				:cd_tam_corporal,
				:nr_corporal,
				:idade_aparente_pessoa,
				:cd_tempo_idade,
				:cd_tip_clas_item,
				:cd_tip_unid_item,
				:seql_integ,
				:cd_vlnt_resp_cadas,
				:cd_vlnt_resp_disponib,
				now(),
				:in_neces_pre_triagem,
				:cd_tip_evt_neces)";

		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('seql_item_neces', $this->__get('seql_max'));
		$stmt->bindValue('seql_acomp', $seql_acomp);          
		$stmt->bindValue('cd_setor_resp', $cd_setor_resp);
		$stmt->bindValue('obs_sobre_item', $obs_sobre_item);
		$stmt->bindValue('dt_prev_disponib_item', $dt_prev_disponib_item);
		$stmt->bindValue('cd_disponib_item', $cd_disponib_item);
		$stmt->bindValue('dt_disponib_item_entrega', $dt_disponib_item_entrega);
		$stmt->bindValue('dsc_item_neces', $dsc_item_neces);
		$stmt->bindValue('qtd_item_neces', $qtd_item_neces);
		$stmt->bindValue('vlr_neces', $vlr_neces);
		$stmt->bindValue('cd_situ_item_solicitado', $cd_situ_item_solicitado);
		$stmt->bindValue('cd_tip_sexo', $cd_tip_sexo);
		$stmt->bindValue('cd_estrut_corporal', $cd_estrut_corporal);
		$stmt->bindValue('cd_tam_corporal', $cd_tam_corporal);
		$stmt->bindValue('nr_corporal', $nr_corporal);
		$stmt->bindValue('idade_aparente_pessoa', $idade_aparente_pessoa);
		$stmt->bindValue('cd_tempo_idade', $cd_tempo_idade);
		$stmt->bindValue('cd_tip_clas_item', $cd_tip_clas_item);
		$stmt->bindValue('cd_tip_unid_item', $cd_tip_unid_item);
		$stmt->bindValue('seql_integ', $seql_integ);
		$stmt->bindValue('cd_vlnt_resp_cadas', $cd_vlnt_resp_cadas);
		$stmt->bindValue('cd_vlnt_resp_disponib', $cd_vlnt_resp_disponib);
		$stmt->bindValue('in_neces_pre_triagem', $in_neces_pre_triagem);
		$stmt->bindValue('cd_tip_evt_neces', $cd_tip_evt_neces);
		$stmt->execute();

		return $this;

	}	// Fim function insertItemSubitemNeces

// =================================================== //

	public function getProximoSequencial() {
		$query0 = "select count(*) + 1 as qtde
					from  tb_item_neces_fml
					where cd_fmlID    = :cd_fml
					and   cd_itemID   = :cd_item
					and   cd_sbitemID = :cd_sbitem";
		$stmt0 = $this->db->prepare($query0);
		$stmt0->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt0->bindValue('cd_item', $this->__get('cd_item'));
		$stmt0->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt0->execute();
		
		$nr_registros = $stmt0->fetch(\PDO::FETCH_ASSOC);		

		if ($nr_registros['qtde'] == 1) {
			$this->__set ('seql_max', $nr_registros['qtde']);
			
		} else {
			$query1 = "select max(seql_item_necesID) + 1 as qtde
					from  tb_item_neces_fml
					where cd_fmlID    = :cd_fml
					and   cd_itemID   = :cd_item
					and   cd_sbitemID = :cd_sbitem";
			$stmt1 = $this->db->prepare($query1);
			$stmt1->bindValue('cd_fml', $this->__get('cd_fml'));
			$stmt1->bindValue('cd_item', $this->__get('cd_item'));
			$stmt1->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
			$stmt1->execute();

			$nr_registros_1 = $stmt1->fetch(\PDO::FETCH_ASSOC);

			$this->__set ('seql_max', $nr_registros_1['qtde']);
		}
	}	// Fim function getProximoSequencial

// ====================================================== //

	public function deleteItemSubitemNeces() {
		$query = "
				delete 
				from  tb_item_neces_fml
				where cd_fmlID                = :cd_fml
				and   cd_itemID               = :cd_item
				and   cd_sbitemID             = :cd_sbitem
				and   dt_prev_disponib_item   = str_to_date(:dt_prev_disponib_item, '%Y-%m-%d')   
				and   cd_situ_item_solicitado in (1, 2)";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('dt_prev_disponib_item', $this->__get('dt_prev_disponib_item'));
		$stmt->execute();

		return $this;	

	}	// Fim function deleteItemSubitemNeces

// ====================================================== //

	public function getQtdItemSubitemNecesNaoKH() {
		$query = "
				select count(*) as qtde
				from  tb_item_neces_fml
				where cd_fmlID                 = :cd_fml
				and   dt_prev_disponib_item   >= now() 
				and   cd_situ_item_solicitado  = 1 
				and   cd_disponib_item         = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	// Fim function getQtdItemSubitemNecesNaoKH

// ====================================================== //

	public function cancelaItemSubitemNecesNaoKH() {
		$query = "
				update  tb_item_neces_fml
				set     cd_situ_item_solicitado = 3
				where cd_fmlID                 = :cd_fml
				and   dt_prev_disponib_item   >= now() 
				and   cd_situ_item_solicitado  = 1 
				and   cd_disponib_item         = 1";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->execute();

		return $this;	

	}	// Fim function cancelaItemSubitemNecesNaoKH

// ====================================================== //

	public function getDadosNecesEspecifica() {
		$query = "
				select *,
		        case when cd_situ_item_solicitado = 1 THEN 'Pendente'
					 when cd_situ_item_solicitado = 2 THEN 'Atendido'
					 when cd_situ_item_solicitado = 3 THEN 'Cancelado/Indisponível'
				end	as nm_situ_item_solicitado				

				from  tb_item_neces_fml
				where cd_fmlID          = :cd_fml
				and   cd_itemID         = :cd_item
				and   cd_sbitemID       = :cd_sbitem
				and   seql_item_necesID = :seql_item_neces";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('seql_item_neces', $this->__get('seql_item_neces'));
		$stmt->execute();

		return $stmt->fetch(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosNecesEspecifica

// ====================================================== //

	public function alteraItemSubitemNecesPV() {

		if (null !== ($this->__get('vlr_neces'))) {
			if (empty($this->__get('vlr_neces'))) {
				$vlr_neces = 0;
			} else {
				$vlr_neces = str_replace('.','', $this->__get('vlr_neces'));
				$vlr_neces = str_replace(',','.', $vlr_neces);
			}
		} else {
			$vlr_neces = 0;
		}

		$query = "
				update tb_item_neces_fml
				set obs_sobre_item      = :obs_sobre_item,
					dsc_item_neces      = :dsc_item_neces,
					qtd_item_neces      = :qtd_item_neces,
					vlr_neces           = :vlr_neces,
					cd_tip_unid_item    = :cd_tip_unid_item,
					cd_vlnt_resp_cadas  = :cd_vlnt_resp_cadas
				where cd_fmlID          = :cd_fml
				and   cd_itemID         = :cd_item
				and   cd_sbitemID       = :cd_sbitem
				and   seql_item_necesID = :seql_item_neces";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('seql_item_neces', $this->__get('seql_item_neces'));
		$stmt->bindValue('obs_sobre_item', $this->__get('obs_sobre_item'));
		$stmt->bindValue('dsc_item_neces', $this->__get('dsc_item_neces'));
		$stmt->bindValue('qtd_item_neces', $this->__get('qtd_item_neces'));
		$stmt->bindValue('vlr_neces', $vlr_neces);
		$stmt->bindValue('cd_tip_unid_item', $this->__get('cd_tip_unid_item'));
		$stmt->bindValue('cd_vlnt_resp_cadas', $this->__get('cd_vlnt_resp_cadas'));

		$stmt->execute();

		return $this;

	}	// Fim function alteraItemSubitemNecesPV

// ====================================================== //

	public function excluiLogicamenteItemSubitemNeces() {
		$query = "
				update tb_item_neces_fml
				set    cd_situ_item_solicitado = 3,
				       cd_disponib_item        = 3,
					   obs_sobre_item          = :obs_sobre_item,
					   dsc_item_neces          = :dsc_item_neces,
					   cd_vlnt_resp_disponib   = :cd_vlnt_resp_disponib
				where cd_fmlID          = :cd_fml
				and   cd_itemID         = :cd_item
				and   cd_sbitemID       = :cd_sbitem
				and   seql_item_necesID = :seql_item_neces";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('seql_item_neces', $this->__get('seql_item_neces'));
		$stmt->bindValue('obs_sobre_item', $this->__get('obs_sobre_item'));
		$stmt->bindValue('dsc_item_neces', $this->__get('dsc_item_neces'));
		$stmt->bindValue('cd_vlnt_resp_disponib', $this->__get('cd_vlnt_resp_disponib'));
		$stmt->execute();

		return $this;

	}	// Fim function excluiLogicamenteItemSubitemNeces

// ====================================================== //

	// Somente Grupo para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_01() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_01

// ====================================================== //

	// Somente Grupo e Subgrupo para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_02() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID

				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp

				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_02

// ====================================================== //

	// Somente Grupo, Subgrupo e Família para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_03() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = :cd_fml
				and	  a.cd_fmlID                = e.cd_fmlID

				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_03

// ====================================================== //

	// Somente Grupo, Subgrupo, Família e Setor Responsável para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_04() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = :cd_fml
				and   a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_04

// ====================================================== //

	// Somente Grupo, Subgrupo, Família, Setor Responsável e Item para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_05() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = :cd_fml
				and   a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_05

// ====================================================== //

	// Somente Grupo, Subgrupo, Família, Setor Responsável, Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_06() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = :cd_fml
				and   a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_sbitemID             = :cd_sbitem
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_06

// ====================================================== //

	// Somente Grupo, Subgrupo e Setor Responsável para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_07() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_07

// ====================================================== //

	// Somente Grupo, Subgrupo, Setor Responsável e Item para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_08() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_08

// ====================================================== //

	// Somente Grupo, Subgrupo, Setor Responsável, Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_09() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_sbitemID             = :cd_sbitem
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_09

// ====================================================== //

	// Somente Grupo, Subgrupo e Item para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_10() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_10

// ====================================================== //

	// Somente Grupo, Subgrupo, Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_11() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_sbitemID             = :cd_sbitem
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_11

// ====================================================== //

	// Somente Grupo, Subgrupo, Família, Setor Responsável, Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_12() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = :cd_fml
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_12

// ====================================================== //

	// Somente Grupo, Subgrupo, Família, Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_13() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = :cd_fml
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_sbgrpID              = :cd_sbgrp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_sbitemID             = :cd_sbitem
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_sbgrp', $this->__get('cd_sbgrp'));
		$stmt->bindValue('cd_fml', $this->__get('cd_fml'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_13


// ====================================================== //

	// Somente Grupo e Setor Responsável para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_14() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_14

// ====================================================== //

	// Somente Grupo, Setor Responsável e Item para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_15() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_15

// ====================================================== //

	// Somente Grupo, Setor Responsável, Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_16() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_setor_resp           = :cd_setor_resp
				and	  a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_sbitemID             = :cd_sbitem
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_setor_resp', $this->__get('cd_setor_resp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_16

// ====================================================== //

	// Somente Grupo e Item para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_17() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_17

// ====================================================== //

	// Somente Grupo, Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_18() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID
				and   e.cd_grpID                = :cd_grp
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_sbitemID             = :cd_sbitem
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_grp', $this->__get('cd_grp'));
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_18

// ====================================================== //

	// Somente Item para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_19() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml,
						f.cd_grpID as cd_grp,
						f.nm_grp as nm_grp
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_19

// ====================================================== //

	// Somente Item e Subitem para Pesquisar Item Necessidade da Família
	public function getDadosPesquisaOpcao_20() {
		$query = "
				select 	b.cd_fmlID, 
						a.cd_itemID, 
						a.cd_sbitemID, 
				        a.seql_item_necesID,
				        a.cd_setor_resp, 
				        case when a.cd_setor_resp = 1 THEN 'Almoxarifado'
							 when a.cd_setor_resp = 2 THEN 'Grupo de Família'
							 when a.cd_setor_resp = 3 THEN 'Setor Orientação Profissional'
							 when a.cd_setor_resp = 4 THEN 'Biblioteca Canguru'
							 when a.cd_setor_resp = 5 THEN 'Biblioteca Canguru - Contadores de História'                              
							 when a.cd_setor_resp = 6 THEN 'Grupo de Desobesessão Auta de Souza'                              
							 when a.cd_setor_resp = 7 THEN 'Implantação do Evenagelho no Lar'
							 when a.cd_setor_resp = 8 THEN 'Parcerias Médicas'
							 when a.cd_setor_resp = 9 THEN 'Grupo de Apoio/Atendimento sede Comunhão'
							 when a.cd_setor_resp = 10 THEN 'Direção Comunhão'                              
						end	as nm_setor_resp,        
						b.nm_grp_fmlr as nm_grp_fmlr,         
				        c.nm_item as nm_item,      
				        d.nm_sbitem as nm_sbitem, 
				        g.nm_sbgrp as nm_sbgrp,
				        DATE_FORMAT(a.dt_prev_disponib_item, '%d/%m/%Y') as dt_prev_disponib_item,
				        a.cd_tip_evt_neces as cd_tip_evt_neces,
				        case when a.cd_tip_evt_neces = 1 THEN 'Próxima Vista'
							 when a.cd_tip_evt_neces = 2 THEN 'Quando disponível'
						end	as nm_tip_evt_neces,
                        g.cd_sbgrpID as cd_sbgrp,
				        case when b.cd_est_situ_fml = 2 THEN 'Aguardando Triagem (Início/Término)'
							 when b.cd_est_situ_fml = 3 THEN 'Em Atendimento'
							 when b.cd_est_situ_fml = 4 THEN 'Atendimento Encerrado'
						end	as nm_est_situ_fml,
						b.cd_est_situ_fml,
						f.cd_grpID as cd_grp,
						f.nm_grp as nm_grp
				from tb_item_neces_fml a,
					 tb_fml b, 
				     tb_item_suprimt c,
				     tb_sbitem_suprimt d,
				     tb_vncl_fml_sbgrp e,
				     tb_grp f,
				     tb_sbgrp g
				where a.cd_fmlID                = e.cd_fmlID
				and   e.cd_est_vncl             = 1
				and   e.cd_grpID                = f.cd_grpID
				and   e.cd_grpID                = g.cd_grpID
				and   e.cd_sbgrpID              = g.cd_sbgrpID
				and   a.cd_fmlID                = b.cd_fmlID                    
				and   a.cd_itemID               = :cd_item
				and   a.cd_sbitemID             = :cd_sbitem
				and   a.cd_itemID               = c.cd_itemID
				and   a.cd_itemID               = d.cd_itemID
				and   a.cd_sbitemID             = d.cd_sbitemID                    
				and   b.cd_est_situ_fml         in (2, 3, 4)
				and   a.cd_situ_item_solicitado = :cd_situ_item_solicitado
				and   a.cd_disponib_item        = :cd_disponib_item
				order by b.nm_grp_fmlr, a.dt_prev_disponib_item, a.cd_itemID, a.cd_sbitemID";
		$stmt = $this->db->prepare($query);
		$stmt->bindValue('cd_item', $this->__get('cd_item'));
		$stmt->bindValue('cd_sbitem', $this->__get('cd_sbitem'));
		$stmt->bindValue('cd_situ_item_solicitado', $this->__get('cd_situ_item_solicitado'));
		$stmt->bindValue('cd_disponib_item', $this->__get('cd_disponib_item'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);	

	}	// Fim function getDadosPesquisaOpcao_20


}	// Fim da Classe

?>