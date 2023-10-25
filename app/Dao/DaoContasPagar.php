<?php

namespace App\Dao;

use App\Dao\Dao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

use App\Models\ContasPagar;
use App\Dao\DaoFornecedor;
use App\Dao\DaoFormasPagamento;

class DaoContasPagar implements Dao
{
    protected $daoFornecedor;
    protected $daoformasPagamento;

    public function __construct()
    {
        $this->daoFornecedor = new DaoFornecedor();
        $this->daoformasPagamento = new DaoFormasPagamento();
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

        $contasPagar->setDataCadastro($dados['data_create'] ?? null);
        $contasPagar->setDataAlteracao($dados['data_alt'] ?? null);

        $contasPagar->setNumeroNota($dados['compra_numero_nota']);
        $contasPagar->setSerie($dados['compra_serie']);
        $contasPagar->setModelo($dados['compra_modelo']);
        $contasPagar->setParcela($dados['numero_parcela']);

        $fornecedor = $this->daoFornecedor->findById($dados['compra_id_fornecedor'], false);
        $fornecedor = $this->daoFornecedor->create(get_object_vars($fornecedor));
        $contasPagar->setFonecedor($fornecedor);
        $formasPagamento = $this->daoformasPagamento->findById($dados['id_formapagamento'], false);
        $formasPagamento = $this->daoformasPagamento->create(get_object_vars($formasPagamento));
        $contasPagar->setFormaPagamento($formasPagamento);

        $contasPagar->setParcela($dados['numero_parcela']);
        $contasPagar->setValorParcela($dados['valor_parcela']);

        $contasPagar->setDataEmissao($dados['data_emissao']);
        $contasPagar->setDataVencimeto($dados['data_vencimento']);
        $contasPagar->setDataPagamento($dados['data_pagamento'] ?? "");

        $contasPagar->setJuros($dados['juros']);
        $contasPagar->setDesconto($dados['desconto']);

        $contasPagar->setValorPago($dados['valor_pago'] ?? 0);
        $contasPagar->setStatus($dados['status']);

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
            'fornecedor'  =>  $this->daoFornecedor->getData($contasPagar->getFonecedor()),
            'formaPagamento' =>  $this->daoformasPagamento->getData($contasPagar->getFormaPagamento()),
            'valor_parcela' => $contasPagar->getValorParcela(),
            'data_emissao' => $contasPagar->getDataEmissao(),
            'data_vencimento' => $contasPagar->getDataVencimeto(),
            'juros' => $contasPagar->getJuros(),
            'desconto' => $contasPagar->getDesconto(),
            'valor_pago' => $contasPagar->getValorPago(),
            'status'  =>  $contasPagar->getStatus(),
            'data_create' => $contasPagar->getDataCadastro(),
            'data_alt' => $contasPagar->getDataAlteracao()
            /*'condicao_pagamento' => $this->daoCondicaoPagamento->getData($compra->getCondicaoPagamento()),
            'produtos' => $compra->getCompraProdutoArray(),
            'profissional' => $this->daoProfissional->getData($compra->getProfissional()),
            'status' =>  $compra->getStatus(),
            'data_cancelamento' => $compra->getDataCancelamento(),
            'observacao' => $compra->getObservacao(),*/
        ];
        return $dados;
    }
}
