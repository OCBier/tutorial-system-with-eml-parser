/* The approach here is to load the template from TEMPLATE_LOC using global TEMPLATE_MODULE.TemplateLoader.
  TemplateLoader uses TEMPLATE_MODULE.QuizMaker() as a callback to create the quiz following template load.
  The quiz is created using json data at PATH.*/

window.addEventListener("load", function() {
	const QUIZ1_PATH = "quiz_questions/quiz1.JSON";
	const TEMPLATE_LOC = "template.html";
	
			
	/*Create the template loader and pass a callback which creates quiz 1 quiz using TEMPLATE_MODULE.QuizMaker.
	 Callback will be invoked by templateLoader after template is loaded by calling TEMPLATE_MODULE.templateLoader.start() */
	var templateLoader = new TEMPLATE_MODULE.TemplateLoader(TEMPLATE_LOC, function() {
	 /*Create the quiz and pass path of JSON data for quiz 1 questions and DOM element in which
	   to insert the quiz html to QuizMaker constructor */
		let quiz1 = new TEMPLATE_MODULE.QuizMaker(QUIZ1_PATH, "quiz1");
		quiz1.showQuiz();                        //Call showQuiz() to load and show the quiz.
			 
	 /*Prevent default form submit action on quiz form. Then call method gradeQuiz() to
	   grade the quiz and display results.*/
		 quiz1.setFormSubmitHandler(function(ev){
			quiz1.gradeQuiz();
			ev.preventDefault(); 
			}, true);
	});
		templateLoader.start()                   //Call templateLoader.start() to begin loading template
});