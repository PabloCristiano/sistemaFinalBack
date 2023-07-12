<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoParcela;
use App\Models\CondicaoPagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaoCondicaoPagamento implements Dao
{   
    private DaoParcela $daoParcela;
    
    public function __construct()
    {
        $this->daoParcela = new DaoParcela();
    }
    public function all(bool $model = false){

        return 'Dentro da Daooooo';
    }

    public function create(array $dados){}

    public function store($obj){}

    public function update(Request $request, $id){}

    public function delete($id){}

    public function findById(int $id, bool $model = false){}
}
