var TEMPLATE_MODULE = (function($app) {	
	/*Creates quizzes from JSON data which is loaded from a specified file using an AJAX request. 
	The quiz is created using a form element which can be submitted. Quizzes are graded and the 
	result for each question is provided when the user clicks submit. The total score for the quiz 
	is also calculated and then displayed. 
	@param path the location of the JSON file
	@param target DOM elment within which quiz should be created */
	$app.QuizMaker = function(path, targetID)
	{
		this.location = path;       //Location for JSON data
		this.loaded = false;       //Is JSON loaded? False initially.
		this.targetID = targetID;
		this.quizData = null;       //Private member for loaded quiz data.
				
		this.quizForm = document.createElement("form");      //Create form element for quiz.
				
		document.getElementById(this.targetID).appendChild(this.quizForm);       //Append the quiz form to targetElement(target).
		
		this.outcomeDiv = document.createElement("div");                              //Element for holding summary 
		
		/*Loads the JSON data from the specified location in param target and displays it as HTML
		  in the DOM element specified by param target */
		this.loadJSON = function(callback)
		{
			try
			{
				let request = new XMLHttpRequest();
				let callback = processJSON.bind(this);    //Ensure callback is called in this context.
				request.addEventListener("readystatechange", function() {
					callback(request);
					});
				
				request.responseType = "text";
				request.open("GET", this.location, true);         //Asynchronous GET request for JSON data at "path".
				request.send(null);
			} catch (ex)
			  {
				console.log("Error while loading JSON data: \n" + ex.toString());
			  }
		}
		
		
		/*Used as callback in loadJSON. Handles processing of
		  JSON data which is obtain by the XMLHttpRequest. */
		let processJSON = function(xhr)
		{
			if (xhr.readyState === 4 && xhr.status === 200)
			{
				this.quizData = JSON.parse(xhr.response);      //Parse returned DOMString to create array of objects and store in quizData.
				this.loaded = true;                           //Set loaded flag to true.
				createQuiz.call(this);                         //Call createQuiz() in this context to add appropriate HTML to target.
			}
		}
		
		/*Create the different quiz questions from the loaded data. This includes a title for each question, the question itself,
		  different answer options, and a div for the question answer. Also append an element for showing outcome of quiz.
		  */
		let createQuiz = function()
		{
			this.quizForm.innerHTML = "<p>Choose the best option from each of the following: </p>";    //Clear the quiz form
			
			/*Iterate over the different question objects in the json data 
			  and create a question for each within the quiz */
			this.quizData.forEach(function(jsonElem)
			{
				let curQuestion = document.createElement("div");  //Create div for current question.
				curQuestion.className = "quiz-question";          //Set classname to quiz-question.
				curQuestion.id = jsonElem.id;                     //Set id attribute of curQuestion to id property of object. 
				
				/*Add header for question number. Note that question number is id + 1, as id's have zero-based index.
				  id is an integer, so 1 must be added before it is appended to the string. Otherwise, the id will be treated
				  appended to the existing string. Then 1 will also be appended to the string. But not addition would take place. */
				curQuestion.innerHTML = "<h3> Question " + (jsonElem.id + 1) + "</h3>";               
				curQuestion.innerHTML += "<p>" + jsonElem.question + "</p>";    //Add question itself in a <p> element.
				
				var questionOptions = "";
				
				for (let i = 0; i < jsonElem.answers.length; ++i)
				{
					let curAnswer = jsonElem.answers[i];         //Current answer object.
					
					/*Add several radio input elements for each option in the question. The structure of the question options is as follows:
					  a. RADIO SELCTOR some question option
					  b. RADIO SELCTOR another options 
					  c. .....
					  n. RADIO SELCTOR last question option. */
					  
					//Create a <p> element holding the question.
					questionOptions += "<p>";
					questionOptions += "<label for = \"answer_"+jsonElem.id + "_" + curAnswer.letter + "\"/>" +
						curAnswer.letter + ". " + "</label>";
					questionOptions += "<input type = \"radio\" name = \"q_" + jsonElem.id + "\"" +
						"value = \"" +curAnswer.letter + "\" id = \"answer_"+jsonElem.id + "_" + curAnswer.letter + "\"/>";
					questionOptions += " " + curAnswer.value +"</p>";
				}  //End-for
							
				curQuestion.innerHTML += questionOptions;           //Append html string holding all question options for the current question.
				
				let questionResult = document.createElement("div"); //Append an (initially) invisible div holding result.
				questionResult.style.visibility = "hidden";      //Make result div invisible initially
				questionResult.className = "result";               //Set classname to "result".
				curQuestion.appendChild(questionResult);           //Append questionResult div for curQuestion.
				
				this.quizForm.appendChild(curQuestion);                 //Append curQuestion to the quiz form (quizForm).
			}, this); //End-forEach() 
			
			/*Create and append a submit button to the quiz form */
			let submitButton = document.createElement("button");
			submitButton.type = "submit";
			submitButton.value = "Submit Quiz";
			submitButton.id = "submit-button";
			submitButton.name = "submit-button";
			submitButton.innerHTML = "submit quiz";
			
			this.quizForm.appendChild(submitButton);
			
			/*Create an element for showing overall outcome of quiz and store reference in outcomeDiv */
			this.outcomeDiv.id = "outcome";
			this.outcomeDiv.style.visibility = "hidden";            //Make outcome div hidden initially.
			document.getElementById(this.targetID).appendChild(this.outcomeDiv);        //Append the invisible div for result of quiz to the target.
			
		}//End-function createQuiz()

	}
	
	/*Returns true if quiz is loaded, false otherwise. */
	$app.QuizMaker.prototype.isLoaded = function()
	{
		return this.loaded;
	}
	
	/*Displays the quiz within the target DOM element specified in ctor. */
	$app.QuizMaker.prototype.showQuiz = function()
	{
		this.loadJSON.call(this);             //Call loadJSON in this context to get the JSON data and display it as HTML.
	}
	
	
	/*Grades the quiz by checking answer to each completed question against the correct answer
	  stored in the JSON data. The total score is then displayed*/
	$app.QuizMaker.prototype.gradeQuiz = function()
	{
		//Make sure quiz is actually loaded.
		if (!(this.isLoaded()))
			return null;
			
		let score = 0;                                                     //Counter for number of correct questions.
		let questionElems = this.quizForm.querySelectorAll(".quiz-question");   //Get all questions.
		
		
		questionElems.forEach(function(curQuestion)
		{
			let resultDiv = curQuestion.querySelector(".result");                                   //Get result div in current question.
			let radioGroupName = "q_"+curQuestion.id;												//Note name is "q_"+ id of cur question.
			
			let checkedInput = curQuestion.querySelector("input[name =\"" + radioGroupName +"\"]:checked");      //Get selected input from current question.
			
			/*If no answer given (no checked input), then indicate this in 
			  resultDiv for this question and return null */
			if (checkedInput == null) 
			{
				resultDiv.innerHTML = "<p> You did not answer this question!</p>";
				resultDiv.classList.remove("correct");                 //Remove class "correct", if necessary
				resultDiv.classList.add("incorrect");                  //Add class "incorrect"
			}	
			/*Otherwise use the input value on checked question. This implies that
			  an answer was provided */
			else
			{
				let selectedAnswer = checkedInput.value;                                              
				
				/*Display the selected answer as well as the correct answer stored in this.quizData */
				resultDiv.innerHTML = "<p> Your answer: " + selectedAnswer; + "</p>"
				resultDiv.innerHTML += "<p> Correct answer: "+ this.quizData[curQuestion.id].correctAnswer + "</p>";
				
				if (selectedAnswer == (this.quizData[curQuestion.id].correctAnswer) )                 //If answer is correct
				{
					resultDiv.classList.remove("incorrect");               //Remove class incorrect, if necessary
					resultDiv.classList.add("correct");                    //Toggle class correct to add it if div doesn't have this class
					++score;                                               //Increment score.
				}
				
				else                                                       //Otherwise, answer must be incorrect
				{ 
					resultDiv.classList.remove("correct");                 //Remove class "correct", if necessary
					resultDiv.classList.add("incorrect");                  //Add class "incorrect"
				}
				
			}//End-else
			
			resultDiv.style.visibility = "visible";                   //Make result element visible in each question.
			
		}, this);                                                     //Note value of this in outer context is passed as second arg to forEach().
		
		/*Set content of outcomeDiv to show result of entire quiz and make outcomeDiv visible. */
		let percentScore = Math.round( (score/questionElems.length) * 100 );
		this.outcomeDiv.innerHTML = "<p> Your score: " + score + "/" + questionElems.length + " (" + percentScore + "%) </p>";
		
		this.outcomeDiv.style.visibility = "visible";                      
		
		window.scrollTo(0, document.body.scrollHeight);              //Scroll down to bottom of body (y coordinate is body.scrollHeight)
		
		
	}
	
	/*Add handler for form submit event. 
	 @param callback Callback which fires when submit occurs
	 @capture (optional) Specify whether handler is for bubbling phase
	 or capture phase (if capture is true). Default is false (bubbling phase).*/
	$app.QuizMaker.prototype.setFormSubmitHandler = function(callback, capture)
	{
		if (typeof capture === 'undefined' || capture === false)
		{
			this.quizForm.addEventListener("submit", function(ev) {
				
				callback(ev);
				}, false);
		}
		
		//If we reach this point, then capture is true, so set handler for capturing phase.
		else 
		{
			this.quizForm.addEventListener("submit", function(ev) {
				
				callback(ev);
				}, true);
		}
	}
	
	/*Set the action attribute of the quiz form. Specifies location to which form
	  should be submitted. */
	$app.QuizMaker.prototype.setFormAction = function(actionLocation)
	{
		this.quizForm.action = actionLocation;
	}
	
	
	return $app;                //Return reference to modified (or defined) module here.
	
})(TEMPLATE_MODULE || {});
