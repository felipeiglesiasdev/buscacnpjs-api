<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    // 1. Carrega a página listando os tokens
    public function index()
    {
        // Pega todos os tokens do usuário logado (usando o Sanctum)
        $tokens = Auth::user()->tokens;
        
        // Retorna a view que acabamos de criar, passando os tokens para ela
        return view('dashboard.tokens.index', compact('tokens'));
    }

    // 2. Cria um novo token para um site/projeto
    public function store(Request $request)
    {
        // Valida se o usuário preencheu o nome do site
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Cria o token no banco de dados. O Laravel Sanctum faz a mágica toda aqui!
        $token = $user->createToken($request->token_name);

        // Retorna para a mesma página, mandando o token gerado (plainTextToken)
        // para aparecer naquela caixinha verde linda apenas uma vez.
        return back()->with('successToken', $token->plainTextToken);
    }

    // 3. Revoga (Deleta) o acesso de um site
    public function destroy($id)
    {
        $user = Auth::user();
        
        // Procura o token pelo ID e deleta
        $user->tokens()->where('id', $id)->delete();

        // Retorna para a página com a mensagem de sucesso azul
        return back()->with('status', 'Acesso revogado com sucesso!');
    }
}