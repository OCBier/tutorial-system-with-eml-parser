<?php
include_once("LearningModule.php");
include_once ("Quiz.php");
include_once("QuizQuestion.php");
include_once ("Lesson.php");
//error_reporting(0);

class EMLParser
{
		
	private static $GENERAL_MAPPINGS = NULL;                                   //General EML tags mapped to html tags
	private static $QUIZ_MAPPINGS = NULL;			                          //Maps EML tags specific to quizzes to html tag names
	private static $LESSON_MAPPINGS = NULL;                                  //Maps EML tags specific to lessons to html tag names
			
	
	/*Return reference to the LearningModule passed to param with HTML data set using parsed EML data.
	  
	  No assumption is made about the type of EML document so we do a simple linear traversal
	  through each of the sets of mappings to convert EML tags to html.
	*/  
	public static function parseEML(string $data)
	{
		self::initData();                                                         //Make sure that mapping data is initialized.
		self::replaceTags(self::$GENERAL_MAPPINGS, $data);                              //Parse general tags in $data.
		self::replaceTags(self::$LESSON_MAPPINGS, $data);                                //Parse lesson tags in $data.             
		self::replaceTags(self::$QUIZ_MAPPINGS, $data);                                 //Parse quiz tags in $data.
		
		return $data;
	}
	
	
	
	
	/*Parsed $data into html and returns it.

	  The strategy for parsing is to traverse self::$LESSON_MAPPINGS. Whenever match occurs,
	  the tag is converted. Private function repaceTags() performs mapping operation..
	  */
	public static function parseLesson(string $data)
	{
		self::initData();                                                        //Make sure that mapping data is initialized.
		
		/*Ignore everything upto the first <Lesson> element and after
		the closing tag of this element*/
		$startTag = "<" . self::$LESSON_MAPPINGS["0"]->emlTag . ">";             //Begin parsing at this tag.
		$endTag = "</" . self::$LESSON_MAPPINGS["0"]->emlTag . ">";              //End parsing at this closing tag.
		
		if (($startPos = stripos($data, $startTag)) === FALSE)                    //Look for index of start tag (case insensitive)
			throw new Exception("Could not parse data. No <Lesson> start tag");  
		
		if (($endPos = stripos($data, $endTag)) === FALSE)                        //Look for index of end tag (case insensitive)
			throw new Exception("Could not parse data. No </Lesson> end tag");
		
		/*Get only lesson data. This will be all data within and including <Lesson> opening
		 and </Lesson closing tag*/
		$data = substr($data, $startPos, $endPos + strlen($endTag));
		self::replaceTags(self::$LESSON_MAPPINGS, $data);                        //Parse lesson tags in $data.   
				
		return $data;
	}
	
	
	/*Somewhat similar approach to parseLesson. The major different is that
	  quiz html is populated with Question objects in order
	  to make it easier to manage this data within the object, including
	  modifying or displaying it. However, EML is still mapped to HTML
	  for any required usage within the caller.*/
	public static function parseQuiz(string $data)
	{
		self::initData();                                                    //Make sure that mapping data is initialized.
		
		$success = FALSE;		
		$result = NULL;
		$startTag = "<\s*" . self::$QUIZ_MAPPINGS["0"]->emlTag . "\s*>";         //Begin parsing at this tag.
		$endTag = "<\/\s*" . self::$QUIZ_MAPPINGS["0"]->emlTag . "\s*>";          //End parsing at this closing tag.
		
		$data = preg_replace("/(\r\n|\r|\n)/", "", $data);                     //Remove all CRLF from $data.
		/*Get only data within quiz element tags. First find indexes of start and end tags
		  and then extract this data.*/
		$success = preg_match("/$startTag/i", $data, $result, PREG_OFFSET_CAPTURE);
		$startIndex = $result[0][1];
		$success = preg_match("/$endTag/i", $data, $result, PREG_OFFSET_CAPTURE);
		$endIndex = $result[0][1];
		
		
		if ($success === FALSE)            //Position of start tag for quiz (case insensitive)
			throw new Exception("Could not parse data. No Quiz tags.");
		
		/*Ignore everything upto the first <Quiz> element and after
		the closing tag of this element when extracting data.*/
		$data = substr($data, $startIndex, $endIndex + strlen($result[0][0]));                    
		self::replaceTags(self::$QUIZ_MAPPINGS, $data);                                         //Parse lesson tags in $data. 
							
		return $data;
	}
	
	
	/*Convenience function for updating the html of a Quiz object given
	  its current questions */
	public static function refreshQuizHTML($oldHTML, $questionHTML)
	{
		self::initData();                                                              //Make sure that mapping data is initialized.
		$startTag = self::$QUIZ_MAPPINGS["3"]->htmlTag;                                  //Tag for Body of quiz.
		$endTag = (explode(" ", self::$QUIZ_MAPPINGS["3"]->htmlTag))[0];                //Only use first part of tag for end tag.
	
		
		/*Create a new body for the quiz with the updated questions */
		$newQuizBody = "<$startTag>$questionHTML</$endTag>";
		
		/*Create array of patterns. 1st pattern will match old section with question.
          Corresponding replacement is new body. Second pattern will match all CRLF.
          Replace CRLF with empty string*/
		$patterns = array("/<\s*$startTag\s*>.+<\/\s*$endTag\s*>/i", "/(\r\n|\r|\n)/");
		$replacements = array($newQuizBody, "");
		
		$oldHTML = preg_replace($patterns, $replacements, $oldHTML);                          
		$pattern = "<\s*$startTag\s*>.+<\/\s*$endTag\s*>";                              
		
		return preg_replace("/$pattern/i", $newQuizBody , $oldHTML);                

	}
	
	
	
	
	/*Creates and returns an array of quiz questions using the
	  provided data. Questions are expected to follow uniform syntax. 
	  For reference, format of EML question is as follows:
	  
		 <MCQuestion>
			 <QuestionHeading> Question N</QuestionHeading>
			 <QuestionText>Question here</QuestionText>
			<WrongOption> <label>a. </label> Wrong choice...</WrongOption>
			 <WrongOption><label>b. </label> Another wrong choice...</WrongOption>
			 <WrongOption><label>c. </label>Another wrong choice...WrongOption>
			 ...
			 <CorrectOption><label>d. </label> The correct choice...</CorrectOption>
			 ...
		 </MCQuestion>
	 */
	public static function createQuizQuestions($data)
	{
		self::initData();                                               //Make sure that mapping data is initialized.
		static $tags = NULL;
		static $patterns = NULL;
		$quizQuestions = array();                                                             //Array to hold questions.
		
		/*If array $tags has not been initialized, set its contents here to the different tag pairs
		  that will be used in patterns */
		if (is_null($tags))
		{
			$tags = array( "questionTag" => self::$QUIZ_MAPPINGS["5"]->emlTag,
							"headingTag" => self::$QUIZ_MAPPINGS["9"]->emlTag,                 
						    "textTag" => self::$QUIZ_MAPPINGS["8"]->emlTag,                   
							"wrongOptionTag" => self::$QUIZ_MAPPINGS["11"]->emlTag,           
							"correctOptionTag" => self::$QUIZ_MAPPINGS["10"]->emlTag,         
							"labelTag" => self::$GENERAL_MAPPINGS["6"]->emlTag);                   
		}
		
		/*Similar to above. If the patterns for quiz elemets have not been initialized, do so here. Start and end tag pairs are matched
		  with at least 1 symbol required between them. However, negatie lookahead is used to prevent leaving this pair and getting unwanted
		  matches on the opening tag of the next pair.*/
		if(is_null($patterns))
		{
			$patterns = array(  "questionPattern" => "<\s*".  $tags["questionTag"] ."\s*>((?!". $tags["questionTag"] . ").)+<\/\s*". $tags["questionTag"]."\s*>",
								"headingPattern" => "<\s*".$tags["headingTag"] . "\s*>((?!". $tags["questionTag"] . ").)+<\/\s*" . $tags["headingTag"]. "\s*>",
								"textPattern" => "<\s*" . $tags["textTag"] . "\s*>((?!". $tags["questionTag"] . ").)+<\/\s*" . $tags["textTag"]. "\s*>",
								"wrongOptionPattern" => "<\s*" . $tags["wrongOptionTag"] . "\s*>((?!".$tags["wrongOptionTag"]  . "|". 
									$tags["questionTag"] . ").)+<\/\s*" . $tags["wrongOptionTag"]. "\s*>",
								"correctOptionPattern" => "<\s*" . $tags["correctOptionTag"] . "\s*>((?!". $tags["questionTag"] . "|". 
									$tags["wrongOptionTag"] . "|" . $tags["correctOptionTag"] . ").)+<\/\s*" . $tags["correctOptionTag"]. "\s*>",
								"tagPattern" => "<\/?\s*[^<>]+\s*>" );
		}
		
				
		$data = preg_replace("/(\r\n|\r|\n)/", "", $data);                                                                         //Remove instances of CRLF.
		 
		/*Get all Multiple Choice Question elements in $data. The 2D array returned by preg_match_all()
		  has the set of complete matches in its first row at index 0 */
		$count = preg_match_all("/". $patterns["questionPattern"] ."/i", $data, $result);
		$matches = $result[0];                                                
		
			
		/*Iterate over each of the matching EML question elements and create corresonpding QuizQuestion objects. */
		foreach($matches AS $key => $curQuestion)
		{
						
			$tempMatches = NULL;
			$properties = array("heading"=>"", "text"=>"", "options"=>"", "correctOption"=>"");                                  //Properties for each question. 
						
			preg_match("/". $patterns["headingPattern"] . "/i", $curQuestion, $tempMatches);                                    //Get the question heading.
			$properties["heading"] = $tempMatches[0];                                                                           //Remove tags.
			preg_match("/". $patterns["textPattern"] . "/i", $curQuestion, $tempMatches);                                       //Get the question text.
			$properties["text"] = $tempMatches[0];  
			$properties = preg_replace("/".$patterns["tagPattern"] . "/", " ", $properties);                                    //Remove all tags from elements so far.
					
			/*Get all options. These are <correctoption> and <wrongoption>*/			
			preg_match_all("/(" . $patterns["wrongOptionPattern"] . ")|(" . $patterns["correctOptionPattern"] . ")/i", $curQuestion, $tempMatches);    
			$properties["options"] = $tempMatches[0];                                                                          //Set to the matching array of option strings.
			
			preg_match("/" . $patterns["correctOptionPattern"] . "/i", implode(" ", $properties["options"]), $tempMatches);    //Get the correct option element.
			
			preg_match("/(<\s*" . $tags["labelTag"] . "\s*>\s*[^<>]+\s*<\/\s*" . 
				$tags["labelTag"] . "\s*>)/i", $tempMatches[0], $tempMatches);                                                  //Extract the label elem of the correct option
			$properties["correctOption"] = preg_replace("/(" .$patterns["tagPattern"]. ")|([\.\s]+)/", "", $tempMatches[0]);    //Remove tags AND any periods.
			
						
			$properties["options"] =  preg_replace("/" .$patterns["tagPattern"]. "/", " ", $properties["options"]);            //Remove all tags from question options.
			
			array_push($quizQuestions, new QuizQuestion($properties["heading"], $properties["text"],
				$properties["options"], $properties["correctOption"]));                                            //Push QuizQuestion object onto $quizQuestions. Use key as unique id.
		} //End-foreach
		
		return $quizQuestions;
	}
	
		
	/*Replaces tags within a set of EML elements in a string using an array of EML:HTML tag pairs
	  which specifies the replacements.*/
	private static function replaceTags(&$pairs, &$data)
	{
		foreach($pairs AS $key => $curPair)
		{
			
			/*Special case for <location> elements. These map to anchor elements, so href attribute must be set using
			  the contents of the original <location> element */
			if (preg_match("/^\s*location\s*/i", $curPair->emlTag) === 1)
            {
				/*Use callback to set href attribute of <a> which is derived from text contents
				  of original <location> */
				$data = preg_replace_callback("/<\s*location[^>]*\s*>[^<>]*<\/location>/i", 'self::processLocationElem', $data);
			}
			
			/*Another special case for <SectionHeading> and <Subsection> elements. We want the id attribute to be set in the
			  corresponding html element.*/
			elseif (preg_match("/^\s*(SectionHeading$)|(Subsection$)\s*/i", $curPair->emlTag) === 1)
            {
				/*Use callback processSectionHeading() to set attributes if the current tag
				  being processed is a SectionHeading or a Subsection element */
				$data = preg_replace_callback("/<\s*".$curPair->emlTag."[^>]*\s*>[^<>]*<\s*\/".$curPair->emlTag."\s*>/i", function($matches) use($curPair)
				{
					return self::processSpecialElem($matches, $curPair);
				}, $data);
			}
			
			/*Last special case is for <Image> tags which are converted to <img> tags. The src and alt attributes must be set
			  in the <img> element. */
			elseif (preg_match("/^\s*Image\s*/i", $curPair->emlTag) === 1)
            {
				/*Use callback  processImageElem() to set href attribute of <a> which is derived from text contents
				  of original <location> */
				$data = preg_replace_callback("/<\s*Image\s*>[^<>]*<\s*\/Image\s*>/i", "self::processImageElem", $data); 
			}
			
			/*For any other tags, we want to retain attributes and perform conversion, without any other changes */
			else
			{				
				$closingTag = explode(" ", trim($curPair->htmlTag))[0];                                                      //Form closing tag by stripping off any attributes and adding a '/'
				
				/*Replace opening tags (case insensitive) first part but don't remove attributes.
				  Note that "use" keyword allows acces to $curPair within scope of anonymous function*/
				$data = preg_replace_callback("/<\s*" . $curPair->emlTag . "\s*>/i", function($matches) use($curPair) {
				    
					static $curMatch = -1;                                                                         //Static variable for index into $matches. Incremented after each call.
					$curMatch++;
					if ($curMatch >= count($matches))
						$curMatch = 0;
						
					$completeMatch = $matches[$curMatch];
					
					return preg_replace("/<\s*". $curPair->emlTag . "/i", "<". $curPair->htmlTag . " ", $completeMatch);  //Strip off element name and opening "<" from old tag, replace with <$htmlTag.
				}, $data);
				
				$data = preg_replace("/<\/\s*" . $curPair->emlTag . "\s*>/i", "</$closingTag>", $data);               //Replace closing tags for this pair.
			}
		}
	}
	
	
	private static function processLocationElem($matches)
	{
		$output = NULL;
		static $curMatch = -1;                                                               //Static variable for index into $matches. Incremented after each call.
		$curMatch++;
		if ($curMatch >= count($matches))
			$curMatch = 0;
			
		$location = $matches[$curMatch];
		$linkMatches = array();
		
		/*Attempt to find link attribute in location elem */
		if (preg_match("/link\s*=\s*\"?[^<>]+\"?/i", $location , $linkMatches) )
		{
			$href = strtr($linkMatches[0], array("link" =>"", "\""=>"", "\'" => ""));               //Remove all superfluous or invalid chars.
			$href = preg_replace("/(^\s*=)|(\s*)/", "", $href);                                  //Remove any "=" chars at beginning of sequence and all spaces.
			
			$userString = preg_replace("/(<\s*location[^>]*>)|(<'s*\/location>\s*)/i", "", $location);  //Remove <location> tags from match to get text visible to user.
			$output = "<a href=\"$href\">$userString</a>";  
		}
			
		return $output;                                                //return the replacement here in <a> with href set.
	}
	
	
	private static function processSpecialElem($matches, $curPair)
	{
		$emlTag = $curPair->emlTag;
		$htmlTag = $curPair->htmlTag;
		$output = NULL;
		static $curMatch = -1;                                                                               //Incremented on each call for next match index in $matches.                              
		$curMatch++;
					
		if ($curMatch >= count($matches) )
			$curMatch = 0;
						
		$heading = $matches[$curMatch];
		$idMatches = array();
		if (preg_match("/id\s*=\s*\"[^<>]+\"/i", $heading, $idMatches) === 1)
		{
			$id = $idMatches[0];                                                                                //Get id attribute from match.
			$id = strtr($id, array("=" => "", "\"" => "", "id"=>""));                                          //Remove extra parts of string
			$heading = preg_replace("/(<\s*".$emlTag."[^>]*>)|(<\s*\/".$emlTag."\s*>)/i", "", $heading); //Remove <SectionHeading> tags from match.
			$output = "<$htmlTag id = $id>$heading</$htmlTag>";
		}
		return $output;       														//return the replacement here in <a> with href set.
	}			
	
	
	/*Callback which processes a matching EML image tag within array $matches. Creates an html <img> tag with src and alt 
	  attributes set. Each subsequent call processes the next Image element until there are no matches left in $matches.*/
	private static function processImageElem($matches)
	{
		$output = NULL;
		static $curMatch = -1;                                                                       
		$curMatch++;                                                                               //Increment $curmatch to get index of next match.
		if ($curMatch >= count($matches))
			$curMatch = 0;
			
		$image = $matches[$curMatch];
		$src = preg_replace("/(<\s*Image\s*>)|(<\s*\/Image\s*>)/i", "", $image);                   //Remove <Image> tags from match to get image source.
					
		$imageNameMatches = array();
		if (preg_match("/([^\/\.]+\.[^\/\.]+)$/i", $src, $imageNameMatches) === 1)                          //Extract file name from path for $src. 
		{
			$imageName = $imageNameMatches[0];
			$output = "<img src =\"$src\" alt =\"$imageName\"/>";
		}
					
		return $output;                            
	}
	
	
	/*Initializes static class members, if required. Required for non-primitive types which cannot be initialized at
      compile time. Mapping data is "hard-coded" here but could be loaded from file or db instead for more flexibility.*/
	private static function initData()
	{
		if (is_null(self::$GENERAL_MAPPINGS))
		{
			self::$GENERAL_MAPPINGS = array("0" => new Pair("PARAGRAPH", "p"),
				"1" => new Pair("Summary", "div id = \"summary\""),
				"2" => new Pair("Section", "article"),
				"3" => new Pair("Subsection", "section"),
				"4" => new Pair("SectionHeading", "h3"),
				"5" => new Pair("SubsectionHeading", "h4"),
				"6" => new Pair("Label", "label"));
		}
		
		if(is_null(self::$QUIZ_MAPPINGS))
		{
			self::$QUIZ_MAPPINGS = array("0" => new Pair("QUIZ", "div id = \"quiz\""),
				"1" => new Pair("Title", "h2"),
				"2" => new Pair("Instructions", "div id=\"instructions\""),
				"3" => new Pair("QuizBody", "main id=\"quiz-body\""),
				"4" => self::$GENERAL_MAPPINGS["0"],
				"5" => new Pair("MCQuestion", "div class = \"quiz-question\""),
				"8" => new Pair("QuestionText", "p"),
				"9" => new Pair("QuestionHeading", "h3"),
				"10" => new Pair("CorrectOption", "input type=\"radio\" id=\"correct_option\""),
				"11" => new Pair("WrongOption", "input type=\"radio\""),
				"12" => self::$GENERAL_MAPPINGS["6"]);
		}
		
		if(is_null(self::$LESSON_MAPPINGS))
		{
			self::$LESSON_MAPPINGS = array("0" => new Pair("Lesson", "main id = \"lesson\""), 
				"1" => new Pair("Title", "h2"), 
				"2" => self::$GENERAL_MAPPINGS["1"],
				"3" => self::$GENERAL_MAPPINGS["2"],
				"4" => self::$GENERAL_MAPPINGS["5"],
				"5" => self::$GENERAL_MAPPINGS["4"],
				"6" => self::$GENERAL_MAPPINGS["3"],
				"7" => new Pair("id", ""),
				"8" => self::$GENERAL_MAPPINGS["0"],
				"10" => new Pair("CodeExample", "div class=\"codeExample\""),
				"11" => new Pair("QuizLink", "aside"),
				"12" => new Pair("TermList", "dl"),
				"13" => new Pair("OutputFigure", "figure"),
				"14" => new Pair("CodeText", "code"),
				"15" => new Pair("Term", "dt"),
				"16" => new Pair("Definition", "dd"),
				"17" => new Pair("Image", "img"),
				"18" => new Pair("Caption", "figcaption"),
				"19" => new Pair("Location", "a")); 
		}	
			
	}
		
}




class Pair
{
	function __construct(string $emlTag, string $htmlTag)
	{
		$this->emlTag = $emlTag;
		$this->htmlTag = $htmlTag;
	}
	
	
	function __destruct()
	{}
	
	public $emlTag;
	public $htmlTag;

}





?>