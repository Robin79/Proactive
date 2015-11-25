<?php

class DB_Manager
{
	private $db_host = '127.0.0.1';
	private $db_user = 'root';
	private $db_pass = 'root';
	private $db_name = 'proactive';
	private $con;

	
    /*****************************************************************************************************************/	
    public function connect()
	{	
		if(!$this->con)
		{	
			$this->mysqli = new mysqli($this->db_host,$this->db_user,$this->db_pass,$this->db_name);
			if (mysqli_connect_errno()) {
				echo "Errore in connessione al DBMS: ".mysqli_connect_error();				
				return false;				
			}
			$this->con = true;
			return true;
		}
		else 
		{
			return true;
		}	
	}
    /*****************************************************************************************************************/	
	public function disconnect()
	{
		$this->mysqli->close();
	}	
    /*****************************************************************************************************************/
	public function Insert_raw_data_correlation($CLU,$SENS,$TIME,$FAIL)
	{		
		//$fh = fopen('./SEGNALI/'.$FAIL, "r");
		//$data = addslashes(fread($fh, filesize('./SEGNALI/'.$FAIL)));
		//fclose($fh);
		
		$data = $this->mysqli->real_escape_string(fread(fopen('./SEGNALI/'.$FAIL, "r"), filesize('./SEGNALI/'.$FAIL)));
		
		$query = "INSERT INTO raw_data_correlation(sensors_id_cluster,sensors_id_sensor,timestamp_misura,timestamp_insert,num_sample,f_sampling,data) ";
		$query .= "VALUES ('".$CLU."','".$SENS."','".$TIME."','".$TIME."',300000,5000,'".$data."');";
		//echo($query."\r\n"); file_get_contents('./SEGNALI/'.$FAIL)
		$result = $this->mysqli->query($query);
		echo($result);
		
		
	}
	/*****************************************************************************************************************/
        public function Insert_noise_level($CLU,$SENS,$TIME,$LEVEL,$FREQ)
        {

            $level = $this->mysqli->real_escape_string(fread(fopen('./SEGNALI/'.$LEVEL, "r"), filesize('./SEGNALI/'.$LEVEL)));
            $freq = $this->mysqli->real_escape_string(fread(fopen('./SEGNALI/'.$FREQ, "r"), filesize('./SEGNALI/'.$FREQ)));

            $query = "INSERT INTO noise_logger(sensors_id_cluster,sensors_id_sensor,timestamp_misura,timestamp_insert,noise_level,freq) ";    
            $query .=" VALUES('".$CLU."','".$SENS."','".$TIME."','".$TIME."','".$level."','".$freq."')";

            $result = $this->mysqli->query($query);
            echo($result);

        }
        /**********************************************************************/  
        public function GetListNoiseLevel()
        {
 
            $query = "SELECT id_insert,noise_level,freq FROM noise_logger  ";    
            $query .=" WHERE fprocess  = 0 ";

            $result = $this->mysqli->query($query);

            $array = array();
            if ($result->num_rows>0)
            {
              $i = 0;
	      while($row = $result->fetch_array(MYSQLI_NUM))
	      {   
                $lung_noise = strlen($row[1]);
                $lung_freq  = strlen($row[2]);
                if($lung_noise==$lung_freq)
                {
                  $noise = array();
                  $freq = array();
                  for($j=0;$j<($lung_noise)/2;$j++)
                  {                  
                    $noise[$j]  = unpack("v",substr($row[1],$j*2,2))[1];
                    $freq[$j]   = unpack("v",substr($row[2],$j*2,2))[1];
                  }
		  $array[$i] =  array("id"=>$row[0],"noise"=>$noise,"freq"=>$freq,"flag"=>1);
	          $i = $i + 1;
                }
                else
                {
		  $array[$i] =  array("id"=>$row[0],"noise"=>0,"freq"=>0,"flag"=>-1);
	          $i = $i + 1;
                }  
	     }
            }
            
            return($array);

        }
       /************************************************************************/
        public function SegnalaElaborati($id)
        {
            $query = "UPDATE raw_data_correlation SET elaborati=1"; 
            $query .= " WHERE id_insert = ".$id;

            $result = $this->mysqli->query($query);

        }
       /************************************************************************/
        public function UpdateNoiseLevel($id,$freq,$level,$flag)
        {
            $query = "UPDATE noise_logger SET noise_level_max=".$level; 
            $query .=", freq_max= ".$freq.",fprocess=".$flag." WHERE id_insert = ".$id;

            $result = $this->mysqli->query($query);
        }
 	/*****************************************************************************************************************/
        public function getFlagUpdate()
        {
          $query = " SELECT FlagUpdate FROM clusters WHERE id_cluster = 'CLUSTER1'";
          $result = $this->mysqli->query($query);
          $row = $result->fetch_array(MYSQLI_NUM);
          return(intval($row[0]));
        
        }
/*****************************************************************************/
        public function setFlagUpdate()
        {
          $query = " UPDATE clusters SET  FlagUpdate=0 WHERE id_cluster = 'CLUSTER1'";
          $result = $this->mysqli->query($query);        
        }
/*****************************************************************************/
	public function getNewData()
	{
		$array = array();
		$query = " SELECT id_insert,sensors_id_cluster,sensors_id_sensor,timestamp_misura,timestamp_insert,num_sample,f_sampling,data,tab_len,data_len FROM raw_data_correlation WHERE elaborati=0";
		
		$result = $this->mysqli->query($query);
			
		if($result->num_rows >0)
		{
			
			while($row = $result->fetch_array(MYSQLI_NUM))
			{
				$array[$row[0]] = array($row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9]);				
			}
			
		}
		return $array;
	
	}
	/*****************************************************************************************************************/
	public function getNewData_Light()
	{
		$array = array();
		$query = " SELECT id_insert,sensors_id_cluster,sensors_id_sensor,timestamp_misura,data_len,f_sampling,doc_id FROM raw_data_correlation WHERE elaborati=0";
	
		$result = $this->mysqli->query($query);
		$k=0;	
		if($result->num_rows >0)
		{
				
			while($row = $result->fetch_array(MYSQLI_NUM))
			{
				$array[$k] = array($row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[0]);
				$k++;
			}
				
		}
		return $array;
	
	}
	/*****************************************************************************************************************/
	public function setUpdateDocID($k,$docid)
	{
		$query = " UPDATE raw_data_correlation SET doc_id = '".$docid."' WHERE id_insert = ".$k;
		$result = $this->mysqli->query($query);
    }
    /*****************************************************************************************************************/
	public function getStrisciata($key)
	{
		
		$query = " SELECT data FROM raw_data_correlation WHERE id_insert=".$key;
		
		$result = $this->mysqli->query($query);
			
		if($result->num_rows >0)
		{
			$array[$row[0]] = array($row[1],$row[2],$row[3],$row[4],$row[5],$row[6]);						
		}
	}
	/*****************************************************************************************************************/
	public function getSensorCorrelation()
	{
		$array = array();
		$query = " SELECT id_cluster,id_sensor1,id_sensor2 FROM sensor_correlation";
		
		$result = $this->mysqli->query($query);
		$i=0;	
		if($result->num_rows >0)
		{
				
			while($row = $result->fetch_array(MYSQLI_NUM))
			{
			    $array[$i] = array($row[0],$row[1],$row[2]);
			    $i++;
			}
				
		}
		return $array;
		
	}
	/*****************************************************************************************************************/
	public function getCloseRequest()
	{
		$array = array();
		$query = " SELECT uid_tessera,timestamp,id_richiesta,id_rfid,delta_credito,delta_decremento,online,tbase,modalita,send_mail,tempo_insaponamento,tempo_effettivo FROM close_request ORDER BY timestamp ASC LIMIT 1";
		$result = $this->mysqli->query($query);
			
		if($result->num_rows >0)
		{
			$row = $result->fetch_array(MYSQLI_NUM);
			$array =  array('uid'=>$row[0],
					        'timestamp'=>$row[1],
					        'id_richiesta'=>$row[2],
					        'id_rfid'=>$row[3],
					        'delta_credito'=>$row[4],
					        'delta_decremento'=>$row[5],
					        'online'=>$row[6],
					        'tbase'=>$row[7],
					        'modalita'=>$row[8],
					        'send_mail'=>$row[9],
					        'tempo_insaponamento'=>$row[10],
					        'tempo_effettivo'=>$row[11]
			               );
		}
		return $array;
				
	}
	/*****************************************************************************************************************/
	public function UpdateCloseRequest($id)
	{
		$query = " UPDATE close_request SET online = 0 WHERE id_richiesta = ".$id;
		$result = $this->mysqli->query($query);
	}
	/*****************************************************************************************************************/
	public function getReboot_Log()
	{
		$array = array();
		$query = " SELECT timestamp,causa,idreboot_log FROM reboot_log ORDER BY timestamp ASC LIMIT 1";
		$result = $this->mysqli->query($query);
			
		if($result->num_rows >0)
		{
			$row = $result->fetch_array(MYSQLI_NUM);
			$array =  array($row[0],$row[1],$row[2]);				
		}
		return $array;
	
	}
    /*****************************************************************************************************************/	
	
	/*****************************************************************************************************************/	
	public function getStandAloneTime($ID_RFID)
	{		
		$query = " SELECT modalita,tbase,delta_decremento,pausa_insaponamento,decremento_colpo_secco,tempo_erogazione_colpo_secco,decremento_abilitazione,durata_abilitazione FROM configuration WHERE id_rfid=".$ID_RFID;
		$result = $this->mysqli->query($query);
			
		$row = $result->fetch_array(MYSQLI_NUM);
		$array = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7]);
		return $array;
	}
    /*****************************************************************************************************************/
	public function putResponse($resp)
	{
		$query  = " INSERT INTO response(id_response,id_rfid,modalita,consenso,credito_allineato,tbase,pausa_insaponamento,online,delta_decremento,credito_lampeggio,timeout_send_mail) ";
		$query .= " VALUES (".$resp->{'id_response'};
		$query .= ",".$resp->{'id_rfid'};
		$query .= ",".$resp->{'modalita'};
		$query .= ",".$resp->{'consenso'};
		$query .= ",".$resp->{'credito_allineato'};
		$query .= ",".$resp->{'tbase'};
		$query .= ",".$resp->{'pausa_insaponamento'};
		$query .= ",".$resp->{'on_line'};
		$query .= ",".$resp->{'delta_decremento'};
		$query .= ",".$resp->{'credito_lampeggio'};
		$query .= ",".$resp->{'timeout_send_mail'}.");";
	    	
		try {
		      $result = $this->mysqli->query($query);		     
		    } catch (mysqli_sql_exception $e) {
		    	return -1;
		    }
	   return 1;
	   
	}
	/*****************************************************************************************************************/
	public function deleteRequest($req)
	{
		$query  = " DELETE FROM open_request WHERE id_richiesta =";		
		$query .= $req["id_richiesta"]." AND id_rfid = ".$req["id_rfid"]." AND TIMESTAMP = ".$req["timestamp"];
		
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		return 1;
	}	
	/*****************************************************************************************************************/
	public function deleteRebootLog($id)
	{
		$query  = " DELETE FROM reboot_log WHERE idreboot_log = ".$id;				
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		return 1;
	}
	/*****************************************************************************************************************/
	public function deleteCloseRequest($req)
	{
		$query  = " DELETE FROM close_request WHERE id_richiesta =";
		$query .= $req["id_richiesta"]." AND id_rfid = ".$req["id_rfid"]." AND TIMESTAMP = ".$req["timestamp"];
	
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		return 1;
	}		
	/*****************************************************************************************************************/
	public function InsertKeep($nome_Thread,$time,$pid,$uart)
	{
		$query  = " INSERT INTO keep_alive(thread_name,uart_id,timestamp,pid) ";
		$query  .= " VALUES('".$nome_Thread."',".$uart.",".$time.",".$pid.") ";
		
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		return 1;
		
	}
	/*****************************************************************************************************************/
	public function UpdateKeep($nome_Thread,$time,$pid,$uart)
	{
		$time = shell_exec("date +%s");
		$query  = " SELECT COUNT(*) FROM keep_alive WHERE thread_name = '".$nome_Thread."'";
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		$row = $result->fetch_array(MYSQLI_NUM);
		 
		if($row[0]=="1")
		{  		  
		  $query  = " UPDATE keep_alive SET timestamp = ".$time;
		  $query  .= " WHERE thread_name = '".$nome_Thread."'";
		}
		else
		{
          $query  = " INSERT INTO keep_alive(thread_name,uart_id,timestamp,pid) ";
	  	  $query  .= " VALUES('".$nome_Thread."',".$uart.",".$time.",".$pid.") ";
	    }
					   
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		return 1;
	
	}
	/*****************************************************************************************************************/
	public function SetOnline($val)
	{
		$query  = " UPDATE configuration SET flag_standalone = ".$val;
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		return 1;
	}
	/*****************************************************************************************************************/
	public function isOnline()
	{
		$query  = "SELECT flag_standalone FROM configuration WHERE uart_id = 1";
	    $result = $this->mysqli->query($query);
	    
	    $i = 0;
	    if($result->num_rows >0)
	    {
	    	
	    	$row = $result->fetch_array(MYSQLI_NUM);
	    	$i = $row[0];	    		
	    }
	    
	    if (intval($i)==0) return(1);
	    else return(0);
	}
	/*****************************************************************************************************************/
	public function Get_Keep_Alive()
	{
		$query  = "SELECT thread_name,uart_id,timestamp,pid FROM keep_alive";
		try {
			$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
			return -1;
		}
		
		$array = array();
		if($result->num_rows >0)
		{
			$i = 0;
			while($row = $result->fetch_array(MYSQLI_NUM))
			{
				$array[$i] =  array($row[0],$row[1],$row[2],$row[3]);
				$i = $i + 1;
			}
			
		}
		return $array;
			
		
	}
	/*****************************************************************************************************************/	
	public function insertClose($resp,$uid,$scalo)
	{		
		$query  = " INSERT INTO close_request(uid_tessera,timestamp,id_richiesta,id_rfid,delta_credito,delta_decremento,online,tbase,modalita,send_mail) ";
		$query .= " VALUES ( ".$uid;
		$query .= ",UNIX_TIMESTAMP()";
		$query .= ",".$resp->{'id_response'};;
		$query .= ",".$resp->{'id_rfid'};
		$query .= ",".$scalo;
		$query .= ",".$scalo;
		$query .= ",".$resp->{'on_line'};
		$query .= ",".$resp->{'tbase'};
		$query .= ",".$resp->{'modalita'};
		$query .= ",0 );";

		try {
		$result = $this->mysqli->query($query);
		} catch (mysqli_sql_exception $e) {
		return -1;
		}
		return 1;
	}
	
	
}

?>
