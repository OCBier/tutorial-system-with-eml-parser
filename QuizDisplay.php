<?php
include_once("Class/QuizController.php");
include_once("Template.php");


(function()
{
	/*If param contentid has been given in query string, attempt to load and display this content by creating
	  a TutorialPage object and calling appropriate methods. If success, return the parsed data, else
	  return error message from exception. */
	$id = (empty($_GET['contentid'])) ? NULL : $_GET['contentid'];
	if (is_null($id))
		header("Location: ./");                                            //Go to home page if no contentid given.
		
	$controller = new QuizController($id);  
	$quizScore = NULL;
	
	try
	{
		$controller->loadModule();                                          //Load EML data.
	} catch (Exception $ex)
	  {
		echo $ex->getMessage();
	  }
	
	/*If quiz has been submitted, attempt to grade it */
	if ($_SERVER["REQUEST_METHOD"] === "POST")
	{
		$numQuestions = $controller->getNumQuestions();                   //Get number of questions.
		$numCorrect = $controller->getGrades();                          //Grade the quiz. Returns num correct questions.
		$quizScore = bcmul(bcdiv($numCorrect, $numQuestions, 2), 100);   //Get % result.
	}
		
	$quizContent = $controller->outputModule();                          //Get the html holding different elements of quiz.
	$formAction = htmlspecialchars($_SERVER["PHP_SELF"]."?".$_SERVER["QUERY_STRING"]);
	$content = "
	<form id = \"quiz-form\" method = \"POST\" action = \"$formAction\">  
		$quizContent
		<input type = \"submit\" value = \"submit quiz\" name = \"submit\">
	</form>";
	
	if(!is_null($quizScore))
		$content .= "<div id = \"outcome\" ><p>Your score: $numCorrect/$numQuestions ($quizScore%)</p></div>";   //Add quiz result to content if form has been graded.       
	                                        
	createTemplate($content);                                            //Create template with quiz questions displayed as contents.
})();
?>


