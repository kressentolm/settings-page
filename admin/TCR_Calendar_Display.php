<?php
class TCR_Calendar_Display {

    private $active_year, $active_month, $active_day;
    private $events = [];

    public function __construct($date = null) {

        $this->today = date('y-m-d');
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');

        $this->current_month = date('F', strtotime(date('F')));
        $this->previous_month = $date != null ? date("F", strtotime("-1 month", strtotime($date))) : null;
        $this->next_month = $date != null ? date("F", strtotime("+1 month", strtotime($date))) : null;
        $this->day_counter = 1;
    }

    public function add_event($txt, $date, $end, $days, $color = '') {
        $color = $color ? ' ' . $color : $color;
        $this->events[] = [$txt, $date, $end, $days, $color];
    }

    public function __toString() {
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
        $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);

        $html = '<div class="calendar">';
        $html .= '<div class="header">';
        $html .= '<div class="month-year">';
        $html .= date('F Y', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day));
        $html .= '</div>';
        $html .= "<div class='month-selector'>";
        if ($this->previous_month) {
            $html .= '<button id="previous-month" class="month-selector" data-month-target="' . $this->previous_month . '"><span><<</span> '
                . $this->previous_month . '</button>';
        }
        $html .= '<button id="current-month" class="month-selector" data-month-target="'
            . $this->current_month . '">Current Month</button>';
        if ($this->next_month) {
            $html .= '<button id="next-month" class="month-selector" data-month-target="' . $this->next_month . '">' .
                $this->next_month . ' <span>>></span></button>';
        }
        $html .= "</div>";
        $html .= '</div>';
        $html .= '<div class="days">';
        foreach ($days as $day) {
            $html .= '
                <div class="day_name">
                    ' . $day . '
                </div>
            ';
        }

        // Prev month days on calendar
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '
                <div class="day_num ignore"><span>
                    ' . ($num_days_last_month - $i + 1) . '
                </span></div>
            ';
        }

        // Cycle through calendar days
        for ($i = 1; $i <= $num_days; $i++) {
            $selected = '';
            $has_event = '';
            $day_in_cycle = date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i));

            if ($i === (int)$this->active_day && $this->today == $day_in_cycle) {
                $selected = ' selected';
            }

            // For each event, see if meets criteria
            foreach ($this->events as $event) {

                if ($this->meetsCriteria($event, $day_in_cycle, $i)) {
                    $has_event = ' has-event';
                }
                
            }

            $html .= '<div class="day_num' . $selected . $has_event . '">';
            $html .= '<span>' . $i . '</span>';
            $html .= '<div class="event red">';
            $html .= "<p>Booked</p>"; // we could access $event[0] for description, but currently don't need it
            $html .= '</div></div>';

        }
        // Next month days on calendar
        for ($i = 1; $i <= (42 - $num_days - max($first_day_of_week, 0)); $i++) {
            $html .= '<div class="day_num ignore"><span>' . $i . '</span></div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
        die();
    }

    private function dayHasEvent($event, $index) {
        $current_date = date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $index));
        $start = $event[1];

        if ($current_date == $start) {
            return true;
        }
    }

    private function meetsCriteria($event, $day_in_cycle, $i) {
        $start = $event[1];
        $end = $event[2];

        $meets_criteria = ($day_in_cycle >= $start && $day_in_cycle <= $end)
            || ($this->dayHasEvent($event, $i));

        return $meets_criteria;
    }

    public function displayCalendar() {
?>
        <style>
            .calendar {
                display: flex;
                flex-flow: column;
            }

            .calendar .header .month-year {
                font-size: 20px;
                font-weight: bold;
                color: #636e73;
                padding: 20px 0;
            }

            .calendar .days {
                display: flex;
                flex-flow: wrap;
            }

            .calendar .days .day_name {
                width: calc(100% / 7);
                border-right: 1px solid #2c7aca;
                padding: 20px;
                text-transform: uppercase;
                font-size: 12px;
                font-weight: bold;
                color: #818589;
                color: #fff;
                background-color: #448cd6;
            }

            .calendar .days .day_name:nth-child(7) {
                border: none;
            }

            .calendar .days .day_num {
                display: flex;
                flex-flow: column;
                width: calc(100% / 7);
                border-right: 1px solid #e6e9ea;
                border-bottom: 1px solid #e6e9ea;
                padding: 15px;
                font-weight: bold;
                color: #7c878d;
                cursor: pointer;
                min-height: 100px;
            }

            .calendar .days .day_num span {
                display: inline-flex;
                width: 30px;
                font-size: 14px;
            }

            .calendar .days .day_num .event {
                margin-top: 10px;
                font-weight: 500;
                font-size: 14px;
                padding: 3px 6px;
                border-radius: 4px;
                color: #fff;
                word-wrap: break-word;
            }

            .calendar .days .day_num .event.green {
                background-color: #51ce57;
            }

            .calendar .days .day_num .event.blue {
                background-color: #518fce;
            }

            .calendar .days .day_num:nth-child(7n+1) {
                border-left: 1px solid #e6e9ea;
            }

            .calendar .days .day_num:hover {
                background-color: #fdfdfd;
            }

            .calendar .days .day_num.ignore {
                background-color: #fdfdfd;
                color: #ced2d4;
                cursor: inherit;
            }

            .calendar .days .day_num.selected {
                background-color: #f1f2f3;
                cursor: inherit;
            }
        </style>
<?php

        $today = date("y-m-d");

        // Set date from cookie if exists
        if (isset($_COOKIE['targetMonth'])) {
            $target_month = $_COOKIE['targetMonth'];
            $new_month = date("y-m-d", strtotime($target_month));
        }

        $calendar_month_to_show = isset($new_month) || !empty($new_month) ? $new_month : $today;

        // If we want to view the previous or next month
        $calendar = new TCR_Calendar_Display($calendar_month_to_show);

        $events_list = get_posts(array(
            'post_type' => 'tcr_event',
            'numberposts' => -1
        ));

        // Cycle through events to create calendar, based on current Month view
        foreach ($events_list as $item) {


            $start = get_post_meta($item->ID, 'tcr_event_start', true);
            $end = get_post_meta($item->ID, 'tcr_event_end', true);
            $start_date = new DateTime($start);
            $end_date =  new DateTime($end);
            $difference = $start_date->diff($end_date);
            $calendar->add_event($item->post_title, $start, $end, $difference->d, 'red');
        }
        return $calendar;
    }
}
