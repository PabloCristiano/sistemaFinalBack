<?php

namespace App\Dao;

use App\Dao\Dao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

use App\Models\ContasPagar;

class DaoContasPagar implements Dao
{
    public function __construct()
    {
    }
    public function all(bool $json = false)
    {
        $itens = DB::select('SELECT * 
        FROM contas_pagar 
        ORDER BY data_vencimento, numero_parcela ASC');
        $contasPagar = [];
        foreach ($itens as $item) {
            $contaPagar = $this->create(get_object_vars($item));
            if ($json) {
                $contaPagar_json = $this->getData($contaPagar);
                array_push($contasPagar, $contaPagar_json);
            } else {
                array_push($contasPagar, $contaPagar);
            }
        }
        return $contasPagar;
    }

    public function create(array $dados)
    {
        $contasPagar = new ContasPagar;

        $contasPagar->setNumeroNota($dados['compra_numero_nota']);
        $contasPagar->setSerie($dados['compra_serie']);
        $contasPagar->setModelo($dados['compra_modelo']);
        $contasPagar->setParcela($dados['numero_parcela']);
        return $contasPagar;
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

    public function getData(ContasPagar $contasPagar)
    {

        $dados = [
            'compra_modelo' => $contasPagar->getModelo(),
            'compra_numero_nota' => $contasPagar->getNumeroNota(),
            'compra_serie'  => $contasPagar->getSerie(),
            'numero_parcela' => $contasPagar->getParcela(),
            /*'valor_compra' => $compra->getValorCompra(),
            'valor_produto' => $compra->getValorProduto(),
            'frete' => $compra->getFrete(),
            'seguro' => $compra->getSeguro(),
            'outras_despesas' => $compra->getOutrasDespesas(),
            'data_emissao' => $compra->getDataEmissao(),
            'data_chegada' => $compra->getDataChegada(),
            'fornecedor'  =>  $this->daoFornecedor->getData($compra->getFornecedor()),
            'condicao_pagamento' => $this->daoCondicaoPagamento->getData($compra->getCondicaoPagamento()),
            'produtos' => $compra->getCompraProdutoArray(),
            'profissional' => $this->daoProfissional->getData($compra->getProfissional()),
            'status' =>  $compra->getStatus(),
            'data_cancelamento' => $compra->getDataCancelamento(),
            'observacao' => $compra->getObservacao(),
            'data_create' => $compra->getDataCadastro(),
            'data_alt' => $compra->getDataAlteracao()*/
        ];
        return $dados;
    }
}
