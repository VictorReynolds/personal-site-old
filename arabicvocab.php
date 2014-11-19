<?php
	header('Content-Type: text/html; charset=utf-8');
	//mb_http_input('UTF-16le');
	//mb_internal_encoding('UTF-16le');
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		require_once('lib-php/simple_html_dom.php');

		$pageURL = $_POST["page_url"];

		$url = $pageURL;
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$html = curl_exec($ch);
		curl_close($ch);

		$html = mb_convert_encoding($html, 'html-entities', "utf-16le");

		$term_def_del = mb_convert_encoding($_POST["btw_term_definition"], 'html-entities');
		$def_term_del = mb_convert_encoding($_POST["btw_definition_term"], 'html-entities');

		$outputString = "";

		$content = str_get_html($html);

		// $dom = new DOMDocument;
		// libxml_use_internal_errors(true);
		// $dom->loadHTML($html);
		// libxml_clear_errors();

		// $headers = $dom->getElementsByTagName('tr');

		// var_dump($headers);
		// var_dump($html);

		$termTotal = 0;

		foreach($content->find('tr') as $trKey => $tableRow) {
			if($tableRow->children(0) !== null && $tableRow->children(1) !== null && $tableRow->children(2) !== null && $trKey !== 0) {
				$def = $tableRow->children(0)->plaintext;
				$def = str_replace("&nbsp;", "", $def);
				$term = $tableRow->children(1)->plaintext;
				$term = str_replace("&nbsp;", "", $term);
				$roman = $tableRow->children(2)->plaintext;
				$roman = str_replace("&nbsp;", "", $roman);

				if (trim($def) != "" && trim($term) != "") {
					$outputString = $outputString . "&#8206;(" . $roman . ")&#8207" . $term . "&#8207" . $term_def_del . "&#8206;" . $def . $def_term_del;
					$termTotal++;
					//$outputString = $outputString . "&#8206;(" . $roman . ")&#8207" . $term . "&#8207|&#8206;" . $def . "&#10;";
				}
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

	<title>Desert Sky Arabic Vocab Extractor</title>
	<meta name="description" content="Vocab Extractor from Desert Sky to a quizlet-compatible format." />
	<LINK href="style/arabicvocab.css" rel="stylesheet" type="text/css">

</head>

<body>

	<div class="page-title">Desert Sky to Quizlet Arabic Vocab Extractor</div>
	<div class="page-main">
	<?php if ($_SERVER["REQUEST_METHOD"] != "POST") { ?>
		<form class="center-column" method="post">
			<label class="text-label" for="page_url">Page URL: </label><input class="text-input" type="text" name="page_url">
			<br>
			<label class="text-label" for="btw_term_definition">Between Term and Definition: </label><input class="text-input" type="text" name="btw_term_definition" value="|">
			<br>
			<label class="text-label" for="btw_definition_term">Between Definition and Term: </label><input class="text-input" type="text" name="btw_definition_term" value="&#x26;#10;">
			<br>
			<input type="submit" id="parse_button" class="button" value="Extract Vocab">
		</form>
	<?php } else { ?>
		<div class="center-column">
			<div class="section-title">Formatted Vocab Export: </div>
			<div class="section-info">Total Terms: <?=$termTotal?></div>
			<div class="data-wrapper">
			<pre><?php echo $outputString; ?></pre>
			<textarea id="data"><?php echo $outputString; ?></textarea>
			</div>
		</div>
	<?php } ?>
	</div>

</body>

<?php if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
<script>
	$('#data').click(function() {
		 this.focus();
		 this.select();
		 });

</script>

<?php } ?>