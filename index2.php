<!DOCTYPE html>
<html>
<head>
<title>Protsendipõhine otsja</title>
<link rel="stylesheet" type="text/css" href="style.css">	
</head>
<body>

<div id="wrapper">
	<a href="index.php">Sõnade põhine otsija</a><br>
	<a href="pahalased.txt">Kurjategijad</a>
	<a href="noise_words.txt">Müra sõnad</a><br>
	<br><strong>Protsendipõhine otsija</strong>

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
	//trim double white spaces
	$userentrytrimmed = preg_replace('/\s+/', ' ', $userentrytrimmed);
	//trim beginning and end whitespace
	$userentrytrimmed = trim($userentrytrimmed);
	
	//partial match alerts variable declaration
	$alert_partialmatch ="";
	$alert_remotematch ="";
	
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
			
			//sort arrays
			sort($criminalarray);
			sort($userentryarray);
			
			//convert arrays to strings for comparison
			$criminaltrimmed = implode(" ",$criminalarray);
			$userentrytrimmed = implode(" ",$userentryarray);
			
			//compare strings
			similar_text($criminaltrimmed, $userentrytrimmed, $percent);
			$percent = round($percent);
			
			if($percent == 100){
				$alert_totalmatch = "Täielik kokkulangevus:<br><strong>$criminal</strong> ($percent%), rahaülekanne keelatud";
			}else if($percent >=85){
				$alert_partialmatch .= "<strong>$criminal</strong> ($percent%)<br>";
			}else if($percent >=70){
				$alert_remotematch .="<strong>$criminal</strong> ($percent%)<br>";
			}			
		}
	}else{
		$alert_entername = "Palun sisesta nimi";
	}
}
?>

<!-- input form -->
<form action="index2.php" method="POST" id="form">
	<input type="text" name="userentry" placeholder="Sisesta nimi" value="<?php if (!empty($userentry)){ echo $userentry;} ?>"/>
	<input type="submit" value="Kontrolli" class="button"/>
</form>

<!-- result alerts -->
<?php
//total criminal match found
if(!empty($alert_totalmatch)){
	echo "$alert_totalmatch<br><br>";
	echo "<body style='background-color:#f5c6c6'>";
}
//partial criminal match found
if(!empty($alert_partialmatch)){
	echo "Osaline kokkulangevus järgnevate nimedega:<br>$alert_partialmatch<br>";
	echo "<body style='background-color:#f7f9c8'>";
}
//remote criminal match found
if(!empty($alert_remotematch)){
	echo "Kaudne kokkulangevus järgnevate nimedega:<br>$alert_remotematch<br>";
	echo "<body style='background-color:#f7f9c8'>";
}
//criminal not found
if(!empty($userentrytrimmed) && empty($alert_totalmatch) && empty($alert_partialmatch) && empty($alert_remotematch)){
	echo "Rahaülekanne lubatud";
}
//enter name alert
if(!empty($alert_entername)){
	echo $alert_entername;
}
?>

<!-- end wrapper div -->
</div>
</body>
</html>