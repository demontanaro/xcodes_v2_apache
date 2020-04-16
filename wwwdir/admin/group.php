<?php
include "session.php"; include "functions.php";
if ((!$rPermissions["is_admin"]) OR ((!hasPermissions("adv", "add_group")) && (!hasPermissions("adv", "edit_group")))) { exit; }

$rAdvPermissions = Array(
	Array("add_rtmp", "Add & Edit RTMP IP", "Add or edit RTMP IP."),
	Array("add_bouquet", "Add Bouquet", "Add a new bouquet, attribute streams, VOD and radio to the bouquet."),
	Array("add_cat", "Add Category", "Add a new category."),
	Array("add_e2", "Add Enigma Device", "Add a new enigma device or link a device with no credit requirements."),
	Array("add_epg", "Add EPG", "Add a new EPG."),
	Array("add_episode", "Add Episode", "Add a new episode to a TV Series, file browsing and adding to bouquets and servers."),
	Array("add_group", "Add Group", "Add a new group, with access to the permissions system."),
	Array("add_mag", "Add MAG Device", "Add a new MAG device or link a device with no credit requirements."),
	Array("add_movie", "Add Movie", "Add a new movie, file browsing and adding to bouquets and servers."),
	Array("add_packages", "Add Package", "Add a new package, apply to groups and add bouquets."),
	Array("add_radio", "Add Radio Station", "Add a new radio station and add to servers."),
	Array("add_reguser", "Add Registered User", "Add a new registered user, grant credits and override packages."),
	Array("add_server", "Add Server", "Add a new server or install a load balancer."),
	Array("add_stream", "Add Stream", "Add a new stream, file browsing and adding to bouquets and servers."),
	Array("tprofile", "Add Transcode Profile", "Add a transcode profile with access to all settings."),
	Array("add_series", "Add TV Series", "Add a new tv series and apply to bouquets."),
	Array("add_user", "Add User", "Add a new user with no credit requirements."),
	Array("block_ips", "Block IP Address", "Block an IP address from accessing the server."),
	Array("block_uas", "Block User Agent", "Block a User Agent from accessing streams."),
	Array("create_channel", "Create Channel", "Create a new channel, file browsing and adding to bouquets and servers."),
	Array("edit_bouquet", "Edit Bouquet", "Edit or delete bouquets, add streams, VOD and radio stations."),
	Array("edit_cat", "Edit Category", "Edit or delete category, view streams that are attributed to the category."),
	Array("channel_order", "Edit Channel Order", "Edit order of channels, VOD and radio stations."),
	Array("edit_cchannel", "Edit Created Channel", "Edit or delete created channel in addition to adding one."),
	Array("edit_e2", "Edit Enigma Device", "Edit or delete enigma device in addition to adding one."),
	Array("epg_edit", "Edit EPG", "Edit EPG in addition to adding one."),
	Array("edit_episode", "Edit Episode", "Edit or delete episode in addition to adding one."),
	Array("folder_watch_settings", "Edit Folder Watch Settings", "Edit folder watch settings in the folder watch page."),
	Array("settings", "Edit Settings", "Edit general, video, mag, security & Xtream UI settings."),
	Array("edit_group", "Edit Group", "Edit or delete group in addition to adding one."),
	Array("edit_mag", "Edit MAG Device", "Edit or delete mag device in addition to adding one."),
	Array("edit_movie", "Edit Movie", "Edit or delete movie in addition to adding one."),
	Array("edit_package", "Edit Package", "Edit or delete package in addition to adding one."),
	Array("edit_radio", "Edit Radio Station", "Edit or delete radio station in addition to adding one."),
	Array("edit_reguser", "Edit Registered User", "Edit or delete registered user in addition to adding one."),
	Array("edit_server", "Edit Server", "Edit or delete server in addition to adding one."),
	Array("edit_stream", "Edit Stream", "Edit or delete stream in addition to adding one."),
	Array("edit_series", "Edit TV Series", "Edit or delete tv series in addition to adding one."),
	Array("edit_user", "Edit User", "Edit or delete user in addition to adding one."),
	Array("fingerprint", "Fingerprint Stream", "Ability to fingerprint streams."),
	Array("import_episodes", "Import Episodes", "Import multiple episodes."),
	Array("import_movies", "Import Movies", "Import movies from folder or M3U."),
	Array("import_streams", "Import Streams", "Import streams from M3U."),
	Array("database", "Manage Database Backups", "View, generate or restore database backups in the settings menu."),
	Array("mass_delete", "Mass Delete Content", "Ability to mass delete users, channels, VOD and radio stations."),
	Array("mass_sedits_vod", "Mass Edit Movies", "Ability to mass edit movies."),
	Array("mass_sedits", "Mass Edit Series", "Ability to mass edit series and episodes."),
	Array("mass_edit_users", "Mass Edit Users", "Ability to mass edit users."),
	Array("mass_edit_streams", "Mass Edit Streams", "Ability to mass edit streams."),
	Array("mass_edit_radio", "Mass Edit Radio", "Ability to mass edit radio stations."),
	Array("ticket", "Reply to Tickets", "Ability to reply to tickets from any user as an admin."),
	Array("subreseller", "Setup Subreseller", "Setup access for a subreseller group."),
	Array("stream_tools", "Use Stream Tools", "Utilise stream tools to replace DNS, move streams or cleanup database."),
	Array("bouquets", "View Bouquets", "View bouquet list."),
	Array("categories", "View Categories", "View category list."),
	Array("client_request_log", "View Client Logs", "View client logs and grant ability to clear them."),
	Array("connection_logs", "View Activity Logs", "View activity logs and grant ability to clear them."),
	Array("manage_cchannels", "View Created Channels", "View list of created channels."),
	Array("credits_log", "View Credits Log", "View credit logs and grant ability to clear them."),
	Array("index", "View Dashboard", "View main dashboard with server overview."),
	Array("manage_e2", "View Enigma Devices", "View list of enigma devices."),
	Array("epg", "View EPG's", "View list of EPG's."),
	Array("folder_watch", "View Folder Watch", "View list of watched folder with ability to delete them or kill folder watch process."),
	Array("folder_watch_output", "View Folder Watch Output", "View list of succeeeded or failed folder watch items with ability to clear them."),
	Array("mng_groups", "View Groups", "View list of groups."),
	Array("live_connections", "View Live Connections", "View live connections and grant ability to kick users."),
	Array("login_logs", "View Login Logs", "View login logs and grant ability to clear them."),
	Array("manage_mag", "View MAG Devices", "View list of MAG devices with ability to send events, control them."),
	Array("manage_events", "View MAG Events", "View MAG event logs with ability to delete them."),
	Array("movies", "View Movies", "View list of movies."),
	Array("mng_packages", "View Packages", "View list of packages."),
	Array("player", "View Player", "Add player to streams and VOD listing pages."),
	Array("process_monitor", "View Process Monitor", "View active processes on each server with ability to kill them, clear temp or streams directory."),
	Array("radio", "View Radio Stations", "View list of radio stations."),
	Array("mng_regusers", "View Registered Users", "View list of registered users."),
	Array("reg_userlog", "View Reseller Logs", "View reseller logs and grant ability to clear them."),
	Array("rtmp", "View RTMP IP's", "View list of RTMP IP's"),
	Array("servers", "View Servers", "View list of servers."),
	Array("stream_errors", "View Stream Logs", "View stream logs and grant ability to clear them."),
	Array("streams", "View Streams", "View list of streams."),
	Array("subresellers", "View Subresellers", "View list of subreseller groups."),
	Array("manage_tickets", "View Tickets", "View list of tickets from all users, plus read them."),
	Array("tprofiles", "View Transcode Profiles", "View transcode profiles in addition to adding one."),
	Array("series", "View TV Series", "View list of TV series."),
	Array("users", "View Users", "View list of all users."),
	Array("episodes", "View TV Episodes", "View list of TV episodes."),
	Array("edit_tprofile", "Edit Transcode Profile", "Edit or delete profile in addition to adding one."),
	Array("folder_watch_add", "Add Folder Watch", "Add a folder to the folder watch cronjob for automatic scanning.")
);

if (isset($_POST["submit_group"])) {
    if (isset($_POST["edit"])) {
		if (!hasPermissions("adv", "edit_group")) { exit; }
        $rArray = getMemberGroup($_POST["edit"]);
		$rGroup = $rArray;
        unset($rArray["group_id"]);
    } else {
		if (!hasPermissions("adv", "add_group")) { exit; }
        $rArray = Array("group_name" => "", "group_color" => "", "is_banned" => 0, "is_admin" => 0, "is_reseller" => 0, "total_allowed_gen_in" => "day", "total_allowed_gen_trials" => 0, "minimum_trial_credits" => 0, "can_delete" => 1, "delete_users" => 0, "allowed_pages" => "", "reseller_force_server" => "", "create_sub_resellers_price" => 0, "create_sub_resellers" => 0, "alter_packages_ids" => 0, "alter_packages_prices" => 0, "reseller_client_connection_logs" => 0, "reseller_assign_pass" => 0, "allow_change_pass" => 0, "allow_import" => 0, "allow_export" => 0, "reseller_trial_credit_allow" => 0, "edit_mac" => 0, "edit_isplock" => 0, "reset_stb_data" => 0, "reseller_bonus_package_inc" => 0, "allow_download" => 1);
    }
    if (strlen($_POST["group_name"]) == 0) {
        $_STATUS = 1;
    }
    foreach (Array("is_admin", "is_reseller", "is_banned", "delete_users", "create_sub_resellers", "allow_change_pass", "allow_download", "reseller_client_connection_logs", "reset_stb_data", "allow_import") as $rSelection) {
        if (isset($_POST[$rSelection])) {
            $rArray[$rSelection] = 1;
            unset($_POST[$rSelection]);
        } else {
            $rArray[$rSelection] = 0;
        }
    }
	if ((!$rArray["can_delete"]) && (isset($_POST["edit"]))) {
		$rArray["is_admin"] = $rGroup["is_admin"];
		$rArray["is_reseller"] = $rGroup["is_reseller"];
	}
	$rArray["allowed_pages"] = array_values(json_decode($_POST["permissions_selected"], True));
    unset($_POST["permissions_selected"]);
    if (!isset($_STATUS)) {
        foreach($_POST as $rKey => $rValue) {
            if (isset($rArray[$rKey])) {
                $rArray[$rKey] = $rValue;
            }
        }
        $rCols = $db->real_escape_string(implode(',', array_keys($rArray)));
        foreach (array_values($rArray) as $rValue) {
            isset($rValues) ? $rValues .= ',' : $rValues = '';
            if (is_array($rValue)) {
                $rValue = json_encode($rValue);
            }
            if (is_null($rValue)) {
                $rValues .= 'NULL';
            } else {
                $rValues .= '\''.$db->real_escape_string($rValue).'\'';
            }
        }
        if (isset($_POST["edit"])) {
            $rCols = "`group_id`,".$rCols;
            $rValues = $_POST["edit"].",".$rValues;
        }
        $rQuery = "REPLACE INTO `member_groups`(".$rCols.") VALUES(".$rValues.");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
            header("Location: ./group.php?id=".$rInsertID); exit;
        } else {
            $_STATUS = 2;
        }
    }
}

if (isset($_GET["id"])) {
    $rGroup = getMemberGroup($_GET["id"]);
    if ((!$rGroup) OR (!hasPermissions("adv", "edit_group"))) {
        exit;
    }
} else if (!hasPermissions("adv", "add_group")) {
	exit;
}

if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
        if ($rSettings["sidebar"]) { ?>
        <div class="content-page"><div class="content boxed-layout-ext"><div class="container-fluid">
        <?php } else { ?>
        <div class="wrapper boxed-layout-ext"><div class="container-fluid">
        <?php } ?>
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <a href="./groups.php"><li class="breadcrumb-item"><i class="mdi mdi-backspace"></i> Back to Groups</li></a>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rGroup)) { echo "Edit"; } else { echo "Add"; } ?> Group</h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 
                <div class="row">
                    <div class="col-xl-12">
                        <?php if ((isset($_STATUS)) && ($_STATUS == 0)) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            Group operation was completed successfully.
                        </div>
                        <?php } else if ((isset($_STATUS)) && ($_STATUS > 0)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            There was an error performing this operation! Please check the form entry and try again.
                        </div>
                        <?php } ?>
                        <div class="card">
                            <div class="card-body">
                                <form action="./group.php<?php if (isset($_GET["id"])) { echo "?id=".$_GET["id"]; } ?>" method="POST" id="group_form" data-parsley-validate="">
                                    <?php if (isset($rGroup)) { ?>
                                    <input type="hidden" name="edit" value="<?=$rGroup["group_id"]?>" />
                                    <?php } ?>
									<input type="hidden" name="permissions_selected" id="permissions_selected" value="" />
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#group-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-card-details-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Details</span>
                                                </a>
                                            </li>
											<li class="nav-item">
                                                <a href="#reseller" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-badge-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Reseller Permissions</span>
                                                </a>
                                            </li>
											<?php if ((!isset($rGroup)) OR ($rGroup["can_delete"])) { ?>
											<li class="nav-item">
                                                <a href="#permissions" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-badge-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Admin Permissions</span>
                                                </a>
                                            </li>
											<?php } ?>
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="group-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="group_name">Group Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="group_name" name="group_name" value="<?php if (isset($rGroup)) { echo htmlspecialchars($rGroup["group_name"]); } ?>" required data-parsley-trigger="change">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="is_admin">Is Admin</label>
                                                            <div class="col-md-2">
                                                                <input name="is_admin" id="is_admin" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["is_admin"]) { echo "checked "; } if (!$rGroup["can_delete"]) { echo "disabled "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="is_reseller">Is Reseller</label>
                                                            <div class="col-md-2">
                                                                <input name="is_reseller" id="is_reseller" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["is_reseller"]) { echo "checked "; } if (!$rGroup["can_delete"]) { echo "disabled "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="is_banned">Is Banned</label>
                                                            <div class="col-md-2">
                                                                <input name="is_banned" id="is_banned" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["is_banned"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="list-inline-item float-right">
                                                        <input name="submit_group" type="submit" class="btn btn-primary" value="<?php if (isset($rGroup)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
											<div class="tab-pane" id="reseller">
                                                <div class="row">
                                                    <div class="col-12">
														<p class="sub-header">
                                                            The below permissions will only take effect if the group has the Reseller permission set.
                                                        </p>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="total_allowed_gen_trials">Allowed Trials</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="total_allowed_gen_trials" name="total_allowed_gen_trials" value="<?php if (isset($rGroup)) { echo intval($rGroup["total_allowed_gen_trials"]); } else { echo "0"; } ?>" required data-parsley-trigger="change">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="total_allowed_gen_in">Allowed Trials In</label>
                                                            <div class="col-md-2">
                                                                <select name="total_allowed_gen_in" id="total_allowed_gen_in" class="form-control select2" data-toggle="select2">
                                                                    <?php foreach (Array("Day", "Month") as $rOption) { ?>
                                                                    <option <?php if (isset($rGroup)) { if ($rGroup["total_allowed_gen_in"] == strtolower($rOption)) { echo "selected "; } } ?>value="<?=strtolower($rOption)?>"><?=$rOption?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
															<label class="col-md-4 col-form-label" for="allow_import">Can Use Reseller API</label>
                                                            <div class="col-md-2">
                                                                <input name="allow_import" id="allow_import" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["allow_import"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="delete_users">Can Delete Users</label>
                                                            <div class="col-md-2">
                                                                <input name="delete_users" id="delete_users" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["delete_users"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="create_sub_resellers">Can Create Subresellers</label>
                                                            <div class="col-md-2">
                                                                <input name="create_sub_resellers" id="create_sub_resellers" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["create_sub_resellers"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="create_sub_resellers_price">Subreseller Price</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="create_sub_resellers_price" name="create_sub_resellers_price" value="<?php if (isset($rGroup)) { echo htmlspecialchars($rGroup["create_sub_resellers_price"]); } else { echo "0"; } ?>" required data-parsley-trigger="change">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="allow_change_pass">Can Change Logins</label>
                                                            <div class="col-md-2">
                                                                <input name="allow_change_pass" id="allow_change_pass" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["allow_change_pass"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="allow_download">Can Download Playlist</label>
                                                            <div class="col-md-2">
                                                                <input name="allow_download" id="allow_download" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["allow_download"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="reset_stb_data">Can View VOD & Streams</label>
                                                            <div class="col-md-2">
                                                                <input name="reset_stb_data" id="reset_stb_data" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["reset_stb_data"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="reseller_client_connection_logs">Can View Live Connections</label>
                                                            <div class="col-md-2">
                                                                <input name="reseller_client_connection_logs" id="reseller_client_connection_logs" type="checkbox" <?php if (isset($rGroup)) { if ($rGroup["reseller_client_connection_logs"]) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
														<div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="minimum_trial_credits">Minimum Credits for Trials</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="minimum_trial_credits" name="minimum_trial_credits" value="<?php if (isset($rGroup)) { echo intval($rGroup["minimum_trial_credits"]); } else { echo "0"; } ?>" required data-parsley-trigger="change">
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_group" type="submit" class="btn btn-primary" value="<?php if (isset($rGroup)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
											<div class="tab-pane" id="permissions">
                                                <div class="row">
                                                    <div class="col-12">
														<p class="sub-header">
                                                            The below permissions will only take effect if the group has the Admin permission set. Selecting no permissions is equivalent to granting full access.<br/>
															Advanced Permissions will not affect the main Administrator group, please create a separate group for sub-admins.
                                                        </p>
                                                        <div class="form-group row mb-4">
                                                            <table id="datatable-permissions" class="table table-borderless mb-0">
                                                                <thead class="bg-light">
                                                                    <tr>
                                                                        <th style="display:none;">ID</th>
                                                                        <th>Permission</th>
                                                                        <th>Description</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($rAdvPermissions as $rPermission) { ?>
                                                                    <tr<?php if ((isset($rGroup)) & (in_array($rPermission[0], json_decode($rGroup["allowed_pages"], True)))) { echo " class='selected selectedfilter ui-selected'"; } ?>>
                                                                        <td style="display:none;"><?=$rPermission[0]?></td>
                                                                        <td><?=$rPermission[1]?></td>
                                                                        <td><?=$rPermission[2]?></td>
                                                                    </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
													<li class="next list-inline-item">
														<a href="javascript: void(0);" onClick="selectAll()" class="btn btn-info">Select All</a>
														<a href="javascript: void(0);" onClick="selectNone()" class="btn btn-warning">De-select All</a>
													</li>
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_group" type="submit" class="btn btn-primary" value="<?php if (isset($rGroup)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
                                        </div> <!-- tab-content -->
                                    </div> <!-- end #basicwizard-->
                                </form>

                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                </div>
            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->
        <?php if ($rSettings["sidebar"]) { echo "</div>"; } ?>
        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 copyright text-center"><?=getFooter()?></div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->

        <script src="assets/js/vendor.min.js"></script>
        <script src="assets/libs/jquery-toast/jquery.toast.min.js"></script>
        <script src="assets/libs/jquery-ui/jquery-ui.min.js"></script>
        <script src="assets/libs/jquery-nice-select/jquery.nice-select.min.js"></script>
        <script src="assets/libs/switchery/switchery.min.js"></script>
        <script src="assets/libs/select2/select2.min.js"></script>
        <script src="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="assets/libs/clockpicker/bootstrap-clockpicker.min.js"></script>
        <script src="assets/libs/moment/moment.min.js"></script>
        <script src="assets/libs/daterangepicker/daterangepicker.js"></script>
        <script src="assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables/dataTables.bootstrap4.js"></script>
        <script src="assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <script src="assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables/buttons.flash.min.js"></script>
        <script src="assets/libs/datatables/buttons.print.min.js"></script>
        <script src="assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="assets/libs/datatables/dataTables.select.min.js"></script>
        <script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="assets/libs/treeview/jstree.min.js"></script>
        <script src="assets/js/pages/treeview.init.js"></script>
        <script src="assets/js/pages/form-wizard.init.js"></script>
        <script src="assets/libs/parsleyjs/parsley.min.js"></script>
        <script src="assets/js/app.min.js"></script>
        
        <script>
		var rPermissions = [];

        (function($) {
          $.fn.inputFilter = function(inputFilter) {
            return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
              if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
              } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
              }
            });
          };
        }(jQuery));
        
        function selectAll() {
            $("#datatable-permissions tr").each(function() {
                if (!$(this).hasClass('selected')) {
                    $(this).addClass('selectedfilter').addClass('ui-selected').addClass("selected");
                    if ($(this).find("td:eq(0)").html()) {
                        window.rPermissions.push(parseInt($(this).find("td:eq(0)").html()));
                    }
                }
            });
        }
        
        function selectNone() {
            $("#datatable-permissions tr").each(function() {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selectedfilter').removeClass('ui-selected').removeClass("selected");
                    if ($(this).find("td:eq(0)").html()) {
                        window.rPermissions.splice(parseInt($.inArray($(this).find("td:eq(0)").html()), window.rPermissions), 1);
                    }
                }
            });
        }
        
        $(document).ready(function() {
            $('select.select2').select2({width: '100%'})
            $(".js-switch").each(function (index, element) {
                var init = new Switchery(element);
            });
            
            $(document).keypress(function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
            });
			
			$("#datatable-permissions").DataTable({
                "rowCallback": function(row, data) {
                    if ($.inArray(data[0], window.rPermissions) !== -1) {
                        $(row).addClass("selected");
                    }
                },
				order: [[ 1, "asc" ]],
                paging: false,
                bInfo: false,
                searching: false
            });
            $("#datatable-permissions").selectable({
                filter: 'tr',
                selected: function (event, ui) {
                    if ($(ui.selected).hasClass('selectedfilter')) {
                        $(ui.selected).removeClass('selectedfilter').removeClass('ui-selected').removeClass("selected");
                        window.rPermissions.splice(parseInt($.inArray($(ui.selected).find("td:eq(0)").html()), window.rPermissions), 1);
                    } else {            
                        $(ui.selected).addClass('selectedfilter').addClass('ui-selected').addClass("selected");
                        window.rPermissions.push(parseInt($(ui.selected).find("td:eq(0)").html()));
                    }
                }
            });
			$("#datatable-permissions_wrapper").css("width","100%");
			$("#datatable-permissions").css("width","100%");
			$("#group_form").submit(function(e){
                var rPermissions = [];
                $("#datatable-permissions tr.selected").each(function() {
                    rPermissions.push($(this).find("td:eq(0)").html());
                });
                $("#permissions_selected").val(JSON.stringify(rPermissions));
            });

            $("#max_connections").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#trial_credits").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#trial_duration").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#official_credits").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#official_duration").inputFilter(function(value) { return /^\d*$/.test(value); });
			$("#total_allowed_gen_trials").inputFilter(function(value) { return /^\d*$/.test(value); });
			$("#minimum_trial_credits").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("form").attr('autocomplete', 'off');
        });
        </script>
    </body>
</html>