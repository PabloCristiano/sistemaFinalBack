<?php

namespace App\Dao;
use App\Dao\Dao;
use App\Models\Pais;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaoPais implements Dao
{
    public function all(bool $json = true)
    {
        //$itens = DB::table('paises')->get();
        $itens = DB::select('select * from paises order by id desc');
        $paises = [];
        foreach ($itens as $item) {
            $pais = $this->create(get_object_vars($item));
            if ($json) {
                $pais_json = $this->getData($pais);
                array_push($paises, $pais_json);
            } else {
                array_push($paises, $pais);
            }
        }
        return $paises;
    }

    public function create(array $dados)
    {
        $pais = new Pais();
        if (isset($dados['id'])) {
            $pais->setId($dados['id']);
            $pais->setDataCadastro($dados['data_create'] ?? null);
            $pais->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $pais->setPais($dados['pais']);
        $pais->setSigla($dados['sigla']);
        $pais->setDDI($dados['ddi']);
        return $pais;
    }
    public function store($obj)
    {
        $pais = $obj->getPais();
        $sigla = $obj->getSigla();
        $ddi = $obj->getDDI();
        DB::beginTransaction();
        try {
            //DB::table('paises')->insert($dados);
            DB::INSERT("INSERT INTO paises (pais,sigla,ddi) VALUES ('$pais','$sigla','$ddi')");
            DB::commit();
            $ultimopais = DB::table('paises')->get()->last();
            return $ultimopais;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    public function update(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $paises = $this->create($request->all());
            // $dados = $this->getData($paises);
            $paises->setDataAlteracao(Carbon::now());
            $pais     =  $paises->getPais();
            $sigla    =  $paises->getSigla();
            $ddi      =  $paises->getDDI();
            $data_alt =  $paises->getDataAlteracao();
            //DB::table('paises')->where('id', $dados['id'])->update($dados);
            DB::UPDATE("UPDATE paises  SET pais = '$pais' , sigla = '$sigla', ddi = '$ddi', data_alt = '$data_alt' where id = $id ");
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
            // DB::table('paises')->where('id', $id)->delete();
            DB::DELETE("DELETE FROM  paises where id = '$id'");
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
            // return DB::table('paises')->get()->where('id', $id)->first();
            $dados = DB::select('select * from paises where id = ?',[$id]);
            return $dados[0];
        }
        // $dados = DB::table('paises')->where('id', $id)->first();
        $dados = DB::select('select * from paises where id = ?',[$id]);
        if ($dados) {
            return $this->create(get_object_vars($dados[0]));
        }
        return $dados;
    }
    public function getData(Pais $pais)
    {
        $dados = [
            'id' => $pais->getid(),
            'pais' => $pais->getPais(),
            'sigla' => $pais->getSigla(),
            'ddi' => $pais->getDDI(),
            'data_create' => $pais->getDataCadastro(),
            'data_alt' => $pais->getDataAlteracao(),
        ];
        return $dados;
    }
}
