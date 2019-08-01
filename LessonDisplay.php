<?php
include_once("Class/TutorialController.php");
include_once("Template.php");
function loadLesson()
{
	/*If param contentid has been given in query string, attempt to load and display this content by creating
	  a TutorialPage object and calling appropriate methods. If success, return the parsed data, else
	  return error message from exception. */
	$content = ""; 
	$id = (empty($_GET['contentid'])) ? NULL : $_GET['contentid'];   //If no content id given in query string, use value of 2 which gives default.
	if (is_null($id))
		header("Location: ./");                                            //Go to home page if no contentid given.
		
	$controller = new TutorialController($id);                           //Create the TutorialController for this lesson id.
	try
	{
		$controller->loadModule();                                    //Load data into TutorialPage's internal container.
		$content = $controller->outputModule();                       //Show the html contents by calling outputModule() on the LessonPage object
	} catch (Exception $ex)
	  {
		return $ex->getMessage();
	  }
	
	
	return $content;
}
 
createTemplate(loadLesson());                                 //Create the template for course pages with lesson content inserted.
			
?>