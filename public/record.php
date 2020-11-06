<?php

require '../vendor/autoload.php';

use XBase\Table;

try {
    // Ruta del archivo .dbf
    $table = new Table('C:/intensis/dbtrans/TRANS.DBF', null, 'cp1251');
    $columns = $table->getRecordCount() . PHP_EOL;
    // Recorriendo el archivo del ultimo al primero
    for ($i = (int)$columns - 1; $i >= 0; $i--) {
        $record = $table->pickRecord($i);
        if ($record->get('bomba') == $_GET['bomb_id']) {
            $sale['id_gasoline'] = $record->get('prod');
            $sale['liters'] = $record->get('cant');
            $sale['price'] = $record->get('importe');
            $sale['sale'] = $record->get('id_venta');
            // devolviendo los datos de la venta
            echo json_encode($sale);
            break;
        }
    }
} catch (Exception $e) {
}
