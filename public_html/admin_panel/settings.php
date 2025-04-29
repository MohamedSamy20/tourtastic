<?php include_once "includes/header.php"; ?>
<script src="assets/plugins/ckeditor/ckeditor.js"></script>

<?php
$ID = clean("1");

if (isset($_POST["submit"])) {
    $data = [
        "notification_provider" => $_POST["notification_provider"],
        "fcm_server_key" => $_POST["fcm_server_key"],
        "fcm_notification_topic" => $_POST["fcm_notification_topic"],
        "onesignal_app_id" => $_POST["onesignal_app_id"],
        "onesignal_rest_api_key" => $_POST["onesignal_rest_api_key"],
        "privacy_policy" => $_POST["privacy_policy"],
        "more_apps_url" => $_POST["more_apps_url"],
        "user_agent" => $_POST["user_agent"],
        "toolbar" => $_POST["toolbar"],
        "navigation_drawer" => $_POST["navigation_drawer"],
        "geolocation" => $_POST["geolocation"],
        "cache" => $_POST["cache"],
        "zoom_controls" => $_POST["zoom_controls"],
    ];

    $update = update("tbl_settings", $data, "WHERE id = '$ID'");

    if ($update > 0) {
        $_SESSION["msg"] = "Changes saved...";
        header("Location:settings.php");
        exit();
    }
}

?>

<section class="content">
    <ol class="breadcrumb breadcrumb-offset">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li class="active">Settings</a></li>
    </ol>
    <div class="container-fluid" id="fade-in">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <form method="post" id="form_validation" enctype="multipart/form-data">
                    <div class="card corner-radius">
                        <div class="header">
                            <h2>SETTINGS</h2>
                            <div class="header-dropdown m-r--5">
                                <button type="submit" name="submit" class="button button-rounded btn-offset bg-blue waves-effect">&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;</button>
                            </div>
                        </div>
                        <div class="body">
                            <?php if(isset($_SESSION['msg'])) { ?>
                            <div class='alert alert-info alert-dismissible corner-radius' role='alert'>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>&nbsp;&nbsp;</button>
                                <?php echo $_SESSION['msg']; ?>
                            </div>
                            <?php unset($_SESSION['msg']); } ?>  
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                    <div style="font-size: 16px; margin-bottom: 10px;"><b>APP FEATURES</b></div>
                                    <div class="form-group">
                                        <div class="font-12">Toolbar</div>
                                        <select class="form-control show-tick" name="toolbar" id="toolbar">
                                            <?php if ($settingsRow['toolbar'] == '1') { ?>
                                            <option value="1" selected="selected">Yes</option>
                                            <option value="0">No</option>
                                            <?php } else { ?>
                                            <option value="1">Yes</option>
                                            <option value="0" selected="selected">No</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="font-12">Navigation Drawer</div>
                                        <select class="form-control show-tick" name="navigation_drawer" id="navigation_drawer">
                                            <?php if ($settingsRow['navigation_drawer'] == '1') { ?>
                                            <option value="1" selected="selected">Yes</option>
                                            <option value="0">No</option>
                                            <?php } else { ?>
                                            <option value="1">Yes</option>
                                            <option value="0" selected="selected">No</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="font-12">Geolocation</div>
                                        <select class="form-control show-tick" name="geolocation" id="geolocation">
                                            <?php if ($settingsRow['geolocation'] == '1') { ?>
                                            <option value="1" selected="selected">Yes</option>
                                            <option value="0">No</option>
                                            <?php } else { ?>
                                            <option value="1">Yes</option>
                                            <option value="0" selected="selected">No</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="font-12">WebView Cache</div>
                                        <select class="form-control show-tick" name="cache" id="cache">
                                            <?php if ($settingsRow['cache'] == '1') { ?>
                                            <option value="1" selected="selected">Yes</option>
                                            <option value="0">No</option>
                                            <?php } else { ?>
                                            <option value="1">Yes</option>
                                            <option value="0" selected="selected">No</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="font-12">Zoom Controls</div>
                                        <select class="form-control show-tick" name="zoom_controls" id="zoom_controls">
                                            <?php if ($settingsRow['zoom_controls'] == '1') { ?>
                                            <option value="1" selected="selected">Yes</option>
                                            <option value="0">No</option>
                                            <?php } else { ?>
                                            <option value="1">Yes</option>
                                            <option value="0" selected="selected">No</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div style="font-size: 16px; margin-top: 10px; margin-bottom: 10px;"><b>NOTIFICATIONS</b></div>
                                    <div class="form-group">
                                        <div class="font-12">Provider</div>
                                        <select class="form-control show-tick" name="notification_provider" id="notification_provider">
                                            <?php if ($settingsRow['notification_provider'] == 'onesignal') { ?>
                                            <option value="onesignal" selected="selected">OneSignal</option>
                                            <option value="firebase">Firebase Cloud Messaging (FCM)</option>
                                            <?php } else { ?>
                                            <option value="onesignal">OneSignal</option>
                                            <option value="firebase" selected="selected">Firebase Cloud Messaging (FCM)</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">FCM Server Key</div>
                                            <input type="text" class="form-control" name="fcm_server_key" id="fcm_server_key" value="<?php echo $settingsRow['fcm_server_key'];?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">FCM Notification Topic</div>
                                            <input type="text" class="form-control" name="fcm_notification_topic" id="fcm_notification_topic" value="<?php echo $settingsRow['fcm_notification_topic'];?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">OneSignal APP ID</div>
                                            <input type="text" class="form-control" name="onesignal_app_id" id="onesignal_app_id" value="<?php echo $settingsRow['onesignal_app_id'];?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">OneSignal Rest API Key</div>
                                            <input type="text" class="form-control" name="onesignal_rest_api_key" id="onesignal_rest_api_key" value="<?php echo $settingsRow['onesignal_rest_api_key'];?>" required>
                                        </div>
                                    </div>
                                    <div style="font-size: 16px; margin-top: 10px; margin-bottom: 10px;"><b>MORE APPS, USER AGENT & PRIVACY</b></div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">More Apps Url</div>
                                            <input type="text" class="form-control" name="more_apps_url" id="more_apps_url" value="<?php echo $settingsRow['more_apps_url'];?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12">Custom User Agent</div>
                                            <input type="text" class="form-control" name="user_agent" id="user_agent" placeholder="Mozilla/5.0 (Linux; Android 12; SM-S906N Build/QP1A.190711.020; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/80.0.3987.119 Mobile Safari/537.36" value="<?php echo $settingsRow['user_agent'];?>" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <div class="font-12 ex1" style="margin-bottom: 6px;">Privacy Policy</div>
                                            <textarea class="form-control" name="privacy_policy" id="privacy_policy" class="form-control" cols="60" rows="10" required><?php echo $settingsRow['privacy_policy'];?></textarea>
                                            <?php if ($ENABLE_RTL_MODE == 'true') { ?>
                                            <script>                             
                                                CKEDITOR.replace( 'privacy_policy' );
                                                CKEDITOR.config.contentsLangDirection = 'rtl';
                                            </script>
                                            <?php } else { ?>
                                            <script>                             
                                                CKEDITOR.replace( 'privacy_policy' );
                                                CKEDITOR.config.height = 300; 
                                            </script>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
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