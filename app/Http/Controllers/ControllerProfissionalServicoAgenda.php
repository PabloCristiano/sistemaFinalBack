<?php

namespace App\Http\Controllers;

use App\Dao\DaoProfissionalServicoAgenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Exception;
use InvalidArgumentException;
use PhpParser\Node\Stmt\TryCatch;

class ControllerProfissionalServicoAgenda extends Controller
{
    protected $daoProfissionalServicoAgenda;
    public function __construct()
    {
        $this->daoProfissionalServicoAgenda = new DaoProfissionalServicoAgenda();
    }
    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $agendamendo = [];
        $agendamendo = $request->agenda;
        //$agendamendo = json_decode($request->agenda, true);
        try {
            $regrasagenda = $this->rules();
            $feedbacksagenda = $this->feedbacks();

            if (!is_array($agendamendo)) {
                throw new InvalidArgumentException('Agendamento deve ser um array válido.');
            }

            $validator = Validator::make($agendamendo, $regrasagenda,  $feedbacksagenda);
            // dd($validator->fails());
            if ($validator->fails()) {
                $erros = $validator->errors();
                $mensagensOrganizadas = [];
                // Iterar pelas mensagens de erro e agrupar
                foreach ($erros->messages() as $chave => $mensagens) {
                    list($posicao, $campo) = explode('.', $chave, 2);
                    $posicaoAgenda = $posicao + 1; // Adiciona 1 para a referência do tipo "produto"
                    $mensagensOrganizadas[$posicaoAgenda][$campo][] = $mensagens;
                }
                // Reorganizar para o formato desejado
                $errosAgenda = [];
                foreach ($mensagensOrganizadas as $posicaoAgenda => $mensagensPorCampo) {
                    foreach ($mensagensPorCampo as $campo => $mensagens) {
                        $errosAgenda[$posicaoAgenda][$campo] = $mensagens;
                    }
                }
                //Se tiver erros Retorna a request
                if (!empty($errosAgenda)) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'produtos' => $errosAgenda
                        ]
                    ], 422);
                }
            }
        } catch (InvalidArgumentException $e) {
            // Lidar com o erro de tipo inválido
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            // Lidar com outras exceções se necessário
            return response()->json(['error' => 'Something went wrong'], 500);
        }
        //$agenda = $this->daoProfissionalServicoAgenda->create($agendamendo);
        $store = $this->daoProfissionalServicoAgenda->store($agendamendo);
        if ($store === true) {
            return response::json(['success' => true, 'message' => 'Agenda Criada com Sucesso'], 200);
        } else {
            return response::json(['success' => false, 'message' => 'Agenda Não pode ser Criada com Sucesso'], 400);
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
        //
    }


    public function destroy($id)
    {
        //
    }

    //regras de validação
    public function rules()
    {
        $regras = [
            '*.id_profissional' => 'required|integer',
            '*.profissional' => 'required|min:3|max:50',
            '*.data' => 'required|date_format:d/m/Y',
            '*.horario_inicio' => 'required|date_format:H:i:s',
        ];
        return $regras;
    }
    //mensagens das regras de validação
    public function feedbacks()
    {
        $feedbacks = [
            '*.id_profissional.required' => 'O campo ID do profissional é obrigatório.',
            '*.id_profissional.integer' => 'O campo ID do profissional deve ser um número inteiro.',
            '*.profissional.required' => 'O campo nome do profissional é obrigatório.',
            '*.profissional.min' => 'O campo nome do profissional deve ter pelo menos :min caracteres.',
            '*.profissional.max' => 'O campo nome do profissional não pode ter mais de :max caracteres.',
            '*.data.required' => 'O campo data é obrigatório.',
            '*.data.date_format' => 'O campo data deve estar no formato dd/mm/aaaa.',
            '*.horario_inicio.required' => 'O campo horário de início é obrigatório.',
            '*.horario_inicio.date_format' => 'O campo horário de início deve estar no formato HH:MM:SS.',
        ];
        return $feedbacks;
    }
}
