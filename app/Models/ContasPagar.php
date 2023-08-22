<?php

namespace App\Models;

use App\Models\Compra;
use App\Models\Fornecedor;
use App\Models\FormasPagamento;
use App\Models\Profissional;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ContasPagar extends TObject
{

    protected int $numeroNota;
    protected int $serie;
    protected int $modelo;
    protected Compra $compra;
    protected Fornecedor $fornecedor;
    protected Profissional $profissional;
    protected FormasPagamento $formaPagamento;
    protected int $parcela;
    protected float $valorParcela;
    protected string $dataEmissao;
    protected string $dataVencimento;
    protected string $dataPagamento;
    protected float $juros;
    protected float $multa;
    protected float $desconto;
    protected float $valorPago;
    protected string $status;
    protected string $dataCancelamento;


    public function __construct()
    {
        $this->numeroNota = 0;
        $this->serie = 0;
        $this->modelo = 0;
        $this->compra =  new Compra();
        $this->fornecedor = new Fornecedor();
        $this->profissional = new Profissional();
        $this->formaPagamento = new FormasPagamento();
        $this->parcela = 0;
        $this->valorParcela = 0;
        $this->dataEmissao = '';
        $this->dataVencimento = '';
        $this->dataPagamento = '';
        $this->juros = 0;
        $this->multa = 0;
        $this->desconto = 0;
        $this->valorPago = 0;
        $this->status = '';
        $this->dataCancelamento = '';
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

    public function setCompra(Compra $compra)
    {
        $this->compra = $compra;
    }

    public function setFonecedor(Fornecedor $fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }

    public function setProfissional(Profissional $profissional)
    {
        $this->profissional = $profissional;
    }

    public function setFormaPagamento(FormasPagamento $formaPagamento)
    {
        $this->formaPagamento = $formaPagamento;
    }

    public function setParcela(int $parcela)
    {
        $this->parcela = $parcela;
    }

    public function setValorParcela(float $valorParcela)
    {
        $this->valorParcela = $valorParcela;
    }

    public function setDataEmissao(string $dataEmissao)
    {
        $this->dataEmissao = $dataEmissao;
    }

    public function setDataVencimeto(string $dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;
    }

    public function setDataPagamento(string $dataPagamento)
    {
        $this->dataPagamento = $dataPagamento;
    }

    public function setJuros(float $juros)
    {
        $this->juros = $juros;
    }

    public function setMulta(float $multa)
    {
        $this->multa = $multa;
    }

    public function setDesconto(float $desconto)
    {
        $this->desconto = $desconto;
    }

    public function setValorPago(float $valorPago)
    {
        $this->valorPago = $valorPago;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function setDataCancelamento(string $dataCancelamento)
    {
        $this->dataCancelamento = $dataCancelamento;
    }

    public function getNumeroNota()
    {
        return $this->numeroNota;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function getModelo()
    {
        return $this->modelo;
    }

    public function getCompra()
    {
        return $this->compra;
    }

    public function getFonecedor()
    {
        return $this->fornecedor;
    }

    public function getProfissional()
    {
        return $this->profissional;
    }

    public function getFormaPagamento()
    {
        return $this->formaPagamento;
    }

    public function getParcela()
    {
        return $this->parcela;
    }

    public function getValorParcela()
    {
        return $this->valorParcela;
    }

    public function getDataEmissao()
    {
        return $this->dataEmissao;
    }

    public function getDataVencimeto()
    {
        return $this->dataVencimento;
    }

    public function getDataPagamento()
    {
        return $this->dataPagamento;
    }

    public function getJuros()
    {
        return $this->juros;
    }

    public function getMulta()
    {
        return $this->multa;
    }

    public function getDesconto()
    {
        return $this->desconto;
    }

    public function getValorPago()
    {
        return $this->valorPago;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDataCancelamento()
    {
        return $this->dataCancelamento;
    }
}
