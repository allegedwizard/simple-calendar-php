## Simple Calendar (PHP)

by [AllegedWizard](https://allegedwizard.com)

Generates a calendar data model array for a given input Month and Year. 

Can be used to build calendar views.

```php
<?php
// Example output of:
$calendar = new SimpleCalendar( 'August', 2023 );
$model = $calendar->toArray();
print_r( $model );
```

```
Array
(
    [0] => Array
        (
            [date] => 2023-07-30
            [day_of_month] => 30
            [is_prev_month] => 1
            [is_current_month] => 
            [is_next_month] => 
            [is_today] => 
        )

    [1] => Array
        (
            [date] => 2023-07-31
            [day_of_month] => 31
            [is_prev_month] => 1
            [is_current_month] => 
            [is_next_month] => 
            [is_today] => 
        )

    [2] => Array
        (
            [date] => 2023-08-01
            [day_of_month] => 1
            [is_prev_month] => 
            [is_current_month] => 1
            [is_next_month] => 
            [is_today] => 
        )
    ...
```


* Arbitrary first day of week calendar assignment (defaults to Sunday).
* Allows for overriding the <b>date($format, $timestamp)</b> formatter function, for example <b>wp_date()</b>
  in WordPress environments to leverage localized time.

See <code>tests/simple-calendar-test.php</code> for a full calendar example. 