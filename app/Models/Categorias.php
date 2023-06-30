<?php
namespace App\Models;

class Categorias extends TObject
{
    protected $table = 'categorias';
    private $categoria;

    public function __construct()
    {
        $this->categoria = '';
    }

    public function setCategoria($categoria)
    {
        $this->categoria = strtoupper($categoria);
    }

    public function getCategoria()
    {
        return $this->categoria;
    }
}
