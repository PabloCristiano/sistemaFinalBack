<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoFormasPagamento;
use Illuminate\Support\Facades\DB;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DaoParcela implements Dao
{

    private  $daoFormasPagamento;

    public function __construct()
    {
        $this->daoFormasPagamento = new DaoFormasPagamento();
    }

    public function all(bool $json = true)
    {
    }

    public function create(array $dados)
    {

        $parcela = new Parcela();
        if (isset($dados["id"]))
            $parcela->setId($dados["id"]);
        $parcela->setDataCadastro($dados["data_create"] ?? null);
        $parcela->setDataAlteracao($dados["data_alt"] ?? null);
        $parcela->setParcela($dados["parcela"]);
        $parcela->setPrazo($dados["prazo"]);
        $parcela->setPorcentagem((float) $dados["porcentagem"]);
        $fP = $this->daoFormasPagamento->findById($dados["idformapg"], false);
        $formaPagamento = $this->daoFormasPagamento->create(get_object_vars($fP));
        $parcela->setFormasPagamento($formaPagamento);
        return $parcela;
    }

    public function store($parcelas)
    {
        $parcela = $parcelas["parcela"];
        $prazo  =  $parcelas["prazo"];
        $porcentagem  =  $parcelas["porcentagem"];
        $idformapg  =  $parcelas["idformapg"];
        $idcondpg  =  $parcelas["idcondpg"];
        DB::beginTransaction();
        try {
            //$sql = DB::table('parcelas')->insert($parcelas);
            $sql = DB::SELECT("INSERT INTO parcelas (parcela,prazo,porcentagem, idformapg, idcondpg) VALUES ($parcela,$prazo, $porcentagem,$idformapg,$idcondpg)");
            DB::commit();
            return $sql;
        } catch (QueryException $e) {
            $mensagem = $e->getMessage(); // Mensagem de erro
            $codigo = $e->getCode(); // Código do erro
            $consulta = $e->getSql(); // Consulta SQL que causou o erro
            $bindings = $e->getBindings(); // Valores passados como bind para a consulta
            DB::rollBack();
            return [$mensagem,$codigo,$consulta,$bindings];
        }
    }

    public function update(Request $request, $id)
    {
    }

    public function updateParcela(array $par, $qtd, $id)
    {
        DB::beginTransaction();
        try {
            for ($i = 0; $i < $qtd; $i++) {
                $dadosParcela = [
                    'parcela'                => $par[$i]["parcela"],
                    'prazo'                  => $par[$i]["prazo"],
                    'porcentagem'            => $par[$i]["porcentagem"],
                    'idformapg'             => $par[$i]["idformapg"],
                    'idcondpg'              => $id,
                ];
                DB::table('parcelas')->where('parcelas.idcondpg', $id)
                    ->where('parcelas.parcela', [$dadosParcela["parcela"]])->update($dadosParcela);
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function delete($id)
    {
    }

    public function deleteParcela(array $par, $qtd, $id)
    {
        DB::beginTransaction();
        try {
            for ($i = 0; $i < $qtd; $i++) {
                $dadosParcela = [
                    'parcela'                => $par[$i]["parcela"],
                    'prazo'                  => $par[$i]["prazo"],
                    'porcentagem'            => $par[$i]["porcentagem"],
                    'idformapg'             => $par[$i]["idformapg"],
                    'idcondpg'              => $id,
                ];
                DB::table('parcelas')->where('parcelas.idcondpg', $id)
                    ->where('parcelas.parcela', [$dadosParcela["parcela"]])->delete();
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }



    public function findById(int $id, bool $model = false)
    {
    }

    //VERIFICAR NA HORA DE SALVAR NO BANCO DE DADOS !!!!!
    public function getData(Parcela $parcela)
    {
        // dd($parcela, "getData(Parcela parcela)");
        $dados = [
            'parcela'            =>  $parcela->getParcela(),
            'prazo'              =>  $parcela->getPrazo(),
            'porcentagem'        =>  $parcela->getPorcentagem(),
            'formaPagamento'     =>  $this->daoFormasPagamento->findById($parcela->getFormasPagamento()->getId(), true),
            'data_create'        =>  $parcela->getDataCadastro(),
            'data_alt'           =>  $parcela->getDataAlteracao(),
        ];

        return $dados;
    }

    public function gerarParcelas(array $parcelas)
    {
        $par = array();
        $qtd = count($parcelas);
        for ($i = 0; $i < $qtd; $i++) {

            $dadosParcela = [
                "parcela"            => $parcelas[$i]["parcela"],
                "prazo"              => $parcelas[$i]["prazo"],
                "porcentagem"        => $parcelas[$i]["porcentagem"],
                "id_formapg"         => $parcelas[$i]["idformapg"],
                "data_create"        => $parcelas[$i]["data_create"],
                "data_alt"           => $parcelas[$i]["data_alt"],
            ];
            array_push($par, $dadosParcela);
        }
        return $par;
    }

    public function getDataGerarParcelas(array $obj)
    {

        $parcelas = [];
        $qtd = count($obj);
        // dd($qtd,"getDataGerarParcelas");
        for ($i = 0; $i < $qtd; $i++) {
            array_push($parcelas, $this->getData($obj[$i]));
        }
        return $parcelas;
    }
}
