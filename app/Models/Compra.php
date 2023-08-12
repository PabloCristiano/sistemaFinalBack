<?php

namespace App\Models;

use App\Models\Fornecedor;
use App\Models\Profissional;
use App\Models\CondicaoPagamento;
// use App\Models\ProdutoCompra;
// use App\Models\ContasPagar;

use Illuminate\Support\Carbon;
use stdClass;

class Compra extends TObject
{

    protected  $numeroNota;
    protected  $serie;
    protected  $modelo;
    protected  $status;
    protected  $dataEmissao;
    protected  $dataChegada;
    protected Fornecedor $fornecedor;
    // // protected $produtos;
    // /**
    //  * @var ContasPagar[]
    //  */
    // protected $contasPagar;
    protected float $frete;
    protected int  $qtd_produto;
    protected float $valor_compra;
    protected CondicaoPagamento $condicaoPagamento;
    protected Profissional $profissional;
    protected $dataCancelamento;
    protected $observacao;

    public function __construct()
    {
        $this->numeroNota = 0;
        $this->serie = 0;
        $this->modelo = 0;
        $this->status = '';
        $this->dataEmissao = '';
        $this->dataChegada = '';
        $this->fornecedor = new Fornecedor();
        $this->frete = 0;
        $this->qtd_produto = 0;
        $this->condicaoPagamento = new CondicaoPagamento();
        $this->profissional = new Profissional();
        $this->dataCancelamento = '';
        $this->observacao = '';
    }

    public function setNumeroNota($numeroNota)
    {
        $this->numeroNota = $numeroNota;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function setDataEmissao($dataEmissao)
    {
        $this->dataEmissao = $dataEmissao;
    }

    public function setDataChegada($dataChegada)
    {
        $this->dataChegada = $dataChegada;
    }

    public function setFornecedor(Fornecedor $fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }

    public function setFrete(int $frete)
    {
        $this->frete = $frete;
    }

    public function setQtdProduto(int $qtd_produto)
    {
        $this->qtd_produto = $qtd_produto;
    }

    public function setValorCompra(float $valor_compra)
    {
        $this->valor_compra = $valor_compra;
    }

    public function setCondicaoPagamento(CondicaoPagamento $condicaoPagamento)
    {
        $this->condicaoPagamento = $condicaoPagamento;
    }

    public function setProfissional(Profissional $profissional)
    {
        $this->profissional = $profissional;
    }

    public function setDataCancelamento($dataCancelamento)
    {
        $this->dataCancelamento = $dataCancelamento;
    }

    public function setObservacao(string $observacao)
    {
        $this->observacao = $observacao;
    }


    public function getNumeroNota()
    {
        return  $this->numeroNota;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDataEmissao()
    {
        return $this->dataEmissao;
    }

    public function getDataChegada()
    {
        return $this->dataChegada;
    }

    public function getFornecedor()
    {
        return $this->fornecedor;
    }

    public function getFrete()
    {
        return $this->frete;
    }

    public function getQtdProduto()
    {
        return $this->qtd_produto;
    }

    public function getValorCompra()
    {
        return $this->valor_compra;
    }

    public function getCondicaoPagamento()
    {
        return $this->condicaoPagamento;
    }

    public function getProfissional()
    {
        return $this->profissional;
    }

    public function getDataCancelamento()
    {
        return $this->dataCancelamento;
    }

    public function getObservacao()
    {
        return $this->observacao;
    }
}
