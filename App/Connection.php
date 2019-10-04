<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 11/07/2019                       
   Objetivo:  Faz a conexão com o banco de dados do sistema
*/

namespace App;

class Connection {
	public static function getDb() {
		try {
			// Deverá ser colocado o endereço do servidor da comunhão
			$conn = new \PDO(
				"mysql:host=localhost;dbname=sgas;charset=utf8",
				"root",
				""
			);

			return $conn;

		} catch (\PDOException $e) {
			// Tratar o erro
			echo "Erro: ".$e;
			return null;
		}
	}
}

?>