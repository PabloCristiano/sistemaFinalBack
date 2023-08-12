<?php

namespace App\Dao;

use App\Dao\Dao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Compra;
class DaoCompra implements Dao
{

 

    public function __construct()
    {
        
    }

    public function all(bool $json = false)
    {
    }

    public function create(array $dados)
    {
       
    }

    public function store($compra)
    {
       
            
    }

    public function update(Request $request, $id)
    {
      
    }

    public function delete($id)
    {
       
    }

    public function findById(int $id, bool $model = false)
    {
       
    }

    public function getData(Compra $compra)
    {
        
    }

    public function getProductsData(Compra $compra)
    {
        
    }

    public function getDuplicatesData(Compra $compra)
    {
     
    }
}
