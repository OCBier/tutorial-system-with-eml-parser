<?php
include_once("LearningModule.php");
//error_reporting(0);

/*A type of LearningModule which represents a Quiz. Contains inherited methods and protected properties from
  super class, as well as functionality for grading a quiz (function gradeQuiz()) */
class Quiz extends LearningModule
{

	private $quizQuestions;
	private $numQuestions;
	
	/*Calls parent's ctor in order to initialize data members */
	function __construct(string $EMLData, string $moduleName, string $courseName, int $unitNumber, string $description, string $htmlData = "")
	{
		parent::__construct($EMLData, $moduleName, $courseName, $unitNumber, $description, $htmlData);
		$numQuestions = 0;
	}
	
	function __destruct()
	{ }

	/*Grade the quiz by finding the associated response for each question in $responses 
	Returns the number of correct questions.*/
	public function gradeQuiz($responses)
	{
		$numCorrect = 0;
		
		foreach ($this->quizQuestions AS $curQuestion)
		{
			$id = $curQuestion->getUniqueId();                                                   //Get identifier, which is key in $_POST for response.
			$submittedAnswer = $responses[$id];                                             
			$curQuestion->markQuestion(( (empty($responses[$id])) ? NULL : $responses[$id] ));   //If answer  is specified, pass answer to $curQuestion->markQuestion(), else pass NULL.
			if($curQuestion->isCorrect())
				++$numCorrect;
		}
				
		return $numCorrect;
	}
	
	
	public function getNumQuestions()
	{
		return count($this->quizQuestions);
	}
	
	
	/*Returns the questions for this quiz */
	public function getQuestions()
	{
		return $this->quizQuestions;
	}
	
	
	/*Takes an array containing QuizQuestion objects which represent the questions
	 for this quiz and sets member quizQuestions to this value */
	public function setQuestions(array $questions)
	{
		$this->numQuestions = count($questions);          //Set numQuestions to size of $questions.
		$this->quizQuestions = $questions;                //Set the array of questions.
	}
	
	
}


?>