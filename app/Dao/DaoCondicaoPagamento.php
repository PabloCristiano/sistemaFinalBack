<?php

namespace App\Http\Dao;

use App\Dao\Dao;
use App\Dao\DaoParcela;
use App\Models\CondicaoPagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaoCondicaoPagamento implements Dao
{
    private DaoParcela $daoParcela;

    public function __construct()
    {
        $this->daoParcela = new DaoParcela();
    }

    public function all(bool $model = false)
    {
        if (!$model) {
            $itens = DB::select('select * from condicao_pg');
            $listacondicao = array();
            foreach ($itens as $item) {
                array_push($listacondicao, $item);
            }
            return $listacondicao;
        }

        $itens = DB::select('select * from condicao_pg');
        $listaCondicoes = array();
        foreach ($itens as $item) {
            $listacondicao = $this->listarCondição(get_object_vars($item));
            array_push($listaCondicoes, $listacondicao);
        }
        return $listaCondicoes;
    }

    //criar condição parcela
    public function create(array $dados)
    {
        $condicaoPagamento = new CondicaoPagamento();

        if (isset($dados["id"])) {
            $condicaoPagamento->setId(($dados["id"]));
            $condicaoPagamento->setDataCadastro($dados["data_create"] ?? null);
            $condicaoPagamento->setDataAlteracao($dados["data_alt"] ?? null);
        }
        $condicaoPagamento->setCondicaoPagamento($dados["condicao_pagamento"]);
        $condicaoPagamento->setJuros((float)$dados["juros"]);
        $condicaoPagamento->setMulta((float)$dados["multa"]);
        $condicaoPagamento->setDesconto((float)$dados["desconto"]);
        $totalParcelas =   $dados["qtd_parcela"];
        $condicaoPagamento->setTotalParcelas($totalParcelas);
        if (isset($dados["parcelas"])) {
            $parcelas = $this->gerarParcelas($dados["parcelas"], $condicaoPagamento);
            $condicaoPagamento->setParcelas($parcelas);
        }
        return $condicaoPagamento;
    }

    function listarCondição(array $dados)
    {

        $condicaoPagamento = new CondicaoPagamento();
        $condicaoPagamento->setId(($dados["id"]));
        $condicaoPagamento->setDataCadastro($dados["data_create"] ?? null);
        $condicaoPagamento->setDataAlteracao($dados["data_alt"] ?? null);
        $condicaoPagamento->setCondicaoPagamento($dados["condicao_pagamento"]);
        $condicaoPagamento->setJuros((float)$dados["juros"]);
        $condicaoPagamento->setMulta((float)$dados["multa"]);
        $condicaoPagamento->setDesconto((float)$dados["desconto"]);
        $totalParcelas =   $dados["qtd_parcela"];
        $condicaoPagamento->setTotalParcelas($totalParcelas);
        $parcelas = array();
        $parcelas = $this->buscarParcelas($condicaoPagamento->getId());
        $condicaoPagamento->setParcelas($parcelas);
        return $condicaoPagamento;
    }

    public function gerarParcelas($dadosParcelas, $condicaoPagamento)
    {
        $par = array();
        $qtd = $condicaoPagamento->getTotalParcelas();
        for ($i = 0; $i < $qtd; $i++) {
            $dadosParcela = [
                "parcela"            => $dadosParcelas[$i]["parcela"],
                "prazo"              => $dadosParcelas[$i]["prazo"],
                "porcentagem"        => $dadosParcelas[$i]["porcentagem"],
                "idformapg"          => $dadosParcelas[$i]["idformapg"],
            ];
            array_push($par, $dadosParcela);
        }
        return $par;
    }

    public function salvarParcelas(array $parcelas, $idCondicaoPagamento)
    {
        $qtd = count($parcelas);
        DB::beginTransaction();
        try {
            for ($i = 0; $i < $qtd; $i++) {
                $dadosParcela = [
                    'parcela'                => $parcelas[$i]["parcela"],
                    'prazo'                  => $parcelas[$i]["prazo"],
                    'porcentagem'            => $parcelas[$i]["porcentagem"],
                    'idformapg'             => $parcelas[$i]["idformapg"],
                    'idcondpg'              => $idCondicaoPagamento,
                ];
                DB::table('parcelas')->insert($dadosParcela);
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function buscarParcelas($idCondicaoPagamento)
    {
        $dados = DB::select('select parcela, prazo, porcentagem,idformapg, data_create, data_alt from 
                             parcelas where idcondpg = ?', [$idCondicaoPagamento]);
        $parcelas = array();
        foreach ($dados as $dadosParcela) {
            $parcela = $this->daoParcela->create(get_object_vars($dadosParcela));
            array_push($parcelas, $parcela);
        }
        return $parcelas;
    }
    //parcelas condição de Pagamento Produto
    public function CondicaoPagamentoProduto($idCondicaoPagamento)
    {
        $dados = DB::select('select p.parcela, p.prazo, p.porcentagem,p.idformapg, p.data_create, p.data_alt, f.forma_pg from 
        parcelas as p join forma_pg as f on p.idformapg = f.id where idcondpg = ?', [$idCondicaoPagamento]);
        $CondicaoPagamentoParcela = array();
        foreach ($dados as $dadosParcela) {
            array_push($CondicaoPagamentoParcela, $dadosParcela);
        }
        return $CondicaoPagamentoParcela;
    }

    public function store($condicaoPagamento)
    {
        DB::beginTransaction();
        try {
            $dados = $this->getData($condicaoPagamento);
            DB::table('condicao_pg')->insert($dados);
            /*
            if($condicaoPagamento->getTotalParcelas() > 0){
             $idCondicaoPagamento = DB::getPdo()->lastInsertId();
             $teste = $this->salvarParcelas($condicaoPagamento->getParcelas(), $idCondicaoPagamento); 
            }
         */
            $idCondicaoPagamento = DB::getPdo()->lastInsertId();
            $teste = $this->salvarParcelas($condicaoPagamento->getParcelas(), $idCondicaoPagamento);

            DB::commit();
            return $teste;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function update(Request $request, $id)
    {

        $itens = json_decode($request->parcelas_array[0], true);
        $dadosParcelas = array();
        foreach ($itens as $item) {
            array_push($dadosParcelas, $item);
        }
        $par = array();
        $qtd = $request->total_parcelas;
        $id_condicao = $request->id;
        for ($i = 0; $i < $qtd; $i++) {
            $dadosParcela = [
                "parcela"            => $dadosParcelas[$i]["parcela"],
                "prazo"              => $dadosParcelas[$i]["prazo"],
                "porcentagem"        => $dadosParcelas[$i]["porcentagem"],
                "idformapg"          => $dadosParcelas[$i]["idformapg"],
            ];
            array_push($par, $dadosParcela);
        }
        $dadosCondicao = [
            'id' => $request->id,
            'condicao_pagamento' => $request->condicao_pagamento,
            'juros' => $request->juros,
            'multa' => $request->multa,
            'desconto' => $request->desconto,
            'data_alt'           => Carbon::now(),
            'qtd_parcela' => $request->total_parcelas,
        ];
        DB::beginTransaction();
        try {
            $parcela = $this->daoParcela->updateParcela($par, $qtd, $id_condicao);
            DB::table('condicao_pg')->where('id', $id_condicao)->update($dadosCondicao);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return false;
        }
    }
    public function delete($id)
    {
    }

    public function deleteCondicao(Request $request)
    {
        $itens = json_decode($request->parcelas_array[0], true);
        $dadosParcelas = array();
        foreach ($itens as $item) {
            array_push($dadosParcelas, $item);
        }
        $par = array();
        $qtd = $request->total_parcelas;
        $id_condicao = $request->id;
        for ($i = 0; $i < $qtd; $i++) {
            $dadosParcela = [
                "parcela"            => $dadosParcelas[$i]["parcela"],
                "prazo"              => $dadosParcelas[$i]["prazo"],
                "porcentagem"        => $dadosParcelas[$i]["porcentagem"],
                "idformapg"          => $dadosParcelas[$i]["idformapg"],
            ];
            array_push($par, $dadosParcela);
        }
        $dadosCondicao = [
            'id' => $request->id,
            'condicao_pagamento' => $request->condicao_pagamento,
            'juros' => $request->juros,
            'multa' => $request->multa,
            'desconto' => $request->desconto,
            'data_alt'           => Carbon::now(),
            'qtd_parcela' => $request->total_parcelas,
        ];

        // dd($dadosCondicao,$par,$id_condicao);
        DB::beginTransaction();
        try {
            $parcela = $this->daoParcela->deleteParcela($par, $qtd, $id_condicao);
            DB::table('condicao_pg')->delete($id_condicao);
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return false;
        }
    }

    public function findById(int $id, bool $model = false)
    {
        if (!$model)
            return DB::table('condicao_pg')->get()->where('id', $id)->first();

        $dados = DB::table('condicao_pg')->where('id', $id)->first();

        if ($dados)
            $dados = $this->create(get_object_vars($dados));

        return $dados;
    }

    public function getdata(CondicaoPagamento $condicaoPagamento)
    {
        $dados = [
            "id"                 => $condicaoPagamento->getid(),
            "condicao_pagamento" => $condicaoPagamento->getCondicaoPagamento(),
            "juros"              =>  $condicaoPagamento->getJuros(),
            "multa"              =>  $condicaoPagamento->getMulta(),
            "desconto"           =>  $condicaoPagamento->getDesconto(),
            'qtd_parcela'        =>  $condicaoPagamento->getTotalParcelas(),
            'data_create'        =>  $condicaoPagamento->getDataAlteracao(),
            'data_alt'           => Carbon::now(),
        ];

        return $dados;
    }

    public function buscar(bool $model = false)
    {
        $itens = DB::select(
            'SELECT * FROM condicao_pg ORDER BY id DESC LIMIT 1'
        );
        $condicaopagamentos = array();
        foreach ($itens as $item) {
            $condicaopagamento = $this->create(get_object_vars($item));
            array_push($condicaopagamentos, $condicaopagamento);
        }

        return $condicaopagamentos;
    }

    public function listCondicaoPagamento($id)
    {
        $dados = DB::select('select * from condicao_pg where id = ?', [$id]);
        $listcondicoes = array();
        foreach ($dados as $item) {
            $listacondicao = $this->listarCondição(get_object_vars($item));
            array_push($listcondicoes, $listacondicao);
        }
        return $listcondicoes;
    }

    public function listaParcela($id)
    {
        $dados = DB::select('select p.parcela, p.prazo, p.porcentagem, p.idformapg, f.forma_pg, p.data_create, p.data_alt 
        from parcelas p
        join forma_pg f on f.id = p.idformapg
        where p.idcondpg = ?', [$id]);
        $parcelas = array();
        foreach ($dados as $dadosParcela) {
            array_push($parcelas, $dadosParcela);
        }
        return $parcelas;
    }

    public function showCondicaoPagamento()
    {
        $item = DB::select('select * from condicao_pg');
        $condicao_pg = array();
        foreach ($item as $itens) {
            array_push($condicao_pg, $itens);
        }
        return $condicao_pg;
    }
}
