<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dao\DaoCidade;
use Illuminate\Support\Facades\Response;

class ControllerCidade extends Controller
{
    private $daoCidade;

    public function __construct()
    {
        $this->daoCidade = new DaoCidade();
    }

    public function index(Request $request)
    {
        $cidades = $this->daoCidade->all(true);
        return response()->json($cidades, 200);
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
        $cidade = $this->daoCidade->create($request->all());
        $store = $this->daoCidade->store($cidade);
        if ($store) {
            return response()->json(['success' => 'Cidade Cadastrado com Sucesso', 'obj' => $store]);
        } else {
            return response::json(['error' => 'Cidade não Cadastrado...']);
        }
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
        $regras = $this->rules();
        $regras['cidade'] = 'required|min:3|max:40';
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $cidade = $this->daoCidade->findById($id, false);
        if ($cidade === null) {
            return response()->json(['erro' => 'Impossível realizar  atualização'], 404);
        }
        $update = $this->daoCidade->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Cidade Alterado com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }
    public function destroy($id)
    {
        $delete = $this->daoCidade->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Cidade excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['erro' => $delete], 404);
        }
    }
    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $cidade = $this->daoCidade->findById($id, true);
            if ($cidade) {
                return response()->json($cidade, 200);
            }
        }

        return response()->json(['error' => 'Cidade não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'cidade' => 'required|min:3|max:40|unique:cidades',
            'ddd' => 'required|integer|min:2',
            'id_estado' => 'required|integer',
            'estado' => 'required',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'cidade.required' => 'O campo Cidade deve ser preenchido.',
            'cidade.min' => 'O campo deve ter no mínimo 3 caracteres.',
            'cidade.max' => 'O campo deve ter no máximo 40 caracteres.',
            'cidade.unique' => 'Cidade já Cadastrado !',
            'ddd.required' => 'O campo DDD deve ser preenchido.',
            'ddd.integer' => 'O campo DDD deve ser um número inteiro.',
            'ddd.min' => 'O campo deve ter no mínimo 2 caracteres.',
            'id_estado.required' => 'O campo Codigo Estado deve ser preenchido.',
            'id_estado.integer' => 'O campo Codigo Estado deve ser um número inteiro.',
            'estado.required' => 'O campo Estado deve ser preenchido.',
        ];
        return $feedbacks;
    }
}
