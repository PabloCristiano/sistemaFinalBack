<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaoServico implements Dao
{
    public function all(bool $json = true)
    {
        $itens = DB::select('select * from servicos order by id desc');
        $servicos = [];
        foreach ($itens as $item) {
            $servico = $this->create(get_object_vars($item));
            if ($json) {
                $servico_json = $this->getData($servico);
                array_push($servicos, $servico_json);
            } else {
                array_push($servicos, $servico);
            }
        }
        return $servicos;
    }

    public function create(array $dados)
    {
        $servico = new Servico();
        if (isset($dados['id'])) {
            $servico->setId($dados['id']);
            $servico->setDataCadastro($dados['data_create'] ?? null);
            $servico->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $servico->setServico($dados['servico']);
        $servico->setTempo($dados['tempo']);
        $servico->setValor($dados['valor']);
        $servico->setComissao($dados['comissao']);
        $servico->setObservacoes($dados['observacoes']);
        return $servico;
    }

    public function store($obj)
    {
        $servico = $obj->getServico();
        $tempo = $obj->getTempo();
        $valor = $obj->getValor();
        $comissao = $obj->getComissao();
        $observacoes = $obj->getObservacoes();
        try {
            DB::beginTransaction();
            DB::SELECT("INSERT INTO servicos (servico, tempo, valor, comissao, observacoes)  VALUES ('$servico',$tempo, $valor,$comissao,'$observacoes')");
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            return $error;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $servicos = $this->create($request->all());
            $servicos->setDataAlteracao(Carbon::now());
            $servico = $servicos->getServico();
            $tempo   = $servicos->getTempo();
            $valor   = $servicos->getValor();
            $comissao = $servicos->getComissao();
            $observacoes = $servicos->getObservacoes();
            $data_alt = $servicos->getDataAlteracao();
            DB::UPDATE('UPDATE servicos SET servico = ?,tempo = ?, valor = ?, comissao = ?, observacoes = ?, data_alt = ? where id = ?',
                         [$servico,$tempo,$valor,$comissao,$observacoes,$data_alt,$id]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ['error' => $e->getMessage(), 'CodigoError' => $e->getCode()];
            return $error;
        }
    }
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            // DB::table('servicos')->delete($id);
            $sql = DB::DELETE("DELETE FROM  servicos where id = '$id'");
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return $error = ['Não foi possível excluir, registro já vinculado.'];
            }
            return $error = ['Não foi possível excluir o registro.'];
        }
    }
    public function findById(int $id, bool $model = false)
    {
        if (!$model) {
            $dados = DB::select('select * from servicos where id = ?', [$id]);
            return $dados;
        }
        // $dados = DB::table('servicos')->where('id', $id)->first();
        $dados = DB::select('select * from servicos where id = ?', [$id]);
        if ($dados) {
            $servicos = [];
            foreach ($dados as $item) {
                $servico = $this->create(get_object_vars($item));
                $servico_json = $this->getData($servico);
                array_push($servicos, $servico_json);
            }
            return $servicos;
        }
    }

    public function getData(Servico $servico)
    {
        $dados = [
            'id' => $servico->getId(),
            'servico' => $servico->getServico(),
            'tempo' => $servico->getTempo(),
            'valor' => $servico->getValor(),
            'comissao' => $servico->getComissao(),
            'observacoes' => $servico->getObservacoes(),
            'data_create' => $servico->getDataCadastro(),
            'data_alt' => $servico->getDataAlteracao(),
        ];

        return $dados;
    }
}
