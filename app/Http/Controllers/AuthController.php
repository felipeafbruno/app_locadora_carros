<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {
        $credenciais = $request->all(['email', 'password']);

        // Primeiro autenticar o usuário
        $token = auth('api')->attempt($credenciais);
        
        if($token) { // Usuário antenticado com sucesso
            return response()->json(['token' => $token], 200);
        } else { // erro usuário ou senha
            return response()->json(['erro' => 'Usuário ou senha inválido.'], 403);
        }

        // Segundo gerar um JWT (Json Web Token)
        return 'login';
    }

    public function logout() {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout foi reliazado com sucesso']);
    }

    public function refresh() {
        // renovando uma autorização de acesso
        $token = auth('api')->refresh(); // cliente encaminhe um JWT válido
        return response()->json(['token' => $token]);
    }

    public function me() {
        return response()->json(auth()->user());
    }
}
