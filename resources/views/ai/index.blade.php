@extends('layouts.app')

@section('title', 'AI Cafeteria Assistant')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 via-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-md flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                <i data-lucide="bot" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-200">
                    Role-Aware Intelligence Pipeline
                </span>
                <h1 class="text-2xl font-black">AI Cafeteria Assistant</h1>
                <p class="text-xs text-orange-100">
                    Authenticated Role: <strong class="uppercase underline">{{ $user->role }}</strong>
                    &bull; Enforced Access Control: {{ $user->isAdmin() ? 'Administrative & Financial Analytics Authorized' : 'Customer Scope (Menu, Tastes, Diet & Combos)' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Main Chat Window -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[650px]"
         x-data="{
             messages: [
                 {
                     role: 'assistant',
                     content: '{{ $user->isAdmin() ? '👨‍💼 Welcome Administrator! I am ready to answer authorized questions about cafeteria revenue, today\'s orders, low stock items, best-selling dishes, or customer counts. What would you like to know?' : '👋 Hello ' . $user->name . '! I am your AI Cafeteria Assistant. I know your food preferences and can suggest dishes, find meals within your budget, recommend spicy foods, or compare meals. How can I help you today?' }}'
                 }
             ],
             input: '',
             loading: false,
             sendMessage(customText) {
                 const msg = customText || this.input;
                 if (!msg.trim()) return;
                 this.messages.push({ role: 'user', content: msg });
                 if (!customText) this.input = '';
                 this.loading = true;

                 fetch('{{ route('ai.chat') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                     },
                     body: JSON.stringify({ message: msg })
                 })
                 .then(res => res.json())
                 .then(data => {
                     this.loading = false;
                     this.messages.push({
                         role: 'assistant',
                         content: data.message,
                         status: data.status,
                         suggestions: data.suggestions || []
                     });
                     this.$nextTick(() => {
                         const box = document.getElementById('fullChatBox');
                         box.scrollTop = box.scrollHeight;
                     });
                 })
                 .catch(err => {
                     this.loading = false;
                     this.messages.push({ role: 'assistant', content: '⚠️ Error communicating with AI service.' });
                 });
             }
         }">

        <!-- Messages Area -->
        <div id="fullChatBox" class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50">
            <template x-for="(m, idx) in messages" :key="idx">
                <div>
                    <div class="flex gap-3" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                        <template x-if="m.role === 'assistant'">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-500 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                            </div>
                        </template>
                        <div class="max-w-[80%] rounded-2xl p-4 text-xs shadow-sm whitespace-pre-line leading-relaxed"
                             :class="m.role === 'user'
                                ? 'bg-orange-500 text-white rounded-br-none'
                                : (m.status === 'forbidden' ? 'bg-rose-50 border border-rose-200 text-rose-900 rounded-bl-none font-medium' : 'bg-white border border-slate-200 text-slate-800 rounded-bl-none')">
                            <span x-text="m.content"></span>
                        </div>
                    </div>

                    <!-- Suggestions Chips -->
                    <template x-if="m.suggestions && m.suggestions.length">
                        <div class="mt-2.5 pl-11 flex flex-wrap gap-2">
                            <template x-for="(sug, sIdx) in m.suggestions" :key="sIdx">
                                <button @click="sendMessage(sug)"
                                        class="text-xs px-3 py-1.5 bg-white hover:bg-orange-50 text-orange-700 border border-orange-200 rounded-full transition-colors shadow-2xs font-medium"
                                        x-text="sug"></button>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <div x-show="loading" class="flex items-center gap-2 text-xs text-slate-400 pl-11">
                <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></div>
                <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse delay-75"></div>
                <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse delay-150"></div>
                <span>Analyzing request through security pipeline...</span>
            </div>
        </div>

        <!-- Quick Questions Toolbar -->
        <div class="p-3 bg-white border-t border-slate-100 overflow-x-auto">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Suggested Questions:</span>
            <div class="flex gap-2 whitespace-nowrap text-xs">
                @if($user->isAdmin())
                    <button @click="sendMessage('What is today\'s total sales?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">💰 Today's Sales</button>
                    <button @click="sendMessage('How many orders were placed today?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">📋 Orders Today</button>
                    <button @click="sendMessage('How many customers are registered?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">👥 Customer Count</button>
                    <button @click="sendMessage('What is the most ordered food?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">🥇 Top Food</button>
                    <button @click="sendMessage('Which products have low stock?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">⚠️ Low Stock</button>
                    <button @click="sendMessage('Which food items have never been ordered?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">🔍 Never Ordered</button>
                @else
                    <button @click="sendMessage('What food do you recommend for me?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">✨ AI Recommendations</button>
                    <button @click="sendMessage('Recommend something spicy')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">🌶️ Spicy Food</button>
                    <button @click="sendMessage('I want something under 100 EGP')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">💵 Under 100 EGP</button>
                    <button @click="sendMessage('What drinks are suitable for me?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">🥤 Cold/Hot Drinks</button>
                    <button @click="sendMessage('What is the healthiest meal available?')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 font-bold">🥗 Healthiest Meal</button>
                    <button @click="sendMessage('What are today\'s total sales?')" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg font-bold" title="Test Admin Barrier">🛡️ Test RBAC Security Wall</button>
                @endif
            </div>
        </div>

        <!-- Input Box -->
        <div class="p-4 bg-white border-t border-slate-200">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input type="text"
                       x-model="input"
                       placeholder="{{ $user->isAdmin() ? 'Ask administrative questions (e.g. sales, orders, low stock, customer counts)...' : 'Ask for personalized meals, spicy dishes, budget ideas, ingredients...' }}"
                       class="flex-1 text-sm px-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 font-medium">
                <button type="submit"
                        :disabled="loading || !input.trim()"
                        class="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 disabled:opacity-50 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-2">
                    <span>Send</span>
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
