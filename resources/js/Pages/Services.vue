<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const services = [
    { name: 'กลุ่มอาการออฟฟิศซินโดรม (Office Syndrome) ต่อตำแหน่ง', price: '700' },
    { name: 'ปวดศีรษะ (Tension Headache)', price: '700' },
    { name: 'ปวดคอ บ่า (Text Neck Syndrome)', price: '700' },
    { name: 'ปวดแขน ปวดข้อศอก ปวดข้อมือ (Arm Pain / Elbow Pain / Wrist Pain)', price: '700' },
    { name: 'คอตกหมอน (Neck Sprain)', price: '700' },
    { name: 'ปวดน่อง ตะคริวน่อง (Leg Cramp)', price: '700' },
    { name: 'ปวดข้อเท้า ปวดส้นเท้า รองช้ำ (Ankle Pain / Plantar Fasciitis)', price: '700' },
    { name: 'ข้อเท้าแพลง (Ankle Sprain)', price: '700' },
    { name: 'นวดป้องกันการปวดประจำเดือน (Dysmenorrhea)', price: '700' },
    { name: 'นวดสตรีตั้งครรภ์ (Prenatal Massage)', price: '700' },
    { name: 'ท้องผูก จุกเสียด (Constipation / Heartburn)', price: '700' },
    { name: 'นวดปรับสมดุลร่างกาย (Body Balance Massage)', price: '800' },
    { name: 'ปวดหลัง ปวดเอว (Lower Back Pain / Back Strain)', price: '800' },
    { name: 'ปวดศีรษะ ปวดหัวไมเกรน (Headache/Migraine)', price: '900' },
    { name: 'ปวดสะโพกสลักเพชร (Piriformis Syndrome)', price: '900' },
    { name: 'ปวดขาหนีบ (Groin Pain)', price: '900' },
    { name: 'ปวดขา ปวดเข่า (Leg-Knee Pain)', price: '900' },
    { name: 'นิ้วล็อก/นิ้วไกปืน (Trigger Finger)', price: '900' },
    { name: 'ไหล่ติด (Frozen Shoulder)', price: '900' },
    { name: 'นวดทางการกีฬา (Sport Massage)', price: '1,000' },
    { name: 'นวดบำบัดการนอนไม่หลับ (Nuad Thai for Sleep Disorder)', price: '1,000' },
    { name: 'ปัสสาวะขัด กลั้นปัสสาวะไม่ได้ (Urinary Incontinence)', price: '1,000' },
    { name: 'นวดรักษาภาวะมีบุตรยาก (Fertility and Infertility Massage)', price: '1,200' },
    { name: 'Erectile Dysfunction with Thai Kasai Massage', price: '1,500' },
];

const searchQuery = ref('');

const filteredServices = computed(() => {
    if (!searchQuery.value) return services;
    const query = searchQuery.value.toLowerCase();
    return services.filter(service => 
        service.name.toLowerCase().includes(query) || 
        service.price.toString().includes(query)
    );
});

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

onMounted(() => {
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 50,
    });
});
</script>

<template>
    <Head title="อัตราค่ารักษา" />
    <div class="min-h-screen bg-slate-50 font-sans text-slate-800 selection:bg-blue-200 selection:text-blue-900">
        <!-- Navbar -->
        <nav class="navbar fixed top-0 w-full z-50 transition-all duration-300 bg-white/95 backdrop-blur-md shadow-sm h-20 px-0 py-0" data-aos="fade-down" data-aos-duration="600">
            <div class="container mx-auto px-4 h-full flex items-center justify-between">
                <div class="navbar-start">
                    <div class="dropdown">
                        <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                            <li><Link href="/nava-clinic">หน้าแรก</Link></li>
                            <li><Link href="/services" class="active">บริการและราคา</Link></li>
                            <li><Link :href="route('booking.create')">จองคิว</Link></li>
                        </ul>
                    </div>
                    <Link href="/" class="btn btn-ghost btn-circle text-slate-500 hover:text-blue-600 hover:bg-blue-50 mr-2 group" title="กลับไปหน้ารวมร้าน">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 transition-transform group-hover:-translate-x-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </Link>
                    <Link href="/nava-clinic" class="btn btn-ghost text-2xl text-blue-700 font-bold flex gap-2 items-center hover:bg-transparent transition-transform hover:scale-105 active:scale-95 duration-200">
                        <img src="/images/logo.png" alt="Nava Clinic Logo" class="h-10 w-auto" />
                        <span class="font-serif tracking-wide">NAVA CLINIC</span>
                    </Link>
                </div>
                <!-- Center: Navigation Links -->
                <div class="navbar-center hidden lg:flex">
                     <ul class="menu menu-horizontal px-1 font-medium text-lg text-slate-600 gap-4">
                        <li><Link href="/nava-clinic" class="hover:text-blue-600 active:text-blue-700 active:bg-transparent transition-colors duration-200 relative after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-blue-600 after:transition-all hover:after:w-full">หน้าแรก</Link></li>
                        <li><Link href="/services" class="hover:text-blue-600 active:text-blue-700 active:bg-transparent transition-colors duration-200 relative after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-blue-600 after:transition-all">บริการและราคา</Link></li>
                        <li><Link :href="route('booking.create')" class="hover:text-blue-600 active:text-blue-700 active:bg-transparent transition-colors duration-200 relative after:content-[''] after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-blue-600 after:transition-all hover:after:w-full">จองคิว</Link></li>
                    </ul>
                </div>

                <!-- Right: Action Buttons -->
                <div class="navbar-end hidden lg:flex gap-3">
                     <template v-if="$page.props.auth.user">
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost gap-2 font-medium text-slate-600 hover:text-blue-600 transition-transform active:scale-95">
                                {{ $page.props.auth.user.name }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </div>
                            <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                                <li v-if="$page.props.auth.user.is_admin">
                                    <Link :href="route('admin.dashboard')">แดชบอร์ดแอดมิน</Link>
                                </li>
                                <li v-if="!$page.props.auth.user.is_admin"><Link :href="route('dashboard')">ประวัติการจองคิว</Link></li>
                                <li><Link :href="route('logout')" method="post" as="button" class="text-red-600">ออกจากระบบ</Link></li>
                            </ul>
                        </div>
                     </template>
                </div>
            </div>
        </nav>

        <!-- content -->
        <div class="pt-20"> <!-- Padding top for fixed navbar -->
            <div class="relative bg-slate-50 overflow-hidden pb-12">
                 <!-- Background Elements -->
                 <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-blue-100 rounded-full blur-3xl opacity-40"></div>
                 <div class="absolute top-40 left-0 -ml-20 w-60 h-60 bg-purple-100 rounded-full blur-3xl opacity-40"></div>

                <div class="container mx-auto py-12 px-4 max-w-5xl relative z-10">
                    <!-- Hero Header -->
                    <div class="text-center mb-12" data-aos="fade-up">
                        <div class="inline-block p-1 px-4 mb-4 rounded-full bg-blue-50 text-blue-600 font-bold text-sm tracking-wide uppercase shadow-sm border border-blue-100">
                            Service & Rates
                        </div>
                        <h1 class="text-5xl md:text-6xl font-extrabold mb-4 text-slate-800 leading-tight">
                            อัตราค่ารักษา <br class="hidden md:inline"/> <span class="text-blue-500 text-4xl md:text-5xl block md:inline mt-2 md:mt-0 font-serif">Price Rates</span>
                        </h1>
                        <p class="text-xl text-slate-500 max-w-2xl mx-auto font-light">
                            นวคลินิกการแพทย์แผนไทย สาขานวดไทย ให้บริการด้วยหัวใจและมาตรฐานวิชาชีพ
                        </p>
                        
                        <!-- Search Box -->
                        <div class="mt-10 max-w-md mx-auto relative group">
                            <input 
                                v-model="searchQuery"
                                type="text" 
                                placeholder="ค้นหาอาการ หรือ บริการ..." 
                                class="input input-bordered input-lg w-full rounded-full pl-12 shadow-md focus:shadow-xl focus:border-blue-500 transition-all border-slate-200 bg-white/90 backdrop-blur text-slate-700 placeholder-slate-400"
                            />
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Table Card -->
                    <div class="overflow-hidden bg-white shadow-xl shadow-slate-200/50 rounded-3xl border border-slate-100" data-aos="fade-up" data-aos-delay="200">
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <!-- head -->
                                <thead class="bg-gradient-to-r from-slate-50 to-white text-slate-600 text-lg uppercase tracking-wide border-b border-slate-100">
                                    <tr>
                                        <th class="w-2/3 py-6 pl-10 font-bold text-slate-700">การรักษา (Treatment)</th>
                                        <th class="w-1/3 py-6 text-right pr-10 font-bold text-slate-700">ราคาเริ่มต้น (Price)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <tr v-if="filteredServices.length === 0">
                                        <td colspan="2" class="py-12 text-center text-slate-400 text-lg italic bg-slate-50/50">
                                            ไม่พบข้อมูลที่ค้นหา "{{ searchQuery }}"
                                        </td>
                                    </tr>
                                    <tr 
                                        v-for="(service, index) in filteredServices" 
                                        :key="index" 
                                        class="hover:bg-blue-50/50 transition-colors duration-200 group cursor-default"
                                    >
                                        <td class="py-5 pl-10 text-lg text-slate-600 font-medium group-hover:text-blue-700 transition-colors flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-slate-300 group-hover:bg-blue-500 transition-colors"></span>
                                            {{ service.name }}
                                        </td>
                                        <td class="py-5 text-right pr-10">
                                            <span class="inline-block font-bold text-xl text-slate-700 bg-slate-100 px-3 py-1 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                                {{ service.price }}
                                            </span>
                                            <span class="text-sm text-slate-400 ml-1 font-normal group-hover:text-blue-500/70">THB</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes Grid -->
                    <div class="max-w-3xl mx-auto mt-12" data-aos="fade-up" data-aos-delay="400">
                        <!-- Note Card -->
                        <div class="p-8 bg-gradient-to-br from-amber-50 to-orange-50 rounded-3xl border border-amber-100 shadow-lg hover:shadow-xl transition-shadow relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-2xl text-amber-800 mb-4 flex items-center gap-2 font-serif">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                หมายเหตุ (Note)
                            </h3>
                            <ul class="space-y-3 text-amber-900/80 font-medium relative z-10 text-lg">
                                <li class="flex items-start gap-3">
                                    <span class="mt-2 w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                                    กรณีรักษามากกว่าหนึ่งอาการ ค่ารักษาเริ่มต้นที่ 1,000 บาท/ครั้ง
                                </li>
                                <li class="flex items-start gap-3">
                                     <span class="mt-2 w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                                    นักศึกษา เพียงแสดงบัตรนักศึกษา รับส่วนลดทันที 100 บาท
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-16">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                    <div class="md:col-span-1" data-aos="fade-up" data-aos-delay="0">
                        <div class="flex items-center gap-3 text-white text-xl font-bold mb-6">
                             <img src="/images/logo.png" alt="Logo" class="w-12 h-12 rounded-full bg-white p-1" />
                             NAVA CLINIC
                        </div>
                        <p class="mb-6 font-light">
                            คลินิกการแพทย์แผนไทยและนวดเพื่อสุขภาพ โดยทีมงานมืออาชีพ ใส่ใจทุกรายละเอียดของการรักษา
                        </p>
                    </div>
                    <div data-aos="fade-up" data-aos-delay="100">
                        <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">บริการ</h4>
                        <ul class="space-y-3 font-light">
                            <li><Link href="/services" class="hover:text-blue-400 transition-colors">นวดแผนไทย</Link></li>
                            <li><Link href="/services" class="hover:text-blue-400 transition-colors">รักษากล้ามเนื้อ</Link></li>
                            <li><Link href="/services" class="hover:text-blue-400 transition-colors">นวดแก้อาการ</Link></li>
                             <li><Link href="/services" class="hover:text-blue-400 transition-colors">ออฟฟิศซินโดรม</Link></li>
                        </ul>
                    </div>
                    <div data-aos="fade-up" data-aos-delay="200">
                        <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">เวลาทำการ</h4>
                        <ul class="space-y-3 font-light">
                            <li class="flex justify-between"><span>อังคาร - ศุกร์</span> <span>09:00 - 20:00</span></li>
                            <li class="flex justify-between"><span>เสาร์ - อาทิตย์</span> <span>09:00 - 21:00</span></li>
                             <li class="text-blue-500 text-sm pt-2">*รับคิวสุดท้าย 19:00</li>
                        </ul>
                    </div>
                    <div data-aos="fade-up" data-aos-delay="300">
                        <h4 class="text-white font-bold mb-6 uppercase tracking-wider text-sm">ติดต่อเรา</h4>
                        <ul class="space-y-3 font-light">
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mt-1 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                541/13 การเคหะหนองหอย อำเภอเมืองเชียงใหม่ เชียงใหม่ 50000
                            </li>
                             <li class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                062-278-1007
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm font-light text-gray-600">
                    &copy; 2026 Nava Clinic. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
