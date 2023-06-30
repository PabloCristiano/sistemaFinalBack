<?php
namespace App\Models;
use App\Models\Profissional;
use App\Models\Servico;

class ProfissionalServico extends TObject
{
    protected $qtd_servico;
    protected $servico;
    protected $profissional;

    public function __construct()
    {
        $this->qtd_servico = 0;
        $this->servico = new Servico();
        $this->profissional = new Profissional();
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
