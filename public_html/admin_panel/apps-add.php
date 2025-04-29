<?php include_once "includes/header.php"; ?>

<?php

if (isset($_POST["submit"])) {
    if ($_POST["status"] == "active") {
        $status = clean("1");
    } else {
        $status = clean("0");
    }

    $applicationId = clean($_POST["application_id"]);
    $redirectUrl = clean($_POST["redirect_url"]);

    $sql = "SELECT * FROM tbl_app_config WHERE application_id = '$applicationId' LIMIT 1";
    $result = mysqli_query($connect, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($applicationId == $row["application_id"]) {
            $_SESSION["msg"] = "Package name already added...";
            header("Location: apps-add.php");
            exit();
        }
    } else {
        $data = [
            "application_id" => $applicationId,
            "status" => $status,
            "redirect_url" => $redirectUrl,
        ];

        $qry = insert("tbl_app_config", $data);

        $_SESSION["msg"] = "App added successfully...";
        header("Location: apps-add.php");
        exit();
    }
}

?>

<script type="text/javascript">
    $(document).ready(function(e) {
    
        $("#status").change(function() {
            var type = $("#status").val();
    
            if (type == "active") {
                $("#active").show();
                $("#inactive").hide();
            }
    
            if (type == "inactive") {
                $("#active").hide();
                $("#inactive").show();
            }
            
        });
    
        $( window ).load(function() {
            var type=$("#status").val();
    
            if (type == "active")  {
                $("#active").show();
                $("#inactive").hide();
            }
    
            if (type == "inactive") {
                $("#active").hide();
                $("#inactive").show();
            }
    
        });
    
    });
    
</script>

<section class="content">
    <ol class="breadcrumb">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="apps.php">Apps</a></li>
        <li class="active">Add</a></li>
    </ol>
    <div class="container-fluid">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <form id="form_validation" method="post" enctype="multipart/form-data">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>ADD APPS</h2>
                        </div>
                        <div class="body">
                            <?php if(isset($_SESSION['msg'])) { ?>
                            <div class='alert alert-info alert-dismissible corner-radius' role='alert'>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>&nbsp;&nbsp;</button>
                                <?php echo $_SESSION['msg']; ?>
                            </div>
                            <?php unset($_SESSION['msg']); } ?>                            
                            <div class="row clearfix">
                                <div class="form-group col-sm-12">
                                    <div class="form-line">
                                        <div class="font-12">applicationId (Package Name)</div>
                                        <input type="text" class="form-control" name="application_id" id="application_id" placeholder="com.domain.appname" required>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="font-12">Status</div>
                                    <select class="form-control show-tick" name="status" id="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div id="active">
                                    <input type="hidden" class="form-control" name="redirect_url" id="redirect_url" value="" required/>
                                </div>
                                <div id="inactive">
                                    <div class="form-group col-sm-12">
                                        <div class="form-line">
                                            <div class="font-12">Redirect Url</div>
                                            <input type="text" class="form-control" name="redirect_url" id="redirect_url" placeholder="https://play.google.com/store/apps/details?id=com.domain.appname" required>
                                        </div>
                                        <div class="help-info pull-left"><font color="#337ab7">Redirect url will be used if app status is not active so your current app user will be redirected to new url of your app with this existing admin panel data</font></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button class="button button-rounded waves-effect waves-float pull-right" type="submit" name="submit">SUBMIT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once('includes/footer.php'); ?>