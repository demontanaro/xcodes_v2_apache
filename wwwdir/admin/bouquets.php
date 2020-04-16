<?php
include "session.php"; include "functions.php";
if ((!$rPermissions["is_admin"]) OR (!hasPermissions("adv", "bouquets"))) { exit; }
$rBouquets = getBouquets();

if ($rSettings["sidebar"]) {
    include "header_sidebar.php";
} else {
    include "header.php";
}
        if ($rSettings["sidebar"]) { ?>
        <div class="content-page"><div class="content boxed-layout"><div class="container-fluid">
        <?php } else { ?>
        <div class="wrapper boxed-layout"><div class="container-fluid">
        <?php } ?>
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
							<?php if (hasPermissions("adv", "add_bouquet")) { ?>
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li>
                                        <a href="bouquet.php">
                                            <button type="button" class="btn btn-success waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-plus"></i> Add Bouquet
                                            </button>
                                        </a>
                                    </li>
                                </ol>
                            </div>
							<?php } ?>
                            <h4 class="page-title">Bouquets</h4>
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
                                            <th>Bouquet Name</th>
                                            <th class="text-center">Streams</th>
                                            <th class="text-center">Series</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rBouquets as $rBouquet) { ?>
                                        <tr id="bouquet-<?=$rBouquet["id"]?>">
                                            <td class="text-center"><?=$rBouquet["id"]?></td>
                                            <td><?=$rBouquet["bouquet_name"]?></td>
                                            <td class="text-center"><?=count(json_decode($rBouquet["bouquet_channels"], True))?></td>
                                            <td class="text-center"><?=count(json_decode($rBouquet["bouquet_series"], True))?></td>
                                            <td class="text-center">
												<?php if (hasPermissions("adv", "edit_bouquet")) { ?>
                                                <a href="./bouquet_order.php?id=<?=$rBouquet["id"]?>"><button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reorder Bouquet" class="btn btn-outline-primary waves-effect waves-light btn-xs"><i class="mdi mdi-format-line-spacing"></i></button></a>
                                                <a href="./bouquet.php?id=<?=$rBouquet["id"]?>"><button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Bouquet" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>
                                                <button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Bouquet" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api(<?=$rBouquet["id"]?>, 'delete');""><i class="mdi mdi-close"></i></button>
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
                if (confirm('Are you sure you want to delete this bouquet? This cannot be undone!') == false) {
                    return;
                }
            }
            $.getJSON("./api.php?action=bouquet&sub=" + rType + "&bouquet_id=" + rID, function(data) {
                if (data.result === true) {
                    if (rType == "delete") {
                        $("#bouquet-" + rID).remove();
                        $.toast("Bouquet successfully deleted.");
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