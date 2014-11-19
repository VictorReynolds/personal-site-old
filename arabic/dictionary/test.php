<pre>
<?php
	$test = "poop\nfart|cheese\ngravy";
	$test = "بووب\nبتبثش|test\nmore";
	
	$arr = str_getcsv($test, "|");
	
	$arr[0] = mb_convert_encoding($arr[0], "html-entities", "utf-8");
	
	var_dump($arr);

?></pre>