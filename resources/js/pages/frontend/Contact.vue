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

const socialLinks = [
    {
        name: 'Facebook',
        icon: '📘',
        url: 'https://facebook.com/antonf',
        username: '@antonf.dev',
        color: '#1877F2'
    },
    {
        name: 'LinkedIn',
        icon: '💼',
        url: 'https://linkedin.com/in/antonf',
        username: 'Anton F.',
        color: '#0A66C2'
    },
    {
        name: 'Instagram',
        icon: '📸',
        url: 'https://instagram.com/antonf.dev',
        username: '@antonf.dev',
        color: '#E4405F'
    },
    {
        name: 'Telegram',
        icon: '✈️',
        url: 'https://t.me/antonf',
        username: '@antonf',
        color: '#0088CC'
    },
    {
        name: 'GitHub',
        icon: '💻',
        url: 'https://github.com/antonf',
        username: 'antonf',
        color: '#333333'
    },
    {
        name: 'Email',
        icon: '📧',
        url: 'mailto:anton@example.com',
        username: 'anton@example.com',
        color: '#EA4335'
    }
]

const websites = [
    {
        name: 'Personal Portfolio',
        url: 'https://antonf.dev',
        description: 'My main portfolio website showcasing all projects and skills',
        type: 'portfolio'
    },
    {
        name: 'Tech Blog',
        url: 'https://blog.antonf.dev',
        description: 'Technical articles and tutorials about web development',
        type: 'blog'
    },
    {
        name: 'Code Snippets',
        url: 'https://snippets.antonf.dev',
        description: 'Useful code snippets and development resources',
        type: 'resources'
    }
]

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

    <div class="contact-container">
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
                    <li><Link href="/contact" class="nav-link active">Contact</Link></li>
                    <li><Link href="/more" class="nav-link">More</Link></li>
                </ul>
                
                <!-- Search Input -->
                <div class="nav-search" hidden>
                    <input type="text" placeholder="Search..." class="search-input">
                </div>
            </div>
        </nav>

        <!-- Contact Hero Section -->
        <section class="contact-hero">
            <div class="hero-content">
                <h1 class="page-title" :class="{ 'fade-in-up': isVisible }">Get In Touch</h1>
                <p class="page-description" :class="{ 'fade-in-up': isVisible }">
                    Let's connect and discuss opportunities, collaborations, or just have a friendly chat about technology.
                </p>
            </div>
        </section>

        <!-- Social Media Section -->
        <section class="social-section">
            <div class="section-container">
                <h2 class="section-title" :class="{ 'fade-in-up': isVisible }">Follow Me</h2>
                <div class="social-grid">
                    <a
                        v-for="(social, index) in socialLinks"
                        :key="index"
                        :href="social.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="social-card"
                        :class="{ 'fade-in-up': isVisible }"
                        :style="{ 
                            animationDelay: `${index * 0.1}s`,
                            '--hover-color': social.color 
                        }"
                    >
                        <div class="social-icon">{{ social.icon }}</div>
                        <div class="social-info">
                            <h3 class="social-name">{{ social.name }}</h3>
                            <p class="social-username">{{ social.username }}</p>
                        </div>
                        <div class="social-arrow">→</div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Websites Section -->
        <section class="websites-section">
            <div class="section-container">
                <h2 class="section-title" :class="{ 'fade-in-up': isVisible }">My Websites</h2>
                <div class="websites-grid">
                    <a
                        v-for="(website, index) in websites"
                        :key="index"
                        :href="website.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="website-card"
                        :class="{ 'fade-in-up': isVisible }"
                        :style="{ animationDelay: `${index * 0.2}s` }"
                    >
                        <div class="website-icon">
                            <span v-if="website.type === 'portfolio'">🌐</span>
                            <span v-else-if="website.type === 'blog'">📝</span>
                            <span v-else="website.type === 'resources'">🔧</span>
                        </div>
                        <div class="website-content">
                            <h3 class="website-name">{{ website.name }}</h3>
                            <p class="website-description">{{ website.description }}</p>
                            <span class="website-url">{{ website.url }}</span>
                        </div>
                        <div class="website-arrow">↗</div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="contact-form-section">
            <div class="section-container">
                <h2 class="section-title" :class="{ 'fade-in-up': isVisible }">Send a Message</h2>
                <div class="contact-form-container" :class="{ 'fade-in-up': isVisible }">
                    <form class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <span>Send Message</span>
                            <span class="btn-icon">📤</span>
                        </button>
                    </form>
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

.contact-container {
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

/* Contact Hero Section */
.contact-hero {
    padding: 120px 5% 80px;
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
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
    max-width: 600px;
    margin: 0 auto;
}

/* Sections */
.section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 5%;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 600;
    margin-bottom: 60px;
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Social Media Grid */
.social-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.social-card {
    display: flex;
    align-items: center;
    gap: 20px;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 25px;
    text-decoration: none;
    color: #ffffff;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.social-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.1),
        transparent
    );
    transition: all 0.6s ease;
}

.social-card:hover::before {
    left: 100%;
}

.social-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
    border-color: var(--hover-color, rgba(255, 255, 255, 0.3));
}

.social-icon {
    font-size: 2.5rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    flex-shrink: 0;
}

.social-info {
    flex: 1;
}

.social-name {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: #ffffff;
}

.social-username {
    color: #a0a0a0;
    font-size: 0.95rem;
}

.social-arrow {
    font-size: 1.5rem;
    color: #a0a0a0;
    transition: all 0.3s ease;
}

.social-card:hover .social-arrow {
    color: #ffffff;
    transform: translateX(5px);
}

/* Websites Grid */
.websites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
}

.website-card {
    display: flex;
    gap: 20px;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 30px;
    text-decoration: none;
    color: #ffffff;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.website-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(102, 126, 234, 0.3);
}

.website-icon {
    font-size: 2.5rem;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    flex-shrink: 0;
}

.website-content {
    flex: 1;
}

.website-name {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: #ffffff;
}

.website-description {
    color: #a0a0a0;
    line-height: 1.5;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.website-url {
    color: #667eea;
    font-size: 0.9rem;
    font-weight: 500;
}

.website-arrow {
    font-size: 1.5rem;
    color: #a0a0a0;
    transition: all 0.3s ease;
    align-self: flex-start;
}

.website-card:hover .website-arrow {
    color: #ffffff;
    transform: translate(3px, -3px);
}

/* Contact Form */
.contact-form-container {
    max-width: 600px;
    margin: 0 auto;
}

.contact-form {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 40px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #ffffff;
    font-weight: 500;
    font-size: 0.95rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    padding: 15px;
    color: #ffffff;
    font-size: 1rem;
    transition: all 0.3s ease;
    font-family: inherit;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.15);
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #a0a0a0;
}

.submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    border: none;
    padding: 18px 30px;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
}

.btn-icon {
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.submit-btn:hover .btn-icon {
    transform: translateX(3px);
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
    
    .section-container {
        padding: 60px 4%;
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
    
    .contact-hero {
        padding: 100px 4% 60px;
    }
    
    .page-title {
        font-size: 2.5rem;
    }
    
    .page-description {
        font-size: 1.1rem;
    }
    
    .section-container {
        padding: 50px 4%;
    }
    
    .section-title {
        font-size: 2rem;
        margin-bottom: 40px;
    }
    
    .social-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .websites-grid {
        grid-template-columns: 1fr;
        gap: 25px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .contact-form {
        padding: 30px;
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
    
    .section-container {
        padding: 40px 3%;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .social-card {
        padding: 20px;
        gap: 15px;
    }
    
    .social-icon {
        width: 50px;
        height: 50px;
        font-size: 2rem;
    }
    
    .social-name {
        font-size: 1.1rem;
    }
    
    .website-card {
        padding: 25px;
        gap: 15px;
    }
    
    .website-icon {
        width: 50px;
        height: 50px;
        font-size: 2rem;
    }
    
    .contact-form {
        padding: 25px;
    }
    
    .form-group input,
    .form-group textarea {
        padding: 12px;
    }
    
    .submit-btn {
        padding: 15px 25px;
        font-size: 1rem;
    }
}
</style>