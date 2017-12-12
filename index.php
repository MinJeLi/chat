<?php

function Chat($p){

	$text = "";
	$user_name = "";
	$token = "";
	$SetToken = "unTpcPseo4Zv";

	if (isset($p["text"])) {
			$text = strval($p["text"]);
	}
	
	if (isset($p["token"])) {
			$token = strval($p["token"]);
	}
	
	if (isset($p["username"])) {
			$user_name = strval($p["username"]);
	}
	
	if ($token == $SetToken) {
		
		$sentence = explode(" ", $text, 4);
		$question = "";
		$answer = "";
		if ($sentence[1] == "#學習" or $sentence[1] == "#learn") {
			
			$question = trim($sentence[2]);
			$answer = trim($sentence[3]);
			if ($sentence[2] and $sentence[3]){
				SQLInsertRespones($user_name, $question, $answer);			
				echo "{\"text\": \"@" . $user_name. " 學習了，感謝教學 :grinning:\"}";
			}else{
				echo "{\"text\": \"@" . $user_name. " 我沒學會，可以再教我嗎？(#學習(learn) 你的問題 我的回答) >///<\"}";
			}
			
		} elseif ($sentence[1]) {

			$PeopleQuestion = trim($sentence[1]);
			
			if ($sentence[1] == "叫雞"){
				$Link = SQLReturnJPGLink();
				echo "{\"text\": \"" . 這位滿意嗎？. "\" , \"file_url\": \"".addslashes($Link)."\"}";		
				SQLRecord($user_name, $Link);
			}
			
			$SQLAnswer = SQLReadBrain($PeopleQuestion);
			if($SQLAnswer) {
				echo "{\"text\": \"@" .$user_name." ".addslashes($SQLAnswer). "\"}";
			} else {
				echo "{\"text\": \"@" . $user_name. " 不好意思，這方面我不太懂，可以教我嗎？(#學習(learn) 你的問題 我的回答)\"}";
			}
		} else {
			echo "{\"text\": \"@" .$user_name. " 什麼事？\"}";
		}
	}	
}

function SQLInsertRespones($user,$question,$respond){

	include 'SQLInfo.php';

	// Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
	
	$sql = "INSERT INTO Brian (teacher, incoming, respond)
			VALUE('$user','$question','$respond') ON DUPLICATE KEY UPDATE
			respond='$respond'";
			
	mysqli_query($conn, $sql);
	
//	if (mysqli_query($conn, $sql)){
//		echo "success";
//	} else {
//		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
//	}
		
	mysqli_close($conn);
	
}

function SQLReadBrain($question){
	
	include 'SQLInfo.php';

//	Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

   if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
	}
	
	$sql = "SELECT respond FROM Brian WHERE incoming='$question'";
			
	$result = mysqli_query($conn, $sql);
		
	if (mysqli_num_rows($result) > 0){
		$row = mysqli_fetch_array($result);
		mysqli_close($conn);
		return $row["respond"];
	}else{
		mysqli_close($conn);
		return NULL;
	}
		
}

function SQLReturnJPGLink(){

	include 'SQLInfo.php';
		
	// Create connection
    $conn = mysqli_connect($servername, $username, $password, "Girls");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
	
	$sql = "SELECT ImagesLink FROM ImgLink ORDER BY RAND() LIMIT 1";

	$result = mysqli_query($conn, $sql);
	
	if ($result){
		$row = mysqli_fetch_array($result, MYSQLI_NUM);
//		printf ("%s \n", $row[0]);
	} else {
		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
	}
	
	mysqli_free_result($result);	
	mysqli_close($conn);
	return $row[0];
	
}

function SQLRecord($Caller, $Link){

	include 'SQLInfo.php';

	// Create connection
    $conn = mysqli_connect($servername, $username, $password, "Girls");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }	
	
	$sql = "INSERT INTO  Request(caller, RespondLink) 
			VALUES ('$Caller', '$Link')";
	
	mysqli_query($conn, $sql);

//	if (mysqli_query($conn, $sql)){
//		echo "New record created successfully";
//	} else {
//		echo "Error: " . $sql . "<br>" . mysqli_error($conn);
//	} 

	mysqli_close($conn);
	return 0;
		
}

Chat($_POST) 

?>