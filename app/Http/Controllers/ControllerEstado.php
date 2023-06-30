<?php

namespace App\Http\Controllers;

use App\Dao\DaoEstado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ControllerEstado extends Controller
{
    private $daoEstado;

    public function __construct()
    {
        $this->daoEstado = new DaoEstado();
    }

    public function index(Request $request)
    {
        $estados = $this->daoEstado->all(true);
        return response()->json($estados, 200);
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
        $estado = $this->daoEstado->create($request->all());
        $store = $this->daoEstado->store($estado);
        if ($store) {
            return response()->json(['success' => 'Estado Cadastrado com Sucesso', 'obj' => $store]);
        } else {
            return response::json(['error' => 'Estado não Cadastrado...']);
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
        $regras['estado'] = 'required|min:3|max:40';
        $regras['uf'] = 'required|min:2|max:5';
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $estado = $this->daoEstado->findById($id, false);
        if ($estado === null) {
            return response()->json(['erro' => 'Impossível realizar  atualização'], 404);
        }
        $update = $this->daoEstado->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Estado Alterado com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }

    public function destroy($id)
    {
        $delete = $this->daoEstado->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Estado excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['erro' => $delete], 404);
        }
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $estado = $this->daoEstado->findById($id, true);
            if ($estado) {
                return response()->json($estado, 200);
            }
        }

        return response()->json(['error' => 'Estado não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'estado' => 'required|min:3|max:40|unique:estados',
            'uf' => 'required|min:2|max:5|unique:estados',
            'id_pais' => 'required|integer',
            'pais' => 'required',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'estado.required' => 'O campo Estado deve ser preenchido.',
            'estado.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'estado.max' => 'O campo nome deve ter no máximo 40 caracteres.',
            'estado.unique' => 'Estado já Cadastrado !',
            'uf.unique' => 'UF já Cadastrado !',
            'uf.required' => 'O campo uf deve ser preenchido.',
            'uf.min' => 'O campo uf deve ter no mínimo 3 caracteres.',
            'uf.max' => 'O campo SIGLA deve ter no máximo 5 caracteres.',
            'id_pais.required' => 'O campo Codigo País  deve ser preenchido.',
            'id_pais.integer' => 'O campo Codigo País deve ser um número inteiro.',
            'pais.required' => 'O campo País deve ser preenchido.',
        ];
        return $feedbacks;
    }
}
