<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl" style="color: #222222;">Chaves de API</h2>
                <p class="text-sm text-gray-500 mt-1">Gerencie os tokens de acesso para os seus projetos e sites satélites.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-8 pb-12">

        <!-- ALERTA DE NOVO TOKEN (SÓ APARECE QUANDO GERA UM NOVO) -->
        @if (session('successToken'))
            <div class="bg-green-50 border-l-4 border-[#499F2D] p-6 rounded-r-xl shadow-sm mb-6 animate-fade-in-up">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="bi bi-check-circle-fill text-[#499F2D] text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-green-800">Token Gerado com Sucesso!</h3>
                        <p class="text-sm text-green-700 mt-1">
                            Copie o seu novo token de acesso pessoal agora. <strong>Por motivos de segurança, ele não será exibido novamente.</strong>
                        </p>
                        
                        <div class="mt-4 flex items-center gap-2">
                            <code class="bg-white px-4 py-3 rounded-lg text-gray-800 font-mono text-sm border border-green-200 shadow-sm flex-1 select-all" id="newTokenValue">
                                {{ session('successToken') }}
                            </code>
                            <button onclick="copyToken()" class="bg-[#004EA5] hover:bg-[#003d82] text-white px-4 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm" id="copyBtn">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function copyToken() {
                    var copyText = document.getElementById("newTokenValue").innerText.trim();
                    navigator.clipboard.writeText(copyText).then(function() {
                        var btn = document.getElementById("copyBtn");
                        btn.innerHTML = '<i class="bi bi-check2"></i> Copiado!';
                        btn.classList.replace('bg-[#004EA5]', 'bg-[#499F2D]');
                        setTimeout(function() {
                            btn.innerHTML = '<i class="bi bi-clipboard"></i> Copiar';
                            btn.classList.replace('bg-[#499F2D]', 'bg-[#004EA5]');
                        }, 3000);
                    });
                }
            </script>
        @endif

        <!-- MENSAGEM DE SUCESSO PADRÃO (EX: QUANDO DELETA) -->
        @if (session('status'))
            <div class="bg-blue-50 border-l-4 border-[#004EA5] p-4 rounded-r-lg shadow-sm mb-6">
                <div class="flex items-center">
                    <i class="bi bi-info-circle-fill text-[#004EA5] text-lg mr-3"></i>
                    <p class="text-sm text-blue-800 font-medium">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-6">
            
            <!-- FORMULÁRIO DE CRIAÇÃO (ESQUERDA) -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-2">
                        <i class="bi bi-plus-circle-fill text-[#004EA5]"></i> Novo Token
                    </h3>
                    <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                        Crie um token exclusivo para cada site ou projeto. Assim, se um projeto for comprometido, você revoga apenas a chave dele.
                    </p>

                    <form method="POST" action="{{ route('tokens.store') }}">
                        @csrf
                        <div class="mb-5">
                            <label for="token_name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Projeto/Site</label>
                            <input type="text" name="token_name" id="token_name" required 
                                   class="w-full rounded-lg border-gray-300 focus:border-[#004EA5] focus:ring focus:ring-[#004EA5] focus:ring-opacity-20 transition-colors text-sm"
                                   placeholder="Ex: Landing Page Advocacia">
                            @error('token_name')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-[#222222] hover:bg-[#004EA5] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            <i class="bi bi-key"></i> Gerar Chave de API
                        </button>
                    </form>
                </div>
            </div>

            <!-- LISTAGEM DE TOKENS (DIREITA) -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Tokens Ativos</h3>
                        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">{{ $tokens->count() }} chaves</span>
                    </div>

                    @if ($tokens->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-400 bg-gray-50/50 uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">Nome do Projeto</th>
                                        <th class="px-6 py-4 font-semibold">Criado em</th>
                                        <th class="px-6 py-4 font-semibold">Último Uso</th>
                                        <th class="px-6 py-4 text-right font-semibold">Ação</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach ($tokens as $token)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-8 w-8 rounded bg-blue-50 flex items-center justify-center text-[#004EA5]">
                                                        <i class="bi bi-shield-check"></i>
                                                    </div>
                                                    <span class="font-bold text-gray-800">{{ $token->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $token->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($token->last_used_at)
                                                    <span class="text-[#499F2D] font-medium"><i class="bi bi-clock-history"></i> {{ $token->last_used_at->diffForHumans() }}</span>
                                                @else
                                                    <span class="text-gray-400 italic">Nunca usado</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <form method="POST" action="{{ route('tokens.destroy', $token->id) }}" onsubmit="return confirm('Tem a certeza que deseja revogar o acesso do site: {{ $token->name }}? Esta ação cortará o acesso à API imediatamente e não pode ser desfeita.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50" title="Revogar Token">
                                                        <i class="bi bi-trash3-fill text-lg"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                <i class="bi bi-key text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Nenhum token encontrado</h4>
                            <p class="text-gray-500 mt-1 max-w-sm mx-auto text-sm">Você ainda não gerou nenhuma chave de API. Use o formulário ao lado para criar o acesso para o seu primeiro projeto.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>