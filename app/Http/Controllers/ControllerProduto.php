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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
            'unidade' => 'required|integer',
            //'valor' => 'required|numeric|between:0,9999.99',
            //'comissao' => 'nullable|numeric',
            //'observacoes' => 'nullable|max:150',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            //'servico.required' => 'O campo Serviço deve ser preenchido.',
            //'servico.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            //'servico.max' => 'O campo nome deve ter no máximo 50 caracteres.',
            //'servico.unique' => 'Serviço já Cadastrado!',
            //'tempo.required' => 'O campo Tempo deve ser preenchido.',
            //'tempo.integer' => 'O campo Tempo deve ser um numero Inteiro.',
            //'valor.required' => 'O campo Valor deve ser preenchido.',
            //'comissao.numeric' => 'O campo Comissão deve ser um valor permitido',
            //'observacoes.max' => 'O campo Observações deve conter no máximo 150 caracteres.',
        ];
        return $feedbacks;
    }
}
