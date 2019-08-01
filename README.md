# tutorial-system-with-eml-parser

A small-scale learning management system which will present different components of courses to students/learners. 

Course content is formatted in an educational markup language (EML) which can be parsed and output as the corresponding HTML.



## Use Cases and Scenarios

-See Docs/Scenarios.pdf [Docs/Scenarios.pdf](https://github.com/OCBier/tutorial-system-with-eml-parser/blob/master/Docs/Scenarios.pdf)
 and [Docs/Use_Cases.png](https://github.com/OCBier/tutorial-system-with-eml-parser/blob/master/Docs/Use_Cases.png)



## System Design 

-See [Docs/Class_Design.pdf](https://github.com/OCBier/tutorial-system-with-eml-parser/blob/master/Docs/Class_Design.pdf)





## Educational Markup Language Design


Note that tag names are NOT case-sensitive.

**Quiz Elements**

-See [Docs/Quiz_Diagram.png](https://github.com/OCBier/tutorial-system-with-eml-parser/blob/master/Docs/Quiz_Diagram.png)

*Quiz*
    Represents the entire quiz with multiple choice questions. Acts as the parent for all quiz elements.

*Title*
    1 instance in parent. Text element containing the title of the quiz (Eg. Lesson 1 Quiz) in this context.
    
-*Instructions*
    1 instance in parent. Text element which gives instructions to the learner about how to complete the quiz. May also contain any points that need to be highlighted to ensure quiz completion.
    
*QuizBody*
    1 instance in parent. The main section of the quiz containing 1 or more multiple choice questions.
    
*MCQuestion*
    1 or more instances in parent. A multiple choice question.
    
*QuestionHeading*
    1 instance in parent. A label for a specific question. Should uniquely identify question with a numeric value starting indexed from 1.
    
*QuestionText*
    1 instance in parent. The text of the question which the learner must answer.
    
-*CorrectOption*
    1 instance in parent. Selectable text element which represents the correct option for this question.
    
-*WrongOption*
    1 or more instances in parent. Selectable text elements which hold the wrong answers(s) in this question.
    
-*Label*
    1 instance in parent. Each CorrectOption and WrongOption element must have a label which is a text element containing a number, letter, or other marker which uniquely distinguishes the element within the context of a specific MultipleChoiceQuestion.


**Lesson Elements**

See [Docs/Lesson_Diagram.png](https://github.com/OCBier/tutorial-system-with-eml-parser/blob/master/Docs/Lesson_Diagram.png)
-*Lesson*
    Element which represents an entire lesson. Parent element for all other elements in the lesson.
    
-*Title*
    1 instance in parent. Text element containing title of the lesson in this context.
    
-*SectionHeading*
    1 instance as direct descendent in parent. Contains a descriptive marker for the beginning of a Section
    
-*SubSectionHeading*
    1 instance as direct descendent in parent. Contains a descriptive marker for the beginning of a Subsection
    
-*Summary*
    1 instance as direct descendent in parent. Text element which summarizes the content of the parent Lesson or Section.
    
-*Section*
    1 or more instances in parent. Represents a main section within the lesson.
    
-*Subsection*
    1 or more instances in parent. Represents a subsection within a Section. This is typically used to focus on some specific aspect of the topic of the Section.
    
-*Paragraph*
    1 or more instances in parent. A paragraph of text. Typically used in a Summary or Subsection. In the latter it describes the topic of the subsection.
    
-*CodeExample*
    1 or more instances in parent. An example which illustrates some concept for a Subsection topic. May contain an OutputImage (see OutputImage below).
    
-*OutputFigure*
    n instances in parent. Container element which holds an Image and a Caption element (see Image and Caption elements below). This demonstrates the output for the CodeText element of a CodeExample (see CodeText below).
    
-*CodeText*
    1 instance in parent. Source code in a CodeExample element. Used to illustrate some concept.
    
-*Image*
    1 instance in parent. A graphical element in a OutputFigure container which shows the output of a CodeText element. Holds the location (relative or absolute URL) of the image file.
    
-*Caption*
    1 instance in parent. A caption describing the content of an image. Used in an OutputFigure element in this case to describe an Image which displays the output of the CodeExample's CodeText element.
    
-*QuizLink*
    1 instance in parent. An element which directs learners to the quiz for a Lesson. Contains a paragraph and a Location element which holds link to quiz.
    
-*Location*
    n instances in parent. Contains the location (relative or absolute URL) of some resource.
    
-*TermList*
    n instances in parent. Represents a list of terms with definitions.
    
-*Term*
    1 or more instances in parent. Child of a TermList element. Represents a term to be defined.
    
-*Definition*
    1 or more instances in parent. Child of a TermList element. Represents the definition of a preceding Term. Must follow directly after a Term element.


