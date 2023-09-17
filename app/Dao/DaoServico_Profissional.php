<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoServico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;
use App\Models\Servico;
use App\Models\Servico_Profissional;

class DaoServico_Profissional implements Dao
{
    private $daoServico;

    public function __construct()
    {
        $this->daoServico = new DaoServico();
    }
    public function all(bool $model = false)
    {
    }

    public function create(array $dados)
    {
    }

    public function store($obj)
    {
    }

    public function storeProfissionalServico($obj, $id)
    {
       // dd($obj,$id);
    }

    public function update(Request $request, $id)
    {
    }

    public function updateProfissionalServico($obj, $id)
    {
    }

    public function delete($id)
    {
        dd($id);
    }

    public function findById(int $id, bool $model = false)
    {

        if (!$model) {
            $dados = DB::select('select * from servico_profissional where id_profissional = ?', [$id]);
            return $dados;
        }
        //$dados = DB::select('select * from profissionais where id = ?', [$id]);
        // if ($dados) {
        //     $profissionais = [];
        //     foreach ($dados as $item) {
        //         $profissional = $this->create(get_object_vars($item));
        //         $profissional_json = $this->getData($profissional);
        //         array_push($profissionais, $profissional_json);
        //     }
        //     return $profissionais;
        // }
    }

    public function getData(Servico_Profissional $profissional_servico)
    {
    }

    public function gerarProfissionalServico(array $dados)
    {
    }
}
