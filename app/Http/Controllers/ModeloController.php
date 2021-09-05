<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;

class ModeloController extends Controller
{

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);
        // Caso o parametro request do método index contenha o atributo 'atributos_marca' 
        // porém não contêm o atributo 'atributos', o que vai gerar um erro no select, 
        // verifico primeiro a existência do atributos_marca para depois continuedade 
        // no processo de requisição.
        if($request->has('marca')) {
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectRegistrosRelacionadosPelosAtributosModelos($atributos_marca);
        } else {
            $modeloRepository->selectRegistrosRelacionadosPelosAtributosModelos('marca');
        }

        // Verifico a existência do atributo 'atributos' para poder passar no método selectRaw() (filtro).
        // Caso não existe pego os registros sem os filtros contornando um possível erro.
        if($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        } 

        if($request->has('atributos')) {
            $atributos = $request->atributos;
            $modeloRepository->selectRegistroRelacionadosPelosAtributos($atributos);
        } 

        $modelos = $modeloRepository->getResultados();

        return response()->json($modelos, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());
        
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        
        if($modelo === null) {
            return response()->json(['erro' => 'Recurso pesquisado não existe.'], 404);
        }

        return response()->json($modelo, 200);
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
        $modelo = $this->modelo->find($id);
        
        if($modelo === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'], 404);
        }

        // Validação de paramentros caso o method da requisição seja PATCH ou PUT
        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            //percorrendo todas a regras definidas na Model Marca
            foreach($modelo->rules() as $input => $regra) {
                // Coletar apenas as regras aplicáveis as parâmetros passados na requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            } 
            $request->validate($regrasDinamicas);
        } else {
            $request->validate($modelo->rules());
        }

        // Upload da imagem atualizada no diretório public
        // remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;

        $modelo->save();

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);

        if($modelo === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'], 404);
        }

        
        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json(['msg' => 'O modelo foi removida com sucesso!'], 200);
    }
}
