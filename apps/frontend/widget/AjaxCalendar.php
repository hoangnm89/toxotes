<?php

class AjaxCalendar extends FrontendBaseWidget {
    public $viewFile = 'calendar';

    public function begin() {
        \Flywheel\Factory::getDocument()->addJs('assets/js/ajax_calendar.js');
        $this->fetch_child = (boolean) $this->fetch_child;
        $this->term_id = (int) $this->term_id;

        $this->month = ($this->month) ? $this->month : date('m');
        $this->year = ($this->year)? $this->year : date('Y');
        $this->day = ($this->day)? $this->day : date('d');
    }
}