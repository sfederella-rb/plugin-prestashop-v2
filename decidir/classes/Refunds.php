<?php
require_once(dirname(__FILE__).'../../../../config/config.inc.php');

class Refunds extends ModuleFrontController
{
    public function totalRefund($id, $amount, $info, $connector){
        try{    
            $data = array();

            $response = $connector->payment()->Refund($data, $id);

            if($response->getStatus() == "approved"){

                $amount = ($amount/100); //formated amount

                $loggerData = array();
                $loggerData['id'] = $response->getId();
                $loggerData['amount'] = $amount;
                $loggerData['status'] = $response->getStatus();

                $this->module->log->info('respuesta de devolucion parcial - '. json_encode($loggerData));

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
            $rta = "Devolución Rechazada";
        }

        return $rta;
    }

    public function partialRefund($id, $info, $connector){
        $format_amount = str_replace(',', '.', $info['amount']);
        $format_amount = floatval(number_format($format_amount, 2, '.', ''));

        $data = array(
                "amount" => $format_amount
            );

        try{    
            $response = $connector->payment()->partialRefund($data, $id);

            if($response->getStatus() == "approved"){

                $loggerData = array();
                $loggerData['id'] = $response->getId();
                $loggerData['amount'] = $response->getAmount();
                $loggerData['status'] = $response->getStatus();

                $this->module->log->info('respuesta de devolucion parcial - '. json_encode($loggerData));

                $order = new Order($info['order']);
                $order_detail_list = array();
                $voucher = array();
                $choosen = array();
                $full_quantity_list = array();
    
                OrderSlip::create($order, $order_detail_list, $format_amount, $voucher, $choosen, false);

                Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $order_detail_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);

                $rta = "Devolución aprobada";

            }elseif($response->getStatus() == "KO"){

                $rta = "No se pudo realizarse la devolución, ";
            }
         
        }catch(exception $e){
            $rta = "Devolución Rechazada";
        }

        return $rta;
    }
}