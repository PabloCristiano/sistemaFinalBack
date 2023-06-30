<?php

namespace App\Models;

class Servico extends TObject
{
    protected $table = 'servicos';
    protected $servico;
    protected $tempo;
    protected $valor;
    protected $comissao;
    protected $observacoes;

    public function __construct()
    {
        $this->servico = '';
        $this->tempo = null;
        $this->valor =null;
        $this->comissao = null;
        $this->observacoes = '';
    }

    public function setServico($servico)
    {
        $this->servico = strtoupper($servico);
    }

    public function setTempo($tempo)
    {
        $this->tempo = intval($tempo);
    }

    public function setValor($valor)
    {
        $this->valor = floatval($valor);
    }

    public function setObservacoes($observacoes)
    {
        $this->observacoes = strtoupper($observacoes);
    }


    public function setComissao($comissao)
    {
        $this->comissao = floatval($comissao);
    }


    // GETTERS

    public function getServico()
    {
        return $this->servico;
    }

    public function getTempo()
    {
        return $this->tempo;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function getComissao()
    {
        return $this->comissao;
    }

    public function getObservacoes()
    {
        return $this->observacoes;
    }
}
