<?php

namespace App\Models;

use App\Models\Profissional;
use App\Models\Servico;

class ProfissionalServico extends TObject
{
    protected $table = 'profissionais_servicos';
    protected $profissional;
    protected $servico;
    protected $cliente;
    protected string $data;
    protected string $hora_inicio;
    protected string $hora_fim;
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

    public function setQtdServico($qtd_servico)
    {
        $this->qtd_servico = $qtd_servico;
    }

    public function setServico(Servico $servico)
    {
        $this->servico = $servico;
    }

    public function setProfissional(Profissional $profissional)
    {
        $this->profissional = $profissional;
    }

    public function getQtdServico()
    {
        return $this->qtd_servico;
    }

    public function getServico()
    {
        return $this->servico;
    }

    public function getProfissional()
    {
        return $this->profissional;
    }
}
