<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-slate-50 font-sans text-slate-800 relative overflow-hidden selection:bg-blue-500 selection:text-white"
    >
        <!-- Background Elements -->
        <div class="fixed inset-0 z-0">
             <!-- Light gradient -->
             <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-blue-50"></div>
             <!-- Floating Particles - darker for visibility on light bg -->
             <div class="absolute inset-0 overflow-hidden pointer-events-none">
                 <div v-for="n in 6" :key="n" class="absolute rounded-full bg-blue-300/20 blur-3xl animate-float" 
                      :style="`
                        width: ${Math.random() * 400 + 200}px; 
                        height: ${Math.random() * 400 + 200}px; 
                        left: ${Math.random() * 100}%; 
                        top: ${Math.random() * 100}%; 
                        animation-delay: ${Math.random() * 5}s;
                        animation-duration: ${Math.random() * 15 + 15}s;
                      `">
                 </div>
             </div>
        </div>

        <div class="z-10 w-full flex flex-col items-center">
            <div class="mb-8 flex flex-col items-center animate-fade-in-down">
                <Link href="/nava-clinic" class="flex flex-col items-center group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-blue-200 rounded-full blur-xl opacity-50 group-hover:opacity-80 transition-opacity duration-500 animate-pulse-slow"></div>
                        <img src="/images/logo.png" alt="Nava Clinic Logo" class="relative w-28 h-28 mb-4 rounded-full shadow-xl ring-4 ring-white group-hover:ring-blue-100 transition-all duration-500 transform group-hover:scale-105" />
                    </div>
                    <span class="text-4xl font-bold tracking-tight text-blue-900 font-serif drop-shadow-sm">
                        NAVA CLINIC
                    </span>
                    <span class="text-lg text-slate-500 font-light tracking-[0.2em] mt-2 uppercase">
                       The Art of Thai Healing
                    </span>
                </Link>
            </div>

            <div
                class="w-full overflow-hidden bg-white/80 backdrop-blur-xl px-8 py-10 shadow-xl sm:max-w-md sm:rounded-3xl border border-white/60 ring-1 ring-slate-100/50 animate-fade-in-up transition-all duration-500 hover:shadow-2xl hover:border-blue-100"
            >
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}
.animate-float {
  animation: float infinite ease-in-out;
}

@keyframes pulse-slow {
    0%, 100% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}
.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down {
    animation: fadeInDown 0.8s ease-out;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out 0.2s backwards;
}
</style>
