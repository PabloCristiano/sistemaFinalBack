<?php
namespace App\Http\Controllers;
use App\Dao\DaoFormasPagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ControllerFormasPagamento extends Controller
{
    private $daoFormasPagamento;
    public function __construct()
    {
        $this->daoFormasPagamento = new DaoFormasPagamento();
    }

    public function index(Request $request)
    {
        $formasPagamento = $this->daoFormasPagamento->all(true);
        return response()->json($formasPagamento, 200);
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
        $formasPagamento = $this->daoFormasPagamento->create($request->all());
        $store = $this->daoFormasPagamento->store($formasPagamento);
        if ($store) {
            return response()->json(['success' => 'Forma de Pagamento Cadastrado com Sucesso', 'obj' => $store]);
        } else {
            return response::json(['error' => 'Forma de Pagamento não Cadastrado...']);
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
        $regras['forma_pg'] = 'required|min:3|max:40';
        $feedbacks = $this->feedbacks();
        $request->validate($regras, $feedbacks);
        $formasPagamento = $this->daoFormasPagamento->findById($id, false);
        if ($formasPagamento === null) {
            return response()->json(['erro' => 'Impossível realizar  atualização'], 404);
        }
        $update = $this->daoFormasPagamento->update($request, $id);
        if ($update === true) {
            return response()->json(['success' => 'Forma de Pagamento Alterada com Sucesso.'], 200);
        }
        if ($update['error']) {
            return response()->json(['erro' => $update], 404);
        }
    }

    public function destroy($id)
    {
        $delete = $this->daoFormasPagamento->delete($id);
        if ($delete === true) {
            return response()->json(['success' => 'Forma de Pagamento excluído com sucesso.'], 200);
        }
        if ($delete) {
            return response()->json(['erro' => $delete], 404);
        }
    }

    public function getByid($id)
    {
        if (ctype_digit(strval($id))) {
            $formasPagamento = $this->daoFormasPagamento->findById($id, true);
            if ($formasPagamento) {
                return response()->json($formasPagamento, 200);
            }
        }
        return response()->json(['error' => 'Forma de Pagamento não Cadastrado...'], 400);
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            'forma_pg' => 'required|min:3|max:40|unique:forma_pg',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            'forma_pg.required' => 'O campo Forma de Pagamento deve ser preenchido.',
            'forma_pg.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'forma_pg.max' => 'O campo nome deve ter no máximo 40 caracteres.',
            'forma_pg.unique' => 'Forma de Pagamento já Cadastrada!',
        ];
        return $feedbacks;
    }
}
