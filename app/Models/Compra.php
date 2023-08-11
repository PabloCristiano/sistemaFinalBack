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

    protected int  $numeroNota;
    protected int $serie;
    protected int $modelo;
    protected $status;
    protected $dataEmissao;
    protected $dataChegada;
    protected Fornecedor $fornecedor;
    // // protected $produtos;
    // /**
    //  * @var ContasPagar[]
    //  */
    // protected $contasPagar;
    protected int  $totalProduto;
    protected float $totalCompra;
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
        $this->totalProduto = 0;
        $this->condicaoPagamento = new CondicaoPagamento();
        $this->profissional = new Profissional();
        $this->dataCancelamento = '';
        $this->observacao = '';
    }

    public function setNumeroNota(int $numeroNota)
    {
        $this->numeroNota = $numeroNota;
    }

    public function setSerie(int  $serie)
    {
        $this->serie = $serie;
    }

    public function setModelo(int $modelo)
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

    public function setTotalProduto(int  $totalProduto)
    {
        $this->totalProduto = $totalProduto;
    }

    public function setTotalCompra( float $totalCompra)
    {
        $this->totalCompra = $totalCompra;
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

    public function getTotalProduto()
    {
       return $this->totalProduto;
    }

    public function getTotalCompra()
    {
        return $this->totalCompra;
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
