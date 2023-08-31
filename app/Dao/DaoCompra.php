<?php

namespace App\Dao;

use App\Dao\Dao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Compra;
use App\Dao\DaoFornecedor;
use App\Dao\DaoCompraProduto;
use App\Dao\DaoCondicaoPagamento;
use App\Dao\DaoProfissional;
use App\Dao\DaoProduto;

class DaoCompra implements Dao
{
    protected Compra $compra;
    protected DaoFornecedor $daoFornecedor;
    protected DaoCompraProduto $daoCompraProduto;
    protected DaoCondicaoPagamento $daoCondicaoPagamento;
    protected DaoProfissional $daoProfissional;
    protected DaoProduto $daoProduto;


    public function __construct()
    {
        $this->daoFornecedor = new DaoFornecedor();
        $this->daoCompraProduto = new DaoCompraProduto();
        $this->daoCondicaoPagamento = new DaoCondicaoPagamento();
        $this->daoProfissional = new DaoProfissional();
        $this->daoProduto = new DaoProduto();
    }

    public function all(bool $json = false)
    {
        $itens = DB::select('select * from compra order by data_create desc');
        $compras = [];
        foreach ($itens as $item) {
            $compra = $this->create(get_object_vars($item));
            if ($json) {
                $compra_json = $this->getData($compra);
                array_push($compras, $compra_json);
            } else {
                array_push($compras, $compra);
            }
        }
        return $compras;
    }

    public function create(array $dados)
    {

        $compra = new Compra();

        // auth('api')->user();
        $profissional = auth('api')->user(); // resgata o usuário logado e autenticado 

        if (isset($dados["data_create"]) && isset($dados["data_alt"])) {
            $compra->setStatus($dados["status"]);
            $compra->setDataCadastro($dados["data_create"]);
            $compra->setDataAlteracao($dados["data_alt"]);
            $compra->setDataCancelamento($dados["data_cancelamento"]);
        }

        // Dados nota
        $compra->setModelo($dados["modelo"]);
        $compra->setSerie($dados["serie"]);
        $compra->setNumeroNota($dados["numero_nota"]);
        $compra->setDataEmissao($dados["data_emissao"]);
        $compra->setDataChegada($dados["data_chegada"]);
        $compra->setQtdProduto((int) $dados["qtd_produto"]);
        $compra->setFrete((float) $dados["frete"]);
        //$compra->setValorProduto((float) $dados["valor_produto"]);
        $compra->setValorCompra(floatval($dados["valor_compra"]));
        $compra->setSeguro((float) $dados["seguro"]);
        $compra->setOutrasDespesas((float) $dados["outras_despesas"]);
        $compra->setObservacao((string) $dados["observacao"] ?? Null);

        //Dados Fornecedor
        $fornecedor = $this->daoFornecedor->findById($dados['id_fornecedor'], false);
        $fornecedores = $this->daoFornecedor->create(get_object_vars($fornecedor));
        $compra->setFornecedor($fornecedores);

        //Dados Condição de Pagamento
        $condicaoPagamento = $this->daoCondicaoPagamento->findById($dados['id_condicaopg'], false);
        $condicaoPagamento = $this->daoCondicaoPagamento->listarCondicao(get_object_vars($condicaoPagamento));
        $compra->setCondicaoPagamento($condicaoPagamento);

        // Dados Produto
        $produtos = $this->daoCompraProduto->findById($compra->getModelo(), $compra->getNumeroNota(), $compra->getSerie(), true);
        if (!$produtos) {
            $compra->setCompraProdutoArray($dados['produtos']);
            //$valor_compra = $this->calcTotalCompra($dados['produtos']);
            $compra->setValorProduto(floatval($dados["valor_produto"]));
        } else {
            $compra->setCompraProdutoArray($produtos);
            $valor_produto = $this->calcTotalCompra($compra->getCompraProdutoArray());
            $compra->setValorProduto(floatval($valor_produto));
        }

        //Dados Profissional 
        $profissional = $this->daoProfissional->findById($dados['id_profissional'], false);
        $profissional = $this->daoProfissional->create(get_object_vars($profissional));
        $compra->setProfissional($profissional);

        $compra->setFrete((float) $dados["frete"]);
        return $compra;
    }

    public function store($compra)
    {

        $modelo = $compra->getModelo();
        $numero_nota = $compra->getNumeroNota();
        $serie = $compra->getSerie();
        $id_fornecedor = $compra->getFornecedor()->getId();
        $status = "ATIVA";
        $data_emissao = $compra->getDataEmissao();
        $data_chegada = $compra->getDataChegada();
        $compraProduto_array = $compra->getCompraProdutoArray();
        $frete = $compra->getFrete();
        $valor_produto = $compra->getValorProduto();
        $seguro = $compra->getSeguro();
        $outras_despesas = $compra->getOutrasDespesas();
        $qtd_produto = $compra->getQtdProduto();
        $valor_compra = $compra->getValorCompra();
        $id_condicaopg = $compra->getCondicaoPagamento()->getId();
        $id_profissional = $compra->getProfissional()->getId();
        $observacao = $compra->getObservacao();
        $custos = ($frete + $seguro + $outras_despesas);
        $this->calcularRateioCusto($compraProduto_array, $custos);
        // $clonedArray = array_map(function ($item) {
        //     return array_merge([], $item);
        // }, $compraProduto_array);

        try {
            DB::beginTransaction();
            $result = DB::INSERT(
                "INSERT INTO compra (modelo,numero_nota,serie,id_fornecedor,id_condicaopg,id_profissional,data_emissao,data_chegada,qtd_produto,valor_compra,status,observacao,valor_produto,frete,seguro,outras_despesas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $modelo,
                    $numero_nota,
                    $serie,
                    $id_fornecedor,
                    $id_condicaopg,
                    $id_profissional,
                    $data_emissao,
                    $data_chegada,
                    $qtd_produto,
                    $valor_compra,
                    $status,
                    $observacao,
                    $valor_produto,
                    $frete,
                    $seguro,
                    $outras_despesas
                ]
            );
            try {
                foreach ($compraProduto_array  as  $produto) {
                    $result = DB::INSERT(
                        "INSERT INTO compra_produto (compra_modelo,compra_numero_nota,compra_serie,id_produto,compra_id_fornecedor,qtd_produto,valor_unitario,valor_custo,total_produto,desconto,unidade) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $modelo,
                            $numero_nota,
                            $serie,
                            $produto['id_produto'],
                            $id_fornecedor,
                            $produto['qtd_produto'],
                            $produto['valor_unitario'],
                            $produto['valor_custo'],
                            $produto['total_produto'],
                            $produto['desconto'],
                            $produto['unidade'],

                        ]
                    );
                }
            } catch (QueryException $e) {
                $mensagem = $e->getMessage(); // Mensagem de erro
                $codigo = $e->getCode(); // Código do erro
                $consulta = $e->getSql(); // Consulta SQL que causou o erro
                $bindings = $e->getBindings(); // Valores passados como bind para a consulta
                DB::rollBack();
                return [$mensagem, $codigo, $consulta, $bindings];
            }
            try {
                $obj_condicaopagamento = $this->daoCondicaoPagamento->getData($compra->getCondicaoPagamento());
                foreach ($obj_condicaopagamento['parcelas'] as  $item) {
                    $numero_parcela = $item['parcela'];
                    $id_formapagamento = $item['formaPagamento'][0]['id'];
                    $data_vencimento = $this->somarDias($data_emissao, $item['prazo']);
                    $desconto = $obj_condicaopagamento['desconto'];
                    $juros = $obj_condicaopagamento['juros'];
                    $valor_parcela =  ($valor_compra * ($item['porcentagem']) / 100);
                    $status = "PENDENTE";
                    $result = DB::INSERT(
                        "INSERT INTO contas_pagar (compra_modelo,compra_numero_nota,compra_serie,numero_parcela,compra_id_fornecedor,id_formapagamento,data_emissao,data_vencimento,desconto,juros,valor_parcela,status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        [
                            $modelo,
                            $numero_nota,
                            $serie,
                            $numero_parcela,
                            $id_fornecedor,
                            $id_formapagamento,
                            $data_emissao,
                            $data_vencimento,
                            $desconto,
                            $juros,
                            $valor_parcela,
                            $status
                        ]
                    );
                }
            } catch (QueryException $e) {
                $mensagem = $e->getMessage(); // Mensagem de erro
                $codigo = $e->getCode(); // Código do erro
                $consulta = $e->getSql(); // Consulta SQL que causou o erro
                $bindings = $e->getBindings(); // Valores passados como bind para a consulta
                DB::rollBack();
                return [$mensagem, $codigo, $consulta, $bindings];
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registro inserido com sucesso!'
            ]);
        } catch (QueryException $e) {
            $mensagem = $e->getMessage(); // Mensagem de erro
            $codigo = $e->getCode(); // Código do erro
            $consulta = $e->getSql(); // Consulta SQL que causou o erro
            $bindings = $e->getBindings(); // Valores passados como bind para a consulta
            DB::rollBack();
            return [$mensagem, $codigo, $consulta, $bindings];
        }

        // dd($compraProduto_array, $clonedArray);
        // dd('store', $compra);
    }

    public function update(Request $request, $id)
    {
    }

    public function delete($id)
    {
    }

    public function findById(int $id, bool $model = false)
    {
    }

    public function getData(Compra $compra)
    {
        $dados = [
            'modelo' => $compra->getModelo(),
            'numero_nota' => $compra->getNumeroNota(),
            'serie'  => $compra->getSerie(),
            'qtd_produto' => $compra->getQtdProduto(),
            'valor_compra' => $compra->getValorCompra(),
            'valor_produto' => $compra->getValorProduto(),
            'frete' => $compra->getFrete(),
            'seguro' => $compra->getSeguro(),
            'outras_despesas' => $compra->getOutrasDespesas(),
            'data_emissao' => $compra->getDataEmissao(),
            'data_chegada' => $compra->getDataChegada(),
            'fornecedor'  =>  $this->daoFornecedor->getData($compra->getFornecedor()),
            'condicao_pagamento' => $this->daoCondicaoPagamento->getData($compra->getCondicaoPagamento()),
            'produtos' => $compra->getCompraProdutoArray(),
            'profissional' => $this->daoProfissional->getData($compra->getProfissional()),
            'status' =>  $compra->getStatus(),
            'data_cancelamento' => $compra->getDataCancelamento(),
            'observacao' => $compra->getObservacao(),
            'data_create' => $compra->getDataCadastro(),
            'data_alt' => $compra->getDataAlteracao()
        ];
        return $dados;
    }

    public function calcTotalCompra(array $compraProduto)
    {
        $total = 0;
        foreach ($compraProduto as $produto) {
            $total += $produto['valor_unitario'] * $produto['qtd_produto'];
        }
        return floatval($total);
    }

    public function calcularRateioCusto(&$produtos, $custos)
    {
        $totalCusto = 0;
        foreach ($produtos as $key => &$produto) {
            $quantidade = $produto['qtd_produto'];
            $valorUnitario = $produto['valor_unitario'];
            $totalCusto += $quantidade * $valorUnitario;
        }

        foreach ($produtos as &$produto) {
            $quantidade = $produto['qtd_produto'];
            $valorUnitario = $produto['valor_unitario'];
            $rateio = (($valorUnitario * $quantidade) / $totalCusto) * $custos;
            $valor_rateio = $rateio / $quantidade;
            $produto['valor_custo'] = $valorUnitario + $valor_rateio;
        }
    }

    public function somarDias($data, $dias)
    {
        $dataObj = Carbon::parse($data);
        $dataNova = $dataObj->addDays($dias);
        return $dataNova->format('Y-m-d');
    }

    // function calcularMediaPonderada($notas, $pesos) {
    //     if (count($notas) !== count($pesos)) {
    //         return false; // Verificar se os arrays têm o mesmo tamanho
    //     }

    //     $somaProdutos = 0;
    //     $somaPesos = 0;

    //     for ($i = 0; $i < count($notas); $i++) {
    //         $somaProdutos += $notas[$i] * $pesos[$i];
    //         $somaPesos += $pesos[$i];
    //     }

    //     if ($somaPesos === 0) {
    //         return false; // Verificar se a soma dos pesos é diferente de zero
    //     }

    //     return $somaProdutos / $somaPesos;
    // }

    // // Exemplo de uso
    // $notas = array(8, 9, 7);
    // $pesos = array(2, 3, 1);

    // $mediaPonderada = calcularMediaPonderada($notas, $pesos);

    // if ($mediaPonderada !== false) {
    //     echo "A média ponderada é: " . $mediaPonderada;
    // } else {
    //     echo "Erro ao calcular a média ponderada. Verifique os dados.";
    // }

}
