<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoCompra;

class ControllerCompra extends Controller
{
    private $daoCompra;

    public function __construct()
    {
        $this->daoCompra = new DaoCompra();
    }
    public function index()
    {
        $compras = $this->daoCompra->all(true);
        return response()->json($compras, 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //dd($request->all());
        $payLoad = $this->convertArray($request->all());
        $id_condicaopg = intval($request->id_condicaopg);
        $produtos = [];
        $produtos = json_decode($request->produtos, true);
        $parcelas = json_decode($request->condicaopagamento, true);
        $quantidadeProdutos = count($produtos);
        $quantidadeParcelas = count($parcelas);
        $parcelas_convertida = $this->convertValorParcelaToFloat($parcelas);
        $produtos_convertido = $this->convertProdutoArray($produtos);
        //dd($id_condicaopg, $produtos, $parcelas, $quantidadeProdutos, $quantidadeParcelas, $parcelas_convertida, $produtos_convertido);
        // dd($parcelas_convertida);
        dd($payLoad);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }

    public function convertArray($array)
    {
        $array['id_fornecedor'] = intval($array['id_fornecedor']);
        $array['id_condicaopg'] = intval($array['id_condicaopg']);
        $array['total_compra'] = floatval($array['total_compra']);
        $array['total_produtos'] = floatval($array['total_produtos']);
        $array['frete'] = floatval($array['frete']);
        $array['seguro'] = floatval($array['seguro']);
        $array['outras_despesas'] = floatval($array['outras_despesas']);

        // Convertendo a string JSON em um array associativo para 'produtos' e 'condicaopagamento'
        $array['produtos'] = $this->convertProdutoArray(json_decode($array['produtos'], true));
        $array['condicaopagamento'] = $this->convertValorParcelaToFloat( json_decode($array['condicaopagamento'], true));

        return $array;
    }

    public function convertValorParcelaToFloat($array)
    {
        foreach ($array as &$item) {
            $valorParcela = str_replace(['R$', ' '], '', $item['valorParcela']);
            $item['valorParcela'] = floatval(str_replace(',', '.', $valorParcela));
        }
        return $array;
    }
    public function convertProdutoArray($array)
    {
        foreach ($array as &$item) {
            $item['qtd_produto'] = intval($item['qtd_produto']);

            $valorUnitario = str_replace(',', '.', $item['valor_unitario']);
            $item['valor_unitario'] = floatval($valorUnitario);

            $desconto = str_replace(',', '.', $item['desconto']);
            $item['desconto'] = floatval($desconto);

            $totalProduto = str_replace(',', '.', $item['total_produto']);
            $item['total_produto'] = floatval($totalProduto);

            // Mantém a string diretamente em "produto"
            $item['produto'] = $item['produto']['produto'];

            // Remove os itens indesejados do array
            unset($item['desativar']);
            unset($item['editing']);
            unset($item['msgErrorQtd']);
            unset($item['msgErrorPer']);
            unset($item['msgErrorvl']);
        }
        return $array;
    }
}
