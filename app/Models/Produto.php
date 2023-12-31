<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Categorias;
use App\Models\Fornecedor;


class Produto extends TObject
{

    protected $table = 'produtos';
    protected $produto;
    protected $unidade;
    protected $fornecedor;
    protected $categoria;
    protected $qtdEstoque;
    protected $precoCusto;
    protected $precoVenda;
    protected $custoUltCompra;
    protected $dataUltCompra;
    protected $dataUltVenda;

    public function __construct()
    {
        $this->produto           = '';
        $this->unidade           = '';
        $this->qtdEstoque        = 0;
        $this->precoCusto        = 0;
        $this->precoVenda        = 0;
        $this->custoUltCompra = 0;
        $this->dataUltCompra  = '';
        $this->dataUltVenda   = '';
        $this->fornecedor = new Fornecedor();
        $this->categoria  = new Categorias();
    }


    public function getProduto()
    {
        return $this->produto;
    }


    public function setProduto(string $produto)
    {
        $this->produto = strtoupper($produto);
    }


    public function getUnidade()
    {
        return $this->unidade;
    }


    public function setUnidade(string $unidade = null)
    {
        $this->unidade = $unidade;
    }


    public function getFornecedor()
    {
        return $this->fornecedor;
    }


    public function setFornecedor(Fornecedor $fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }


    public function getCategoria()
    {
        return $this->categoria;
    }


    public function setCategoria(Categorias $categoria)
    {
        $this->categoria = $categoria;
    }


    public function getQtdEstoque()
    {
        return $this->qtdEstoque;
    }


    public function setQtdEstoque(int $qtdEstoque)
    {
        $this->qtdEstoque = $qtdEstoque;
    }


    public function getPrecoCusto()
    {
        return $this->precoCusto;
    }


    public function setPrecoCusto(float $precoCusto)
    {
        $this->precoCusto = $precoCusto;
    }


    public function getPrecoVenda()
    {
        return $this->precoVenda;
    }


    public function setPrecoVenda(float $precoVenda )
    {
        $this->precoVenda = $precoVenda;
    }


    public function getCustoUltCompra()
    {
        return $this->custoUltCompra;
    }


    public function setCustoUltCompra(float $custoUltCompra = null)
    {
        $this->custoUltCompra = $custoUltCompra;
    }


    public function getDataUltCompra()
    {
        return $this->dataUltCompra;
    }


    public function setDataUltCompra(string $dataUltCompra )
    {   
        // dd($dataUltCompra);
        //  $dataUltCompra = Carbon::parse($dataUltCompra);
        // $dataUltCompra = $dataUltCompra->format('Y-m-d');
        $this->dataUltCompra = $dataUltCompra;
    }


    public function getDataUltVenda()
    {
        return $this->dataUltVenda;
    }


    public function setDataUltVenda(string $dataUltVenda = null)
    {
        $this->dataUltVenda = $dataUltVenda;
    }
}
