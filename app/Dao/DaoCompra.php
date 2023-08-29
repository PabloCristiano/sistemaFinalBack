<?php

namespace App\Dao;

use App\Dao\Dao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Compra;
use App\Dao\DaoFornecedor;
use App\Dao\DaoCompraProduto;
use App\Dao\DaoCondicaoPagamento;
use App\Dao\DaoProfissional;

class DaoCompra implements Dao
{
    protected Compra $compra;
    protected DaoFornecedor $daoFornecedor;
    protected DaoCompraProduto $daoCompraProduto;
    protected DaoCondicaoPagamento $daoCondicaoPagamento;
    protected DaoProfissional $daoProfissional;


    public function __construct()
    {
        $this->daoFornecedor = new DaoFornecedor();
        $this->daoCompraProduto = new DaoCompraProduto();
        $this->daoCondicaoPagamento = new DaoCondicaoPagamento();
        $this->daoProfissional = new DaoProfissional();
    }

    public function all(bool $json = false)
    {
        $itens = DB::select('select * from compra order by data_create desc');
        $compras = [];
        foreach ($itens as $item) {
            $compra = $this->create(get_object_vars($item));
            if ($json) {
                $compra_json = $this->getData($compra);
                array_push($compras, $compra_json);
            } else {
                array_push($compras, $compra);
            }
        }
        return $compras;
    }

    public function create(array $dados)
    {
        $compra = new Compra();

        // auth('api')->user();
        $profissional = auth('api')->user(); // resgata o usuário logado e autenticado 

        if (isset($dados["data_create"]) && isset($dados["data_alt"])) {
            $compra->setStatus($dados["status"]);
            $compra->setDataCadastro($dados["data_create"]);
            $compra->setDataAlteracao($dados["data_alt"]);
            $compra->setDataCancelamento($dados["data_cancelamento"]);
        }

        // Dados nota
        $compra->setModelo($dados["modelo"]);
        $compra->setSerie($dados["serie"]);
        $compra->setNumeroNota($dados["numero_nota"]);
        $compra->setDataEmissao($dados["data_emissao"]);
        $compra->setDataChegada($dados["data_chegada"]);
        $compra->setQtdProduto((int) $dados["qtd_produto"]);
        $compra->setFrete((float) $dados["frete"]);
        $compra->setValorProduto((float) $dados["valor_produto"]);
        $compra->setSeguro((float) $dados["seguro"]);
        $compra->setOutrasDespesas((float) $dados["outras_despesas"]);
        $compra->setObservacao((string) $dados["observacao"] ?? Null);

        //Dados Fornecedor
        $fornecedor = $this->daoFornecedor->findById($dados['id_fornecedor'], false);
        $fornecedores = $this->daoFornecedor->create(get_object_vars($fornecedor));
        $compra->setFornecedor($fornecedores);

        //Dados Condição de Pagamento
        $condiçãoPagamento = $this->daoCondicaoPagamento->findById($dados['id_condicaopg'], false);
        $condiçãoPagamento = $this->daoCondicaoPagamento->listarCondição(get_object_vars($condiçãoPagamento));
        $compra->setCondicaoPagamento($condiçãoPagamento);

        // Dados Produto
        $produtos = $this->daoCompraProduto->findById($compra->getModelo(), $compra->getNumeroNota(), $compra->getSerie(), true);
        if (!$produtos) {
            $compra->setCompraProdutoArray($dados['produtos']);
            $valor_compra = $this->calcTotalCompra($dados['produtos']);
            $compra->setValorCompra(floatval($valor_compra));
        } else {
            $compra->setCompraProdutoArray($produtos);
            $valor_compra = $this->calcTotalCompra($compra->getCompraProdutoArray());
            $compra->setValorCompra(floatval($valor_compra));
        }

        //Dados Profissional 
        $profissional = $this->daoProfissional->findById($dados['id_profissional'], false);
        $profissional = $this->daoProfissional->create(get_object_vars($profissional));
        $compra->setProfissional($profissional);

        $compra->setFrete((float) $dados["frete"]);

        return $compra;
    }

    public function store($compra)
    {
        $modelo = $compra->getModelo();
        $nunero_nota = $compra->getNumeroNota();
        $serie = $compra->getSerie();
        $id_fornecedor = $compra->getFornecedor()->getId();
        $status = $compra->getStatus();
        $dataEmissao = $compra->getDataEmissao();
        $dataChegada = $compra->getDataChegada();
        $compraProduto_array = $compra->getCompraProdutoArray();
        $frete = $compra->getFrete();
        $valor_produto = $compra->getValorProduto();
        $seguro = $compra->getSeguro();
        $outras_despesas = $compra->getOutrasDespesas();
        $qtd_produto = $compra->getQtdProduto();
        $valor_compra = $compra->getValorCompra();
        $id_condicaopg = $compra->getCondicaoPagamento()->getId();
        $id_profissional = $compra->getProfissional()->getId();
        $dataCancelamento = $compra->getDataCancelamento();
        $observacao = $compra->getObservacao();
        dd('store', $compra);
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

    public function getData(Compra $compra)
    {
        $dados = [
            'modelo' => $compra->getModelo(),
            'numero_nota' => $compra->getNumeroNota(),
            'serie'  => $compra->getSerie(),
            'qtd_produto' => $compra->getQtdProduto(),
            'valor_compra' => $compra->getValorCompra(),
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
            'data_alt' => $compra->getDataAlteracao()
        ];
        return $dados;
    }

    public function calcTotalCompra(array $compraProduto)
    {
        $total = 0;
        foreach ($compraProduto as $produto) {
            $total += $produto['valor_unitario'] * $produto['qtd_produto'];
        }
        return floatval($total);
    }
}
