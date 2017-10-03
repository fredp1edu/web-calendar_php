    <?php

class Event {

    public $id;
    public $title;
    public $loc;
    public $type;
    public $desc;
    public $start;
    public $end;
    public $rem;

    public function __construct($event) {
        
        if (is_array($event)) {
            $this->id = $event['event_id'];
            $this->title = $event['event_title'];
            $this->loc = $event['event_loc'];
            $this->type = $event['event_type'];
            $this->desc = $event['event_desc'];
            $this->start = $event['event_start'];
            $this->end = $event['event_end'];
            $this->rem = $event['event_rem'];
        } else {
            if ($event == 'form') {
                $this->id = '';
                $this->title = '';
                $this->loc = '';
                $this->type = '';
                $this->desc = '';
                $this->start = '';
                $this->end = '';
                $this->rem = '';
            } else 
                throw new Exception("No event data was supplied.");
        }
    }
}
?>


