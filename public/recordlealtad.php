<?php

require '../vendor/autoload.php';

use XBase\Table;

 if($_SERVER['REQUEST_METHOD']=='GET'){
	    $datos = json_decode(file_get_contents("php://input"),true);
	    $array = json_decode($datos, true);
        $element = array();

        date_default_timezone_set("America/Mexico_City");
        $flecha = date('Y-m-d'); 
        //fecha servidor
        $date = strtotime($flecha);
        $fechaserv = date("Ymd", strtotime("-1 day", $date));
                
        foreach ($array as $value) {
 
           $table = new Table('C:/intesis/bdtrans/trans.dbf', ['id', 'id_venta', 'prod', 'cant', 'precio', 'importe', 'fecha', 'hora', 'status']);
           $columns = $table->getRecordCount() . PHP_EOL;
            $i = (int)$columns - 1;
              
                for ($i = (int)$columns - 1; $i >= 0; $i--) {
                    $row = $table->pickRecord($i);
                     if ($fechaserv <= $row->get('fecha')) {
                       if ($row->get('id') == $value['number_valor'] && $row->get('id_venta') == $value['number_ticket']) {
                          
                   		 $newdata =  array (
                                  'valor' => $row->get('id'),
                                  'folio' => $row->get('id_venta'),
                                  'producto' => $row->get('prod'),
                                  'litro' => $row->get('cant'),
                                  'precio' => $row->get('precio'),
                                  'costo' => $row->get('importe'),
                                  'fecha' => $row->get('fecha'),
                                  'hora' => $row->get('hora'),
                                  'estatus' => $row->get('status'),
                                  'mensaje' => "correcto"
                                );
                    		$element[] = $newdata;
                    		break;
                    	}
                     }
                     else{
                                $newdata =  array (
                                  'valor' => $value['number_valor'],
                                  'folio' => $value['number_ticket'],
                                  'producto' => "",
                                  'litro' => "",
                                  'precio' => "",
                                  'costo' => "",
                                  'fecha' => "",
                                  'hora' => "",
                                  'estatus' => "",
                                  'mensaje' => "incorrecto"
                                );
                    		$element[] = $newdata;
                      break;
                     }
                }
           }
          echo $result = json_encode($element);
}
