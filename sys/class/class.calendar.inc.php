<?php

class Calendar extends DB_Connect {
    
    private $_useDate;
    private $_month;
    private $_year;
    private $_daysInMonth;
    private $_startDay;
    private $params;
    
    public function __construct($dbo=NULL, $useDate=NULL) {
        parent::__construct($dbo);
        
        if (isset($useDate))                // get the time
            $this->_useDate = $useDate;
        else {
            date_default_timezone_set('America/New_York');
            $this->_useDate = date('Y-m-d H:i:s');
        }
        $time = strtotime($this->_useDate);     //break down the time
        $this->_month = date('m', $time);
        $this->_year = date('Y', $time);
        $this->_daysInMonth = cal_days_in_month(CAL_GREGORIAN, $this->_month, $this->_year);
        $time = mktime(0, 0, 0, $this->_month, 1, $this->_year);
        $this->_startDay = date('w', $time);
        $this->params = new Params();
    }
    private function _loadEventData($item=NULL) {
        $query = "SELECT * FROM events";
        if (!empty($item) && substr($item, 0, 1) != "*")
            $query .= " WHERE event_id = $item LIMIT 1";
        else {
            if (!empty($item)) {
                $sDay = (int) substr($item, 1);
                $eDay = $sDay;
                $eMonth = $this->_month;
            } else {
                $sDay = 1;
                $eDay = 0;
                $eMonth = $this->_month + 1;
            }
            $startTime = mktime(0, 0, 0, $this->_month, $sDay, $this->_year);
            $endTime = mktime(23, 59, 59, $eMonth, $eDay, $this->_year);
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
        $html = '<section class="calHead">
        <a href="./?change=-1year">-&#60;&#60;&#60; year</a><a href="./?change=-1month">-&#60;&#60; month</a>
        <a href="./?change=today" class="calMonthYr" title="Click here for current date">' . $calendarMonth . '</a>
        <a href="./?change=%2b1month">month &#62;&#62;+</a><a href="./?change=%2b1year">year &#62;&#62;&#62;+</a></section>';
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
                    $ecl = $this->params->getEventCharLimit();
                    foreach($events[$c] as $event) {
                        $title = $event->title;
                        $type = $this->params->getEventStyle($event->type);
                        if (strlen($title) > $ecl) {                  // add 15 to a global class 
                            $title = substr($title, 0, $ecl);         // add 13 to a global class - how to compensate for length of line
                            $title .= "...";
                        }
                        $link = '<a class="event ' .$type. '" href="view.php?event_id=' . $event->id . '">' . $title . '</a>';
                        $eventInfo .= "\n\t\t$link";
                    }
                }
                $mo = date('F', strtotime($this->_useDate));
                $click = "title=\"Click here for $mo $c events\"";
                $date = sprintf("\n\t\t\t<strong><a href=\"view.php?day=%d\" %s class=\"dateNum\">%02d</a></strong>", 
                                $c, $click, $c++);
            } else 
                $date = "&nbsp;";
            $wrap = ($i != 0 && $i % 7 == 0) ? "\n\t</ul>\n\t<ul>" : NULL;
            $html .= $lStart . $date . $eventInfo . $lEnd . $wrap;
        }
        while ($i % 7 != 1) {                                       //check out variable scope to see how $i still exists
            $html .= "\n\t\t<li class=\"fill\">&nbsp;<li>";         // whatever it's doing it's not working exactly right 
            ++$i;
        }
        $html .= "\n\t</ul>\n\n";
        
        $admin = $this->_adminGeneralOptions();
        
        return $html . $admin;
    }
    public function displayEvent($id) {
        if (empty($id))
            return NULL;
        $id = preg_replace('/[^0-9]/', '', $id);
        $event = $this->_loadEventById($id);
        $tStart = strtotime($event->start);
        $date = date('D, F j, Y', $tStart);
        $start = date('g:ia', $tStart);
        $end = date('g:ia', strtotime($event->end));
        $rem = ($event->rem == 0) ? "no reminder set" : $this->params->getRemText($event->rem) . " before the appt";
        $admin = $this->_adminEntryOptions($id);
        $type = $this->params->getEventType($event->type);
        
        return "<h2>$event->title</h2>" .
                "\n\t<p class=\"para date\">Date: $date</p>" . 
                "\n\t<p class=\"para date\">Time: $start &mdash; $end</p>" .
                "\n\t<p class=\"para loc\">Loc: $event->loc</p>" .
                "\n\t<p class=\"para desc\">Desc: $event->desc</p>" .
                "\n\t<p class=\"para date\">$type </p> " .
                "\n\t<p class=\"para date\">Reminder: $rem</p>" . $admin;
    }
    public function displayDayEvents($d) {
        if (empty($d))
            return NULL;
        $d = "*" . $d;
        $events = array();
        $events = _loadEventData($d);
        $rows = mysqli_num_rows($events);
        if ($rows == 0) {
            $admin = _adminGeneralOptions();
            return "There are no entries for this date" . $admin;
        }
    }
    public function displayForm() {
        if (isset($_POST['event_id']))
            $id = (int) $_POST['event_id'];
        else 
            $id = NULL;
        $submit = "Create a New Event";
        $event = new Event('form');
        $event->start = $this->_useDate;
        $event->end = date('Y-m-d H:i:s', strtotime($event->start . '+ 1 hour'));
        $fieldDisable = NULL;
        $holidayType = NULL;
        if (!empty($id)) {
            $event = $this->_loadEventById($id);
            if ($event->type == 9) {
                $fieldDisable = "readonly";
                $holidayType = "<input type=\"hidden\" name=\"event_type\" value=9 />";
            }        
            if (!is_object($event))
                return NULL;
            $submit = "Edit This Event";
        }
        $timeS = strtotime($event->start);
        $timeE = strtotime($event->end);
        $optBoxYearS = $this->params->getOptionSet("year", date('Y', $timeS));
        $optBoxMonthS = $this->params->getOptionSet("month", date('m', $timeS));
        $optBoxDateS = $this->params->getOptionSet("date", date('d', $timeS));
        $optBoxHourS = $this->params->getOptionSet("hour", date('H', $timeS));
        $optBoxYearE = $this->params->getOptionSet("year", date('Y', $timeE));
        $optBoxMonthE = $this->params->getOptionSet("month", date('m', $timeE));
        $optBoxDateE = $this->params->getOptionSet("date", date('d', $timeE));
        $optBoxHourE = $this->params->getOptionSet("hour", date('H', $timeE));
        if ($event->id == NULL) {
            $optBoxMinS = $this->params->getOptionSet("min", sprintf('%02d', 0));
            $optBoxMinE = $this->params->getOptionSet("min", sprintf('%02d', 0));    
        } else {
            $optBoxMinS = $this->params->getOptionSet("min", date('i', $timeS));
            $optBoxMinE = $this->params->getOptionSet("min", date('i', $timeE));
        }
        $selectBoxType = $this->params->getSelectBox("type", $event->type);
        $selectBoxRem = $this->params->getSelectBox("rem", $event->rem);
        
        return include 'assets/inc/editform.inc.php';
    }
    public function processForm() {
        if ( $_POST['action'] != 'event_edit')
            return "Don't know how you got here, but you can't stay";
        
        $formFieldEvent = $this->params->getFormFieldsEvent();
        $formInputEvent = array();
        foreach($formFieldEvent as $e) {
            $formInputEvent[$e] = htmlentities($_POST[$e], ENT_QUOTES);
        }
        $formInputEvent['event_start'] = "$formInputEvent[event_sYear]-$formInputEvent[event_sMonth]-$formInputEvent[event_sDate] " .
                                        "$formInputEvent[event_sHour]:$formInputEvent[event_sMin]:00";
        $formInputEvent['event_end'] = "$formInputEvent[event_eYear]-$formInputEvent[event_eMonth]-$formInputEvent[event_eDate] " .
                                        "$formInputEvent[event_eHour]:$formInputEvent[event_eMin]:00";
        
        $eventFields = $this->params->getEventFields();
        $fieldList = '';
        $addList = '';
        $editList = '';
        $eventAdd = array();
        foreach ($eventFields as $f) {
            $eventAdd[$f] = $formInputEvent[$f];
            $fieldList .= "$f,";
            $addList .= "'$eventAdd[$f]',";
            $editList .= "$f = '$eventAdd[$f]',";
        }
        $addList = substr($addList, 0, -1);
        $fieldList = substr($fieldList, 0, -1);
        $editList = substr($editList, 0, -1);
        if (empty($_POST['event_id']))
            $sql = "INSERT INTO events ($fieldList) VALUES ($addList)";
        else {
            $id = (int) $_POST['event_id'];
            $sql = "UPDATE events SET $editList WHERE event_id = $id";
        }
        try {
            $result = mysqli_query($this->db, $sql);
            mysqli_close($this->db);
            return TRUE;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    private function _adminGeneralOptions() {
        if (isset($_SESSION['user'])) {
            return <<<ADMIN_OPTIONS
        <a href="admin.php" class="admin">+ Add a New Event</a>
        <form action="assets/inc/process.inc.php" method="POST">
            <div>
                <input type="submit" value="Log Out" class="logout" />
                <input type="hidden" name="token" value="$_SESSION[token]" />
                <input type="hidden" name="action" value="user_logout" />
            </div>
        </form>
ADMIN_OPTIONS;
        } else {
            return <<<ADMIN_OPTIONS
        <a href="login.php" class="admin">Log In</a>
ADMIN_OPTIONS;
        }
    }
    private function _adminEntryOptions($id) {
        if (isset($_SESSION['user'])) {
            return <<<ADMIN_OPTIONS
        <div class="admin-options">
        <form action="admin.php" method="POST">
        <p>
        <input type="submit" name="edit_event" value="Edit This Event" />
        <input type="hidden" name="event_id" value="$id" />
        <p>
        </form>
        <form action="confirmdelete.php" method="POST">
        <p>
        <input type="submit" name="delete_event" value="Delete This Event" />
        <input type="hidden" name="event_id" value="$id" />
        </p></form></div>
ADMIN_OPTIONS;
        } else {
            return NULL;
        }
    }
    public function confirmDelete($id) {
        if (empty($id))
            return NULL;
        $id = preg_replace('/[^0-9]/', '', $id);
        if (isset($_POST['confirm_delete']) && $_POST['token'] == $_SESSION['token']) {
            if ($_POST['confirm_delete'] == "Yes, Delete It") {
                $sql = "DELETE FROM events WHERE event_id = $id";
                try {
                    $result = mysqli_query($this->db, $sql);
                    mysqli_close($this->db);
                    header("Location: ./");
                    return;
                } catch (Exception $e) {
                    return $e->getMessage();
                }
            } else {
                header("Location: ./");
                return;
            }
        }
        $event = $this->_loadEventById($id);
        if (!is_object($event))
            header("Location: ./");
        return <<<CONFIRM_DELETE
        <form action="confirmdelete.php" method="POST">
        <h2>Are you sure you want to delete "$event->title"?</h2>
        <p>You cannot undo once deleted.</p>
        <p>
        <input type="submit" name="confirm_delete" value="Yes, Delete It" />
        <input type="submit" name="confirm_delete" value="No! Keep It!" />
        <input type="hidden" name="event_id" value="$event->id" />
        <input type="hidden" name="token" value="$_SESSION[token]" /></p></form>
CONFIRM_DELETE;
    }
}
?>
