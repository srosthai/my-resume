<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Laptop, Eye, Github } from 'lucide-vue-next'

const props = defineProps({
    title: String,
    description: String,
    projects: Array
})

const isVisible = ref(false)
const isMobileMenuOpen = ref(false)
const selectedFilter = ref('all')

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const projectsData = [
    {
        id: 1,
        title: 'Online Cinema Platform',
        description: 'A comprehensive streaming platform featuring user authentication, personalized recommendations, watchlists, and responsive design for seamless viewing across all devices.',
        image: '/api/placeholder/600/400',
        technologies: ['React', 'Node.js', 'MongoDB', 'Express.js', 'Socket.io'],
        category: 'fullstack',
        date: 'September 2023',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 2,
        title: 'E-Commerce Dashboard',
        description: 'Modern admin dashboard for e-commerce management with real-time analytics, inventory tracking, and order management system.',
        image: '/api/placeholder/600/400',
        technologies: ['Vue.js', 'TypeScript', 'PostgreSQL', 'Express.js', 'Chart.js'],
        category: 'frontend',
        date: 'June 2023',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 3,
        title: 'Task Management API',
        description: 'RESTful API for task management with authentication, role-based permissions, file uploads, and real-time notifications.',
        image: '/api/placeholder/600/400',
        technologies: ['Go', 'PostgreSQL', 'Redis', 'Docker', 'JWT'],
        category: 'backend',
        date: 'March 2023',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 4,
        title: 'Social Media Analytics',
        description: 'Data visualization platform for social media metrics with interactive charts, real-time updates, and export functionality.',
        image: '/api/placeholder/600/400',
        technologies: ['Next.js', 'D3.js', 'Python', 'FastAPI', 'PostgreSQL'],
        category: 'fullstack',
        date: 'December 2022',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 5,
        title: 'Mobile Banking App',
        description: 'Cross-platform mobile application for banking services with biometric authentication and real-time transaction tracking.',
        image: '/api/placeholder/600/400',
        technologies: ['React Native', 'TypeScript', 'Node.js', 'MongoDB'],
        category: 'mobile',
        date: 'October 2022',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    },
    {
        id: 6,
        title: 'AI Content Generator',
        description: 'AI-powered platform for generating marketing content with natural language processing and customizable templates.',
        image: '/api/placeholder/600/400',
        technologies: ['Python', 'Flask', 'OpenAI API', 'React', 'PostgreSQL'],
        category: 'ai',
        date: 'August 2022',
        status: 'completed',
        links: {
            demo: '#',
            github: '#'
        }
    }
]

const filteredProjects = ref(projectsData)

const filterProjects = (category) => {
    selectedFilter.value = category
    if (category === 'all') {
        filteredProjects.value = projectsData
    } else {
        filteredProjects.value = projectsData.filter(project => project.category === category)
    }
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
                    <li><Link href="/" class="nav-link">Home</Link></li>
                    <li><Link href="/about" class="nav-link">About Me</Link></li>
                    <li><Link href="/portfolio" class="nav-link active">Portfolio</Link></li>
                    <li><Link href="/contact" class="nav-link">Contact</Link></li>
                    <li><Link href="/more" class="nav-link">More</Link></li>
                </ul>
                
                <!-- Search Input -->
                <div class="nav-search" hidden>
                    <input type="text" placeholder="Search..." class="search-input">
                </div>
            </div>
        </nav>

        <!-- Portfolio Hero Section -->
        <section class="portfolio-hero">
            <div class="hero-content">
                <h1 class="page-title" :class="{ 'fade-in-up': isVisible }">My Portfolio</h1>
                <p class="page-description" :class="{ 'fade-in-up': isVisible }">
                    Explore my collection of projects showcasing modern web development, mobile applications, and innovative solutions.
                </p>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="filter-section">
            <div class="section-container">
                <div class="filter-buttons" :class="{ 'fade-in-up': isVisible }">
                    <button 
                        @click="filterProjects('all')"
                        :class="['filter-btn', { active: selectedFilter === 'all' }]"
                    >
                        All Projects
                    </button>
                    <button 
                        @click="filterProjects('fullstack')"
                        :class="['filter-btn', { active: selectedFilter === 'fullstack' }]"
                    >
                        Full Stack
                    </button>
                    <button 
                        @click="filterProjects('frontend')"
                        :class="['filter-btn', { active: selectedFilter === 'frontend' }]"
                    >
                        Frontend
                    </button>
                    <button 
                        @click="filterProjects('backend')"
                        :class="['filter-btn', { active: selectedFilter === 'backend' }]"
                    >
                        Backend
                    </button>
                    <button 
                        @click="filterProjects('mobile')"
                        :class="['filter-btn', { active: selectedFilter === 'mobile' }]"
                    >
                        Mobile
                    </button>
                    <button 
                        @click="filterProjects('ai')"
                        :class="['filter-btn', { active: selectedFilter === 'ai' }]"
                    >
                        AI/ML
                    </button>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section class="projects-section">
            <div class="section-container">
                <div class="projects-grid">
                    <div
                        v-for="(project, index) in filteredProjects"
                        :key="project.id"
                        class="project-card"
                        :class="{ 'fade-in-up': isVisible }"
                        :style="{ animationDelay: `${index * 0.1}s` }"
                    >
                        <div class="project-image">
                            <div class="image-placeholder">
                                <Laptop :size="48" />
                            </div>
                            <div class="project-overlay">
                                <div class="project-links">
                                    <a :href="project.links.demo" class="project-link demo">
                                        <Eye :size="24" />
                                        <span>Live Demo</span>
                                    </a>
                                    <a :href="project.links.github" class="project-link github">
                                        <Github :size="24" />
                                        <span>GitHub</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="project-content">
                            <div class="project-header">
                                <h3 class="project-title">{{ project.title }}</h3>
                                <span class="project-date">{{ project.date }}</span>
                            </div>
                            
                            <p class="project-description">{{ project.description }}</p>
                            
                            <div class="project-technologies">
                                <span
                                    v-for="tech in project.technologies"
                                    :key="tech"
                                    class="tech-tag"
                                >
                                    {{ tech }}
                                </span>
                            </div>
                            
                            <div class="project-status">
                                <span class="status-badge completed">{{ project.status }}</span>
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

/* Portfolio Hero Section */
.portfolio-hero {
    padding: 120px 5% 60px;
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
    max-width: 700px;
    margin: 0 auto;
}

/* Filter Section */
.filter-section {
    padding: 40px 0;
}

.section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 5%;
}

.filter-buttons {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 60px;
}

.filter-btn {
    background: rgba(255, 255, 255, 0.1);
    color: #a0a0a0;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 12px 24px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.filter-btn:hover,
.filter-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

/* Projects Grid */
.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 40px;
    padding: 40px 0;
}

.project-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.project-card:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
}

.project-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: rgba(255, 255, 255, 0.8);
}

.project-icon {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
}

.project-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.project-card:hover .project-overlay {
    opacity: 1;
}

.project-links {
    display: flex;
    gap: 20px;
}

.project-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: #ffffff;
    text-decoration: none;
    padding: 15px 20px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 500;
}

.project-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

.project-link span:first-child {
    font-size: 1.5rem;
}

.project-content {
    padding: 30px;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    gap: 15px;
}

.project-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #ffffff;
    line-height: 1.3;
}

.project-date {
    color: #667eea;
    font-weight: 500;
    font-size: 0.9rem;
    white-space: nowrap;
}

.project-description {
    color: #a0a0a0;
    line-height: 1.6;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.project-technologies {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 20px;
}

.tech-tag {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.tech-tag:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: translateY(-1px);
}

.project-status {
    display: flex;
    justify-content: flex-end;
}

.status-badge {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.completed {
    background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
    color: #ffffff;
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
    
    .projects-grid {
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
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
    
    .portfolio-hero {
        padding: 100px 4% 40px;
    }
    
    .page-title {
        font-size: 2.5rem;
    }
    
    .page-description {
        font-size: 1.1rem;
    }
    
    .filter-buttons {
        gap: 10px;
        margin-bottom: 40px;
    }
    
    .filter-btn {
        padding: 10px 18px;
        font-size: 0.85rem;
    }
    
    .projects-grid {
        grid-template-columns: 1fr;
        gap: 25px;
        padding: 20px 0;
    }
    
    .project-content {
        padding: 20px;
    }
    
    .project-title {
        font-size: 1.3rem;
    }
    
    .project-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
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
    
    .filter-buttons {
        gap: 8px;
    }
    
    .filter-btn {
        padding: 8px 15px;
        font-size: 0.8rem;
    }
    
    .project-image {
        height: 200px;
    }
    
    .image-placeholder {
        font-size: 3rem;
    }
    
    .project-content {
        padding: 15px;
    }
    
    .project-title {
        font-size: 1.2rem;
    }
    
    .project-description {
        font-size: 0.9rem;
    }
    
    .tech-tag {
        font-size: 0.75rem;
        padding: 4px 10px;
    }
}
</style>