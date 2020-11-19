<!doctype html>
<html>
<head>
<title>Customer</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>
.container {
    margin: 30px auto;
}
</style>
</head>
<body>

<div class="jumbotron text-center" style="margin-bottom:0">
  <h1>AgamiSoft Invoice System</h1>
  <p>Create invoice and pay now or later.</p> 
</div>

<div class="container">

<div class="row">

<div class="col-sm-12">
    
<?php
// Create connection
$conn = new mysqli('localhost', 'root', '', 'agami');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql_customer = "SELECT * FROM customer";

$result_customer = $conn->query($sql_customer);

if ($result_customer->num_rows > 0) {
  echo '<table class="table table-striped table-bordered">';
    echo '<tr><th>Customer Name</th><th>Total Payable</th><th>Total Paid</th><th>Total Due</th></tr>';
  while($row_customer = $result_customer->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . $row_customer['customer_fname'] . '</td>';
    $result_order = $conn->query("SELECT SUM(grand_total) AS grand_total FROM orders WHERE customerID = \"{$row_customer["customerID"]}\"");
    if($result_order->num_rows > 0) {
      while($row_order = $result_order->fetch_assoc()) {
        $total_payable = $row_order['grand_total'];
      }
    } else {
      $total_payable = 0;
    }
    echo '<td>' . $total_payable . '</td>';
    $result_payment = $conn->query("SELECT SUM(paid_amount) AS paid_amount FROM payments WHERE customerID = \"{$row_customer["customerID"]}\"");
    if($result_payment->num_rows > 0) {
      while($row_payment = $result_payment->fetch_assoc()) {
        $total_paid = $row_payment['paid_amount'];
      }
    } else {
      $total_paid = 0;
    }
    echo '<td>' . $total_paid . '</td>';
    echo '<td>' . ($total_payable - $total_paid) . '</td>';
    echo '</tr>';
  }
  echo '</table>';
}
?>

</div>
</div>
</div>

<div class="jumbotron text-center" style="margin-bottom:0">
  <p>Copyright 2020 by AgamiSoft. All Rights Reserved.</p>
</div>

</body>
</html>