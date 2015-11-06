<?php

  define('ROOT_PATH', realpath(__DIR__));
  require_once(ROOT_PATH.'/PHP/Cloudant.php');
  require_once(ROOT_PATH.'/PHP/dati-cloudant.php');
  require_once(ROOT_PATH.'/PHP/DB_Manager.php');

  
  $DB = new DB_Manager();
  if($DB->connect()==false) {
  	    echo("Errore di connessione al DB");
    	exit(-1);
  }
  
  $CLO = new Cloudant();
    
  /*******************************************************************************
   1) Estraggo i nuovi dati dal DB Mysql e li metto in documenti opportuni
  ********************************************************************************/ 
  $new_data = $DB->getNewData();
  $jj=0;
  $date = date_create();
  foreach($new_data as $key=>$value)
  {
	  	$data = $value[6];	  	
	  	/* 1   : COPIO I DATI RAW IN UN FILE PER SUCCESSIVA ELABORAZIONE */
                /*      IL FILE GENERATO SARA' SEMPRE  "SIG_IDSENSOR_TIMESTAMP.dat" */ 
	  	$sig1 = fopen("SEGNALI/SIG_".$value[1]."_".$value[2].".dat", 'wb');
	  	fwrite($sig1, $data);
	  	fclose($sig1);

                /*2    : Calcolo i valori di noise Logger */
              
//	  	echo("octave --silent ./OCTAVE/NoiseLogger.m ./SEGNALI/SIG_".$value[1]."_".$value[2].".dat 1 ".$value[5]." > /dev/null");
	  	exec("octave --silent ./OCTAVE/NoiseLogger.m ./SEGNALI/SIG_".$value[1]."_".$value[2].".dat 1 ".$value[5]." > /dev/null");

  	        $new_doc = new stdClass();	  	
	  	$new_doc->type                 = "misura";
	  	$new_doc->id_cluster           = $value[0];
	  	$new_doc->sensor               = $value[1];
                date_timestamp_set($date, intval($value[2])/1000);
                $new_doc->dataora              = date_format($date,'Y-m-d H:i:s');
	  	$new_doc->timestamp_misura     = $value[2];
	  	$new_doc->timestamp_insert     = $value[3];
	  	$new_doc->num_sample           = $value[4];
	  	$new_doc->f_sampling           = $value[5];

	  	$sig1 = fopen("./frequenze_max.dat", 'rb');
	  	$singolo = fread($sig1, 4);
                $new_doc->freq_max = intval(unpack("l",$singolo));
	  	fclose($sig1);
                /*****************************************************************/  
	  	$sig1 = fopen("./minimi_min.dat", 'rb');
	  	$singolo = fread($sig1, 4);
                $new_doc->minimi_min = intval(unpack("l",$singolo));
	  	fclose($sig1);
                /*****************************************************************/  
                $new_doc->frequenze = array();
	  	$handle = fopen("./frequenze.dat", 'rb');
                while (!feof($handle))
                {
                    $singolo = fread($handle,4);
                    array_push($new_doc->frequenze,intval(unpack("l",$singolo)));
                } 
	  	fclose($handle);
                /*****************************************************************/  
                $new_doc->minimi = array();
	  	$handle = fopen("./minimi.dat", 'rb');
                while (!feof($handle))
                {
                    $singolo = fread($handle,4);
                    array_push($new_doc->minimi,intval(unpack("l",$singolo)));
                } 
	  	fclose($handle);
                /*****************************************************************/ 
                
  	  	
                /*** 3- Inserisco il nuovo documento in cloudant ***/
	  	$ris = $CLO->POST(baseUrl, db, $new_doc);
	  	echo("\r\n".$ris);
	  	$doc = json_decode($ris);
	  	
                /*** Inserisco l' _id generato da Cloudant e lo metto nel DB mysql 
                     cosÃ¬ conosco per ogni misura il relativo documento creato sul db NOSQL
                ***/
	  	$DB->setUpdateDocID($key,$doc->id);
	  	
	  	/* 1-  ATTACHMENT DATI RAW - estratti dal db */ 
	  	$ris = $CLO->PUT_ATTACHMENT(baseUrl, db,$doc->id,$doc->rev,"dati.dat",$data);
	  	echo("\r\nPUT ATTACHMENT 1 ==> ".$ris);
	  	$doc = json_decode($ris);	  	
	  	
	  	
          
	  	/* 3° : LANCIO OCTAVE CHE GENERA PLOT DATI 
                        che genera  plot.jpg
                */
	  	exec("octave --silent ./OCTAVE/PlotSignal.m ./SEGNALI/SIG_".$value[1]."_".$value[2].".dat ".$value[4]." ".$value[5]." > /dev/null");
	  	
	  	$data = $value[6];
	  	$ris = $CLO->PUT_IMAGE(baseUrl, db, $doc->id, $doc->rev, "dati.jpg", "dati.jpg");
	  	echo("\r\nPUT ATTACHMENT 2 ==> ".$ris);
	  	$doc = json_decode($ris);
	  	// 3° : LANCIO OCTAVE CHE GENERA PLOT FFT
	  	exec("octave --silent ./OCTAVE/PlotFFT.m  ./SEGNALI/SIG_".$value[1]."_".$value[2].".dat ".$value[4]." ".$value[5]." > /dev/null");
	  	 
	  	$data = $value[6];
	  	$ris = $CLO->PUT_IMAGE(baseUrl, db, $doc->id, $doc->rev, "fft_dati.jpg", "fft_dati.jpg");
	  	echo("\r\nPUT ATTACHMENT 3 ==> ".$ris);
	  	
	  	exec("rm dati.jpg");
	  	exec("rm fft_dati.jpg");
	  	exec("rm frequenze.dat");
	  	exec("rm minimi.dat");
	  	exec("rm frequenze_max.dat");
	  	exec("rm minimi_min.dat");
  	       
  	        $jj++;
          //      $DB->SegnalaElaborati($key);
 }
 

 
         /* Estraggo solo i dati da elaborare */
	 $new_data = $DB->getNewData_Light();
        
	 //$sens_corr = $DB->getSensorCorrelation();

	 for($k=0;$k<count($new_data)-1;$k++)
	 {
                /* Flaggo la riga come elaborata */
                $DB->SegnalaElaborati($new_data[$k][6]);

   	        for($t=$k+1;$t<count($new_data);$t++)
	 	{
	 		/*
	 		* $value[0] = ID_CLUSTER
	 		* $value[1] = ID_SENSOR
	 		* $value[2] = TIMESTAMP
	 		* $value[3] = NUM_SAMPLE
	 		* $value[4] = F_SAMPLING
	 		* $value[5] = DOC_ID
	 		* $value[6] = ID_INSERT
	 		*/
	 		//foreach($sens_corr as $key=>$value)
	 		//{
//	 		   if( (strcmp($new_data[$k][1],$value[1])==0) && (strcmp($new_data[$t][1],$value[2])==0) && (strcmp($new_data[$k][0],$value[0])==0) && (strcmp($new_data[$t][0],$value[0])==0) )
                           if ( strcmp($new_data[$k][1],$new_data[$t][1]) != 0 ) 
	 		   {
	 		   	  exec("octave --silent ./OCTAVE/PlotCorrS1S2.m  ./SEGNALI/SIG_".$new_data[$k][1]."_".$new_data[$k][2].".dat ./SEGNALI/SIG_".$new_data[$t][1]."_".$new_data[$t][2].".dat SIG_CORR.dat CORR.jpg ".$new_data[$k][3]." ".$new_data[$k][4]." > /dev/null");
	 		   	  //GENERA SIG_CORR.dat CORR.jpg
	 		   	  $new_doc = new stdClass();	 		   	  
	 		   	  $new_doc->type                 = "correlazione";
	 		   	  $new_doc->misura1              = $new_data[$k][5];
	 		   	  $new_doc->misura2              = $new_data[$t][5];
                                  $new_doc->cluster1             = $new_data[$k][0];
                                  $new_doc->sensor1              = $new_data[$k][1];
                                  $new_doc->cluster2             = $new_data[$t][0];
                                  $new_doc->sensor2              = $new_data[$t][1];

	 		   	  $ris = $CLO->POST(baseUrl, db, $new_doc);
 	 		   	  echo("\r\n".$ris);
	 		   	  $doc = json_decode($ris);
	 		   	  
	 		   	  $data = file_get_contents("SIG_CORR.dat");
	 		   	  $ris = $CLO->PUT_ATTACHMENT(baseUrl, db,$doc->id,$doc->rev,"dati.dat",$data);
	 		   	  echo("\r\nPUT ATTACHMENT 1 ==> ".$ris);
	 		   	  $doc = json_decode($ris);
	 		   	  
	 		   	  $ris = $CLO->PUT_IMAGE(baseUrl, db, $doc->id, $doc->rev, "CORR.jpg", "ImgCorrS1S2.jpg");
	 		   	  echo("\r\nPUT ATTACHMENT 2 ==> ".$ris);
	 		   	  $doc = json_decode($ris);
	 		   	  
	 		   	  
	 		   }	
/*
	 		   if( (strcmp($new_data[$t][1],$value[1])==0) && (strcmp($new_data[$k][1],$value[2])==0) && (strcmp($new_data[$k][0],$value[0])==0) && (strcmp($new_data[$t][0],$value[0])==0))
	 		   {
	 		   	  exec("octave --silent ./OCTAVE/PlotCorrS1S2.m  ./SEGNALI/SIG_".$new_data[$t][1]."_".$new_data[$t][2].".dat ./SEGNALI/SIG_".$new_data[$k][1]."_".$new_data[$k][2].".dat SIG_CORR.dat CORR.jpg ".$new_data[$t][3]." ".$new_data[$t][4]." > /dev/null");
	 		      //GENERA SIG_CORR.dat CORR.jpg
	 		   	  //GENERA SIG_CORR.dat CORR.jpg
	 		   	  $new_doc = new stdClass();
	 		   	  $new_doc->type                 = "correlazione";
	 		   	  $new_doc->misura1              = $new_data[$k][5];
	 		   	  $new_doc->misura2              = $new_data[$t][5];
	 		   	  $ris = $CLO->POST(baseUrl, db, $new_doc);
	 		   	  echo("\r\n".$ris);
	 		   	  $doc = json_decode($ris);
	 		   	  
	 		   	  $data = file_get_contents("SIG_CORR.dat");
	 		   	  $ris = $CLO->PUT_ATTACHMENT(baseUrl, db,$doc->id,$doc->rev,"dati.dat",$data);
	 		   	  echo("\r\nPUT ATTACHMENT 1 ==> ".$ris);
	 		   	  $doc = json_decode($ris);
	 		   	  
	 		   	  $ris = $CLO->PUT_IMAGE(baseUrl, db, $doc->id, $doc->rev, "CORR.jpg", "ImgCorrS1S2.jpg");
	 		   	  echo("\r\nPUT ATTACHMENT 2 ==> ".$ris);
	 		   	  $doc = json_decode($ris);
	 		   }
*/	
	 		//}
	 		
	 		
	 	}
	 }
		  
 
  
  
  
  
  
  
  
  
  
  
  
  
  /*
  $ARR = new Arrest();  
  
  $val = $ARR->READ(Proactive_URI."log_receive_data/");
  echo($val);
  $log = json_decode($val); 
  while(is_object($log))
  {
  	echo("Attesa!!!\r\n");
  	sleep(10);
  	$val = $ARR->READ(Proactive_URI."log_receive_data/");
  	echo($val);
  	$log = json_decode($val);
  }  
  
  echo("Attesa FINITA!!!\r\n");
  echo("ID -->".$log[0]->idlog_receive_data."\n\n");
  
  $id = $log[0]->idlog_receive_data;
  */ 
  /*******************************************************************************
   2) Recupero una strisciata
  ********************************************************************************/
//	  echo("FACCIO LA MIGRAZIONE MYSQL --> CLOUDANT");
//	  $ch = curl_init();
//	  curl_setopt($ch, CURLOPT_URL, "http://proactive.esy.es/Send_to_Cloudant.php?id_log=".$id);
//	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//	  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//	  'Content-type: application/json',
//	  'Accept: */*'
//	  		));
//	  $response = curl_exec($ch);
//	  curl_close($ch);
//	  json_decode($response);
	  
  //echo(Proactive_URI."raw_data_correlation/");
  //$risp = $ARR->READ(Proactive_URI."raw_data_correlation/id_sensor/SENS1");
  //echo($risp);
  //$lettura = json_decode($risp);
  //print_r($lettura);
  /*
  $sig1 = fopen('SEGNALI/S1.dat', 'wb');
  for($i=0;$i<150000;$i++)
  {fwrite($sig1, pack("V",4));}
  for($i=0;$i<150000;$i++)
  {fwrite($sig1, pack("V",66));}
  fclose($sig1);
  */
  /*$sig1 = fopen('SEGNALI/S1.dat', 'rb');
  for($i=0;$i<6;$i++)
  {
  	$val = fread($sig1,4);
    $num = unpack("V",$val);
  	echo($num[1]." ");
  }
  fclose($sig1);
  */
  /*
  $sig1 = fopen('SEGNALI/S1.dat', 'rb');
  for($i=0;$i<30000;$i++)
  {$val = fread($sig1,4);
  $num = unpack("V",$val);
  //if($num[1]!=0)
  	echo($num[1]." ");
  }
  */
  
  
  /*
  $sig1 = fopen('SEGNALI/S2.dat', 'wb');
  for($i=0;$i<150000;$i++)
  {fwrite($sig1, pack("V",0));}
  for($i=0;$i<150000;$i++)
  {fwrite($sig1, pack("V",15));}
  fclose($sig1);
  */
  
  /********************************************************
   2) S1.dat e S2.dat ==> OCTAVE ==> S1S2_Corr.dat
      (TODO: script Octave)  
  ********************************************************/
  /*
   $sig1 = fopen('SEGNALI/S1S2_Corr.dat', 'wb');
   for($i=0;$i<150000;$i++)
   {fwrite($sig1, pack("V",0));}
   for($i=0;$i<150000;$i++)
   {fwrite($sig1, pack("V",$i));}
   for($i=150000;$i>0;$i--)
   {fwrite($sig1, pack("V",$i));}  
   for($i=0;$i<150000;$i++)
   {fwrite($sig1, pack("V",0));}
   fclose($sig1);     
  
  
  $sig1 = fopen('SEGNALI/signal1.dat', 'rb');
  for($i=0;$i<30000;$i++)
  {$val = fread($sig1,4);
   $num = unpack("V",$val);
   if($num[1]!=0)
   echo($num[1]." ");  
  }
$sig1 = fopen('SEGNALI/signal1.dat', 'rb');
  for($i=0;$i<30000;$i++)
  {$val = fread($sig1,4);
   $num = unpack("V",$val);
   if($num[1]!=0)
   echo($num[1]." ");
  }
*/
  
  //exec("octave --silent ./OCTAVE/Octave.m ./SEGNALI/S1.dat ./SEGNALI/S2.dat ./SEGNALI/Corr.dat");
  
  
  /***********************************************************************/
  /*
  $CLO = new Cloudant();
  $id ="Sens1";
  $ris = $CLO->GET(baseUrl, db, $id);
  $doc = json_decode($ris);
  $ris = $CLO->PUT_ATTACHMENT(baseUrl, db, $id,$doc->_rev,"./IMAGES/myCorr2.jpg");
  
  echo($ris);
  */
?>
  
