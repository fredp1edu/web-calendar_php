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
        include_once 'assets/inc/calhead.inc.php';          //calendar header in a separate file 
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
                        if (strlen($title) > $ecl) {               
                            $title = substr($title, 0, $ecl);   // this substr works differently for differnt lines. 
                            $title .= "...";
                        }
                        $link = '<a class="event ' .$type. '" href="view.php?event_id=' . $event->id . '">' . $title . '</a>';
                        $eventInfo .= "\n\t\t$link";
                    }
                }
                $mo = date('F', strtotime($this->_useDate));
                $click = "title=\"Click here for $mo $c events\"";
                $date = sprintf("\n\t\t\t<strong><a href=\"view.php?day_event=%d\" %s class=\"dateNum\">%02d</a></strong>", 
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
        
        $logStat = (isset($_SESSION['user'])) ? "in" : "out";
        $admin = $this->_adminGeneralOptions();
        $calFoot = "<section class=\"calFoot\">\n\t" .
                    "<a href=\"view.php?month_event\"><button class=\"btnMonth\" type=\"button\" >" .
                    "Month Events View</button></a>&nbsp;\n$admin</section>\n" .
                    "<section class=\"calFootTxt\"><p>Click on any calendar date to view the day events.<p>\n" .
                    "<p>You are currently logged $logStat.</p>\n ";
        
        return $html . $calFoot;
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
        $type = $this->params->getEventType($event->type);
        if ($type == NULL)
            $admin = $this->_adminEntryOptions($id, 1);
        else
            $admin = $this->_adminEntryOptions($id);
        
        return "<h2>$event->title</h2>" .
                "\n\t<p class=\"para date\">Date: $date</p>" . 
                "\n\t<p class=\"para date\">Time: $start &mdash; $end</p>" .
                "\n\t<p class=\"para loc\">Loc: $event->loc</p>" .
                "\n\t<p class=\"para desc\">Desc: $event->desc</p>" .
                "\n\t<p class=\"para date\">$type </p> " .
                "\n\t<p class=\"para date\">Reminder: $rem</p>" . $admin;
    }
    private function _formatDispEvent($ev) {
        $start = date('g:ia', strtotime($ev->start));
        $end = date('g:ia', strtotime($ev->end));
        return "<section class=\"dispDayItem\"><span class=\"dispTime\">$start &mdash; $end</span>\n" .
                    "<a href=\"view.php?event_id=$ev->id\" class=\"dispTitle\">$ev->title</a>\n" .
                    "<aside class=\"dispLoc\">$ev->loc</aside>\n" .
                    "<aside class=\"dispDesc\">$ev->desc</aside>\n</section>\n";
    }
    public function displayDayEvents($d) {
        if (empty($d))
            return NULL;
        $d = "*" . $d;
        $events = array();
        $events = $this->_loadEventData($d);
        $admin = $this->_adminGeneralOptions();
        $rows = mysqli_num_rows($events);
        $dispDate = date('l, F j, Y', strtotime($this->_useDate));
        $display = "<h2>Events for $dispDate</h2>";
        if ($rows == 0)
            return $display . "There are no entries for this date" . $admin;
        for ($i = 0; $i < $rows; $i++) {
            $event = new Event(mysqli_fetch_assoc($events));
            $display .= $this->_formatDispEvent($event);
        }
        return $display . $admin;
    }
    public function displayMonthEvents() {
        $monthEvents = $this->_createEventObj();
        $admin = $this->_adminGeneralOptions();
        $dispDate = date('F Y', strtotime($this->_useDate));
        $display = "<h2>Events for $dispDate</h2>\n";
        if ($monthEvents == NULL) 
            return $display . "There are no events posted for this month" . $admin;
        foreach ($monthEvents as $day => $events) {
            $display .= "<h3 class=\"dispMonthDate\">$day</h3>\n";
            foreach($events as $event) {
                $display .= $this->_formatDispEvent($event);
            }
        }
        return $display . $admin;
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
        if (!empty($id)) {
            $event = $this->_loadEventById($id);
            if (!is_object($event))
                return NULL;
            $submit = "Edit This Event";
        }
        $timeS = strtotime($event->start);
        $timeE = strtotime($event->end);
        $sYear = date('Y', $timeS);
        $sMonth = date('m', $timeS);
        $sDate = date('d', $timeS);
        $sHour = date('H', $timeS);
        $sMin = date('i', $timeS);
        $eYear = date('Y', $timeE);
        $eMonth = date('m', $timeE);
        $eDate = date('d', $timeE);
        $eHour = date('H', $timeE);
        $eMin = date('i', $timeE);
        $selectBoxRem = $this->params->getSelectBox("rem", $event->rem);
        if ($event->type == 9) {
            return include 'assets/inc/editformhol.inc.php';    
        }        
        $optBoxYearS = $this->params->getOptionSet("year", $sYear);
        $optBoxMonthS = $this->params->getOptionSet("month", $sMonth);
        $optBoxDateS = $this->params->getOptionSet("date", $sDate);
        $optBoxHourS = $this->params->getOptionSet("hour", $sHour);
        $optBoxYearE = $this->params->getOptionSet("year", $eYear);
        $optBoxMonthE = $this->params->getOptionSet("month", $eMonth);
        $optBoxDateE = $this->params->getOptionSet("date", $eDate);
        $optBoxHourE = $this->params->getOptionSet("hour", $eHour);
        if ($event->id == NULL) {
            $optBoxMinS = $this->params->getOptionSet("min", sprintf('%02d', 0));
            $optBoxMinE = $this->params->getOptionSet("min", sprintf('%02d', 0));    
        } else {
            $optBoxMinS = $this->params->getOptionSet("min", $sMin);
            $optBoxMinE = $this->params->getOptionSet("min", $eMin);
        }
        $selectBoxType = $this->params->getSelectBox("type", $event->type);
        
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
        <a href="admin.php"><button type="button" class="admin btnAdd">+ Add a New Event</button></a>&nbsp;
        <form class="logout" action="assets/inc/process.inc.php" method="POST">            
            <button type="submit" class="btnLogout">Log Out</button>
            <input type="hidden" name="token" value="$_SESSION[token]" />
            <input type="hidden" name="action" value="user_logout" />
        </form>
ADMIN_OPTIONS;
        } else {
            return <<<ADMIN_OPTIONS
        <a href="login.php"><button type="button" class="admin btnlogin">Log In</button></a>
ADMIN_OPTIONS;
        }
    }
    private function _adminEntryOptions($id, $delBtn=NULL) {
        if (isset($_SESSION['user'])) {
            if ($delBtn == NULL) {
                $del = "<form action=\"confirmdelete.php\" method=\"POST\">\n<p>\n" .
                            "<input class=\"btnDel\" type=\"submit\" name=\"delete_event\" value=\"Delete This Event\" />\n" .
                            "<input type=\"hidden\" name=\"event_id\" value=\"$id\" /></p>\n</form>\n";
            } else
                $del = NULL;
            return <<<ADMIN_OPTIONS
        <div class="admin-options">
        <form action="admin.php" method="POST">
        <p>
        <input type="submit" name="edit_event" value="Edit This Event" />
        <input type="hidden" name="event_id" value="$id" />
        <p>
        </form>
        $del
        </div>
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
