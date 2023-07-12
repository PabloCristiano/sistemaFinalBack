<?php

namespace App\Models;

use App\Models\FormasPagamento;

class Parcela extends TObject
{
    protected $parcela;
    protected $prazo;
    protected $porcentagem;
    protected $formasPagamento;

    public function __construct()
    {
        $this->parcela = 0;
        $this->prazo  = 0;
        $this->porcentagem = 0;
        $this->formasPagamento    = new FormasPagamento();
    }

    public function getParcela()
    {
        return $this->parcela;
    }

    public function setParcela(int $parcela)
    {
        $this->parcela = $parcela;
    }

    public function getPrazo()
    {
        return $this->prazo;
    }


    public function setPrazo(int $prazo)
    {
        $this->prazo = $prazo;
    }


    public function getPorcentagem()
    {
        return $this->porcentagem;
    }


    public function setPorcentagem(float $porcentagem)
    {
        $this->porcentagem = $porcentagem;
    }


    public function getFormasPagamento()
    {
        return $this->formasPagamento;
    }


    public function setFormasPagamento(FormasPagamento $formasPagamento)
    {
        $this->formasPagamento = $formasPagamento;
    }
}
