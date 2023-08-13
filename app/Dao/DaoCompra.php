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

class DaoCompra implements Dao
{
    protected Compra $compra;
    protected DaoFornecedor $daoFornecedor;
    protected DaoCompraProduto $daoCompraProduto;
    protected DaoCondicaoPagamento $daoCondicaoPagamento;


    public function __construct()
    {
        $this->compra = new Compra();
        $this->daoFornecedor = new DaoFornecedor();
        $this->daoCompraProduto = new DaoCompraProduto();
        $this->daoCondicaoPagamento = new DaoCondicaoPagamento();
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
        $compra->setQtdProduto($dados["qtd_produto"]);
        $compra->setValorCompra($dados["valor_compra"]);
        
        //Dados Fornecedor
        $fornecedor = $this->daoFornecedor->findById($dados['id_fornecedor'], false);
        $fornecedores = $this->daoFornecedor->create(get_object_vars($fornecedor));
        $compra->setFornecedor($fornecedores);

        //Dados Condição de Pagamento
        $condiçãoPagamento = $this->daoCondicaoPagamento->findById($dados['id_condicaopg'], false);
        $condiçãoPagamento = $this->daoCondicaoPagamento->listarCondição(get_object_vars($condiçãoPagamento));
        $compra->setCondicaoPagamento($condiçãoPagamento);

        // Dados Produto
         $produtos = $this->daoCompraProduto->findById($compra->getModelo(),$compra->getNumeroNota(), $compra->getSerie(),true);
         $compra->setCompraProdutoArray($produtos);
        return $compra;
    }

    public function store($compra)
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

    public function getData(Compra $compra)
    {
        $dados = [
            'modelo' => $compra->getModelo(),
            'numero_nota' => $compra->getNumeroNota(),
            'serie'  => $compra->getSerie(),
            'qtd_produto' => $compra->getQtdProduto(),
            'valor_compra' => $compra->getValorCompra(),
            'data_emissao' => $compra->getDataEmissao(),
            'data_chegada' => $compra->getDataChegada(),
            'fornecedor'  =>  $this->daoFornecedor->getData($compra->getFornecedor()),
            'condicao_pagamento'=> $this->daoCondicaoPagamento->getData($compra->getCondicaoPagamento()),
            'produtos' => $compra->getCompraProdutoArray(),
            'status' =>  $compra->getStatus(),
            'data_cancelamento' => $compra->getDataCancelamento(),
            'data_create' => $compra->getDataCadastro(),
            'data_alt' => $compra->getDataAlteracao()
        ];
        return $dados;
    }

   
}
