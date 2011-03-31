<?php
require_once(ROOT.DS.MAIN.DS.'config'.DS.'db.php');
class Sql_query {
    protected $_dbHandle;
    protected $_result;
	protected $_table;
    /** Connects to database **/
    function __construct($table) {
			$this->_table = $table;
			$this->_dbHandle = DB_DEVELOPMENT ? mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) : @mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
			mysql_set_charset('utf8',$this->_dbHandle);
        	if ($this->_dbHandle != false) {
        		if (@mysql_select_db(DB_NAME, $this->_dbHandle)) {	
					$this->_connected = true;
       		        return true;
       		    }
       		    else {
       		        return false;
        	   	}
       		}
       		else {
       	    	return false;
        	}
    }

    /** Disconnects from database **/
    function disconnect() {
        if (@mysql_close($this->_dbHandle) != false) {
            return true;
        }  
		else {
            return false;
        }
    }

    function selectAll() {
    	$query = 'select * from `'.$this->_table.'`';
    	return $this->query($query);
	}
	
	function selectWhere($col, $val){
		$query = 'select * from `'.$this->_table.'` where `'.$col.'` = \''.mysql_real_escape_string($val).'\'';
    	return $this->query($query, 1);
	}
	
	function numRows($col, $val){
		$query = 'select * from `'.$this->_table.'` where `'.$col.'` = \''.mysql_real_escape_string($val).'\'';
		$result = mysql_query($query, $this->_dbHandle);
		if(DB_DEVELOPMENT)  echo mysql_errno($this->_dbHandle),' : ',mysql_error($this->_dbHandle), '\n';
		return mysql_num_rows($result);
		
	}
	
    /** Custom SQL Query **/
	//BE CAREFUL WHEN USING THIS FUNCTION DIRECTLY IT DOESN'T ESCAPE STRING FOR ITSELF
	function simpleQuery($query){
		$this->_result = mysql_query($query, $this->_dbHandle);
		if(DB_DEVELOPMENT)  echo mysql_errno($this->_dbHandle),' : ',mysql_error($this->_dbHandle), '\n';
	}
	
	function query($query, $singleResult = 0) {

		$this->_result = mysql_query($query, $this->_dbHandle);
		
		
		if (preg_match("/select/i",$query)) {
		$result = array();
		$table = array();
		$field = array();
		$tempResults = array();
		$numOfFields = mysql_num_fields($this->_result);
		for ($i = 0; $i < $numOfFields; ++$i) {
		    array_push($table,mysql_field_table($this->_result, $i));
		    array_push($field,mysql_field_name($this->_result, $i));
		}
			while ($row = mysql_fetch_row($this->_result)) {
				for ($i = 0;$i < $numOfFields; ++$i) {
					$table[$i] = trim(ucfirst($table[$i]),"s");
					$tempResults[$table[$i]][$field[$i]] = $row[$i];
				}
				if ($singleResult == 1) {
		 			mysql_free_result($this->_result);
					return $tempResults;
				}
				array_push($result,$tempResults);
			}
			mysql_free_result($this->_result);
			return($result);
		}
		if(DB_DEVELOPMENT)  echo mysql_errno($this->_dbHandle),' : ',mysql_error($this->_dbHandle), '\n';
	}

    /** Get number of rows **/
    function getNumRows() {
		return mysql_num_rows($this->_result);
		
    }
	/** get comment database **/
	function getComments($post,$filter){
		$string = ' AND ( ';
		if($post === 'ALL'){
			$post = '';
			$string = '';
		}
		else{
			$post = ' `post_slug`=\''.$post.'\'';
		}
		if(is_array($filter)){
			$i=1;
			$count = count($filter);
			while(list($k,$v) = each($filter)){
				$string .= '`approved`=\''.$v.'\'';
				if($i < $count){
					$string .= ' OR ';
				}
				$i+=1;
			}
			if($post !== ''){
				$string .=' )';
			}
		}
		else if($filter === 'ALL'){
			$string = '';
		}
		else{
			die('AN INCORRECT PARAMETER WAS PASSED TO Sql_query->getComments()');
		}
		$this->_result = mysql_query('SELECT * FROM `comments` WHERE'.$post.$string, $this->_dbHandle);
		$field = array();
		$result = array();
		$children = array();
		$numOfFields = mysql_num_fields($this->_result);
		for ($i = 0; $i < $numOfFields; ++$i){
			array_push($field,mysql_field_name($this->_result, $i));
		}
		$int=0;
		while ($row = mysql_fetch_row($this->_result)){
			$i=0;
			while($i < $numOfFields){
				$result[$int][$field[$i]] = $row[$i];
				$i+=1;
			}
			$int+=1;
		}
		mysql_free_result($this->_result);
		return $result;
	}
    /** Free resources allocated by a query **/
    function freeResult() {
        mysql_free_result($this->_result);
		if(DB_DEVELOPMENT)  echo mysql_errno($this->_dbHandle),' : ',mysql_error($this->_dbHandle), '\n';
    }

    /** Get error string **/
    function getError() {
        return mysql_error($this->_dbHandle);
		if(DB_DEVELOPMENT)  echo mysql_errno($this->_dbHandle),' : ',mysql_error($this->_dbHandle), '\n';
    }
}