<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoServico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
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

    public function storeServicoProfissional($obj, $id)
    {
        try {
            DB::beginTransaction();
            foreach ($obj as $key => $servico_obj) {
                $servico = $this->daoServico->findById($servico_obj['id'], false);
                $valor =  floatval($servico[0]->valor);
                $sql = DB::INSERT(
                    "INSERT INTO servico_profissional (id_profissional,id,servico,tempo,valor) 
               VALUES (?, ?, ?, ?, ?)",
                    [
                        $id,
                        $servico[0]->id,
                        $servico[0]->servico,
                        $servico[0]->tempo,
                        $valor,
                    ]
                );
            }
            if ($sql) {
                DB::commit();
                return true;
            } else {
                return false;
            }
        } catch (QueryException $e) {
            $mensagem = $e->getMessage(); // Mensagem de erro
            $codigo = $e->getCode(); // Código do erro
            $consulta = $e->getSql(); // Consulta SQL que causou o erro
            $bindings = $e->getBindings(); // Valores passados como bind para a consulta
            DB::rollBack();
            return [$mensagem, $codigo, $consulta, $bindings];
        }
    }

    public function update(Request $request, $id)
    {
    }

    public function updateServicoProfissional($obj, $id)
    {
    }

    public function delete($id)
    {
        $id = intval($id);
        try {
            DB::beginTransaction();
            $sql = DB::Select("DELETE FROM  servico_profissional where id_profissional = '$id'");
            DB::commit();
            return true;
        } catch (QueryException $e) {
            $mensagem = $e->getMessage(); // Mensagem de erro
            $codigo = $e->getCode(); // Código do erro
            $consulta = $e->getSql(); // Consulta SQL que causou o erro
            $bindings = $e->getBindings(); // Valores passados como bind para a consulta
            DB::rollBack();
            return [$mensagem, $codigo, $consulta, $bindings];
        }
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

    public function gerarServicoProfissional(array $dados)
    {
    }
}
