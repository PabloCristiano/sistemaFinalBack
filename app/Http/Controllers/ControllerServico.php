<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dao\DaoServico;
use Illuminate\Support\Facades\Response;

class ControllerServico extends Controller
{
    private $daoServico;

    public function __construct()
    {
        $this->daoServico = new DaoServico();
    }

    public function index(Request $request)
    {
        $servico = $this->daoServico->all(true);
        return response()->json($servico, 200);
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
        $servico = $this->daoServico->create($request->all());
        $store = $this->daoServico->store($servico);
        if ($store === true) {
            return response()->json(['success' => 'Serviço Cadastrado com Sucesso', 'obj' => $store, 200]);
        } else {
            return response::json($store);
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
        $regras['servico'] = 'required|min:3|max:50';
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $servico = $this->daoServico->findById($id, false);
        if (empty($servico)) {
            return response()->json(['error' => 'Impossível realizar  atualização, Serviço não encontrado !'], 404);
        }
        $update = $this->daoServico->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Serviço Alterado com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }

    public function destroy($id)
    {
        $servico = $this->daoServico->findById($id, false);
        if (empty($servico)) {
            return response()->json(['error' => 'Impossível realizar, Serviço não encontrado !'], 404);
        }
        $delete = $this->daoServico->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Serviço excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['error' => $delete], 404);
        }
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $servico = $this->daoServico->findById($id, true);
            if ($servico) {
                return response()->json($servico, 200);
            }
        }
        return response()->json(['error' => 'Serviço não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'servico' => 'required|min:3|max:50|unique:servicos',
            'tempo' => 'required|integer',
            'valor' => 'required|numeric|between:0,9999.99',
            'comissao' => 'nullable|numeric',
            'observacoes' => 'nullable|max:150',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'servico.required' => 'O campo Serviço deve ser preenchido.',
            'servico.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'servico.max' => 'O campo nome deve ter no máximo 50 caracteres.',
            'servico.unique' => 'Serviço já Cadastrado!',
            'tempo.required' => 'O campo Tempo deve ser preenchido.',
            'tempo.integer' => 'O campo Tempo deve ser um numero Inteiro.',
            'valor.required' => 'O campo Valor deve ser preenchido.',
            'comissao.numeric' => 'O campo Comissão deve ser um valor permitido',
            'observacoes.max' => 'O campo Observações deve conter no máximo 150 caracteres.',
        ];
        return $feedbacks;
    }
}
