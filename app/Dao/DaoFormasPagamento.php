<?php
namespace App\Dao;

use App\Dao\Dao;
use App\Models\FormasPagamento;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaoFormasPagamento implements Dao
{
    public function all(bool $json = true)
    {
        $itens = DB::select('select * from forma_pg order by id desc');
        $formaspg = [];
        foreach ($itens as $item) {
            $formapg = $this->create(get_object_vars($item));
            if ($json) {
                $formaspagemento_json = $this->getData($formapg);
                array_push($formaspg, $formaspagemento_json);
            } else {
                array_push($formaspg, $formapg);
            }
        }
        return $formaspg;
    }

    public function create(array $dados)
    {
        $formapg = new FormasPagamento();
        if (isset($dados['id'])) {
            $formapg->setId($dados['id']);
            $formapg->setDataCadastro($dados['data_create'] ?? null);
            $formapg->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $formapg->setFormapg($dados['forma_pg']);
        return $formapg;
    }

    public function store($obj)
    {
        // $dados = $this->getData($obj);
        $formapagamento = $obj->getFormapg();
        DB::beginTransaction();
        try {
            // DB::table('forma_pg')->insert($dados);
            DB::INSERT("INSERT INTO forma_pg (forma_pg) VALUES ('$formapagamento')");
            DB::commit();
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
            // DB::table('forma_pg')->where('id', $dados['id'])->update($dados);
            $formapg = $this->create($request->all());
            $formapg->setDataAlteracao(Carbon::now());
            $formapagamento = $formapg->getFormapg();
            $data_alt = $formapg->getDataAlteracao();
            DB::UPDATE("UPDATE forma_pg  SET forma_pg = '$formapagamento' , data_alt = '$data_alt' where id = $id ");
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            $error = ['error' => $e->getMessage(), 'CodigoError' => $e->getCode()];
            return $error;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            // DB::table('forma_pg')->delete($id);
            DB::DELETE("DELETE FROM  forma_pg where id = '$id'");
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
            // return DB::table('forma_pg')->get()->where('id', $id)->first();
            $dados = DB::select('select * from forma_pg where id = ?', [$id]);
            return $dados[0];
        }

        // $dados = DB::table('forma_pg')->where('id', $id)->first();
        $dados = DB::select('select * from forma_pg where id = ?', [$id]);
        if ($dados) {
            $formaspagamentos = [];
            foreach ($dados as $item) {
                $formapagamento = $this->create(get_object_vars($item));
                $formapagamento_json = $this->getData($formapagamento);
                array_push($formaspagamentos, $formapagamento_json);
            }
            return $formaspagamentos;
        }
    }

    public function getData(FormasPagamento $formapg)
    {
        $dados = [
            'id' => $formapg->getid(),
            'forma_pg' => $formapg->getFormapg(),
            'data_create' => $formapg->getDataCadastro(),
            'data_alt' => $formapg->getDataAlteracao(),
        ];

        return $dados;
    }
}
