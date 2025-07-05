<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    title: String,
    description: String
})

const isVisible = ref(false)
const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}

onMounted(() => {
    setTimeout(() => {
        isVisible.value = true
    }, 100)
})
</script>

<template>
    <Head>
        <title>{{ title }}</title>
        <meta name="description" :content="description" />
    </Head>

    <div class="more-container">
        <!-- Navigation -->
        <nav class="portfolio-nav">
            <div class="nav-content">
                <div class="nav-logo" hidden>Anton F.</div>
                
                <!-- Mobile Menu Toggle -->
                <button 
                    class="mobile-menu-toggle"
                    @click="toggleMobileMenu"
                    :class="{ 'active': isMobileMenuOpen }"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <!-- Navigation Links -->
                <ul class="nav-links" :class="{ 'mobile-open': isMobileMenuOpen }">
                    <li><Link href="/" class="nav-link">Home</Link></li>
                    <li><Link href="/about" class="nav-link">About Me</Link></li>
                    <li><Link href="/portfolio" class="nav-link">Portfolio</Link></li>
                    <li><Link href="/contact" class="nav-link">Contact</Link></li>
                    <li><Link href="/more" class="nav-link active">More</Link></li>
                </ul>
                
                <!-- Search Input -->
                <div class="nav-search" hidden>
                    <input type="text" placeholder="Search..." class="search-input">
                </div>
            </div>
        </nav>

        <!-- More Content Section -->
        <section class="more-hero">
            <div class="hero-content">
                <h1 class="page-title" :class="{ 'fade-in-up': isVisible }">More</h1>
                <p class="page-description" :class="{ 'fade-in-up': isVisible }">
                    This section is coming soon. Stay tuned for more exciting content and features!
                </p>
                
                <!-- Coming Soon Card -->
                <div class="coming-soon-card" :class="{ 'fade-in-up': isVisible }">
                    <div class="card-icon">🚧</div>
                    <h2 class="card-title">Under Construction</h2>
                    <p class="card-description">
                        We're working hard to bring you amazing new features. Check back soon!
                    </p>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
/* Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.more-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
    color: #ffffff;
    font-family: 'Arial', 'Helvetica', sans-serif;
    overflow-x: hidden;
}

/* Navigation - Same as other pages */
.portfolio-nav {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 15px 30px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    width: auto;
    max-width: calc(100vw - 40px);
}

.nav-content {
    display: flex;
    align-items: center;
    gap: 30px;
    position: relative;
}

.nav-logo {
    font-size: 1.2rem;
    font-weight: 600;
    color: #ffffff;
    white-space: nowrap;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 20px;
}

.nav-link {
    text-decoration: none;
    color: #a0a0a0;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    white-space: nowrap;
}

.nav-link:hover,
.nav-link.active {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.nav-search {
    display: flex;
    align-items: center;
}

.search-input {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    padding: 8px 16px;
    color: #ffffff;
    width: 150px;
    transition: all 0.3s ease;
}

.search-input::placeholder {
    color: #a0a0a0;
}

.search-input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.15);
    width: 200px;
}

.mobile-menu-toggle {
    display: none;
    flex-direction: column;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    gap: 3px;
}

.mobile-menu-toggle span {
    width: 20px;
    height: 2px;
    background-color: #ffffff;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

/* More Hero Section */
.more-hero {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 120px 5% 80px;
    max-width: 1200px;
    margin: 0 auto;
}

.hero-content {
    text-align: center;
    max-width: 600px;
}

.page-title {
    font-size: 4rem;
    font-weight: 900;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.1;
}

.page-description {
    font-size: 1.3rem;
    color: #a0a0a0;
    line-height: 1.6;
    margin-bottom: 50px;
}

/* Coming Soon Card */
.coming-soon-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 25px;
    padding: 50px 40px;
    text-align: center;
    transition: all 0.3s ease;
}

.coming-soon-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2);
}

.card-icon {
    font-size: 4rem;
    margin-bottom: 25px;
    display: block;
}

.card-title {
    font-size: 2rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card-description {
    font-size: 1.1rem;
    color: #a0a0a0;
    line-height: 1.6;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 1s ease-out forwards;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .portfolio-nav {
        padding: 12px 25px;
    }
    
    .nav-content {
        gap: 20px;
    }
    
    .search-input {
        width: 120px;
    }
    
    .search-input:focus {
        width: 150px;
    }
    
    .page-title {
        font-size: 3rem;
    }
    
    .more-hero {
        padding: 100px 4% 60px;
    }
}

@media (max-width: 768px) {
    .portfolio-nav {
        top: 15px;
        padding: 10px 20px;
        border-radius: 25px;
        width: calc(100vw - 30px);
    }
    
    .nav-content {
        gap: 15px;
        justify-content: space-between;
    }
    
    .mobile-menu-toggle {
        display: flex;
    }
    
    .nav-links {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        margin-top: 10px;
        padding: 20px;
        flex-direction: column;
        gap: 10px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
    
    .nav-links.mobile-open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .nav-search {
        display: none;
    }
    
    .page-title {
        font-size: 2.5rem;
    }
    
    .page-description {
        font-size: 1.1rem;
        margin-bottom: 40px;
    }
    
    .coming-soon-card {
        padding: 40px 30px;
    }
    
    .card-title {
        font-size: 1.8rem;
    }
    
    .card-description {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .portfolio-nav {
        top: 10px;
        padding: 8px 15px;
        width: calc(100vw - 20px);
    }
    
    .nav-logo {
        font-size: 1rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .page-description {
        font-size: 1rem;
    }
    
    .coming-soon-card {
        padding: 30px 20px;
    }
    
    .card-icon {
        font-size: 3rem;
    }
    
    .card-title {
        font-size: 1.5rem;
    }
    
    .card-description {
        font-size: 0.95rem;
    }
}
</style>