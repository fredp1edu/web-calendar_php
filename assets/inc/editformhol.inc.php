<?php 
return <<<FORM_MARKUP
<form action="assets/inc/process.inc.php" method="POST">
<fieldset>
<legend>Edit The Holiday Location/Description</legend>
<label for "event_title">Event Title:</label>
    <input type="text" name="event_title" id="event_title" value="$event->title" readonly />
 <label for "event_loc">Location:</label>
    <input type="text" name="event_loc" id="event_loc" value="$event->loc" />
<label for "event_desc">Description:</label>
    <textarea name="event_desc" id="event_desc"/>$event->desc</textarea>
<label for "event_rem">Reminder:</label>
    $selectBoxRem
<input type="hidden" name="event_id" value="$event->id" />
<input type="hidden" name="token" value="$_SESSION[token]" />
<input type="hidden" name="action" value="event_edit" />
<input type="hidden" name="event_type" value=9 />
<input type="hidden" name="event_sYear" value="$sYear" />
<input type="hidden" name="event_sMonth" value="$sMonth" />
<input type="hidden" name="event_sDate" value="$sDate" />
<input type="hidden" name="event_sHour" value="$sHour" />
<input type="hidden" name="event_sMin" value="$sMin" />
<input type="hidden" name="event_eYear" value="$eYear" />
<input type="hidden" name="event_eMonth" value="$eMonth" />
<input type="hidden" name="event_eDate" value="$eDate" />
<input type="hidden" name="event_eHour" value="$eHour" />
<input type="hidden" name="event_eMin" value="$eMin" />
<button type="submit" id="btnEdit">$submit</button> or <a href="./">cancel</a>
</fieldset>
</form>
FORM_MARKUP;
?>