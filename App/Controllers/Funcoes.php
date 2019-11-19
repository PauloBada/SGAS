<?php

/*  Nome Programador: Paulo Tarrago Jaques
    Data de criação: 06/07/2019
    Objetivo:  Classe que contém facilidades usadas no sistema
*/

namespace App\Controllers;

// Recursos do sistema
use MF\Controller\Action;
use MF\Model\Container;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Funcoes extends Action {

// ====================================================== //
	
public static function ValidaData($dat){
	// Datas no formato DD/MM/AAAA

	// Data vazia
	if(empty($dat)) {
		return 1;
	}
	
	$data = explode("/","$dat"); 
	$d = $data[0];
	$m = $data[1];
	$y = $data[2];

	// verifica se a data é válida!
	// 1 = true (válida)
	// 0 = false (inválida)
	$res = checkdate($m,$d,$y);
	if ($res == 1){
	   return 1;
	} else {
	   return 0;
	}
}	

// ====================================================== //

	public static function editaData($data) {
		// Para $data igual a 9998-12-31, retorna 01/01/1970

		return date("d/m/Y", strtotime($data));
	}

// ====================================================== //

	public static function validaCPF($cpf) {
		if(empty($cpf)) {
			return 1;
		}
		 
	    $cpf = preg_match('/[0-9]/', $cpf)?$cpf:0;

	    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
	    
	    if (strlen($cpf) != 11) {
	        return 2;
	    }
	    
	    else if ($cpf == '00000000000' || 
	        $cpf == '11111111111' || 
	        $cpf == '22222222222' || 
	        $cpf == '33333333333' || 
	        $cpf == '44444444444' || 
	        $cpf == '55555555555' || 
	        $cpf == '66666666666' || 
	        $cpf == '77777777777' || 
	        $cpf == '88888888888' || 
	        $cpf == '99999999999') {
	        return 3;

	     } else {   
	         
	        for ($t = 9; $t < 11; $t++) {
	             
	            for ($d = 0, $c = 0; $c < $t; $c++) {
	                $d += $cpf{$c} * (($t + 1) - $c);
	            }
	        
	            $d = ((10 * $d) % 11) % 10;
	            if ($cpf{$c} != $d) {
	                return 4;
	            }
	        }
	 
	        return 0;
	    }
    }	//	Fim função validaCPF

// ====================================================== //

	public static function validaCNPJ($cnpj) {

	   // O valor original
	   $cnpj_original = $cnpj;

		// Verifica se um número foi informado
		if(empty($cnpj)) {
			return 1;
		}

		// Elimina possivel mascara
		$cnpj = preg_replace("/[^0-9]/", "", $cnpj);
		$cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
		
		// Verifica se o numero de digitos informados é igual a 14 
		if (strlen($cnpj) != 14) {
			return 2;
		} 
			
		if ($cnpj == '00000000000000' || 
			$cnpj == '11111111111111' || 
			$cnpj == '22222222222222' || 
			$cnpj == '33333333333333' || 
			$cnpj == '44444444444444' || 
			$cnpj == '55555555555555' || 
			$cnpj == '66666666666666' || 
			$cnpj == '77777777777777' || 
			$cnpj == '88888888888888' || 
			$cnpj == '99999999999999') {
			return 3;
	 	} 
	 
	    $primeiro_digito_cnpj = substr($cnpj, 12, 1);    
	   	$segundo_digito_cnpj = substr($cnpj, 13, 1);    

		$j = 5;
		$k = 6;
		$soma1 = 0;
		$soma2 = 0;

	  	// Cálculo do primeiro Dígito
	   	for ($i = 0; $i < 12; $i++) {
	      	$j = $j == 1 ? 9 : $j;

	      	$soma1 += ($cnpj{$i} * $j);

	      	$j--;
	   	}

	   	$digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;

		// Cálculo do segundo Dígito
		for ($i = 0; $i < 13; $i++) {

			$k = $k == 1 ? 9 : $k;

			if ($i == 12) {
				$soma2 += ($digito1 * $k);      

			} else {
				$soma2 += ($cnpj{$i} * $k); 
			}

			$k--;
		}

		$digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

		if ($digito1 != $primeiro_digito_cnpj || $digito2 != $segundo_digito_cnpj) {
			return 4;
		}

		return 0;

    }	//	Fim função validaCNPJ

// ====================================================== //

    public static function enviaEmailCadastro($nome_envia, $email_envia, $senha_envia) {

    	/*
    	echo "NOME ENVIA".$nome_envia;
    	echo "EMAIL ENVIA".$email_envia;
    	echo "SENHA ENVIA".$senha_envia;
    	*/

		$mail = new PHPMailer(); // instancia a classe PHPMailer

		$mail->IsSMTP();

		//configuração do gmail
		$mail->Port = '465'; //porta usada pelo gmail.
		$mail->Host = 'smtp.gmail.com'; 
		$mail->IsHTML(true); 
		$mail->Mailer = 'smtp'; 
		$mail->SMTPSecure = 'ssl';

		// Configurações da conta para envio ===>>> (COLOCAR EM BASE DE DADOS e usar o meu algoritimo)
		//$dados_conta_envio = 'centralvoluntarios@gmail.com';
		//$senha_conta_envio = senha da comunhão;
		
		$dados_conta_envio = 'bada305.ptj@gmail.com';
		$senha_conta_envio = 'Ma!Ba!Gm@trap2';

		//configuração do usuário do gmail
		$mail->SMTPAuth = true; 
		$mail->Username = $dados_conta_envio; 		// usuario gmail.   
		$mail->Password = $senha_conta_envio; 		// senha do email.

		$mail->SingleTo = true; 

		// configuração do email a ser enviado.
		$mensagem = "Senha gerada pelo sistema para login. É Prudente alterá-la!";
		$data_envio = date('d/m/Y');
		$hora_envio = date('H:i:s');

		$corpo_email = "
		    <html>
		        <table width='510' border='0'>
		          <tr>
		           <td>
		           	<tr>
		               	<td width='320'>$mensagem</td>
		            </tr>
		  			<tr>
		            	<td width='500'>Nome:$nome_envia</td>
		            </tr>
		            <tr>
		               	<td width='320'>E-mail:<b>$email_envia</b></td>
		     		</tr>
		            <tr>
		               	<td width='320'>Senha:<b>$senha_envia</b></td>
		     		</tr>
		           </td>
		          </tr>  
		          
		          <tr>
		            <td>Este e-mail foi enviado em <b>$data_envio</b> às <b>$hora_envio</b></td>
		          </tr>
		        </table>
		    </html>
		  ";

		$mail->From = $dados_conta_envio; 
		$mail->FromName = "DPS - Divisao de Promocao Social"; 
		$mail->addAddress($email_envia); 			// email do destinatario.
		$mail->Subject = "Acesso ao Sistema de Gestao Auta de Souza"; 
		$mail->Body = $corpo_email;

		if(!$mail->Send()) {
		    //echo "Erro ao enviar Email:" . $mail->ErrorInfo;
			return 1;
		} else {
			//echo "Email enviado!";
			return 0;
		}

    }	// Fim função enviaEmail

// ====================================================== //

	public static function formatarNumeros ($tipo = "", $string, $size = 10, $tipoData = "") {

    	$string = preg_replace("[^0-9]", "", $string);
    
    	switch ($tipo) {
	        case 'fone':
	            if($size === 10) {
	             $string = preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) \$2-\$3", $string);

		         } else
		         if($size === 11){
		             $string = preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) \$2-\$3", $string);
		         }
		         break;

	        case 'cep':
				$string = preg_replace("/(\d{5})(\d{3})/", "\$1-\$2", $string);				
	         	break;

	        case 'cpf':
	            $string = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $string);
	         	break;

	        case 'cnpj':
	            $string = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $string);
	         	break;

	        case 'rg':
	            $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) . 
	                '.' . substr($string, 5, 3);
	         	
	         	break;

	        // Formata datas e retorna no formato DD/MM/AAAA
	        case 'data':
	        	if ($tipoData == 'DMA') {
	        		if ($size === 8) {
	        			$string = substr($string, 0, 2) . '/' . substr($string, 2, 2) . '/' . substr($string, 4, 4);	
	        		} else {
	        			$string = substr($string, 0, 2) . '/' . substr($string, 3, 2) . '/' . substr($string, 6, 4);	
	        		}
	        	} else {	//AMD
	        		if ($size === 8) {
	        			$string = substr($string, 6, 2) . '/' . substr($string, 4, 2) . '/' . substr($string, 0, 4);		
	        		} else {
	        			$string = substr($string, 8, 2) . '/' . substr($string, 5, 2) . '/' . substr($string, 0, 4);		
	        		}	        		
	        	}
	         	break;

	        default:
	         	$string = 'É necessário definir um tipo(fone, cep, cpg, cnpj, rg)';
	         	break;
    	}

   		return $string;

	}	// Fim função formatarNumeros

// ====================================================== //

	public static function CalculaProximaDataVisita( $data_visita, $semana_atuacao ){
		# Trata data  - a data a ser recebida dever ser no formato Y-m-d (AAAA-MM-DD)
		$data = explode("-", "$data_visita");
		$d = $data[2];
		$m = $data[1];
		$y = $data[0];

		$res = checkdate($m, $d, $y);

		// Data inválida
		if ($res != 1){
			return "9999-99-99";
		}

		# Trata número semana
		if ($semana_atuacao <= 0) {
			return "9999-99-99";
		}

		$data_triagem = $data_visita;
		$ano_triagem = substr($data_triagem, 0, 4);
		$mes_triagem = (int)substr($data_triagem, 5, 2) + 1;

		if ($mes_triagem == 13){
			$mes_triagem = 01;
			$ano_triagem = (int)$ano_triagem + 1;
		}

		if (strlen($mes_triagem) == 1){
			$mes_triagem = (int)"0".$mes_triagem;
		}

		$prim_dia_prox_mes = (string)$ano_triagem.'-'.(string)$mes_triagem.'-01';

		$diaa=substr($prim_dia_prox_mes,8,2);
		$mesa=substr($prim_dia_prox_mes,5,2);
		$anoa=substr($prim_dia_prox_mes,0,4);

		$dia_semana_numero = date("w", mktime(0,0,0,$mesa,$diaa,$anoa));

		$data_trata = new \DateTime($prim_dia_prox_mes);

		$semana_atuacao_grupo = $semana_atuacao;

		switch ($semana_atuacao_grupo) { 
			case 1:
				if ($dia_semana_numero == 0){
					$proximo_dia = 'next sunday';	
					$dias_acres  = -7;
				} else {
					$proximo_dia = 'next sunday';	
					$dias_acres  = 0;
				}
				break;
			case 2:
				if ($dia_semana_numero == 0){
					$proximo_dia = 'next sunday';	
					$dias_acres  = 0;
				} else {
					$proximo_dia = 'next sunday';	
					$dias_acres  = 7;
				}
				break;
			case 3:
				if ($dia_semana_numero == 0){
					$proximo_dia = 'next sunday';	
					$dias_acres  = 7;
				} else {
					$proximo_dia = 'next sunday';	
					$dias_acres  = 14;
				}
				break;
			case 4:
				if ($dia_semana_numero == 0){
					$proximo_dia = 'next sunday';	
					$dias_acres  = 14;
				} else {
					$proximo_dia = 'next sunday';	
					$dias_acres  = 21;
				}
				break;
			case 5:
				if ($dia_semana_numero == 1){
					$proximo_dia = 'next monday';	
					$dias_acres  = -7;
				} else {
					$proximo_dia = 'next monday';	
					$dias_acres  = 0;
				}
				break;
			case 6:
				if ($dia_semana_numero == 1){
					$proximo_dia = 'next monday';	
					$dias_acres  = 0;
				} else {
					$proximo_dia = 'next monday';	
					$dias_acres  = 7;
				}
				break;
			case 7:
				if ($dia_semana_numero == 1){
					$proximo_dia = 'next monday';	
					$dias_acres  = 7;
				} else {
					$proximo_dia = 'next monday';	
					$dias_acres  = 14;
				}
				break;
			case 8:
				if ($dia_semana_numero == 1){
					$proximo_dia = 'next monday';	
					$dias_acres  = 14;
				} else {
					$proximo_dia = 'next monday';	
					$dias_acres  = 21;
				}
				break;
			case 9:
				if ($dia_semana_numero == 2){
					$proximo_dia = 'next tuesday';	
					$dias_acres  = -7;
				} else {
					$proximo_dia = 'next tuesday';	
					$dias_acres  = 0;
				}
				break;
			case 10:
				if ($dia_semana_numero == 2){
					$proximo_dia = 'next tuesday';	
					$dias_acres  = 0;
				} else {
					$proximo_dia = 'next tuesday';	
					$dias_acres  = 7;
				}
				break;
			case 11:
				if ($dia_semana_numero == 2){
					$proximo_dia = 'next tuesday';	
					$dias_acres  = 7;
				} else {
					$proximo_dia = 'next tuesday';	
					$dias_acres  = 14;
				}
				break;
			case 12:
				if ($dia_semana_numero == 2){
					$proximo_dia = 'next tuesday';	
					$dias_acres  = 14;
				} else {
					$proximo_dia = 'next tuesday';	
					$dias_acres  = 21;
				}
				break;
			case 13:
				if ($dia_semana_numero == 3){
					$proximo_dia = 'next wednesday';	
					$dias_acres  = -7;
				} else {
					$proximo_dia = 'next wednesday';	
					$dias_acres  = 0;
				}
				break;
			case 14:
				if ($dia_semana_numero == 3){
					$proximo_dia = 'next wednesday';	
					$dias_acres  = 0;
				} else {
					$proximo_dia = 'next wednesday';	
					$dias_acres  = 7;
				}
				break;
			case 15:
				if ($dia_semana_numero == 3){
					$proximo_dia = 'next wednesday';	
					$dias_acres  = 7;
				} else {
					$proximo_dia = 'next wednesday';	
					$dias_acres  = 14;
				}
				break;
			case 16:
				if ($dia_semana_numero == 3){
					$proximo_dia = 'next wednesday';	
					$dias_acres  = 14;
				} else {
					$proximo_dia = 'next wednesday';	
					$dias_acres  = 21;
				}
				break;
			case 17:
				if ($dia_semana_numero == 4){
					$proximo_dia = 'next thursday';	
					$dias_acres  = -7;
				} else {
					$proximo_dia = 'next thursday';	
					$dias_acres  = 0;
				}
				break;
			case 18:
				if ($dia_semana_numero == 4){
					$proximo_dia = 'next thursday';	
					$dias_acres  = 0;
				} else {
					$proximo_dia = 'next thursday';	
					$dias_acres  = 7;
				}
				break;
			case 19:
				if ($dia_semana_numero == 4){
					$proximo_dia = 'next thursday';	
					$dias_acres  = 7;
				} else {
					$proximo_dia = 'next thursday';	
					$dias_acres  = 14;
				}		
				break;
			case 20:
				if ($dia_semana_numero == 4){
					$proximo_dia = 'next thursday';	
					$dias_acres  = 14;
				} else {
					$proximo_dia = 'next thursday';	
					$dias_acres  = 21;
				}
				break;
			case 21:
				if ($dia_semana_numero == 5){
					$proximo_dia = 'next friday';	
					$dias_acres  = -7;
				} else {
					$proximo_dia = 'next friday';	
					$dias_acres  = 0;
				}
				break;
			case 22:
				if ($dia_semana_numero == 5){
					$proximo_dia = 'next friday';	
					$dias_acres  = 0;
				} else {
					$proximo_dia = 'next friday';	
					$dias_acres  = 7;
				}
				break;
			case 23:
				if ($dia_semana_numero == 5){
					$proximo_dia = 'next friday';	
					$dias_acres  = 7;
				} else {
					$proximo_dia = 'next friday';	
					$dias_acres  = 14;
				}
				break;
			case 24:
				if ($dia_semana_numero == 5){
					$proximo_dia = 'next friday';	
					$dias_acres  = 14;
				} else {
					$proximo_dia = 'next friday';	
					$dias_acres  = 21;
				}
				break;
			case 25:
				if ($dia_semana_numero == 6){
					$proximo_dia = 'next saturday';	
					$dias_acres  = -7;
				} else {
					$proximo_dia = 'next saturday';	
					$dias_acres  = 0;
				}
				break;
			case 26:
				if ($dia_semana_numero == 6){
					$proximo_dia = 'next saturday';	
					$dias_acres  = 0;
				} else {
					$proximo_dia = 'next saturday';	
					$dias_acres  = 7;
				}
				break;
			case 27:
				if ($dia_semana_numero == 6){
					$proximo_dia = 'next saturday';	
					$dias_acres  = 7;
				} else {
					$proximo_dia = 'next saturday';	
					$dias_acres  = 14;
				}
				break;
			case 28:
				if ($dia_semana_numero == 6){
					$proximo_dia = 'next saturday';	
					$dias_acres  = 14;
				} else {
					$proximo_dia = 'next saturday';	
					$dias_acres  = 21;
				}
				break;
			// Igual Otherwise
			default:			
				return $prox_data = "9999-99-99";	
				break;
		}

		$data_trata->modify( $proximo_dia );

		$nextDaysNeed = range(1,1);
		$nextDaysArray = array($data_trata->format('Y-m-d'));

		foreach($nextDaysNeed as $number)
		{
			$nextDaysArray[] = $data_trata->modify('+'.(int)$dias_acres.'day')->format('Y-m-d');
		}

		$prox_data = $nextDaysArray[1];

		return $prox_data;

	}	// Fim função CalculaProximaDataVisita

// ====================================================== //

	public static function orderByArray(&$ary, $clause, $ascending = true) { 
        $clause = str_ireplace('order by', '', $clause); 
        $clause = preg_replace('/\s+/', ' ', $clause); 
        $keys = explode(',', $clause); 
        $dirMap = array('desc' => 1, 'asc' => -1); 
        $def = $ascending ? -1 : 1; 

        $keyAry = array(); 
        $dirAry = array(); 
        foreach($keys as $key) { 
            $key = explode(' ', trim($key)); 
            $keyAry[] = trim($key[0]); 
            if(isset($key[1])) { 
                $dir = strtolower(trim($key[1])); 
                $dirAry[] = $dirMap[$dir] ? $dirMap[$dir] : $def; 
            } else { 
                $dirAry[] = $def; 
            } 
        } 

        $fnBody = ''; 
        for($i = count($keyAry) - 1; $i >= 0; $i--) { 
            $k = $keyAry[$i]; 
            $t = $dirAry[$i]; 
            $f = -1 * $t; 
            $aStr = '$a[\''.$k.'\']'; 
            $bStr = '$b[\''.$k.'\']'; 
            if(strpos($k, '(') !== false) { 
                $aStr = '$a->'.$k; 
                $bStr = '$b->'.$k; 
            } 

            if($fnBody == '') { 
                $fnBody .= "if({$aStr} == {$bStr}) { return 0; }\n"; 
                $fnBody .= "return ({$aStr} < {$bStr}) ? {$t} : {$f};\n";                
            } else { 
                $fnBody = "if({$aStr} == {$bStr}) {\n" . $fnBody; 
                $fnBody .= "}\n"; 
                $fnBody .= "return ({$aStr} < {$bStr}) ? {$t} : {$f};\n"; 
            } 
        } 

        if($fnBody) { 
            $sortFn = create_function('$a,$b', $fnBody); 
            usort($ary, $sortFn);        
        } 
    } 	// Fim função orderByArray

// ====================================================== //

	public static function CalculaDataPeriodo($dt_entrada, $nr_dma_periodo, $dma_periodo, $avanca_retrocede_periodo) {
		if ($dt_entrada == '') {
			return 1;
		}

		if (!is_numeric($nr_dma_periodo)) {
			return 2;
		}

		if ($dma_periodo != 'D' && $dma_periodo != 'M'&& $dma_periodo != 'Y') {
			return 3;
		}

		if ($avanca_retrocede_periodo != 'A' && $dma_periodo != 'R') {
			return 4;
		}

		$dt_entrada = str_replace('/', '-', $dt_entrada);
		$dt_entrada = new \DateTime($dt_entrada);

		$string_periodo = 'P'.$nr_dma_periodo.$dma_periodo;

		// Para calcular 3 meses de intervalo
		$periodo = new \Dateinterval($string_periodo);

		if ($avanca_retrocede_periodo == 'A') {
			$dt_entrada ->add($periodo);			
		} else {
			$dt_entrada ->sub($periodo);						
		}

		$data_calculada = $dt_entrada->format("d/m/Y");

		return $data_calculada;

    } 	// Fim função CalculaDataPeriodo

}	// Fim Classe 

?>