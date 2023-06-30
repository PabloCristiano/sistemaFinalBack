<?php
namespace App\Models;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Dao\DaoProfissionalServico;

class Profissional extends Pessoa
{
    protected $table = 'profissionais';
    protected $apelido;
    protected $senha;
    protected $tipoProf;
    protected $comissao;
    protected $qtd_servico;
    protected $servico;
    protected $daoProfissionalServico;

    public function __construct()
    {
        $this->apelido = '';
        $this->senha = '';
        $this->tipoProf = '';
        $this->comissao = '';
        $this->qtd_servico = 0;
        $this->servico = [];
        $this->daoProfissionalServico = new DaoProfissionalServico;
    }

    public function setApelido(string $apelido)
    {
        $this->apelido = strtoupper($apelido);
    }

    public function setSenha(string $senha)
    {
        $this->senha = Hash::make($senha);
    }

    public function setTipoProf(string $tipoProf = null)
    {
        $this->tipoProf = strtoupper($tipoProf);
    }

    public function setComissao(float $comissao)
    {
        $this->comissao = $comissao;
    }

    public function setQtdServico(int $qtd_servico)
    {
        $this->qtd_servico = $qtd_servico;
    }

    public function setServico(int $id)
    {
        $this->servico = $this->daoProfissionalServico->findById($id);
    }

    public function getApelido()
    {
        return $this->apelido;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function getTipoProf()
    {
        return $this->tipoProf;
    }

    public function getComissao()
    {
        return $this->comissao;
    }

    public function getQtdServico()
    {
        return $this->qtd_servico;
    }

    public function getServico()
    {
        return $this->servico;
    }
}
