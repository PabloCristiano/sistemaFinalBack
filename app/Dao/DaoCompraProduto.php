<?php

namespace App\Dao;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\CompraProduto;
use App\Dao\DaoFornecedor;

class DaoCompraProduto
{
    public function create(array $dados)
    {
    }

    public function findById($compra_modelo, $compra_numero_nota, $compra_serie, bool $model = false)
    {
        if (!$model) {
            $dados = DB::select(
                'SELECT * FROM compra_produto WHERE compra_modelo = ? AND compra_numero_nota = ? AND compra_serie = ?',
                [trim($compra_modelo), trim($compra_numero_nota), trim($compra_serie)]
            );
            dd($dados[0]);
            return $dados[0];
        }

        $dados = DB::select(
            'SELECT * FROM compra_produto WHERE compra_modelo = ? AND compra_numero_nota = ? AND compra_serie = ?',
            [trim($compra_modelo), trim($compra_numero_nota), trim($compra_serie)]
        );

        dd($dados);

        // if ($dados) {
        //     $clientes = [];
        //     foreach ($dados as $item) {
        //         $cliente = $this->create(get_object_vars($item));
        //         $cliente_json = $this->getData($cliente);
        //         array_push($clientes, $cliente_json);
        //     }

        //     return $clientes;
        // }
    }
}
