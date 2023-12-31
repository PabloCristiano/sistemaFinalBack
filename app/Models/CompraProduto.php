<?php

namespace App\Models;

use App\Models\Produto;
use App\Models\Fornecedor;


class CompraProduto
{
    protected $table = 'compra_produto';
    protected Produto $produto;
    protected Fornecedor $fornecedor;
    protected string $compra_modelo;
    protected string $compra_numero_nota;
    protected string $compra_serie;
    protected int $id_produto;
    protected int $qtd_produto;
    protected float $valor_unitario;
    protected float $valor_custo;
    protected float $total_produto;
    protected string $unidade;
    protected float $desconto;
    protected string $data_create;
    protected string $data_alt;
    public function __construct()
    {
        $this->produto = new Produto();
        $this->fornecedor = new Fornecedor();
        $this->compra_modelo = '';
        $this->compra_numero_nota = '';
        $this->compra_serie = '';
        $this->id_produto = 0;
        $this->qtd_produto = 0;
        $this->valor_unitario = 0;
        $this->valor_custo = 0;
        $this->total_produto = 0;
        $this->unidade = '';
        $this->desconto = 0;
        $this->data_create = '';
        $this->data_alt = '';
    }

    public function setCompraModelo(string $compra_modelo)
    {
        $this->compra_modelo = $compra_modelo;
    }

    public function getCompraModelo()
    {
        return $this->compra_modelo;
    }

    public function setCompraNumeroNota(string $compra_numero_nota)
    {
        $this->compra_numero_nota = $compra_numero_nota;
    }

    public function getCompraNumeroNota()
    {
        return $this->compra_numero_nota;
    }

    public function setCompraSerie(string $compra_serie)
    {
        $this->compra_serie = $compra_serie;
    }

    public function getCompraSerie()
    {
        return $this->compra_serie;
    }

    public function setProduto(Produto $produto)
    {
        $this->produto = $produto;
    }

    public function getProduto()
    {
        return $this->produto;
    }

    public function setFornecedor(Fornecedor $fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }

    public function getFornecedor()
    {
        return $this->fornecedor;
    }

    public function setIdProduto(int $id_produto)
    {
        $this->id_produto = $id_produto;
    }

    public function getIdProduto()
    {
        return $this->id_produto;
    }

    public function setQtdProduto(int $qtd_produto)
    {
        $this->qtd_produto = $qtd_produto;
    }

    public function getQtdProduto()
    {
        return $this->qtd_produto;
    }

    public function setValorUnitario(float $valor_unitario)
    {
        $this->valor_unitario = number_format($valor_unitario, 6,'.','');
    }

    public function getValorUnitario()
    {
        return $this->valor_unitario;
    }

    public function setValorCusto(float $valor_custo)
    {
        $this->valor_custo = number_format($valor_custo, 6,'.','');
    }


    public function getValorCusto()
    {
        return $this->valor_custo;
    }

    public function setTotalProduto(float $total_produto)
    {
        $this->total_produto = number_format($total_produto, 6,'.','');
    }


    public function getTotalProduto()
    {
        return $this->total_produto;
    }

    public function setUnidade(string $unidade)
    {
        $this->unidade = $unidade;
    }


    public function getUnidade()
    {
        return $this->unidade;
    }
    
    public function setDesconto(float $desconto)
    {
        $this->desconto = number_format($desconto, 6,'.','');
    }


    public function getDesconto()
    {
        return $this->desconto;
    }

    public function setDataCriacao(string $data_create)
    {
        $this->data_create = $data_create;
    }


    public function getDataCriacao()
    {
        return $this->data_create;
    }

    public function setDataAlteracao(string $data_alt)
    {
        $this->data_alt = $data_alt;
    }


    public function getDataAlteracao()
    {
        return $this->data_alt;
    }
}
