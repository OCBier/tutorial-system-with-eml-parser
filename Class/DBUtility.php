<?php
include_once("LearningModule.php");
include_once ("Quiz.php");
include_once ("Lesson.php");
//error_reporting(0);

/*Controller which makes entities available to interface by loading data from database. This allows lesson and quiz module data
  to be processed in a consistent manner in any upper layer(s). */
class DBUtility
{

	private $connection;
	
	
	/*The ctor will establishes the MySQL connection by creating an mysqli object with the specified hostname, user, password, database name, and port.
      MySQL port 3306 will be used if none is specified. */
	function __construct($hostname, $dbUser, $password, $dbName, $port = 3306) 
	{
		$this->connection = new mysqli($hostname, $dbUser, $password, $dbName, $port);
		
		if ($this->connection->connect_error)
			throw new Exception("Database connection to $dbame on $hostname:$port with specified username $user and password failed.");
	}
	
	
	function __destruct() 
	{
		$this->connection->close();
	}
	
	
	/*Loads content given a certain contentId. The database is queried using connection and the a LearningModule object
	is returned.*/
	public function loadContent(int $contentId)
	{
		$query = "SELECT EMLData, type, moduleName, courseName, unitNumber, description				  
				  FROM CourseContent
				  WHERE contentId = ?;";
				
		$prep = $this->connection->prepare($query);
		if(!$prep)
			throw new Exception("Error in statement.");
			
		$prep->bind_param('i', $contentId);
		return ($this->createModule($prep));               //Create a LearningModule with using result of query.
	}
	
	
	/*Uses courseName, unit number, and type to locate and load a course module. Returns a LearningModule
      of appropriate type using result of query. */
	public function loadContentByAttr(string $courseName, int $unitNumber, string $type)
	{
		$query = "SELECT EMLData, type, moduleName, courseName, unitNumber, description
				  FROM CourseContent
				  WHERE courseName = ? AND unitNumber = ? AND type = ?;";
		
		$prep = $this->connection->prepare($query);
		if(!$prep)
			throw new Exception("There was an error in statement.");
			
		$prep->bind_param('sis', $courseName, unitNumber, $type);
		return ($this->createModule($prep));
	}
	
	/*Executes a prepared statement amd returns the result within a LearningModule object.
	  The LearningModule derived type (either Quiz or Lesson) is determined using the type
	  field in the row and is created by parsing the EML data retrieved from the database.
	  Parsing is performed using the appropriate static function from class EMLParser. */
	private function createModule($statement)
	{
		if (!($statement->execute()))
			throw new Exception("Database query execution failed.");
		
		$result = array("EMLData"=>"", "type"=>"", "moduleName"=>"", "courseName" => "", "unitNumber"=> 2, "description" => "");   //Associative array for result.
		$statement->bind_result($result["EMLData"], $result["type"], 
			$result["moduleName"], $result["courseName"], $result["unitNumber"], $result["description"]);
			
		if (($statement->fetch()) === NULL)
			throw new Exception("No data could be fetched from database. ");
		

		$statement->close();
		
		/*If the content type is lesson, create Lesson wrapper object to hold EML and metadata. */
		if (strtoupper($result["type"]) == "LESSON"):
			return new Lesson($result["EMLData"], $result["moduleName"], $result["courseName"], $result["unitNumber"], $result["description"]);
		
		
		/*Else if content type is quiz, create a Quiz wrapper object */
		elseif (strtoupper($result["type"]) == "QUIZ"):
			return new Quiz($result["EMLData"], $result["moduleName"], $result["courseName"], $result["unitNumber"], $result["description"]);
			
		/*Otherwise, if type is anything else, create a base type (LearningModule) wrapper object */	
		else:
			return new LearningModule($result["EMLData"], $result["moduleName"], $result["courseName"], $result["unitNumber"], $result["description"]);
		
		endif;
	}
	
	


}




?>