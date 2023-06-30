<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pais;

class Estado extends TObject
{
    protected $table = 'estados';
    protected $estado;
    protected $uf;
    protected Pais $pais;

    public function __construct()
    {
        $this->estado = '';
        $this->uf = '';
        $this->pais = new Pais();
    }

    public function setEstado(string $estado)
    {
        $this->estado = strtoupper($estado);
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setUF(string $uf)
    {
        $this->uf = strtoupper($uf);
    }

    public function getUF()
    {
        return $this->uf;
    }

    public function setPais(Pais $pais)
    {
        $this->pais = $pais;
    }

    public function getPais()
    {
        return $this->pais;
    }
}
