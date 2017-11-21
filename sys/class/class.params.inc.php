<?php

class Params {

    private $weekdays = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    private $eventStyle = array("zro", "one", "two", "thr", "for", "fiv", "six", "sev", "eig", "nin");
                                    // eventually eventType and remText will load from DB key tables
    private $eventType = array(
        "name"  => "event_type",
        "txt"   => array("Normal", "Urgent", "Business", "Leisure", "Birthday")
    );
    private $remText = array(
        "name"  => "event_rem",
        "txt"   => array("No reminder", "10 min", "15 min", "30 min", "1 hour", "3 hours", "12 hours", "1 day")
    );    
    private $HOLIDAY_NUM = 9;
    private $EVENT_CHAR_LIMIT = 16;
    private $EVENT_LIST_LIMIT = 2;
    
    private $formFieldEvent = array("event_title", "event_type", "event_loc", "event_desc", "event_rem",
                                    "event_sMonth", "event_sYear", "event_sDate", "event_sHour", "event_sMin",
                                    "event_eMonth", "event_eYear", "event_eDate", "event_eHour", "event_eMin");
    private $eventField = array("event_title", "event_type", "event_start", "event_end", "event_loc", "event_desc", "event_rem");
    
    private $month = array();
    private $year = array();
    private $date = array();
    private $hour = array();
    private $min = array();
    
    public function __construct() {
        for ($m = 1; $m < 13; $m++)
            $this->month[date('m', strtotime('01.'.$m.'.2001'))] = date('F', strtotime('01.'.$m.'.2001'));
        for ($y = 2015; $y < 2036; $y++)
            $this->year[$y] = $y;
        for ($d = 1; $d < 32; $d++)
            $this->date[sprintf('%02d', $d)] = $d;
        for ($h = 0; $h < 24; $h++)
            $this->hour[sprintf('%02d', $h)] = sprintf('%02d', $h);
        for ($n = 0; $n < 60; $n++)
            $this->min[sprintf('%02d', $n)] = sprintf('%02d', $n);
    }
    public function getWeekdays() {
        return $this->weekdays;
    }
    public function getEventStyle($nm) {
        $lim = count($this->eventStyle);
        return ($nm < $lim) ? $this->eventStyle[$nm] : $this->eventStyle[0];
    }
    public function getEventFields() {
        return $this->eventField;
    }
    public function getEventType($num) {
        $count = count($this->eventType['txt']);
        return ($num < $count) ? "Event type: " .$this->eventType["txt"][$num] : NULL;
    }
    public function getRemText($num) {
        return $this->remText["txt"][$num];
    }
    public function getFormFieldsEvent() {
        return $this->formFieldEvent;
    }
    public function getEventCharLimit() {
        return $this->EVENT_CHAR_LIMIT;
    }
    public function getEventListLimit() {
        return $this->EVENT_LIST_LIMIT;
    }
    public function getSelectBox($type, $select) {
        $select = (int) $select;
    
        if ($type == "type") {
            $arr = $this->eventType;
        } else {
            $arr = $this->remText;
        } 
        $count = count($arr["txt"]);
        $box = "<select class=\"selBox\" name=\"" .$arr["name"]. "\" id=\"" .$arr["name"]. "\">\n";
        for ($i = 0; $i < $count; $i++) {
            $selTxt = ($i == $select) ? "selected" : NULL;
            $box .= "\t<option value=\"$i\" " .$selTxt. ">" .$arr["txt"][$i]. "</option>\n";
        }
        $box .= "</select>\n";
        return $box;
    }
    public function getOptionSet($type, $select) {
        $array = $this->$type;
        $box = NULL;
        foreach ($array as $index => $val) {
            $selTxt = ($index == $select) ? "selected" : NULL;
            $box .= "\t<option value=\"$index\" " .$selTxt. ">" .$val. "</option>\n";
        }
        return $box;
    }
}
?>