<?php

namespace App\Dao;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\CompraProduto;
use App\Dao\DaoFornecedor;


class DaoCompraProduto
{
    protected DaoFornecedor $daoFornecedor;

    public function __construct()
    {
        $this->daoFornecedor = new DaoFornecedor();
    }

    public function create(array $dados)
    {
        $compraProduto = new CompraProduto();
        $compraProduto->setCompraModelo((string) $dados['compra_modelo']);
        $compraProduto->setCompraNumeroNota((string) $dados['compra_numero_nota']);
        $compraProduto->setCompraSerie((string) $dados['compra_serie']);
        $compraProduto->setQtdProduto((int) $dados['qtd_produto']);
        $compraProduto->setValorUnitario((float) $dados['valor_unitario']);
        $compraProduto->setValorCusto((float) $dados['valor_custo']);
        $compraProduto->setTotalProduto((float) $dados['total_produto']);
        $compraProduto->setDesconto((float) $dados['desconto']);
        $fornecedor = $this->daoFornecedor->findById($dados['compra_id_fornecedor'], false);
        $fornecedor = $this->daoFornecedor->create(get_object_vars($fornecedor));
        $compraProduto->setFornecedor($fornecedor);
        return $compraProduto;
    }

    public function findById($compra_modelo, $compra_numero_nota, $compra_serie, bool $model = false)
    {
        if (!$model) {
            $dados = DB::select(
                'SELECT * FROM compra_produto WHERE compra_modelo = ? AND compra_numero_nota = ? AND compra_serie = ?',
                [trim($compra_modelo), trim($compra_numero_nota), trim($compra_serie)]
            );
            return $dados[0];
        }

        $dados = DB::select(
            'SELECT * FROM compra_produto WHERE compra_modelo = ? AND compra_numero_nota = ? AND compra_serie = ?',
            [trim($compra_modelo), trim($compra_numero_nota), trim($compra_serie)]
        );

        if ($dados) {
            $List_produtos = [];
            foreach ($dados as $item) {
                $produto = $this->create(get_object_vars($item));
                $produto_json = $this->getData($produto);
                array_push($List_produtos, $produto_json);
            }
            return $List_produtos;
        }
    }


    public function getData(CompraProduto $compraProduto)
    {
        return $dados = [
            'compra_modelo' => $compraProduto->getCompraModelo(),
            'compra_numero_nota' => $compraProduto->getCompraNumeroNota(),
            'compra_serie' => $compraProduto->getCompraSerie(),
            'qtd_produto' => $compraProduto->getQtdProduto(),
            'valor_unitario' => $compraProduto->getValorUnitario(),
            'valor_custo' => $compraProduto->getValorCusto(),
            'total_produto' => $compraProduto->getTotalProduto(),
            'desconto' => $compraProduto->getDesconto(),
            'id_fornecedor' => $compraProduto->getFornecedor()->getid(),
        ];
    }
}
