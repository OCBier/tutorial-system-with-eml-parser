/*When content has finished loading, use global TEMPLATE_MODULE.TemplateLoader to load template from html document at TEMPLATE_LOC.
  TEMPLATE_MODULE.QuizMaker() is invoked in callback within TemplateLoader so that quiz can be created after template has been loaded.
  The quiz is created using json data at PATH.*/
window.addEventListener("load", function() {
	const PATH = "quiz_questions/quiz2.JSON";
	const TEMPLATE_LOC = "template.html";
	
			
	/*Create the template loader and pass a callback which creates quiz 1 quiz using TEMPLATE_MODULE.QuizMaker.
	 Callback will be invoked by templateLoader after template is loaded by calling TEMPLATE_MODULE.templateLoader.start() */
	var templateLoader = new TEMPLATE_MODULE.TemplateLoader(TEMPLATE_LOC, function() {
	 /*Create the quiz and pass path of JSON data for quiz 1 questions and DOM element in which
	   to insert the quiz html to QuizMaker constructor */
		let quiz = new TEMPLATE_MODULE.QuizMaker(PATH, "quiz2");
		quiz.showQuiz();                        //Call showQuiz() to load and show the quiz.
			 
	 /*Prevent default form submit action on quiz form. Then call method gradeQuiz() to
	   grade the quiz and display results.*/
		 quiz.setFormSubmitHandler(function(ev){
			quiz.gradeQuiz();
			ev.preventDefault(); 
			}, true);
	});
		templateLoader.start()                   //Call templateLoader.start() to begin loading template
});