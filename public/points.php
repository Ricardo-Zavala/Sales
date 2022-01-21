<?php
require '../vendor/autoload.php';

use XBase\Table;

class points
{
    function points()
    {
        try {
            $saleqr = (int) $_GET['sale'];
            $code = $_GET['code'];
            date_default_timezone_set("America/Mexico_City");
            // Variables fecha y apertura del archivo
            $start = date('Y/m/d H:i:s');
            $archivo = fopen("bitacora.txt", "a");
            $data = [];

            // abriendo el archivo trans.dbf
            $table = new Table(
                'C:/intensis/dbtrans/trans.dbf',
                [
                    'encoding' => 'cp1251',
                    'columns' => ['id_venta', 'id', 'prod', 'cant', 'importe', 'bomba', 'fecha', 'hora', 'status']
                ]
            );
            if ($archivo)
                fwrite($archivo, "Inicio: {$start}. Buscando venta: {$saleqr} ");

            // Numero de columnas, primer y ultimo elemento
            $columns = $table->getRecordCount() . PHP_EOL;
            $firstSale = $table->pickRecord(0);
            $lastSale = $table->pickRecord((int)$columns - 1);
            // Validacion de la venta a buscar sobre el rango de ventas
            if ($lastSale->id_venta >= $saleqr && $firstSale->id_venta <= $saleqr) {
                // Buscando la venta
                $diff = $lastSale->id_venta - $saleqr;
                $posix = $lastSale->getRecordIndex() - $diff;
                $sale = $table->pickRecord((int)$posix);
                // Validando la venta y el alfanumerico
                if ($sale->id_venta == $saleqr) {
                    if ($sale->id == $code) {
                        // Venta correcta
                        $data['sale'] = $sale->id_venta;
                        $data['code'] = $sale->id;
                        $data['gasoline_id'] = $sale->prod;
                        $data['liters'] = $sale->cant;
                        $data['payment'] = $sale->importe;
                        $data['no_bomb'] = $sale->bomba;
                        $data['date'] = $sale->fecha;
                        $data['hour'] = $sale->hora;
                        $data['status'] = $sale->status;
                        $data['validation'] = 200;
                        $status = 'Correcto.';
                    } else {
                        // El alfanumerico es incorrecto
                        $data['validation'] = 2;
                        $status = 'Alfanumerico incorrecto.';
                    }
                } else {
                    // Intente más tarde
                    $data['validation'] = 3;
                    $status = 'Error de busqueda.';
                }
            } else {
                // La venta no existe
                $data['validation'] = 404;
                $status = 'no encontrado.';
            }
        } catch (\Throwable $th) {
            $data['validation'] = 3;
            $status = 'Error de busqueda.';
        } finally {
            $end = date('Y/m/d H:i:s');
            if ($archivo)
                fwrite($archivo, "Finalizado: {$end}. Estado: {$status}\r");
            fclose($archivo);
            $table->close();
            return $data;
        }
    }
}

ini_set('error_reporting', E_ALL ^ E_WARNING);
ini_set('error_reporting', E_ALL ^ E_NOTICE);
$point = new points();
$response = $point->points();
echo json_encode($response);
