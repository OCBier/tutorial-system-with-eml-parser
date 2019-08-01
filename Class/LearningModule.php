<?php
include_once ("EMLParser.php");
//error_reporting(0);

/*Represents a general learning module and contains data required for any instance. Can be instantiated
  but mostly used as super class for more specific types. */
class LearningModule
{
	protected $EMLData;
	protected $moduleName;
	protected $courseName;
	protected $unitNumber;
	protected $description;
	
	/*Sets properties for this learning module. */
	function __construct(string $EMLData, string $moduleName, string $courseName, int $unitNumber, string $description, string $htmlData = " ")
	{
		$this->EMLData = $EMLData;
		$this->moduleName = $moduleName;
		$this->courseName = $courseName;
		$this->unitNumber = $unitNumber;
		$this->description = $description;
	}
	
	function __destruct()
	{ }

		
	/*Set html for this LearningModule */
	public function setEML(string $data)
	{
		$this->EMLData = $data;
	}
	
	public function getEML()
	{
		return $this->EMLData;
	}
	
	/*Returns the metadata for this LearningModule in an associative array.
     Array keys are:
	 "moduleName" => module name
	 "courseName" => course name
	 "unitNumber" => unit number
	 "description" => module description
	 */
	public function getMetaData()
	{
		return array("moduleName"=>$this->moduleName, "courseName"=>$this->$courseName, 
			"unitNumber" => $this->unitNumber, "description" => $this->description);
	}
}






?>