<?php

class connexion extends PDO{
	
	 public function __construct($dsn, $user, $password){
		parent::__construct($dsn, $user, $password);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
		$this->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
	}
	
	public function prepare($sql, $options=NULL){
        $statement = parent::prepare($sql);
        if(strpos(strtoupper($sql), 'SELECT') === 0){
            $statement->setFetchMode(PDO::FETCH_ASSOC);
        }
        return $statement;
    }

}

?>