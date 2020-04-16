<?php
include "session.php"; include "functions.php";
if ((!$rPermissions["is_admin"]) OR (!hasPermissions("adv", "epg"))) { exit; }

$rEPGs = getEPGs();
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
                                    <li>
                                        <button type="button" class="btn btn-dark waves-effect waves-light btn-sm" onClick="forceUpdate();" id="force_update">
                                            <i class="mdi mdi-refresh"></i> Force EPG Reload
                                        </button>
										<?php if (hasPermissions("adv", "add_epg")) { ?>
                                        <a href="epg.php">
                                            <button type="button" class="btn btn-success waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-plus"></i> Add EPG
                                            </button>
                                        </a>
										<?php } ?>
                                    </li>
                                </ol>
                            </div>
                            <h4 class="page-title">EPG's</h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" style="overflow-x:auto;">
                                <table id="datatable" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th>EPG Name</th>
                                            <th>Source</th>
                                            <th class="text-center">Days to Keep</th>
                                            <th class="text-center">Last Updated</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rEPGs as $rEPG) {
                                        ?>
                                        <tr id="server-<?=$rEPG["id"]?>">
                                            <td class="text-center"><?=$rEPG["id"]?></td>
                                            <td><?=$rEPG["epg_name"]?></td>
                                            <td><?=parse_url($rEPG["epg_file"])['host']?></td>
                                            <td class="text-center"><?=$rEPG["days_keep"]?></td>
                                            <td class="text-center"><?php if ($rEPG["last_updated"]) { echo date("Y-m-d H:i:s", $rEPG["last_updated"]); } else { echo "Never"; } ?></td>
                                            <td class="text-center">
												<?php if (hasPermissions("adv", "epg_edit")) { ?>
                                                <a href="./epg.php?id=<?=$rEPG["id"]?>"><button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit EPG" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>
                                                <button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete EPG" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api(<?=$rEPG["id"]?>, 'delete');"><i class="mdi mdi-close"></i></button>
												<?php } else { echo "--"; } ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div> <!-- end card body-->
                        </div> <!-- end card -->
                    </div><!-- end col-->
                </div>
                <!-- end row-->
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

        <script>
        function api(rID, rType) {
            if (rType == "delete") {
                if (confirm('Are you sure you want to delete this EPG? This cannot be undone!') == false) {
                    return;
                }
            }
            $.getJSON("./api.php?action=epg&sub=" + rType + "&epg_id=" + rID, function(data) {
                if (data.result === true) {
                    if (rType == "delete") {
                        $("#server-" + rID).remove();
                        $.toast("Server successfully deleted.");
                    }
                    $.each($('.tooltip'), function (index, element) {
                        $(this).remove();
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                } else {
                    $.toast("An error occured while processing your request.");
                }
            });
        }
        
        function forceUpdate() {
			$("#force_update").attr("disabled", true);
            $.toast("Updating EPG in the background...");
            $.getJSON("./api.php?action=force_epg", function(data) {
                $.toast("EPG update complete!");
				$("#force_update").attr("disabled", false);
            });
        }
        
        $(document).ready(function() {
            $("#datatable").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                responsive: false
            });
            $("#datatable").css("width", "100%");
        });
        </script>

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
    </body>
</html>