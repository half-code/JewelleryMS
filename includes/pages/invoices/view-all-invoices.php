<?php
$model_name = "Invoice";
require_once "db/models/{$model_name}.class.php";
$rs = Invoice::viewAll();
//This array will store the table headers for the columns we are selecting from database
$column_names_as = array(
    "invoice_no" => "Inv No",
    "product_name"=>"Product Name",
    "created_at" => "Sell Date",
    "customer_name" => "Customer Name",
    "customer_contact" => "Customer Contact",
);
require_once 'includes/pages/invoices/delete-invoice.php';
?>
    <div class="row">
        <div class="offset-1 col-md-10">
            <div class="table-responsive">
                <table class="tables table table-bordered">
                    <thead>
                    <tr>
                        <?php
                        foreach ($column_names_as as $column_name_as) {
                            echo "<th>{$column_name_as}</th>";
                        }
                        ?>
                        <th>Detail</th>
                        <th>View</th>
                        <th>Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $column_names = array_keys($column_names_as);
                    while($row = $rs->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        foreach ($column_names as $column_name) {
                            if(empty($row[$column_name])) {
                                echo "<td>NULL</td>";
                            }
                            else {
                                echo "<td>$row[$column_name]</td>";
                            }
                        }
                        echo "<td><a class='btn btn-info text-white' data-toggle='tooltip' href='invoices.php?src=view-invoice-details&id={$row['invoice_id']}' data-html='true' title='View Detail'><i class='fa fa-info'></i></a></td>";
                        echo "<td><a class='btn btn-success text-white' data-toggle='tooltip' target='_blank' href='invoices.php?src=view-invoice&id={$row['invoice_id']}' data-html='true' title='View Invoice'><i class='fa fa-file-invoice'></i></a></td>";
                        echo "<td><a class='btn btn-danger text-white delete' data-toggle='modal' data-target='#deleteModal' data-html='true' title='Delete' data-delete='invoices.php?src=delete-invoice&id={$row['invoice_id']}'><i class='fa fa-trash'></i></a></td>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
include_once 'includes/modal.php';
$body = "<h4>Note: </h4> Please check if all the usages of this particular entry has been deleted or else entry will not be deleted.";
createModal(DELETE_TITLE, $body, "Delete");
?>