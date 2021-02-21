<?php
/**
 * Created by PhpStorm.
 * User: Tarun
 * Date: 21-03-201 9
 * Time: 04:18 PM
 */
require_once 'db/models/Table.class.php';

class Invoice extends Table
{
    public static $table_name = "invoices";
    public static function select($rows="*", $deleted=0, $condition = 1, ...$params)
    {
        return CRUD::select(self::$table_name, $rows, $deleted, $condition, ...$params);
    }
    public static function find($condition, ...$params)
    {
        return CRUD::find(self::$table_name, $condition, ...$params);
    }
    public function __construct($result = null)
    {
        parent::__construct($result);
    }

    public static function viewProductDetails($invoice_id)
    {
        return CRUD::query("SELECT gst2.gst_id, invoice_product.product_id, gst2.wef, invoices.invoice_date, categories.category_name, gst2.gst_rate, gst.hsn_code, products.product_name, products.product_label, invoice_product.product_rate, products.product_quantity, invoice_product.making_charges FROM invoices INNER JOIN invoice_product ON invoices.invoice_id = ? AND invoices.invoice_id = invoice_product.invoice_id INNER JOIN products ON products.product_id = invoice_product.product_id INNER JOIN categories ON products.category_id = categories.category_id INNER JOIN gst ON categories.gst_id = gst.gst_id INNER JOIN gst as gst2 ON gst.hsn_code = gst2.hsn_code AND gst2.wef <= invoices.invoice_date AND gst2.wef IN (SELECT MAX(gst2.wef) as wef FROM invoices INNER JOIN invoice_product ON invoices.invoice_id = ? AND invoices.invoice_id = invoice_product.invoice_id INNER JOIN products ON invoice_product.product_id = products.product_id INNER JOIN categories ON categories.category_id = products.category_id INNER JOIN gst ON gst.gst_id = categories.gst_id INNER JOIN gst as gst2 ON gst2.hsn_code = gst.hsn_code AND gst2.wef <= invoices.invoice_date GROUP BY products.product_id)", $invoice_id, $invoice_id);
    }
    public static function viewPaymentDetails($invoice_id)
    {
        return CRUD::query("SELECT * FROM payments JOIN invoices ON invoices.invoice_id=payments.invoice_id WHERE invoices.invoice_id=? AND invoices.deleted=0 AND payments.deleted=0", $invoice_id);
    }

    public function insert()
    {
        if(!$this->exists())
        {
            parent::addCreated();
            return CRUD::insert(self::$table_name, $this->columns_values);
        }
    }

    public function update()
    {
        parent::addUpdated();
        return CRUD::update(self::$table_name, $this->columns_values, "invoice_id={$this->invoice_id}");
    }

    public function delete()
    {
        $this->addDeleted();
        $this->deleted = 1;
        return CRUD::update(self::$table_name, $this->columns_values, "invoice_id={$this->invoice_id}");
    }
    public static function viewAll(){
        return $rs = CRUD::query("SELECT @sr_no:=@sr_no+1 as serial_no, invoices.*,customers.customer_name,customers.customer_contact, GROUP_CONCAT(CONCAT(products.product_name, '-',products.product_label) SEPARATOR ',') AS product_name FROM invoices JOIN customers ON invoices.customer_id=customers.customer_id INNER JOIN (SELECT @sr_no:=0) AS a INNER JOIN invoice_product ON invoices.invoice_id = invoice_product.invoice_id INNER JOIN products on invoice_product.product_id = products.product_id  WHERE invoices.deleted = 0 GROUP BY invoices.invoice_id");
    }


    public function exists()
    {
//        $result = CRUD::query("SELECT * FROM (SELECT * FROM products WHERE category_id = ? AND deleted = 0) AS CATEGORY_PRODUCT WHERE product_name = ? AND deleted = 0",$this->category_id, $this->product_name);
        $result = self::select("*",0,"invoice_no = ?", $this->invoice_no);
        if($result->rowCount() >= 1)
            return true;
        return false;
    }
}