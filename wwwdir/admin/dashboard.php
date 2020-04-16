<?php
include "session.php"; include "functions.php";
if (!$rPermissions["is_admin"]) { header("Location: ./reseller.php"); }

if ($rAdminSettings["dark_mode"]) {
	$rColours = Array(1 => Array("secondary", "#7e8e9d"), 2 => Array("secondary", "#7e8e9d"), 3 => Array("secondary", "#7e8e9d"), 4 => Array("secondary", "#7e8e9d"));
} else {
	$rColours = Array(1 => Array("purple", "#675db7"), 2 => Array("success", "#23b397"), 3 => Array("pink", "#e36498"), 4 => Array("info", "#56C3D6"));
}

if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
        if ($rSettings["sidebar"]) { ?>
        <div class="content-page"><div class="content"><div class="container-fluid">
        <?php } else { ?>
        <div class="wrapper"><div class="container-fluid">
        <?php } ?>
				<?php if (hasPermissions("adv", "index")) { ?>
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <ul class="nav nav-tabs nav-bordered dashboard-tabs" style="flex-wrap: nowrap !important;">
                                <li class="nav-item">
                                    <a data-id="home" href="#" class="nav-link active">
                                        Overview
                                    </a>
                                </li>
                                <?php foreach ($rServers as $rServer) { ?>
                                <li class="nav-item">
                                    <a data-id="<?=$rServer["id"]?>" href="#" class="nav-link">
                                        <?=$rServer["server_name"]?>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 
                <div class="tab-content">
                    <div class="tab-pane show active" id="server-home">
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
								<?php if (hasPermissions("adv", "live_connections")) { ?>
								<a href="./live_connections.php">
								<?php } ?>
									<div class="card-box active-connections">
										<div class="row">
											<div class="col-6">
												<?php if ($rAdminSettings["dark_mode"]) { ?>
												<div class="avatar-sm bg-secondary rounded">
													<i class="fe-zap avatar-title font-22 text-white"></i>
												</div>
												<?php } else { ?>
												<div class="avatar-sm bg-soft-purple rounded">
													<i class="fe-zap avatar-title font-22 text-purple"></i>
												</div>
												<?php } ?>
											</div>
											<div class="col-6">
												<div class="text-right">
													<h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
													<p class="text-muted mb-1 text-truncate">Open Connections</p>
												</div>
											</div>
										</div>
									</div> <!-- end card-box-->
								<?php if (hasPermissions("adv", "live_connections")) { ?>
								</a>
								<?php } ?>
                            </div> <!-- end col -->

                            <div class="col-md-6 col-xl-3">
								<?php if (hasPermissions("adv", "live_connections")) { ?>
								<a href="./live_connections.php">
								<?php } ?>
									<div class="card-box online-users">
										<div class="row">
											<div class="col-6">
												<?php if ($rAdminSettings["dark_mode"]) { ?>
												<div class="avatar-sm bg-secondary rounded">
													<i class="fe-zap avatar-title font-22 text-white"></i>
												</div>
												<?php } else { ?>
												<div class="avatar-sm bg-soft-success rounded">
													<i class="fe-users avatar-title font-22 text-success"></i>
												</div>
												<?php } ?>
											</div>
											<div class="col-6">
												<div class="text-right">
													<h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
													<p class="text-muted mb-1 text-truncate">Online Users</p>
												</div>
											</div>
										</div>
									</div> <!-- end card-box-->
								<?php if (hasPermissions("adv", "live_connections")) { ?>
								</a>
								<?php } ?>
                            </div> <!-- end col -->
							
							<div class="col-md-6 col-xl-3">
								<?php if (hasPermissions("adv", "streams")) { ?>
								<a href="./streams.php?filter=1">
								<?php } ?>
									<div class="card-box active-streams">
										<div class="row">
											<div class="col-6">
												<?php if ($rAdminSettings["dark_mode"]) { ?>
												<div class="avatar-sm bg-secondary rounded">
													<i class="fe-zap avatar-title font-22 text-white"></i>
												</div>
												<?php } else { ?>
												<div class="avatar-sm bg-soft-pink rounded">
													<i class="fe-play avatar-title font-22 text-pink"></i>
												</div>
												<?php } ?>
											</div>
											<div class="col-6">
												<div class="text-right">
													<h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
													<p class="text-muted mb-1 text-truncate">Online Streams</p>
												</div>
											</div>
										</div>
									</div> <!-- end card-box-->
								<?php if (hasPermissions("adv", "streams")) { ?>
								</a>
								<?php } ?>
                            </div> <!-- end col -->
							
							<div class="col-md-6 col-xl-3">
								<?php if (hasPermissions("adv", "streams")) { ?>
								<a href="./streams.php?filter=2">
								<?php } ?>
									<div class="card-box offline-streams">
										<div class="row">
											<div class="col-6">
												<?php if ($rAdminSettings["dark_mode"]) { ?>
												<div class="avatar-sm bg-secondary rounded">
													<i class="fe-zap avatar-title font-22 text-white"></i>
												</div>
												<?php } else { ?>
												<div class="avatar-sm bg-soft-info rounded">
													<i class="fe-pause avatar-title font-22 text-info"></i>
												</div>
												<?php } ?>
											</div>
											<div class="col-6">
												<div class="text-right">
													<h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
													<p class="text-muted mb-1 text-truncate">Offline Streams</p>
												</div>
											</div>
										</div>
									</div> <!-- end card-box-->
								<?php if (hasPermissions("adv", "streams")) { ?>
								</a>
								<?php } ?>
                            </div> <!-- end col -->
							<?php $i = 0; foreach ($rServers as $rServer) { $i ++; if ($i == 5) { $i = 1; } ?>
							<div class="col-xl-3 col-md-6">
								<div class="card-header bg-<?=$rColours[$i][0]?> py-3 text-white">
									<?php if (hasPermissions("adv", "live_connections")) { ?>
									<div class="float-right">
										<a href="./live_connections.php?server_id=<?=$rServer["id"]?>" class="arrow-none card-drop">
											<i class="mdi mdi-play"></i>
										</a>
									</div>
									<?php } ?>
									<h5 class="card-title mb-0 text-white"><?=$rServer["server_name"]?></h5>
								</div>
								<div class="card-header py-3 text-white<?php if (!$rAdminSettings["dark_mode"]) { echo " bg-white"; } ?>">
									<div class="row">
										<div class="col-md-4" align="center">
											<h4 class="header-title">Conns.</h4>
											<p class="sub-header" id="s_<?=$rServer["id"]?>_conns">0</p>
										</div>
										<div class="col-md-4" align="center">
											<h4 class="header-title">Users</h4>
											<p class="sub-header" id="s_<?=$rServer["id"]?>_users">0</p>
										</div>
										<div class="col-md-4" align="center">
											<h4 class="header-title">Online</h4>
											<p class="sub-header" id="s_<?=$rServer["id"]?>_online">0</p>
										</div>
									</div>
									<div class="row" style="margin-bottom:-20px;">
										<div class="col-md-4" align="center">
											<h4 class="header-title">Input</h4>
											<p class="sub-header" id="s_<?=$rServer["id"]?>_input">0 Mbps</p>
										</div>
										<div class="col-md-4" align="center">
											<h4 class="header-title">Output</h4>
											<p class="sub-header" id="s_<?=$rServer["id"]?>_output">0 Mbps</p>
										</div>
										<div class="col-md-4" align="center">
											<h4 class="header-title">Uptime</h4>
											<p class="sub-header" id="s_<?=$rServer["id"]?>_uptime">0d 0h</p>
										</div>
									</div>
								</div>
								<div class="card-box">
									<div class="row">
                                        <div class="col-md-6" align="center">
                                            <a href="./process_monitor.php?server=<?=$rServer["id"]?>">
                                                <h4 class="header-title">CPU Usage</h4>
                                                <input id="s_<?=$rServer["id"]?>_cpu" data-plugin="knob" data-width="64" data-height="64" data-fgColor="<?=$rColours[$i][1]?>" data-bgColor="#e8e7f4" value="0" data-skin="tron" data-angleOffset="180" data-readOnly=true data-thickness=".15"/>
                                            </a>
                                        </div>
                                        <div class="col-md-6" align="center">
                                            <a href="./process_monitor.php?server=<?=$rServer["id"]?>&mem">
                                                <h4 class="header-title">Mem Usage</h4>
                                                <input id="s_<?=$rServer["id"]?>_mem" data-plugin="knob" data-width="64" data-height="64" data-fgColor="<?=$rColours[$i][1]?>" data-bgColor="#e8e7f4" value="0" data-skin="tron" data-angleOffset="180" data-readOnly=true data-thickness=".15"/>
                                            </a>
                                        </div>
									</div>
								</div>
							</div>
							<?php } ?>
                        </div>
                    </div>
                    
                    <div class="tab-pane tab-pane-server" id="server-tab">
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card-box active-connections">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-purple rounded">
                                                <i class="fe-zap avatar-title font-22 text-purple"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
                                                <p class="text-muted mb-1 text-truncate">Open Connections</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">Total Connections <span class="float-right entry-percentage">0</span></h6>
                                        <div class="progress progress-sm m-0">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } else { ?>
											<div class="progress-bar bg-purple" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } ?>
                                                <span class="sr-only">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box online-users">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-success rounded">
                                                <i class="fe-users avatar-title font-22 text-success"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
                                                <p class="text-muted mb-1 text-truncate">Online Users</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">Total Active <span class="float-right entry-percentage">0</span></h6>
                                        <div class="progress progress-sm m-0">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } else { ?>
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } ?>
                                                <span class="sr-only">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box input-flow">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-primary rounded">
                                                <i class="fe-trending-down avatar-title font-22 text-primary"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span> <small>Mbps</small></h3>
                                                <p class="text-muted mb-1 text-truncate">Input Flow</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">Network Load <span class="float-right entry-percentage">0%</span></h6>
                                        <div class="progress progress-sm m-0">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } else { ?>
                                            <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } ?>
                                                <span class="sr-only">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box output-flow">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-info rounded">
                                                <i class="fe-trending-up avatar-title font-22 text-info"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span> <small>Mbps</small></h3>
                                                <p class="text-muted mb-1 text-truncate">Output Flow</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">Network Load <span class="float-right entry-percentage">0%</span></h6>
                                        <div class="progress progress-sm m-0">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } else { ?>
                                            <div class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } ?>
                                                <span class="sr-only">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->
                            
                            <div class="col-md-6 col-xl-3">
                                <div class="card-box active-streams">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-purple rounded">
                                                <i class="fe-arrow-up-right avatar-title font-22 text-purple"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <a href="javascript:void(0);" onClick="onlineStreams()">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
                                                <p class="text-muted mb-1 text-truncate">Online Streams</p>
                                            </div>
                                            </a>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" onClick="offlineStreams()">
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">Offline Streams <span class="float-right entry-percentage">0</span></h6>
                                        <div class="progress progress-sm m-0">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } else { ?>
                                            <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } ?>
                                                <span class="sr-only">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                    </a>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box cpu-usage">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-success rounded">
                                                <i class="fe-cpu avatar-title font-22 text-success"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span><small>%</small></h3>
                                                <p class="text-muted mb-1 text-truncate">CPU Usage</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">&nbsp;</h6>
                                        <div class="progress progress-sm m-0">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } else { ?>
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } ?>
                                                <span class="sr-only">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->

                            <div class="col-md-6 col-xl-3">
                                <div class="card-box mem-usage">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-primary rounded">
                                                <i class="fe-terminal avatar-title font-22 text-primary"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span><small>%</small></h3>
                                                <p class="text-muted mb-1 text-truncate">Memory Usage</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">&nbsp;</h6>
                                        <div class="progress progress-sm m-0">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
                                            <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } else { ?>
                                            <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
											<?php } ?>
                                                <span class="sr-only">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->
                           
                            <div class="col-md-6 col-xl-3">
                                <div class="card-box uptime">
                                    <div class="row">
                                        <div class="col-6">
											<?php if ($rAdminSettings["dark_mode"]) { ?>
											<div class="avatar-sm bg-secondary rounded">
												<i class="fe-zap avatar-title font-22 text-white"></i>
											</div>
											<?php } else { ?>
                                            <div class="avatar-sm bg-soft-info rounded">
                                                <i class="fe-power avatar-title font-22 text-info"></i>
                                            </div>
											<?php } ?>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-right">
                                                <h3 class="text-dark my-1 entry">--</h3>
                                                <p class="text-muted mb-1 text-truncate">System Uptime</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">&nbsp;</span></h6>
                                        <div class="progress-sm m-0"></div>
                                    </div>
                                </div> <!-- end card-box-->
                            </div> <!-- end col -->

                        </div>
                    </div>
                </div>
                <!-- end row -->
				<?php } else { ?>
				<div class="alert alert-danger show text-center" role="alert" style="margin-top:20px;">
					Welcome to Xtream UI! You do not have permission to view the dashboard.<br/>
					<?php if ($rSettings["sidebar"]) { echo "Please navigate using the menu on the left."; } else { echo "Please navigate using the top menu."; } ?>
				</div>
				<?php } ?>
               
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
        <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
        <script src="assets/libs/peity/jquery.peity.min.js"></script>
        <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
        <script src="assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/libs/jquery-number/jquery.number.js"></script>
        <script src="assets/libs/datatables/dataTables.bootstrap4.js"></script>
        <script src="assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <script src="assets/js/pages/dashboard.init.js"></script>
        <script src="assets/js/app.min.js"></script>
        
        <script>
        rServerID = "home";
        
        function offlineStreams() {
            window.location.href = "./streams.php?filter=2&server=" + window.rServerID;
        }
        
        function onlineStreams() {
            window.location.href = "./streams.php?filter=1&server=" + window.rServerID;
        }
        
        function getStats() {
            var rStart = Date.now();
            if (window.rServerID == "home") {
                rURL = "./api.php?action=stats";
            } else {
                rURL = "./api.php?action=stats&server_id=" + window.rServerID;
            }
            $.getJSON(rURL, function(data) {
                // Open Connections
                var rCapacity = Math.ceil((data.open_connections / data.total_connections) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".active-connections .entry").html($.number(data.open_connections, 0));
                $(".active-connections .entry-percentage").html($.number(data.total_connections, 0));
                $(".active-connections .progress-bar").prop("aria-valuenow", rCapacity);
                $(".active-connections .progress-bar").css("width", rCapacity.toString() + "%");
                $(".active-connections .sr-only").html(rCapacity.toString() + "%");
                // Online Users
                var rCapacity = Math.ceil((data.online_users / data.total_users) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".online-users .entry").html($.number(data.online_users, 0));
                $(".online-users .entry-percentage").html($.number(data.total_users, 0));
                $(".online-users .progress-bar").prop("aria-valuenow", rCapacity);
                $(".online-users .progress-bar").css("width", rCapacity.toString() + "%");
                $(".online-users .sr-only").html(rCapacity.toString() + "%");
                // Network Load - Input
                var rCapacity = Math.ceil((Math.ceil(data.bytes_received) / data.network_guaranteed_speed) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".input-flow .entry").html($.number(Math.ceil(data.bytes_received), 0));
                $(".input-flow .entry-percentage").html(rCapacity.toString() + "%");
                $(".input-flow .progress-bar").prop("aria-valuenow", rCapacity);
                $(".input-flow .progress-bar").css("width", rCapacity.toString() + "%");
                $(".input-flow .sr-only").html(rCapacity.toString() + "%");
                // Network Load - Output
                var rCapacity = Math.ceil((Math.ceil(data.bytes_sent) / data.network_guaranteed_speed) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".output-flow .entry").html($.number(Math.ceil(data.bytes_sent), 0));
                $(".output-flow .entry-percentage").html(rCapacity.toString() + "%");
                $(".output-flow .progress-bar").prop("aria-valuenow", rCapacity);
                $(".output-flow .progress-bar").css("width", rCapacity.toString() + "%");
                $(".output-flow .sr-only").html(rCapacity.toString() + "%");
                // Active Streams
                var rCapacity = Math.ceil((data.total_running_streams / (data.offline_streams + data.total_running_streams)) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".active-streams .entry").html($.number(data.total_running_streams, 0));
                $(".active-streams .entry-percentage").html($.number(data.offline_streams, 0));
                $(".active-streams .progress-bar").prop("aria-valuenow", rCapacity);
                $(".active-streams .progress-bar").css("width", rCapacity.toString() + "%");
                $(".active-streams .sr-only").html(rCapacity.toString() + "%");
				$(".offline-streams .entry").html($.number(data.offline_streams, 0));
                // CPU Usage
                $(".cpu-usage .entry").html(data.cpu);
                $(".cpu-usage .entry-percentage").html(data.cpu.toString() + "%");
                $(".cpu-usage .progress-bar").prop("aria-valuenow", data.cpu);
                $(".cpu-usage .progress-bar").css("width", data.cpu.toString() + "%");
                $(".cpu-usage .sr-only").html(data.cpu.toString() + "%");
                // Memory Usage
                $(".mem-usage .entry").html(data.mem);
                $(".mem-usage .entry-percentage").html(data.mem.toString() + "%");
                $(".mem-usage .progress-bar").prop("aria-valuenow", data.mem);
                $(".mem-usage .progress-bar").css("width", data.mem.toString() + "%");
                $(".mem-usage .sr-only").html(data.mem.toString() + "%");
                // Uptime
				if (data.uptime) {
					$(".uptime .entry").html(data.uptime.split(" ").slice(0,2).join(" "));
				}
				// Per Server
				$(data.servers).each(function(i) {
					$("#s_" + data.servers[i].server_id + "_conns").html($.number(data.servers[i].open_connections, 0));
					$("#s_" + data.servers[i].server_id + "_users").html($.number(data.servers[i].online_users, 0));
					$("#s_" + data.servers[i].server_id + "_online").html($.number(data.servers[i].total_running_streams, 0));
					$("#s_" + data.servers[i].server_id + "_input").html($.number(Math.ceil(data.servers[i].bytes_received), 0) + " Mbps");
					$("#s_" + data.servers[i].server_id + "_output").html($.number(Math.ceil(data.servers[i].bytes_sent), 0) + " Mbps");
					$("#s_" + data.servers[i].server_id + "_cpu").val(data.servers[i].cpu).trigger('change');
					$("#s_" + data.servers[i].server_id + "_mem").val(data.servers[i].mem).trigger('change');
					if (data.servers[i].uptime) {
						$("#s_" + data.servers[i].server_id + "_uptime").html(data.servers[i].uptime.split(" ").slice(0,2).join(" "));
					}
				});
                if (Date.now() - rStart < 1000) {
                    setTimeout(getStats, 1000 - (Date.now() - rStart));
                } else {
                    getStats();
                }
            }).fail(function() {
                setTimeout(getStats, 1000);
            });
        }
        
        $('.dashboard-tabs .nav-link').on('click', function (e) {
            window.rServerID = $(e.target).data("id");
            getStats();
            $(".nav-link").each(function() {
                $(this).removeClass("active");
            });
            $(e.target).addClass("active");
            if (window.rServerID == "home") {
                if (!$("#server-home").is(":visible")) {
                    $("#server-tab").hide();
                    $("#server-home").show();
                }
            } else {
                if (!$("#server-tab").is(":visible")) {
                    $("#server-home").hide();
                    $("#server-tab").show();
                }
            }
        });
        
        $(document).ready(function() {
            getStats();
        });
        </script>
    </body>
</html>