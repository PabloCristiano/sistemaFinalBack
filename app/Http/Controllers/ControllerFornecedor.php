<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dao\DaoFornecedor;
use Illuminate\Support\Facades\Response;
use App\Rules\Cnpj;
use App\Rules\Cpf;

class ControllerFornecedor extends Controller
{
    private $daoFornecedores;
    public function __construct()
    {
        $this->daoFornecedores = new DaoFornecedor();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fornecedores = $this->daoFornecedores->all(true);
        return response()->json($fornecedores, 200);
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
        $requestData = $request->all();
        if ($request->tipo_pessoa === 'JURIDICA') {
            $requestData['cpf'] = null;
            $requestData['apelido'] = null;
        } else {
            $requestData['cnpj'] = null;
            $requestData['nomefantasia'] = null;
        }
        $fornecedor = $this->daoFornecedores->create($requestData);
        $store = $this->daoFornecedores->store($fornecedor);
        if ($store[0] === 'Sucess') {
            return response()->json(['success' => 'Fornecedor Cadastrado com Sucesso', 'obj' => $store]);
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
        $regras['razaoSocial'] = 'required|min:3|max:50';
        unset($regras['cnpj'][2]);
        unset($regras['cpf'][2]);
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $requestData = $request->all();
        if ($request->tipo_pessoa === 'JURIDICA') {
            $requestData['cpf'] = "";
            $requestData['apelido'] = "";
        }
        if ($request->tipo_pessoa === 'FISICA') {
            $requestData['cnpj'] = "";
            $requestData['nomefantasia'] = "";
        } 
        $fornecedor = $this->daoFornecedores->findById($id,false);
        if ($fornecedor === null) {
            return response()->json(['erro' => 'Fornecedor não encontrado, Impossível realizar atualização'], 404);
        }
        $update = $this->daoFornecedores->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Fornecedor Alterado com Sucesso.'], 200);
        }
        if ($update['error']){
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
        $delete = $this->daoFornecedores->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Fornecedor excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['erro' => $delete], 404);
        }
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $fornecedor = $this->daoFornecedores->findById($id, true);
            if ($fornecedor) {
                return response()->json($fornecedor, 200);
            }
        }
        return response()->json(['error' => 'Fornecedor não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $cnpj = new Cnpj();
        $cpf = new Cpf();
        $regras = [
            'tipo_pessoa' => 'required',
            'razaoSocial' => 'required|min:3|max:50|unique:fornecedores',
            'nomefantasia' => 'required|min:3|max:50',
            'apelido' => 'required|min:3|max:50',
            'logradouro' => 'required|min:3|max:50',
            'numero' => 'required|max:10',
            'complemento' => 'nullable|min:3|max:50',
            'bairro' => 'required|min:3|max:50',
            'cep' => 'required|min:9|max:9',
            'id_cidade' => 'required|integer',
            'cidade' => 'required|min:3|max:50',
            'whatsapp' => 'nullable|min:15|max:15',
            'telefone' => 'required|min:15|max:15',
            'email' => 'nullable|email|max:50',
            'pagSite' => 'nullable|url|max:50',
            'contato' => 'nullable|min:3|max:50',
            'cnpj' => ['required', $cnpj, 'unique:fornecedores'],
            'ie' => 'nullable|max:14',
            'cpf' => ['required', $cpf, 'unique:fornecedores'],
            'rg' => 'nullable|max:9',
            'id_condicaopg' => 'required|integer',
            'condicaopg' => 'required',
            'limiteCredito' => 'nullable|max:10000000',
            'obs' => 'nullable|min:5|max:255',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'tipo_pessoa.required' => 'O campo Tipo Pessoa deve ser preenchido.',
            'razaoSocial.required' => 'O campo Razão Social deve ser preenchido.',
            'razaoSocial.min' => 'Razão Social deve ter no mínimo 3 caracteres.',
            'razaoSocial.max' => 'Razão Social deve ter no máximo 50 caracteres.',
            'razaoSocial.unique' => 'Razão Social já Cadastrada !',
            'nomefantasia.required' => 'Nome Fantasia deve ser preenchido.',
            'nomefantasia.min' => 'Nome Fantasia deve ter no mínimo 3 caracteres.',
            'nomefantasia.max' => 'Nome Fantasia deve ter no máximo 50 caracteres.',
            'apelido.required' => 'Apelido deve ser preenchido.',
            'apelido.min' => 'Apelido deve ter no mínimo 3 caracteres.',
            'apelido.max' => 'Apelido deve ter no máximo 50 caracteres.',
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
            'pagSite.url' => 'Site deve ser uma Url Ex: http://www.exemplo.com .',
            'pagSite.max' => 'Site deve ter no máximo 50 caracteres.',
            'contato.min' => 'Contato deve ter no mínimo 3 caracteres.',
            'contato.max' => 'Contato deve ter no máximo 50 caracteres.',
            'cnpj.required' => 'CNPJ deve ser preenchido.',
            'cnpj.unique' => 'CNPJ já Cadastrado !',
            'ie.min' => 'Inscrição Estadual deve ter no mínimo 14 caracteres.',
            'ie.max' => 'Inscrição Estadual deve ter no máximo 14 caracteres.',
            'cpf.required' => 'CPF deve ser preenchido.',
            'cpf.unique' => 'CPF já Cadastrado !',
            'rg.min' => 'RG deve ter no mínimo 9 caracteres.',
            'rg.max' => 'RG deve ter no máximo 9 caracteres.',
            'id_condicaopg.required' => 'O Código Condição Pagamento deve ser preenchido.',
            'id_condicaopg.integer' => 'O Código Condição Pagamento deve ser um número inteiro.',
            'condicaopg.required' => 'O Condição Pagamento deve ser preenchido.',
            'limiteCredito.gt' => 'Limite de Credito deve conter um Zero.',
            'obs.min' => 'Observação deve ter no mínimo 5 caracteres.',
            'obs.max' => 'Observação deve ter no máximo 255 caracteres.',
        ];
        return $feedbacks;
    }
}
