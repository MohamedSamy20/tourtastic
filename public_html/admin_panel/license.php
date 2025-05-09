<?php include_once "includes/header.php"; ?>

<?php
$sql = "SELECT * FROM tbl_license ORDER BY id DESC LIMIT 1";
$result = $connect->query($sql);
$data = $result->fetch_assoc();

if (isset($_POST["revoke_license"])) {
    $delete = "DELETE FROM tbl_license";
    $delete = mysqli_query($connect, $delete);
    if ($delete) {
        $success = <<<EOF
                    <script>
                        alert('Your License has been revoked.');
                        window.location = 'logout.php';
                    </script>
EOF;
        echo $success;
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($connect);
    }
    mysqli_close($connect);
}

?>

<section class="content">
    <ol class="breadcrumb">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li class="active">License</a></li>
    </ol>
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <form method="post">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>LICENSE</h2>
                        </div>
                        <div class="body">
                            <div class="body table-responsive">
                                <table class='table table-offset'>
                                    <tr>
                                        <td width="15%">Item ID</td>
                                        <td width="1%">:</td>
                                        <td><?php echo $data['item_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Item Name</td>
                                        <td>:</td>
                                        <td><?php echo $data['item_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Buyer</td>
                                        <td>:</td>
                                        <td><?php echo $data['buyer']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Purchase Code</td>
                                        <td>:</td>
                                        <td><?php echo $data['purchase_code']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>License Type</td>
                                        <td>:</td>
                                        <td><?php echo $data['license_type']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Purchase Date</td>
                                        <td>:</td>
                                        <td><?php echo $data['purchase_date']; ?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><button type="submit" name="revoke_license" class="button button-rounded waves-effect waves-float pull-right" onclick="return confirm('Are you sure want to revoke this license?')">REVOKE LICENSE</button></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once('includes/footer.php'); ?>