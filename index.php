<!doctype html>
<html>
<head>
<title>Customer</title>
</head>
<body>
<form method="post" action="index.php" enctype="multipart/form-data">
<input type="text" name="search_txt" placeholder="Search By Company Name Or Mobile Number">
<input type="submit" name="search_submit">
</form>

<?php
if(isset($_POST['search_submit'])) {
    //echo 'form is submitted';
// Create connection
$conn = new mysqli('localhost', 'root', '', 'agami');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$search_txt = trim($_POST['search_txt']);

$sql_customer = "SELECT * FROM customer WHERE company_name LIKE \"%$search_txt%\" OR phone LIKE \"%$search_txt%\"";

$result_customer = $conn->query($sql_customer);

if ($result_customer->num_rows > 0) {
    ?>

<form method="post" action="index.php" enctype="multipart/form-data">

    <?php
  while($row_customer = $result_customer->fetch_assoc()) {
    // echo "customerID: " . $row_customer["customerID"] . "<br>";

  //$sql_order = "SELECT orders.*, payments.* FROM orders LEFT JOIN payments ON orders.orderID = payments.orderID WHERE orders.customerID = \"{$row_customer["customerID"]}\"";
  $sql_order = "SELECT * FROM orders WHERE customerID = \"{$row_customer["customerID"]}\"";
  $result_order = $conn->query($sql_order);
  $total_due = 0;
  echo '<table>';
        echo '<tr><td>Check</td><td>Date</td><td>Invoice no.</td><td>Total amount</td><td>Paid amount</td><td>Due amount</td></tr>';
  if($result_order->num_rows > 0) {
      while($row_order = $result_order->fetch_assoc()) {
        $due_amount = 0;  
        echo '<tr>';
        echo '<td>' . "<input type='checkbox'>" . '</td>';
        echo '<td>' . $row_order['date'] . '</td>';
        echo '<td>' . $row_order['invoice_no'] . '</td>';
        echo '<td>' . $row_order['grand_total'] . '</td>';
        $result_payment = $conn->query("SELECT SUM(paid_amount) AS paid_amount FROM payments WHERE orderID = \"{$row_order['orderID']}\"");
        if($result_payment->num_rows > 0) {
            while($row_payment = $result_payment->fetch_assoc()) {
                $paid_amount = $row_payment['paid_amount'];
            }
        } else {
            $paid_amount = 0;
        }
        if($paid_amount === NULL) {
            $paid_amount = 0;
        }
        
        echo '<td>' . $paid_amount . '</td>';
        echo '<td>' . ($row_order['grand_total'] - $paid_amount) . '</td>';
        $due_amount = $row_order['grand_total'] - $paid_amount;
        $total_due += $due_amount;
        echo '</tr>';
      }
      echo '<tr>';
      echo '<td>' . '</td>';
      echo '<td><strong>Due: ' . $total_due . '</strong></td>';
      echo '<td>' . '</td>';
      echo '<td>' . '</td>';
      echo '<td>' . '</td>';
      echo '<td>' . '</td>';
      echo '</tr>';
      echo '</table>';
  } else {
  echo "No results found.";
}
  
  ?>

<input type="hidden" name="customer_id" value="<?php echo $row_customer["customerID"]; ?>">
<input type="text" name="receipt_amount" placeholder="Search By Company Name Or Mobile Number">
<input type="submit" name="receipt_submit">
</form>

  <?php
}
} else {
  echo "No results found.";
}

// $conn->close();
}
?>


<?php
if(isset($_POST['receipt_submit'])) {
    $conn = new mysqli('localhost', 'root', '', 'agami');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//echo 'Customer ID is: ' . $_POST['customer_id'] . ' Receipt Amount is: ' . $_POST['receipt_amount'];
$customer_id = $_POST['customer_id'];
$receipt_amount = $_POST['receipt_amount'];
$sql_order = "SELECT * FROM orders WHERE customerID = {$customer_id} ORDER BY orderID ASC";
  $result_order = $conn->query($sql_order);
  if($result_order->num_rows > 0) {
      while($row_order = $result_order->fetch_assoc()) {
        $invoice_total = $row_order['grand_total'];
        $result_payment = $conn->query("SELECT SUM(paid_amount) AS paid_amount FROM payments WHERE orderID = \"{$row_order['orderID']}\"");
        if($result_payment->num_rows > 0) {
            while($row_payment = $result_payment->fetch_assoc()) {
                $paid_amount = $row_payment['paid_amount'];
            }
        } else {
            $paid_amount = 0;
        }
        if($paid_amount >= $invoice_total) {
            continue;
        } else {
            $invoice_due = $invoice_total - $paid_amount;
            if($receipt_amount <= $invoice_due) {
                $pay_now = $receipt_amount;
            } else {
                $pay_now = $invoice_due;
            }
            if($receipt_amount <= 0) {
                continue;
            }
            $receipt_amount -= $pay_now;
            $sql_payment = "INSERT INTO payments (orderID, customerID, paid_amount, payment_status) VALUES (\"{$row_order['orderID']}\", \"{$row_order['customerID']}\", \"$pay_now\", \"paid\")";
            if ($conn->query($sql_payment) === TRUE) {
                $payment_feedback = "Payment made successfully";
              } else {
                $payment_feedback = "Error: " . $sql . "<br>" . $conn->error;
              }
            //echo 'Pay to Invoice No.: ' . $row_order['invoice_no'] . ' Invoice Due: ' . $invoice_due . '<br />';
        }
      }
    }
    echo $payment_feedback;
?>



<?php
// $conn->close();
}
?>

</body>
</html>