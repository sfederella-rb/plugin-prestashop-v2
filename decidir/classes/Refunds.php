<?php
require_once(dirname(__FILE__).'../../../../config/config.inc.php');

class Refunds{
    public function totalRefund($id, $amount, $info, $connector){
        try{
            $order = new Order($info['order']);
            $data = array();
            $amount = ($amount/100); //formated amount

            $response = $connector->payment()->Refund($data, $id);

            if($response->getStatus() == "approved"){

                $order = new Order($info['order']);
                $order_detail_list = array();
                $voucher = array();
                $choosen = array();
                $full_quantity_list = array();
    
                OrderSlip::create($order, $order_detail_list, $amount, $voucher, $choosen, false);

                Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $order_detail_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);

                $rta = "Devolución aprobada";

            }elseif($response->getStatus() == "KO"){

                $rta = "No pudo realizarse la devolución, ";

            }
            
        }catch(exception $e){
            $rta = "rejected";
        }

        return $rta;
    }

    public function partialRefund($id, $info, $connector){
        try{
            $order = new Order($info['order']);
            $format_amount = number_format((float)$info['amount'], 2, '.', '');

            $data = array(
                "amount" => $format_amount
            );

            $response = $connector->payment()->partialRefund($data, $id);
            
            if($response->getStatus() == "approved"){

                $order = new Order($info['order']);
                $order_detail_list = array();
                $voucher = array();
                $choosen = array();
                $full_quantity_list = array();
    
                OrderSlip::create($order, $order_detail_list, $info['amount'], $voucher, $choosen, false);

                Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $order_detail_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);

                $rta = "Devolución parcial aprobada";

            }elseif($response->getStatus() == "KO"){

                $rta = "No pudo realizarse la devolución, ";

            }
            
        }catch(exception $e){
            $rta = "rejected";
        }

        return $rta;
    }
}
    
?>
