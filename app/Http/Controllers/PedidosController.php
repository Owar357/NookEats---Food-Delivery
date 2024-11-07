<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidosController extends Controller
{
    public function RealizarPedido(Request $request,$id)
    {
      
       DB::beginTransaction();
        
       try {
        
      $pedido =   new Pedido();

      $pedido -> id  = $request -> id;
      $pedido -> fecha_hora_pedido = $request -> fecha_hora_pedido;
      
      
      //$pedido -> total  = $request -> total;
      
      $pedido -> estado_pedido = $request -> estado_pedido;
      $pedido -> metodo_pago  = $request -> metodo_pago;
      $pedido -> usuario_id  = $request -> $id;
 




       } catch (\Throwable $th) {
        //throw $th;
       }
    }
}
