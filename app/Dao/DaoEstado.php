<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoPais;
use App\Models\Estado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;

class DaoEstado implements Dao
{
    private $daoPais;
    public function __construct()
    {
        $this->daoPais = new DaoPais();
    }

    public function all(bool $json = true)
    {
        // $itens = DB::table('estados')->get();
        $itens = DB::select('select * from estados order by id desc');
        $estados = [];
        foreach ($itens as $item) {
            $estado = $this->create(get_object_vars($item));
            if ($json) {
                $estado_json = $this->getData($estado);
                array_push($estados, $estado_json);
            } else {
                array_push($estados, $estado);
            }
        }
        return $estados;
    }

    public function create(array $dados)
    {
        $estado = new Estado();
        if (isset($dados['id'])) {
            $estado->setId($dados['id']);
            $estado->setDataCadastro($dados['data_create'] ?? null);
            $estado->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $estado->setEstado($dados['estado']);
        $estado->setUf($dados['uf']);
        $pais = $this->daoPais->findById($dados['id_pais'], false);
        $pais =  $this->daoPais->create(get_object_vars($pais));
        $estado->setPais($pais);
        return $estado;
    }

    public function store($obj)
    {
        $estado = $obj->getEstado();
        $uf = $obj->getUF();
        $id_pais = $obj->getPais()->getId();
        DB::beginTransaction();
        try {
            //DB::table('estados')->insert($dados);
            DB::INSERT("INSERT INTO estados (estado,uf,id_pais) VALUES ('$estado','$uf','$id_pais')");
            DB::commit();
            $ultimoestado = DB::table('estados')
                ->get()
                ->last();
            return $ultimoestado;
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
            $estados = $this->create($request->all());
            // $dados = $this->getData($estados);
            $estados->setDataAlteracao(Carbon::now());
            $estado = $estados->getEstado();
            $uf = $estados->getUF();
            $id_pais = $estados->getPais()->getId();
            $data_alt = $estados->getDataAlteracao();
            //DB::table('estados')->where('id', $dados['id'])->update($dados);
            DB::UPDATE("UPDATE estados  SET estado = '$estado' , uf = '$uf', id_pais = '$id_pais', data_alt = '$data_alt' where id = $id ");
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
            // DB::table('estados')->where('id', $id)->delete();
            DB::DELETE("DELETE FROM  estados where id = '$id'");
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
            $dados = DB::select('select * from estados where id = ?', [$id]);
            return $dados[0];
        }
        // $dados = DB::table('estados')->where('id', $id)->first();
        $dados = DB::select('select * from estados where id = ?', [$id]);
        if ($dados) {
            $estados = [];
            foreach ($dados as $item) {
                $estado = $this->create(get_object_vars($item));
                $estado_json = $this->getData($estado);
                array_push($estados, $estado_json);
            }

            return $estados;
        }
    }

    public function getData(Estado $estado)
    {
        $dados = [
            'id' => $estado->getId(),
            'estado' => $estado->getEstado(),
            'uf' => $estado->getUF(),
            'pais' => $this->daoPais->getData($estado->getPais()),
            'data_create' => $estado->getDataCadastro(),
            'data_alt' => $estado->getDataAlteracao(),
        ];
        return $dados;
    }
}
