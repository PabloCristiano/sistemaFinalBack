<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Dao\DaoFormasPagamento;
use Illuminate\Support\Facades\DB;
use App\Models\Parcela;
use Illuminate\Http\Request;

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
        $formaPagamento = $this->daoFormasPagamento->findById($dados["idformapg"], true);
        $parcela->setFormasPagamento($formaPagamento);

        return $parcela;
    }

    public function store($parcela)
    {
        DB::beginTransaction();
        try {
            $dados = $this->getData($parcela);
            DB::table('parcelas')->insert($dados);
            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function update(Request $request,$id)
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


    public function getData(Parcela $parcela)
    {

        $dados = [
            'parcela'            =>  $parcela->getParcela(),
            'prazo'              =>  $parcela->getPrazo(),
            'porcentagem'        =>  $parcela->getPorcentagem(),
            'idformapg'          =>  $parcela->getFormasPagamento()->getId(),
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
}
