<?php

namespace App\Models;

use App\Models\Profissional;
use App\Models\Servico;


class Servico_Profissional extends TObject
{
    protected $table = 'servicos_profissionais';
    protected $profissional;
    protected $servico;

    public function __construct()
    {
        $this->profissional = new Profissional;
        $this->servico = new Servico;
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
}
