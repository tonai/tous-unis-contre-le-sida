<?php

	class BaseDeDonnees {
	
		function BaseDeDonnees() {
		}
		
		
		
		function connexion() {
			include("admin/config.php");
			$this->mysqli = mysqli_connect($db['hostName'], $db['userName'], $db['password']);
			mysqli_select_db($this->mysqli, $db['dataBase']);
		}
		
		
		
		function deconnexion() {
			mysqli_close($this->mysqli);
		}
		
		
		
		function select($select, $from, $where, $orderBy) {
			return mysqli_query($this->mysqli, "SELECT '$select' FROM $from WHERE $where' ORDER BY $orderBy");
		}
		
	}

?>