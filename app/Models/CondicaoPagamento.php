<?php

namespace App\Models;

use App\Models\Parcela;

class CondicaoPagamento extends TObject
{
    protected $condicaoPagamento;
    protected $juros;
    protected $multa;
    protected $desconto;
    protected $parcelas;
    protected $totalParcelas;

    public function __construct()
    {
        $this->condicaoPagamento = '';
        $this->juros             = 0;
        $this->multa             = 0;
        $this->desconto          = 0;
        $this->parcelas          = array();
        $this->totalParcelas     = 0;
    }


    public function getCondicaoPagamento()
    {
        return $this->condicaoPagamento;
    }

    public function setCondicaoPagamento(string $condicaoPagamento)
    {
        $this->condicaoPagamento = $condicaoPagamento;
    }

    public function getJuros()
    {
        return $this->juros;
    }

    public function setJuros(float $juros)
    {
        $this->juros = $juros;
    }

    public function getMulta()
    {
        return $this->multa;
    }

    public function setMulta(float $multa)
    {
        $this->multa = $multa;
    }

    public function getDesconto()
    {
        return $this->desconto;
    }

    public function setDesconto(float $desconto)
    {
        $this->desconto = $desconto;
    }

    public function getTotalParcelas()
    {
        return $this->totalParcelas;
    }

    public function setTotalParcelas(int $totalParcelas)
    {
        $this->totalParcelas = $totalParcelas;
    }

    public function getParcelas()
    {
        // dd($this->parcelas);
        return $this->parcelas;
    }

    public function setParcelas(array $parcelas)
    {

        $this->parcelas = $parcelas;
    }
}
