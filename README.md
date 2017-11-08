# web-calendar_php
A web calendar app done with PHP/MySQL/JQuery to be redone later with Node / React
This calendar app was adapted from a book, Pro PHP and JQuery, I was reading to catch up on PHP skills. I chose PHP as the platform because while it is old and a bit cumbersome writing, it is used by a vast majority of web hosts in the US and, since I have a few projects I'm working on for a few organizations whose websites run PHP, I need to get my refresher on.
I will reproduce the calendar project as it is in the book, with some tweaking (using mysqli instead of PDO, which didn't work for me for some reason), but there are other places I will go back and reconfigure altogether. The sample in the book relies heavily on one class to do just about all the work, including producing the HTML for various forms and the calendar structure itself. I would distribute that work out more among various helper classes and the php output files themselves. But doing it the way its done here, the forms can be run through both PHP and Javascript as HTML and not have extra form production (cool, unless the form is really lengthy). In this model, since the Calendar class does all the work of creating the HTML, there's no need for JSON transfer betweeen it and javascript. I'm already moving some of the forms out into their own includes.
Notes:
*   I like the structure of the the calendar and admin classes and how they tie into each other
*   There's some presentation issue with the formatting of day boxes that are not calendar dates. While view source code shows the right coding, the DOM is actually doing something different. Have to check CSS and how calendar is built.
*   NO MONTH/YEAR NAVIGATION: The directions for this Calendar app keeps the month static throughout the project and there is NO coverage on how to navigate to different months or years. Guess they said that's up to you to figure out. So I did. The month and year navigation buttons well so far, can navigate months and years.
*   Added some automation to some of the SQL statements as well as collecting the add-event form data (used associative arrays)
*   Added a few lines so that if the event title exceeds one line in the calendar, it will be truncated and "..." added after. Using the string length method works but isn't the best since it doesn't compensate for proportionally spaced fonts. I can either make the event title a monospaced font (which I'll do for now), or (better) see if CSS can handle this issue better than PHP.
*   Need to limit the amount of events displayed per day, have a ---more--- link that links to a full day view
*   Need a full day view of all events for the day.
*   Added authentication.
*   SPECIAL NOTE: THIS APP CURRENTLY HAS NO VALIDATION OF INPUTS -- So inputting invalid date information will simply crash the program right now. I will be adding validation, but before doing that, I actually want to revamp the add/edit input forms to limit the types of input: i.e., add selection boxes for date, time and type inputs. 
    *   Created a new class with constant parameters (input fields, select boxes data, etc.). Now able to set a reminder and event type. 
*   Will continue with jquery after more is done with input/edit screens. 
*   Want to eventually make multiple reminder alerts (create a separate reminder table)
*   Want to remove holiday events from event list, add a symbol to calendar, change color of day, add link and title describing holiday.
*   Work on events that span days. 
