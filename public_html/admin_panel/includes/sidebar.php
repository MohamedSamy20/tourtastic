<section>
    <aside id="leftsidebar" class="sidebar">
        <div class="user-info">
            <div class="info-container">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <img src="assets/images/ic_launcher.png" width="40px" height="40px" />
                </div>
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <b><?php echo $data['username'] ?></b>
                </div>
                <div class="email"><?php echo $data['email'] ?></div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="admin-edit.php?id=<?php echo $data['id']; ?>"><i class="material-icons">person</i>Profile</a></li>
                        <li><a href="logout.php"><i class="material-icons">power_settings_new</i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="menu">
            <ul class="list">
                <?php $page = $_SERVER['REQUEST_URI']; ?>
                <li class="header">MENU</li>
                <li class="<?php if (strpos($page, 'dashboard') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="dashboard.php">
                    <i class="material-icons">dashboard</i>
                    <span><?php echo $menuDashboard; ?></span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'navigation') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="navigation.php">
                    <i class="material-icons">view_list</i>
                    <span><?php echo $menuDrawer; ?></span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'ads') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="ads.php">
                    <i class="material-icons">monetization_on</i>
                    <span><?php echo $menuAds; ?></span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'notification') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="notification.php">
                    <i class="material-icons">notifications</i>
                    <span><?php echo $menuNotification; ?></span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'admin') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="admin.php">
                    <i class="material-icons">people</i>
                    <span><?php echo $menuAdministrator; ?></span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'settings') !== false || strpos($page, 'api-key') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="settings.php">
                    <i class="material-icons">settings</i>
                    <span><?php echo $menuSettings; ?></span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'project') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="project.php">
                    <i class="material-icons">folder</i>
                    <span>Project</span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'flight_provider') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="flight_provider.php">
                    <i class="material-icons">flight_takeoff</i>
                    <span>Flight Provider</span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'flight_settings') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="flight_settings.php">
                    <i class="material-icons">settings_applications</i>
                    <span>Flight Settings</span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'apps.php') !== false || strpos($page, 'apps-') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="apps.php">
                    <i class="material-icons">adb</i>
                    <span><?php echo $menuApp; ?></span>
                    </a>
                </li>
                <li class="<?php if (strpos($page, 'license') !== false) {echo 'active';} else { echo 'noactive'; }?>">
                    <a href="license.php">
                    <i class="material-icons">vpn_key</i>
                    <span><?php echo $menuLicense; ?></span>
                    </a>
                </li>
                <li>
                    <a href="logout.php" onclick="return confirm('Are you sure want to logout?')">
                    <i class="material-icons">power_settings_new</i>
                    <span><?php echo $menuLogout; ?></span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="legal">
            <div class="copyright">
                <?php echo $appCopyright; ?>
            </div>
            <div class="version">
                <b>Version: </b> <?php echo $appVersion; ?>
            </div>
        </div>

    </aside>

</section>