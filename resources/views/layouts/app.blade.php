<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cafeteria AI') - AI-Powered Cafeteria System</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                        },
                        accent: {
                            500: '#6366f1',
                            600: '#4f46e5',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800" x-data="{ chatOpen: false, surpriseModal: false, surpriseData: null }">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('menu.index') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform">
                            <i data-lucide="utensils" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="text-xl font-black bg-gradient-to-r from-orange-600 to-amber-600 bg-clip-text text-transparent">AI Cafeteria</span>
                            <span class="text-[10px] block font-semibold text-slate-600 uppercase tracking-wider">Smart Food & Drinks</span>
                        </div>
                    </a>

                    <!-- Navigation Links for Logged-In Users -->
                    @auth
                    <nav class="hidden md:flex items-center ml-8 gap-1">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Dashboard</a>
                            <a href="{{ route('admin.food-items.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.food-items.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Food Items</a>
                            <a href="{{ route('admin.beverages.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.beverages.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Beverages</a>
                            <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Categories</a>
                            <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.orders.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Orders</a>
                            <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Customers</a>
                        @else
                            <a href="{{ route('menu.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('menu.index') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Menu</a>
                            <a href="{{ route('recommendations.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-1.5 {{ request()->routeIs('recommendations.index') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                                <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i> AI Picks
                            </a>
                            <a href="{{ route('recommendations.combos') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('recommendations.combos') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Smart Combos</a>
                            <a href="{{ route('recommendations.budget') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('recommendations.budget') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Budget Deals</a>
                            <a href="{{ route('recommendations.compare') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('recommendations.compare') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Compare</a>
                            <a href="{{ route('orders.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('orders.*') ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">My Orders</a>
                        @endif
                    </nav>
                    @endauth
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Surprise Me Button (for Customers) -->
                        @if(!auth()->user()->isAdmin())
                        <button @click="
                            fetch('{{ route('recommendations.surprise') }}')
                                .then(res => res.json())
                                .then(data => { surpriseData = data; surpriseModal = true; })
                        " class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gradient-to-r from-purple-50 to-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition-colors shadow-sm">
                            <i data-lucide="party-popper" class="w-3.5 h-3.5 text-indigo-500"></i>
                            Surprise Me!
                        </button>
                        @endif

                        <!-- AI Chat Trigger Button -->
                        <button @click="chatOpen = true" class="relative inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm hover:from-amber-600 hover:to-orange-600 transition-all">
                            <i data-lucide="bot" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">AI Assistant</span>
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                            </span>
                        </button>

                        <!-- Cart (for Customers) -->
                        @if(!auth()->user()->isAdmin())
                        <a href="{{ route('cart.index') }}" class="relative p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                            @php
                                $cartCount = session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0;
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 bg-orange-600 text-white text-[11px] font-bold h-5 w-5 rounded-full flex items-center justify-center shadow-sm">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                        @endif

                        <!-- User Profile Dropdown / Pill -->
                        <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                            @if(!auth()->user()->isAdmin())
                            <a href="{{ route('customer.preferences') }}" title="Edit AI Preferences" class="text-xs flex items-center gap-1 px-2 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 font-medium">
                                <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">Preferences</span>
                            </a>
                            @endif

                            <div class="text-right hidden sm:block">
                                <span class="text-xs font-bold text-slate-800 block leading-tight">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] font-semibold uppercase tracking-wider px-1.5 py-0.5 rounded {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">Sign in</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-orange-500 rounded-lg hover:bg-orange-600 shadow-sm">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Global Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 mb-4 flex items-center gap-3 text-emerald-800 shadow-sm">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 mb-4 flex items-center gap-3 text-rose-800 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 mb-4 text-rose-800 shadow-sm">
                <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i> Please check the form errors:
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Body Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <p class="font-medium text-slate-600">AI-Powered Cafeteria Management System &bull; Intelligent Food & Beverage Operations</p>
            <p class="mt-1 text-slate-400">Generative AI Engine &bull; Role-Based Access Control &bull; Semantic Recommendations</p>
        </div>
    </footer>

    <!-- Surprise Me Modal -->
    <div x-cloak x-show="surpriseModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div @click="surpriseModal = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6 border border-slate-200">
                <div class="text-center" x-show="surpriseData">
                    <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto text-amber-600 mb-3 shadow-inner">
                        <i data-lucide="sparkles" class="w-7 h-7"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full" x-text="surpriseData?.tagline"></span>
                    <h3 class="text-xl font-black text-slate-900 mt-2" x-text="surpriseData?.item?.name"></h3>
                    <p class="text-sm font-semibold text-orange-600 mt-0.5" x-text="surpriseData?.item?.price + ' EGP'"></p>
                    <p class="text-xs text-slate-500 mt-2 italic px-4" x-text="surpriseData?.why"></p>

                    <template x-if="surpriseData?.item?.image">
                        <img :src="surpriseData?.item?.image" class="w-full h-40 object-cover rounded-xl mt-4 border border-slate-100" />
                    </template>

                    <div class="mt-6 flex gap-2 justify-center">
                        <button @click="surpriseModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">Close</button>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="item_type" :value="surpriseData?.item?.type === 'food' ? 'food' : 'beverage'">
                            <input type="hidden" name="item_id" :value="surpriseData?.item?.id">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 shadow-md">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive AI Chatbot Slide-over / Drawer -->
    @auth
    <div x-cloak x-show="chatOpen" class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity" @click="chatOpen = false"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col border-l border-slate-200"
                 x-data="{
                     messages: [
                         {
                             role: 'assistant',
                             content: '{{ auth()->user()->isAdmin() ? '👨‍💼 Hello Administrator! I can provide live cafeteria stats, sales analytics, today\'s order numbers, low stock alerts, or top selling meals. How can I assist you?' : '👋 Welcome! I am your AI Cafeteria Assistant. I can recommend personalized meals, match your taste preferences, suggest cold/hot drinks, find meals under a budget, or compare dishes!' }}'
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
                                 const box = document.getElementById('chatBox');
                                 box.scrollTop = box.scrollHeight;
                             });
                         })
                         .catch(err => {
                             this.loading = false;
                             this.messages.push({ role: 'assistant', content: '⚠️ Could not communicate with AI engine right now.' });
                         });
                     }
                 }">

                <!-- Drawer Header -->
                <div class="p-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white flex items-center justify-between shadow-md">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center">
                            <i data-lucide="bot" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold leading-tight">Cafeteria AI Chatbot</h2>
                            <p class="text-[11px] text-orange-100">
                                Role: <span class="font-semibold uppercase tracking-wider underline">{{ auth()->user()->role }}</span>
                            </p>
                        </div>
                    </div>
                    <button @click="chatOpen = false" class="text-white/80 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div id="chatBox" class="flex-1 p-4 overflow-y-auto space-y-4 bg-slate-50">
                    <template x-for="(m, idx) in messages" :key="idx">
                        <div>
                            <div class="flex gap-2.5" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                                <template x-if="m.role === 'assistant'">
                                    <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                    </div>
                                </template>
                                <div class="max-w-[85%] rounded-2xl px-4 py-2.5 text-xs shadow-sm whitespace-pre-line leading-relaxed"
                                     :class="m.role === 'user'
                                        ? 'bg-orange-500 text-white rounded-br-none'
                                        : (m.status === 'forbidden' ? 'bg-rose-50 border border-rose-200 text-rose-900 rounded-bl-none' : 'bg-white border border-slate-200 text-slate-800 rounded-bl-none')">
                                    <span x-text="m.content"></span>
                                </div>
                            </div>

                            <!-- Clickable Suggestions if provided -->
                            <template x-if="m.suggestions && m.suggestions.length">
                                <div class="mt-2 pl-9 flex flex-wrap gap-1.5">
                                    <template x-for="(sug, sIdx) in m.suggestions" :key="sIdx">
                                        <button @click="sendMessage(sug)"
                                                class="text-[11px] px-2.5 py-1 bg-white hover:bg-orange-50 text-orange-700 border border-orange-200 rounded-full transition-colors shadow-2xs font-medium"
                                                x-text="sug"></button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div x-show="loading" class="flex items-center gap-2 text-xs text-slate-400 pl-2">
                        <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></div>
                        <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse delay-75"></div>
                        <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse delay-150"></div>
                        <span>AI is thinking...</span>
                    </div>
                </div>

                <!-- Suggested Quick Prompts -->
                <div class="px-4 py-2 bg-white border-t border-slate-100 overflow-x-auto">
                    <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block mb-1">Quick Inquiries:</span>
                    <div class="flex gap-1.5 whitespace-nowrap text-[11px]">
                        @if(auth()->user()->isAdmin())
                            <button @click="sendMessage('What is today\'s total sales?')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">💰 Today's Sales</button>
                            <button @click="sendMessage('How many orders were placed today?')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">📋 Orders Today</button>
                            <button @click="sendMessage('Which products have low stock?')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">⚠️ Low Stock</button>
                            <button @click="sendMessage('What is the most ordered food?')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">🥇 Top Food</button>
                            <button @click="sendMessage('What are the top 5 selling items?')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">🏆 Top 5</button>
                        @else
                            <button @click="sendMessage('What food do you recommend for me?')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">✨ My AI Picks</button>
                            <button @click="sendMessage('Recommend something spicy')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">🌶️ Spicy Food</button>
                            <button @click="sendMessage('I want something under 100 EGP')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">💵 Under 100 EGP</button>
                            <button @click="sendMessage('What is the healthiest meal available?')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded-md text-slate-700 font-medium">🥗 Healthy Low-Cal</button>
                            <button @click="sendMessage('What are today\'s total sales?')" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-md font-medium" title="Test Admin Permission Barrier">🛡️ Test RBAC Barrier</button>
                        @endif
                    </div>
                </div>

                <!-- Input Footer -->
                <div class="p-3 bg-white border-t border-slate-200">
                    <form @submit.prevent="sendMessage()" class="flex gap-2">
                        <input type="text"
                               x-model="input"
                               placeholder="{{ auth()->user()->isAdmin() ? 'Ask about sales, stock, customers, orders...' : 'Ask about meals, ingredients, budget, drinks...' }}"
                               class="flex-1 text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                        <button type="submit"
                                :disabled="loading || !input.trim()"
                                class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 disabled:opacity-50 text-white rounded-xl text-xs font-bold flex items-center justify-center transition-all shadow-sm">
                            <i data-lucide="send" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @endauth

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
        document.addEventListener('alpine:initialized', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
