<?php 
return <<<FORM_MARKUP
<form action="assets/inc/process.inc.php" method="POST">
<fieldset>
<legend>$submit</legend>
<label for "event_title">Event Title:</label>
    <input type="text" name="event_title" id="event_title" value="$event->title" required />
<label for "event_type">Type:</label>
    $selectBoxType
<label for "event_start">Start Time:</label>
    <input type="text" name="event_start" id="event_start" placeholder="YYYY-MM-DD hh:mm:ss" value="$event->start" />
<label for "event_end">End Time:</label>
    <input type="text" name="event_end" id="event_end" placeholder="YYYY-MM-DD hh:mm:ss" value="$event->end" />
 <label for "event_loc">Location:</label>
    <input type="text" name="event_loc" id="event_loc" value="$event->loc" />
<label for "event_desc">Description:</label>
    <textarea name="event_desc" id="event_desc"/>$event->desc</textarea>
<label for "event_rem">Reminder:</label>
    $selectBoxRem
<input type="hidden" name="event_id" value="$event->id" />
<input type="hidden" name="token" value="$_SESSION[token]" />
<input type="hidden" name="action" value="event_edit" />
<button type="submit" id="btnEdit">$submit</button> or <a href="./">cancel</a>
</fieldset>
</form>
FORM_MARKUP;
?>