<?php

  define('ROOT_PATH', realpath(__DIR__));
  require_once(ROOT_PATH.'/PHP/Arrest.php'); 
  require_once(ROOT_PATH.'/PHP/dati-cloudant.php');
  require_once(ROOT_PATH.'/PHP/DB_Manager.php');
  
  
  $DB = new DB_Manager();
  
  if($DB->connect()==false) {
 	                     echo("Errore di connessione al DB");
                             exit(-1);
                            }
  //Vettore delle frequenze
  $freq = fopen('SEGNALI/Freq1.dat','wb');
  for($i=0;$i<60;$i++)
  {
    fwrite($freq,pack("v",rand(0,1000))); 
  } 
  fclose($freq);
  //Vettore dei Decibel
  $freq = fopen('SEGNALI/Decibel1.dat','wb');
  for($i=0;$i<60;$i++)
  {  
    fwrite($freq,pack("v",rand(0,90))); 
  }
  fclose($freq);
  
  $tempo  = time()*1000;
  $clu =  "CLUSTER_1";
  $sens = "SENSORE_".rand(1,4);                             
  $DB->Insert_noise_level($clu,$sens,$tempo,"Decibel1.dat","Freq1.dat");
  
  $DB->disconnect();
  
  echo("OK\r\n");
  
?>
