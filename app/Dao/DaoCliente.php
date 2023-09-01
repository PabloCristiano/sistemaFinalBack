<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use App\Dao\DaoCidade;
use App\Dao\DaoCondicaoPagamento;
use Exception;

class DaoCliente implements Dao
{
    protected $daoCidade;
    protected $daoCondicaoPagemento;

    public function __construct()
    {
        $this->daoCidade = new DaoCidade();
        $this->daoCondicaoPagemento = new DaoCondicaoPagamento();
    }
    public function all(bool $json = true)
    {
        $itens = DB::select('select * from clientes order by id desc');
        $clientes = [];
        foreach ($itens as $item) {
            $cliente = $this->create(get_object_vars($item));
            if ($json) {
                $cliente_json = $this->getData($cliente);
                array_push($clientes, $cliente_json);
            } else {
                array_push($clientes, $cliente);
            }
        }
        return $clientes;
    }

    public function create(array $dados)
    {
        $cliente = new Cliente();

        if (isset($dados['id'])) {
            $cliente->setid($dados['id']);
            $cliente->setDataCadastro($dados['data_create'] ?? null);
            $cliente->setDataAlteracao($dados['data_alt'] ?? null);
        }

        $cliente->setNome((string) $dados['cliente']);
        $cliente->setApelido((string) $dados['apelido']);
        $cliente->setCpf((string) $dados['cpf']);
        $cliente->setRg((string) $dados['rg']);
        $cliente->setDataNasc($dados['dataNasc']);
        $cliente->setLogradouro((string) $dados['logradouro']);
        $cliente->setNumero((string) $dados['numero']);
        $cliente->setComplemento((string) $dados['complemento']);
        $cliente->setBairro((string) $dados['bairro']);
        $cliente->setCep((string) $dados['cep']);
        $cliente->setWhatsapp((string) $dados['whatsapp']);
        $cliente->setTelefone((string) $dados['telefone']);
        $cliente->setEmail((string) $dados['email']);
        $cliente->setSenha((string) $dados['senha']);
        $cliente->setConfSenha((string) $dados['confSenha']);
        $cliente->setTelefone((string) $dados['telefone']);
        $cliente->setObservacoes((string) $dados['observacao']);
        $cidade = $this->daoCidade->findById($dados['id_cidade'], false);
        $cidade = $this->daoCidade->create(get_object_vars($cidade));
        $cliente->setCidade($cidade);
        $condicaoPagamento = $this->daoCondicaoPagemento->findById($dados['id_condicao'], false);
        $condicaoPagamento = $this->daoCondicaoPagemento->listarCondicao(get_object_vars($condicaoPagamento));
        $cliente->setCondicaoPagamento($condicaoPagamento);
        return $cliente;
    }

    public function store($obj)
    {
        //$dados = $this->getData($obj);
        $cliente = $obj->getNome();
        $apelido = $obj->getApelido();
        $cpf = $obj->getCpf();
        $rg = $obj->getRg();
        $dataNasc = $obj->getDataNasc();
        $logradouro = $obj->getLogradouro();
        $numero = $obj->getNumero();
        $complemento = $obj->getComplemento();
        $bairro = $obj->getBairro();
        $cep = $obj->getCep();
        $id_cidade = $obj->getCidade()->getId();
        $id_condicao = $obj->getCondicaoPagamento()->getId();
        $whatsapp = $obj->getWhatsapp();
        $telefone = $obj->getTelefone();
        $email = $obj->getEmail();
        $senha = $obj->getSenha();
        $confSenha = $obj->getSenha();
        $observacao = $obj->getObservacoes();
        try {
            DB::beginTransaction();
            // DB::table('categorias')->insert($dados);
            // DB::INSERT("INSERT INTO clientes (categoria) VALUES ('$categoria')");
            DB::SELECT("INSERT INTO clientes (cliente,apelido,cpf,rg,dataNasc,logradouro,numero,complemento,bairro,cep,id_cidade,id_condicao,whatsapp,
             telefone,email,senha,confSenha,observacao) VALUES ('$cliente', '$apelido', '$cpf', '$rg', '$dataNasc', '$logradouro',$numero, '$complemento', '$bairro', '$cep', $id_cidade, $id_condicao, '$whatsapp',' $telefone', '$email', '$senha', '$confSenha', '$observacao')");
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            return $error;
            //return $th;
        }
    }

    public function update(Request $request, $id)
    {
        $obj = $this->create($request->all());
        $obj->setDataAlteracao(Carbon::now());
        $cliente = $obj->getNome();
        $apelido = $obj->getApelido();
        $cpf = $obj->getCpf();
        $rg = $obj->getRg();
        $dataNasc = $obj->getDataNasc();
        $logradouro = $obj->getLogradouro();
        $numero = $obj->getNumero();
        $complemento = $obj->getComplemento();
        $bairro = $obj->getBairro();
        $cep = $obj->getCep();
        $id_cidade = $obj->getCidade()->getId();
        $id_condicao = $obj->getCondicaoPagamento()->getId();
        $whatsapp = $obj->getWhatsapp();
        $telefone = $obj->getTelefone();
        $email = $obj->getEmail();
        $senha = $obj->getSenha();
        $confSenha = $obj->getSenha();
        $observacao = $obj->getObservacoes();
        $data_alt = $obj->getDataAlteracao();
        try {
            DB::beginTransaction();
            DB::UPDATE(
                'UPDATE
                        clientes
                        SET cliente = ?,apelido = ?,cpf = ?,rg = ?,dataNasc = ?,logradouro = ?,numero = ?,complemento = ?,bairro =?,
                        cep = ?,id_cidade = ?,id_condicao = ?,whatsapp = ?,
                        telefone = ?,email = ?,senha = ?,confSenha = ?,observacao = ?,data_alt = ?
                        WHERE  id = ?',
                [$cliente, $apelido, $cpf, $rg, $dataNasc, $logradouro, $numero, $complemento, $bairro, $cep, $id_cidade, $id_condicao, $whatsapp, $telefone, $email, $senha, $confSenha, $observacao, $data_alt, $id],
            );
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            return $error;
            //return $th;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            //DB::table('clientes')->delete($id);
            DB::DELETE("DELETE FROM  clientes WHERE id = '$id'");
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e->getCode() === '23000') {
                return $error = ['Não foi possível excluir, registro já vinculado.'];
            }
            return $error = ['Não foi possível excluir o registro.'];
        }
    }

    public function findById(int $id, bool $model = false)
    {
        if (!$model) {
            $dados = DB::select('select * from clientes where id = ?', [$id]);
            return $dados[0];
        }
        // $dados = DB::table('clientes')->where('id', $id)->first();
        $dados = DB::select('select * from clientes where id = ?', [$id]);
        if ($dados) {
            $clientes = [];
            foreach ($dados as $item) {
                $cliente = $this->create(get_object_vars($item));
                $cliente_json = $this->getData($cliente);
                array_push($clientes, $cliente_json);
            }

            return $clientes;
        }
    }

    public function getData(Cliente $cliente)
    {
        $dados = [
            'id' => $cliente->getid(),
            'cliente' => $cliente->getNome(),
            'apelido' => $cliente->getApelido(),
            'cpf' => $cliente->getCpf(),
            'rg' => $cliente->getRg(),
            'dataNasc' => $cliente->getDataNasc(),
            'logradouro' => $cliente->getLogradouro(),
            'numero' => $cliente->getNumero(),
            'complemento' => $cliente->getComplemento(),
            'bairro' => $cliente->getBairro(),
            'cep' => $cliente->getCep(),
            'cidade' => $this->daoCidade->getData($cliente->getCidade()),
            'whatsapp' => $cliente->getWhatsapp(),
            'telefone' => $cliente->getTelefone(),
            'email' => $cliente->getEmail(),
            'senha' => $cliente->getSenha(),
            'confSenha' => $cliente->getSenha(),
            'observacao' => $cliente->getObservacoes(),
            'condicao_pagemento' => $this->daoCondicaoPagemento->getData($cliente->getCondicaoPagamento()),
            'data_create' => $cliente->getDataCadastro(),
            'data_alt' => $cliente->getDataAlteracao(),
        ];
        return $dados;
    }
    public function verificaSenha($ObjCliente, $senha)
    {
        $password = $senha;
        $hashedPassword = $ObjCliente;
        if (Hash::check($password, $hashedPassword)) {
            return true;
        } else {
            return false;
        }
    }
}
