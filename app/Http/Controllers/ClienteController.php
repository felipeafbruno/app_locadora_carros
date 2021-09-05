<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente);

        // Verifico a existência do atributo 'atributos' para poder passar no método selectRaw() (filtro).
        // Caso não existe pego os registros sem os filtros contornando um possível erro.
        if($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        } 

        if($request->has('atributos')) {
            $atributos = $request->atributos;
            $clienteRepository->selectRegistroRelacionadosPelosAtributos($atributos);
        } 

        $cliente = $clienteRepository->getResultados();

        return response()->json($cliente, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules());

        $cliente = $this->cliente->create([
            'nome' => $request->nome
        ]);

        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cliente = $this->cliente->find($id);
        
        if($cliente === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe.'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);

        if($cliente === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'], 404);
        }

        // Validação de paramentros caso o method da requisição seja PATCH ou PUT
        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            //percorrendo todas a regras definidas na Model Marca
            foreach($cliente->rules() as $input => $regra) {
                // Coletar apenas as regras aplicáveis as parâmetros passados na requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            } 
            $request->validate($regrasDinamicas);
        } else {
            $request->validate($cliente->rules());
        }

        $cliente->fill($request->all());
        $cliente->save();

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'], 404);
        }

        $cliente->delete();
        return response()->json(['msg' => 'O cliente foi removida com sucesso!'], 200);
    }
}
