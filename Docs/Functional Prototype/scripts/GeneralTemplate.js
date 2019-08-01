/*Uses TEMPLATE_MODULE.TemplateLoader to load the basic template. Any existing html within the
  document is inserted into the template. */
  
window.addEventListener("load", function(){

		const TEMPLATE_DOC = "template.html";
					
		/*Create the template loader. No callback in TemplateLoader is required, so no second argument
		  is passed to constructor. Call TemplateLoader.start() when TemplateLoader object been created.
		  Tis begins loading and writes template to DOM.*/
		(new TEMPLATE_MODULE.TemplateLoader(TEMPLATE_DOC)).start();
				
});