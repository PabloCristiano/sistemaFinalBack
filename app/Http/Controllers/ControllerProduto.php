<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dao\DaoProduto;
use Illuminate\Support\Facades\Response;

class ControllerProduto extends Controller
{
    private $daoProduto;

    public function __construct()
    {
        $this->daoProduto = new DaoProduto();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $produtos = $this->daoProduto->all(true);
        return response()->json($produtos, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $regras = $this->rules();
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $produto = $this->daoProduto->create($request->all());

        $store = $this->daoProduto->store($produto);
        if ($store === true) {
            return response()->json(['success' => 'Produto Cadastrado com Sucesso', 'obj' => $store, 200]);
        } else {
            return response::json($store);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $regras = $this->rules();
        $regras['produto'] = 'required|min:3|max:50';
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $servico = $this->daoProduto->findById($id, false);
        if (empty($servico)) {
            return response()->json(['error' => 'Impossível realizar  atualização, Produto não encontrado !'], 404);
        }
        $update = $this->daoProduto->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Produto Alterado com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produto = $this->daoProduto->findById($id, false);
        if (empty($produto)) {
            return response()->json(['error' => 'Impossível realizar, Produto não encontrado !'], 404);
        }
        $delete = $this->daoProduto->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Produto excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['error' => $delete], 404);
        }
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $produto = $this->daoProduto->findById($id, true);
            if ($produto) {
                return response()->json($produto, 200);
            }
        }
        return response()->json(['error' => 'Produto não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'produto' => 'required|min:3|max:50|unique:produtos',
            'unidade' => 'required|min:3|max:20',
            /* 'qtdEstoque' => 'required|integer|gt:0',*/
            /*'precoCusto' => 'required|numeric|gt:0',*/
            'precoVenda' => 'required|numeric|gt:0',
            /*'custoUltCompra' => 'required|numeric|gt:0',*/
            'id_categoria' => 'required|integer',
            'categoria' => 'required|min:3|max:50',
            'id_fornecedor' => 'required|integer',
            'fornecedor' => 'required|min:3|max:50',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [

            'produto.required' => 'O campo Produto deve ser preenchido.',
            'produto.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'produto.max' => 'O campo nome deve ter no máximo 50 caracteres.',
            'produto.unique' => 'Produto já Cadastrado!',
            'unidade.required' => 'O campo Unidade deve ser preenchido.',
            'unidade.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'unidade.max' => 'O campo nome deve ter no máximo 20 caracteres.',
            'qtdEstoque.required' => 'O campo Qtd Estoque deve ser preenchido.',
            'qtdEstoque.integer' => 'O campo Qtd Estoque deve ser um numero Inteiro.',
            'qtdEstoque.gt' => 'O campo Qtd Estoque deve ser Positivo e maior que zero.',
            'precoCusto.required' => 'O campo Preço de Custo deve ser preenchido.',
            'precoCusto.gt' => 'O campo Preço de Custo deve ser Positivo e maior que zero.',
            'precoVenda.required' => 'O campo Preço de Venda deve ser preenchido.',
            'precoVenda.gt' => 'O campo Preço de Venda deve ser Positivo e maior que zero.',
            'custoUltCompra.required' => 'O campo Custo da Últ Compra deve ser preenchido.',
            'custoUltCompra.gt' => 'O campo Custo da Últ Compra deve ser Positivo e maior que zero.',
            'id_categoria.required' => 'Codigo Categoria deve ser preenchido.',
            'id_categoria.integer' => 'Codigo Categoria deve ser um numero inteiro.',
            'categoria.required' => 'Categoria deve ser preenchido.',
            'categoria.min' => 'Categoria deve ter no mínimo 3 caracteres.',
            'categoria.max' => 'Categoria deve ter no máximo 50 caracteres.',
            'id_fornecedor.required' => 'Codigo Fornecedor deve  ser preenchido.',
            'id_fornecedor.integer' => 'Codigo Fornecedor deve ser um numero inteiro.',
            'fornecedor.required' => 'Fornecedor deve ser preenchido.',
            'fornecedor.min' => 'Fornecedor deve ter no mínimo 3 caracteres.',
            'fornecedor.max' => 'Fornecedor deve ter no máximo 50 caracteres.',

        ];
        return $feedbacks;
    }
}
