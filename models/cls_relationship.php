<?php
class Relationship extends DB{
	function __construct(){
		parent::__construct(); 
		$this->table = "tbl_relationship";
	}
	function unlink(){
		$_data = $_POST;
		
		/*$data = $this->delete($_data['rid']);
		if($data==false)
			echo "error";
		else
			echo "success";
		exit();*/
		$query = "DELETE FROM $this->table WHERE ID = :id AND (base_client = :cid OR relation_to LIKE :rid)";	     
	    $bind_array	= array("id"=>$_data['id'],"cid"=>$_data['cid'], "rid"=>$_data['rid']);
	    $stmt = $this->query($query,$bind_array);
	    exit();
	}
	/*function select($where){
		$end = ITEM_DISPLAY_COUNT;
	    $query = "SELECT c.client_type, c.record_number, r.type
			    FROM $this->table as r JOIN tbl_client as c
			    $where";
	     
	    $bind_array= array("rid"=>$rid);
	    $stmt = $this->query($query,$bind_array);
	    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}*/
	function sortByOrder($a, $b) {
	    return $a['type'] - $b['type'];
	}
	function checkRelationshipFirst($cid){
		global $type;

		$r_type = $type->get_all('relationship');
		
		$end = ITEM_DISPLAY_COUNT;
	    $query = "SELECT c.client_type, c.record_number,r.* 
			    FROM $this->table as r JOIN tbl_client as c
			    WHERE r.base_client = :cid AND r.base_client = c.ID";
	     
	    $bind_array= array("cid"=>$cid);
	    $stmt = $this->query($query,$bind_array);
	    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    
	    if($data){

			usort($data, array($this,'sortByOrder'));
			$prev_type = "";
			$prev_key = 0;
			$ctr=0;
	      	foreach($data as $key=>$d){
	      		$current_type = $r_type['value'][$d['type']];
	      		$data[$key]['type'] = $current_type;
	      		if($current_type == $prev_type){
	      			$data[$prev_key]['type'] .= "#".$ctr;
	      			$ctr++;
	      			$data[$key]['type'] .= "#".$ctr;      			
	      		}
	      		else{
	      			$prev_type = $current_type;
	      			$prev_key = $key;
	      			$ctr++;
	      		}
	      }
	      return $data;
	    }
	    else return false;
	}
	function checkRelationshipSecond($rid){

	    $end = ITEM_DISPLAY_COUNT;
	    $query = "SELECT c.client_type, c.record_number, r.type, r.ID
			    FROM $this->table as r JOIN tbl_client as c
			    WHERE r.relation_to LIKE :rid AND r.base_client = c.ID";
	     
	    $bind_array= array("rid"=>$rid);
	    $stmt = $this->query($query,$bind_array);
	    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    // convert type
	    $child_count =  0;
	    $child_1_key=   0;
	    $sibli_1_key=   0;
	    $sibli_count =  0;
	    
	    if($data){
	    	//print_r($data); exit();
	      	foreach($data as $key=>$d){
		        switch($d['type']){
		          case 0 :
		          case 1 : 
		          	$data[$key]['type']=	"Child";  break;		          
		          case 2 :
		            if($d['client_type'] == 'Female') $data[$key]['type'] =	"Mother";
		            elseif($d['client_type'] == 'Male') $data[$key]['type'] =	"Father"; 
		            break;
		          
		          case 3 : //sibling
		            $sibli_count++; 
		            if($sibli_count==1)
		              $sibli_1_key = $key;

		          	$data[$key]['type']	=	"Sibling #".$sibli_count;
		            break;

		          case (4 && $d['client_type'] == 'Female') : 
		            $data[$key]['type']	= 	"Wife"; break;
		          case (4 && $d['client_type'] == 'Male') : 
		            $data[$key]['type']	=	"Husband"; break;  
		        }
	      	}
		      
		    if($child_count==1){
		        $data[$child_1_key]['type']="Child";
		    }
		    if($sibli_count==1){
		        $data[$sibli_1_key]['type']="Sibling";
		    }

	      return $data;
	    }
	    else return false;
	}
	public function removeRelationship(){

	}
}