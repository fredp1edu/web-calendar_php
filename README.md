# web-calendar_php
A web calendar app done with PHP/MySQL/JQuery to be redone later with Node / React
This calendar app was adapted from a book, "Pro PHP and JQuery", I was reading to catch up on PHP skills. While it is a bit cumbersome writing, PHP is used by a vast majority of web hosts in the US, including the ISPs of two organizations with whom I'm working on some web development projects. Thus, I need to get my PHP refresher on.
I will reproduce the calendar project as it is in the book, but will make a few changes:
*   Just about all the work, including producing the HTML for various forms and the calendar structure itself is done through one main calendar class, so it's definitely not following a MVC framework. I will distribute some of that work to one or more helper classes and the php output files themselves.
*   Some of the jquery work is unnecessary in my eyes, namely not having php refresh the screen after an event add or edit. I don't really see a problem with a refresh after one of those actions. I'll go along with the instrucs now just to see the methodology but may take it out later.
*   Added 2 features: event type, and event reminder alerts (to be developed later). 
*   Added month, year and current date navigation of calendar, the book example only used one static month.
*   Automated form input collection and db processing through creation and use of a constants parameters class.
*   Added a few lines so that if the event title exceeds one line in the calendar, it will be truncated and "..." added after. Using the PHP string length method works for now but isn't the best since it doesn't compensate for proportionally spaced fonts. I can either make the event title a monospaced font (which I don't want to do), or (better) see if HTML/CSS can handle this issue better than PHP.
*   Added select boxes for event type, reminder, event start and end times. All boxes update during editing process. 
*   Added a day and month view.
Notes:
*   There's some presentation issue with the formatting of day boxes that are not calendar dates. While viewing the source code shows the right coding, the DOM is actually doing something different. The coding in buildCalendar is very.... interesting. It works, but I keep thinking of another approach: a 2-dimension array 7x5 grid that uses a nested loop to traverse each cell OR (maybe better) a loop array that just traverses and processes 35 (7x5) boxes and then let CSS's flex box do the column formatting. Will work on that later.
*   SPECIAL NOTE: THIS APP CURRENTLY HAS NO VALIDATION OF INPUTS -- So it is still possible to input an invalid date or an end time  that is earlier than the start time. An invalid date may freeze stuff up right now. I will be adding front-end and back-end validation shortly.

*   Online now. to log in:  caltester fpProj2017! 

NEXT TO DO:
CSS prettying and tidying up along the way.
1.  Add day and month views to jquery modal windows.
2.  Need to limit the amount of events displayed per day, have a ---more--- link that links to a full day view
3.  Add jquery enhancements, add front and back end form validation (basically go back to where I left off in book and finish it)
4.  Allow for batch deletion of entries in month and day views.
5.  Allow for creation of multiple reminders for one event by creation a separate reminder table. Set up a reminder class to check for reminders and display as an alert upon initial calendar display. 
6.  Remove holiday events from event listings on calendar, add a symbol to calendar day to denote holiday, change color of day, add link and title describing holiday.
7.  Look into online API that lists holidays and make separate table for that to auto-populate calendar at the beginning of each year.
8.  Allow events to span days. 
9.  Turn calendar app into enterprise app that allows multiple users to create proprietary calendar.