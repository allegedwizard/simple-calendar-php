<?php
// This test loads the class directly, but ideally use Composer in your project.
require_once __DIR__ . '/../src/SimpleCalendar.php';

// Instantiate the SimpleCalendar class with a month and date.
$month = 'August';
$year = 2023;
$calendar = new \AllegedWizard\SimpleCalendar\SimpleCalendar( $month, $year );

// Optionally specify the date formatter function, ie: 'wp_date' for WordPress.
// If the function doesn't exist, the default PHP date function is used.
// $calendar->setDateFunction( 'wp_date' );

// Optionally specify the first calendar day of week. Defaults to Sunday.
//$calendar->setFirstDayOfWeek( 'Monday' );

$month = $calendar->getMonth();
$model = $calendar->toArray();
$by_row = array_chunk( $model, 7 );


// Start example calendar view...
ob_start();

// Print current month, year.
printf( '<h1>%s %s</h1>', $calendar->getMonth(), $calendar->getYear() );

// Open table.
echo '<table>';

// Print table headers (days of week).
$headers = [];
foreach ( $calendar->getDaysOfWeek() as $day ) {
    $headers[] = sprintf( '<th>%s</th>', $day );
}
printf( '<thead><tr>%s</tr></thead>', implode( '', $headers ) );

// Print days of month.
echo '<tbody>';
foreach ( $by_row as $row_items ) {
    echo '<tr>';
    foreach ( $row_items as $calendar_cell ) {
        //printf( '<td>%s</td>', print_r( $calendar_cell, 1 ) );
        printf( '<td><div>%s</div></td>', $calendar_cell['day_of_month'] );
    }
    echo '</tr>';
}
echo '</tbody>';

// Close table.
echo '</table>';
$example_calendar_html = ob_get_clean();

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <title>Simple Calendar PHP</title>

    <style>
        table {
            width: 100%;
            height: 100%;
        }
        th {
            padding: 12px;
        }
        td {
            width: calc(100% / 7)
        }
        td:nth-of-type(odd), th:nth-of-type(odd) {
            background-color: #e0e0e0;
        }
        td div {
            padding: 12px;
        }
    </style>
</head>
<body>

<div class="container">
    <?php echo $example_calendar_html; ?>
    <hr>
    <pre>
// Example output of:
<strong>$calendar = new SimpleCalendar( '<?= $month; ?>', <?= $year; ?> );</strong>
<strong>$calendar->toArray()<br></strong><?php print_r( $model ); ?></pre>
</div>
</body>
</html>


