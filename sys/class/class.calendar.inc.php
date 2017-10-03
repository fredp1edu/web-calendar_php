<?php

class Calendar extends DB_Connect {
    
    private $_useDate;
    private $_month;
    private $_year;
    private $_daysInMonth;
    private $_startDay;
    
    public function __construct($dbo=NULL, $useDate=NULL) {
        parent::__construct($dbo);
        
        if (isset($useDate))                // get the time
            $this->_useDate = $useDate;
        else
            $this->_useDate = date('Y-m-d H:i:s');
        
        $time = strtotime($this->_useDate);     //break down the time
        $this->_month = date('m', $time);
        $this->_year = date('Y', $time);
        $this->_daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->_month, $this->_year);
        $time = mktime(0, 0, 0, $this->_month, 1, $this->_year);
        $this->_startDay = date('w', $time);
    }
    private function _loadEventData($id=NULL) {
        $query = "SELECT * FROM events";
        if (!empty($id))
            $query .= " WHERE event_id = $id LIMIT 1";
        else {
            $startTime = mktime(0, 0, 0, $this->_month, 1, $this->_year);
            $endTime = mktime(23, 59, 59, $this->_month+1, 0, $this->_year);
            $startDate = date('Y-m-d H:i:s', $startTime);
            $endDate = date('Y-m-d H:i:s', $endTime);
            $query .= " WHERE event_start BETWEEN '$startDate' AND '$endDate' ORDER BY event_start";
        }
        try {
            $result = mysqli_query($this->db, $query);
            mysqli_close($this->db);
            return $result;
        } catch(Exception $e) { 
            die($e->getMessage());
        }
    }
    private function _createEventObj() {
        $dbEvents = $this->_loadEventData();
        $events = array();
        $rows = mysqli_num_rows($dbEvents);
        for ($i = 0; $i < $rows; ++$i) {
            $row = mysqli_fetch_assoc($dbEvents);
            $day = date('j', strtotime($row['event_start']));
            $events[$day][] = new Event($row);
        }
        return $events;
    }
    private function _loadEventById($id) {
        if (empty($id))
            return NULL;
        $eventDB = $this->_loadEventData($id);
        $event = new Event(mysqli_fetch_assoc($eventDB));
        if (isset($event))
            return $event;
        else 
            return NULL;
    }
    public function buildCalendar() {
        $calendarMonth = date('F Y', strtotime($this->_useDate));
        $weekdays = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $html = "\n\t<h2>$calendarMonth</h2>";
        for ($d=0, $labels=NULL; $d < 7; ++$d) {
            $labels .= "\n\t\t<li>" . $weekdays[$d] . "</li>";
        }
        $html .= "\n\t<ul class=\"weekdays\">" . $labels . "\n\t</ul>";
        
        $events = $this->_createEventObj();
        $html .= "\n\t<ul>";
        for ($i = 1, $c = 1, $t = date('j'), $m = date('m'), $y = date('Y'); $c <= $this->_daysInMonth; ++$i) {
            $class = ($i <= $this->_startDay) ? "fill" : NULL;
            if ($c == $t && $m == $this->_month && $y == $this->_year)
                $class = "today";
            $lStart = sprintf("\n\t\t<li class=\"%s\">", $class);
            $lEnd = "\n\t\t</li>";
            $eventInfo = NULL;
            if ($this->_startDay < $i && $this->_daysInMonth >= $c) {
                if (isset($events[$c])) {
                    foreach($events[$c] as $event) {
                        $link = '<a href="view.php?event_id=' . $event->id . '">' . $event->title . '</a>';
                        $eventInfo .= "\n\t\t$link";
                    }
                }
                $date = sprintf("\n\t\t\t<strong>%02d</strong>", $c++);
            } else 
                $date = "&nbsp;";
            $wrap = ($i != 0 && $i % 7 == 0) ? "\n\t</ul>\n\t<ul>" : NULL;
            $html .= $lStart . $date . $eventInfo . $lEnd . $wrap;
        }
        while ($i % 7 != 1) {                                       //check out variable scope to see how $i still exists
            $html .= "\n\t\t<li class=\"fill\">&nbsp;<li>";
            ++$i;
        }
        $html .= "\n\t</ul>\n\n";
        return $html;
    }
    public function displayEvent($id) {
        if (empty($id))
            return NULL;
        $id = preg_replace('/[^0-9]/', '', $id);
        $event = $this->_loadEventById($id);
        $tStart = strtotime($event->start);
        $date = date('F d, Y', $tStart);
        $start = date('g:ia', $tStart);
        $end = date('g:ia', strtotime($event->end));
        $rem = ($event->rem == NULL || $event->rem == '0000-00-00 00:00:00') ? 
            "no reminder set" : date('g:ia', strtotime($event_rem));
        
        return "<h2>$event->title</h2>" .
                "\n\t<p class=\"para date\">$date &mdash; $start&mdash;$end</p>" .
                "\n\t<p class=\"para loc\">$event->loc</p>" .
                "\n\t<p class=\"para desc\">$event->desc</p>" .
                "\n\t<p class=\"para date\">Reminder: $rem</p>";
    }
    public function displayForm() {
        if (isset($_POST['event_id']))
            $id = (int) $_POST['event_id'];
        else 
            $id = NULL;
        $submit = "Create a New Event";
        $event = new Event('form');
        if (!empty($id)) {
            $event = $this->loadEventById($id);
            if (!is_object($event))
                return NULL;
            $submit = "Edit This Event";
        } 
return <<<FORM_MARKUP
<form action="assets/inc/process.inc.php" method="POST">
<fieldset>
<legend>$submit</legend>
<label for "event_title">Event Title:</label>
    <input type="text" name="event_title" id="event_title" value="$event->title" />
<label for "event_type">Type:</label>
    <input type="text" name="event_type" id="event_type" value="$event->type" />
<label for "event_start">Start Time:</label>
    <input type="text" name="event_start" id="event_start" value="$event->start" />
<label for "event_end">End Time:</label>
    <input type="text" name="event_end" id="event_end" value="$event->end" />
 <label for "event_loc">Location:</label>
    <input type="text" name="event_loc" id="event_loc" value="$event->loc" />
<label for "event_desc">Description:</label>
    <textarea name="event_desc" id="event_desc"/>$event->desc</textarea>
<label for "event_rem">Reminder:</label>
    <input type="text" name="event_rem" id="event_rem" value="$event->rem" />
<input type="hidden" name="event_id" value="$event->id" />
<input type="hidden" name="token" value="$_SESSION[token]" />
<input type="hidden" name="action" value="event_edit" />
<button type="submit" id="btnEdit">$submit</button> or <a href="./">cancel</a>
</fieldset>
</form>
FORM_MARKUP;
    }
    public function processForm() {
        if ( $_POST['action'] != 'event_edit')
            return "Don't know how you got here, but you can't stay";
        
        $field = array('event_title', 'event_type', 'event_start', 'event_end', 'event_loc', 'event_desc', 'event_rem');
        $fieldList = '';
        $addList = '';
        $eventAdd = array();
        foreach ($field as $f) {
            $eventAdd[$f] = htmlentities($_POST[$f], ENT_QUOTES);
            $fieldList .= "$f,";
            $addList .= "'$eventAdd[$f]',";
        }
        $addList = substr($addList, 0, -1);
        $fieldList = substr($fieldList, 0, -1);
        if (empty($_POST['event_id']))
            $sql = "INSERT INTO events ($fieldList) VALUES ($addList)";
        else {
            $id = (int) $_POST['event_id'];
            $sql = "UPDATE events SET event_title = '$eventAdd[event_title]', event_loc = '$eventAdd[event_loc]' 
                    WHERE event_id = $id";
        }
        try {
            $result = mysqli_query($this->db, $sql);
            mysqli_close($this->db);
            return TRUE;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>
