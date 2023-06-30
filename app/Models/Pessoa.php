<?php
namespace App\Models;

use App\Models\Cidade;

class Pessoa extends TObject
{
    protected $nome;
    protected $sexo;
    protected $dataNasc;
    protected $logradouro;
    protected $numero;
    protected $complemento;
    protected $bairro;
    protected $cep;
    protected $telefone;
    protected $whatsapp;
    protected $email;
    protected $cpf;
    protected $cnpj;
    protected $rg;
    protected $inscricaoEstadual;
    protected $observacoes;
    protected $cidade;

    public function __construct()
    {
        $this->nome = '';
        $this->sexo = '';
        $this->dataNasc = '';
        $this->logradouro = '';
        $this->numero = '';
        $this->complemento = '';
        $this->bairro = '';
        $this->cep = '';
        $this->telefone = '';
        $this->whatsapp = '';
        $this->email = '';
        $this->cpf = '';
        $this->cnpj = '';
        $this->rg = '';
        $this->inscricaoEstadual = '';
        $this->observacoes = '';
        $this->cidade = new Cidade();
    }
    public function getNome()
    {
        return $this->nome;
    }

    public function setNome(string $nome)
    {
        $this->nome = strtoupper($nome);
    }

    public function getSexo()
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo = null)
    {
        $this->sexo = strtoupper($sexo);
    }

    public function setDataNasc(string $dataNasc = null)
    {
        $this->dataNasc = $dataNasc;
    }

    public function getDataNasc()
    {
        return $this->dataNasc;
    }

    public function getLogradouro()
    {
        return $this->logradouro;
    }

    public function setLogradouro(string $logradouro)
    {
        $this->logradouro = strtoupper($logradouro);
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero(string $numero)
    {
        $this->numero = $numero;
    }

    public function getComplemento()
    {
        return $this->complemento;
    }

    public function setComplemento(string $complemento = null)
    {
        $this->complemento = strtoupper($complemento);
    }

    public function getBairro()
    {
        return $this->bairro;
    }

    public function setBairro(string $bairro)
    {
        $this->bairro = strtoupper($bairro);
    }

    public function getCep()
    {
        return $this->cep;
    }

    public function setCep(string $cep = null)
    {
        $this->cep = $cep;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone(string $telefone = null)
    {
        $this->telefone = $telefone;
    }

    public function getWhatsapp()
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(string $whatsapp = null)
    {
        $this->whatsapp = $whatsapp;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email = null)
    {
        $this->email = strtoupper($email);
    }

    public function getCpf()
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf)
    {
        $this->cpf = $cpf;
    }

    public function getCnpj()
    {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj)
    {
        $this->cnpj = $cnpj;
    }

    public function getRg()
    {
        return $this->rg;
    }

    public function setRg(string $rg = null)
    {
        $this->rg = $rg;
    }

    public function getInscricaoEstadual()
    {
        return $this->inscricaoEstadual;
    }

    public function setInscricaoEstadual(string $inscricaoEstadual = null)
    {
        $this->inscricaoEstadual = $inscricaoEstadual;
    }

    public function getObservacoes()
    {
        return $this->observacoes;
    }

    public function setObservacoes(string $observacoes = null)
    {
        $this->observacoes = strtoupper($observacoes);
    }

    public function getCidade()
    {
        return $this->cidade;
    }

    public function setCidade(Cidade $cidade)
    {
        $this->cidade = $cidade;
    }
}
