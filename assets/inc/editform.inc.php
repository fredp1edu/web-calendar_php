<?php 
return <<<FORM_MARKUP
<form action="assets/inc/process.inc.php" method="POST">
<fieldset>
<legend>$submit</legend>
<label for "event_title">Event Title:</label>
    <input type="text" name="event_title" id="event_title" value="$event->title" required $fieldDisable />
<label for "event_type">Type:</label>
    $selectBoxType
<label for "event_start">Start Time:</label>
    <select class="selBox" name="event_sMonth" id="event_sMonth">
        $optBoxMonthS
    </select> 
    <select class="selBox" name="event_sDate" id="event_sDate">
        $optBoxDateS
    </select> 
    <select class="selBox" name="event_sYear" id="event_sYear">
        $optBoxYearS
    </select> &nbsp;&nbsp;
    <select class="selBox" name="event_sHour" id="event_sHour">
        $optBoxHourS
    </select> : 
    <select class="selBox" name="event_sMin" id="event_sMin">
        $optBoxMinS
    </select>

<label for "event_end">End Time:</label>
    <select class="selBox" name="event_eMonth" id="event_sMonth">
        $optBoxMonthE
    </select> 
    <select class="selBox" name="event_eDate" id="event_sDate">
        $optBoxDateE
    </select> 
    <select class="selBox" name="event_eYear" id="event_sYear">
        $optBoxYearE
    </select> &nbsp;&nbsp;
    <select class="selBox" name="event_eHour" id="event_eHour">
        $optBoxHourE
    </select> : 
    <select class="selBox" name="event_eMin" id="event_eMin">
        $optBoxMinE
    </select> 
 <label for "event_loc">Location:</label>
    <input type="text" name="event_loc" id="event_loc" value="$event->loc" />
<label for "event_desc">Description:</label>
    <textarea name="event_desc" id="event_desc"/>$event->desc</textarea>
<label for "event_rem">Reminder:</label>
    $selectBoxRem
<input type="hidden" name="event_id" value="$event->id" />
<input type="hidden" name="token" value="$_SESSION[token]" />
$holidayType
<input type="hidden" name="action" value="event_edit" />
<button type="submit" id="btnEdit">$submit</button> or <a href="./">cancel</a>
</fieldset>
</form>
FORM_MARKUP;
?>