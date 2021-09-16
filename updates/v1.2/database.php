<?php
session_start();
error_reporting(1);

$db_config_path = '../application/config/database.php';

if (!isset($_SESSION["license_code"])) {
    $_SESSION["error"] = "Invalid purchase code!";
    header("Location: index.php");
    exit();
}

if (isset($_POST["btn_admin"])) {

    $_SESSION["db_host"] = $_POST['db_host'];
    $_SESSION["db_name"] = $_POST['db_name'];
    $_SESSION["db_user"] = $_POST['db_user'];
    $_SESSION["db_password"] = $_POST['db_password'];


    /* Database Credentials */
    defined("DB_HOST") ? null : define("DB_HOST", $_SESSION["db_host"]);
    defined("DB_USER") ? null : define("DB_USER", $_SESSION["db_user"]);
    defined("DB_PASS") ? null : define("DB_PASS", $_SESSION["db_password"]);
    defined("DB_NAME") ? null : define("DB_NAME", $_SESSION["db_name"]);

    /* Connect */
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $connection->query("SET CHARACTER SET utf8");
    $connection->query("SET NAMES utf8");

    /* check connection */
    if (mysqli_connect_errno()) {
        $error = 0;
    } else {
        
        mysqli_query($connection, "UPDATE settings SET version = '1.2' WHERE id = 1;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `enable_category` VARCHAR(155) NULL DEFAULT '0' AFTER `time_interval`;");
        mysqli_query($connection, "ALTER TABLE `appointments` ADD `sub_location_id` VARCHAR(155) NULL DEFAULT '0' AFTER `location_id`;");
        mysqli_query($connection, "ALTER TABLE `settings` ADD `num_format` VARCHAR(155) NULL DEFAULT '0' AFTER `chart_style`, ADD `curr_locate` VARCHAR(155) NULL DEFAULT '0' AFTER `num_format`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `curr_locate` VARCHAR(155) NULL DEFAULT '0' AFTER `template_style`, ADD `num_format` VARCHAR(155) NULL DEFAULT '0' AFTER `curr_locate`;");

        mysqli_query($connection, "ALTER TABLE `ratings` CHANGE `patient_id` `customer_id` INT(11) NOT NULL;");

        mysqli_query($connection, "ALTER TABLE `ratings` ADD `business_id` VARCHAR(255) NULL AFTER `user_id`, ADD `service_id` INT NULL AFTER `business_id`, ADD `appointment_id` INT NULL AFTER `service_id`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `enable_rating` VARCHAR(155) NULL DEFAULT '0' AFTER `enable_category`;");

        mysqli_query($connection, "ALTER TABLE `services` ADD `duration_type` VARCHAR(255) NULL DEFAULT 'minute' AFTER `duration`;");

        mysqli_query($connection, "ALTER TABLE `settings` ADD `razorpay_payment` VARCHAR(155) NULL DEFAULT '0' AFTER `secret_key`, ADD `razorpay_key_id` VARCHAR(255) NULL DEFAULT NULL AFTER `razorpay_payment`, ADD `razorpay_key_secret` VARCHAR(255) NULL DEFAULT NULL AFTER `razorpay_key_id`;");

        mysqli_query($connection, "ALTER TABLE `users` ADD `razorpay_payment` VARCHAR(155) NULL DEFAULT '0' AFTER `secret_key`, ADD `razorpay_key_id` VARCHAR(255) NULL DEFAULT NULL AFTER `razorpay_payment`, ADD `razorpay_key_secret` VARCHAR(255) NULL DEFAULT NULL AFTER `razorpay_key_id`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `interval_type` VARCHAR(255) NULL DEFAULT 'minute' AFTER `time_interval`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `interval_settings` VARCHAR(155) NULL DEFAULT '2' AFTER `interval_type`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `enable_location` VARCHAR(155) NULL DEFAULT '0' AFTER `enable_rating`;");

        mysqli_query($connection, "ALTER TABLE `appointments` ADD `note` TEXT NULL DEFAULT NULL AFTER `time`;");

        mysqli_query($connection, "ALTER TABLE `settings` ADD `enable_frontend` VARCHAR(155) NULL DEFAULT '1' AFTER `enable_multilingual`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `enable_group` VARCHAR(155) NULL DEFAULT '0' AFTER `enable_location`, ADD `total_person` VARCHAR(155) NULL DEFAULT '5' AFTER `enable_group`;");

        mysqli_query($connection, "ALTER TABLE `appointments` ADD `group_booking` VARCHAR(155) NULL DEFAULT '0' AFTER `note`, ADD `total_person` VARCHAR(155) NULL DEFAULT '0' AFTER `group_booking`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `enable_payment` VARCHAR(155) NULL DEFAULT '1' AFTER `total_person`, ADD `enable_onsite` VARCHAR(155) NULL DEFAULT '1' AFTER `enable_payment`;");

        mysqli_query($connection, "ALTER TABLE `business` ADD `facebook` VARCHAR(255) NULL AFTER `color`, ADD `twitter` VARCHAR(255) NULL AFTER `facebook`, ADD `instagram` VARCHAR(255) NULL AFTER `twitter`, ADD `whatsapp` VARCHAR(255) NULL AFTER `instagram`;");


        mysqli_query($connection, "DROP TABLE locations;");

        // import database table
        $query = '';
          $sqlScript = file('sql/locations.sql');
          foreach ($sqlScript as $line) {
            
            $startWith = substr(trim($line), 0 ,2);
            $endWith = substr(trim($line), -1 ,1);
            
            if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
              continue;
            }
              
            $query = $query . $line;
            if ($endWith == ';') {
              mysqli_query($connection, $query) or die('<div class="error-response sql-import-response">Problem in executing the SQL query <b>' . $query. '</b></div>');
              $query= '';   
            }
        }



        mysqli_query($connection, "
            INSERT INTO `lang_values` (`type`, `label`, `keyword`, `english`) VALUES
            ('user', 'You have reached the maximum limit! Please upgrade your plan.', 'reached-maximum-limit', 'You have reached the maximum limit! Please upgrade your plan'),
            ('user', 'Enable Category', 'enable-category', 'Enable Category'),
            ('user', 'Disable Category', 'disable-category', 'Disable Category'),
            ('user', 'Location', 'location', 'Location'),
            ('user', 'Locations', 'locations', 'Locations'),
            ('user', 'Sub location', 'sub-location', 'Sub location'),
            ('user', 'Sub locations', 'sub-locations', 'Sub locations'),
            ('user', 'Currency location', 'currency-location', 'Currency location'),
            ('user', 'Number format', 'number-format', 'Number format'),
            ('user', 'Currency Position', 'currency-position', 'Currency Position'),
            ('user', 'Paid', 'paid', 'Paid'),
            ('user', 'Minute', 'minute', 'Minute'),
            ('user', 'Hour', 'hour', 'Hour'),
            ('user', 'Send SMS Reminder', 'send-sms-reminder', 'Send SMS Reminder'),
            ('user', 'Review', 'review', 'Review'),
            ('user', 'Reviews', 'reviews', 'Reviews'),
            ('user', 'Customer Feedback', 'customer-feedback', 'Customer Feedback'),
            ('user', 'Average Rating', 'average-rating', 'Average Rating'),
            ('user', 'Ratings Summary', 'ratings-summary', 'Ratings Summary'),
            ('user', 'Ratings', 'ratings', 'Ratings'),
            ('user', 'Service Ratings', 'service-ratings', 'Service Ratings'),
            ('user', 'Enable Ratings', 'enable-ratings', 'Enable Ratings'),
            ('user', 'Enable to publicly visible service ratings, Until complete 3 ratings it will be hidden', 'enable-ratings-title', 'Enable to publicly visible service ratings, Until complete 3 ratings it will be hidden'),
            ('user', 'Learn more', 'learn-more', 'Learn more'),
            ('user', 'Write your review', 'write-review', 'Write your review'),
            ('user', 'January', 'january', 'January'),
            ('user', 'February', 'february', 'February'),
            ('user', 'March', 'march', 'March'),
            ('user', 'April', 'april', 'April'),
            ('user', 'May', 'may', 'May'),
            ('user', 'June', 'june', 'June'),
            ('user', 'July', 'july', 'July'),
            ('user', 'August', 'august', 'August'),
            ('user', 'September', 'september', 'September'),
            ('user', 'October', 'october', 'October'),
            ('user', 'November', 'november', 'November'),
            ('user', 'December', 'december', 'December'),
            ('user', 'Su', 'su', 'Su'),
            ('user', 'Mo', 'mo', 'Mo'),
            ('user', 'Tu', 'tu', 'Tu'),
            ('user', 'We', 'we', 'We'),
            ('user', 'Th', 'th', 'Th'),
            ('user', 'Fr', 'fr', 'Fr'),
            ('user', 'Sa', 'sa', 'Sa'),
            ('user', 'Days', 'days', 'Days'),
            ('user', 'Day', 'day', 'Day'),
            ('user', 'Kay Id', 'kay-id', 'Key Id'),
            ('user', 'Key Secret', 'key-secret', 'Key Secret'),
            ('user', 'Setup your Razorpay account to accept payments', 'razorpay-title', 'Setup your Razorpay account to accept payments'),
            ('user', 'Razorpay ', 'razorpay', 'Razorpay '),
            ('user', 'Opening Hour', 'opening-hour', 'Opening Hour'),
            ('user', 'End Hour', 'end-hour', 'End Hour'),
            ('user', 'Branches', 'branches', 'Branches'),
            ('user', 'Enable Locations', 'enable-locations', 'Enable Locations'),
            ('user', 'Disable Locations', 'disable-locations', 'Disable Locations'),
            ('user', 'Enable to allow locations in booking page', 'enable-location-title', 'Enable to allow locations in booking page'),
            ('user', 'Disable to hide locations in booking page', 'disable-location-title', 'Disable to hide locations in booking page'),
            ('user', 'Any special notes?', 'any-special-notes', 'Any special notes?'),
            ('user', 'Write your notes here', 'write-your-notes-here', 'Write your notes here'),
            ('user', 'Enable Frontend', 'enable-frontend', 'Enable Frontend'),
            ('user', 'Enable to show frontend site', 'enable-to-show-frontend-site', 'Enable to show frontend site'),
            ('user', 'View Details', 'view-details', 'View Details'),
            ('user', 'Total Appointments', 'total-appointments', 'Total Appointments'),
            ('user', 'Total Services', 'total-services', 'Total Services'),
            ('user', 'Last Appointment', 'last-appointment', 'Last Appointment'),
            ('user', 'Add Breaks', 'add-breaks', 'Add Breaks'),
            ('user', 'This phone number will used for as username', 'phone-as-username', 'This phone number will used for as username'),
            ('user', 'Search', 'search', 'Search'),
            ('user', 'Search Value', 'search-value', 'Search Value'),
            ('user', ' Twillo SMS Settings', 'twillo-sms-settings', ' Twillo SMS Settings'),
            ('user', 'Cancel', 'cancel', 'Cancel'),
            ('user', 'Phone already exist', 'phone-exist', 'Phone already exist'),
            ('user', 'Persons', 'persons', 'Persons'),
            ('user', 'Bringing anyone with you?', 'bringing-anyone-with-you', 'Bringing anyone with you?'),
            ('user', 'Additional Persons:', 'additional-persons', 'Additional Persons:'),
            ('user', 'General Settings', 'general-settings', 'General Settings'),
            ('user', 'Enable Group Booking', 'enable-group-booking', 'Enable Group Booking'),
            ('user', 'Enable to allow group booking for your customers', 'enable-group-title', 'Enable to allow group booking for your customers'),
            ('user', 'Maximum allowed additional persons', 'max-allowed-persons', 'Maximum allowed additional persons'),
            ('user', 'Group Booking', 'group-booking', 'Group Booking'),
            ('user', 'Payments', 'payments', 'Payments'),
            ('user', 'just now', 'just-now', 'just now'),
            ('user', 'one minute ago', 'one-minute-ago', 'one minute ago'),
            ('user', 'minutes ago', 'minutes-ago', 'minutes ago'),
            ('user', 'an hour ago', 'an-hour-ago', 'an hour ago'),
            ('user', 'hours ago', 'hours-ago', 'hours ago'),
            ('user', 'yesterday', 'yesterday', 'yesterday'),
            ('user', 'days ago', 'days-ago', 'days ago'),
            ('user', 'weeks ago', 'weeks-ago', 'weeks ago'),
            ('user', 'a month ago', 'a-month-ago', 'a month ago'),
            ('user', 'months ago', 'months-ago', 'months ago'),
            ('user', 'one year ago', 'one-year-ago', 'one year ago'),
            ('user', 'years ago', 'years-ago', 'years ago'),
            ('user', 'Jan', 'jan', 'Jan'),
            ('user', 'Feb', 'feb', 'Feb'),
            ('user', 'Mar', 'mar', 'Mar'),
            ('user', 'Apr', 'apr', 'Apr'),
            ('user', 'Jun', 'jun', 'Jun'),
            ('user', 'Jul', 'jul', 'Jul'),
            ('user', 'Aug', 'aug', 'Aug'),
            ('user', 'Sep', 'sep', 'Sep'),
            ('user', 'Oct', 'oct', 'Oct'),
            ('user', 'Nov', 'nov', 'Nov'),
            ('user', 'Dec', 'dec', 'Dec'),
            ('user', 'Facebook', 'facebook', 'Facebook'),
            ('user', 'Twitter', 'twitter', 'Twitter'),
            ('user', 'Instagram', 'instagram', 'Instagram'),
            ('user', 'WhatsApp', 'whatsapp', 'WhatsApp'),
            ('user', 'LinkedIn', 'linkedin', 'LinkedIn'),
            ('user', 'Google Analytics', 'google-analytics', 'Google Analytics'),
            ('user', 'reCaptcha', 'recaptcha', 'reCaptcha'),
            ('user', 'Total Persons', 'total-persons', 'Total Persons'),
            ('user', 'Apply service duration to generate booking time slots', 'generate-booking-time-slots', 'Apply service duration to generate booking time slots'),
            ('user', 'Apply fixed duration to generate booking time slots', 'fixed-booking-time-slots', 'Apply fixed duration to generate booking time slots'),
            ('user', 'Enable Online Payments', 'enable-online-payment', 'Enable Online Payments'),
            ('user', 'Enable to active only payment methods to receive booking payments', 'enable-online-title', 'Enable to active online payment methods to receive booking payments'),
            ('user', 'Enable offline payment', 'enable-offline-payment', 'Enable offline payment'),
            ('user', 'Enable to active onsite payment option', 'enable-offline-title', 'Enable to active onsite payment option');
        ");


        mysqli_query($connection, "TRUNCATE TABLE `working_time`;");



        

      /* close connection */
      mysqli_close($connection);

      $redir = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
      $redir .= "://" . $_SERVER['HTTP_HOST'];
      $redir .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
      $redir = str_replace('updates/v1.2/', '', $redir);
      header("refresh:5;url=" . $redir);
      $success = 1;
    }



}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aoxio &bull; Update Installer</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/libs/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,500,600,700&display=swap" rel="stylesheet">
    <script src="assets/js/jquery-1.12.4.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-12 col-md-offset-2">

                <div class="row">
                    <div class="col-sm-12 logo-cnt">
                        <p>
                           <img src="assets/img/logo.png" alt="">
                       </p>
                       <h1>Welcome to the update installer</h1>
                   </div>
               </div>

               <div class="row">
                <div class="col-sm-12">

                    <div class="install-box">

                        <div class="steps">
                            <div class="step-progress">
                                <div class="step-progress-line" data-now-value="100" data-number-of-steps="3" style="width: 100%;"></div>
                            </div>
                            <div class="step" style="width: 50%">
                                <div class="step-icon"><i class="fa fa-arrow-circle-right"></i></div>
                                <p>Start</p>
                            </div>
                            <div class="step active" style="width: 50%">
                                <div class="step-icon"><i class="fa fa-database"></i></div>
                                <p>Database</p>
                            </div>
                        </div>

                        <div class="messages">
                            <?php if (isset($message)) { ?>
                            <div class="alert alert-danger">
                                <strong><?php echo htmlspecialchars($message); ?></strong>
                            </div>
                            <?php } ?>
                            <?php if (isset($success)) { ?>
                            <div class="alert alert-success">
                                <strong>Completing Updates ... <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i> Please wait 5 second </strong>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="step-contents">
                            <div class="tab-1">
                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                    <div class="tab-content">
                                        <div class="tab_1">
                                            <h1 class="step-title">Database</h1>
                                            <div class="form-group">
                                                <label for="email">Host</label>
                                                <input type="text" class="form-control form-input" name="db_host" placeholder="Host"
                                                value="<?php echo isset($_SESSION["db_host"]) ? $_SESSION["db_host"] : 'localhost'; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Database Name</label>
                                                <input type="text" class="form-control form-input" name="db_name" placeholder="Database Name" value="<?php echo @$_SESSION["db_name"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Username</label>
                                                <input type="text" class="form-control form-input" name="db_user" placeholder="Username" value="<?php echo @$_SESSION["db_user"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Password</label>
                                                <input type="password" class="form-control form-input" name="db_password" placeholder="Password" value="<?php echo @$_SESSION["db_password"]; ?>">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="buttons">
                                        <a href="index.php" class="btn btn-success btn-custom pull-left">Prev</a>
                                        <button type="submit" name="btn_admin" class="btn btn-success btn-custom pull-right">Finish</button>
                                    </div>
                                </form>
                            </div>
                        </div>


                    </div>
                </div>
            </div>


        </div>


    </div>


</div>

<?php

unset($_SESSION["error"]);
unset($_SESSION["success"]);

?>

</body>
</html>

