<?php
/**
 * Created by PhpStorm.
 * User: Tarun
 * Date: 21-03-201 9
 * Time: 04:18 PM
 */
require_once 'Table.class.php';

class GST extends Table
{
    public static $table_name = "gst";
    public static function select($rows="*", $deleted=0, $condition = 1, ...$params)
    {
        return CRUD::select(self::$table_name, $rows, $deleted, $condition, ...$params);
    }

    /**
     * Returns the unique result set which includes all the hsn codes with the latest rates.
     * @return mixed
     */
    public static function viewAll()
    {
        return CRUD::query("SELECT * FROM gst INNER JOIN (SELECT MAX(wef) as wef, hsn_code from gst GROUP BY hsn_code) as g1 WHERE gst.hsn_code = g1.hsn_code AND gst.wef = g1.wef");
    }
    public static function find($condition, ...$params)
    {
        return CRUD::find(self::$table_name, $condition, ...$params);
    }
    public function __construct($result = null)
    {
        parent::__construct($result);
    }

    public function insert()
    {
        if(!$this->exists()){
            parent::addCreated();
            return CRUD::insert(self::$table_name, $this->columns_values);
        }
        return false;
    }

    public function update()
    {
        return CRUD::update(self::$table_name, $this->columns_values, "hsn_code={$this->hsn_code}");
    }

    public function delete()
    {
        return CRUD::delete(self::$table_name, "hsn_code={$this->hsn_code}");
    }

    public function exists()
    {
//        $result = CRUD::query("SELECT * FROM gst WHERE hsn_code = ?",$this->hsn_code);
        $result = self::select("*", 0, "hsn_code = ?", $this->hsn_code);
        if($result->rowCount() >= 1)
            return true;
        return false;
    }

    /**
     * Retrieves that the current selected entry of the gst table is with the latest date of wef column.
     * @return bool
     */
    public function isLatest(){
        $result = CRUD::query("SELECT * FROM gst WHERE hsn_code = ? AND deleted = 0 ORDER BY wef DESC", $this->hsn_code);
        if($result){
            $latest = $result->fetch();
            if($latest->gst_id == $this->gst_id)
                return true;
        }
        return false;
    }
}