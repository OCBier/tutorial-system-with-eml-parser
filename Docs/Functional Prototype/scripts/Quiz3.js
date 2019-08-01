/*Loads the template using Template_Module.TemplateLoader. Template document is found at TEMPLATE_LOC.
  Quiz is created after template is loaded by using QuizModule.QuizMaker(). Quiz question JSON is loaded from
  PATH within QuizMaker. The appropriate JSON data is used for each quiz question. */

window.addEventListener("load", function() {
	const PATH = "quiz_questions/quiz3.JSON";
	const TEMPLATE_LOC = "template.html";
	
			
	/*Create the template loader and pass a callback which creates quiz 1 quiz using TEMPLATE_MODULE.QuizMaker.
	 Callback will be invoked by templateLoader after template is loaded by calling TEMPLATE_MODULE.templateLoader.start() */
	var templateLoader = new TEMPLATE_MODULE.TemplateLoader(TEMPLATE_LOC, function() {
	 /*Create the quiz and pass path of JSON data for quiz 1 questions and DOM element in which
	   to insert the quiz html to QuizMaker constructor */
		let quiz3 = new TEMPLATE_MODULE.QuizMaker(PATH, "quiz3");
		quiz3.showQuiz();                        //Call showQuiz() to load and show the quiz.
			 
	 /*Prevent default form submit action on quiz form. Then call method gradeQuiz() to
	   grade the quiz and display results.*/
		 quiz3.setFormSubmitHandler(function(ev){
			quiz3.gradeQuiz();
			ev.preventDefault(); 
			}, true);
	});
		templateLoader.start()                   //Call templateLoader.start() to begin loading template
});