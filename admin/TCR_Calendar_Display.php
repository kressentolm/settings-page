<?php
class TCR_Calendar_Display {

    private $active_year, $active_month, $active_day;
    private $events = [];

    public function __construct($date = null) {
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
    }

    public function add_event($txt, $date, $days = 1, $color = '') {
        $color = $color ? ' ' . $color : $color;
        $this->events[] = [$txt, $date, $days, $color];
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
        $html .= '</div>';
        $html .= '<div class="days">';
        foreach ($days as $day) {
            $html .= '
                <div class="day_name">
                    ' . $day . '
                </div>
            ';
        }
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '
                <div class="day_num ignore">
                    ' . ($num_days_last_month - $i + 1) . '
                </div>
            ';
        }
        for ($i = 1; $i <= $num_days; $i++) {
            $selected = '';
            if ($i == $this->active_day) {
                $selected = ' selected';
            }
            $html .= '<div class="day_num' . $selected . '">';
            $html .= '<span>' . $i . '</span>';
            foreach ($this->events as $event) {
                for ($d = 0; $d <= ($event[2] - 1); $d++) {
                    if (date('y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day')) == date('y-m-d', strtotime($event[1]))) {
                        $html .= '<div class="event' . $event[3] . '">';
                        $html .= $event[0];
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
        }
        for ($i = 1; $i <= (42 - $num_days - max($first_day_of_week, 0)); $i++) {
            $html .= '
                <div class="day_num ignore">
                    ' . $i . '
                </div>
            ';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
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
                background-color: #f7c30d;
                color: #fff;
                word-wrap: break-word;
            }

            .calendar .days .day_num .event.green {
                background-color: #51ce57;
            }

            .calendar .days .day_num .event.blue {
                background-color: #518fce;
            }

            .calendar .days .day_num .event.red {
                background-color: #ce5151;
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
        // TODO: Use saved events instead of these hardcoded events from Google Cal that we are perusing through in the plugin

        // TODO: Allow for clicking back/forth to get events within a month range:
        // 1. Get previous and next month as links on top part of calendar
        // 2. Remove clickability
        // 3. Add option to turn off names (should always be off, but they may change their mind in the future)
        // 4. Update styling and put into its own CSS file, if we need to.
        // 5. Hook into calendar inside theme that consumes it.
        // $todays_date = date("Y/m/d");
        $todays_date = date("Y/m/d", strtotime("15 January 2021"));
        $calendar = new TCR_Calendar_Display($todays_date);

        // TODO: Update to only get range for the month
        // TODO: Add ability to look at next and previous month
        $events_list = get_posts(array(
            'post_type' => 'tcr_event',
            'numberposts' => -1
        ));

        // Cycle through events to create calendar, based on current Month view
        foreach ($events_list as $item) {

            $start = get_post_meta($item->ID, 'tcr_event_start', true);
            $end = get_post_meta($item->ID, 'tcr_event_end', true);
            $start_date = new DateTime(date('y-m-d', strtotime($start)));
            $end_date = new DateTime(date('y-m-d', strtotime($end)));
            $difference = $start_date->diff($end_date);

            $calendar->add_event($item->post_title, $start, $difference->d + 1, 'red');
        }
        return $calendar;
    }
}
