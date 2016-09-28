<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">	
</head>
<body>

<div id="wrapper">
	<a href="pahalased.txt">Kurjategijad</a>
	<a href="noise_words.txt">Müra sõnad</a>

<?php
if(isset($_POST['userentry'])){
	//get criminals
	$criminals = file('pahalased.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);	
	//get noise words
	$noise_words = file('noise_words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	//get user entry
	$userentry = $_POST['userentry'];
	//convert user entry to lowercase
	$userentrylowercase = mb_strtolower($userentry, 'utf-8');
	//trim punctuation
	$userentrytrimmed = preg_replace("#[[:punct:]]#", ' ', $userentrylowercase);
	//trim noise words
	$userentrytrimmed = preg_replace('/\b('.implode('|',$noise_words).')\b/','',$userentrytrimmed);

	if(!empty($userentrytrimmed)){
		//control against each criminal name
		foreach($criminals as $criminal){
			
			//convert to lowercase
			$criminallower = mb_strtolower($criminal, 'utf-8');
			//trimpunctuation
			$criminaltrimmed = preg_replace("#[[:punct:]]#", ' ', $criminallower);
			
			//split into array		
			$criminalarray = preg_split('/ +/', $criminaltrimmed, -1, PREG_SPLIT_NO_EMPTY);
			$userentryarray = preg_split('/ +/', $userentrytrimmed, -1, PREG_SPLIT_NO_EMPTY);
			
			//sort arrays for comparison
			sort($criminalarray);
			sort($userentryarray);
			
			//compare arrays
			if ($criminalarray==$userentryarray){
				echo "<body style='background-color:#f5c6c6'>";
				$alert = "Kokkulangevus: <strong>$criminal</strong>, rahaülekanne keelatud";
				break;
				
			}
		}
	}else{
		$alert2 = "Palun sisesta nimi";
	}
}
?>

<!-- input form -->
<form action="index.php" method="POST" id="form">
	<input type="text" name="userentry" placeholder="Sisesta nimi" value="<?php if (!empty($userentry)){ echo $userentry;} ?>"/>
	<input type="submit" value="Kontrolli" class="button"/>
</form>

<!-- result alerts -->
<?php
//criminal found
if(!empty($alert)){
	echo $alert;
}
//criminal not found
if(!empty($userentrytrimmed) && empty($alert)){
	echo "Rahaülekanne lubatud";
}
//enter name alert
if(!empty($alert2)){
	echo $alert2;
}
?>

<!-- end wrapper div -->
</div>
</body>
</html>