<?php

namespace App\Http\Controllers;

use App\Dao\DaoPais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ControllerPais extends Controller
{
    private $daoPais;
    public function __construct()
    {
        $this->daoPais = new DaoPais();
    }

    public function index(Request $request)
    {
        $paises = $this->daoPais->all(true);
        return response()->json($paises, 200);
    }

    public function store(Request $request)
    {
        $regras = $this->rules();
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $pais = $this->daoPais->create($request->all());
        $store = $this->daoPais->store($pais);
        if ($store) {
            return response()->json(['success' => 'Pais Cadastrado com Sucesso', 'obj' => $store]);
        } else {
            return response::json(['icon' => 'error', 'title' => 'Pais não Cadastrado...']);
        }
    }

    public function update(Request $request, $id)
    {
        $regras = $this->rules();
        $regras['pais'] = 'required|min:3|max:40';
        $regras['sigla'] = 'required|min:2|max:3';
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $pais = $this->daoPais->findById($id, false);
        if ($pais === null) {
            return response()->json(['erro' => 'Impossível realizar  atualização'], 404);
        }
        $update = $this->daoPais->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'País Alterado com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }

    public function destroy($id)
    {
        $delete = $this->daoPais->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Pais excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['erro' => $delete], 404);
        }
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $pais = $this->daoPais->findById($id, false);
            if ($pais) {
                return response()->json($pais, 200);
            }
        }
        return response()->json(['error' => 'Pais não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'pais' => 'required|min:3|max:40|unique:paises',
            'sigla' => 'required|min:2|max:3|unique:paises',
            'ddi' => 'required|integer',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'pais.required' => 'O campo PAÍS deve ser preenchido.',
            'pais.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'pais.max' => 'O campo nome deve ter no máximo 40 caracteres.',
            'pais.unique' => 'PAÍS já Cadastrado !',
            'sigla.unique' => 'SIGLA já Cadastrada !',
            'sigla.required' => 'O campo SIGLA deve ser preenchido.',
            'sigla.min' => 'O campo SIGLA deve ter no mínimo 3 caracteres.',
            'sigla.max' => 'O campo SIGLA deve ter no máximo 3 caracteres.',
            'ddi.required' => 'O campo DDI deve ser preenchido.',
            'ddi.integer' => 'O campo DDI deve ser um número inteiro.',
        ];
        return $feedbacks;
    }
}
