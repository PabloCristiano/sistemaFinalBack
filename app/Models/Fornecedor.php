<?php
namespace App\Models;

class Fornecedor extends Pessoa
{
    protected $table = 'fornecedores';
    protected $tipo_pessoa;
    protected $razaoSocial;
    protected $nomeFantasia;
    protected $pagSite;
    protected $contato;
    protected $condicaoPagamento;
    protected $limiteCredito;

    public function __construct()
    {
        $this->tipo_pessoa = '';
        $this->razaoSocial = '';
        $this->nomeFantasia = '';
        $this->pagSite = '';
        $this->contato = '';
       // $this->condicaoPagamento = new CondicaoPagamento();
        $this->limiteCredito = 0;
    }

    public function getTipoPessoa()
    {
        return $this->tipo_pessoa;
    }

    public function setTipoPessoa(string $tipo_pessoa)
    {
        $this->tipo_pessoa = strtoupper($tipo_pessoa);
    }

    public function getRazaoSocial()
    {
        return $this->razaoSocial;
    }

    public function setRazaoSocial(string $razaoSocial)
    {
        $this->razaoSocial = strtoupper($razaoSocial);
    }

    public function getNomeFantasia()
    {
        return $this->nomeFantasia;
    }

    public function setNomeFantasia(string $nomeFantasia = null)
    {
        $this->nomeFantasia = strtoupper($nomeFantasia);
    }

    public function getPagSite()
    {
        return $this->pagSite;
    }

    public function setPagSite(string $pagSite = null)
    {
        $this->pagSite = strtoupper($pagSite);
    }

    public function getContato()
    {
        return $this->contato;
    }

    public function setContato(string $contato = null)
    {
        $this->contato = strtoupper($contato);
    }

    public function getCondicaoPagamento()
    {
        return $this->condicaoPagamento;
    }

    // public function setCondicaoPagamento()
    // {
    //     $this->condicaoPagamento = $condicaoPagamento;
    // }

    public function getLimiteCredito()
    {
        return $this->limiteCredito;
    }

    public function setLimiteCredito(float $limiteCredito = null)
    {
        $this->limiteCredito = $limiteCredito;
    }
}
