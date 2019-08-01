/*Script which loads template from specified html document and applies it to a target document. The content of the document body
  is inserted into the template DOM, which becomes the new content of the original document body.
  After creating the document, an optional callback may be called. */
  
  
var TEMPLATE_MODULE = (function($template) 
{
	$template.TemplateLoader = function(location, optionalCallback)
	{
		this.templateLocation = location;
		this.callback = optionalCallback;
		this.template = null;
		
		this.start = function()
		{
			let boundCreateDocument = createDocument.bind(this);
			loadTemplate(function(xhr) {
				boundCreateDocument(xhr);
			});
		}
		
		
		/*Loads template from html document at location TEMPLATE_LOCATION and stores it to be accessed by variable
		  template.*/
		let loadTemplate = function(func)
		{
			try
			{
				var request = new XMLHttpRequest();
				request.addEventListener("readystatechange", function() {
					func(request);
				}, false);
				request.open("GET", location, true);
				request.send(null);
			} catch(ex)
			  {
				alert("Error loading page");
				alert(ex.toString());
			  }
		
		}
		
		
		/*Inserts content of the original document into the template. The result then becomes the content of the
		  original document body. */
		let createDocument = function (xhr)
		{
			
			if (xhr.readyState === 4 && xhr.status === 200)
			{
				this.template = document.createElement("div");
				this.template.innerHTML = xhr.responseText;
				
				//Set innerHTML of #content element in template to content of original body.
				this.template.querySelector("#content").innerHTML = document.body.innerHTML;    
				
				//Set document body to the augmented template
				document.body.innerHTML = this.template.querySelector("#template").outerHTML;
				
				//Call the optional callback, if it is defined
				if (!(typeof this.callback === 'undefined'))
				{
					this.callback();
				}
				
				setTimeout(function()                                         //Allow 1 second delay to allow content to be prepared before making body visible.
				{
					document.body.style.visibility = "visible";                 //Make body visible after all elements are created.
				}, 1000);
									
			}//End-if
		}
	}
	
	return $template;
	

}(TEMPLATE_MODULE || {} ));