<?php

namespace App\Models;
use Illuminate\Support\Facades\Crypt;



class Pais extends TObject
{
    protected $table = 'paises';
    protected $pais;
    protected $sigla;
    protected $ddi;

    public function __construct(){
        $this->pais = '';
        $this->sigla = '';
        $this->ddi = '';
    }

    public function setPais(string $pais){
        $this->pais = strtoupper($pais);
    }

    public function getPais(){
        return $this->pais;
    }

    public function setSigla(string $sigla){
        $this->sigla = strtoupper($sigla);
    }

    public function getSigla(){
        return $this->sigla;
    }

    public function setDDI(string $ddi){
        $this->ddi = strtoupper($ddi);
    }

    public function getDDI(){
        return $this->ddi;
    }
}
