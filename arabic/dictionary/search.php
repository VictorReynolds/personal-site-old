<?php
	require_once("lib/dictionary_manager.php");
	if (empty($_GET) || !(array_key_exists("searchstring", $_GET) && trim($_GET["searchstring"]) != ""))
	{
		echo("There is no searchstring value!");
	}
	else
	{
		echo(json_encode(searchDictionary($_GET["searchstring"])));
	}
?>