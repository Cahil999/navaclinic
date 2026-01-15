<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref, onUnmounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const scrollY = ref(0);

const handleScroll = () => {
    scrollY.value = window.scrollY;
};

onMounted(() => {
    AOS.init({
        duration: 1200,
        easing: 'ease-out-quart',
        once: true,
        offset: 100,
    });
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <Head title="Siam Retreat - Premium Spa & Wellness" />
    
    <div class="min-h-screen bg-stone-950 text-amber-50 font-serif selection:bg-amber-900 selection:text-amber-50 overflow-x-hidden relative">
        
        <!-- Custom Cursor Follower (optional, keeping it simple for now) -->

        <!-- Navigation -->
        <nav class="fixed top-0 w-full z-50 transition-all duration-500 ease-out border-b"
             :class="scrollY > 50 ? 'bg-stone-950/90 backdrop-blur-md border-white/5 py-4' : 'bg-transparent border-transparent py-8'">
            <div class="container mx-auto px-6 flex items-center justify-between">
                <Link href="/" class="group relative z-50">
                    <h1 class="text-2xl md:text-3xl font-display font-medium tracking-[0.15em] text-amber-100 transition-colors duration-300 group-hover:text-white">
                        SIAM <span class="text-amber-500">RETREAT</span>
                    </h1>
                </Link>
                
                <div class="hidden md:flex items-center gap-12 text-xs tracking-[0.2em] uppercase text-amber-100/70 font-sans">
                    <Link href="#" class="hover:text-amber-400 transition-colors duration-300 relative group">
                        Our Story
                        <span class="absolute -bottom-2 left-0 w-0 h-px bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </Link>
                    <Link href="#" class="hover:text-amber-400 transition-colors duration-300 relative group">
                        Treatments
                        <span class="absolute -bottom-2 left-0 w-0 h-px bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </Link>
                    <Link href="#" class="hover:text-amber-400 transition-colors duration-300 relative group">
                        Sanctuary
                         <span class="absolute -bottom-2 left-0 w-0 h-px bg-amber-400 transition-all duration-300 group-hover:w-full"></span>
                    </Link>
                    <Link :href="route('booking.create')" class="px-8 py-3 bg-amber-900/20 border border-amber-500/30 text-amber-200 hover:bg-amber-600 hover:border-amber-600 hover:text-white transition-all duration-500 rounded-sm">
                        Reserve
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="relative h-[110vh] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-stone-950/30 z-10"></div>
                <!-- Parallax Image -->
                <img src="https://images.unsplash.com/photo-1600334089648-b0d9d3028eb2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
                     alt="Siam Retreat Atmosphere" 
                     class="w-full h-full object-cover opacity-80 transition-transform duration-75 ease-linear will-change-transform"
                     :style="{ transform: `scale(1.1) translateY(${scrollY * 0.5}px)` }" />
                <div class="absolute inset-0 bg-gradient-to-b from-stone-950/60 via-transparent to-stone-950 z-20"></div>
            </div>

            <div class="relative z-30 text-center px-4 max-w-5xl mx-auto flex flex-col items-center">
                <div class="w-px h-32 bg-gradient-to-b from-transparent via-amber-200/50 to-transparent mb-12 animate-pulse-slow"></div>
                
                <h2 class="font-sans text-amber-300/90 text-xs md:text-sm tracking-[0.4em] uppercase mb-12" data-aos="fade-up" data-aos-delay="200">
                    The Ultimate Holistic Sanctuary
                </h2>
                
                <h1 class="font-display text-6xl md:text-9xl font-medium tracking-wide mb-14 text-white drop-shadow-2xl leading-none" data-aos="fade-up" data-aos-duration="1500">
                    Royal <span class="italic text-amber-500 font-light pr-4">Tranquility</span>
                </h1>
                
                <p class="font-serif text-stone-300 text-lg md:text-2xl font-light leading-relaxed max-w-3xl mx-auto mb-20 antialiased" data-aos="fade-up" data-aos-delay="400">
                    Rediscover the ancient art of healing in a sanctuary designed for <br class="hidden md:block" /> absolute serenity and profound relaxation.
                </p>
                
                <div data-aos="fade-up" data-aos-delay="600">
                    <Link href="#explore" class="group relative inline-flex items-center justify-center w-16 h-16 rounded-full border border-white/20 hover:border-amber-500/50 transition-all duration-500">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-6 h-6 text-white group-hover:text-amber-400 group-hover:translate-y-1 transition-all duration-300">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                        <div class="absolute inset-0 rounded-full border border-white/10 scale-125 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all duration-700"></div>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Aesthetic Content Section -->
        <section id="explore" class="py-40 bg-stone-950 relative overflow-hidden">
            <!-- Background Texture -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('https://www.transparenttextures.com/patterns/stardust.png');"></div>
            
            <div class="container mx-auto px-6 relative z-10">
                <!-- Grid 1 -->
                <div class="grid md:grid-cols-12 gap-16 items-center mb-56">
                    <div class="md:col-span-5 relative group" data-aos="fade-right">
                        <div class="relative overflow-hidden aspect-[3/4]">
                             <div class="absolute inset-0 bg-stone-950/20 group-hover:bg-transparent transition-colors duration-700 z-10"></div>
                             <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-full h-full border border-amber-500/30 -z-10 transition-transform duration-500 group-hover:translate-x-2 group-hover:translate-y-2"></div>
                    </div>
                    
                    <div class="md:col-span-1 hidden md:block"></div>
                    
                    <div class="md:col-span-6 space-y-12" data-aos="fade-left">
                        <div class="flex items-center gap-4 text-amber-500/80 mb-4">
                            <span class="w-12 h-px bg-current"></span>
                            <span class="font-sans text-xs tracking-[0.2em] uppercase">Philosophy</span>
                        </div>
                        <h3 class="font-display text-5xl md:text-7xl text-amber-50 leading-tight">
                            Timeless <br/><span class="italic text-amber-600">Elegance</span>
                        </h3>
                        <p class="text-stone-400 font-serif font-light leading-loose text-lg text-justify">
                            Immerse yourself in an environment crafted for peace. Every detail at Siam Retreat is curated to transport you away from the chaos of modern life into a realm of stillness and luxury. Our architecture reflects the balance of nature.
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-white/5">
                            <div class="space-y-2">
                                <h4 class="font-display text-2xl text-amber-200">Private Suites</h4>
                                <p class="text-stone-500 text-sm font-sans tracking-wide">Secluded sanctuaries for total privacy.</p>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-display text-2xl text-amber-200">Organic</h4>
                                <p class="text-stone-500 text-sm font-sans tracking-wide">100% natural, locally sourced botanicals.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid 2 (Reversed) -->
                <div class="grid md:grid-cols-12 gap-16 items-center">
                     <div class="md:col-span-6 space-y-12 order-2 md:order-1 relative" data-aos="fade-right">
                        <div class="flex items-center gap-4 text-amber-500/80 mb-4 justify-end md:justify-start">
                             <span class="font-sans text-xs tracking-[0.2em] uppercase">Experience</span>
                             <span class="w-12 h-px bg-current"></span>
                        </div>
                        <h3 class="font-display text-5xl md:text-7xl text-amber-50 leading-tight text-right md:text-left">
                            Holistic <br/><span class="italic text-amber-600">Restoration</span>
                        </h3>
                        <p class="text-stone-400 font-serif font-light leading-loose text-lg text-justify">
                            Our treatments blend ancient Eastern wisdom with modern wellness techniques. From deep tissue restoration to aromatic sensory journeys, find your balance with us. We invite you to pause, breathe, and heal.
                        </p>
                         <div class="flex justify-end md:justify-start pt-8">
                             <Link href="/services" class="group flex items-center gap-4 text-amber-200 hover:text-white transition-colors">
                                <span class="font-sans text-xs tracking-[0.3em] uppercase group-hover:tracking-[0.4em] transition-all duration-300">View Service Menu</span>
                                <span class="w-8 h-px bg-amber-500 group-hover:w-16 transition-all duration-300"></span>
                            </Link>
                        </div>
                    </div>
                    
                    <div class="md:col-span-1 hidden md:block order-1"></div>

                    <div class="md:col-span-5 relative group order-1 md:order-2" data-aos="fade-left">
                        <div class="relative overflow-hidden aspect-[4/5]">
                             <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
                              <div class="absolute bottom-0 left-0 p-8 z-20">
                                   <div class="font-display text-8xl text-white/10 group-hover:text-white/30 transition-colors duration-500">02</div>
                              </div>
                        </div>
                         <div class="absolute -top-6 -left-6 w-full h-full border border-stone-700 -z-10 transition-transform duration-500 group-hover:-translate-x-2 group-hover:-translate-y-2"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Signature Scroll Section (Parallax Text) -->
        <section class="py-32 overflow-hidden bg-amber-950/20 border-y border-white/5">
            <div class="whitespace-nowrap flex gap-12 animate-marquee opacity-30 select-none">
                 <span class="text-9xl font-display font-bold text-transparent stroke-text">ROYAL TRANQUILITY</span>
                 <span class="text-9xl font-display font-bold text-transparent stroke-text">SIAM RETREAT</span>
                 <span class="text-9xl font-display font-bold text-transparent stroke-text">HEALING SANCTUARY</span>
                 <span class="text-9xl font-display font-bold text-transparent stroke-text">ROYAL TRANQUILITY</span>
            </div>
        </section>

        <!-- CTA / Booking -->
        <section class="py-40 relative flex items-center justify-center overflow-hidden min-h-[80vh]">
            <div class="absolute inset-0 z-0">
                 <img src="https://images.unsplash.com/photo-1620733723572-11c52f7c2d82?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" 
                     class="w-full h-full object-cover opacity-20" />
                <div class="absolute inset-0 bg-stone-950/80"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-stone-950 via-transparent to-stone-950"></div>
            </div>
            
            <div class="relative z-10 text-center space-y-12 max-w-4xl px-6" data-aos="zoom-in">
                <div class="inline-block p-px bg-gradient-to-r from-transparent via-amber-500 to-transparent w-32 mb-8"></div>
                
                <h2 class="font-display text-6xl md:text-8xl text-amber-50 leading-tight">
                    Begin Your <br/><span class="italic text-amber-600">Journey</span>
                </h2>
                <p class="font-serif text-stone-400 text-xl font-light tracking-wide max-w-2xl mx-auto">
                    Limited daily availability to ensure absolute privacy and dedication to your wellness.
                </p>
                <div class="flex flex-col md:flex-row justify-center gap-8 pt-12">
                     <Link :href="route('booking.create')" class="group relative px-12 py-5 bg-amber-700 overflow-hidden">
                        <span class="relative z-10 font-sans text-xs tracking-[0.3em] font-bold text-amber-50 uppercase group-hover:text-white transition-colors">Book Appointment</span>
                        <div class="absolute inset-0 bg-amber-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
                    </Link>
                    <button class="group px-12 py-5 bg-transparent border border-stone-800 hover:border-amber-700 transition-colors duration-300">
                        <span class="font-sans text-xs tracking-[0.3em] font-medium text-stone-500 uppercase group-hover:text-amber-500 transition-colors">Contact Concierge</span>
                    </button>
                </div>
            </div>
        </section>

        <footer class="bg-stone-950 pt-24 pb-12 border-t border-white/5 relative z-10">
            <div class="container mx-auto px-6 grid md:grid-cols-4 gap-16 md:gap-8">
                <div class="md:col-span-2 space-y-8 pr-12">
                    <h3 class="font-display text-3xl tracking-wide text-amber-100">SIAM RETREAT</h3>
                    <p class="text-stone-500 font-serif font-light leading-relaxed text-sm max-w-md">
                        A refuge of calm in the heart of the city. Where tradition meets luxury, and silence speaks louder than words. We are dedicated to restoring your balance.
                    </p>
                </div>
                <div class="space-y-8">
                    <h4 class="font-sans text-xs font-bold tracking-[0.2em] text-amber-700/80">CONTACT</h4>
                    <p class="text-stone-400 font-serif font-light leading-loose text-sm">
                        123 Sukhumvit Road, Bangkok<br/>
                        concierge@siamretreat.com<br/>
                        +66 2 123 4567
                    </p>
                </div>
                 <div class="space-y-8">
                    <h4 class="font-sans text-xs font-bold tracking-[0.2em] text-amber-700/80">FOLLOW</h4>
                    <div class="flex flex-col gap-4 text-stone-500 font-sans text-xs tracking-widest uppercase">
                        <a href="#" class="hover:text-amber-500 transition-colors w-max">Instagram</a>
                        <a href="#" class="hover:text-amber-500 transition-colors w-max">Facebook</a>
                        <a href="#" class="hover:text-amber-500 transition-colors w-max">Line OA</a>
                    </div>
                </div>
            </div>
            
            <div class="container mx-auto px-6 mt-24 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center text-xs text-stone-700 font-sans tracking-widest uppercase">
                <p>&copy; 2026 Siam Retreat. All rights reserved.</p>
                <div class="flex gap-8 mt-4 md:mt-0">
                    <a href="#" class="hover:text-stone-500">Privacy</a>
                    <a href="#" class="hover:text-stone-500">Terms</a>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
.stroke-text {
    -webkit-text-stroke: 1px rgba(255, 255, 255, 0.1);
}

@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.animate-marquee {
    animation: marquee 40s linear infinite;
}

.animate-pulse-slow {
    animation: pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
