<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Dao\DaoCondicaoPagamento;

class ControllerCondicaoPagamento extends Controller
{
    private  $daoCondicaoPagamento;

    public function __construct()
    {
        $this->daoCondicaoPagamento = new DaoCondicaoPagamento();
    }
    public function index()
    {

        $condicaoPagamento = $this->daoCondicaoPagamento->all(true);
        return response()->json($condicaoPagamento, 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $regras = $this->rules();
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $dados = [
            'id' => $request->id,
            'condicao_pagamento' => $request->condicao_pagamento,
            'juros' => $request->juros,
            'multa' => $request->multa,
            'desconto' => $request->desconto,
            'data_create' => $request->data_create,
            'data_alt' => $request->data_alt,
            'qtd_parcela' => intval($request->qtd_parcela),
            'parcelas' => json_decode($request->parcelas, true),
        ];
        $condicaopagamento = $this->daoCondicaoPagamento->create($dados);
        $store = $this->daoCondicaoPagamento->store($condicaopagamento);
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

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $condicaoPagamento = $this->daoCondicaoPagamento->findById($id, true);
            if ($condicaoPagamento) {
                return response()->json($condicaoPagamento, 200);
            }
        }
        return response()->json(['error' => 'Condição de Pagamento não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'condicao_pagamento' => 'required|min:3|max:40|unique:condicao_pg',
            'juros' => 'required|numeric|between:0,100',
            'multa' => 'required|numeric|between:0,100',
            'desconto' => 'required|numeric|between:0,100',
            'parcelas' => 'required',
            'qtd_parcela' => 'required|integer',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'condicao_pagamento.required' => 'O campo Condição de Pagamento deve ser preenchido.',
            'condicao_pagamento.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'condicao_pagamento.max' => 'O campo nome deve ter no máximo 40 caracteres.',
            'condicao_pagamento.unique' => 'Condição de Pagamento já Cadastrada!',
            'juros.required' => 'O campo Juros deve ser preenchido.',
            'juros.numeric' => 'O campo Juros deve um numero Válido.',
            'juros.between' => 'O campo Juros deve ter máximo 100%.',
            'multa.required' => 'O campo Multa deve ser preenchido.',
            'multa.numeric' => 'O campo Multa deve um numero Válido.',
            'multa.between' => 'O campo Multa deve ter máximo 100%.',
            'desconto.required' => 'O campo Desconto deve ser preenchido.',
            'desconto.numeric' => 'O campo Desconto deve um numero Válido.',
            'desconto.between' => 'O campo Desconto deve ter máximo 100%.',
            'qtd_parcela.integer' => 'O campo qtd_parcela deve ser um numero inteiro.',
            'qtd_parcela.required' => 'O campo qtd_parcela deve ser preenchido.',
            'parcelas.required' => 'Deve conter Parcela(s).',
        ];
        return $feedbacks;
    }
}
