<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Cadastro Público
    public function cadastrar(Request $request)
    {
        $dados = $request->validate([
            'nome'         => 'required|string|max:100',
            'cpf_cnpj'     => 'required|unique:usuario',
            'tipo_usuario' => 'required|in:cliente,instituicao',
            'email'        => 'required|email|unique:usuario',
            'senha'        => 'required|min:6'
        ]);

        Usuario::create($dados);
        return response()->json(['res' => 'Usuário cadastrado com sucesso!'], 201);
    }

    // Login com geração de Token para API
    public function login(Request $request)
    {
        $user = Usuario::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->senha, $user->senha)) {
            return response()->json(['erro' => 'E-mail ou senha incorretos'], 401);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'tipo'  => $user->tipo_usuario,
            'nome'  => $user->nome
        ]);
    }

    // Ver o próprio perfil ou um perfil específico
    public function ver($id)
    {
        $usuario = Usuario::findOrFail($id);
        return response()->json($usuario);
    }

    // Editar: APENAS o próprio dono da conta
    public function editar(Request $request, $id)
    {
        // Se o ID logado for diferente do ID que quer editar, bloqueia
        if (Auth::id() != $id) {
            return response()->json(['erro' => 'Você só pode alterar seus próprios dados.'], 403);
        }

        $usuario = Usuario::find($id);
        $usuario->update($request->all());

        return response()->json(['res' => 'Perfil atualizado!']);
    }

    // Excluir: APENAS o próprio dono da conta
    public function excluir($id)
    {
        if (Auth::id() != $id) {
            return response()->json(['erro' => 'Você só pode excluir sua própria conta.'], 403);
        }

        Usuario::find($id)->delete();
        return response()->json(['res' => 'Conta removida com sucesso.']);
    }
}