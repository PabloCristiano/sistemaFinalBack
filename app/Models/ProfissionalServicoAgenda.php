<?php

namespace App\Models;

use App\Models\Profissional;
use App\Models\Servico;
use App\Models\Cliente;

class ProfissionalServico extends TObject
{
    protected $table = 'profissionais_servicos';
    protected $profissional;
    protected $servico;
    protected $cliente;
    protected  $data;
    protected  $hora_inicio;
    protected  $hora_fim;
    protected float  $preco;
    protected string  $status;
    protected string $avaliacao;

    public function __construct()
    {
        $this->profissional = new Profissional;
        $this->servico = new Servico;
        $this->cliente = new Cliente;
        $this->data = '';
        $this->hora_inicio = '';
        $this->hora_fim = '';
        $this->preco = 0;
        $this->status = '';
        $this->avaliacao = '';
    }





    public function setProfissional(Profissional $profissional)
    {
        $this->profissional = $profissional;
    }

    public function getProfissional()
    {
        return $this->profissional;
    }

    public function setServico(Servico $servico)
    {
        $this->servico = $servico;
    }

    public function getServico()
    {
        return $this->servico;
    }

    public function setCliente(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setHoraInicio($hora_inicio)
    {
        $this->hora_inicio = $hora_inicio;
    }

    public function getHoraInicio()
    {
        return $this->hora_inicio;
    }

    public function setHoraFim($hora_fim)
    {
        $this->hora_fim = $hora_fim;
    }

    public function getHoraFim()
    {
        return $this->hora_fim;
    }

    public function setPreco($preco)
    {
        $this->preco = $preco;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setAvaliacao($avaliacao)
    {
        $this->avaliacao = $avaliacao;
    }

    public function geAvaliacao()
    {
        return $this->avaliacao;
    }
}
