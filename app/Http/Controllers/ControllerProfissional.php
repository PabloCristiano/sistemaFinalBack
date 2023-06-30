<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Dao\DaoProfissional;
use Illuminate\Support\Facades\Response;
use App\Rules\Cpf;
class ControllerProfissional extends Controller
{
    private $daoProfissional;

    public function __construct()
    {
        $this->daoProfissional = new DaoProfissional();
    }
    public function index(Request $request)
    {
        $profissional = $this->daoProfissional->all(true);
        return response()->json($profissional, 200);
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
        $store = $this->daoProfissional->storeProfissional($request);
        if ($store === true) {
            return response()->json(['success', 'Profissional Cadastrado com Sucesso'], 200);
        } else {
            return response::json(['error', 'Profissional não Cadastrado...'], 200);
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
        $regras['profissional'] = 'required|min:3|max:50';
        unset($regras['cpf'][2]);
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $profissional = $this->daoProfissional->findById($id, false);
    
        if ($profissional === null) {
            return response()->json(['erro' => 'Profissional não encontrado, Impossível realizar atualização'], 404);
        }
        $update = $this->daoProfissional->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Profissional Alterado com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }

    public function destroy($id)
    {
        $delete = $this->daoProfissional->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Profissional excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['erro' => $delete], 404);
        }
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $profissional = $this->daoProfissional->findById($id, true);
            if ($profissional) {
                return response()->json($profissional, 200);
            }
        }
        return response()->json(['error' => 'Profissional não Cadastrado...'], 400);
    }

    public function rules()
    {
        $cpf = new Cpf();
        $regras = [
            'profissional' => 'required|min:3|max:50|unique:profissionais',
            'apelido' => 'required|min:3|max:50',
            'cpf' => ['required', $cpf, 'unique:profissionais'],
            'rg' => 'nullable|min:9|max:9',
            'dataNasc' => 'required|date|date_format:Y-m-d|before:-18 years',
            'logradouro' => 'required|min:3|max:50',
            'numero' => 'required|max:10',
            'complemento' => 'nullable|min:3|max:50',
            'bairro' => 'required|min:3|max:50',
            'cep' => 'required|min:9|max:9',
            'id_cidade' => 'required|integer',
            'cidade' => 'required|min:3|max:50',
            'whatsapp' => 'nullable|min:15|max:15',
            'telefone' => 'required|min:15|max:15',
            'senha' => 'required|max:255',
            'confSenha' => 'required|max:255',
            'email' => 'nullable|email|max:50',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'profissional.required' => 'O campo profissional deve ser preenchido.',
            'profissional.min' => 'Profissional deve ter no mínimo 3 caracteres.',
            'profissional.max' => 'Profissional deve ter no máximo 50 caracteres.',
            'profissional.unique' => 'Profissional já Cadastrado !',
            'apelido.required' => 'Campo Apelido deve ser preenchido.',
            'apelido.min' => 'Apelido deve ter no mínimo 3 caracteres.',
            'apelido.max' => 'Apelido Fantasia deve ter no máximo 50 caracteres.',
            'cpf.required' => 'CPF deve ser preenchido.',
            'cpf.unique' => 'CPF já Cadastrado !',
            'rg.min' => 'RG deve ter no mínimo 9 caracteres.',
            'rg.max' => 'RG deve ter no máximo 9 caracteres.',
            'dataNasc.required' => 'O Data Nascimento deve ser preenchido.',
            'dataNasc.before' => 'O cliente deve ser maior de 18 de anos.',
            'logradouro.required' => 'Logradouro deve ser preenchido.',
            'logradouro.min' => 'Logradouro deve ter no mínimo 3 caracteres.',
            'logradouro.max' => 'Logradouro deve ter no máximo 50 caracteres.',
            'numero.required' => 'Número deve ser preenchido.',
            'numero.max' => 'Número deve ter no máximo 10 caracteres.',
            'complemento.min' => 'Complemento deve ter no mínimo 3 caracteres.',
            'complemento.max' => 'Complemento deve ter no máximo 50 caracteres.',
            'bairro.required' => 'Bairro deve ser preenchido.',
            'bairro.min' => 'Bairro deve ter no mínimo 3 caracteres.',
            'bairro.max' => 'Bairro deve ter no máximo 50 caracteres.',
            'cep.required' => 'CEP deve ser preenchido.',
            'cep.min' => 'CEP deve ter no mínimo 9 caracteres.',
            'cep.max' => 'CEP deve ter no máximo 9 caracteres.',
            'id_cidade.required' => 'Codigo Cidade deve ser preenchido.',
            'id_cidade.integer' => 'Codigo Cidade deve um numero inteiro.',
            'cidade.required' => 'Cidade deve ser preenchido.',
            'cidade.min' => 'Cidade deve ter no mínimo 3 caracteres.',
            'cidade.max' => 'Cidade deve ter no máximo 50 caracteres.',
            'whatsapp.min' => 'WhatsApp deve ter no mínimo 15 caracteres.',
            'whatsapp.max' => 'WhatsApp deve ter no máximo 15 caracteres.',
            'telefone.required' => 'Telefone deve ser preenchido.',
            'telefone.min' => 'Telefone ter no mínimo 15 caracteres.',
            'telefone.max' => 'Telefone deve ter no máximo 15 caracteres.',
            'email.email' => 'Deve ser um e-mail válido.',
            'email.max' => 'E-mail deve ter no máximo 50 caracteres.',
        ];
        return $feedbacks;
    }
}
