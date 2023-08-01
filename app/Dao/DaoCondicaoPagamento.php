<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoParcela;
use App\Models\CondicaoPagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class DaoCondicaoPagamento implements Dao
{
    private DaoParcela $daoParcela;

    public function __construct()
    {
        $this->daoParcela = new DaoParcela();
    }
    public function all(bool $json = true)
    {

        $itens = DB::select('select * from condicao_pg order by id desc');
        $listCondicao = [];
        foreach ($itens as $item) {
            $lista_condicao = $this->listarCondição(get_object_vars($item));
            if ($json) {
                $listCondicao_json = $this->getData($lista_condicao);
                array_push($listCondicao, $listCondicao_json);
            } else {
                array_push($listCondicao, $lista_condicao);
            }
        }
        return $listCondicao;
    }

    public function create(array $dados)
    {
        $condicaoPagamento = new CondicaoPagamento();

        if (isset($dados["id"])) {
            $condicaoPagamento->setId(($dados["id"]));
            $condicaoPagamento->setDataCadastro($dados["data_create"] ?? null);
            $condicaoPagamento->setDataAlteracao($dados["data_alt"] ?? null);
        }
        $condicaoPagamento->setCondicaoPagamento($dados["condicao_pagamento"]);
        $condicaoPagamento->setJuros((float)$dados["juros"]);
        $condicaoPagamento->setMulta((float)$dados["multa"]);
        $condicaoPagamento->setDesconto((float)$dados["desconto"]);
        $totalParcelas =   $dados["qtd_parcela"];
        $condicaoPagamento->setTotalParcelas($totalParcelas);
        if (isset($dados["parcelas"])) {
            $parcelas = $this->gerarParcelas($dados["parcelas"], $condicaoPagamento);
            $condicaoPagamento->setParcelas($parcelas);
        }
        return $condicaoPagamento;
    }

    public function store($obj)
    {
        DB::beginTransaction();
        try {
            $condicao_pagamento = $obj->getCondicaoPagamento();
            $juros = $obj->getJuros();
            $multa = $obj->getMulta();
            $desconto = $obj->getDesconto();
            $qtd_parcela = $obj->getTotalParcelas();
            DB::INSERT("INSERT INTO condicao_pg (condicao_pagamento, juros, multa, desconto, qtd_parcela) VALUES('$condicao_pagamento', '$juros', '$multa',' $desconto','$qtd_parcela')");
            $idCondicaoPagamento = DB::select('select id from condicao_pg order by id desc limit 1');
            $idCondicaoPagamento = $idCondicaoPagamento ? $idCondicaoPagamento[0]->id : null;
            $storeParcela = $this->salvarParcelas($obj->getParcelas(), $idCondicaoPagamento);
            if (empty($storeParcela)) {
                DB::commit();
                return ['success' => true, 'Message' => 'Condição de Pagamento cadastrada com Sucesso!'];
            }
            return $storeParcela;
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

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $deleteParcela = $this->daoParcela->delete($id);
            if (empty($deleteParcela)) {
                $sql = DB::Select("DELETE FROM condicao_pg WHERE id= '$id'");
                if (empty($sql)) {
                    DB::commit();
                    return ['success' => true, 'Message' => 'Condição de Pagamento Excluida com Sucesso!'];
                }
            }
        } catch (QueryException $e) {
            $mensagem = $e->getMessage(); // Mensagem de erro
            $codigo = $e->getCode(); // Código do erro
            $consulta = $e->getSql(); // Consulta SQL que causou o erro
            $bindings = $e->getBindings(); // Valores passados como bind para a consulta
            $error = "Não foi possível excluir Condição de Pagamento, registro já vinculado ";
            DB::rollBack();
            return [$error,$mensagem, $codigo, $consulta, $bindings];
        }
    }

    public function findById(int $id, bool $model = false)
    {
        if (!$model) {
            // return DB::table('condicao_pg')->get()->where('id', $id)->first();
            $dados = DB::select('select * from condicao_pg where id = ?', [$id]);
            return $dados[0];
        }
        // $dados = DB::table('condicao_pg')->where('id', $id)->first();
        $dados = DB::select('select * from condicao_pg where id = ?', [$id]);
        if ($dados) {
            $condicaoPagamnentos = [];
            foreach ($dados as $item) {
                $condicaoPagamnento = $this->listarCondição(get_object_vars($item));
                $condicaoPagamnento_json = $this->getData($condicaoPagamnento);
                array_push($condicaoPagamnentos, $condicaoPagamnento_json);
            }
            return $condicaoPagamnentos;
        }
    }

    function listarCondição(array $dados)
    {

        $condicaoPagamento = new CondicaoPagamento();
        $condicaoPagamento->setId(($dados["id"]));
        $condicaoPagamento->setDataCadastro($dados["data_create"] ?? null);
        $condicaoPagamento->setDataAlteracao($dados["data_alt"] ?? null);
        $condicaoPagamento->setCondicaoPagamento($dados["condicao_pagamento"]);
        $condicaoPagamento->setJuros((float)$dados["juros"]);
        $condicaoPagamento->setMulta((float)$dados["multa"]);
        $condicaoPagamento->setDesconto((float)$dados["desconto"]);
        $totalParcelas =   $dados["qtd_parcela"];
        $condicaoPagamento->setTotalParcelas($totalParcelas);
        $parcelas = array();
        $parcelas = $this->buscarParcelas($condicaoPagamento->getId());
        $condicaoPagamento->setParcelas($parcelas);
        return $condicaoPagamento;
    }

    public function buscarParcelas($idCondicaoPagamento)
    {
        $dados = DB::select('select * from parcelas where idcondpg = ?', [$idCondicaoPagamento]);
        $parcelas = array();
        foreach ($dados as $dadosParcela) {
            $parcela = $this->daoParcela->create(get_object_vars($dadosParcela));
            array_push($parcelas, $parcela);
        }
        return $parcelas;
    }

    public function getData(CondicaoPagamento $condicaoPagamento)
    {
        $data = [
            'id' => $condicaoPagamento->getId(),
            'condicao_pagamento' => $condicaoPagamento->getCondicaoPagamento(),
            'juros' => $condicaoPagamento->getJuros(),
            'multa' => $condicaoPagamento->getMulta(),
            'desconto' => $condicaoPagamento->getDesconto(),
            'qtd_parcela' => $condicaoPagamento->getTotalParcelas(),
            'parcelas' => $this->daoParcela->getDataGerarParcelas($condicaoPagamento->getParcelas()),
            'data_create' => $condicaoPagamento->getDataCadastro(),
            'data_alt' => $condicaoPagamento->getDataAlteracao(),
        ];
        return $data;
    }

    public function gerarParcelas($dadosParcelas, $condicaoPagamento)
    {
        $parcelaCondicao = [];
        $qtd = $condicaoPagamento->getTotalParcelas();
        for ($i = 0; $i < $qtd; $i++) {
            $dadosParcela = [
                "parcela"            => $dadosParcelas[$i]["parcela"],
                "prazo"              => $dadosParcelas[$i]["prazo"],
                "porcentagem"        => $dadosParcelas[$i]["porcentagem"],
                "idformapg"          => $dadosParcelas[$i]["formaPagamento"][0]["id"],
            ];
            array_push($parcelaCondicao, $dadosParcela);
        }
        return $parcelaCondicao;
    }

    public function salvarParcelas(array $parcelas, $idCondicaoPagamento)
    {
        $qtd = count($parcelas);
        try {
            for ($i = 0; $i < $qtd; $i++) {
                $dadosParcela = [
                    'parcela'                => $parcelas[$i]["parcela"],
                    'prazo'                  => $parcelas[$i]["prazo"],
                    'porcentagem'            => $parcelas[$i]["porcentagem"],
                    'idformapg'             => $parcelas[$i]["idformapg"],
                    'idcondpg'              => $idCondicaoPagamento,
                ];
                $resp =  $this->daoParcela->store($dadosParcela);
            }
            return  $resp;
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
