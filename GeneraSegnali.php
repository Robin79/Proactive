<?php

  define('ROOT_PATH', realpath(__DIR__));
  require_once(ROOT_PATH.'/PHP/Arrest.php'); 
  require_once(ROOT_PATH.'/PHP/dati-cloudant.php');
  require_once(ROOT_PATH.'/PHP/DB_Manager.php');
  

  /*
   * 1) Uso lo script OCTAVE per generare 4 segnali sintetici
   *    ognuno da 300.000 sample salvandoli in S1.dat, S2.dat, S3.dat, S4.dat
   */
/*  exec("octave --silent ./OCTAVE/GenerateTestSignal.m 300000 150000 20000 ./SEGNALI/S1.dat");
  exec("octave --silent ./OCTAVE/GenerateTestSignal.m 300000 150000 20000 ./SEGNALI/S2.dat");
  exec("octave --silent ./OCTAVE/GenerateTestSignal.m 300000 150000 20000 ./SEGNALI/S3.dat");
  exec("octave --silent ./OCTAVE/GenerateTestSignal.m 300000 150000 20000 ./SEGNALI/S4.dat");
  exit();
 */ 
  /*
   * 2) Inserisco i dati nel DB MYSQL presente su Hostinger
   *    Utilizzando la libreria ArrestDB
   */
  
  $DB = new DB_Manager();
  
  if($DB->connect()==false) {
 	                      echo("Errore di connessione al DB");
                              exit(-1);
                            }
                            
  $DB->Insert_raw_data_correlation("CLUSTER1","SENSORE_1","1446389379100","S1.dat");
  $DB->Insert_raw_data_correlation("CLUSTER1","SENSORE_2","1446389379200","S1.dat");
  $DB->Insert_raw_data_correlation("CLUSTER1","SENSORE_3","1446389379300","S1.dat");
//  $DB->Insert_raw_data_correlation("CLUSTER1","SENSORE_4","1437138405400","S1.dat");
//  $DB->Insert_raw_data_correlation("CLUSTER1","SENSORE_5","1437138405500","S1.dat");
  
  $DB->disconnect();
  
  echo("OK\r\n");
  
?>
