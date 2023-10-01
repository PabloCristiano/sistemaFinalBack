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
                $minutos_mili = $this->minutosParaMilissegundos(intval($item['intervalo']));
                $hora_mili   = $this->tempoParaMilissegundos($horarioInicio);
                $horarioFim = $this->milissegundosParaTempo(($minutos_mili + $hora_mili));
                $idProfissional = intval($item['id_profissional']);
                $status = 'LIVRE';
                DB::insert("INSERT INTO profissionais_servicos_agenda (id_profissional,data,horario_inicio,horario_fim,status) VALUES ($idProfissional,'$data','$horarioInicio','$horarioFim','$status')");
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            //return $error;
            return $th;
        }
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
    public function findCriarAgendaProfissional(int $id, string $data_inicio, string $hora_inicio, string $hora_fim)
    {

        $dados = DB::select('select * from profissionais_servicos_agenda where id_profissional = ? and data = ? and horario_inicio >= ? and horario_inicio <=?', [$id, $data_inicio, $hora_inicio, $hora_fim]);
        return $dados;
    }


    function tempoParaMilissegundos($tempo)
    {
        $carbon = Carbon::createFromFormat('H:i:s', $tempo);
        $milissegundos = $carbon->hour * 3600000 + $carbon->minute * 60000 + $carbon->second * 1000;
        return $milissegundos;
    }

    function milissegundosParaTempo($milissegundos)
    {
        $horas = floor($milissegundos / 3600000);
        $milissegundos %= 3600000;
        $minutos = floor($milissegundos / 60000);
        $milissegundos %= 60000;
        $segundos = floor($milissegundos / 1000);

        $tempo = sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
        return $tempo;
    }

    function minutosParaMilissegundos($minutos)
    {
        $milissegundos = $minutos * 60000;
        return $milissegundos;
    }

    // $minutos = 30; // Substitua pelo número de minutos que você deseja converter
    // $milissegundos = minutosParaMilissegundos($minutos);
    // echo $milissegundos;

    // $tempo = "18:20:00";
    // $milissegundos = tempoParaMilissegundos($tempo);
    // echo $milissegundos;

    // $milissegundos = 500000; // Substitua pelo valor de milissegundos que você deseja converter
    // $tempo = milissegundosParaTempo($milissegundos);
    // echo $tempo;

    function findAllAgendaProfissional($id)
    {
        $dataAtualFormatada = Carbon::now()->format('Y-m-d');
        $dataAtual = Carbon::now();
        $dataFutura = $dataAtual->addDays(6)->format('Y-m-d');
        $dados = DB::select('select * from profissionais_servicos_agenda where id_profissional = ? and data >= ? and data <=?', [$id, $dataAtualFormatada, $dataFutura]);
        return $dados;
    }


    function findAgendaProfissionalProximoHorario($id_profissionais_servicos_agenda, $id_profissional, $qtd_horario, $data)
    {
        for ($i = 0; $i < $qtd_horario; $i++) {
            $agenda = 0;
            $agenda = $id_profissionais_servicos_agenda + $i;
            $dados = DB::select('select * from profissionais_servicos_agenda where id_profissional = ? and id_profissionais_servicos_agenda = ? and data  = ?', [$id_profissional, $agenda, $data]);
            if (!empty($dados)) {
                if ($dados[0]->status != "LIVRE") {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }
}
