<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoCondicaoPagamento;

class ControllerCondicaoPagamento extends Controller
{
    private  $daoCondicaoPagamento;

    public function __construct()
    {
        $this->daoCondicaoPagamento = new DaoCondicaoPagamento();
    }
    public function index()
    {

        $condicaoPagamento = $this->daoCondicaoPagamento->all(true);
        return response()->json($condicaoPagamento, 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $condicaoPagamento = $this->daoCondicaoPagamento->findById($id, true);
            if ($condicaoPagamento) {
                return response()->json($condicaoPagamento, 200);
            }
        }
        return response()->json(['error' => 'Condição de Pagamento não Cadastrado...'], 400);
    }
}
