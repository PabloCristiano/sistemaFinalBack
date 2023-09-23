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
use App\Models\ProfissionalServicoAgenda;
use Illuminate\Support\Facades\Response;

class DaoProfissionalServicoAgenda implements Dao
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
        dd($dados);
    }

    public function store($obj)
    {
        try {
            DB::beginTransaction();
            foreach ($obj as $item) {
                $data = date_create_from_format('d/m/Y', $item['data'])->format('Y-m-d');
                $horarioInicio = $item['horario_inicio'];
                $idProfissional = intval($item['id_profissional']);
                $status = 'LIVRE';
                DB::insert("INSERT INTO profissionais_servicos_agenda (id_profissional,data,horario_inicio,status) VALUES ($idProfissional,'$data','$horarioInicio','$status')");
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            return $error;
            //return $th;
        }

        dd($obj);
    }

    public function storeProfissionalServico($obj, $id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function updateProfissionalServico($obj, $id)
    {
    }

    public function delete($id)
    {
    }

    public function findById(int $id, bool $model = false)
    {
    }

    // public function getData(ProfissionalServicoAgenda $profissional_servico)
    // {

    // }

    public function gerarProfissionalServico(array $dados)
    {
    }

    public function findAgendaProfissional(int $id, string $data)
    {
        $dados = DB::select('select * from profissionais_servicos_agenda where id_profissional = ? and data = ?', [$id, $data]);
        return $dados;
    }
}
