
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />

    <title>Authenticator QR Code</title>

    <link rel="icon" type="image/png" href="css/icon.png">
        <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap-table/dist/bootstrap-table.min.css" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.css" type="text/css" />
    <link rel="stylesheet" href="css/index.css" type="text/css" />
  </head>
  <body class='container-fluid'>


<?php
session_start();
  require(dirname(__FILE__) . '/include/functions.php');
  require(dirname(__FILE__) . '/include/connect.php');


if (!isset($_SESSION['user_id'],$_SESSION['secret_code']))   {
    header( "refresh:0;url=/index.php" );
}
else {
    $user_id = $_SESSION['user_id'];
    $secret_code = $_SESSION['secret_code'];
    $paired = false;
    $error = false;
    if (isset($_POST['authenticator_pair'])) {
        $pinNumber = $_POST['authenticator_pin_number'];

        $result = file_get_contents("https://www.authenticatorApi.com/Validate.aspx?Pin=$pinNumber&SecretCode=$secret_code");

        $valid = filter_var($result, FILTER_VALIDATE_BOOLEAN);
        $error = !$valid;
        $paired = $valid;
     }
    if ($paired) {
        echo "Successfully paired.";
        updateUserFlag($bdd, $user_id, "user_2factor_paired","1");
        session_destroy();
        header( "refresh:5;url=/index.php" );
    }
   else  {
        $content =  file_get_contents("https://www.authenticatorapi.com/pair.aspx?AppName=AyreMedia&AppInfo=$user_id&SecretCode=$secret_code");


        echo strip_tags($content, '<img>');
        require(dirname(__FILE__) . '/include/html/form/authenticator_pair.php');
        if ($error) {
            printError("PIN Number is not valid. Try again.");
        }
    }


}



?>
</body>

</html>