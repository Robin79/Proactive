<?php

class Arrest
{
	private $ch;
	
	public function INSERT($uri,$num,$data)
	{		
		$ch = curl_init();				
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch,CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		//curl_setopt($ch, CURLOPT_USERPWD, 'darietto79:darietto79');		
						
		$response = curl_exec($ch);		
		curl_close($ch);		
		return($response);				
	}
	
	public function READ($baseUrl)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $baseUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
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
				
		curl_setopt($ch, CURLOPT_URL, $baseUrl."/".$db);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'darietto79:darietto79');
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
		curl_setopt($ch, CURLOPT_USERPWD, 'darietto79:darietto79');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($doc));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-type: application/json',
		'Accept: */*'
				));
	
		$response = curl_exec($ch);
	
		curl_close($ch);
	
		return($response);
	
	}
	
	
	public function PUT_ATTACHMENT($baseUrl,$db,$id,$rev,$file)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $baseUrl.$db."/".$id."/image.dat?rev=".$rev);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, 'darietto79:darietto79');
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