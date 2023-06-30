<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dao\DaoCategorias;
use Illuminate\Support\Facades\Response;

class ControllerCategorias extends Controller
{
    private $daoCategorias;

    public function __construct()
    {
        $this->daoCategorias = new DaoCategorias();
    }
    public function index(Request $request)
    {
        $categorias = $this->daoCategorias->all(true);
        return response()->json($categorias, 200);
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
        $categoria = $this->daoCategorias->create($request->all());
        $store = $this->daoCategorias->store($categoria);
        if ($store) {
            return response()->json(['success' => 'Categoria Cadastrado com Sucesso', 'obj' => $store]);
        } else {
            return response::json(['error' => 'Categoria não Cadastrado...']);
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
        $regras['categoria'] = 'required|min:3|max:40';
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $categoria = $this->daoCategorias->findById($id, false);
        if ($categoria === null) {
            return response()->json(['erro' => 'Impossível realizar  atualização'], 404);
        }
        $update = $this->daoCategorias->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Categoria Alterada com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }

    public function destroy($id)
    {
        $delete = $this->daoCategorias->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Categoria excluída com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['erro' => $delete], 404);
        }
    }
    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $categoria = $this->daoCategorias->findById($id, true);
            if ($categoria) {
                return response()->json($categoria, 200);
            }
        }
        return response()->json(['error' => 'Categoria não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'categoria' => 'required|min:3|max:40|unique:categorias',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'categoria.required' => 'O campo Categoria deve ser preenchido.',
            'categoria.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'categoria.max' => 'O campo nome deve ter no máximo 40 caracteres.',
            'categoria.unique' => 'Categoria já Cadastrada!',
        ];
        return $feedbacks;
    }
}
