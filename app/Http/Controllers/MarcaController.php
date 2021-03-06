<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function __construct(Marca $marca) 
    {
        $this->marca = $marca;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectRegistrosRelacionadosPelosAtributosModelos($atributos_modelos);
        } else {
            $marcaRepository->selectRegistrosRelacionadosPelosAtributosModelos('modelos');
        }

        if($request->has('filtro')) {
           $marcaRepository->filtro($request->filtro);
        }
        
        if($request->has('atributos')) {
            $atributos = $request->atributos;
            $marcaRepository->selectRegistroRelacionadosPelosAtributos($atributos);
        } 

        $marcas = $marcaRepository->getResultadosPaginados(3);

        return response()->json($marcas, 200);

        // Código antes da criação de Repository 
        // $marcas = array();

        // if($request->has('atributos_modelos')) {
        //     $atributos_modelos = $request->atributos_modelos;
        //     $marcas = $this->marca->with('modelos:id,'.$atributos_modelos);
        // } else {
        //     $marcas = $this->marca->with('modelos');
        // }

        // if($request->has('filtro')) {
        //     // separados os filtros
        //     $filtros = explode(';', $request->filtro);
        //     foreach($filtros as $key => $condicao) {
        //         // separados o paramêtro, o operador e o valor da condição.
        //         $c = explode(':', $condicao);
        //         $marcas = $marcas->where($c[0], $c[1], $c[2]);
        //     }
        // }

        // if($request->has('atributos')) {
        //     $atributos = $request->atributos;
        //     $marcas = $marcas->selectRaw($atributos)->get();
        // } else {
        //     $marcas = $marcas->get();
        // }

        // return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validação de parâmetros
        $request->validate($this->marca->rules(), $this->marca->feedback());
        
        // Para recuperar arquivos da request é necessário utilizar o método file('nome_atributo');
        $imagem = $request->file('imagem');
        // No método store() é possível passar 2 parâmetros
        // 1) Nome da pasta que receberá o arquivo
        // 2) Disk (Disco) onde será armazenado no projeto -> local, public ou cloud (AWS S3) 
        $imagem_urn = $imagem->store('imagens', 'public');
        
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        
        if($marca === null) {
            // utilizand o helper response() para manipular a resposta.
            return response()->json(['erro' => 'Recurso pesquisa não existe.'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Integer $id
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);
        
        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a atualização. O recurso solicitado não existe.'], 404);
        }

        // Validação de paramentros caso o method da requisição seja PATCH ou PUT
        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            //percorrendo todas a regras definidas na Model Marca
            foreach($marca->rules() as $input => $regra) {
                // Coletar apenas as regras aplicáveis as parâmetros passados na requisição PATCH
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            } 
            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }

        // Upload da imagem atualizada no diretório public
        // remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;

        $marca->save();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if($marca === null) {
            return response()->json(['erro' => 'Impossível realizar a exclusão. O recurso solicitado não existe.'], 404);
        }

        
        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
