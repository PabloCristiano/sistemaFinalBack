<?php
namespace App\Models;
use App\Models\Estado;

class Cidade extends TObject
{
    protected $table = 'cidades';
    protected $cidade;
    protected $ddd;
    protected Estado $estado;

    public function __construct()
    {
        $this->cidade = '';
        $this->ddd = '';
        $this->estado = new Estado();
    }

    // SETTERS
    public function setCidade($cidade)
    {
        $this->cidade = strtoupper($cidade);
    }

    public function setDDD($ddd)
    {
        $this->ddd = strtoupper($ddd);
    }

    public function setEstado(Estado $estado)
    {
        $this->estado = $estado;
    }

    // GETTERS
    public function getCidade()
    {
        return $this->cidade;
    }

    public function getDDD()
    {
        return $this->ddd;
    }

    public function getEstado()
    {
        return $this->estado;
    }
}
