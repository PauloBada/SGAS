<?php

/* Nome Programador: Paulo Tarrago Jaques
   Data de criação: 11/07/2019                       
   Objetivo:  Faz a conexão com os bancos de dados do sistema
*/

namespace MF\Model;

abstract class Model {

	protected $db;

	public function __construct(\PDO $pdodb) {
		$this->db = $pdodb;
	}
}

?>