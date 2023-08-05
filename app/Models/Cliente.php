<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;

class Cliente extends Pessoa
{
    protected $table = 'clientes';
    protected $apelido;
    protected $senha_criptografada;
    protected $confSenha_criptografada;
    protected $condicaoPagamento;
    protected $confSenha;

    public function __construct()
    {
        $this->apelido = '';
        $this->senha_criptografada = '';
        $this->confSenha = '';
        $this->condicaoPagamento = new CondicaoPagamento();
    }

    public function getApelido()
    {
        return $this->apelido;
    }

    public function setApelido(string $apelido)
    {
        $this->apelido = strtoupper($apelido);
    }

    public function getSenha()
    {
        return $this->senha_criptografada;
    }

    public function setSenha(string $senha)
    {
        $this->senha_criptografada = Hash::make($senha);
    }

    public function getConfSenha()
    {
        return $this->confSenha_criptografada;
    }

    public function setConfSenha(string $confSenha)
    {
        $this->confSenha_criptografada = Hash::make($confSenha);
    }

    public function getCondicaoPagamento()
    {
        return $this->condicaoPagamento;
    }

    public function setCondicaoPagamento(CondicaoPagamento $condicaoPagamento)
    {
        $this->condicaoPagamento = $condicaoPagamento;
    }

    public function verificaSenha($senha)
    {
        $password = $senha;
        $hashedPassword = Hash::make($password);
        if (Hash::check($password, $hashedPassword)) {
            dd('A senha está correta!');
        } else {
            dd('A senha está incorreta!');
        }
        //return Hash::check($senha, '$2y$10$wN7xQqitkl6JqiYa8BtpVuo4EKGWazQDkBPIYOznx3q');
    }

    //  $password = $cliente->getSenha();
    //  $hashedPassword = Hash::make($password);
    // if (Hash::check($password, $hashedPassword)) {
    //     dd('A senha está correta!');
    // } else {
    //     dd('A senha está incorreta!');
    // }
}
