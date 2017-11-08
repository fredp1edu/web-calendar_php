<?php

class Params {

    private $eventField = array('event_title', 'event_type', 'event_start', 'event_end', 'event_loc', 'event_desc', 'event_rem');
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
    
    public function getEventFields() {
        return $this->eventField;
    }
    public function getEventType($num) {
        return ($num == $this->HOLIDAY_NUM) ? NULL : "Event type: " .$this->eventType["txt"][$num];
    }
    public function getRemText($num) {
        return $this->remText["txt"][$num];
    }
    public function getSelectBox($type, $select) {
        $select = (int) $select;
        $disable = NULL;
        if ($type == "type") {
            $arr = $this->eventType;
            if ($select == $this->HOLIDAY_NUM)            // disable changing of "holiday" #9 types
                $disable = "disabled";
        } else {
            $arr = $this->remText;
        } 
        $count = count($arr["txt"]);
        $box = "<select class=\"selBox\" name=\"" .$arr["name"]. "\" id=\"" .$arr["name"]. "\" " .$disable. ">\n";
        for ($i = 0; $i < $count; $i++) {
            $selTxt = ($i == $select) ? "selected" : NULL;
            $box .= "\t<option value=\"$i\" " .$selTxt. ">" .$arr["txt"][$i]. "</option>\n";
        }
        $box .= "</select>\n";
        return $box;
    }
}
?>