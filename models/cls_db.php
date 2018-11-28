<?php

/*
Created by Francis Albores 9/19/13

*/

class DB{

	public $table;
	private $_config;

	public function __construct($param = array()){
		if($param==array()){
			$param = array(
		        'host'=>DBHOST,
		        'user'=> DBUSER,
		        'password'=>DBPASS,
		        'database'=>DBNAME
		    );
		}      
		$this->_config = $param;
		$this->connect();
    } 

    private function set_default(&$table){
		$table = ($table=="") ? $this->table : $table;
	}
	private function implodeTableName($data=array()){
        $arr = array();
    	foreach($data as $key=>$value):
            $arr[] = $key."=:".  $key;
        endforeach;	        
        return implode(",",$arr);
    }
    private function setValue(&$stmt, $data){
    	foreach($data as $key=>$val){ // BINDING WHERE VALUES
			if(is_array($val))
				$val = json_encode($val);
			$stmt->bindValue(":$key", "$val");
		}
    }
    function connect(){
        extract($this->_config);
        if(isset($host) && isset($user) && isset($password) && isset($database)){
        	$status = new PDO('mysql:host='.$host.';dbname='.$database, $user, $password);
			$status->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $status;
        }        
        else{
        	return false;
        }
    }
    public function query($query="",$bind_array){
    	$con = $this->connect();
    	$stmt = $con->prepare($query);
    	foreach($bind_array as $key=>$val){ // BINDING WHERE VALUES
			$stmt->bindValue(":$key", "$val");
		}
		$stmt->execute();
		return $stmt;
    }
	public function select($data="", $arr_where = array(), $isSingle=false, $table="", $isSearch=false, $orderby="", $order="ASC", $start=0, $end=ITEM_DISPLAY_COUNT){
		$this->set_default($table);

		$con = $this->connect();
		// =============================[ BUILDING WHERE STRING FOR PREPARE ]=============
		if(count($arr_where)>0){
			$where = "";
			foreach($arr_where as $key=>$val){ 
				if($isSearch)
					$where .= "$key LIKE :$key AND ";
				else
					$where .= "$key=:$key AND ";
			}

			$where = substr($where, 0,-4);
			$q = "SELECT $data FROM $table WHERE $where";
			if($orderby!=""){
				$q .= " ORDER BY $orderby $order";
			}
			if(!$isSingle){
				$q.=" LIMIT $start, $end";	
			}	
			$stmt = $con->prepare($q);

			foreach($arr_where as $key=>$val){ // BINDING WHERE VALUES
				if($isSearch)
					$stmt->bindValue(":$key", "$val%");
				else
					$stmt->bindValue(":$key", "$val");
			}
		}
		else{
			$stmt = $con->prepare("SELECT $data FROM $table LIMIT $start, $end");
		}		
		// =============================[ end ]=============
	    $stmt->execute();
		
		$array = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if(count($array)>0){
			if($isSingle) return $array[0];
			else return $array;
		}
		else{
			return false;
		}
	}

	public function select_SecondMethod($table, $rows = '*', $join = null, $where = null, $order = null, $limit = null){
		$con = $this->connect();

		// Create query from the variables passed to the function
		$q = 'SELECT '.$rows.' FROM '.$table;
		if($join != null){
			$q .= ' JOIN '.$join;
		}
        if($where != null){
        	$q .= ' WHERE '.$where;
		}
        if($order != null){
            $q .= ' ORDER BY '.$order;
		}
        if($limit != null){
            $q .= ' LIMIT '.$limit;
        }
        
        $stmt = $con->prepare($q);
		$stmt->execute();
		
		$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $array;
	}
    public function update($data ="", $ID, $CLIENT_ID){
		
		
		$con = $this->connect();
		
		$q = 'UPDATE tbl_fingerprint SET finger_data = "'.$data.'" 
			  WHERE ID="'.$ID.'" AND client_id = "'.$CLIENT_ID.'"';

		$stmt = $con->prepare($q);
        $stmt->execute();

        if($stmt->rowCount()>0){
     		return true;
        }
        else{
        	return false;
        }
	}
	public function save($data = array(), $arr_where=array(), $table = "",$return="count"){

		$this->set_default($table);
		$con = $this->connect();
		
		$q = (count($arr_where)>0) ? "Update " : "Insert into ";
        $q .= $table." set ".$this->implodeTableName($data);

		// =============================[ BUILDING WHERE STRING FOR PREPARE ]=============
		if(count($arr_where)>0){
			$where = " where ";
			foreach($arr_where as $key=>$val){ 
				$where .= "$key=:$key AND ";
			}
			$where = substr($where, 0,-4);		
			$q.=$where;
		}	
		// =============================[ end ]=============
		$stmt = $con->prepare($q);
        $this->setValue($stmt, $data);

		foreach($arr_where as $key=>$val){ // BINDING WHERE VALUES
			$stmt->bindValue(":$key", "$val");
		}
		
        $stmt->execute();

        if($stmt->rowCount()>0){
     		if($return=="count")
     			return $stmt->rowCount();
	     	elseif($return=="lastInsertId")
        		return $con->lastInsertId('ID');
        }
        else{
        	return false;
        }
        
	}	// insert func()
	
	public function delete($id = "", $column = "id" ,$table = ""){	
		$this->set_default($table);
		$con = $this->connect();
		//echo "DELETE FROM $table WHERE id = :id";
		$stmt = $con->prepare("DELETE FROM $table WHERE $column = :id");
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		if($stmt->rowCount()>0)
        	return $stmt->rowCount();
        else
        	return false;
	}// delete func()
	
	// USED FOR SUSUMAMA DB DESIGN ONLY
	function get_json_value($val, $parent_ids, $isSingleJson=false){		
		$temp = json_decode($parent_ids, true);
		$temp = $temp[$val];		
		if($isSingleJson)
			$data = $this->select("area_name", array("ID"=>$parent_ids),true);
		else
			$data = $this->select("area_name", array("ID"=>$temp),true);
		return $data['area_name'];
	}
	function get_parent_ids($office_id){
		$data = $this->select("parent_ids", array("ID"=>$office_id),true);
		return $data['parent_ids'];	
	}
	function get_max($table, $column, $where=array()){
    $con = $this->connect();
    $q = "SELECT MAX($column) as 'max' FROM $table";
    if(count($where)>0){
      $where = " where ";
      foreach($where as $key=>$val){ 
        $where .= "$key=:$key AND ";
      }
      $where = substr($where, 0,-4);    
      $q.=$where;
    } 
    $stmt = $con->prepare($q);
    $stmt->execute();
    $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($array)>0){
      foreach ($array as $key => $value) {
         return $value['max'];
      }
      
    }else{
      return -1;
    }
  }
}