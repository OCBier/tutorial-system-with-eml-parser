<?php
//error_reporting(0);

class QuizQuestion
{
	private $heading;
	private $questionText;
	private $options;
	private $correctAnswer;
	private $resultState;
	private $marked;

	private static $numInstances = 0;
	private $uniqueName = NULL;


	function __construct($heading, $questionText, array $options, $correctAnswer)
	{
		$this->heading = $heading;
		$this->questionText = $questionText;
		$this->options = $options;
		$this->correctAnswer = $correctAnswer;
		$this->resultState = array("CORRECT"=> FALSE, "WRONG" => FALSE, "BLANK" => FALSE);  //Possible results of grading question.
		                                                                                 //Name for key in $resultStates
		$this->marked = false;
		
		self::$numInstances++;                                          //Increment number of instances.
		$this->uniqueName = "q_" . self::$numInstances;                 //Set unique name for this question. 
	}	

	function __destruct()
	{ }

	
	public function isCorrect()
	{
		return (($this->resultState["CORRECT"] === true) ? true : false);
	}
	
	
	/*Check if question correct and set appropriate response text.*/
	public function markQuestion($response)
	{
		$this->marked = true;                         //Correct answer.
		
		if ($response === $this->correctAnswer)
			$this->resultState["CORRECT"] = true;
			
		else if (!(empty($response)))                  //Wrong answer.
			$this->resultState["WRONG"] = true;
			
		else                                           //Blank answer.
			$this->resultState["BLANK"] = true;    
	}
	
	
	public function getUniqueId()
	{
		return $this->uniqueName;
	}
	
	
	/*Convenient method for outputting questions in a uniform manner in HTML format. */
	public function outputHTML()
	{
		$output = " <div class = \"quiz-question\">
					<h3>" . $this->heading . "</h3>
					<p>" . $this->questionText . "</p>";
		
		$letter = 'a';
		
			
		/*Display the text for each of the options in this question within <input> elements. These elements
		  have type="radio", a value set to the question letter (Eg. value in range a-d) and a name
		  which identifies the group of inputs for this question. The name is set as the "uniqueName" attribute
		  of this QuizQuestion.*/
		foreach($this->options AS $curOption)
		{
			$output .= "<p>  $letter.
				<input type = \"radio\" value = \"$letter\" name = \"" . $this->uniqueName . "\" >
				$curOption
				</p>";
			$letter++;             //Advance to next letter.
			
		}
		
		/*If question marked, display result with output */
		if($this->marked === true)
		{
			$outcome = null;
			if ($this->resultState["CORRECT"] === true)
				$outcome = "Correct.";
						
			elseif ($this->resultState["WRONG"] === true)
				$outcome = "Incorrect.";
				
			else
				$outcome = "Not answered.";
						
			$output .= "<div class = \"question-result\">$outcome</div>";          //Append the que
		}
		return $output .= "</div>";                                       //Return $question, with closing </div>
	}
	
	
}

?>