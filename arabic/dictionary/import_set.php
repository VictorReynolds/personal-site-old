<pre><?php
	//require_once($_SERVER["DOCUMENT_ROOT"] ."/inc/config.php");
	//require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/database.php");
	require_once("lib/dictionary_manager.php");

	// Importing constants
	$importDir = "./data";
	$completeDir = "./data_imported";

	$delimTermDef = "|";
	$delimDefTerm = "~";
	
	$lists = scandir($importDir);

	foreach($lists as $fileName)
	{
		if (strpos($fileName, ".txt") !== false)
		{
			$filePath = $importDir . "/" . $fileName;

			$data = arabicImportSet($filePath, $delimTermDef, $delimDefTerm);
			//convertDataToHTMLEntities($data);
			//var_dump($data);
			formatSetForInput($data);
			
			importSet($data);
			rename($filePath, $completeDir . "/" . $fileName);
		}
	}

	//var_dump(getOrCreateSetID("poop", "fart"));
?></pre>