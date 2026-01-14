<script setup>
import { Head, Link } from '@inertiajs/vue3';

const services = [
    {
        title: 'Traditional Thai Massage',
        description: 'Ancient healing system combining acupressure, Indian Ayurvedic principles, and assisted yoga postures.',
        price: '$50 / 60 min',
        image: 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        title: 'Aromatherapy Oil Massage',
        description: 'Gentle massage using essential oils to promote relaxation and well-being.',
        price: '$70 / 60 min',
        image: 'https://images.unsplash.com/photo-1519823551278-64ac92734fb1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        title: 'Deep Tissue Massage',
        description: 'Focuses on realigning deeper layers of muscles and connective tissue. It is helpful for chronic aches and pains.',
        price: '$80 / 60 min',
        image: 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        title: 'Foot Reflexology',
        description: 'Massage involving applying pressure to the feet and hands with specific thumb, finger, and hand techniques.',
        price: '$45 / 60 min',
        image: 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    }
];
</script>

<template>
    <Head title="Services" />
    <div class="min-h-screen bg-base-200 font-sans">
        <!-- Navbar -->
        <div class="navbar bg-base-100 shadow-sm">
            <div class="navbar-start">
                  <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /></svg>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                         <li><Link href="/">Home</Link></li>
                        <li><Link href="/services">Services</Link></li>
                    </ul>
                </div>
                <Link href="/" class="btn btn-ghost text-xl text-primary">Nava Clinic</Link>
            </div>
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1">
                    <li><Link href="/">Home</Link></li>
                    <li><Link href="/services">Services</Link></li>
                </ul>
            </div>
            <div class="navbar-end">
                <a class="btn btn-primary">Book Now</a>
            </div>
        </div>

        <!-- Header -->
        <div class="hero h-64 bg-base-100">
            <div class="hero-content text-center">
                <div class="max-w-md">
                    <h1 class="text-5xl font-bold text-primary">Our Services</h1>
                    <p class="py-6">Choose from our wide range of therapeutic massages designed to heal and revitalize.</p>
                </div>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                <div v-for="service in services" :key="service.title" class="card lg:card-side bg-base-100 shadow-xl">
                    <figure class="lg:w-1/2"><img :src="service.image" :alt="service.title" class="h-full w-full object-cover" /></figure>
                    <div class="card-body lg:w-1/2">
                        <h2 class="card-title text-2xl text-primary">{{ service.title }}</h2>
                        <p>{{ service.description }}</p>
                        <p class="text-xl font-bold mt-2 text-secondary">{{ service.price }}</p>
                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-primary">Book This Service</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer p-10 bg-neutral text-neutral-content mt-10">
            <aside>
                <p class="font-bold text-xl">Nava Clinic</p> 
                <p>Providing quality healthcare since 2026</p>
            </aside> 
            <nav>
                <h6 class="footer-title">Services</h6> 
                <a class="link link-hover">Thai Massage</a>
                <a class="link link-hover">Oil Massage</a>
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
