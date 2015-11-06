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

  while(1)
  {  
  
   $array_noise_level = $DB->GetListNoiseLevel();
//  print_r($array_noise_level);
                            
  //Elaboro il vettore

  for($i=0;$i<count($array_noise_level);$i++)
  {
     if($array_noise_level[$i]["flag"]==1){
     $max_level = max($array_noise_level[$i]["noise"]);
     $max_index = in_array($max_level,$array_noise_level);
     $DB->UpdateNoiseLevel(
                         $array_noise_level[$i]["id"],
                         $array_noise_level[$i]["freq"][$max_index],
                         $max_level,
                         $array_noise_level[$i]["flag"]
                        ); 
     }
     else
     {
      $DB->UpdateNoiseLevel(
                         $array_noise_level[$i]["id"],
                         0,
                         0,
                         -1
                        ); 
     } //fine else
                     
     echo("\n\r Elaborata una riga");    
   }//fine for
  }//fine while true
  
  $DB->disconnect();
  
  echo("OK\r\n");
  
?>
