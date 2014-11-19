<pre>
<?php
	$category = "Allif Baa, 3rd Edition";

	$dataDir = "./data";

	$lists = scandir($dataDir);
	
	foreach($lists as $fileName)
	{
		if (strpos($fileName, ".txt") !== false)
		{
			$filePath = $dataDir . "/" . $fileName;
			$chNumber = trim($fileName, ".a..zA..z");
				$chNumber = ltrim($chNumber, "0");
			$setTitle = "Chapter " . $chNumber . " - الفصحى";
			$setDesc = "Chapter " . $chNumber . " Description";

			$prePend = array($category, $setTitle, $setDesc);
			$prePend = implode($prePend, "~") . "~";


			$file = file_get_contents($filePath);
			if (strpos($file, $category) === false)
			{
				$file = $prePend . $file;
				file_put_contents($filePath, $file);

				echo("Modified: " . $filePath);
			}
		}
	}
?>
</pre>