﻿<?php
error_reporting(0);
include_once("Template.php");

$intro = "
		<section id = \"welcome\" >
		<p>Welcome to my front-end web development tutorial site. Here you will find helpful information on 
		HTML and CSS, as well as JavaScript and AJAX with JSON.</p>
		
		<p>My main goal is to show you some of the techniques that I've found most effective. Although this is not intended to be a 
		comprehensive guide, I hope that you will learn something useful or gain a better understanding of an approach you have already encountered. </p>
		
		<p>To get started, use the navigation menu on the left to select a tutorial. I recommend following the tutorials in sequential order. In general, 
		using CSS to control appearance and layout requires an understanding of HTML. JavaScript requires an understanding of both, to create dynamic pages. 
		But you are free to follow any order, especially if you already have a solid understanding of one or more areas. Once you've completed a tutorial, 
		you can use its corresponding quiz to check your understanding.  </p>
		</section>";

createTemplate($intro);               //Show intro in the template.
		

?>