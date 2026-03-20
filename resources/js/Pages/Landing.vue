<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const faqOpen = ref(null);
const toggleFaq = (index) => {
    faqOpen.value = faqOpen.value === index ? null : index;
};

const faqs = [
    { q: 'Preciso gravar vídeos profissionais?', a: 'Não! Vídeos gravados com o celular já funcionam perfeitamente. O importante é mostrar seus pratos de forma real e atrativa.' },
    { q: 'Funciona para delivery?', a: 'Sim! Você pode compartilhar o link do seu cardápio digital em qualquer plataforma, redes sociais ou WhatsApp.' },
    { q: 'O QR Code já vem pronto?', a: 'Sim! O QR Code é gerado automaticamente e você pode personalizar cores e adicionar sua logo.' },
    { q: 'Posso editar categorias e imagens?', a: 'Sim! Tudo é 100% editável. Categorias, imagens, vídeos, descrições e preços.' },
    { q: 'Quantos pratos posso adicionar?', a: 'No plano Pro, ilimitado! Adicione quantos pratos quiser sem restrições.' },
    { q: 'Preciso instalar algo?', a: 'Não! O ClicaFood é 100% web. Funciona em qualquer dispositivo com navegador.' },
    { q: 'Tem métricas detalhadas?', a: 'Sim! Dashboard completo com visualizações, cliques, vídeos assistidos e muito mais.' },
];

const whyCards = [
    { icon: '🎬', title: 'Vídeos que Vendem', desc: 'Mostre seus pratos em ação. Vídeos aumentam pedidos em até 73%.' },
    { icon: '📱', title: 'Stories dos Pratos', desc: 'Estilo Instagram Stories para seu cardápio. Seus clientes vão amar.' },
    { icon: '📊', title: 'Métricas em Tempo Real', desc: 'Saiba exatamente o que seus clientes querem. Dados que geram lucro.' },
    { icon: '🖼️', title: 'Categorias com Imagem', desc: 'Categorias visuais que guiam o cliente direto ao pedido.' },
    { icon: '📷', title: 'QR Code Ilimitado', desc: 'QR Code diferente para cada mesa, balcão ou ambiente.' },
    { icon: '⚡', title: 'Gestão Simples e Poderosa', desc: 'Dashboard completo em um só lugar. Sem complicação.' },
];

const counters = ref([
    { value: 0, target: 73, suffix: '%', label: 'mais pedidos com vídeo' },
    { value: 0, target: 2.4, suffix: 'x', label: 'aumento no ticket médio' },
    { value: 0, target: 89, suffix: '%', label: 'engajamento no cardápio' },
    { value: 0, target: 45, suffix: '%', label: 'tempo de indecisão', prefix: '-' },
]);

const animateCounters = () => {
    counters.value.forEach((counter, i) => {
        const duration = 2000;
        const steps = 60;
        const increment = counter.target / steps;
        let current = 0;
        const interval = setInterval(() => {
            current += increment;
            if (current >= counter.target) {
                counters.value[i].value = counter.target;
                clearInterval(interval);
            } else {
                counters.value[i].value = Math.round(current * 10) / 10;
            }
        }, duration / steps);
    });
};

const visible = ref({});
onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                visible.value[entry.target.dataset.section] = true;
                if (entry.target.dataset.section === 'numbers') {
                    animateCounters();
                }
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('[data-section]').forEach(el => observer.observe(el));
});

const testimonials = [
    { name: 'Carlos Silva', role: 'Dono', restaurant: 'Burguer House', text: 'Depois do ClicaFood, meus pedidos aumentaram 40%. Os vídeos dos hambúrgueres fazem toda a diferença!' },
    { name: 'Ana Rodrigues', role: 'Gerente', restaurant: 'Sushi Express', text: 'Meus clientes adoram ver os pratos em vídeo antes de pedir. O ticket médio subiu muito!' },
    { name: 'Roberto Mendes', role: 'Chef', restaurant: 'Cantina Bella', text: 'A gestão ficou muito mais simples. Em 5 minutos atualizo todo o cardápio com fotos e vídeos.' },
];
</script>

<template>
    <Head title="ClicaFood - Cardápio Digital com Vídeo" />

    <div class="min-h-screen bg-[#0a0a0a] text-white">
        <!-- Header -->
        <header class="fixed top-0 w-full z-50 bg-[#0a0a0a]/90 backdrop-blur-md border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center font-bold text-sm">CF</div>
                    <span class="text-xl font-bold">Clica<span class="text-brand">Food</span></span>
                </div>
                <div class="flex items-center gap-4">
                    <Link href="/login" class="text-gray-300 hover:text-white transition">Entrar</Link>
                    <Link href="/register" class="bg-brand hover:bg-brand-dark px-5 py-2 rounded-lg font-semibold transition">
                        Começar Agora
                    </Link>
                </div>
            </div>
        </header>

        <!-- Social Proof Banner -->
        <div class="pt-20 bg-brand/10 border-b border-brand/20">
            <div class="py-3 text-center">
                <p class="text-sm font-medium text-brand">🔥 5.000+ empresas já usam o ClicaFood</p>
            </div>
        </div>

        <!-- Hero -->
        <section class="pt-20 pb-32 px-4">
            <div class="max-w-5xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-black leading-tight mb-6">
                    Domine a fome,<br>
                    controle o pedido,<br>
                    <span class="text-brand">destrua a concorrência</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-400 mb-10 max-w-2xl mx-auto">
                    O cardápio digital com vídeo que transforma visualizações em vendas.
                    Seus pratos merecem ser vistos em movimento.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                    <Link href="/register" class="bg-brand hover:bg-brand-dark px-8 py-4 rounded-lg font-bold text-lg transition transform hover:scale-105">
                        Começar Agora — É Grátis
                    </Link>
                    <a href="#demo" class="border border-gray-600 hover:border-gray-400 px-8 py-4 rounded-lg font-bold text-lg transition">
                        Ver Demo
                    </a>
                </div>
                <div class="flex items-center justify-center gap-2 text-sm text-gray-400">
                    <div class="flex text-yellow-400">★★★★★</div>
                    <span>4,8/5 baseado em 1.247 avaliações</span>
                </div>
            </div>
        </section>

        <!-- Why ClicaFood -->
        <section data-section="why" class="py-20 px-4 bg-[#111]">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-4">Por que o <span class="text-brand">ClicaFood</span>?</h2>
                <p class="text-gray-400 text-center mb-12 max-w-xl mx-auto">Tudo que você precisa para dominar o mercado gastronômico digital.</p>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="card in whyCards" :key="card.title"
                        class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-brand/50 transition group">
                        <div class="text-3xl mb-4">{{ card.icon }}</div>
                        <h3 class="text-xl font-bold mb-2 group-hover:text-brand transition">{{ card.title }}</h3>
                        <p class="text-gray-400">{{ card.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Numbers -->
        <section data-section="numbers" class="py-20 px-4">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Números que <span class="text-brand">impressionam</span></h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="counter in counters" :key="counter.label" class="text-center">
                        <div class="text-4xl md:text-5xl font-black text-brand mb-2">
                            {{ counter.prefix || '+' }}{{ counter.value }}{{ counter.suffix }}
                        </div>
                        <p class="text-gray-400 text-sm">{{ counter.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Target Audience -->
        <section class="py-20 px-4 bg-[#111]">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-12">Para quem é o <span class="text-brand">ClicaFood</span>?</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div v-for="biz in ['Restaurantes Executivos', 'Bares e Pubs', 'Hamburguerias', 'Steakhouses', 'Delivery', 'Eventos e Buffets']"
                        :key="biz" class="bg-gray-900 border border-gray-800 rounded-xl p-6 hover:border-brand/50 transition">
                        <p class="font-semibold">{{ biz }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Before vs After -->
        <section class="py-20 px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Antes vs <span class="text-brand">Depois</span></h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-gray-900 border border-red-900/50 rounded-xl p-8">
                        <h3 class="text-xl font-bold mb-6 text-red-400">❌ Sem ClicaFood</h3>
                        <ul class="space-y-3 text-gray-400">
                            <li>Cardápio em PDF estático</li>
                            <li>Sem vídeos dos pratos</li>
                            <li>Sem métricas de acesso</li>
                            <li>QR Code genérico</li>
                            <li>Cliente indeciso</li>
                            <li>Pedidos perdidos</li>
                        </ul>
                    </div>
                    <div class="bg-gray-900 border border-green-900/50 rounded-xl p-8">
                        <h3 class="text-xl font-bold mb-6 text-green-400">✅ Com ClicaFood</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li>Cardápio digital interativo</li>
                            <li>Vídeos dos pratos em destaque</li>
                            <li>Métricas em tempo real</li>
                            <li>QR Code personalizado</li>
                            <li>Cliente decidido e engajado</li>
                            <li>Pedidos aumentam até 73%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-20 px-4 bg-[#111]">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">O que dizem nossos <span class="text-brand">clientes</span></h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <div v-for="t in testimonials" :key="t.name"
                        class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <p class="text-gray-300 mb-4 italic">"{{ t.text }}"</p>
                        <div>
                            <p class="font-bold">{{ t.name }}</p>
                            <p class="text-sm text-gray-500">{{ t.role }} — {{ t.restaurant }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section class="py-20 px-4">
            <div class="max-w-lg mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Plano <span class="text-brand">Pro</span></h2>
                <div class="bg-gray-900 border-2 border-brand rounded-2xl p-8 text-center relative overflow-hidden">
                    <div class="absolute top-4 right-4 bg-brand text-white text-xs font-bold px-3 py-1 rounded-full">7 DIAS GRÁTIS</div>
                    <h3 class="text-2xl font-bold mb-2">Plano Pro</h3>
                    <div class="text-5xl font-black text-brand mb-1">R$ 97<span class="text-lg text-gray-400">/mês</span></div>
                    <p class="text-gray-500 text-sm mb-8">Cancele quando quiser</p>
                    <ul class="text-left space-y-3 mb-8">
                        <li v-for="f in ['Itens ilimitados no cardápio', 'Upload de vídeos ilimitados', 'QR Codes ilimitados e personalizáveis', 'Stories dos pratos em destaque', 'Métricas e analytics completo', 'Personalização de cores e logo', 'Suporte prioritário']"
                            :key="f" class="flex items-center gap-3">
                            <span class="text-green-400">✓</span>
                            <span class="text-gray-300">{{ f }}</span>
                        </li>
                    </ul>
                    <Link href="/register" class="block w-full bg-brand hover:bg-brand-dark py-4 rounded-lg font-bold text-lg transition">
                        Começar Agora — 7 Dias Grátis
                    </Link>
                    <p class="text-xs text-gray-500 mt-4">Garantia de 30 dias. Sem compromisso.</p>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-20 px-4 bg-brand/5">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-black mb-6">Pronto para <span class="text-brand">dominar</span>?</h2>
                <p class="text-gray-400 mb-8">Junte-se a milhares de restaurantes que já transformaram seus resultados.</p>
                <div class="flex flex-wrap justify-center gap-8 mb-10 text-sm text-gray-400">
                    <span>27.000+ usuários</span>
                    <span>4,8/5 avaliação</span>
                    <span>98% satisfação</span>
                </div>
                <Link href="/register" class="inline-block bg-brand hover:bg-brand-dark px-10 py-4 rounded-lg font-bold text-lg transition transform hover:scale-105">
                    Criar Minha Conta Grátis
                </Link>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-20 px-4">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Perguntas <span class="text-brand">Frequentes</span></h2>
                <div class="space-y-4">
                    <div v-for="(faq, i) in faqs" :key="i"
                        class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                        <button @click="toggleFaq(i)"
                            class="w-full px-6 py-4 text-left flex items-center justify-between font-semibold hover:text-brand transition">
                            <span>{{ faq.q }}</span>
                            <span class="transform transition" :class="faqOpen === i ? 'rotate-180' : ''">▼</span>
                        </button>
                        <div v-show="faqOpen === i" class="px-6 pb-4 text-gray-400">
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 px-4 border-t border-gray-800">
            <div class="max-w-5xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-brand rounded flex items-center justify-center font-bold text-xs">CF</div>
                    <span class="font-bold">Clica<span class="text-brand">Food</span></span>
                </div>
                <div class="flex gap-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-gray-300 transition">Termos de Uso</a>
                    <a href="#" class="hover:text-gray-300 transition">Política de Privacidade</a>
                </div>
                <p class="text-sm text-gray-600">© 2024-2026 ClicaFood. Todos os direitos reservados.</p>
            </div>
        </footer>

        <!-- WhatsApp Float -->
        <a href="https://wa.me/5500000000000?text=Ol%C3%A1!%20Quero%20saber%20mais%20sobre%20o%20ClicaFood"
            target="_blank"
            class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 w-14 h-14 rounded-full flex items-center justify-center shadow-lg transition transform hover:scale-110">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </a>
    </div>
</template>
