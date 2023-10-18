<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoCompra;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Exception;
use InvalidArgumentException;

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

    public function validaNumNota(Request $request)
    {
        $regras = [
            'numero_nota' => 'required|numeric|gt:0|unique:compra',
        ];
        $feedbacks = [
            'numero_nota.required' => 'O campo número da nota é obrigatório.',
            'numero_nota.numeric' => 'O campo número da nota deve ser um número.',
            'numero_nota.gt' => 'O campo número da Nota não pode ser zero.',
            'numero_nota.unique' => ' já está em uso.',
        ];
        $request->validate($regras, $feedbacks);
        return response::json(true);
    }

    public function verificaNumCompra(Request $request)
    {

        $regras = [
            'modelo' => 'required|numeric|gt:0',
            'serie' => 'required|numeric|gt:0',
            'numero_nota' => 'required|numeric|gt:0',
            'id_fornecedor' => 'required|integer|min:1',
        ];

        $feedbacks = [
            'modelo.required' => 'O campo Modelo deve ser preenchido.',
            'modelo.gt' => 'O campo modelo deve ser maior que zero.',
            'serie.required' => 'O campo Série deve ser preenchido.',
            'serie.gt' => 'O campo série deve ser maior que zero.',
            'numero_nota.required' => 'O campo número da nota é obrigatório.',
            'numero_nota.integer' => 'O campo número da nota deve ser um número inteiro.',
            'numero_nota.min' => 'O campo número da nota deve ser maior que zero.',
            'id_fornecedor.required' => 'O campo ID do fornecedor é obrigatório.',
            'id_fornecedor.integer' => 'O ID do fornecedor deve ser um número inteiro.',
            'id_fornecedor.min' => 'O ID do fornecedor deve ser no mínimo 1.',
        ];
        $request->validate($regras, $feedbacks);        
        // dd($request->all());
        $Validad = $this->daoCompra->verificaNumCompra($request->all());
        return response::json($Validad);
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $regras = $this->rules();
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $produtos = [];
        $parcelas = [];
        
        // $produtos = json_decode($request->produtos, true);
        $produtos = $request->produtos;
        
        // $parcelas = json_decode($request->condicaopagamento, true);
        $parcelas = $request->condicaopagamento;

        try {
            $regrasProdutos = $this->rulesProduto();
            $feedbacksProdutos = $this->feedbacksProduto();
            
            // Certifique-se de que $produtos é um array válido antes de usar na validação
            if (!is_array($produtos)) {
                throw new InvalidArgumentException('$produtos deve ser um array válido.');
            }
            
            // Validação do array de Produtos
            $validator = Validator::make($produtos, $regrasProdutos, $feedbacksProdutos);
            
            if ($validator->fails()) {
                $erros = $validator->errors();
                $mensagensOrganizadas = [];
                // Iterar pelas mensagens de erro e agrupar
                foreach ($erros->messages() as $chave => $mensagens) {
                    list($posicao, $campo) = explode('.', $chave, 2);
                    $posicaoProduto = $posicao + 1; // Adiciona 1 para a referência do tipo "produto"
                    $mensagensOrganizadas[$posicaoProduto][$campo][] = $mensagens;
                }

                // Reorganizar para o formato desejado
                $errosProduto = [];
                foreach ($mensagensOrganizadas as $posicaoProduto => $mensagensPorCampo) {
                    foreach ($mensagensPorCampo as $campo => $mensagens) {
                        $errosProduto[$posicaoProduto][$campo] = $mensagens;
                    }
                }

                //Se tiver erros Retorna a request
                if (!empty($errosProduto)) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'produtos' => $errosProduto
                        ]
                    ], 422);
                }
            }
        } catch (InvalidArgumentException $e) {
            // Lidar com o erro de tipo inválido
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            // Lidar com outras exceções se necessário
            return response()->json(['error' => 'Something went wrong'], 500);
        }
       
        try {
            $regrasCondicaoPagamento = $this->rulesCondicaoPagamento();
            $feedbacksCondicaoPagamento = $this->feedbacksCondicaoPagamento();

            // Certifique-se de que é um array válido antes de usar na validação
            if (!is_array($parcelas)) {
                throw new InvalidArgumentException('$Parcelas deve ser um array válido.');
            }

            // Validação do array de Parcelas
            $validator = Validator::make($parcelas, $regrasCondicaoPagamento, $feedbacksCondicaoPagamento);

            if ($validator->fails()) {
                $erros = $validator->errors();
                $mensagensOrganizadas = [];
                // Iterar pelas mensagens de erro e agrupar
                foreach ($erros->messages() as $chave => $mensagens) {
                    list($posicao, $campo) = explode('.', $chave, 2);
                    $posicaoCondicaoPagamento = $posicao + 1; // Adiciona 1 para a referência do tipo "produto"
                    $mensagensOrganizadas[$posicaoCondicaoPagamento][$campo][] = $mensagens;
                }

                // Reorganizar para o formato desejado
                $errosCondicaoPagamento = [];
                foreach ($mensagensOrganizadas as $posicaoCondicaoPagamento => $mensagensPorCampo) {
                    foreach ($mensagensPorCampo as $campo => $mensagens) {
                        $errosCondicaoPagamento[$posicaoCondicaoPagamento][$campo] = $mensagens;
                    }
                }

                //Se tiver erros Retorna a request
                if (!empty($errosCondicaoPagamento)) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'Parcelas' => $errosCondicaoPagamento
                        ]
                    ], 422);
                }
            }
        } catch (InvalidArgumentException $e) {
            // Lidar com o erro de tipo inválido
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            // Lidar com outras exceções se necessário
            return response()->json(['error' => 'Something went wrong'], 500);
        }
       
        $payLoad = $this->convertArray($request->all());
        // $payLoad = $request->all();
        // dd($payLoad);
        $compras = $this->daoCompra->create($payLoad);
        $store = $this->daoCompra->store($compras);
        return response::json($store);
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

    public function getByid(Request $request, $id)
    {
        $modelo = $request->modelo;
        $numero_nota = $request->numero_nota;
        $serie = $request->serie;
        if (ctype_digit(strval($id))) {
            $compra = $this->daoCompra->findByIdCompra($modelo, $numero_nota, $serie, $id, true);
            if ($compra) {
                return response()->json($compra, 200);
            }
        }
        return response()->json(['error' => 'Condição de Pagamento não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'modelo' => 'required|numeric|gt:0',
            'serie' => 'required|numeric|gt:0',
            // 'numero_nota' => 'required|numeric|gt:0|unique:compra',
            'numero_nota' => 'required|numeric|gt:0',
            'id_fornecedor' => 'required|integer|min:1|exists:fornecedores,id',
            'id_profissional' => 'required|integer|min:1|exists:profissionais,id',
            'fornecedor' => 'required|min:3|max:50',
            'data_emissao' => 'required|date|before_or_equal:today',
            'data_chegada' => 'required|date|after_or_equal:data_emissao',
            'total_compra' => 'required|numeric|min:1',
            'total_produtos' => 'required|numeric|min:1',
            'frete' => 'nullable|numeric||min:0',
            'seguro' => 'nullable|numeric||min:0',
            'outras_despesas' => 'nullable|numeric||min:0',
            'qtd_produto' => 'required|integer|min:1',
            'produtos' => 'required',
            'id_condicaopg' => 'required|integer|min:1|exists:condicao_pg,id',
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
            'modelo.gt' => 'O campo modelo deve ser maior que zero.',
            'serie.required' => 'O campo Série deve ser preenchido.',
            'serie.gt' => 'O campo série deve ser maior que zero.',
            'numero_nota.required' => 'O campo número da nota é obrigatório.',
            'numero_nota.integer' => 'O campo número da nota deve ser um número inteiro.',
            'numero_nota.min' => 'O campo número da nota deve ser maior que zero.',
            'numero_nota.unique' => 'Este número de nota já está em uso.',
            'id_fornecedor.required' => 'O campo ID do fornecedor é obrigatório.',
            'id_fornecedor.integer' => 'O ID do fornecedor deve ser um número inteiro.',
            'id_fornecedor.min' => 'O ID do fornecedor deve ser no mínimo 1.',
            'id_fornecedor.exists' => 'O fornecedor selecionado não foi encontrado.',
            'fornecedor.required' => 'O campo fornecedor é obrigatório.',
            'fornecedor.min' => 'O campo fornecedor deve ter no mínimo :min caracteres.',
            'fornecedor.max' => 'O campo fornecedor deve ter no máximo :max caracteres.',
            'total_compra.required' => 'O campo total da compra é obrigatório.',
            'total_compra.numeric' => 'O campo total da compra deve ser um número.',
            'total_compra.min' => 'O campo total da compra deve ser no mínimo :min.',
            'total_produtos.required' => 'O campo total de produtos é obrigatório.',
            'total_produtos.numeric' => 'O campo total de produtos deve ser um número.',
            'total_produtos.min' => 'O campo total de produtos deve ser no mínimo :min.',
            'frete.numeric' => 'O campo frete deve ser um número.',
            'frete.min' => 'O campo frete deve ser no mínimo :min.',
            'seguro.numeric' => 'O campo seguro deve ser um número.',
            'seguro.min' => 'O campo seguro deve ser no mínimo :min.',
            'outras_despesas.numeric' => 'O campo outras despesas deve ser um número.',
            'outras_despesas.min' => 'O campo outras despesas deve ser no mínimo :min.',
            'qtd_produto.required' => 'A quantidade do produto é obrigatória.',
            'qtd_produto.integer' => 'A quantidade do produto deve ser um número inteiro.',
            'qtd_produto.min' => 'A quantidade do produto deve ser no mínimo 1.',
            'produtos.required' => 'É necessário pelo menos um produto.',
            'produtos.array' => 'O campo produtos deve ser um array.',
            'id_condicaopg.required' => 'O campo ID da condição de pagamento é obrigatório.',
            'id_condicaopg.integer' => 'O ID da condição de pagamento deve ser um número inteiro.',
            'id_condicaopg.min' => 'O ID da condição de pagamento deve ser no mínimo 1.',
            'id_condicaopg.exists' => 'A condição de pagamento selecionada não foi encontrada.',
            'condicaopg.required' => 'O campo condição de pagamento é obrigatório.',
            'condicaopg.min' => 'O campo condição de pagamento deve ter no mínimo :min caracteres.',
            'condicaopg.max' => 'O campo condição de pagamento deve ter no máximo :max caracteres.',
            'condicaopagamento.required' => 'É necessário pelo menos uma condição de pagamento.',
            'observacao.min' => 'O campo observação deve ter no mínimo :min caracteres.',
            'observacao.max' => 'O campo observação deve ter no máximo :max caracteres.',
            'data_emissao.required' => 'O campo data de emissão é obrigatório.',
            'data_emissao.date' => 'O campo data de emissão deve ser uma data válida.',
            'data_emissao.before_or_equal' => 'A data de emissão não pode ser maior que a data atual.',
            'data_chegada.required' => 'O campo data de chegada é obrigatório.',
            'data_chegada.date' => 'O campo data de chegada deve ser uma data válida.',
            'data_chegada.after_or_equal' => 'A data de chegada não pode ser menor que a data de emissão.',
            'id_profissional.required' => 'O campo ID do profissional é obrigatório.',
            'id_profissional.integer' => 'O ID do profissional deve ser um número inteiro.',
            'id_profissional.min' => 'O ID do profissional deve ser no mínimo 1.',
            'id_profissional.exists' => 'O ID do profissional não foi encontrado.',

        ];
        return $feedbacks;
    }

    //regras de validação array de Produtos
    public function rulesProduto()
    {
        $regrasProduto = [
            '*.id_produto' => 'required|integer|min:1',
            '*.produto' => 'required|min:3|max:40',
            '*.unidade' => 'required|string',
            '*.qtd_produto' => 'required|integer|min:1',
            '*.valor_unitario' => 'required|numeric|min:1',
            '*.desconto' => 'required|numeric|between:0,100',
            '*.total_produto' => 'required|numeric|min:0',

        ];
        return $regrasProduto;
    }
    //mensagens das regras de validação array Produtos
    public function feedbacksProduto()
    {
        $feedbacksProdutos = [
            '*.id_produto.required' => 'O campo id do produto é obrigatório.',
            '*.id_produto.integer' => 'O campo id do produto deve ser um número inteiro.',
            '*.id_produto.min' => 'O campo id do produto deve ser maior que zero.',
            '*.produto.required' => 'O campo produto é obrigatório.',
            '*.produto.min' => 'O campo produto deve ter no mínimo :min caracteres.',
            '*.produto.max' => 'O campo produto deve ter no máximo :max caracteres.',
            '*.unidade.required' => 'O campo unidade é obrigatório.',
            '*.qtd_produto.required' => 'O campo quantidade do produto é obrigatório.',
            '*.qtd_produto.integer' => 'O campo quantidade do produto deve ser um número inteiro.',
            '*.qtd_produto.min' => 'O campo quantidade do produto deve ser maior que zero.',
            '*.valor_unitario.required' => 'O campo valor unitário é obrigatório.',
            '*.valor_unitario.numeric' => 'O campo valor unitário deve ser um número.',
            '*.valor_unitario.min' => 'O campo valor unitário deve ser maior que zero.',
            '*.desconto.required' => 'O campo desconto é obrigatório.',
            '*.desconto.numeric' => 'O campo desconto deve ser um número.',
            '*.desconto.between' => 'O campo desconto deve estar entre :min e :max.',
            '*.total_produto.required' => 'O campo total do produto é obrigatório.',
            '*.total_produto.numeric' => 'O campo total do produto deve ser um número.',
            '*.total_produto.min' => 'O campo total do produto deve ser maior ou igual a zero.',

        ];
        return $feedbacksProdutos;
    }

    //regras de validação array de Produtos
    public function rulesCondicaoPagamento()
    {
        $regrasCondicaoPagamento = [
            '*.numero_parcela' => 'required|integer|min:1',
            '*.id_formapagamento' => 'required|integer|min:1',
            '*.formaPagamento' => 'required|min:1|max:40',
            '*.data_vecimento' => 'required|date',
            '*.valor_parcela' => 'required|numeric|min:1',
        ];
        return $regrasCondicaoPagamento;
    }
    //mensagens das regras de validação array Produtos
    public function feedbacksCondicaoPagamento()
    {
        $feedbacksCondicaoPagamento = [
            '*.numero_parcela.required' => 'O campo parcela é obrigatório para todas as condições de pagamento.',
            '*.numero_parcela.integer' => 'O valor da parcela deve ser um número inteiro.',
            '*.numero_parcela.min' => 'A parcela deve ter pelo menos :min.',

            '*.id_formapagamento.required' => 'O campo ID da forma de pagamento é obrigatório para todas as condições de pagamento.',
            '*.id_formapagamento.integer' => 'O ID da forma de pagamento deve ser um número inteiro.',
            '*.id_formapagamento.min' => 'O ID da forma de pagamento deve ser no mínimo :min.',

            '*.formaPagamento.required' => 'O campo forma de pagamento é obrigatório para todas as condições de pagamento.',
            '*.formaPagamento.min' => 'A forma de pagamento deve ter pelo menos :min caracteres.',
            '*.formaPagamento.max' => 'A forma de pagamento não pode ter mais de :max caracteres.',

            '*.data_vecimento.required' => 'O campo data de vencimento é obrigatório para todas as condições de pagamento.',
            '*.data_vecimento.date' => 'A data de vencimento deve estar em um formato válido.',

            '*.valor_parcela.required' => 'O campo valor da parcela é obrigatório para todas as condições de pagamento.',
            '*.valor_parcela.numeric' => 'O valor da parcela deve ser um número.',
            '*.valor_parcela.min' => 'O valor da parcela deve ser no mínimo :min.',

        ];
        return $feedbacksCondicaoPagamento;
    }

    public function convertArray($array)
    {
        
        $array['id_fornecedor'] = intval($array['id_fornecedor']);
        $array['id_condicaopg'] = intval($array['id_condicaopg']);
        $array['total_compra'] = floatval($array['total_compra']);
        $array['valor_compra'] = floatval($array['total_compra']);
        $array['total_produtos'] = floatval($array['total_produtos']);
        $array['valor_produto'] = floatval($array['total_produtos']);
        $array['frete'] = floatval($array['frete']);
        $array['seguro'] = floatval($array['seguro']);
        $array['qtd_produto'] = intval($array['qtd_produto']);
        $array['outras_despesas'] = floatval($array['outras_despesas']);
        $array['produtos'] = $this->convertProdutoArray($array['produtos']);
        $array['condicaopagamento'] = $this->convertValorParcelaToFloat($array['condicaopagamento']);
        
        return $array;
    }
    public function convertValorParcelaToFloat($array)
    {
        // $array = json_decode($array, true);
        foreach ($array as &$item) {
            $valorParcela = str_replace(['R$', ' '], '', $item['valor_parcela']);
            $item['valor_parcela'] = floatval(str_replace(',', '.', $valorParcela));
        }
        return $array;
    }
    public function convertProdutoArray($array)
    {
        // $array = json_decode($array, true);
       // dd($array[0]['produto']);
        foreach ($array as &$item) {
            $item['qtd_produto'] = intval($item['qtd_produto']);
            $valorUnitario = str_replace(',', '.', $item['valor_unitario']);
            $item['valor_unitario'] = floatval($valorUnitario);
            $desconto = str_replace(',', '.', $item['desconto']);
            $item['desconto'] = floatval($desconto);
            $totalProduto = str_replace(',', '.', $item['total_produto']);
            $item['total_produto'] = floatval($totalProduto);

            // Mantém a string diretamente em "produto"
            $item['produto'] = $item['produto'];

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
