<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoCompra;

class ControllerCompra extends Controller
{
    private $daoCompra;

    public function __construct()
    {
        $this->daoCompra = new DaoCompra();
    }
    public function index()
    {
        $compras = $this->daoCompra->all(true);
        return response()->json($compras, 200);
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
}
