<?php
/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 11/07/2019                       
   Objetivo:  Obtem as informações para Combobox de dados de tabelas com foreng key na chave primária
              como por exemplo, subitem cujo dependência é de item, ou seja, para se obter os subitens,
              se faz necessário saber qual é o item atrelado.
/* ======================================

Programa chamado pelos programas:
 - apoioSubitemAlterar.phtml 
 - apoioSubitemConsultar.phtml 
 - 
 - 
 ========================================*/

// Comandos abaixo não funcionaram e tive que definir $db abaixo em cada função
// use App\Connection; 
// use MF\Model\Container;  
// namespace MF\Model; 

$opcao = isset($_GET['opcao']) ? $_GET['opcao'] : '';

$valor = isset($_GET['valor']) ? $_GET['valor'] : ''; 


if (! empty($opcao)){   
    switch ($opcao)
    {
        case 'item':
            {
                echo getDadosItemAll();
                break;
            }

        case 'subitem':
            {
                echo getDadosSubitemAll	($valor);
                break;
            }

        case 'grupo':
            {
                echo getDadosGrupoAll();
                break;
            }

        case 'subgrupo':
            {
                echo getDadosSubgrupoAll ($valor);
                break;
            }

        case 'subgrupo2':
            {
                echo getDadosSubgrupoAll2 ($valor);
                break;
            }

        // Desvincular Voluntário de Grupo/Subgrupo //
        case 'grupoVnclVlntGrp':
            {
                echo getDadosGrupoVVG();

                break;
            }

        case 'subgrupoVnclVlntGrp':
            {
                echo getDadosSubgrupoVVG($valor); 
                break;
            }

        case 'voluntarioVnclVlntGrp':
            {
                echo getDadosVoluntarioVVG($valor);
                break;
            }

        // Consultar Voluntário em Grupo/Subgrupo//
        case 'grupoVnclVlntGrpAll':
            {
                echo getDadosGrupoVVGAll();

                break;
            }

        case 'subgrupoVnclVlntGrpAll':
            {
                echo getDadosSubgrupoVVGAll($valor); 
                break;
            }

        case 'voluntarioVnclVlntGrpAll':
            {
                echo getDadosVoluntarioVVGAll($valor);
                break;
            }

        // Desvincular Família de Grupo/Subgrupo //
        case 'grupoVnclFmlSbgrp':
            {
                echo getDadosGrupoVFS();

                break;
            }

        case 'subgrupoVnclFmlSbgrp':
            {
                echo getDadosSubgrupoVFS($valor); 
                break;
            }

        // Consultar Família de Grupo/Subgrupo //
        case 'grupoVnclFmlSbgrpAll':
            {
                echo getDadosGrupoVFSAll();

                break;
            }

        case 'subgrupoVnclFmlSbgrpAll':
            {
                echo getDadosSubgrupoVFSAll($valor); 
                break;
            }

        case 'familiaVnclFmlSbgrp':
            {
                echo getDadosFamiliaVFS($valor);
                break;
            }

        case 'familiaVnclFmlSbgrpAll':
            {
                echo getDadosFamiliaVFSAll($valor);
                break;
            }

        // Tratamento de Família e Integrantes //
        case 'familia':
            {
                echo getDadosFamiliaAll();
                break;
            }

        case 'integrante':
            {
                echo getDadosIntegranteAll($valor);
                break;
            }

        case 'integranteE':
            {
                echo getDadosIntegranteAllE($valor);
                break;
            }

        case 'familiaAll':
            {
                echo getDadosFamiliaAll2();
                break;
            }

        case 'integranteAll':
            {
                echo getDadosIntegranteAll2($valor);
                break;
            }

        case 'maxDataAcomp':
            {
                echo getMaxDataAcomp($valor);
                break;
            }

        case 'familiaAll3':
            {
                echo getDadosFamiliaAll3();
                break;
            }

        case 'itemEvtNeces':
            {
                echo getDadosItemEvtNeces($valor);
                break;
            }


    }
}

// ====================================================== //

function getDB() {
	try {
		// Deverá ser colocado o endereço do servidor da comunhão
		$db = new \PDO(
			"mysql:host=localhost;dbname=sgas;charset=utf8",
			"root",
			""
		);

		return $db;

	} catch (\PDOException $e) {
		// Tratar o erro
		echo "Erro: ".$e;
		return null;
	}
}

// ====================================================== //

function getDadosItemAll() {
	$db = getDB();

	$query = "
			select 	cd_itemID as cod_item,
					nm_item as nome_item,
					cd_tip_evt_suprimt as cod_evento
				from tb_item_suprimt
				order by nm_item";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
	
	// Não consegui executar, pois dava erro de not found em "MF\Model\Container"
	//$dadosItem_alterar = Container::getModel('TbItemSuprimt');
	//$dadosItem_alterar->getDadosItemAll3();
	//echo json_encode($dadosItem_alterar);	

}	

// ====================================================== //

function getDadosSubitemAll($item) {
	$db = getDB();

	$query = "
			select 	cd_itemID as cod_item,
					cd_sbitemID as cod_subitem,
					nm_sbitem as nome_subitem
				from  tb_sbitem_suprimt
				where cd_itemID = :cd_item
				order by cd_itemID, cd_sbitemID";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_item', $item);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosGrupoAll() {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					nm_grp as nome_grupo,
					cd_semn_atu as cod_semana
				from tb_grp
				where cd_est_grp = 1
				and   cd_grpID in (select cd_grpID
									from tb_sbgrp
									where cd_est_sbgrp = 1)
				order by nm_grp";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosSubgrupoAll($grupo) {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					cd_sbgrpID as cod_subgrupo,
					nm_sbgrp as nome_subgrupo
				from  tb_sbgrp
				where cd_grpID     = :cd_grp
				and   cd_est_sbgrp = 1
				order by cd_grpID, cd_sbgrpID";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //
function getDadosSubgrupoAll2($grupo) {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					cd_sbgrpID as cod_subgrupo,
					nm_sbgrp as nome_subgrupo
				from  tb_sbgrp
				where cd_grpID     = :cd_grp
				order by cd_grpID, cd_sbgrpID";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosGrupoVVG() {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					nm_grp as nome_grupo
				from tb_grp
				where  cd_grpID in (select cd_grpID
									from  tb_vncl_vlnt_grp
									where cd_est_vncl = 1)
				and   cd_est_grp = 1
				order by nm_grp";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosSubgrupoVVG($grupo) {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					cd_sbgrpID as cod_subgrupo,
					nm_sbgrp as nome_subgrupo
				from  tb_sbgrp
				where cd_grpID     = :cd_grp
				and   cd_est_sbgrp = 1
				and   cd_grpID in (select cd_grpID
									from  tb_vncl_vlnt_grp
									where cd_grpID    = :cd_grp
									and   cd_est_vncl = 1)";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;

}	

// ====================================================== //

function getDadosVoluntarioVVG($grupo_subgrupo) {
	$db = getDB();

	// Recebe a variável como (grupo;subgrupo)
	$grupoSubgrupo = explode(';', $grupo_subgrupo);
	$grupo = $grupoSubgrupo[0];
	$subgrupo = $grupoSubgrupo[1];

	if ($subgrupo == 'Sem Subgrupo') {
		$query = "
				select 	cd_vlntID as cod_voluntario,
						nm_vlnt as nome_voluntario
					from tb_vlnt
					where  cd_vlntID in (select cd_vlntID
										from  tb_vncl_vlnt_grp
										where cd_grpID   = :cd_grp
										and   cd_sbgrpID is null
										and   cd_est_vncl = 1)
					order by nm_vlnt";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':cd_grp', $grupo);
		$stmt->execute();

		echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	} else {
		$query = "
				select 	cd_vlntID as cod_voluntario,
						nm_vlnt as nome_voluntario
					from tb_vlnt
					where  cd_vlntID in (select cd_vlntID
										from  tb_vncl_vlnt_grp
										where cd_grpID   = :cd_grp
										and   cd_sbgrpID = :cd_sbgrp
										and   cd_est_vncl = 1)
					order by nm_vlnt";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':cd_grp', $grupo);
		$stmt->bindValue(':cd_sbgrp', $subgrupo);
		$stmt->execute();

		echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
	}

	$db = null;
}	

// ====================================================== //

function getDadosGrupoVVGAll() {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					nm_grp as nome_grupo
				from tb_grp
				where  cd_grpID in (select cd_grpID
									from  tb_vncl_vlnt_grp)
				order by nm_grp";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosSubgrupoVVGAll($grupo) {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					cd_sbgrpID as cod_subgrupo,
					nm_sbgrp as nome_subgrupo
				from  tb_sbgrp
				where cd_grpID     = :cd_grp
				and   cd_grpID in (select cd_grpID
									from  tb_vncl_vlnt_grp
									where cd_grpID = :cd_grp)";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;

}	

// ====================================================== //

function getDadosVoluntarioVVGAll($grupo_subgrupo) {
	$db = getDB();

	// Recebe a variável como (grupo;subgrupo)
	$grupoSubgrupo = explode(';', $grupo_subgrupo);
	$grupo = $grupoSubgrupo[0];
	$subgrupo = $grupoSubgrupo[1];

	if ($subgrupo == 'Sem Subgrupo') {
		$query = "
				select 	cd_vlntID as cod_voluntario,
						nm_vlnt as nome_voluntario
					from tb_vlnt
					where  cd_vlntID in (select cd_vlntID
										from  tb_vncl_vlnt_grp
										where cd_grpID   = :cd_grp
										and   cd_sbgrpID is null)
					order by nm_vlnt";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':cd_grp', $grupo);
		$stmt->execute();

		echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	} else {
		$query = "
				select 	cd_vlntID as cod_voluntario,
						nm_vlnt as nome_voluntario
					from tb_vlnt
					where  cd_vlntID in (select cd_vlntID
										from  tb_vncl_vlnt_grp
										where cd_grpID   = :cd_grp
										and   cd_sbgrpID = :cd_sbgrp)
					order by nm_vlnt";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':cd_grp', $grupo);
		$stmt->bindValue(':cd_sbgrp', $subgrupo);
		$stmt->execute();

		echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
	}

	$db = null;
}	

// ====================================================== //

function getDadosGrupoVFS() {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					nm_grp as nome_grupo
				from tb_grp
				where  cd_grpID in (select cd_grpID
									from  tb_vncl_fml_sbgrp
									where cd_est_vncl = 1)
				and   cd_est_grp = 1
				order by nm_grp";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosSubgrupoVFS($grupo) {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					cd_sbgrpID as cod_subgrupo,
					nm_sbgrp as nome_subgrupo
				from  tb_sbgrp
				where cd_grpID     = :cd_grp
				and   cd_est_sbgrp = 1
				and   cd_grpID in (select cd_grpID
									from  tb_vncl_fml_sbgrp
									where cd_grpID    = :cd_grp
									and   cd_est_vncl = 1)";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;

}	

// ====================================================== //

function getDadosGrupoVFSAll() {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					nm_grp as nome_grupo
				from tb_grp
				where  cd_grpID in (select cd_grpID
									from  tb_vncl_fml_sbgrp)
				and   cd_est_grp = 1
				order by nm_grp";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosSubgrupoVFSAll($grupo) {
	$db = getDB();

	$query = "
			select 	cd_grpID as cod_grupo,
					cd_sbgrpID as cod_subgrupo,
					nm_sbgrp as nome_subgrupo
				from  tb_sbgrp
				where cd_grpID     = :cd_grp
				and   cd_est_sbgrp = 1
				and   cd_grpID in (select cd_grpID
									from  tb_vncl_fml_sbgrp
									where cd_grpID    = :cd_grp)";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;

}	

// ====================================================== //

function getDadosFamiliaVFSAll($grupo_subgrupo) {
	$db = getDB();

	// Recebe a variável como (grupo;subgrupo)
	$grupoSubgrupo = explode(';', $grupo_subgrupo);
	$grupo = $grupoSubgrupo[0];
	$subgrupo = $grupoSubgrupo[1];

	$query = "
			select 	cd_fmlID as cod_familia,
					nm_grp_fmlr as nome_familia
				from tb_fml
				where  cd_fmlID in (select cd_fmlID
									from  tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp)
				order by nm_grp_fmlr";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->bindValue(':cd_sbgrp', $subgrupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	


// ====================================================== //

function getDadosFamiliaAll() {
	$db = getDB();

	$query = "
			select 	cd_fmlID as cod_familia,
					nm_grp_fmlr as nome_familia
				from tb_fml
				where cd_est_situ_fml between 1 and 3
				order by nm_grp_fmlr";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosIntegranteAll($familia) {
	$db = getDB();

	$query = "
			select 	cd_fmlID as cod_familia,
					seql_integID as seql_integrante,
					nm_integ as nome_integrante
				from  tb_integ_fml
				where cd_fmlID         = :cd_fml
				and   cd_est_integ_fml = 1
				and   seql_integID >= 1
				order by nm_integ";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_fml', $familia);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosIntegranteAllE($familia) {
	$db = getDB();

	$query = "
			select 	cd_fmlID as cod_familia,
					seql_integID as seql_integrante,
					nm_integ as nome_integrante
				from  tb_integ_fml
				where cd_fmlID         = :cd_fml
				and   cd_est_integ_fml = 1
				and   seql_integID > 1
				order by nm_integ";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_fml', $familia);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosFamiliaAll2() {
	$db = getDB();

	$query = "
			select 	cd_fmlID as cod_familia,
					nm_grp_fmlr as nome_familia
				from tb_fml
				where cd_est_situ_fml between 1 and 4
				order by nm_grp_fmlr";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosIntegranteAll2($familia) {
	$db = getDB();

	$query = "
			select 	cd_fmlID as cod_familia,
					seql_integID as seql_integrante,
					nm_integ as nome_integrante
				from  tb_integ_fml
				where cd_fmlID         = :cd_fml
				and   cd_est_integ_fml between 1 and 2
				order by nm_integ";
	$stmt = $db->prepare($query);
	$stmt->bindValue('cd_fml', $familia);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getMaxDataAcomp($familia) {
	$db = getDB();

	$query = "
			select 	max(dt_acomp), case when max(dt_acomp) is null     THEN DATE_FORMAT('1998-12-31', '%d/%m/%Y')
							            when max(dt_acomp) is not null THEN DATE_FORMAT(max(dt_acomp), '%d/%m/%Y')
							       end	as dt_acomp
			from  tb_acomp_fml							       
			where cd_fmlID = :cd_fml";

	$stmt = $db->prepare($query);
	$stmt->bindValue('cd_fml', $familia);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

function getDadosFamiliaAll3() {
	$db = getDB();

	$query = "
			select 	cd_fmlID as cod_familia,
					nm_grp_fmlr as nome_familia
				from tb_fml
				where cd_est_situ_fml between 1 and 6
				order by nm_grp_fmlr";
	$stmt = $db->prepare($query);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	

// ====================================================== //

// Havia sido retirado por engano
function getDadosFamiliaVFS($grupo_subgrupo) {
	$db = getDB();

	// Recebe a variável como (grupo;subgrupo)
	$grupoSubgrupo = explode(';', $grupo_subgrupo);
	$grupo = $grupoSubgrupo[0];
	$subgrupo = $grupoSubgrupo[1];

	$query = "
			select 	cd_fmlID as cod_familia,
					nm_grp_fmlr as nome_familia
				from tb_fml
				where  cd_fmlID in (select cd_fmlID
									from  tb_vncl_fml_sbgrp
									where cd_grpID   = :cd_grp
									and   cd_sbgrpID = :cd_sbgrp
									and   cd_est_vncl = 1)
				and cd_est_situ_fml = 2
				order by nm_grp_fmlr";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_grp', $grupo);
	$stmt->bindValue(':cd_sbgrp', $subgrupo);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));


	$db = null;
}	

// ====================================================== //

function getDadosItemEvtNeces($evtNeces) {
	$db = getDB();

	$query = "
			select 	cd_itemID as cod_item,
					nm_item as nome_item
			from   tb_item_suprimt
			where  cd_tip_evt_suprimt = :cd_tip_evt_suprimt
			order by nm_item";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':cd_tip_evt_suprimt', $evtNeces);
	$stmt->execute();

	echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

	$db = null;
}	
