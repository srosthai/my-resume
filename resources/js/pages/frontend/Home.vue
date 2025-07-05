<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    title: {
        type: String,
        default: 'Portfolio'
    },
    description: {
        type: String,
        default: 'Welcome to my portfolio'
    }
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

    <div class="portfolio-container">
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
                    <li><Link href="/" class="nav-link active">Home</Link></li>
                    <li><Link href="/about" class="nav-link">About Me</Link></li>
                    <li><Link href="/portfolio" class="nav-link">Portfolio</Link></li>
                    <li><Link href="/contact" class="nav-link">Contact</Link></li>
                    <li><Link href="/more" class="nav-link">More</Link></li>
                </ul>
                
                <!-- Search Input -->
                <div class="nav-search" hidden>
                    <input type="text" placeholder="Search..." class="search-input">
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="hero-section">
            <div class="hero-content">
                <div class="hero-text" :class="{ 'fade-in-up': isVisible }">
                    <h1 class="hero-title">{{ users.name }}</h1>
                    <h2 class="hero-subtitle">{{ users.position }}</h2>
                    <p class="hero-description">
                        {{ users.description }}
                    </p>
                    <div class="hero-buttons">
                        <Link href="/about" class="btn-primary">Learn more</Link>
                        <Link href="/portfolio" class="btn-secondary">View Portfolio</Link>
                    </div>
                </div>
                <div class="hero-image" :class="{ 'fade-in-up': isVisible }">
                    <div class="avatar-container">
                        <div class="avatar floating">
                            <img 
                                v-if="users.image" 
                                :src="users.image.startsWith('http') ? users.image : `/${users.image}`" 
                                :alt="users.name" 
                                class="avatar-image"
                            />
                            <div v-else class="avatar-placeholder">
                                <span class="avatar-initials">{{ users.name?.charAt(0) }}</span>
                            </div>
                        </div>
                    </div>
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

.portfolio-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
    color: #ffffff;
    font-family: 'Arial', 'Helvetica', sans-serif;
    overflow-x: hidden;
}

/* Navigation */
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


/* Mobile Menu Toggle */
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

/* Hero Section */
.hero-section {
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 0 5%;
    max-width: 1200px;
    margin: 0 auto;
}

.hero-content {
    display: grid;
    grid-template-columns: 60% 40%;
    gap: 60px;
    align-items: center;
    width: 100%;
}

.hero-title {
    font-size: 5rem;
    font-weight: 900;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1.5rem;
    font-weight: 500;
    color: #c0c0c0;
    margin-bottom: 30px;
    letter-spacing: 1px;
}

.hero-description {
    font-size: 1.2rem;
    color: #a0a0a0;
    line-height: 1.6;
    margin-bottom: 40px;
}

.hero-buttons {
    display: flex;
    gap: 20px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    border: none;
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 15px 30px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-secondary:hover {
    transform: translateY(-3px);
    background: rgba(255, 255, 255, 0.2);
}

.avatar-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 400px;
}

.avatar {
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-image {
    width: 280px;
    height: 280px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.1);
}

.avatar-placeholder {
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: #1a1a2e;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid rgba(255, 255, 255, 0.1);
}

.avatar-initials {
    font-size: 4rem;
    font-weight: 600;
    color: #667eea;
}

.floating {
    animation: float 6s ease-in-out infinite;
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

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
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
    
    
    .hero-title {
        font-size: 4rem;
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
    
    
    .hero-section {
        padding: 100px 4% 0;
    }
    
    .hero-content {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .hero-title {
        font-size: 3rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .hero-description {
        font-size: 1rem;
    }
    
    .hero-buttons {
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .btn-primary,
    .btn-secondary {
        width: 200px;
        text-align: center;
    }
    
    .avatar {
        width: 250px;
        height: 250px;
    }
    
    .avatar-image,
    .avatar-placeholder {
        width: 230px;
        height: 230px;
    }
    
    .avatar-initials {
        font-size: 3rem;
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
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
        letter-spacing: 0.5px;
    }
    
    .hero-description {
        font-size: 0.9rem;
    }
    
}
</style>