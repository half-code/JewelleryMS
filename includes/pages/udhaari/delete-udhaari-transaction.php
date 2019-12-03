<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12/1/2019
 * Time: 10:49 PM
 */
require_once ('db/models/Udhaari.class.php');
require_once ('db/models/UdhaariTransaction.class.php');
require_once ('helpers/redirect-helper.php');
require_once('helpers/redirect-constants.php');
if(isset($_GET['id'])) {
    try {
        $udhaari_transaction_id = $_GET['id'];
        CRUD::setAutoCommitOn(false);
        $udhaari_transaction_row = UdhaariTransaction::findNoDeletedColumn("udhaari_transaction_id = ?", $udhaari_transaction_id);
        $udhaari_id = $udhaari_transaction_row->udhaari_id;
        $udhaari_amount = $udhaari_transaction_row->udhaari_amount;

        /*******UPDATING THE ROW OF UDHAARI*****/

        $udhaari = Udhaari::find("udhaari_id = ?", $udhaari_id);

        //udhaari_amount of udhaari table - udhaari_amount of udhaari_transactions table
        $difference = $udhaari->udhaari_amount - $udhaari_amount;

        //total payment made calcualted from udhaari_amount of udhaar table - pending_amount of udhaari_table
        $paymentMade = $udhaari->udhaari_amount - $udhaari->pending_amount;

        if($paymentMade <= $difference){

            //DELETING THE ROW FROM UDHAARI TRANSACTION
            $udhaari_transaction_row->delete();
        }else{
            CRUD::rollback();
            setStatusAndMsg("error", "Udhaari transaction could not be deleted.");
        }

        /*******UPDATING THE ROW OF UDHAARI*****/


        //DELETING THE ROW FROM UDHAARI TRANSACTION
        $udhaari_transaction_row->delete();





//        if ($udhaari_id) {
//            if(!UdhaariTransaction::isUsed($udhaari->udhaari_id)) {
//                if ($udhaari->delete()) {
//                    CRUD::commit();
//                    setStatusAndMsg("success", "Udhaari deleted successfully");
//                    redirect_to(VIEW_ALL_UDHAARIS);
//                } else {
//                    CRUD::rollback();
//                    setStatusAndMsg("error", "Udhaari could be deleted.");
//                }
//            }else{
//                setStatusAndMsg("error", "Payments exists for this udhaari. Please delete the payments first.");
//            }
//        } else {
//            setStatusAndMsg("error", "Udhaari do not exists");
//        }
    } catch (Exception $ex) {
        setStatusAndMsg("error", "Something went wrong");
    }

    CRUD::setAutoCommitOn(true);
}