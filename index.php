<?php 

$offset = 29; 
$entries_per_page = 25;

// $offset = 0; 
// $entries_per_page = 99999999;

$KANJI_ELE =    [ 'k_ele', 'ke_pri' ];
$HIRAGANA_ELE = [ 'r_ele', 're_pri' ];

$ele = $HIRAGANA_ELE;

// 
if (!isset($_GET['offset']))
	header('Location: ?offset='.$offset);


// Make xml from file
$xml = simplexml_load_file('JMdict_e.xml') or die('Error: Cannot create object');

// Parsed word entries and filter by frequency
$entries = $xml->xpath('./entry[' . $ele[0] . '/' . $ele[1] . ']');

// Change offset is set
$offset = isset($_GET['offset']) ? $_GET['offset'] : $offset;

$msg1 = "Page: " . ($offset+1) . " of " . ( (int) (sizeof($entries) / $entries_per_page) + 1) .  "<br>";
$msg2 = "Kanji: " . (($entries_per_page*$offset)+1) . ' - ' . (($entries_per_page*$offset)+$entries_per_page) . " / " . sizeof($entries);
?>





<!DOCTYPE html>
<html lang="en">
<head>
	<title>Table V03</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!-- =============================================================================================== -->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
<style>
	table {
		max-width: 700px;
		margin: 0 auto 0 3px;
	}
</style>
</head>
<body>
	
	<div class="limiter">
		<div class="container-table100">
			<div class="wrap-table100">
				<div class="table100 ver5 m-b-110">
					<table data-vertable="ver5" >
						<thead>
							<tr class="row100 head">
								<th class="column100 column1" data-column="column1">ID #</th>
								<th class="column100 column2" data-column="column2">漢字</th>
								<th class="column100 column3" data-column="column3">かな</th>
							</tr>
						</thead>

						<?php 

// Functions
// --------------------------------------------------

// Assign freq value based on assigned freq lists

function get_freq_value( $str ) {

	if ( substr( $str, 0, 2 ) === "nf" )
		return (int) substr( $str, 2, 2 );

	elseif ( substr( $str, 0, 4 ) === "news" )
		return ((int) substr( $str, 4, 1 )) + 100;

	elseif ( substr( $str, 0, 4 ) === "ichi" )
		return ((int) substr( $str, 4, 1 )) + 200;

	elseif ( substr( $str, 0, 4 ) === "spec" )
		return ((int) substr( $str, 4, 1 )) + 300;

	else
		return 1000;
}


// Sort kanji word entries by computed frequency value

function sort_entries_by_freq( $a, $b ) {
	global $ele;

	$a_val = 1000;
	$a_freq = $a->xpath('./' . $ele[0] . '/' . $ele[1] . '[text()]');
	for ($h = 0; $h < sizeof($a_freq); $h++) {
		$a_val = min($a_val, get_freq_value( (string) $a_freq[$h][0]) );
	}

	$b_val = 1000;
	$b_freq = $b->xpath('./' . $ele[0] . '/' . $ele[1] . '[text()]');
	for ($h = 0; $h < sizeof($b_freq); $h++) {
		$b_val = min($b_val, get_freq_value( (string) $b_freq[$h][0]) );
	}

	return (int) $a_val - (int) $b_val;
}





// Main
// --------------------------------------------------

// Sort by frequency
usort($entries, 'sort_entries_by_freq');

echo '<tbody>';

for ( $i = ($entries_per_page*$offset); $i < (($entries_per_page*$offset)+$entries_per_page); $i++ ) {
	echo '<tr class="row100">';

	$entry = $entries[$i];

	$id = (string) $entry->ent_seq;
	echo "<td class=\"column100 column1\" data-column=\"column1\">$id</td>";

	// $kanji = (string) $entry->k_ele->keb[0];
	// echo "<td class=\"column100 column2\" data-column=\"column2\">$kanji</td>";

	$kanji = [];
	$kanji_entries = $entry->k_ele;

	for ( $j = 0; $j < sizeof($kanji_entries); $j++ ) {
		if ( property_exists($kanji_entries[$j], 'ke_pri') ) {
			array_push($kanji, (string) $kanji_entries[$j]->keb);
		}
	}
	echo '<td class="column100 column2" data-column="column2">' . join(",<br>\n", $kanji) . '</td>';


	// $kana = (string) $entry->r_ele->reb[0];
	// echo "<td class=\"column100 column3\" data-column=\"column3\">$kana</td>";

	$kana = [];
	$kana_entries = $entry->r_ele;

	for ( $j = 0; $j < sizeof($kana_entries); $j++ ) {
		if ( ($j==0) || property_exists($kana_entries[$j], 're_pri') ) {
			array_push($kana, (string) $kana_entries[$j]->reb);
		}
	}
	echo '<td class="column100 column3" data-column="column3">' . join(",<br>\n", $kana) . '</td>';


	echo '</tr>';
}

echo '</tbody>';
 ?>
						
					</table>
				</div>
			</div>
		</div>
		<?php 
			echo $msg1;
			echo $msg2;
		 ?>
	</div>


	

<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>