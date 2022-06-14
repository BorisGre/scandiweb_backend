<?php

include_once('mysql.php');
	
	$mysql = new MySQL('locahost', 'root', '', 'Scandiweb');
	
	// get all posts
	try{
		$posts = $mysql->get('posts');
		print_r($posts);
		echo $mysql->num_rows(); // number of rows returned
	}catch(Exception $e){
		echo 'Caught exception: ', $e->getMessage();
	}

?>   