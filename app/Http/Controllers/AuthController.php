<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciaos = $request->all('email', 'password');
        $token = auth('api')->attempt($credenciaos);
        if ($token) {
            return response()->json(['accessToken' => $token], 200);
        } else {
            return response()->json(['error' => 'Usuário ou Senha, estão incorretos.'], 403);
        }
    }
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['Msg' => 'O LogOut Realizado com Sucesso !!!']);
    }
    public function me()
    {
        return response()->json(auth('api')->user());
    }
    public function refresh()
    {
       
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);

        // $user = Auth::user();
        // if (!$user || !Auth::refresh()) {
        //     return response()->json(['message' => 'Unauthorized'], 401);
        // }
        // return response()->json([
        //     'access_token' => Auth::user()->createToken('AccessToken')->accessToken,
        //     'token_type' => 'Bearer',
        //     'expires_in' => Auth::factory()->getTTL() * 60,
        // ]);
    }
}
