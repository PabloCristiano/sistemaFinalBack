<?php

namespace App\Dao;

use App\Dao\Dao;
use App\Models\Produto;
use App\Dao\DaoCategorias;
use App\Dao\DaoFornecedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DaoProduto implements Dao
{
    private $daoCategoria;
    private $daoFornecedor;
    public function __construct()
    {
        $this->daoCategoria = new DaoCategorias();
        $this->daoFornecedor = new DaoFornecedor();
    }

    public function all(bool $json = true)
    {
        // $itens = DB::table('cidades')->get();
        $itens = DB::select('select * from produtos order by id desc');
        $produtos = [];
        foreach ($itens as $item) {
            $produto = $this->create(get_object_vars($item));
            if ($json) {
                $produto_json = $this->getData($produto);
                array_push($produtos, $produto_json);
            } else {
                array_push($produtos, $produto);
            }
        }
        return $produtos;
    }

    public function create(array $dados)
    {
        $produto = new Produto();
        if (isset($dados['id'])) {
            $produto->setId($dados['id']);
            $produto->setDataCadastro($dados['data_create'] ?? null);
            $produto->setDataAlteracao($dados['data_alt'] ?? null);
        }
        $produto->setProduto($dados['produto']);
        $produto->setUnidade($dados['unidade']);
        $produto->setQtdEstoque($dados['qtdEstoque']);
        $produto->setPrecoCusto($dados['precoCusto']);
        $produto->setPrecoVenda($dados['precoVenda']);
        $produto->setCustoUltCompra($dados['custoUltCompra']);
        $produto->setDataUltCompra($dados['dataUltCompra'] ?? null);
        $produto->setDataUltVenda($dados['dataUltVenda'] ?? null);

        $categoria = $this->daoCategoria->findById($dados['id_categoria'], false);
        $categorias = $this->daoCategoria->create(get_object_vars($categoria));
        $produto->setCategoria($categorias);

        $fornecedor = $this->daoFornecedor->findById($dados['id_fornecedor'], false);
        $fornecedores = $this->daoFornecedor->create(get_object_vars($fornecedor));
        $produto->setFornecedor($fornecedores);
        return $produto;
    }

    public function store($obj)
    {
        $obj->setDataUltCompra(Carbon::now());
        $obj->setDataUltVenda(Carbon::now());
        $produto = $obj->getProduto();
        $unidade = $obj->getUnidade();
        $qtdEstoque = $obj->getQtdEstoque();
        $precoCusto = $obj->getPrecoCusto();
        $precoVenda = $obj->getPrecoVenda();
        $custoUltCompra = $obj->getCustoUltCompra();
        $dataUltCompra = $obj->getDataUltCompra();
        $dataUltVenda = $obj->getDataUltVenda();
        $id_categoria = $obj->getCategoria()->getid();
        $id_fornecedor = $obj->getFornecedor()->getid();
        
        try {
            //DB::beginTransaction();
            //DB::table('estados')->insert($dados);
            DB::INSERT("INSERT INTO produtos (produto,qtdEstoque,precoCusto,precoVenda,custoUltCompra,dataUltCompra,dataUltVenda,id_categoria,id_fornecedor) VALUES ('$produto',$qtdEstoque,$precoCusto,$precoVenda,$custoUltCompra,'$dataUltCompra','$dataUltVenda',$id_categoria,$id_fornecedor)");
            DB::commit();
            // $ultimaCidade = DB::table('cidades')->get()->last();
            // return $ultimaCidade;
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            $error = ['error' => $th->getMessage(), 'CodigoError' => $th->getCode()];
            return $error;
        }
    }

    public function update(Request $request, $id)
    {
        // DB::beginTransaction();
        // try {
        //     $cidades = $this->create($request->all());
        //     // $dados = $this->getData($estados);
        //     $cidades->setDataAlteracao(Carbon::now());
        //     $cidade = $cidades->getCidade();
        //     $ddd = $cidades->getDDD();
        //     $id_estado = $cidades->getEstado()->getId();
        //     $data_alt = $cidades->getDataAlteracao();
        //     //DB::table('estados')->where('id', $dados['id'])->update($dados);
        //    // DB::UPDATE("UPDATE cidades  SET cidade = '$cidade' , ddd = '$ddd', id_estado = '$id_estado', data_alt = '$data_alt' where id = $id ");
        //     DB::update('UPDATE cidades SET cidade = ?,  ddd = ?, id_estado = ?, data_alt = ? WHERE id = ?', [$cidade,$ddd,$id_estado,$data_alt,$id]);
        //     DB::commit();
        //     return true;
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $error = ['error' => $e->getMessage(), 'CodigoError' => $e->getCode()];
        //     return $error;
        // }
    }

    public function delete($id)
    {
        // DB::beginTransaction();
        // try {
        //     // DB::table('cidades')->where('id', $id)->delete();
        //     DB::DELETE("DELETE FROM  cidades where id = '$id'");
        //     DB::commit();
        //     return true;
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     if ($e->getCode() === '23000') {
        //         return $error = ['Não foi possível excluir, registro já vinculado.'];
        //     }
        //     return $error = ['Não foi possível excluir o registro.'];
        // }
    }

    public function findById(int $id, bool $model = false)
    {
        if (!$model) {
            // return DB::table('estados')->get()->where('id', $id)->first();
            $dados = DB::select('select * from produtos where id = ?', [$id]);
            return $dados[0];
        }
        // $dados = DB::table('estados')->where('id', $id)->first();
        $dados = DB::select('select * from produtos where id = ?', [$id]);
        if ($dados) {
            $produtos = [];
            foreach ($dados as $item) {
                $produto = $this->create(get_object_vars($item));
                $produto_json = $this->getData($produto);
                array_push($produtos, $produto_json);
            }

            return $produtos;
        }
    }

    public function getData(Produto $produto)
    {
        $dados = [
            'id' => $produto->getId(),
            'produto' => $produto->getProduto(),
            'unidade' => $produto->getUnidade(),
            'qtdEstoque' => $produto->getQtdEstoque(),
            'precoCusto' => $produto->getPrecoCusto(),
            'precoVenda' => $produto->getPrecoVenda(),
            'custoUltCompra' => $produto->getCustoUltCompra(),
            'dataUltCompra' => $produto->getDataUltCompra(),
            'dataUltVenda' => $produto->getDataUltVenda(),
            'categoria' => $this->daoCategoria->getData($produto->getCategoria()),
            'fornecedor' => $this->daoFornecedor->getData($produto->getFornecedor()),
            'data_create' => $produto->getDataCadastro(),
            'data_alt' => $produto->getDataAlteracao(),
        ];
        return $dados;
    }
}
