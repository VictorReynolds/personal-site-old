<?php
	require_once($_SERVER["DOCUMENT_ROOT"] ."/inc/config.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/database.php");
	
	/* This class contains the methods used to format Arabic strings to be stored in the various fields.
	 * Arabic is usually written without vowels, so the words in the database should be searchable
	 * without them, but it needs to display the vowels when a potential result is found
	 * so that a beginner can learn the pronunciation.
	**/
	class ArabicStringFormat
	{
		// Reference containers for characters to be removed
		//public $vowels = array("&#1611;","&#1612;","&#1613;","&#1614;","&#1615;","&#1616;","&#1618;","&#1619;","&#1620;","&#1621;");
		//public $shadda = "&#1617;";
		//public $alifs = array("&#1570;","&#1571;","&#1573;"); //alif with hamza above, hamza below, and with madda
		//public $alif = "&#1575;";

		public $vowels = array("ِ", "ٍ", "ْ", "َ", "ً", "ُ", "ٌ");
		public $shadda = "ّ";
		public $alifs = array("أ", "إ", "آ");
		public $alif = "ا";
		
		function removeVowels($string)
		{
			return str_replace($this->vowels, "", $string);
		}
		
		function removeAlifs($string)
		{
			return str_replace($this->alifs, $this->alif, $string);
		}
		
		function removeShadda($string)
		{
			return str_replace($this->shadda, "", $string);
		}
	}
	
	/* This function will split apart a set file into a two dimensional array,
	 * with each first index representing a term, definition pair indexed
	 * 0 and 1 respectively.
	 * The file should be encoded in utf-8
	**/
	function arabicImportSet($file_path, $term_def = "|", $def_term = "~")
	{
		$data_file_string = file_get_contents($file_path);
		if ($data_file_string == false)
		{
			return null;
		}
		
		// PHP does some weird stuff with Arabic letters, so I just convert them
		// into HTML-entities in any case where they might need to be displayed.
		//$data_file_string = mb_convert_encoding($data_file_string, "html-entities", "utf-8");
		$csv_data = explode($def_term, $data_file_string);
		foreach($csv_data as $index => &$datum)
		{
			if ($index > 2)
			{
				$datum = explode($term_def, $datum);
			}
		}
		return $csv_data;
	}
	
	/* This function will update a two dimensional array so that
	 * each row will have values with keys associated with the
	 * fields in the mySQL database. This way the set can be
	 * interpreted and imported very easily.
	**/
	function formatSetForInput(&$data, $termIndex = 0, $defIndex = 1)
	{
		$formatter = new ArabicStringFormat();
		foreach($data as $index => &$term)
		{
			// The first three lines should be
			if ($index > 2)
			{
				$term["arabic"] = escapeQuotesInString(trim($term[$termIndex]));
				$term["english"] = escapeQuotesInString(trim($term[$defIndex]));
				unset($term[0]);
				unset($term[1]);
				$arabic = $term["arabic"];
				$term["arabic_uv"] = $formatter->removeShadda($formatter->removeVowels($arabic));
				$term["arabic_uva"] = $formatter->removeAlifs($term["arabic_uv"]);
				$term["arabic_us"] = $formatter->removeVowels($arabic);
				$term["arabic_usa"] = $formatter->removeAlifs($term["arabic_us"]);
			}
		}
	}


	/* This function will return the ID of a set corresponding 
	 * Category or TermSet to a given title.
	 * If the a TermSet with that name exists, it will return that ID.
	 * Otherwise, it will return the ID of a new TermSet record that it creates.
	**/
	function getOrCreateGroupID($type, $title, $desc = '')
	{
		global $arab_db;
		$find_statement = 'SELECT id FROM ' . $type . ' WHERE title="' . $title . '";';
		$results = $arab_db->query($find_statement);
		$results = $results->fetchAll(PDO::FETCH_ASSOC);

		if (empty($results))
		{
			$create_statement = 'INSERT INTO ' . $type . ' (title, description, date)'
			                    . 'VALUES ("' . $title . '", "' . $desc . '", CURDATE());';
			$arab_db->exec($create_statement);
			$results = $arab_db->query($find_statement);
			$results = $results->fetch(PDO::FETCH_ASSOC);
			return $results["id"];
		}
		else
		{
			if (count($results) > 1) 
			{
				throw new Exception("There are two sets or categories with the same name");
			}
			return $results[0]["id"];
		}
	}

	function escapeQuotesInString($str)
	{
		return str_replace('"', '\"', $str);
	}

	function getOrCreateTermID($term)
	{
		global $arab_db;
		$find_statement = 'SELECT id FROM Term WHERE arabic="' . $term["arabic"] . '";';
		$results = $arab_db->query($find_statement);
		$results = $results->fetchAll(PDO::FETCH_ASSOC);

		if (empty($results))
		{
			$fields = implode(array_keys($term), ", ");
			$values = '"' . implode($term, '", "') . '"';

			$create_statement = 'INSERT INTO Term (' . $fields . ') VALUES (' . $values . ');';
			$arab_db->exec($create_statement);

			$results = $arab_db->query($find_statement);
			$results = $results->fetch(PDO::FETCH_ASSOC);
			return $results["id"];
		}
		else
		{
			if (count($results) > 1) 
			{
				throw new Exception("There are two terms with the same values.");
			}
			return $results[0]["id"];
		}
	}

	function createTermSetLink($termID, $setID, $index)
	{
		global $arab_db;
		$find_statement = 'SELECT * FROM TermSet_has_Term WHERE Term_id=' . $termID . ' AND TermSet_id=' . $setID . ';';
		$results = $arab_db->query($find_statement);
		$results = $results->fetchAll(PDO::FETCH_ASSOC);

		if (empty($results))
		{
			$fields = "Term_id, TermSet_id, term_index";
			$values = $termID . ', ' . $setID . ', ' . $index;
			$create_statement = 'INSERT INTO TermSet_has_Term (' . $fields . ') VALUES ('
			                     . $values . ');';
			$arab_db->exec($create_statement);
		}
	}

	function createCategorySetLink($categoryID, $setID)
	{
		global $arab_db;
		$find_statement = 'SELECT * FROM Category_has_TermSet WHERE TermSet_id=' . $setID . ' AND Category_id=' . $categoryID . ';';
		$results = $arab_db->query($find_statement);
		$results = $results->fetchAll(PDO::FETCH_ASSOC);

		if (empty($results))
		{
			$fields = "Category_id, TermSet_id";
			$values = $categoryID . ', ' . $setID;
			$create_statement = 'INSERT INTO Category_has_TermSet (' . $fields . ') VALUES ('
			                     . $values . ');';
			$arab_db->exec($create_statement);
		}
	}
	
	function importSet($data, $hasCategory = false)
	{
		global $arab_db;
		$categoryID = getOrCreateGroupID("Category", $data[0]);
		$setID = getOrCreateGroupID("TermSet", $data[1], $data[2]);
		createCategorySetLink($categoryID, $setID);
		foreach($data as $index => $term)
		{
			if ($index > 2)
			{
				$termID = getOrCreateTermID($term);
				createTermSetLink($termID, $setID, $index - 3);
			}
		}
	}

	function convertDataToHTMLEntities(&$data)
	{
		foreach($data as &$row) {
			if (gettype($row) == "string")
			{
				$row = mb_convert_encoding($row, "html-entities", "utf-8");
			}
			else if (gettype($row) == "array")
			{
				// Yay, recursion!
				convertDataToHTMLEntities($row);
			}
		}
	}

	// More accurate name would be "isNonWestern"
	function isNonLatin($inputStr)
	{
		return preg_match('/[^\\p{Common}\\p{Latin}]/u', $inputStr);
	}

	function searchDictionary($searchstring)
	{
		
		global $arab_db;

		$order = (isNonLatin($searchstring)) ? "arabic" : "english";

		$searchfields = array("LOWER(english)", "arabic", "arabic_uv", "arabic_uva", "arabic_us", "arabic_usa");
		$searchfields = array_map(function($str) { return $str . " LIKE :searchstring"; }, $searchfields);

		$searchstring = "%" . strtolower($searchstring) . "%";

		$search_statement = "SELECT arabic, english FROM Term WHERE " . implode(" OR ", $searchfields) . " ORDER BY CHAR_LENGTH(" . $order . ");";
		$results = $arab_db->prepare($search_statement);
		$results->execute(array(":searchstring" => $searchstring));

		return $results->fetchAll(PDO::FETCH_ASSOC);
	}
	
	// selecting terms from a known set id
	// SELECT english, arabic, term_index From TermSet_has_Term JOIN Term ON id = Term_id WHERE TermSet_id = 4 ORDER BY term_index;
	//
	// selecting all of the terms in a category
	// SELECT arabic, english, TermSet.title FROM Category_has_TermSet as c_has_s JOIN TermSet_has_Term as s_has_t ON 
	// c_has_s.TermSet_id = s_has_t.TermSet_id JOIN Term ON Term.id = s_has_t.Term_id  join TermSet ON TermSet.id = s_has_t.TermSet_id WHERE 
	// c_has_s.Category_id = 3 ORDER BY s_has_t.term_index;
?>