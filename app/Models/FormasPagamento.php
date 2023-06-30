<?php
namespace App\Models;

class FormasPagamento extends TObject
{
    protected $table = 'forma_pg';
    protected $forma_pg;

    public function __construct()
    {
        $this->forma_pg = '';
    }

    public function getFormapg()
    {
        return $this->forma_pg;
    }

    public function setFormapg(string $foma_pg)
    {
        $this->forma_pg = strtoupper($foma_pg);
    }
}
