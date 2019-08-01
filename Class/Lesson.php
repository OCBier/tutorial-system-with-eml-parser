<?php
include_once("LearningModule.php");
//error_reporting(0);

/*Represents a Lesson which is a type of LearningModule. No unique functionality is currently
  defined, although this may be added in future */
class Lesson extends LearningModule
{
	/*Calls parent's ctor in order to initialize data members */
	function __construct(string $eml, string $moduleName, string $courseName, int $unitNumber, string $description, string $htmlData = "")
	{
		parent::__construct($eml, $moduleName, $courseName, $unitNumber, $description, $htmlData);
	}
	
	function __destruct()
	{ }


}

?>