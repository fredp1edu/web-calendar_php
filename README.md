# web-calendar_php
A web calendar app done with PHP/MySQL/JQuery to be redone later with Node / React
This calendar app was adapted from a book, "Pro PHP and JQuery", I was reading to catch up on PHP skills. While it is a bit cumbersome writing, PHP is used by a vast majority of web hosts in the US, including the ISPs of two organizations with whom I'm working on some web development projects. Thus, I need to get my PHP refresher on.
I will reproduce the calendar project as it is in the book, but will make a few changes:
*   Just about all the work, including producing the HTML for various forms and the calendar structure itself is done through one main calendar class, so definitely not following a MVC framework. I will distribute some of that work to one or more helper classes and the php output files themselves.
*   Some of the jquery work is unnecessary in my eyes, namely not having php refresh the screen after an event add or edit. I don't really see a problem with a refresh after one of those actions. I'll go along with the instrucs now just to see the methodology but may take it out later.
*   Added month, year and current date navigation of calendar, the book example only used one static month.
*   Automated form input collection and db processing through creation and use of a constants parameters class.
*   Added a few lines so that if the event title exceeds one line in the calendar, it will be truncated and "..." added after. Using the PHP string length method works for now but isn't the best since it doesn't compensate for proportionally spaced fonts. I can either make the event title a monospaced font (which I don't want to do), or (better) see if HTML/CSS can handle this issue better than PHP.
*   Added select boxes for event type, reminder, event start and end times. All boxes update during editing process. 
Notes:
*   There's some presentation issue with the formatting of day boxes that are not calendar dates. While view source code shows the right coding, the DOM is actually doing something different. Have to check CSS and how calendar is built.
*   SPECIAL NOTE: THIS APP CURRENTLY HAS NO VALIDATION OF INPUTS -- So it is still possible to input an invalid date or an end time  that is earlier than the start time. An invalid date may freeze stuff up right now. I will be adding front-end and back-end validation shortly.
*   Once it goes online: to log in:  caltester fpProj2017! 
    *   If things don't work at all, I'm having a small issue with the mysql db with my ISP - to be resolved shortly.
NEXT TO DO:
1.  Complete day and month view - possible batch deletion of entries through month and day views.
2.  Need to limit the amount of events displayed per day, have a ---more--- link that links to a full day view
3.  Add jquery enhancements, add front and back end form validation (basically go back to where left off from book and finish it)
4.  Make multiple reminder alerts possible by creation a separate reminder table. Set up reminder class to check for reminder alerts and display on initial calendar display. 
5.  Remove holiday events from event listings, add a symbol to calendar to denote holiday, change color of day, add link and title describing holiday.
6.  Look into online API that lists holidays and make separate table for that to auto-populate calendar.
7.  Allow events to span days. 
