<?php

namespace AllegedWizard\SimpleCalendar;

/**
 * Generates 7x6 sequence calendar array model for a given month/year input.
 */
class SimpleCalendar
{
    /**
     * @var int YYYY format.
     */
    protected int $year = 0;

    /**
     * @var string ie: 'January'.
     */
    protected string $month = '';

    /**
     * @var string Allows overriding the default date() function; ie leveraging 'wp_date' in WordPress for localized time.
     */
    protected string $date_function = 'date';

    protected array $timestamps = [
        'current_month_first_day' => -1,
        'next_month_first_day' => -1,
        'prev_month_last_day' => -1,
    ];

    protected array $data = [
        'first_day_of_week' => '',     // The first day of the week of this current month; ie: Monday
        'total_days_in_month' => '',   // Total days in current month.
        'next_month_name' => '',       // Full name of 'next' month, ie: January
        'next_month_year' => '',       // YYYY of next month.
        'prev_month_name' => '',       // Full name of the previous month, ie: December
        'prev_month_total_days' => '', // Total days of the previous month.
    ];

    /**
     * @var string Defines the first day of week to display in the calendar grid, ie: Sunday or Monday
     */
    protected string $first_day_of_week = 'Sunday';

    /**
     * @param $input_month int|string Full name of the month, ie 'January', or a zero-index integer (0-11) representation of the month.
     * @param $input_year int|string A year in YYYY format.
     * @throws \Exception
     */
    public function __construct( $input_month = 'January', $input_year = '2023' ) {
        $this->setMonth( $input_month );
        $this->setYear( $input_year );
        $this->_validateInput();
    }

    /**
     * Assigns the first calendar day of week.
     * @param $day_of_week string ie: 'Monday'
     * @return void
     * @throws \Exception
     */
    public function setFirstDayOfWeek( $day_of_week = 'Sunday' ) : void {
        if ( !in_array( $day_of_week, $this->_daysOfWeek() ) ) {
            throw new \Exception( 'Invalid $day_of_week specified.' );
        }
        $this->first_day_of_week = $day_of_week;
    }

    /**
     * @param $function_name string A date( $format, $timestamp) compatible function.
     * @return void
     */
    public function setDateFunction( string $function_name = 'date' ) : void {
        $this->date_function = $function_name;
    }

    /**
     * Sets the current month.
     * @param $month
     * @return void
     */
    public function setMonth( $month = 'January' ) : void {
        if ( is_numeric( $month ) ) {
            $this->month = $this->_months()[(int) $month];
        } else {
            $this->month = ucfirst( $month );
        }
    }

    /**
     * Sets the current year.
     * @param $year
     * @return void
     */
    public function setYear( $year = '2023' ) : void {
        $this->year = (int) $year;
    }

    /**
     * @return int
     */
    public function getYear() : int {
        return $this->year;
    }

    /**
     * @return string
     */
    public function getMonth() : string {
        return $this->month;
    }

    /**
     * @return string[] An array sequence of full calendar month names.
     */
    protected function _months() : array {
        return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    }

    /**
     * @return string[] An array sequence of days of the week.
     */
    protected function _daysOfWeek() : array {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }

    /**
     * Ensure valid input arguments.
     * @return void
     * @throws \Exception
     */
    protected function _validateInput() : void {
        if (
            !is_numeric( $this->year ) ||
            strlen( (string) $this->year ) !== 4
        ) {
            throw new \Exception( 'Invalid year format, expected YYYY.' );
        }
        if ( !in_array( $this->month, $this->_months() ) ) {
            throw new \Exception( 'Invalid month format, expected name of the month, ie: January.' );
        }
    }

    /**
     * Overridable wrapper for PHP date( $format, $timestamp ) function.
     * @param $format
     * @param $timestamp
     * @return string
     */
    protected function _formatDate( $format, $timestamp ) {
        $date_function = $this->date_function;
        if ( !is_callable( $date_function ) ) {
            $date_function = 'date';
        }
        return $date_function( $format, $timestamp );
    }

    /**
     * @param $month_name
     * @param $day_of_month
     * @param $year
     * @return array
     */
    protected function getDateModel( $month_name = 'January', $day_of_month = 1, $year = 2023 ) {

        $calendar_date_format = 'Y-m-d';
        $input_date = sprintf( "%s %s %s", $month_name, $day_of_month, $year );
        $input_timestamp = strtotime( $input_date );
        $formatted_date = $this->_formatDate( $calendar_date_format, $input_timestamp );

        $cur_month_first_day = $this->timestamps['current_month_first_day'];
        $next_month_first_day = $this->timestamps['next_month_first_day'];

        $is_today = $this->_formatDate( $calendar_date_format, strtotime( 'today' ) ) == $formatted_date;
        $is_prev_month = -1 !== $cur_month_first_day && $input_timestamp < $cur_month_first_day;
        $is_next_month = -1 !== $next_month_first_day && $input_timestamp >= $next_month_first_day;
        $is_current_month = $cur_month_first_day && $next_month_first_day &&
            $input_timestamp >= $cur_month_first_day &&
            $input_timestamp <= $next_month_first_day;

        $date_model = [
            'date' => $formatted_date,
            'day_of_month' => $day_of_month,
            'is_prev_month' => $is_prev_month,
            'is_current_month' => $is_current_month,
            'is_next_month' => $is_next_month,
            'is_today' => $is_today,
        ];

        return $date_model;
    }

    /**
     * @return void
     */
    protected function processCurrentCalendar() : void {
        $day_in_seconds = 24 * 60 * 60;
        $current_year = $this->getYear();

        // Current month data.
        $current_month_first_day_timestamp = strtotime( sprintf( '%s %s', $this->getMonth(), $current_year ) );
        $this->data['first_day_of_week'] = $this->_formatDate( 'l', $current_month_first_day_timestamp ); // Ie: Monday
        $this->data['total_days_in_month'] = $this->_formatDate( 't', $current_month_first_day_timestamp );
        $this->timestamps['current_month_first_day'] = $current_month_first_day_timestamp;

        // Previous month data.
        $this->timestamps['prev_month_last_day'] = $current_month_first_day_timestamp - $day_in_seconds;
        $this->data['prev_month_name'] = $this->_formatDate( 'F', $this->timestamps['prev_month_last_day'] );
        $this->data['prev_month_total_days'] = $this->_formatDate( 't', $this->timestamps['prev_month_last_day'] );

        // Next month data.
        $next_month_input = sprintf( '%s %s %s', $this->getMonth(), $this->data['total_days_in_month'], $current_year );
        $this->timestamps['next_month_first_day'] = strtotime( $next_month_input ) + $day_in_seconds;
        $this->data['next_month_name'] = $this->_formatDate( 'F', $this->timestamps['next_month_first_day'] );
        $this->data['next_month_year'] = $this->_formatDate( 'Y', $this->timestamps['next_month_first_day'] );
    }

    /**
     * @return string[]
     */
    public function getDaysOfWeek() {
        $days_of_week = $this->_daysOfWeek();
        while ( $this->first_day_of_week !== $days_of_week[0] ) {
            $days_of_week[] = array_shift( $days_of_week );
        }
        return $days_of_week;
    }

    /**
     * @return array An array of items to populate a calendar 7x6 calendar. An array of [[<br>
     *    'date' => string,<br>
     *    'is_prev_month' => bool,<br>
     *    'is_current_month' => bool,<br>
     *    'is_next_month' => bool,<br>
     *    'is_today' => bool,<br>
     * ], ...]
     */
    public function toArray() : array {

        // Process previous, current and next month data relative to current Month/Year config.
        $this->processCurrentCalendar();

        // Prepare days of week header, offsetting until we match the specified first day of week.
        $days_of_week = $this->_daysOfWeek();
        while ( $this->first_day_of_week !== $days_of_week[0] ) {
            $days_of_week[] = array_shift( $days_of_week );
        }

        // Declare output array.
        $output = [];

        // Process PREVIOUS month cells (when the first day of this month doesn't fall on the first calendar cell).
        $first_index = array_search( $this->data['first_day_of_week'], $days_of_week );
        if ( 0 !== $first_index ) {
            // Count backwards the previous month days until we hit the first calendar cell.
            $negative_range = range(
                $this->data['prev_month_total_days'] - $first_index + 1,
                $this->data['prev_month_total_days']
            );
            foreach ( $negative_range as $day_of_month ) {
                $output[] = $this->getDateModel( $this->data['prev_month_name'], $day_of_month, $this->getYear() );
            }
        }

        // Process CURRENT month cells.
        $current_month_name = $this->_formatDate( 'F', $this->timestamps['current_month_first_day'] );
        for ( $i = 0; $i < $this->data['total_days_in_month']; $i++ ) {
            $output[] = $this->getDateModel( $current_month_name, $i + 1, $this->getYear() );
        }

        // Process NEXT month cells.
        $total_cells = 42; // 7 days x 6 weeks
        $remaining = $total_cells - count( $output );
        if ( $remaining > 0 ) {
            $i = 0;
            while ( $remaining ) {
                $i++;
                $output[] = $this->getDateModel( $this->data['next_month_name'], $i, $this->data['next_month_year'] );
                $remaining--;
            }
        }

        return $output;
    }
}