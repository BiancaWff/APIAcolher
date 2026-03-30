<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Importante para lidar com arquivos
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    public function cadastrar(Request $request)
    {
        Log::info('Dados recebidos na API:', $request->all());
        try {
            // 1. Validação dos dados (incluindo os novos campos da View)
            $dados = $request->validate([
                'nome'         => 'required|string|max:100',
                'telefone'     => 'required|string|max:20',
                'cpf_cnpj'     => 'required|unique:usuario',
                'endereco'     => 'required|string',
                'email'        => 'required|email|unique:usuario',
                'senha'        => 'required|min:6',
                'tipo_usuario' => 'required|in:cliente,instituicao', // Verifique se está enviando isso no form ou defina um default
                'instagram'    => 'nullable|string|max:50',
                'facebook'     => 'nullable|string|max:50',
                'descricao'    => 'nullable|string|max:150',
                'foto_perfil'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validação da imagem
            ]);

            // 2. Processamento da Imagem
            if ($request->hasFile('foto_perfil')) {
                // Salva a imagem na pasta 'public/usuarios' e retorna o caminho
                $caminhoImagem = $request->file('foto_perfil')->store('usuarios', 'public');
                
                // Substituímos o arquivo bruto pelo caminho da string no array de dados
                $dados['foto_perfil'] = $caminhoImagem;
            }

            // 3. Criação do Usuário
            $usuario = Usuario::create($dados);

            return response()->json([
                'res' => 'Usuário cadastrado com sucesso!',
                'user' => $usuario
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'erro' => 'Erro de validação',
                'mensagens' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro interno no servidor',
                'detalhes' => $e->getMessage()
            ], 500);
        }
    }

    // Editar: Atualizado para também lidar com troca de foto se necessário
    public function editar(Request $request, $id)
    {
        if (Auth::id() != $id) {
            return response()->json(['erro' => 'Acesso negado.'], 403);
        }

        $usuario = Usuario::findOrFail($id);
        $dados = $request->all();

        // Se o usuário estiver enviando uma nova foto
        if ($request->hasFile('foto_perfil')) {
            // Deleta a foto antiga se ela existir no storage
            if ($usuario->foto_perfil) {
                Storage::disk('public')->delete($usuario->foto_perfil);
            }
            // Salva a nova
            $dados['foto_perfil'] = $request->file('foto_perfil')->store('usuarios', 'public');
        }

        $usuario->update($dados);

        return response()->json(['res' => 'Perfil atualizado!', 'user' => $usuario]);
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