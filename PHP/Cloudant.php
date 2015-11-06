<?php

class Cloudant
{
	private $ch;
	
	public function GET($baseUrl,$db,$id)
	{
		$ch = curl_init();				
		curl_setopt($ch, CURLOPT_URL, $baseUrl.$db."/".$id);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'f4b9b016-11ce-4957-b975-383353063907-bluemix:9c9452cae2c4014f8f4915e67913aafd23b1acd6cc61e66f33c56d7f5827c97a');		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-type: application/json',
		'Accept: */*'
				));
		
		$response = curl_exec($ch);
		
		curl_close($ch);
		
		return($response);
				
	}
 	
	public function POST($baseUrl,$db,$doc)
	{
		$ch = curl_init();
				
		curl_setopt($ch, CURLOPT_URL, $baseUrl.$db);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'f4b9b016-11ce-4957-b975-383353063907-bluemix:9c9452cae2c4014f8f4915e67913aafd23b1acd6cc61e66f33c56d7f5827c97a');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($doc));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-type: application/json',
		'Accept: */*'
				));
		
		$response = curl_exec($ch);			
		
		curl_close($ch);
		
		return($response);
	
	}
	
	public function PUT($baseUrl,$db,$id,$doc)
	{
		$ch = curl_init();	    
		curl_setopt($ch, CURLOPT_URL, $baseUrl.$db."/".$id);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'f4b9b016-11ce-4957-b975-383353063907-bluemix:9c9452cae2c4014f8f4915e67913aafd23b1acd6cc61e66f33c56d7f5827c97a');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($doc));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-type: application/json',
		'Accept: */*'
				));
	
		$response = curl_exec($ch);
	
		curl_close($ch);
	
		return($response);
	
	}
	
	
	public function PUT_ATTACHMENT($baseUrl,$db,$id,$rev,$nome,$file)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $baseUrl.$db."/".$id."/".$nome."?rev=".$rev);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'f4b9b016-11ce-4957-b975-383353063907-bluemix:9c9452cae2c4014f8f4915e67913aafd23b1acd6cc61e66f33c56d7f5827c97a');
		//curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-type: binary/octet-stream',
		'Accept: */*'
				));
	
		$response = curl_exec($ch);
	
		curl_close($ch);
	
		return($response);
	
	}
	
	public function PUT_IMAGE($baseUrl,$db,$id,$rev,$nome,$file)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $baseUrl.$db."/".$id."/".$nome."?rev=".$rev);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'f4b9b016-11ce-4957-b975-383353063907-bluemix:9c9452cae2c4014f8f4915e67913aafd23b1acd6cc61e66f33c56d7f5827c97a');
		curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file));		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-type: image/jpeg',
		'Accept: */*'
				));
	
		$response = curl_exec($ch);
	
		curl_close($ch);
	
		return($response);
	
	}
	
	
	
	
	
	
}

?>