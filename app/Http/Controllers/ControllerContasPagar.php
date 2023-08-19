<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoContasPagar;

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
