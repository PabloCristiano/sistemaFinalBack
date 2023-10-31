<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoContasPagar;
use Exception;

class ControllerContasPagar extends Controller
{
    protected $daoContasPagar;

    public function __construct()
    {
        $this->daoContasPagar = new DaoContasPagar();
    }

    public function index()
    {
        $contasPagar = $this->daoContasPagar->all(true);
        return response()->json($contasPagar, 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        try {
            $store = $this->daoContasPagar->store($request->all());
            if ($store === true) {
                return response()->json(['success' => 'Conta Baixada com Sucesso', $store], 200);
            } else {
                return response::json(['error' => 'Conta não Baixada',  $store], 200);
            }
        } catch (Exception $e) {
            // Lidar com outras exceções se necessário
            return response()->json(['error' => 'Something went wrong'], 500);
        }
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
}
