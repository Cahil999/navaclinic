<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-base-100 font-sans text-base-content">
        <!-- Navbar -->
        <div class="navbar bg-base-100 shadow-sm sticky top-0 z-50">
            <div class="navbar-start">
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><Link href="/">Home</Link></li>
                        <li><Link href="/services">Services</Link></li>
                        <li v-if="canLogin">
                            <Link v-if="$page.props.auth.user" :href="route('dashboard')">Dashboard</Link>
                            <template v-else>
                                <Link :href="route('login')">Log in</Link>
                                <Link v-if="canRegister" :href="route('register')">Register</Link>
                            </template>
                        </li>
                    </ul>
                </div>
                <Link href="/" class="btn btn-ghost text-xl text-primary font-bold">Nava Clinic</Link>
            </div>
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1 font-medium">
                    <li><Link href="/">Home</Link></li>
                    <li><Link href="/services">Services</Link></li>
                </ul>
            </div>
            <div class="navbar-end hidden lg:flex">
                <nav v-if="canLogin" class="flex gap-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="btn btn-primary"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="btn btn-ghost"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="btn btn-primary"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="hero min-h-[70vh]" style="background-image: url(https://images.unsplash.com/photo-1540555700478-4be289fbecef?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80);">
            <div class="hero-overlay bg-opacity-60"></div>
            <div class="hero-content text-center text-neutral-content">
                <div class="max-w-2xl">
                    <h1 class="mb-5 text-5xl font-bold">Experience True Relaxation</h1>
                    <p class="mb-5 text-lg">
                        Welcome to Nava Clinic, your sanctuary for holistic healing and rejuvenation. 
                        Discover our range of therapeutic massages tailored to restore your balance.
                    </p>
                    <Link href="/services" class="btn btn-primary btn-lg border-none hover:bg-teal-700">Explore Our Services</Link>
                </div>
            </div>
        </div>

        <!-- Features/Intro Section -->
        <div class="py-16 bg-base-200">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-primary">Why Choose Nava Clinic?</h2>
                    <p class="py-4 text-base-content/70">We are dedicated to providing the best therapeutic experience.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                                </svg>
                            </div>
                            <h2 class="card-title">Expert Therapists</h2>
                            <p>Our certified therapists are skilled in various techniques to ensure your comfort and healing.</p>
                        </div>
                    </div>
                    <!-- Feature 2 -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body items-center text-center">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <h2 class="card-title">Flexible Scheduling</h2>
                            <p>Book online easily at a time that suits you. We are open 7 days a week.</p>
                        </div>
                    </div>
                    <!-- Feature 3 -->
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body items-center text-center">
                             <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                </svg>
                            </div>
                            <h2 class="card-title">Tranquil Atmosphere</h2>
                            <p>Relax in our calming environment designed to melt away your stress.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer p-10 bg-neutral text-neutral-content">
            <aside>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mb-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                </svg>
                <p class="font-bold text-xl">Nava Clinic</p> 
                <p>Providing quality healthcare since 2026</p>
            </aside> 
            <nav>
                <h6 class="footer-title">Services</h6> 
                <Link href="/services" class="link link-hover">Thai Massage</Link>
                <Link href="/services" class="link link-hover">Oil Massage</Link>
            </nav> 
            <nav>
                <h6 class="footer-title">Company</h6> 
                <a class="link link-hover">About us</a>
                <a class="link link-hover">Contact</a> 
            </nav> 
            <nav>
                <h6 class="footer-title">Legal</h6> 
                <a class="link link-hover">Terms of use</a>
                <a class="link link-hover">Privacy policy</a>
            </nav>
        </footer>
    </div>
</template>
