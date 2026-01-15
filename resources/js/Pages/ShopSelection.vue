<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, reactive } from 'vue';

const selectedCategory = ref('all');

const stats = reactive([
    { label: 'ร้านนวดคุณภาพ', value: '0', target: 50, suffix: '+' },
    { label: 'ผู้ใช้งาน', value: '0', target: 10, suffix: 'k+' },
    { label: 'รีวิว 5 ดาว', value: '0', target: 98, suffix: '%' },
]);

const animateStats = () => {
    stats.forEach(stat => {
        let start = 0;
        const end = stat.target;
        const duration = 2000;
        const startTime = performance.now();

        const update = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Ease out quart
            const ease = 1 - Math.pow(1 - progress, 4);
            
            const currentValue = Math.floor(start + (end - start) * ease);
            stat.value = currentValue + stat.suffix;

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        };
        requestAnimationFrame(update);
    });
};

onMounted(() => {
    animateStats();
});

const categories = [
    { id: 'all', label: 'ทั้งหมด' },
    { id: 'relaxation', label: 'เพื่อผ่อนคลายทั่วไป' },
    { id: 'treatment', label: 'เพื่อแก้อาการ/รักษา' },
    { id: 'experience', label: 'เพื่อประสบการณ์พิเศษ' },
];

const shops = [
    {
        id: 1,
        name: 'นวคลินิก',
        nameEn: 'Nava Clinic',
        desc: 'คลินิกการแพทย์แผนไทย<br/>โดยทีมแพทย์ปริญญา',
        image: '/images/logo.png',
        link: '/nava-clinic',
        category: 'treatment',
        isMain: true,
        status: 'active',
        openDays: 'อังคาร - อาทิตย์',
        openTime: '09:00 - 20:00',
        closedDay: 'หยุดทุกวันจันทร์',
        theme: {
            shadow: 'hover:shadow-blue-500/30',
            title: 'group-hover:text-blue-900',
            subtitle: 'group-hover:text-blue-600',
            overlay: 'bg-blue-600/0 group-hover:bg-blue-600/5'
        }
    },
    {
        id: 2,
        name: 'สยาม รีทรีต',
        nameEn: 'Siam Retreat',
        desc: 'สปาหรูใจกลางเมือง<br/>พร้อมห้องส่วนตัวระดับพรีเมียม',
        image: 'https://images.unsplash.com/photo-1600334089648-b0d9d3028eb2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        link: '#',
        category: 'experience',
        status: 'coming_soon',
        openDays: 'ทุกวัน',
        openTime: '10:00 - 22:00',
        theme: {
            shadow: 'hover:shadow-amber-500/30',
            title: 'group-hover:text-amber-900',
            subtitle: 'group-hover:text-amber-600',
            overlay: 'bg-amber-600/0 group-hover:bg-amber-600/5'
        }
    },
    {
        id: 3,
        name: 'ลานนา เวลเนส',
        nameEn: 'Lanna Wellness',
        desc: 'นวดตอกเส้นและประคบสมุนไพร<br/>ตำรับล้านนาแท้',
        image: 'https://images.unsplash.com/photo-1519823551278-64ac92734fb1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        link: '#',
        category: 'treatment',
        status: 'coming_soon',
        openDays: 'จันทร์ - เสาร์',
        openTime: '09:00 - 18:00',
        closedDay: 'หยุดวันอาทิตย์',
        theme: {
            shadow: 'hover:shadow-emerald-500/30',
            title: 'group-hover:text-emerald-900',
            subtitle: 'group-hover:text-emerald-600',
            overlay: 'bg-emerald-600/0 group-hover:bg-emerald-600/5'
        }
    },
    {
        id: 4,
        name: 'เออร์เบิน เอสเคป',
        nameEn: 'Urban Escape',
        desc: 'ผ่อนคลายความตึงเครียด<br/>บำบัดออฟฟิศซินโดรม',
        image: 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        link: '#',
        category: 'treatment',
        status: 'coming_soon',
        openDays: 'ทุกวัน',
        openTime: '10:00 - 21:00',
        theme: {
            shadow: 'hover:shadow-purple-500/30',
            title: 'group-hover:text-purple-900',
            subtitle: 'group-hover:text-purple-600',
            overlay: 'bg-purple-600/0 group-hover:bg-purple-600/5'
        }
    },
    {
        id: 5,
        name: 'ซีรีน นวดไทย',
        nameEn: 'Serene Massage',
        desc: 'มนต์เสน่ห์แห่งการนวดไทย<br/>ในบรรยากาศรีสอร์ต',
        image: 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        link: '#',
        category: 'relaxation',
        status: 'coming_soon',
        openDays: 'ทุกวัน',
        openTime: '09:00 - 23:00',
        theme: {
            shadow: 'hover:shadow-teal-500/30',
            title: 'group-hover:text-teal-900',
            subtitle: 'group-hover:text-teal-600',
            overlay: 'bg-teal-600/0 group-hover:bg-teal-600/5'
        }
    },
    {
        id: 6,
        name: 'เดอะ ทัช',
        nameEn: 'The Touch',
        desc: 'สัมผัสความผ่อนคลาย<br/>ด้วยเทคนิคเฉพาะตัว',
        image: 'https://images.unsplash.com/photo-1600334129128-685c5582fd35?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        link: '#',
        category: 'relaxation',
        status: 'coming_soon',
        openDays: 'ศุกร์ - อาทิตย์',
        openTime: '10:00 - 22:00',
        closedDay: 'เปิดเฉพาะสุดสัปดาห์',
        theme: {
            shadow: 'hover:shadow-rose-500/30',
            title: 'group-hover:text-rose-900',
            subtitle: 'group-hover:text-rose-600',
            overlay: 'bg-rose-600/0 group-hover:bg-rose-600/5'
        }
    },
];

const filteredShops = computed(() => {
    if (selectedCategory.value === 'all') {
        return shops;
    }
    return shops.filter(shop => shop.category === selectedCategory.value);
});
</script>

<template>
    <Head title="เลือกร้านนวด - Thai Massage Hub" />
    <div class="min-h-screen bg-slate-900 font-sans text-slate-800 flex flex-col items-center relative overflow-hidden selection:bg-blue-500 selection:text-white">
        
        <!-- Background Image with Overlay -->
        <div class="fixed inset-0 z-0">
             <img src="/images/storefront.png" alt="Clinic Atmosphere" class="w-full h-full object-cover blur-sm opacity-40 scale-105 animate-slow-zoom" />
             <div class="absolute inset-0 bg-gradient-to-br from-slate-900/95 via-blue-900/90 to-slate-900/95"></div>

             <!-- Floating Particles -->
             <div class="absolute inset-0 overflow-hidden pointer-events-none">
                 <div v-for="n in 5" :key="n" class="absolute rounded-full bg-blue-400/10 blur-xl animate-float" 
                      :style="`
                        width: ${Math.random() * 300 + 100}px; 
                        height: ${Math.random() * 300 + 100}px; 
                        left: ${Math.random() * 100}%; 
                        top: ${Math.random() * 100}%; 
                        animation-delay: ${Math.random() * 5}s;
                        animation-duration: ${Math.random() * 10 + 10}s;
                      `">
                 </div>
             </div>
        </div>

        <div class="relative z-10 w-full max-w-7xl px-4 py-16 flex flex-col min-h-screen">
            <!-- Header -->
            <div class="text-center mb-12 relative">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-blue-500/20 blur-3xl rounded-full z-0 opacity-20"></div>

                <h1 class="relative z-10 text-4xl md:text-6xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-200 via-white to-blue-200 mb-6 drop-shadow-xl font-serif tracking-wide animate-fade-in-down">
                    Thai Massage Hub
                </h1>
                <p class="relative z-10 text-xl text-blue-100 font-light tracking-wider max-w-2xl mx-auto mb-8 animate-fade-in-up">
                    ศูนย์รวมร้านนวดและสปาชั้นนำ คัดสรรสุดยอดประสบการณ์ผ่อนคลายที่คุณวางใจ
                </p>
                
                <!-- Filters -->
                <div class="relative z-10 flex flex-wrap justify-center gap-3 mb-8 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <button 
                        v-for="cat in categories" 
                        :key="cat.id"
                        @click="selectedCategory = cat.id"
                        class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 border backdrop-blur-sm"
                        :class="selectedCategory === cat.id 
                            ? 'bg-white text-blue-900 border-white shadow-[0_0_20px_rgba(255,255,255,0.3)] scale-105' 
                            : 'bg-white/5 text-white/70 border-white/20 hover:bg-white/10 hover:border-white/50 hover:scale-105'"
                    >
                        {{ cat.label }}
                    </button>
                </div>
            </div>

            <!-- Shop Grid -->
            <TransitionGroup 
                tag="div" 
                name="staggered-fade"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-items-center mb-auto"
            >
                <component 
                    :is="shop.status === 'active' ? Link : 'div'"
                    v-for="(shop, index) in filteredShops"
                    :key="shop.id"
                    :href="shop.status === 'active' ? shop.link : null"
                    class="group relative w-full max-w-sm bg-white/10 backdrop-blur-md rounded-3xl overflow-hidden hover:bg-white/95 transition-all duration-500 border border-white/20 hover:border-white shadow-2xl transform hover:-translate-y-3 cursor-pointer"
                    :class="shop.theme.shadow"
                    :style="{ transitionDelay: `${index * 50}ms` }"
                >
                    <!-- Recommended Badge for Main Shop -->
                    <div v-if="shop.isMain" class="absolute top-4 right-4 z-20">
                         <span class="badge badge-primary badge-lg border-none shadow-lg">แนะนำ</span>
                    </div>

                    <!-- Category Badge -->
                    <div class="absolute top-4 left-4 z-20">
                         <span class="badge badge-ghost bg-black/30 text-white border-none backdrop-blur-sm text-xs">
                             {{ categories.find(c => c.id === shop.category)?.label }}
                         </span>
                    </div>

                    <!-- Image -->
                    <div class="h-56 relative overflow-hidden bg-white/90 p-0 flex items-center justify-center group-hover:bg-white transition-colors duration-500">
                        <img 
                            :src="shop.image" 
                            :alt="shop.nameEn" 
                            class="w-full h-full object-cover transition-transform duration-500 transform group-hover:scale-110"
                            :class="[shop.isMain ? 'object-contain p-8' : 'opacity-80 group-hover:opacity-100']"
                        />
                         <!-- Shine Effect -->
                         <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent -translate-x-[200%] group-hover:animate-shine z-30 pointer-events-none"></div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-8 text-center relative overflow-hidden">
                         <div class="absolute inset-0 transition-colors duration-500" :class="shop.theme.overlay"></div>
                        
                        <h2 class="text-2xl font-bold text-white mb-1 transition-colors duration-300" :class="shop.theme.title">
                            {{ shop.name }}
                        </h2>
                        
                        <h3 class="text-base text-blue-200 mb-4 font-serif transition-colors duration-300" :class="shop.theme.subtitle">
                            {{ shop.nameEn }}
                        </h3>
                        
                        <!-- Operating Info -->
                        <div class="mb-5 flex flex-col gap-1 items-center text-sm">
                             <div class="flex items-center gap-2 text-white/80 group-hover:text-slate-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>{{ shop.openDays }} {{ shop.openTime }}</span>
                            </div>
                            <div v-if="shop.closedDay" class="text-red-300 group-hover:text-red-500 text-xs font-medium transition-colors">
                                {{ shop.closedDay }}
                            </div>
                        </div>

                        <p class="text-blue-100/80 group-hover:text-slate-600 text-sm font-light transition-colors duration-300 leading-relaxed mb-6" v-html="shop.desc">
                        </p>
                        
                        <!-- Actions -->
                        <div class="mt-auto">
                            <span v-if="shop.status === 'active'" class="btn btn-sm btn-outline text-white border-white/40 group-hover:btn-primary group-hover:border-none rounded-full px-6 w-full shadow-lg">
                                เลือกใช้บริการ
                            </span>
                            <span v-else class="inline-flex items-center gap-2 text-white/50 text-sm font-medium uppercase tracking-widest transition-colors duration-300" :class="shop.theme.subtitle">
                                Coming Soon
                            </span>
                        </div>
                    </div>
                </component>
            </TransitionGroup>
            
            <!-- Footer Stats -->
             <div class="mt-16 pt-8 border-t border-white/10 w-full animate-fade-in-up" style="animation-delay: 0.6s;">
                <div class="flex flex-wrap justify-center gap-12 md:gap-24 text-center">
                    <div v-for="(stat, index) in stats" :key="index" class="space-y-2">
                         <div class="text-3xl md:text-5xl font-bold text-white mb-1 drop-shadow-lg tabular-nums">{{ stat.value }}</div>
                        <div class="text-blue-200/60 text-sm font-light uppercase tracking-wider">{{ stat.label }}</div>
                    </div>
                </div>
                 <div class="mt-8 text-center text-white/20 text-xs font-light tracking-widest uppercase">
                    &copy; 2026 Nava Group System
                </div>
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

@keyframes slow-zoom {
  0%, 100% { transform: scale(1.05); }
  50% { transform: scale(1.15); }
}
.animate-slow-zoom {
  animation: slow-zoom 20s infinite ease-in-out alternate;
}

@keyframes shine {
  0% { transform: translateX(-200%) skewX(-15deg); }
  100% { transform: translateX(200%) skewX(-15deg); }
}
.animate-shine {
  animation: shine 0.7s;
}

.staggered-fade-enter-active,
.staggered-fade-leave-active {
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.staggered-fade-enter-from,
.staggered-fade-leave-to {
  opacity: 0;
  transform: translateY(30px) scale(0.95);
}
.staggered-fade-leave-active {
  position: absolute; 
}
.staggered-fade-move {
  transition: transform 0.5s;
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
    animation: fadeInUp 0.8s ease-out backwards;
}
</style>
