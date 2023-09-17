<?php
namespace App\Dao;
use App\Dao\Dao;
use App\Dao\DaoServico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;
use App\Models\Servico;
use App\Models\ProfissionalServicoAgenda;

class DaoProfissionalServicoAgenda implements Dao
{
    private $daoServico;

    public function __construct()
    {
        $this->daoServico = new DaoServico();
    }
    public function all(bool $model = false)
    {
    }

    public function create(array $dados)
    {
    }

    public function store($obj)
    {
    }

    public function storeProfissionalServico($obj, $id)
    {
        
    }

    public function update(Request $request, $id)
    {
    }

    public function updateProfissionalServico($obj, $id)
    {
        
    }

    public function delete($id)
    {
       
    }

    public function findById(int $id, bool $model = false)
    {
       
    }

    // public function getData(ProfissionalServicoAgenda $profissional_servico)
    // {
       
    // }

    public function gerarProfissionalServico(array $dados)
    {
       
    }
}
