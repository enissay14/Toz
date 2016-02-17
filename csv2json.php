<?php 

	
	if(($handle = fopen('csv/venues_items.csv', 'r')) !== false){
		
		$venues_items = array() ;
 		//loop through the file line-by-line
 		while(($data = fgetcsv($handle,1000, ";")) !== false)
 		{
 			var_dump($data);
			array_push($venues_items, $data);
			
 			unset($data);
 		}
		
		fclose($handle);
		
		file_put_contents('venues_items.json', json_encode($venues_items));
	}

?>
