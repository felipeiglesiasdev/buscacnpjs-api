<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800">Chaves de API</h2>
                <p class="text-sm text-gray-500 mt-1">Gerencie os tokens de acesso para seus projetos satélites consumirem a API.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6 py-8 px-4 sm:px-6 lg:px-8">

        @if (session('successToken'))
            <div class="bg-green-50 border border-green-200 p-6 rounded-2xl shadow-sm mb-6 animate-fade-in-up">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <i class="bi bi-shield-lock-fill text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4 w-full">
                        <h3 class="text-lg font-bold text-green-900">Token Gerado com Sucesso!</h3>
                        <p class="text-sm text-green-700 mt-1">
                            Este é o seu token de acesso. <strong>Copie-o agora!</strong> O Laravel Sanctum criptografa as chaves no banco de dados, então você não poderá ver este token completo novamente.
                        </p>
                        
                        <div class="mt-4 flex flex-col sm:flex-row items-center gap-3 w-full">
                            <code class="bg-white px-4 py-3 rounded-xl text-gray-800 font-mono text-sm border border-green-300 shadow-inner flex-1 select-all w-full break-all" id="newTokenValue">
                                {{ session('successToken') }}
                            </code>
                            <button onclick="copyToken()" class="w-full sm:w-auto bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-sm flex items-center justify-center gap-2" id="copyBtn">
                                <i class="bi bi-clipboard"></i> Copiar Chave
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
                        btn.innerHTML = '<i class="bi bi-check2-all text-lg"></i> Copiado!';
                        btn.classList.replace('bg-green-700', 'bg-gray-800');
                        btn.classList.replace('hover:bg-green-800', 'hover:bg-gray-900');
                        setTimeout(function() {
                            btn.innerHTML = '<i class="bi bi-clipboard"></i> Copiar Chave';
                            btn.classList.replace('bg-gray-800', 'bg-green-700');
                            btn.classList.replace('hover:bg-gray-900', 'hover:bg-green-800');
                        }, 3000);
                    });
                }
            </script>
        @endif

        @if (session('status'))
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl shadow-sm mb-6 flex items-center">
                <i class="bi bi-info-circle-fill text-blue-600 text-xl mr-3"></i>
                <p class="text-sm text-blue-800 font-medium">{{ session('status') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="bi bi-plus-lg text-blue-600"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Nova Chave</h3>
                    </div>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                        Crie tokens exclusivos para cada aplicação que for consumir sua API.
                    </p>

                    <form method="POST" action="{{ route('tokens.store') }}">
                        @csrf
                        <div class="mb-5">
                            <label for="token_name" class="block text-sm font-semibold text-gray-700 mb-2">Identificação (Nome do Site)</label>
                            <input type="text" name="token_name" id="token_name" required 
                                   class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 transition-all text-sm py-2.5"
                                   placeholder="Ex: App Consultas">
                            @error('token_name')
                                <span class="text-red-500 text-xs mt-2 block font-medium"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-4 py-3 rounded-xl text-sm font-bold transition-all shadow-md hover:shadow-lg">
                            Gerar Token de Acesso <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h3 class="text-base font-bold text-gray-800">Tokens Ativos</h3>
                        <span class="bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1.5 rounded-full">{{ $tokens->count() }} {{ $tokens->count() === 1 ? 'chave' : 'chaves' }}</span>
                    </div>

                    @if ($tokens->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-gray-500 bg-gray-50/50 uppercase tracking-wider border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">Aplicação</th>
                                        <th class="px-6 py-4 font-semibold">Prefixo</th>
                                        <th class="px-6 py-4 font-semibold">Último Uso</th>
                                        <th class="px-6 py-4 text-right font-semibold">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($tokens as $token)
                                        <tr class="hover:bg-gray-50/80 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                                                        <i class="bi bi-hdd-network"></i>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-gray-800 block">{{ $token->name }}</span>
                                                        <span class="text-xs text-gray-400">Criado em {{ $token->created_at->format('d/m/Y') }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <code class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">
                                                    ID: {{ $token->id }}|********
                                                </code>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($token->last_used_at)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                                        <i class="bi bi-activity"></i> {{ $token->last_used_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                        Nunca usado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <form method="POST" action="{{ route('tokens.destroy', $token->id) }}" onsubmit="return confirm('Revogar o acesso de {{ $token->name }}? Esta ação é irreversível.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-2 rounded-xl hover:bg-red-50 focus:ring focus:ring-red-200" title="Revogar Token">
                                                        <i class="bi bi-trash3 text-lg"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-16 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 border border-gray-100 text-gray-300 mb-5">
                                <i class="bi bi-key text-3xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800">Nenhuma chave gerada</h4>
                            <p class="text-gray-500 mt-2 max-w-xs mx-auto text-sm leading-relaxed">Você ainda não tem tokens ativos. Gere o primeiro ao lado para começar a usar a API.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>