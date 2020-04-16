<?php if (count(get_included_files()) == 1) { exit; } ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Xtream UI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <link href="assets/libs/jquery-nice-select/nice-select.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/treeview/style.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/nestable2/jquery.nestable.min.css" rel="stylesheet" />
        <link href="assets/libs/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
		<?php if (!$rAdminSettings["dark_mode"]) { ?>
        <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.css" rel="stylesheet" type="text/css" />
		<?php } else { ?>
		<link href="assets/css/bootstrap.dark.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.dark.css" rel="stylesheet" type="text/css" />
		<?php } ?>
    </head>
    <body>
        <!-- Navigation Bar-->
        <header id="topnav">
            <!-- Topbar Start -->
            <div class="navbar-custom">
                <div class="container-fluid">
                    <ul class="list-unstyled topnav-menu float-right mb-0">
                        <li class="dropdown notification-list">
                            <!-- Mobile menu toggle-->
                            <a class="navbar-toggle nav-link">
                                <div class="lines text-white">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </li>
						<li class="notification-list username">
                            <a class="nav-link text-white waves-effect" href="./edit_profile.php" role="button" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Edit Profile">
                                <?=$rUserInfo["username"]?>
                            </a>
                        </li>
						<?php if (($rServerError) && ($rPermissions["is_admin"]) && (hasPermissions("adv", "servers"))) { ?>
                        <li class="notification-list">
                            <a href="./servers.php" class="nav-link right-bar-toggle waves-effect text-warning">
                                <i class="mdi mdi-wifi-strength-off noti-icon"></i>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($rPermissions["is_reseller"]) { ?>
                        <li class="notification-list">
                            <a class="nav-link text-white waves-effect" href="#" role="button">
                                <i class="mdi mdi-coins noti-icon"></i>
                                <?php if (floor($rUserInfo["credits"]) == $rUserInfo["credits"]) {
                                    echo number_format($rUserInfo["credits"], 0);
                                } else {
                                    echo number_format($rUserInfo["credits"], 2);
                                } ?>
                            </a>
                        </li>
                        <?php } ?>
                        <?php if ($rPermissions["is_admin"]) {
						if ((hasPermissions("adv", "settings")) OR (hasPermissions("adv", "database")) OR (hasPermissions("adv", "block_ips")) OR (hasPermissions("adv", "block_uas")) OR (hasPermissions("adv", "categories")) OR (hasPermissions("adv", "channel_order")) OR (hasPermissions("adv", "epg")) OR (hasPermissions("adv", "folder_watch")) OR (hasPermissions("adv", "mng_groups")) OR (hasPermissions("adv", "mass_delete")) OR (hasPermissions("adv", "mng_packages")) OR (hasPermissions("adv", "process_monitor")) OR (hasPermissions("adv", "rtmp")) OR (hasPermissions("adv", "subresellers")) OR (hasPermissions("adv", "tprofiles"))) { ?>
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="fe-settings noti-icon"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
								<?php if ((hasPermissions("adv", "settings")) OR (hasPermissions("adv", "database"))) { ?>
                                <a href="./settings.php" class="dropdown-item notify-item"><span>Settings</span></a>
								<?php }
								if (hasPermissions("adv", "block_ips")) { ?>
                                <a href="./ips.php" class="dropdown-item notify-item"><span>Blocked IP's</span></a>
								<?php }
								if (hasPermissions("adv", "block_uas")) { ?>
                                <a href="./useragents.php" class="dropdown-item notify-item"><span>Blocked User Agents</span></a>
								<?php }
								if (hasPermissions("adv", "categories")) { ?>
                                <a href="./stream_categories.php" class="dropdown-item notify-item"><span>Categories</span></a>
								<?php }
								if (hasPermissions("adv", "channel_order")) { ?>
                                <a href="./channel_order.php" class="dropdown-item notify-item"><span>Channel Order</span></a>
								<?php }
								if (hasPermissions("adv", "epg")) { ?>
                                <a href="./epgs.php" class="dropdown-item notify-item"><span>EPG's</span></a>
								<?php }
								if (hasPermissions("adv", "folder_watch")) { ?>
                                <a href="./watch.php" class="dropdown-item notify-item"><span>Folder Watch</span></a>
								<?php }
								if (hasPermissions("adv", "mng_groups")) { ?>
                                <a href="./groups.php" class="dropdown-item notify-item"><span>Groups</span></a>
								<?php }
								if (hasPermissions("adv", "mass_delete")) { ?>
                                <a href="./mass_delete.php" class="dropdown-item notify-item"><span>Mass Delete</span></a>
								<?php }
								if (hasPermissions("adv", "mng_packages")) { ?>
                                <a href="./packages.php" class="dropdown-item notify-item"><span>Packages</span></a>
								<?php }
								if (hasPermissions("adv", "process_monitor")) { ?>
                                <a href="./process_monitor.php?server=<?=$_INFO["server_id"]?>" class="dropdown-item notify-item"><span>Process Monitor</span></a>
								<?php }
								if (hasPermissions("adv", "rtmp")) { ?>
                                <a href="./rtmp_ips.php" class="dropdown-item notify-item"><span>RTMP IP's</span></a>
								<?php }
								if (hasPermissions("adv", "subresellers")) { ?>
                                <a href="./subresellers.php" class="dropdown-item notify-item"><span>Subresellers</span></a>
								<?php }
								if (hasPermissions("adv", "tprofiles")) { ?>
                                <a href="./profiles.php" class="dropdown-item notify-item"><span>Transcode Profiles</span></a>
								<?php } ?>
                            </div>
                        </li>
                        <?php }
						} ?>
                        <li class="notification-list">
                            <a href="./logout.php" class="nav-link right-bar-toggle waves-effect text-white">
                                <i class="fe-power noti-icon"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- LOGO -->
                    <div class="logo-box">
                        <a href="<?php if ($rPermissions["is_admin"]) { ?>dashboard.php<?php } else { ?>reseller.php<?php } ?>" class="logo text-center">
                            <span class="logo-lg">
                                <img src="assets/images/logo.png" alt="" height="26">
                            </span>
                            <span class="logo-sm">
                                <img src="assets/images/logo.png" alt="" height="28">
                            </span>
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- end Topbar -->
            <div class="topbar-menu">
                <div class="container-fluid">
                    <div id="navigation">
                        <!-- Navigation Menu-->
                        <ul class="navigation-menu">
                            <li>
                                <a href="./<?php if ($rPermissions["is_admin"]) { ?>dashboard.php<?php } else { ?>reseller.php<?php } ?>"><i class="la la-dashboard"></i>Dashboard</a>
                            </li>
                            <?php if (($rPermissions["is_reseller"]) && ($rPermissions["reseller_client_connection_logs"])) { ?>
                            <li class="has-submenu">
                                <a href="#"><i class="la la-exchange"></i>Connections <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./live_connections.php">Live Connections</a></li>
                                    <li><a href="./user_activity.php">Activity Logs</a></li>
                                </ul>
                            </li>
                            <?php }
                            if ($rPermissions["is_admin"]) {
							if ((hasPermissions("adv", "servers")) OR (hasPermissions("adv", "add_server")) OR (hasPermissions("adv", "live_connections")) OR (hasPermissions("adv", "connection_logs"))) { ?>
                            <li class="has-submenu">
                                <a href="#"><i class="la la-server"></i>Servers <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <?php if (hasPermissions("adv", "add_server")) { ?>
                                    <li><a href="./server.php">Add Existing LB</a></li>
                                    <li><a href="./install_server.php">Install Load Balancer</a></li>
									<?php }
									if (hasPermissions("adv", "servers")) { ?>
                                    <li><a href="./servers.php">Manage Servers</a></li>
									<?php }
                                    if ((hasPermissions("adv", "live_connections")) OR (hasPermissions("adv", "connection_logs"))) { ?>
                                    <div class="separator"></div>
                                    <?php }
									if (hasPermissions("adv", "live_connections")) { ?>
                                    <li><a href="./live_connections.php">Live Connections</a></li>
									<?php }
									if (hasPermissions("adv", "connection_logs")) { ?>
                                    <li><a href="./user_activity.php">Activity Logs</a></li>
									<?php } ?>
                                </ul>
                            </li>
                            <?php }
							if ((hasPermissions("adv", "add_user")) OR (hasPermissions("adv", "users")) OR (hasPermissions("adv", "mass_edit_users")) OR (hasPermissions("adv", "mng_regusers")) OR (hasPermissions("adv", "add_reguser")) OR (hasPermissions("adv", "credits_log")) OR (hasPermissions("adv", "client_request_log")) OR (hasPermissions("adv", "reg_userlog"))) { ?>
							<li class="has-submenu">
                                <a href="#"> <i class="la la-user"></i>Users <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <?php if (hasPermissions("adv", "add_user")) { ?>
                                    <li><a href="./user.php">Add User</a></li>
									<?php }
									if (hasPermissions("adv", "users")) { ?>
                                    <li><a href="./users.php">Manage Users</a></li>
									<?php }
									if (hasPermissions("adv", "mass_edit_users")) { ?>
                                    <li><a href="./user_mass.php">Mass Edit Users</a></li>
									<?php }
                                    if ((hasPermissions("adv", "add_reguser")) OR (hasPermissions("adv", "mng_regusers"))) { ?>
                                    <div class="separator"></div>
                                    <?php }
									if (hasPermissions("adv", "add_reguser")) { ?>
                                    <li><a href="./reg_user.php">Add Registered User</a></li>
									<?php }
									if (hasPermissions("adv", "mng_regusers")) { ?>
                                    <li><a href="./reg_users.php">Manage Registered Users</a></li>
									<?php }
                                    if ((hasPermissions("adv", "credits_log")) OR (hasPermissions("adv", "client_request_log")) OR (hasPermissions("adv", "reg_userlog"))) { ?>
                                    <div class="separator"></div>
                                    <?php }
									if (hasPermissions("adv", "credits_log")) { ?>
                                    <li><a href="./credit_logs.php">Credit Logs</a></li>
									<?php }
									if (hasPermissions("adv", "client_request_log")) { ?>
                                    <li><a href="./client_logs.php">Client Logs</a></li>
									<?php }
									if (hasPermissions("adv", "reg_userlog")) { ?>
                                    <li><a href="./reg_user_logs.php">Reseller Logs</a></li>
									<?php } ?>
                                </ul>
                            </li>
							<?php }
							} else { ?>
							<li class="has-submenu">
                                <a href="#"> <i class="la la-user"></i>Users <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <?php if ((!$rAdminSettings["disable_trial"]) && ($rPermissions["total_allowed_gen_trials"] > 0) && ($rUserInfo["credits"] >= $rPermissions["minimum_trial_credits"])) { ?>
                                    <li><a href="./user_reseller.php?trial">Generate Trial</a></li>
                                    <?php } ?>
                                    <li><a href="./user_reseller.php">Add User</a></li>
                                    <li><a href="./users.php">Manage Users</a></li>
                                </ul>
                            </li>
							<?php }
                            if (($rPermissions["is_reseller"]) && ($rPermissions["create_sub_resellers"])) { ?>
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-users"></i>Subresellers <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <?php if ($rPermissions["is_admin"]) { ?>
                                    <li><a href="./reg_user.php">Add Subreseller</a></li>
                                    <?php } else { ?>
                                    <li><a href="./subreseller.php">Add Subreseller</a></li>
                                    <?php } ?>
                                    <li><a href="./reg_users.php">Manage Subreseller</a></li>
                                </ul>
                            </li>
                            <?php }
							if ($rPermissions["is_admin"]) {
							if ((hasPermissions("adv", "add_mag")) OR (hasPermissions("adv", "manage_mag")) OR (hasPermissions("adv", "add_e2")) OR (hasPermissions("adv", "manage_e2")) OR (hasPermissions("adv", "manage_events"))) { ?>
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-tablet"></i>Devices <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <?php if (hasPermissions("adv", "add_mag")) { ?>
                                    <li><a href="./user.php?mag">Add MAG User</a></li>
                                    <li><a href="./mag.php">Link MAG User</a></li>
									<?php }
									if (hasPermissions("adv", "manage_mag")) { ?>
                                    <li><a href="./mags.php">Manage MAG Devices</a></li>
									<?php }
                                    if ((hasPermissions("adv", "add_e2")) OR (hasPermissions("adv", "manage_e2")) OR (hasPermissions("adv", "manage_events"))) { ?>
                                    <div class="separator"></div>
                                    <?php }
									if (hasPermissions("adv", "add_e2")) { ?>
                                    <li><a href="./user.php?e2">Add Enigma User</a></li>
                                    <li><a href="./enigma.php">Link Enigma User</a></li>
									<?php }
									if (hasPermissions("adv", "manage_e2")) { ?>
                                    <li><a href="./enigmas.php">Manage Enigma Devices</a></li>
									<?php }
									if (hasPermissions("adv", "manage_events")) { ?>
                                    <div class="separator"></div>
                                    <li><a href="./mag_events.php">MAG Event Logs</a></li>
									<?php } ?>
                                </ul>
                            </li>
							<?php }
							} else { ?>
							<li class="has-submenu">
                                <a href="#"> <i class="la la-tablet"></i>Devices <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./mags.php">Manage MAG Devices</a></li>
                                    <li><a href="./enigmas.php">Manage Enigma Devices</a></li>
                                </ul>
                            </li>
                            <?php }
							if ($rPermissions["is_admin"]) {
							if ((hasPermissions("adv", "add_movie")) OR (hasPermissions("adv", "import_movies")) OR (hasPermissions("adv", "movies")) OR (hasPermissions("adv", "series")) OR (hasPermissions("adv", "add_series")) OR (hasPermissions("adv", "radio")) OR (hasPermissions("adv", "add_radio")) OR (hasPermissions("adv", "mass_sedits_vod")) OR (hasPermissions("adv", "mass_sedits")) OR (hasPermissions("adv", "mass_edits_radio"))) { ?>
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-video-camera"></i>VOD <div class="arrow-down"></div></a>
                                <ul class="submenu megamenu">
                                    <li>
                                        <ul>
											<?php if (hasPermissions("adv", "add_movie")) { ?>
                                            <li><a href="./movie.php">Add Movie</a></li>
											<?php }
											if (hasPermissions("adv", "import_movies")) { ?>
                                            <li><a href="./movie.php?import">Import Movies</a></li>
											<?php }
											if (hasPermissions("adv", "movies")) { ?>
                                            <li><a href="./movies.php">Manage Movies</a></li>
											<?php }
                                            if ((hasPermissions("adv", "add_series")) OR (hasPermissions("adv", "series")) OR (hasPermissions("adv", "episodes"))) { ?>
                                            <div class="separator"></div>
                                            <?php }
											if (hasPermissions("adv", "add_series")) { ?>
                                            <li><a href="./serie.php">Add Series</a></li>
											<?php }
											if (hasPermissions("adv", "series")) { ?>
                                            <li><a href="./series.php">Manage Series</a></li>
											<?php }
											if (hasPermissions("adv", "episodes")) { ?>
                                            <li><a href="./episodes.php">Manage Episodes</a></li>
											<?php } ?>
                                        </ul>
                                    </li>
                                    <li>
                                        <ul>
											<?php if (hasPermissions("adv", "add_radio")) { ?>
                                            <li><a href="./radio.php">Add Station</a></li>
											<?php }
											if (hasPermissions("adv", "radio")) { ?>
                                            <li><a href="./radios.php">Manage Stations</a></li>
											<?php }
                                            if ((hasPermissions("adv", "mass_sedits_vod")) OR (hasPermissions("adv", "mass_sedits")) OR (hasPermissions("adv", "mass_edit_radio"))) { ?>
                                            <div class="separator"></div>
                                            <?php }
											if (hasPermissions("adv", "mass_sedits_vod")) { ?>
                                            <li><a href="./movie_mass.php">Mass Edit Movies</a></li>
											<?php }
											if (hasPermissions("adv", "mass_sedits")) { ?>
                                            <li><a href="./series_mass.php">Mass Edit Series</a></li>
                                            <li><a href="./episodes_mass.php">Mass Edit Episodes</a></li>
											<?php }
											if (hasPermissions("adv", "mass_edit_radio")) { ?>
                                            <li><a href="./radio_mass.php">Mass Edit Stations</a></li>
											<?php } ?>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
							<?php }
							if ((hasPermissions("adv", "add_stream")) OR (hasPermissions("adv", "import_streams")) OR (hasPermissions("adv", "create_channel")) OR (hasPermissions("adv", "streams")) OR (hasPermissions("adv", "mass_edit_streams"))  OR (hasPermissions("adv", "stream_tools"))  OR (hasPermissions("adv", "stream_errors"))  OR (hasPermissions("adv", "fingerprint"))) { ?>
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-play-circle-o"></i>Streams <div class="arrow-down"></div></a>
                                <ul class="submenu">
									<?php if (hasPermissions("adv", "add_stream")) { ?>
                                    <li><a href="./stream.php">Add Stream</a></li>
									<?php }
									if (hasPermissions("adv", "create_channel")) { ?>
                                    <li><a href="./created_channel.php">Create Channel</a></li>
									<?php }
									if (hasPermissions("adv", "import_streams")) { ?>
                                    <li><a href="./stream.php?import">Import Streams</a></li>
									<?php }
									if (hasPermissions("adv", "streams")) { ?>
                                    <li><a href="./streams.php">Manage Streams</a></li>
									<?php }
                                    if ((hasPermissions("adv", "mass_edit_streams")) OR (hasPermissions("adv", "stream_errors")) OR (hasPermissions("adv", "stream_tools"))  OR (hasPermissions("adv", "fingerprint"))) { ?>
                                    <div class="separator"></div>
                                    <?php }
									if (hasPermissions("adv", "mass_edit_streams")) { ?>
                                    <li><a href="./stream_mass.php">Mass Edit Streams</a></li>
									<?php }
									if (hasPermissions("adv", "stream_errors")) { ?>
                                    <li><a href="./stream_logs.php">Stream Logs</a></li>
									<?php }
									if (hasPermissions("adv", "stream_tools")) { ?>
									<li><a href="./stream_tools.php">Stream Tools</a></li>
									<?php }
									if (hasPermissions("adv", "fingerprint")) { ?>
                                    <li><a href="./fingerprint.php">Fingerprint</a></li>
									<?php } ?>
                                </ul>
                            </li>
							<?php }
							if ((hasPermissions("adv", "add_bouquet")) OR (hasPermissions("adv", "bouquets"))) { ?>
                            <li class="has-submenu">
                                <a href="#"> <i class="mdi mdi-flower-tulip-outline"></i>Bouquets <div class="arrow-down"></div></a>
                                <ul class="submenu">
									<?php if (hasPermissions("adv", "add_bouquet")) { ?>
                                    <li><a href="./bouquet.php">Add Bouquet</a></li>
									<?php }
									if (hasPermissions("adv", "bouquets")) { ?>
                                    <li><a href="./bouquets.php">Manage Bouquets</a></li>
									<?php } ?>
                                </ul>
                            </li>
                            <?php }
							}
                            if (($rPermissions["is_reseller"]) && ($rPermissions["reset_stb_data"])) { ?>
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-play-circle-o"></i>Content <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./streams.php">Streams</a></li>
                                    <li><a href="./movies.php">Movies</a></li>
                                    <li><a href="./series.php">Series</a></li>
                                    <li><a href="./episodes.php">Episodes</a></li>
                                    <li><a href="./radios.php">Stations</a></li>
                                </ul>
                            </li>
                            <?php }
                            if ($rPermissions["is_reseller"]) { ?>
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-envelope"></i>Support <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./ticket.php">Create Ticket</a></li>
                                    <li><a href="./tickets.php">Manage Tickets</a></li>
                                    <?php if ($rPermissions["allow_import"]) { ?>
                                    <li><a href="./resellersmarters.php">Reseller API Key</a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <?php }
                            if (($rPermissions["is_admin"]) && (hasPermissions("adv", "manage_tickets"))) { ?>
                            <li>
                                <a href="./tickets.php"> <i class="la la-envelope"></i>Tickets</a>
                            </li>
                            <?php } ?>
                        </ul>
                        <!-- End navigation menu -->
                        <div class="clearfix"></div>
                    </div>
                    <!-- end #navigation -->
                </div>
                <!-- end container -->
            </div>
            <!-- end navbar-custom -->
        </header>
        <!-- End Navigation Bar-->