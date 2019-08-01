<?php

include_once("TutorialController.php");
include_once("LearningModule.php");
include_once("Quiz.php");
include_once("EMLParser.php");
//error_reporting(0);

/*Controller for a quiz within a course. The course module is represented by its courseId
Contains methods for loading the EML data for the course module and outputting this data as HTML.*/
class QuizController extends TutorialController
{
	
		
	/*Params for the contentId of the quiz and the optional name of the submit element. */
	function __construct(int $contentId, string $submitElemName = "submit")
	{
		parent::__construct($contentId);
		$this->htmlContent = NULL;
	}
	
	function __destruct()
	{ }
	
	
	public function loadModule()
	{
		$this->learningModule = $this->dbUtility->loadContent($this->contentId);             //Use parent's method to to create the Quiz ($this->learningModule)
		$quizEML = $this->learningModule->getEML();
		$quizQuestions = EMLParser::createQuizQuestions($quizEML);                           //Create quiz question objects from EML
		
		$this->htmlContent = EMLParser::parseQuiz($quizEML);                                 //Parse EML data in Quiz to create HTML and set attribute quizEML.
		$this->learningModule->setQuestions($quizQuestions);                                //Set quiz questions for quiz by parsing question data in EML.
	}
	
	
	/*Overrides parent method. 
	  Outputs the html for this quiz for use by boundary layer. 
	 */
	public function outputModule()
	{
		/*Make sure module is loaded*/
		if ($this->learningModule === NULL)
			throw new Exception( "No course content has been loaded. Unable to display quiz.");
		
		else
			$this->updateQuestionHTML($this->htmlContent);        //Make sure quiz HTML reflects current state of questions.
			
		return $this->htmlContent;                           //Default behaviour here of returning html
	}

	
	/*Calls gradeQuiz() on the LearningModule for this QuizPage in order to
	  calculate grades for the quiz. Returns the number of correct questions.*/
	public function getGrades()
	{
		$inputResponses = array();
		$questions = $this->learningModule->getQuestions();
		foreach($questions AS $curQuestion)
		{
			$inputKey = $curQuestion->getUniqueId();                               //Key to access user responses in $_POST is question's uniqueId property.
			$response = (empty($_POST[$inputKey])) ? NULL : $_POST[$inputKey];     //Pass NULL if no input submitted for this index, otherwise pass value.
			$inputResponses[$inputKey] =  $response;                                //Insert the response in $_POST into $inputResponses
		}
		
		return $this->learningModule->gradeQuiz($inputResponses);                  //Grade the quiz and return result.
	}
	
	
	public function getNumQuestions()
	{
		return $this->learningModule->getNumQuestions();
	}
	
	
		
	/*Updates quiz HTML with new html for questions. Useful when questions change so that
	 state of questions can be reflected in the HTML*/
	private function updateQuestionHTML(string $oldHTML)
	{
		$questionHTML = "";
		$quizQuestions = $this->learningModule->getQuestions();
		
		/*Get the HTML for each quiz question */
		foreach($quizQuestions AS $cur)
		{
			$questionHTML .= $cur->outputHTML();                                        //Get the HTML for this question.
		}
		
		$this->htmlContent = EMLParser::refreshQuizHTML($oldHTML, $questionHTML);      //Parse the updated HTML and set it as htmlContent.
	}
	
	
}


?>




