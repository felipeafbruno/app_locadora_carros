<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    public function rules() {
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3',
            'imagem' => 'required'
        ];
        /**
         * unique pode receber 3 parametros:
         * 1) nome da tabela
         * 2) nome da coluna -> se omitido o Laravel assume que a coluna tem o nome do input
         * 3) id do registro que será disconsiderado na pesquisa
         * 
         * OBS: O tercerio parametro é muito importante ser passado para o unique principalmente por causa da atualização de registro, 
         * já que com ele determinamos para o Laravel que todos os outros registros podem ser visitados na validação 
         * 'exceto' o id informado
         */
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'O nome da marca já existe'
        ];
    }
}
