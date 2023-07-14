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
    public function all(bool $json = true)
    {

        $itens = DB::select('select * from condicao_pg');
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
    }

    public function store($obj)
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
}
