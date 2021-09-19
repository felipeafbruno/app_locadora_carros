<?php 
namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class AbstractRepository {
    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectRegistrosRelacionadosPelosAtributosModelos($atributos_modelos) {
        // necessário sempre atualizar o atributo do objeto com a nova query
        $this->model = $this->model->with($atributos_modelos);
    }

    public function filtro($filtros) {
        $filtros = explode(';', $filtros);
        foreach($filtros as $key => $condicao) {
            $c = explode(':', $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
        }
    }

    public function selectRegistroRelacionadosPelosAtributos($atributos) {
        $this->model = $this->model->selectRaw($atributos);
    }

    public function getResultados() {
        return $this->model->get();
    }

    public function getResultadosPaginados($numeroRegistroPorPagina) {
        return $this->model->paginate($numeroRegistroPorPagina);
    }
}
?>