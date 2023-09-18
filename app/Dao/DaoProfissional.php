<?php
namespace App\Dao;
use App\Dao\Dao;
use App\Dao\DaoCidade;
use App\Dao\DaoServico_Profissional;
use App\Models\Profissional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Exception;

class DaoProfissional implements Dao
{
    private $daoCidade;
    private $daoServicoProfissional;
    public function __construct()
    {
        $this->daoCidade = new DaoCidade();
        $this->daoServicoProfissional = new DaoServico_Profissional();
    }

    public function all(bool $json = true)
    {
        $itens = DB::select('select * from profissionais order by id desc');
        $profissionais = [];
        foreach ($itens as $item) {
            $profissional = $this->create(get_object_vars($item));
            if ($json) {
                $profissionais_json = $this->getData($profissional);
                array_push($profissionais, $profissionais_json);
            } else {
                array_push($profissionais, $profissional);
            }
        }
        return $profissionais;
    }

    public function create(array $dados)
    {
        $profissional = new Profissional();
        if (isset($dados['id'])) {
            $profissional->setId($dados['id']);
            $profissional->setDataCadastro($dados['data_create'] ?? null);
            $profissional->setDataAlteracao($dados['data_alt'] ?? null);
        }

        $profissional->setNome($dados['profissional']);
        $profissional->setApelido($dados['apelido']);
        $profissional->setCpf($dados['cpf']);
        $profissional->setRg($dados['rg']);
        $profissional->setDataNasc($dados['dataNasc']);
        $profissional->setLogradouro($dados['logradouro']);
        $profissional->setNumero($dados['numero']);
        $profissional->setComplemento($dados['complemento']);
        $profissional->setBairro($dados['bairro']);
        $profissional->setCep($dados['cep']);
        $cidade = $this->daoCidade->findById($dados['id_cidade'], false);
        $cidade = $this->daoCidade->create(get_object_vars($cidade));
        $profissional->setCidade($cidade);
        $profissional->setWhatsapp($dados['whatsapp']);
        $profissional->setTelefone($dados['telefone']);
        $profissional->setEmail($dados['email']);
        $profissional->setSenha($dados['senha']);
        $profissional->setTipoProf($dados['tipoProf']);
        if (isset($dados['id'])) {
            $profissional->setServico($dados['id']);
        }
        // else{
        //     $array = json_decode($dados['servico'], true);
        //     //dd($array, "oiii");
        // }
        $profissional->setComissao($dados['comissao'] ?? 0);
        $profissional->setQtdServico($dados['qtd_servico']);
        return $profissional;
    }

    public function store($obj)
    {
    }

    public function storeProfissional(Request $request)
    {
        $array = $request->servico;
        $array = json_decode($array, true);
        //dd($array);
        $obj = $this->create($request->all());
        $profissional = $obj->getNome();
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
        $whatsapp = $obj->getWhatsapp();
        $telefone = $obj->getTelefone();
        $email = $obj->getEmail();
        $senha = bcrypt($obj->getSenha());
        $confSenha = bcrypt($obj->getSenha());
        $tipoProf = $obj->getTipoProf();
        $comissao = $obj->getComissao();
        $qtd_servico = $obj->getQtdServico();
        $password = $senha;
        try {
            DB::beginTransaction();
            DB::SELECT("INSERT INTO profissionais (profissional,apelido,cpf,rg,dataNasc,logradouro,numero,complemento,bairro,cep,id_cidade,whatsapp,
             telefone,email,senha,confSenha,tipoProf,comissao,qtd_servico,password) VALUES ('$profissional', '$apelido', '$cpf', '$rg', '$dataNasc', '$logradouro',$numero, '$complemento', '$bairro', '$cep', $id_cidade,'$whatsapp','$telefone', '$email', '$senha', '$confSenha','$tipoProf',$comissao,$qtd_servico,'$password')");
            $idProfissional = DB::getPdo()->lastInsertId();
            $addProfissionalServico = $this->daoServicoProfissional->storeServicoProfissional($array, $idProfissional);
            if (!$addProfissionalServico) {
                return false;
            } else {
                DB::commit();
                return true;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            return $error;
            //return $th;
        }
    }

    public function update(Request $request, $id)
    {
        $array = $request->servico;
        dd($array);
        //$array = json_decode($array, true);
        $obj = $this->create($request->all());
        $obj->setDataAlteracao(Carbon::now());
        $profissional = $obj->getNome();
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
        $whatsapp = $obj->getWhatsapp();
        $telefone = $obj->getTelefone();
        $email = $obj->getEmail();
        $senha = bcrypt($obj->getSenha());
        $confSenha = bcrypt($obj->getSenha());
        $tipoProf = $obj->getTipoProf();
        $comissao = $obj->getComissao();
        $qtd_servico = $obj->getQtdServico();
        $data_alt = $obj->getDataAlteracao();
        $password = $senha;
        try {
            DB::beginTransaction();
            DB::UPDATE(
                'UPDATE
                profissionais
                        SET profissional = ?,apelido = ?,cpf = ?,rg = ?,dataNasc = ?,logradouro = ?,numero = ?,complemento = ?,bairro =?,
                        cep = ?,id_cidade = ?,whatsapp = ?,
                        telefone = ?,email = ?,senha = ?,confSenha = ?,tipoProf = ?,comissao = ?, qtd_servico = ?, password = ? ,data_alt = ?
                        WHERE  id = ?',
                [$profissional, $apelido, $cpf, $rg, $dataNasc, $logradouro, $numero, $complemento, $bairro, $cep, $id_cidade, $whatsapp, $telefone, $email, $senha, $confSenha, $tipoProf, $comissao, $qtd_servico, $password, $data_alt, $id],
            );
            $deleteProfissionalServico = $this->daoServicoProfissional->delete($id);
            if ($deleteProfissionalServico) {
                $addProfissionalServico = $this->daoServicoProfissional->storeServicoProfissional($array, $id);
            }
            if (!$addProfissionalServico) {
                return false;
            } else {
                DB::commit();
                return true;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            return $error;
            //return $th;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $deleteProfissionalServico = $this->daoServicoProfissional->delete($id);
            if ($deleteProfissionalServico) {
                DB::DELETE("DELETE FROM  profissionais WHERE id = '$id'");
                DB::commit();
                return true;
            }
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
            $dados = DB::select('select * from profissionais where id = ?', [$id]);
            return $dados[0];
        }
        $dados = DB::select('select * from profissionais where id = ?', [$id]);
        if ($dados) {
            $profissionais = [];
            foreach ($dados as $item) {
                $profissional = $this->create(get_object_vars($item));
                $profissional_json = $this->getData($profissional);
                array_push($profissionais, $profissional_json);
            }
            return $profissionais;
        }
    }
    public function getData(Profissional $profissional)
    {
        $dados = [
            'id' => $profissional->getId(),
            'profissional' => $profissional->getNome(),
            'apelido' => $profissional->getApelido(),
            'cpf' => $profissional->getCpf(),
            'rg' => $profissional->getRg(),
            'dataNasc' => $profissional->getDataNasc(),
            'logradouro' => $profissional->getLogradouro(),
            'numero' => $profissional->getNumero(),
            'complemento' => $profissional->getComplemento(),
            'bairro' => $profissional->getBairro(),
            'cep' => $profissional->getCep(),
            'Cidade' => $this->daoCidade->getData($profissional->getCidade()),
            'whatsapp' => $profissional->getWhatsapp(),
            'telefone' => $profissional->getTelefone(),
            'email' => $profissional->getEmail(),
            'senha' => $profissional->getSenha(),
            'tipoProf' => $profissional->getTipoProf(),
            'servico' => $profissional->getServico(),
            'comissao' => $profissional->getComissao(),
            'qtd_servico' => $profissional->getQtdServico(),
            'data_create' => $profissional->getDataCadastro(),
            'data_alt' => $profissional->getDataAlteracao(),
        ];
        return $dados;
    }
}
