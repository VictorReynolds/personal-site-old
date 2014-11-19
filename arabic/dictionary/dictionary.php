<pre><?php
	require_once($_SERVER["DOCUMENT_ROOT"] ."/inc/config.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/database.php");
	var_dump($arab_db);
	$testdata = $arab_db->query("SELECT * FROM Term");
	$testdata = $testdata->fetchAll(PDO::FETCH_ASSOC);
	var_dump($testdata);
	foreach ($testdata[0] as &$datum)
	{
		$datum = mb_convert_encoding($datum, "html-entities", "utf-8");
	}
	var_dump($testdata);
	$data_file_string = file_get_contents("data/akCh12.txt");
	$data_file_string = mb_convert_encoding($data_file_string, "html-entities", "utf-8");
	//var_dump($data_file_string);
	$csv_data = str_getcsv($data_file_string, "\n");
	
	$vowels = array("&#1611;","&#1612;","&#1613;","&#1614;","&#1615;","&#1616;","&#1617;","&#1618;","&#1619;","&#1620;","&#1621;");
	$alefs = array("&#1570;","&#1571;","&#1573;");
	foreach ($csv_data as $index => &$item)
	{
		$item = str_getcsv($item,"|");
		if ($index > 1)
		{
			array_push($item, str_replace($vowels, "", $item[0]));
			array_push($item, str_replace($alefs,"&#1575;",$item[2]));
		}
	}
	$termtest = mb_convert_encoding($csv_data[3][0], "utf-8", "html-entities");
	$deftest = mb_convert_encoding($csv_data[3][1], "utf-8", "html-entities");
	//var_dump($termtest);
	//var_dump($deftest);
	//echo("INSERT INTO Term (english, arabic) VALUES ('" . $termtest ."', '" . $deftest . "');");
	//$arab_db->exec("INSERT INTO Term (arabic, english) VALUES ('" . $termtest ."', '" . $deftest . "');");
	//var_dump($csv_data);
?></pre>

<table>
	<tr>
		<th>Arabic</th>
		<th>Definition</th>
		<th>Arabic (Unvoweled)</th>
		<th>Arabic (Non-strict alif)</th>
	</tr>
	<?php
		for ($i = 0; $i < count($csv_data); $i++)
		{
			if ($i > 1)
			{
				echo("<tr>");
				for($j = 0; $j < count($csv_data[$i]); $j++)
				{
					echo("<td>");
					echo($csv_data[$i][$j]);
					echo("</td>");
				}
				echo("</tr>\n");
			}
		}
	?>
</table>