<?php
$calendarMonth = date('F Y', strtotime($this->_useDate));
$weekdays = $this->params->getWeekdays();
$html = "<section class=\"calHead\">\n" .
    "<div class=\"calHeadSec\">\n\t<a href=\"./?change=-1year\" title=\"Previous year\">\n" .
        "<figure><img class=\"calHeadFig\" src=\"assets/image/tri_dub_lft.png\" /></figure>&nbsp;-y</a></div>\n" .
    "<div class=\"calHeadSec\"><a href=\"./?change=-1month\" title=\"Previous month\">\n\t" .
        "<figure><img class=\"calHeadFig\" src=\"assets/image/tri_lft.png\" /></figure>&nbsp;-m</a></div>\n" .
    "<div class=\"calHeadSec\"><a href=\"./?change=today\" title=\"Click here for current date\">" .
        "<span class=\"calMonthYr\" > $calendarMonth</span></a></div>" .
    "<div class=\"calHeadSec\"><a href=\"./?change=%2b1month\" title=\"Next month\">\n\t" .
        "+m&nbsp;<figure><img class=\"calHeadFig\" src=\"assets/image/tri_rgt.png\" /></figure></a></div>" .
    "<div class=\"calHeadSec\"><a href=\"./?change=%2b1year\" title=\"Next year\">\n\t" .
        "+y&nbsp;<figure><img class=\"calHeadFig\" src=\"assets/image/tri_dub_rgt.png\" /></a></div></section>";
for ($d=0, $labels=NULL; $d < 7; ++$d) {
    $labels .= "\n\t\t<li>" . $weekdays[$d] . "</li>";
}
$html .= "\n\t<ul class=\"weekdays\">" . $labels . "\n\t</ul>";
?>