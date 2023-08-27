<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoCompra;
use Illuminate\Support\Facades\Validator;


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
       // dd($request->all());
        $regras = $this->rules();
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        // $payLoad = $this->convertArray($request->all());
        $produtos = [];
        $produtos = json_decode($request->produtos, true);
        $parcelas = json_decode($request->condicaopagamento, true);
        // $quantidadeProdutos = count($produtos);
        // $quantidadeParcelas = count($parcelas);
        // $parcelas_convertida = $this->convertValorParcelaToFloat($parcelas);
        //$produtos_convertido = $this->convertProdutoArray($produtos);
       // dd($produtos);
        $regras = [
            '*.id_produto' => 'required|integer',
            '*.unidade' => 'required|string',
            '*.qtd_produto' => 'required|numeric',
            '*.valor_unitario' => 'required|numeric',
            // Adicione mais regras de validação conforme necessário
        ];
        $feedbacks = [
            '*.id_produto.required' => 'O campo id_produto deve ser preenchido.',
            '*.unidade.required' => 'O campo unidade deve ser preenchido.',
            '*.qtd_produto.required' => 'O campo qtd_produto deve ser preenchido.',
            '*.valor_unitario.required' => 'O campo Condição de Pagamento deve ser preenchido.',

            '*.id_produto.integer' => 'O campo id_produto ',
            '*.unidade.string' => 'O campo unidade ',
            '*.qtd_produto.numeric' => 'O campo qtd_produto ',
            '*.valor_unitario.numeric' => 'O campo valor_unitario',

        ];

        $validator = Validator::make($produtos, $regras, $feedbacks);

        if ($validator->fails()) {
            $erros = $validator->errors();
            $posicao = 1;
            foreach ($erros->all() as $mensagemErro) {
                echo "Erro na posição " . ($posicao) . ": $mensagemErro <br>";
                $posicao++;
            }
        } else {
            echo 'tchau';
        }
         dd($produtos);
        dd($request->all());
        //dd($payLoad);
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

    //regras de validação
    public function rules()
    {
        $regras = [
            'modelo' => 'required|numeric|gt:0',
            'serie' => 'required|numeric|gt:0',
            'numero_nota' => 'required|unique:compra',
            'id_fornecedor' => 'required|integer',
            'fornecedor' => 'required|min:3|max:50',
            'total_compra' => 'required|numeric|gt:0',
            'total_produtos' => 'required|numeric|gt:0',
            'frete' => 'nullable|numeric|gt:0',
            'seguro' => 'nullable|numeric|gt:0',
            'outras_despesas' => 'nullable|numeric|gt:0',
            'produtos' => 'required',
            'id_condicaopg' => 'required|integer',
            'condicaopg' => 'required|min:3|max:50',
            'condicaopagamento' => 'required',
            'observacao' => 'nullable|min:5|max:255',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'modelo.required' => 'O campo Modelo deve ser preenchido.',
            'serie.required' => 'O campo Série deve ser preenchido.',
            'numero_nota.required' => 'O campo Condição de Pagamento deve ser preenchido.',
            'numero_nota.unique' => 'Numero de Nota já Cadastrada!',
            'total_compra.required' => 'O campo Modelo deve ser preenchido.',

        ];
        return $feedbacks;
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
        $array['condicaopagamento'] = $this->convertValorParcelaToFloat(json_decode($array['condicaopagamento'], true));

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
