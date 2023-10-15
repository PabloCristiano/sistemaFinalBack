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
        // dd($obj, $id);
        $id_servico = $obj['id_servico'];
        $id_cliente = intval($obj['id_cliente']);
        $nome_cliente = $obj['cliente'];
        $preco = str_replace("R$ ", "", $obj['valor']);
        $preco = str_replace(",", ".", $preco);
        $preco = (float) $preco;
        $status = 'RESERVADO';
        $execucao = 'EXECUTAR';
        $qtd_horario = intval($obj['qtd_horario']);
        // $id_profissionais_servicos_agenda = 0;
        $id_profissionais_servicos_agenda = intval($obj['index']);
        $id_profissional = intval($obj['id_profissional']);
        try {
            DB::beginTransaction();
            for ($i = 0; $i < $qtd_horario; $i++) {
                $index = $id_profissionais_servicos_agenda + $i;
                $sql = DB::UPDATE(
                    'UPDATE
                    profissionais_servicos_agenda
                            SET id_servico = ?, id_cliente = ?, nome_cliente = ?, preco = ?, status = ?, execucao = ?
                            WHERE  id_profissionais_servicos_agenda = ? and id_profissional = ?',
                    [$id_servico, $id_cliente, $nome_cliente, $preco, $status, $execucao, $index, $id_profissional],
                );
            }
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
        $dados = DB::select('SELECT id_profissionais_servicos_agenda,
        id_profissional,
        id_servico,
            s. servico,
        id_cliente,
        nome_cliente,
        data,
        horario_inicio,
        horario_fim,
        preco,
        status,
        execucao,
        psa.data_create,
        psa.data_alt FROM profissionais_servicos_agenda AS psa
        LEFT JOIN servicos AS s ON s.id = psa.id_servico 
        where id_profissional = ? and data = ?', [$id, $data]);
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

    function AtulizarExecucaoAgenda($obj)
    {

        $id_profissionais_servicos_agenda = intval($obj['id_profissionais_servicos_agenda']);
        $id_profissional = intval($obj['id_profissional']);
        $id_cliente = intval($obj['id_cliente']);
        $horario_inicio = $obj['horario_inicio'];
        $dataFormatada = Carbon::createFromFormat('d/m/Y', $obj['data']);
        $dataFormatada = $dataFormatada->toDateString();
        $execucao = $obj['execucao'];
        $data_alt = Carbon::now();



        //dd($execucao);
        try {
            DB::beginTransaction();

            if ($execucao === 'EXECUTAR') {
                $execucao = 'EXECUTANDO';
                $sql = DB::UPDATE(
                    'UPDATE
                        profissionais_servicos_agenda
                                SET  execucao = ?, data_alt = ?
                                WHERE  id_profissionais_servicos_agenda = ? and id_profissional = ?',
                    [$execucao, $data_alt, $id_profissionais_servicos_agenda, $id_profissional],
                );
                DB::commit();
                return true;
            }

            if ($execucao === 'EXECUTANDO') {
                $execucao = 'EXECUTADO';
                $sql = DB::UPDATE(
                    'UPDATE
                        profissionais_servicos_agenda
                                SET  execucao = ?, data_alt = ?
                                WHERE  id_profissionais_servicos_agenda = ? and id_profissional = ?',
                    [$execucao, $data_alt, $id_profissionais_servicos_agenda, $id_profissional],
                );
                DB::commit();
                return true;
            }
        } catch (QueryException $e) {
            $mensagem = $e->getMessage(); // Mensagem de erro
            $codigo = $e->getCode(); // Código do erro
            $consulta = $e->getSql(); // Consulta SQL que causou o erro
            $bindings = $e->getBindings(); // Valores passados como bind para a consulta
            DB::rollBack();
            return [$mensagem, $codigo, $consulta, $bindings];
        }
        // dd($dataFormatada, $id_profissionais_servicos_agenda, $id_profissional, $id_cliente, $horario_inicio,  'DAO');
    }

    public function cancelarHorario(Request $request)
    {
        // dd($request->all());
        try {

            //pega Data ea  hora atual 
            $dataHoraAtual = Carbon::now();
            $data_atual = $dataHoraAtual->format('Y-m-d');
            $hora_atual = $dataHoraAtual->format('H:i:s');

            $data_atual_mili = $this->dataParaMilissegundos($data_atual);
            $hora_atual_mili = $this->horaParaMilissegundos($hora_atual);

            $Data_mili = $this->dataParaMilissegundos($request->data);
            $Hora_mili = $this->horaParaMilissegundos($request->horario_inicio);

            $soma_dataAtual = ($data_atual_mili + $hora_atual_mili);
            $soma_data = ($Data_mili + $Hora_mili);

            $id_profissionais_servicos_agenda = $request->id_profissionais_servicos_agenda;
            $id_profissional = $request->id_profissional;
            $horario_inicio = $request->horario_inicio;

            if ($soma_data  >  $soma_dataAtual && $request->status === "RESERVADO") {
                DB::beginTransaction();
                $sql = DB::UPDATE(
                    'UPDATE profissionais_servicos_agenda  SET  id_servico = null, id_cliente = null, id_servico = null, nome_cliente = null, status = "LIVRE", execucao = null   
                    where id_profissionais_servicos_agenda = ? and id_profissional = ? and horario_inicio = ?',
                    [$id_profissionais_servicos_agenda, $id_profissional, $horario_inicio],
                );
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

    public function dataParaMilissegundos($data)
    {
        $timestamp = strtotime(str_replace('/', '-', $data));
        $milissegundos = $timestamp * 1000;
        return  $milissegundos;
    }

    public function horaParaMilissegundos($hora)
    {
        list($horas, $minutos) = explode(':', $hora);
        $milissegundos = ($horas * 3600 + $minutos * 60) * 1000;
        return $milissegundos;
    }
}
