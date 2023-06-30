<?php
namespace App\Dao;
use App\Dao\Dao;
use App\Models\Categorias;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;

class DaoCategorias implements Dao
{
    public function all(bool $json = true)
    {
        $itens = DB::select('select * from categorias order by id desc');
        $categorias = [];
        foreach ($itens as $item) {
            $categoria = $this->create(get_object_vars($item));
            if ($json) {
                $categoria_json = $this->getData($categoria);
                array_push($categorias, $categoria_json);
            } else {
                array_push($categorias, $categoria);
            }
        }
        return $categorias;
    }

    public function create(array $dados)
    {
        $categoria = new Categorias();
        if (isset($dados['id'])) {
            $categoria->setId($dados['id']);
            $categoria->setDataCadastro($dados['data_create'] ?? null);
            $categoria->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $categoria->setCategoria($dados['categoria']);
        return $categoria;
    }

    public function store($obj)
    {
        // $dados = $this->getData($obj);
        $categoria = $obj->getCategoria();
        DB::beginTransaction();
        try {
            // DB::table('categorias')->insert($dados);
            DB::INSERT("INSERT INTO categorias (categoria) VALUES ('$categoria')");
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
            // DB::table('categorias')->where('id', $dados['id'])->update($dados);
            $categorias = $this->create($request->all());
            $categorias->setDataAlteracao(Carbon::now());
            $categoria = $categorias->getCategoria();
            $data_alt = $categorias->getDataAlteracao();
            DB::UPDATE("UPDATE categorias  SET categoria = '$categoria' , data_alt = '$data_alt' where id = $id ");
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
            // DB::table('categorias')->delete($id);
            DB::DELETE("DELETE FROM  categorias where id = '$id'");
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
            $dados = DB::select('select * from categorias where id = ?', [$id]);
            return $dados[0];
        }
        // $dados = DB::table('categorias')->where('id', $id)->first();
        $dados = DB::select('select * from categorias where id = ?', [$id]);
        if ($dados) {
            $categorias = [];
            foreach ($dados as $item) {
                $categoria = $this->create(get_object_vars($item));
                $categoria_json = $this->getData($categoria);
                array_push($categorias, $categoria_json);
            }
            return $categorias;
        }
    }

    public function getData(Categorias $categoria)
    {
        $dados = [
            'id' => $categoria->getId(),
            'categoria' => $categoria->getCategoria(),
            'data_create' => $categoria->getDataCadastro(),
            'data_alt' => $categoria->getDataAlteracao(),
        ];

        return $dados;
    }
}
