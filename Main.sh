#!/bin/sh

# TO DO
# Polling fino a quando non mi segnala che il burst di dati sono arrivati

#Faccio partire script PHP per il recupero dati 
/usr/bin/php Retrieve_Data.php

#Faccio partire script Octave
octave --silent Octave.m ./SEGNALI/S1.dat ./SEGNALI/S2.dat ./SEGNALI/Corr.dat

#Faccio partire script PHP per il salvataggio su Cloudant

