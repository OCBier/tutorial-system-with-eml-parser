<?php
//error_reporting(0);
include_once("DBUtility.php");
include_once("LearningModule.php");
include_once("Lesson.php");
include_once("EMLParser.php");
//error_reporting(0);

/*Controller which creates a Lesson or Quiz object within a course (derived fom LearningModule).
The LearningModule is represented by its courseId.
Contains methods for loading the EML data for the course module and outputting this data as HTML.*/
class TutorialController
{
	
	protected $dbUtility = NULL;                       //Provides interface to MySQLi to create a database connection and package data returned from queries into entity objects.
	protected $learningModule = NULL;                //Polymorphic reference. Container for lesson contents.
	protected $htmlContent = NULL;                      //HTML for module.
	protected $contentId = 0;                          //Unique id for the module to be loaded from database.
	
	/*Create a dbUtility object for interacting with database in order to load
	  data packaged in entity objects. Also set the unique identifier "contentId"
	of the LearnngModule to be loaded */
	function __construct(int $contentId)
	{
		$this->dbUtility = new DBUtility("localhost", "public_elevated", "XlitiuB7QFS0GpXN", "comp_466" , 3307);
		$this->contentId = $contentId;
	}
	
	function __destruct()
	{ }
	
	
	/* Loads the EML for the course module with id $this->contentId and stores it in
	 * $this->moduleData 
	 */
	public function loadModule()
	{
		try
		{
			$this->learningModule = $this->dbUtility->loadContent($this->contentId);
			$this->htmlContent = EMLParser::parseLesson($this->learningModule->getEML());            //Get loaded module EML and parse to create HTML.
		} catch (Exception $e)
		  {
			throw new Exception( "Could not load content: " .$e->getMessage());
		  }
	}
	
	
	/* Uses EMLParser to convert data into html which may be used by boundary layer.
	 *  If learning module has not been set, then throw exception.
	 */
	public function outputModule()
	{
		if ($this->htmlContent === NULL)
			throw new Exception( "No course content has been loaded. Unable to display content.");
		
		else
			return $this->htmlContent;                                   //Return parsed HTML data.
	}
	

}





?>




