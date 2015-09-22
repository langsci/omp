<?php

header("Content-Type: text/html; charset=UTF-8");

error_reporting(E_ALL);
ini_set('display_errors', true);

// Get cURL resource
$curlGet = curl_init();

$headers = array(
	'Accept: application/json',
    'Content-Type: application/json;charset=utf8'
);

$url = $_GET["url"];

// Set some options - we are passing in a useragent too here
curl_setopt_array($curlGet, array(
   	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_ENCODING => "UTF-8",
	CURLOPT_HTTPHEADER=>$headers, 
	CURLOPT_URL => 'https://hypothes.is/api/search?uri='.$url.'&limit=400'
));

// Send the get request + get response
$respGet = curl_exec($curlGet);
curl_close($curlGet);
$resultsArrayExt = json_decode($respGet,true);
$resultsArray = $resultsArrayExt['rows'];

$tags = array();
$texts = array();
$user = array();
$created = array();
$references = array();
$uris = array();
$documents = array();
$docrefs = array();
$getItem = array();
$ids = array();
$targets = array();

for ($i=0; $i<sizeof($resultsArray); $i++) {

	$item = $resultsArray[$i];

	$ids[] = $item['id'];
	$user[] = $item['user'];
	$created[] = $item['created'];
	$targets[] = $item['target'];
	if (in_array("references",array_keys($item)) ) {
		$references[] = $item['references'];
	} else {
		$references[] = array();
	}
	if (in_array("document",array_keys($item)) ) {
		$documents[] = $item['document'];
	} else {
		$documents[] = array();
	}
	if (in_array("document",array_keys($item))&&in_array("references",array_keys($item) )) {
		$docrefs[] = $item['id'];
	} else {
		$docrefs[] = array();
	}
	$tags[] = $item['tags'];
	$texts[] = $item['text'];
	$uris[] = $item['uri'];
}



//  Analyse der Uri
$countUris = array_count_values($uris);
arsort($countUris);

// Analyse der Tags
$allTags = array();
for ($i=0;$i<sizeof($tags); $i++) {
	$currentTag = $tags[$i];
	for ($ii=0;$ii<sizeof($currentTag); $ii++) {
		$allTags[] = $currentTag[$ii];			
	}		
}
$countTags = array_count_values($allTags);
arsort($countTags);

// Analyse Datum der Erstellung (created)
asort($created);

// Analyse References
$numberOfCommentsWithReferences = 0;
for ($i=0;$i<sizeof($references); $i++) {
	if (!empty($references[$i])) {
		$numberOfCommentsWithReferences++;
	}
} 

// Analyse Document
$numberOfCommentsWithDocument = 0;
for ($i=0;$i<sizeof($documents); $i++) {
	if (!empty($documents[$i])) {
		$numberOfCommentsWithDocument++;
	}
} 

// Analyse docrefs
$numberOfDocrefs = 0;
for ($i=0;$i<sizeof($docrefs); $i++) {
	if (!empty($docrefs[$i])) {
		$numberOfDocrefs++;
	}
}

// Analyse User
$countUser = array_count_values($user);
arsort($countUser);

// Analyse der Einträge/Texts (doppelte Einträge finden -> Orphans identifizieren)
$countTexts = array_count_values($texts);
arsort($countTexts);
















// ------------------- AUSGABE ------------------------------


//Ausgabe allgemeine Infos
echo '<br><a style="color:#084B8A;"> ------------------------ Daten zu Annotationen mit hypothes.is ---------------------------------</a>';

/*echo "<br><br>Häufigkeit einzelner URIs:<br>";
$keysCountUris = array_keys($countUris);
for ($i=0;$i<sizeof($countUris); $i++) {
	$count = $countUris[$keysCountUris[$i]];
	$spaces = "";
	for ($ii=0;$ii<(10-strlen($count)*2); $ii++) {
		$spaces = $spaces . "&nbsp;";
	}
	echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $countUris[$keysCountUris[$i]] . $spaces .  $keysCountUris[$i];
}*/

echo '<br><br><a style="color:#084B8A;">Dokument:</a>';
$keysCountUris = array_keys($countUris);
for ($i=0;$i<sizeof($countUris); $i++) {
	if (strpos($keysCountUris[$i],"ttp://")>0) {
		echo "&nbsp;&nbsp;" .  $keysCountUris[$i] . "<br>";
	}
}


echo '<br><a style="color:#084B8A;">Anzahl Kommentare insgesamt:</a> ' . sizeof($user);
echo '<br><a style="color:#084B8A;">Davon sind Replies: </a>' . $numberOfCommentsWithReferences;
//echo "<br>Anzahl Kommentare mit Document-Eintrag: " . $numberOfCommentsWithDocument . " von " . sizeof($documents);
//echo "<br>Anzahl Kommentare mit Docref: " . $numberOfDocrefs . " von " . sizeof($documents);
/*
for ($i=0;$i<sizeof($docrefs); $i++) {
	if (!empty($docrefs[$i])) {
		echo "<br>" . $docrefs[$i];
	}
}*/

echo '<br><br><a style="color:#084B8A;">Zeitpunkt erster Kommentar:</a> ' . date('l, F jS Y \a\t g:ia', strtotime($created[sizeof($created)-1]));
echo '<br><a style="color:#084B8A;">Zeitpunkt letzter Kommentar:</a> ' . date('l, F jS Y \a\t g:ia', strtotime($created[0]));


//echo '<br><a style="color:#084B8A;"> ------------------------ Item --------------------------------------------------------</a>';
//print_r($getItem);

// Ausgabe Analyse User
echo '<br><br><a style="color:#084B8A;"> ------------------------ Personen/User -----------------------------------------------------------------------</a>';
echo '<br><br><a style="color:#084B8A;">Haeufigkeit einzelner User:</a><br>';
$keysCountUser = array_keys($countUser);
for ($i=0;$i<sizeof($countUser); $i++) {
	$count = $countUser[$keysCountUser[$i]];
	$spaces = "";
	for ($ii=0;$ii<(10-strlen($count)*2); $ii++) {
		$spaces = $spaces . "&nbsp;";
	}
	$acct = strpos($keysCountUser[$i],":");
	$at   = strpos($keysCountUser[$i],"@");

	$userName = substr($keysCountUser[$i],$acct+1,$at-$acct-1);

	echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $count . $spaces .  $userName;
}

//Ausgabe Analyse Tags
echo '<br><br><a style="color:#084B8A;"> ------------------------ Tags ----------------------------------------------------------------------------------------</a>';
echo '<br><br><a style="color:#084B8A;">Anzahl Tags insgesamt: </a>' . sizeof($allTags) . "<br>";

$keysCountTags = array_keys($countTags);
for ($i=0;$i<sizeof($countTags); $i++) {
	$count = $countTags[$keysCountTags[$i]];
	$spaces = "";
	for ($ii=0;$ii<(10-strlen($count)*2); $ii++) {
		$spaces = $spaces . "&nbsp;";
	}
	echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $count . $spaces .  $keysCountTags[$i];
}

// Doppelte
echo '<br><br><a style="color:#084B8A;"> ------------------------ Doppelte Einträge ------------------------------------------------------------</a>';
echo '<br><br><a style="color:#084B8A;">Einträge, die 2x oder öfter vorkommen mit Verfasser und Tags:</a><br>';
$count = 0;
$keysCountTexts = array_keys($countTexts);

for ($i=0;$i<sizeof($countTexts); $i++) {

	if ($countTexts[$keysCountTexts[$i]]>1) {
		$count++;
		echo "<br>" . $count . ") "; //. ") Vorkommen: " . $countTexts[$keysCountTexts[$i]];
		echo "TEXT: " . $keysCountTexts[$i] . "<br>"; 
		for ($ii=0;$ii<sizeof($texts); $ii++) {
		
			$doubleTag = $tags[$ii];
			$tagString = "";
			for ($iii=0;$iii<sizeof($doubleTag); $iii++) {
				if ($iii==0) {
					$tagString = $tagString . $doubleTag[$iii];
				} else {
					$tagString = $tagString . " + " . $doubleTag[$iii];
				}
			}	

			if ($keysCountTexts[$i]==$texts[$ii]) {
				$x=substr($user[$ii],5,strlen($user[$ii]));
				$x1 = substr($x,0,strpos($x,"@"));
				echo "<br>&nbsp;&nbsp;USER: " . $x1  . " TAGS: " . $tagString . "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID: " . $ids[$ii] ;
			}

		}
		echo "<br>";
	}
}

echo '<br><br><a style="color:#084B8A;"> ------------------------ einzeln vorkommende Kommentare alphabetisch geordent -----------------------------------------------------</a><br>';
// Ausgabe der Texte
ksort($countTexts);
$keysCountTexts = array_keys($countTexts);
$count = 1;
for ($i=0;$i<sizeof($countTexts); $i++) {

	if ($countTexts[$keysCountTexts[$i]]==1) {
				echo "<br><br>" . $count++ .") TEXT: ".$keysCountTexts[$i]; 

		for ($ii=0;$ii<sizeof($texts); $ii++) {
			if ($texts[$ii]===$keysCountTexts[$i]) {
				echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID: " . $ids[$ii];
			}
		}
	}

}

echo '<br><br><a style="color:#084B8A;"><br><br> --------------------------------------------------------------------------------</a>';



?>



