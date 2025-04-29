<?php include_once ('includes/header.php'); ?>

<?php

$menus = "SELECT COUNT(*) as num FROM tbl_menu";
$totalMenus = $connect->query($menus);
$totalMenus = $totalMenus->fetch_array();
$totalMenus = $totalMenus["num"];

?>

   <section class="content">

    <ol class="breadcrumb">
        <li class="active">Dashboard</a></li>
    </ol>

        <div class="container-fluid">
             
             <div class="row">

                <a href="navigation.php">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuDrawer; ?></div>
                            <div class="color-name"><i class="material-icons">view_list</i></div>
                            <div class="color-class-name">Total ( <?php echo $totalMenus; ?> ) Menus</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="ads.php">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuAds; ?></div>
                            <div class="color-name"><i class="material-icons">monetization_on</i></div>
                            <div class="color-class-name">App Monetization</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="notification.php">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuNotification; ?></div>
                            <div class="color-name"><i class="material-icons">notifications</i></div>
                            <div class="color-class-name">Send notification to your users</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="admin.php">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuAdministrator; ?></div>
                            <div class="color-name"><i class="material-icons">people</i></div>
                            <div class="color-class-name">Admin Panel Privileges</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="settings.php">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuSettings; ?></div>
                            <div class="color-name"><i class="material-icons">settings</i></div>
                            <div class="color-class-name">Key and Privacy Settings</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="apps.php">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuApp; ?></div>
                            <div class="color-name"><i class="material-icons">adb</i></div>
                            <div class="color-class-name">Apps and Redirect</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="license.php">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuLicense; ?></div>
                            <div class="color-name"><i class="material-icons">vpn_key</i></div>
                            <div class="color-class-name">Item purchase code</div>
                            <br>
                        </div>
                    </div>
                </a>

                <a href="logout.php" onclick="return confirm('Are you sure want to logout?')">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                        <div class="card demo-color-box bg-blue waves-effect corner-radius col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <br>
                            <div class="color-name uppercase"><?php echo $menuLogout; ?></div>
                            <div class="color-name"><i class="material-icons">power_settings_new</i></div>
                            <div class="color-class-name">Logout admin panel</div>
                            <br>
                        </div>
                    </div>
                </a>

            </div>
            
        </div>

    </section>

<?php include_once('includes/footer.php'); ?>