<?php
namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoEstado;
use App\Models\Cidade;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;

class DaoCidade implements Dao
{
    public function __construct()
    {
        $this->daoEstado = new DaoEstado();
    }

    public function all(bool $json = true)
    {
        // $itens = DB::table('cidades')->get();
        $itens = DB::select('select * from cidades order by id desc');
        $cidades = [];
        foreach ($itens as $item) {
            $cidade = $this->create(get_object_vars($item));
            if ($json) {
                $cidade_json = $this->getData($cidade);
                array_push($cidades, $cidade_json);
            } else {
                array_push($cidades, $cidade);
            }
        }
        return $cidades;
    }

    public function create(array $dados)
    {
        $cidade = new Cidade();
        if (isset($dados['id'])) {
            $cidade->setId($dados['id']);
            $cidade->setDataCadastro($dados['data_create'] ?? null);
            $cidade->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $cidade->setCidade($dados['cidade']);
        $cidade->setDDD($dados['ddd']);
        $estado = $this->daoEstado->findById($dados['id_estado'], false);
        $estado = $this->daoEstado->create(get_object_vars($estado));
        $cidade->setEstado($estado);
        return $cidade;
    }

    public function store($obj)
    {
        $cidade = $obj->getCidade();
        $ddd = $obj->getDDD();
        $id_estado = $obj->getEstado()->getId();
        DB::beginTransaction();
        try {
            //DB::table('estados')->insert($dados);
            DB::INSERT("INSERT INTO cidades (cidade,ddd,id_estado) VALUES ('$cidade','$ddd','$id_estado')");
            DB::commit();
            $ultimaCidade = DB::table('cidades')
                ->get()
                ->last();
            return $ultimaCidade;
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cidades = $this->create($request->all());
            // $dados = $this->getData($estados);
            $cidades->setDataAlteracao(Carbon::now());
            $cidade = $cidades->getCidade();
            $ddd = $cidades->getDDD();
            $id_estado = $cidades->getEstado()->getId();
            $data_alt = $cidades->getDataAlteracao();
            //DB::table('estados')->where('id', $dados['id'])->update($dados);
           // DB::UPDATE("UPDATE cidades  SET cidade = '$cidade' , ddd = '$ddd', id_estado = '$id_estado', data_alt = '$data_alt' where id = $id ");
            DB::update('UPDATE cidades SET cidade = ?,  ddd = ?, id_estado = ?, data_alt = ? WHERE id = ?', [$cidade,$ddd,$id_estado,$data_alt,$id]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ['error' => $e->getMessage(), 'CodigoError' => $e->getCode()];
            return $error;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            // DB::table('cidades')->where('id', $id)->delete();
            DB::DELETE("DELETE FROM  cidades where id = '$id'");
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return $error = ['Não foi possível excluir, registro já vinculado.'];
            }
            return $error = ['Não foi possível excluir o registro.'];
        }
    }

    public function findById(int $id, bool $model = false)
    {
        if (!$model) {
            // return DB::table('estados')->get()->where('id', $id)->first();
            $dados = DB::select('select * from cidades where id = ?', [$id]);
            return $dados[0];
        }
        // $dados = DB::table('estados')->where('id', $id)->first();
        $dados = DB::select('select * from cidades where id = ?', [$id]);
        if ($dados) {
            $cidades = [];
            foreach ($dados as $item) {
                $cidade = $this->create(get_object_vars($item));
                $cidade_json = $this->getData($cidade);
                array_push($cidades, $cidade_json);
            }

            return $cidades;
        }
    }

    public function getData(Cidade $cidade)
    {
        $dados = [
            'id' => $cidade->getId(),
            'cidade' => $cidade->getCidade(),
            'ddd' => $cidade->getDDD(),
            'estado' => $this->daoEstado->getData($cidade->getEstado()),
            'data_create' => $cidade->getDataCadastro(),
            'data_alt' => $cidade->getDataAlteracao(),
        ];
        return $dados;
    }
}
