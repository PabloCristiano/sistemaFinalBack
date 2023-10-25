<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Models\Fornecedor;
use App\Dao\DaoCidade;
use App\Dao\DaoCondicaoPagamento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;

class DaoFornecedor implements Dao
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
        $itens = DB::select('select * from fornecedores order by id desc');
        $fornecedores = [];
        foreach ($itens as $item) {
            $fornecedor = $this->create(get_object_vars($item));
            if ($json) {
                $fornecedor_json = $this->getData($fornecedor);
                array_push($fornecedores, $fornecedor_json);
            } else {
                array_push($fornecedores, $fornecedor);
            }
        }
        return $fornecedores;
    }

    public function create(array $dados)
    {
        $fornecedor = new Fornecedor();
        if (isset($dados['id'])) {
            $fornecedor->setId($dados['id']);
            $fornecedor->setDataCadastro($dados['data_create'] ?? null);
            $fornecedor->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $fornecedor->setRazaoSocial((string) $dados['razaoSocial']);
        $fornecedor->setTipoPessoa((string) $dados['tipo_pessoa']);
        $fornecedor->setNomeFantasia((string) $dados['nomefantasia']);
        $fornecedor->setNome((string) $dados['apelido']);
        $fornecedor->setLogradouro((string) $dados['logradouro']);
        $fornecedor->setNumero((string) $dados['numero']);
        $fornecedor->setComplemento((string) $dados['complemento']);
        $fornecedor->setBairro((string) $dados['bairro']);
        $fornecedor->setCep((string) $dados['cep']);
        $fornecedor->setWhatsapp((string) $dados['whatsapp']);
        $fornecedor->setTelefone((string) $dados['telefone']);
        $fornecedor->setEmail((string) $dados['email']);
        $fornecedor->setPagSite((string) $dados['pagSite']);
        $fornecedor->setContato((string) $dados['contato']);

        if (isset($dados['cnpj'])) {
            $fornecedor->setCnpj((string) $dados['cnpj']);
        }
        if (isset($dados['cnpj'])) {
            $fornecedor->setInscricaoEstadual((string) $dados['ie']);
        }
        if (isset($dados['cpf'])) {
            $fornecedor->setCpf((string) $dados['cpf']);
        }
        if (isset($dados['rg'])) {
            $fornecedor->setRg((string) $dados['rg']);
        }
        if ($dados['tipo_pessoa'] === 'FISICA') {
            $fornecedor->setNomeFantasia((string) $dados['apelido']);
        }

        $fornecedor->setLimiteCredito((float) $dados['limiteCredito']);
        $fornecedor->setObservacoes((string) $dados['obs']);
        $cidade = $this->daoCidade->findById($dados['id_cidade'], false);
        $cidade = $this->daoCidade->create(get_object_vars($cidade));
        $fornecedor->setCidade($cidade);
        $condicaoPagamento = $this->daoCondicaoPagemento->findById($dados['id_condicaopg'], false);
        $condicaoPagamento = $this->daoCondicaoPagemento->listarCondicao(get_object_vars($condicaoPagamento));
        $fornecedor->setCondicaoPagamento($condicaoPagamento);
        return $fornecedor;
    }

    public function store($obj)
    {
        $data = $this->setData($obj);
        $tipo_pessoa = $obj->getTipoPessoa();
        $razaoSocial = $obj->getRazaoSocial();
        $nomeFantasia = $obj->getNomeFantasia();
        $apelido = $obj->getNome();
        $logradouro = $obj->getLogradouro();
        $numero = $obj->getNumero();
        $complemento = $obj->getComplemento();
        $bairro = $obj->getBairro();
        $cep = $obj->getCep();
        $id_cidade = $obj->getCidade()->getId();
        $whatsapp = $obj->getWhatsapp();
        $telefone = $obj->getTelefone();
        $email = $obj->getEmail();
        $pagSite = $obj->getPagSite();
        $contato = $obj->getContato();
        $cnpj = $obj->getCnpj();
        $ie = $obj->getInscricaoEstadual();
        $cpf = $obj->getCpf();
        $rg = $obj->getRg();
        $id_condicaopg = $obj->getCondicaoPagamento()->getId();
        $limiteCredito = $obj->getLimiteCredito();
        $obs = $obj->getObservacoes();
        DB::beginTransaction();
        try {
            //DB::table('estados')->insert($dados);
            DB::INSERT("INSERT INTO fornecedores (tipo_pessoa,razaoSocial,nomefantasia,apelido,
                logradouro,numero,complemento,bairro,cep,id_cidade,whatsapp,telefone,email,pagSite,
                contato,cnpj,ie,cpf,rg,id_condicaopg,limiteCredito,obs) VALUES('$tipo_pessoa','$razaoSocial',
            '$nomeFantasia','$apelido','$logradouro','$numero','$complemento','$bairro','$cep',$id_cidade,
            '$whatsapp','$telefone','$email','$pagSite','$contato','$cnpj','$ie','$cpf','$rg',
            $id_condicaopg,'$limiteCredito','$obs')");
            DB::commit();
            $ultimoFornecedor = DB::table('fornecedores')
                ->get()
                ->last();
            return ['success', 'Fornecedor Cadastrado com Sucesso !', $ultimoFornecedor];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error', $e];
        }
    }

    public function update(Request $request, $id)
    {
        $fornecedor = $this->create($request->all());
        $fornecedor->setDataAlteracao(Carbon::now());
        $tipo_pessoa = $fornecedor->getTipoPessoa();
        $razaoSocial = $fornecedor->getRazaoSocial();
        $nomeFantasia = $fornecedor->getNomeFantasia();
        $apelido = $fornecedor->getNome();
        $logradouro = $fornecedor->getLogradouro();
        $numero = $fornecedor->getNumero();
        $complemento = $fornecedor->getComplemento();
        $bairro = $fornecedor->getBairro();
        $cep = $fornecedor->getCep();
        $id_cidade = $fornecedor->getCidade()->getId();
        $whatsapp = $fornecedor->getWhatsapp();
        $telefone = $fornecedor->getTelefone();
        $email = $fornecedor->getEmail();
        $pagSite = $fornecedor->getPagSite();
        $contato = $fornecedor->getContato();
        $cnpj = $fornecedor->getCnpj();
        $ie = $fornecedor->getInscricaoEstadual();
        $cpf = $fornecedor->getCpf();
        $rg = $fornecedor->getRg();
        $id_condicaopg = $fornecedor->getCondicaoPagamento()->getId();
        $limiteCredito = $fornecedor->getLimiteCredito();
        $obs = $fornecedor->getObservacoes();
        $data_alt = $fornecedor->getDataAlteracao();
         DB::beginTransaction();
        try {
            // DB::UPDATE("UPDATE fornecedores SET tipo_pessoa = '$tipo_pessoa',razaoSocial = '$razaoSocial',nomefantasia = '$nomeFantasia',apelido = '$apelido',logradouro = '$logradouro',numero = '$numero',complemento = '$complemento',bairro = '$bairro', cep = '$cep',id_cidade = $id_cidade,whatsapp = '$whatsapp',telefone = '$telefone',email = '$email',pagSite = '$pagSite',contato = '$contato',cnpj = '$cnpj',ie = '$ie',cpf = '$cpf',rg = '$rg',id_condicaopg = $id_condicaopg , limiteCredito = $limiteCredito,obs = '$obs',data_alt = '$data_alt' where id = $id ");
            DB::UPDATE('UPDATE 
                         fornecedores SET tipo_pessoa = ?,razaoSocial = ?,nomefantasia = ?, apelido = ?,logradouro = ?,  numero = ?, complemento = ?,bairro = ?, 
                         cep = ?,id_cidade = ?,whatsapp = ?,telefone = ?,email = ?,pagSite = ?,contato = ?,cnpj = ?,ie = ?,cpf = ?,rg = ?,id_condicaopg = ?,limiteCredito = ?, 
                         obs = ?,data_alt = ? where id = ?', [
                $tipo_pessoa, $razaoSocial, $nomeFantasia, $apelido, $logradouro, $numero, $complemento, $bairro, $cep, $id_cidade, $whatsapp,
                $telefone, $email, $pagSite, $contato, $cnpj, $ie, $cpf, $rg, $id_condicaopg, $limiteCredito, $obs, $data_alt, $id
            ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = ['error' => $e->getMessage(), 'CodigoError' => $e->getCode()];
            return $error;
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            // DB::table('fornecedores')->delete($id);
            DB::DELETE("DELETE FROM  fornecedores where id = '$id'");
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
        try {
            if (!$model) {
                $dados = DB::select('select * from fornecedores where id = ?', [$id]);
                return $dados[0];
            }
            //$dados = DB::table('fornecedores')->where('id', $id)->first();
            $dados = DB::select('select * from fornecedores where id = ?', [$id]);
            if ($dados) {
                $fornecedores = [];
                foreach ($dados as $item) {
                    $fornecedor = $this->create(get_object_vars($item));
                    $fornecedor_json = $this->getData($fornecedor);
                    array_push($fornecedores, $fornecedor_json);
                }
                return $fornecedores;
            }
        } catch (\Exception $e) {
            if ($e->getCode() === '23000') {
                return $error = ['Não foi possível excluir, registro já vinculado.'];
            }
            return null;
        }
    }

    public function getData(Fornecedor $fornecedor)
    {
        
        $dados = [
            'id' => $fornecedor->getId(),
            'tipo_pessoa' => $fornecedor->getTipoPessoa(),
            'razaoSocial' => $fornecedor->getRazaoSocial(),
            'nomefantasia' => $fornecedor->getNomeFantasia(),
            'apelido' => $fornecedor->getNome(),
            'logradouro' => $fornecedor->getLogradouro(),
            'numero' => $fornecedor->getNumero(),
            'complemento' => $fornecedor->getComplemento(),
            'bairro' => $fornecedor->getBairro(),
            'cep' => $fornecedor->getCep(),
            'cidade' => $this->daoCidade->getData($fornecedor->getCidade()),
            'whatsapp' => $fornecedor->getWhatsapp(),
            'telefone' => $fornecedor->getTelefone(),
            'email' => $fornecedor->getEmail(),
            'pagSite' => $fornecedor->getPagSite(),
            'contato' => $fornecedor->getContato(),
            'cnpj' => $fornecedor->getCnpj(),
            'ie' => $fornecedor->getInscricaoEstadual(),
            'cpf' => $fornecedor->getCpf(),
            'rg' => $fornecedor->getRg(),
            'condicao_pagemento' => $this->daoCondicaoPagemento->getData($fornecedor->getCondicaoPagamento()),
            'limiteCredito' => $fornecedor->getLimiteCredito(),
            'obs' => $fornecedor->getObservacoes(),
            'data_create' => $fornecedor->getDataCadastro(),
            'data_alt' => $fornecedor->getDataAlteracao(),
        ];
        return $dados;
    }

    public function setData(Fornecedor $fornecedor)
    {
        $data = [
            'tipo_pessoa' => $fornecedor->getTipoPessoa(),
            'razaoSocial' => $fornecedor->getRazaoSocial(),
            'nomeFantasia' => $fornecedor->getNomeFantasia(),
            'apelido' => $fornecedor->getNome(),
            'logradouro' => $fornecedor->getLogradouro(),
            'numero' => $fornecedor->getNumero(),
            'complemento' => $fornecedor->getComplemento(),
            'bairro' => $fornecedor->getBairro(),
            'cep' => $fornecedor->getCep(),
            'id_cidade' => $fornecedor->getCidade()->getId(),
            'whatsapp' => $fornecedor->getWhatsapp(),
            'telefone' => $fornecedor->getTelefone(),
            'email' => $fornecedor->getEmail(),
            'pagSite' => $fornecedor->getPagSite(),
            'contato' => $fornecedor->getContato(),
            'cnpj' => $fornecedor->getCnpj(),
            'ie' => $fornecedor->getInscricaoEstadual(),
            'cpf' => $fornecedor->getCpf(),
            'rg' => $fornecedor->getRg(),
            'id_condicaopg' => $fornecedor->getCondicaoPagamento()->getId(),
            'limiteCredito' => $fornecedor->getLimiteCredito(),
            'obs' => $fornecedor->getObservacoes(),
        ];
        return $data;
    }
}
