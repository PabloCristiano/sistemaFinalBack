<?php

namespace App\Dao;

use App\Dao\Dao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Models\Compra;

class DaoCompra implements Dao
{
    protected Compra $compra;


    public function __construct()
    {
        $this->compra = new Compra();
    }

    public function all(bool $json = false)
    {
        $itens = DB::select('select * from compra order by data_create desc');
        $compras = [];
        foreach ($itens as $item) {
            $compra = $this->create(get_object_vars($item));
            if ($json) {
                $compra_json = $this->getData($compra);
                array_push($compras, $compra_json);
            } else {
                array_push($compras, $compra);
            }
        }
        return $compras;
    }

    public function create(array $dados)
    {
        $compra = new Compra();

        if (isset($dados["data_create"]) && isset($dados["data_alt"])) {
            $compra->setStatus($dados["status"]);
            $compra->setDataCadastro($dados["data_create"]);
            $compra->setDataAlteracao($dados["data_alt"]);
            $compra->setDataCancelamento($dados["data_cancelamento"]);
        }

        // Dados nota
        $compra->setModelo($dados["modelo"]);
        $compra->setSerie($dados["serie"]);
        $compra->setNumeroNota($dados["numero_nota"]);
        $compra->setDataEmissao($dados["data_emissao"]);
        $compra->setDataChegada($dados["data_chegada"]);
        $compra->setQtdProduto($dados["qtd_produto"]);
        $compra->setValorCompra($dados["valor_compra"]);
        // dd($dados);

        return $compra;
    }

    public function store($compra)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function delete($id)
    {
    }

    public function findById(int $id, bool $model = false)
    {
    }

    public function getData(Compra $compra)
    {
        $dados = [
            'modelo' => $compra->getModelo(),
            'numero_nota' => $compra->getNumeroNota(),
            'serie'  => $compra->getSerie(),
            'qtd_produto' => $compra->getQtdProduto(),
            'valor_compra' => $compra->getValorCompra(),
            'data_emissao' => $compra->getDataEmissao(),
            'data_chegada' => $compra->getDataChegada(),
            'status' =>  $compra->getStatus(),
            'data_cancelamento' => $compra->getDataCancelamento(),
            'data_create' => $compra->getDataCadastro(),
            'data_alt' => $compra->getDataAlteracao()
        ];
        return $dados;
    }

    public function getProductsData(Compra $compra)
    {
    }

    public function getDuplicatesData(Compra $compra)
    {
    }
}
