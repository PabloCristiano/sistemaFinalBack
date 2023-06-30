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

class DaoProfissionalServico implements Dao
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
        $qtd = count($obj);
        try {
            DB::beginTransaction();
            for ($i = 0; $i < $qtd; $i++) {
                $qtd_servico = $i;
                $id_servico = $obj[$i]['id'];
                $id_profissional = $id;
                DB::SELECT("INSERT INTO profissional_servico (qtd_servico,id_servico,id_profissional) VALUES ($qtd_servico,$id_servico,$id_profissional)");
                $sql = true;
            }
            if ($sql) {
                DB::commit();
                return $sql;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update(Request $request, $id)
    {
    }

    public function updateProfissionalServico($obj, $id)
    {
        
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            DB::DELETE("DELETE FROM  profissional_servico WHERE id_profissional = '$id'");
            DB::commit();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findById(int $id, bool $model = false)
    {
        $dados = DB::select(
            'select s.id as id_servico from profissional_servico
             inner join servicos as s on  id_servico = s.id
             where id_profissional = ?',
            [$id],
        );
        if ($dados) {
            return $this->gerarProfissionalServico($dados);
        }
        return $error = ['Não foi possível pesquisar!'];
    }

    public function getData(ProfissionalServico $profissional_servico)
    {
        $dados = [
            'qtd_servico' => $profissional_servico->getQtdServico(),
            'servico' => $this->daoServico->getData($profissional_servico->getServico()),
            'data_create' => $profissional_servico->getDataCadastro(),
            'data_alt' => $profissional_servico->getDataAlteracao(),
        ];

        return $dados;
    }

    public function gerarProfissionalServico(array $dados)
    {
        $profissionalServico = [];
        foreach ($dados as $servico) {
            $p_servico = new Servico();
            $p_servico = $this->daoServico->findById($servico->id_servico, true);
            $prof_servico = $this->daoServico->create($p_servico[0]);
            $p_servico = $this->daoServico->getData($prof_servico);
            array_push($profissionalServico, $p_servico);
        }
        return $profissionalServico;
    }
}
