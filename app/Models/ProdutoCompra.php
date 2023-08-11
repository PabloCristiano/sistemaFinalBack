<?php

namespace App\Models;

use App\Models\Produto;

class ProdutoCompra
{
    protected $table = 'produtos';
    protected $produto;
    protected int $quantidade;
    public function __construct()
    {
        $this->quantidade = 0;
        $this->produto = new Produto();
    }
    public function getProduto()
    {
        return $this->produto;
    }
    public function setProduto(Produto $produto)
    {
        $this->produto = $produto;
    }
    public function getQuantidade()
    {
        return $this->quantidade;
    }
    public function setQuantidade(int $quantidade)
    {
        $this->quantidade = $quantidade;
    }
}
