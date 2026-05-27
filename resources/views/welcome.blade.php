<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Kinetic Lab') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="overflow-x-hidden">
        <header class="fixed top-0 w-full z-50 bg-[#f8f6f5]/80 backdrop-blur-md border-b-4 border-outline-variant/20">
            <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black text-primary tracking-tight">{{ config('app.name', 'Kinetic Lab') }}</span>
                </div>
                <nav class="hidden md:flex gap-8 items-center">
                    <a class="text-primary font-bold text-sm uppercase tracking-widest hover:bg-surface-container-low transition-colors px-4 py-2 rounded-full" href="#">Learn</a>
                    <a class="text-outline-variant font-bold text-sm uppercase tracking-widest hover:text-primary transition-colors px-4 py-2 rounded-full" href="#">Leaderboard</a>
                    <a class="text-outline-variant font-bold text-sm uppercase tracking-widest hover:text-primary transition-colors px-4 py-2 rounded-full" href="#">Shop</a>
                    <div class="flex items-center gap-4 ml-4">
                        <div class="flex items-center gap-1 text-primary">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                            <span class="font-bold">1,240</span>
                        </div>
                        @if (Route::has('login'))
                            <livewire:welcome.navigation />
                        @endif
                    </div>
                </nav>
                <button class="md:hidden text-primary">
                    <span class="material-symbols-outlined text-3xl">menu</span>
                </button>
            </div>
        </header>

        <main class="pt-24">
            <section class="relative overflow-hidden px-6 py-16 lg:py-24 max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="z-10">
                        <span class="inline-block bg-tertiary-container text-on-tertiary-fixed font-bold px-4 py-1 rounded-full text-xs uppercase tracking-[0.2em] mb-6">
                            Educational Revolution
                        </span>
                        <h1 class="text-5xl lg:text-7xl font-black text-on-surface leading-[1.1] tracking-tight mb-8">Master your <span class="text-primary italic">technical interviews</span></h1>
                        <p class="text-xl text-on-surface-variant leading-relaxed mb-10 max-w-xl">Ready to take the next step in your career? {{ config('app.name', 'Kinetic Lab') }} transforms the preparation of algorithms and systems into an epic adventure with daily code challenges and real-world simulations.</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-primary text-on-primary px-10 py-5 rounded-full font-bold text-lg tactile-button-primary transition-all active:scale-95">
                                    Start now
                                </a>
                            @endif
                            <button class="bg-surface-container-low text-primary px-10 py-5 rounded-full font-bold text-lg tactile-card border-2 border-outline-variant/20 hover:bg-surface-container-high transition-all">
                                See methodology
                            </button>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary-container/30 rounded-full blur-3xl -z-10"></div>
                        <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-tertiary-container/30 rounded-full blur-3xl -z-10"></div>
                        <div class="relative z-0 p-4">
                            <img alt="Mascot and UI Preview" class="rounded-xl tactile-card border-4 border-outline-variant/20 transform rotate-2 hover:rotate-0 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBDHi0ZZKLgwECuUC3PWqzRBgJAnaQchxZ4DOe0P0N0bamMLQyQ1AhSPjDhfoD7KAgAn-rSJCMo0jWWnFx_iL60tIC-lpiysbuwHoeLQq2oTmtUGZO7ObzHM6G9jiJzZxY4AYYJRflSfe5aWvpnB5lhptew3aJIcKdaZZKL3oUsNjdea0BEk9EQBU3gkiCAiHdZipW-8sMzv8aYwVm9OlphFiCmWSPpEocpPia_9l1KcYjGuA45YR087HPTcdngUWPsf-FPJ14Qw"/>
                        </div>
                    </div>
                </div>
            </section>

            <section class="px-6 py-20 bg-surface-container-low">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl lg:text-4xl font-black text-on-surface tracking-tight mb-4">Why study with us?</h2>
                        <p class="text-on-surface-variant max-w-2xl mx-auto">We designed an experience that keeps you motivated while you learn the key concepts for your academic and professional future.</p>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-surface-container-lowest p-10 rounded-lg tactile-card border-4 border-outline-variant/10 hover:-translate-y-2 transition-transform">
                            <div class="w-16 h-16 bg-primary-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">sports_esports</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Code Progression</h3>
                            <p class="text-on-surface-variant leading-relaxed">Solve logic problems, level up, and keep your commit streak active. It's not just studying, it's building your technical mastery day after day.</p>
                        </div>
                        <div class="bg-surface-container-lowest p-10 rounded-lg tactile-card border-4 border-outline-variant/10 hover:-translate-y-2 transition-transform">
                            <div class="w-16 h-16 bg-secondary-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-secondary text-3xl" style="font-variation-settings: 'FILL' 1;">menu_book</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">FAANG Challenges</h3>
                            <p class="text-on-surface-variant leading-relaxed">Access thousands of algorithmic, data structure, and system design challenges extracted from real interview processes at Google, Meta, and Amazon.</p>
                        </div>
                        <div class="bg-surface-container-lowest p-10 rounded-lg tactile-card border-4 border-outline-variant/10 hover:-translate-y-2 transition-transform">
                            <div class="w-16 h-16 bg-tertiary-container rounded-full flex items-center justify-center mb-6">
                                <span class="material-symbols-outlined text-on-tertiary-container text-3xl" style="font-variation-settings: 'FILL' 1;">groups</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4">Dev Leagues</h3>
                            <p class="text-on-surface-variant leading-relaxed">Compete in weekly problem-solving leagues with developers from across the region. Topping the ranking attracts the attention of the best recruiters.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="px-6 py-20 lg:py-32 overflow-hidden">
                <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16">
                    <div class="w-full lg:w-1/2">
                        <h2 class="text-4xl lg:text-5xl font-black text-on-surface mb-8 leading-tight">The interface you'll <span class="text-secondary">love using</span></h2>
                        <ul class="space-y-6">
                            <li class="flex items-start gap-4">
                                <div class="mt-1 bg-primary-container p-1 rounded-full">
                                    <span class="material-symbols-outlined text-primary text-xl">check</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">Personalized Roadmaps</h4>
                                    <p class="text-on-surface-variant">Our algorithm identifies your weaknesses in data structures and reinforces the exact concepts you need for your next interview.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="mt-1 bg-primary-container p-1 rounded-full">
                                    <span class="material-symbols-outlined text-primary text-xl">check</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">Complexity Analysis</h4>
                                    <p class="text-on-surface-variant">Receive immediate feedback on the efficiency of your code, helping you always think about time and space optimization.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="w-full lg:w-1/2 relative">
                        <div class="relative z-10 bg-white rounded-lg p-6 tactile-card border-4 border-outline-variant/20">
                            <div class="flex items-center justify-between mb-8">
                                <span class="material-symbols-outlined text-outline-variant">close</span>
                                <div class="w-full h-3 bg-surface-container mx-4 rounded-full overflow-hidden">
                                    <div class="bg-tertiary-container h-full w-[65%] rounded-full shadow-[0_0_10px_#fec700]"></div>
                                </div>
                                <div class="flex items-center gap-1 text-primary">
                                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">favorite</span>
                                    <span class="font-bold">5</span>
                                </div>
                            </div>
                            <div class="mb-10">
                                <h3 class="text-2xl font-bold mb-6">What is the time complexity of a binary search in the worst case?</h3>
                                <div class="space-y-4">
                                    <button class="w-full text-left p-4 rounded-lg border-2 border-outline-variant/30 hover:border-secondary transition-all flex justify-between items-center group">
                                        <span class="font-medium">A. O(n)</span>
                                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="material-symbols-outlined text-secondary">radio_button_checked</span>
                                        </span>
                                    </button>
                                    <button class="w-full text-left p-4 rounded-lg border-4 border-secondary bg-secondary-container/20 flex justify-between items-center">
                                        <span class="font-bold text-secondary">B. O(log n)</span>
                                        <span class="material-symbols-outlined text-secondary">radio_button_checked</span>
                                    </button>
                                    <button class="w-full text-left p-4 rounded-lg border-2 border-outline-variant/30 hover:border-secondary transition-all flex justify-between items-center">
                                        <span class="font-medium">C. O(n²)</span>
                                    </button>
                                </div>
                            </div>
                            <div class="bg-error-container/10 p-4 rounded-lg border-2 border-error/20 flex gap-4 items-center">
                                <div class="bg-error text-on-error w-10 h-10 rounded-full flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-lg">error</span>
                                </div>
                                <div>
                                    <p class="text-error font-bold text-sm">Perfect optimization!</p>
                                    <p class="text-on-error-container text-xs">Correct. By dividing the search space in half at each step, the complexity is reduced logarithmically. Log₂(n).</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-full -z-10 animate-pulse"></div>
                        <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-tertiary/10 rounded-full -z-10"></div>
                    </div>
                </div>
            </section>

            <section class="px-6 py-20 bg-primary">
                <div class="max-w-7xl mx-auto">
                    <div class="grid md:grid-cols-4 gap-8 text-center">
                        <div class="text-on-primary">
                            <div class="text-5xl font-black mb-2">+10k</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-80">Developers</div>
                        </div>
                        <div class="text-on-primary">
                            <div class="text-5xl font-black mb-2">88%</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-80">More offers received</div>
                        </div>
                        <div class="text-on-primary">
                            <div class="text-5xl font-black mb-2">+2M</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-80">Lines of code tested</div>
                        </div>
                        <div class="text-on-primary">
                            <div class="text-5xl font-black mb-2">#1</div>
                            <div class="text-sm font-bold uppercase tracking-widest opacity-80">Platform for Devs in Latam</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative px-6 py-24 lg:py-32 overflow-hidden">
                <div class="absolute inset-0 -z-10">
                    <div class="absolute top-0 left-0 w-full h-full opacity-5 bg-[radial-gradient(circle_at_2px_2px,_#2a6900_1px,_transparent_0)] bg-[length:40px_40px]"></div>
                </div>
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 bg-surface-container-high px-4 py-2 rounded-full mb-8 border border-outline-variant/20">
                        <div class="flex -space-x-2">
                            <img alt="Avatar" class="w-8 h-8 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDRRw67IEGK_0lVufuswfGqeffPk0g_ZGPiNd6FqPZloSnGPLuJWD7c32Lr8Z_i7P4eeZ7xNfqxnvNb29vproyyJrvIbs5rfWOvmVO_2gswqbJGz9KGBwmY2io4Z9hBtke9Jeiwg4iY95eo8AXHYrCy95BxoKLfQTFiFMlM2j1uP_lH0J1-cG4fl2ZGcb4AmAXuUWlYa0l_n6rOA_oudW0yFc-k8DrTwlmjbfrCuAy_mVXQKYzBCb3LlAiEx8jmqfNmartx1rJjFA"/>
                            <img alt="Avatar" class="w-8 h-8 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2KyZxILaQOhkEloqYPyEXeIWcCZndjyjQL7_9oEep6NEdVld_3P_XBhthELVqPmEc7VqxcqPgc9UoG6icRd8iluAXIQi477u4w4UxeysA0Izj-LStsf8wFY7A5HDgovi52Yl69GVsprCXzf23H1Gm1ZHOTkitFgbf5DMjokQUsXjyVx60WRVQpwURPd0Vr7n0gRiekg5iSy3wl2CHMl01ZLPECcmNYAJ2TKGGFYRqQRLezbqkLru_ErvkU50XwS2mVLLfQEiEhw"/>
                            <img alt="Avatar" class="w-8 h-8 rounded-full border-2 border-white" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCnpJK4hFTECxVGx5H34UuAKqt0A5-U5RG5g2O-sZ8SXAYZPT3dVjT1kZuI6mw4JNnXqN39O2JquRLxOjW37jNWh3lr5qw9RRjnEAR9JQeePZRsT9V2rotMhgS7gD4R7lZghKT4wKhz3ZsdBF_KJ37rmtKAw_x3xjTpJNs30khEOQcUd7RnHzEDqQ9jpsJI1AxYZ0QPKMi9mHOOigcg3Z8ou6ESM6iooPGp8Fu3OdqT0bBlTBzaBU4r33E6Lv4boJRLD8xHwGPMeQ"/>
                        </div>
                        <span class="text-sm font-bold text-on-surface-variant px-2">Join 10,000+ developers</span>
                    </div>
                    <h2 class="text-4xl lg:text-6xl font-black text-on-surface mb-8 leading-tight">Your Senior career starts <span class="text-primary italic">here.</span></h2>
                    <p class="text-xl text-on-surface-variant mb-12">Sign up for free today and discover why {{ config('app.name', 'Kinetic Lab') }} is the secret weapon of successful software engineers.</p>
                    <div class="flex flex-col sm:flex-row justify-center gap-6">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-primary text-on-primary px-12 py-6 rounded-full font-bold text-xl tactile-button-primary transition-all active:scale-95 flex items-center justify-center gap-3 group">
                                Create my free account
                                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                    <div class="mt-12 flex justify-center gap-8 items-center opacity-60 grayscale hover:grayscale-0 transition-all">
                        <span class="font-black text-xl">App Store</span>
                        <span class="font-black text-xl">Google Play</span>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-surface-container-low border-t-4 border-outline-variant/10 py-12 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex flex-col items-center md:items-start gap-4">
                    <span class="text-2xl font-black text-primary tracking-tight">{{ config('app.name', 'Kinetic Lab') }}</span>
                    <p class="text-sm text-on-surface-variant">{{ date('Y') }} {{ config('app.name', 'Kinetic Lab') }}. All rights reserved.</p>
                </div>
                <div class="flex gap-8">
                    <a class="text-sm font-bold text-on-surface-variant hover:text-primary transition-colors" href="#">Terms</a>
                    <a class="text-sm font-bold text-on-surface-variant hover:text-primary transition-colors" href="#">Privacy</a>
                    <a class="text-sm font-bold text-on-surface-variant hover:text-primary transition-colors" href="#">Contact</a>
                </div>
                <div class="flex gap-4">
                    <button class="w-10 h-10 bg-surface-container-lowest rounded-full flex items-center justify-center tactile-card border border-outline-variant/20">
                        <span class="material-symbols-outlined text-on-surface-variant">public</span>
                    </button>
                    <button class="w-10 h-10 bg-surface-container-lowest rounded-full flex items-center justify-center tactile-card border border-outline-variant/20">
                        <span class="material-symbols-outlined text-on-surface-variant">share</span>
                    </button>
                </div>
            </div>
        </footer>

        <script>
            document.querySelectorAll('.tactile-button-primary').forEach(button => {
                button.addEventListener('mouseenter', () => {
                    button.classList.add('scale-105');
                });
                button.addEventListener('mouseleave', () => {
                    button.classList.remove('scale-105');
                });
            });

            const statsCards = document.querySelectorAll('.text-on-primary');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-bounce');
                        setTimeout(() => entry.target.classList.remove('animate-bounce'), 1000);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            statsCards.forEach(card => observer.observe(card));
        </script>
    </body>
</html>
