<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 11/07/2019                       
   Objetivo:  Faz o link para acssso aos bancos de dados do sistema
*/

namespace MF\Model;

use App\Connection;

class Container {
	
	// Retornará a tabela (modelo) instanciada e com conexão estabelecida
	public static function getModel($model) {

		$classe = DIRECTORY_SEPARATOR."App".DIRECTORY_SEPARATOR."Models".DIRECTORY_SEPARATOR.ucfirst($model);

		// Instância de conexão
		$conn = Connection::getDb();

		return new $classe($conn);
	}
}

?>