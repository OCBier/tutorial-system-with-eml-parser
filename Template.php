﻿<?php
function createTemplate($pageContents)
{
?>
<!DOCTYPE html>

<!--Provides a template for the different pages of tutorials. This allows a uniform look-and-feel with similar banner
    and menus for different pages. -->
<html>
	<head> 
		<meta charset = "utf-8"/>
		<title>Web tutorials</title>
		<link rel="stylesheet" type = "text/css" href="https://fonts.googleapis.com/css?family=Inconsolata"/>
		<link rel="stylesheet" type = "text/css" href="https://fonts.googleapis.com/css?family=Source+Code+Pro"/> 
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Serif"/>
		<link rel = "stylesheet" type = "text/css" href = "/style.css"/> 
		<script type = "text/javascript" src = "scripts/ShowBody.js"></script>
	</head>

	<body class = "hidden">
		<div id = "template">
		
			<!--Banner for site -->
			<header id = "tutorial-banner" >
				<h1>client-side web development tutorials </h1>
			</header>
			
			<!--Wrapper for main menu and custom content. -->
			<div id="wrapper">
				<!--Container for all menu items and submenus. This is a vertical flex container. Note that class vertical-flex is not used since
					this container must be displayed inline. Therefore rules are defined for this element as a special case, rather than the general class.
					Therefore it has class vertical-flex-inline-->
				<div id = "menu-container" class = "vertical-flex-inline">
					<span class = "menu-item"><a href = "./" title = "Home Page">Home</a></span>
					
					<h2 class = "submenu-title">tutorials</h2>
					<!--Submenu for tutorials. This is an item within main menu flex container, parent div, but is itself a flex
					  container as well -->
					<nav id = "tutorials-submenu" class = "vertical-flex">
						<span class = "menu-item"><a href = "lessondisplay.php?contentid=10" title = "Section 1: HTML and CSS">HTML and CSS</a></span>
						<span class = "menu-item"><a href = "lessondisplay.php?contentid=11" title = "Section 2: JavaScript">JavaScript</a></span>
						<span class = "menu-item"><a href = "lessondisplay.php?contentid=12" title = "Section 3: AJAX and JSON">AJAX with JSON</a></span>
					</nav>
					
					<h2 class = "submenu-title">quizzes</h2>
					<!--Submenu for different quizzes. Also a flex container, like #tutorial-submenu -->
					<nav id = "quizzes-submenu" class = "vertical-flex">
						<span class = "menu-item"><a href = "QuizDisplay.php?contentid=6" title = "Unit 1 Quiz">Unit 1 Quiz</a></span>
						<span class = "menu-item"><a href = "QuizDisplay.php?contentid=7" title = "Unit 2 Quiz">Unit 2 Quiz</a></span>
						<span class = "menu-item"><a href = "QuizDisplay.php?contentid=8" title = "Unit 3 Quiz">Unit 3 Quiz</a></span>
					</nav>
				</div>
				
				
				<!--Custom page content goes here. Note that content is displayed inline with menu within an inline column flex container -->
				<div id = "content" class= "vertical-flex-inline">
				<?php echo $pageContents; ?>
				</div>
		
			</div>
			
			
			<footer>|</footer>
		</div>
		
	</body>
</html>

<?php
}   //End function createTemplate()

?>
