<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Repositories\carroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{
    public function __construct(Carro $carro) {
        $this->carro = $carro;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new carroRepository($this->carro);
        // Caso o parametro request do método index contenha o atributo 'atributos_marca' 
        // porém não contêm o atributo 'atributos', o que vai gerar um erro no select, 
        // verifico primeiro a existência do atributos_marca para depois continuedade 
        // no processo de requisição.
        if($request->has('atributos_modelo')) {
            $atributos_modelo = 'modelos:id,'.$request->atributos_modelo;
            $carroRepository->selectRegistrosRelacionadosPelosAtributosModelos($atributos_modelo);
        } else {
            $carroRepository->selectRegistrosRelacionadosPelosAtributosModelos('modelo');
        }

        // Verifico a existência do atributo 'atributos' para poder passar no método selectRaw() (filtro).
        // Caso não existe pego os registros sem os filtros contornando um possível erro.
        if($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        } 

        if($request->has('atributos')) {
            $atributos = $request->atributos;
            $carroRepository->selectRegistroRelacionadosPelosAtributos($atributos);
        } 

        $carros = $carroRepository->getResultados();

        return response()->json($carros, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km,
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);
        
        if($carro === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe.'], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);

        if($carro === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'], 404);
        }

        // Validação de paramentros caso o method da requisição seja PATCH ou PUT
        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            //percorrendo todas a regras definidas na Model Marca
            foreach($carro->rules() as $input => $regra) {
                // Coletar apenas as regras aplicáveis as parâmetros passados na requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            } 
            $request->validate($regrasDinamicas);
        } else {
            $request->validate($carro->rules());
        }

        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);
        if($carro === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'], 404);
        }

        $carro->delete();
        return response()->json(['msg' => 'O carro foi removida com sucesso!'], 200);
    }
}
