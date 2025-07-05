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

const education = [
    {
        period: '2020-2024',
        title: 'Bachelor of Computer Systems and Networks',
        institution: 'BSUIR (Belarusian State University of Informatics and Radioelectronics)',
        description: 'Specialized in computer systems architecture, network protocols, and software development. Graduated with honors.',
        type: 'education'
    },
    {
        period: '2020',
        title: 'Fullstack Development Bootcamp',
        institution: 'IBA (International Business Academy)',
        description: 'Intensive training in modern web development technologies including Java, Spring Boot, JavaScript, React, and Redux.',
        type: 'education'
    }
]

const experience = [
    {
        period: '2022-Present',
        title: 'Senior Fullstack Developer',
        company: 'Tech Solutions Inc.',
        description: 'Leading development of enterprise web applications using React, Node.js, and PostgreSQL. Mentoring junior developers and architecting scalable solutions.',
        type: 'experience'
    },
    {
        period: '2020-2022',
        title: 'Software Engineer',
        company: 'Digital Innovations LLC',
        description: 'Developed and maintained multiple fullstack applications. Implemented automated testing and CI/CD pipelines. Collaborated with cross-functional teams.',
        type: 'experience'
    },
    {
        period: '2020',
        title: 'Junior Developer',
        company: 'StartUp Solutions',
        description: 'Started career in software development. Worked on frontend applications using React and contributed to backend API development.',
        type: 'experience'
    }
]

const techStack = [
    // Frontend Technologies
    {
        name: 'React',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg',
        description: 'A JavaScript library for building user interfaces with component-based architecture.'
    },
    {
        name: 'Vue.js',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg',
        description: 'Progressive JavaScript framework for building modern web applications.'
    },
    {
        name: 'Next.js',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nextjs/nextjs-original.svg',
        description: 'React framework for production with server-side rendering and static site generation.'
    },
    {
        name: 'TypeScript',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg',
        description: 'Strongly typed programming language that builds on JavaScript.'
    },
    {
        name: 'JavaScript',
        category: 'Frontend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg',
        description: 'High-level programming language for web development and interactive applications.'
    },
    // Backend Technologies
    {
        name: 'Node.js',
        category: 'Backend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nodejs/nodejs-original.svg',
        description: 'JavaScript runtime built on Chrome\'s V8 engine for server-side development.'
    },
    {
        name: 'Express.js',
        category: 'Backend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/express/express-original.svg',
        description: 'Fast, unopinionated web framework for Node.js applications.'
    },
    {
        name: 'Spring Boot',
        category: 'Backend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/spring/spring-original.svg',
        description: 'Java-based framework for building enterprise-grade applications.'
    },
    {
        name: 'Java',
        category: 'Backend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg',
        description: 'Object-oriented programming language for building scalable applications.'
    },
    {
        name: 'Python',
        category: 'Backend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg',
        description: 'High-level programming language known for its simplicity and versatility.'
    },
    {
        name: 'Go',
        category: 'Backend',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/go/go-original.svg',
        description: 'Open-source programming language designed for simplicity and efficiency.'
    },
    // Database Technologies
    {
        name: 'PostgreSQL',
        category: 'Database',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/postgresql/postgresql-original.svg',
        description: 'Advanced open-source relational database with powerful features.'
    },
    {
        name: 'MongoDB',
        category: 'Database',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mongodb/mongodb-original.svg',
        description: 'NoSQL document database for modern applications.'
    },
    {
        name: 'Redis',
        category: 'Database',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/redis/redis-original.svg',
        description: 'In-memory data structure store used as database, cache, and message broker.'
    },
    {
        name: 'MySQL',
        category: 'Database',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg',
        description: 'Popular open-source relational database management system.'
    },
    // DevOps & Tools
    {
        name: 'Docker',
        category: 'DevOps & Tools',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg',
        description: 'Platform for developing, shipping, and running applications in containers.'
    },
    {
        name: 'Git',
        category: 'DevOps & Tools',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/git/git-original.svg',
        description: 'Distributed version control system for tracking changes in source code.'
    },
    {
        name: 'Linux',
        category: 'DevOps & Tools',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linux/linux-original.svg',
        description: 'Open-source operating system kernel widely used in server environments.'
    },
    {
        name: 'Nginx',
        category: 'DevOps & Tools',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nginx/nginx-original.svg',
        description: 'High-performance web server and reverse proxy server.'
    },
    // Design & Media
    {
        name: 'Figma',
        category: 'Design & Media',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg',
        description: 'Collaborative design tool for creating user interfaces and prototypes.'
    },
    {
        name: 'Adobe Photoshop',
        category: 'Design & Media',
        image: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/photoshop/photoshop-plain.svg',
        description: 'Professional image editing software for creative design work.'
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

    <div class="about-container">
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
                    <li><Link href="/about" class="nav-link active">About Me</Link></li>
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

        <!-- About Hero Section -->
        <section class="about-hero">
            <div class="hero-content">
                <h1 class="page-title" :class="{ 'fade-in-up': isVisible }">About Me</h1>
                <p class="page-description" :class="{ 'fade-in-up': isVisible }">
                    Passionate software engineer with a strong foundation in fullstack development and a keen eye for creating innovative solutions.
                </p>
            </div>
        </section>

        <!-- Resume Section -->
        <section class="resume-section">
            <div class="section-container">
                <h2 class="section-title" :class="{ 'fade-in-up': isVisible }">Resume</h2>
                
                <!-- Experience Timeline -->
                <div class="timeline-container">
                    <h3 class="timeline-category" :class="{ 'fade-in-up': isVisible }">Professional Experience</h3>
                    <div class="timeline">
                        <div
                            v-for="(item, index) in experience"
                            :key="index"
                            class="timeline-item experience"
                            :class="{ 'fade-in-up': isVisible }"
                            :style="{ animationDelay: `${index * 0.2}s` }"
                        >
                            <div class="timeline-period">{{ item.period }}</div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">{{ item.title }}</h4>
                                <h5 class="timeline-company">{{ item.company }}</h5>
                                <p class="timeline-description">{{ item.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Education Timeline -->
                <div class="timeline-container">
                    <h3 class="timeline-category" :class="{ 'fade-in-up': isVisible }">Education</h3>
                    <div class="timeline">
                        <div
                            v-for="(item, index) in education"
                            :key="index"
                            class="timeline-item education"
                            :class="{ 'fade-in-up': isVisible }"
                            :style="{ animationDelay: `${(index + experience.length) * 0.2}s` }"
                        >
                            <div class="timeline-period">{{ item.period }}</div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">{{ item.title }}</h4>
                                <h5 class="timeline-institution">{{ item.institution }}</h5>
                                <p class="timeline-description">{{ item.description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tech Stack Section -->
        <section class="techstack-section">
            <div class="section-container">
                <h2 class="section-title" :class="{ 'fade-in-up': isVisible }">Tech Stack</h2>
                <div class="techstack-cards-grid">
                    <div
                        v-for="(tech, index) in techStack"
                        :key="index"
                        class="tech-card"
                        :class="{ 'fade-in-up': isVisible }"
                        :style="{ animationDelay: `${index * 0.1}s` }"
                    >
                        <div class="tech-card-header">
                            <div class="tech-image-container">
                                <img :src="tech.image" :alt="tech.name" class="tech-image" />
                            </div>
                            <div class="tech-category-badge">{{ tech.category }}</div>
                        </div>
                        <div class="tech-card-content">
                            <h3 class="tech-name">{{ tech.name }}</h3>
                            <p class="tech-description">{{ tech.description }}</p>
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

.about-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
    color: #ffffff;
    font-family: 'Arial', 'Helvetica', sans-serif;
    overflow-x: hidden;
}

/* Navigation - Same as Portfolio.vue */
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

/* About Hero Section */
.about-hero {
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

/* Timeline */
.timeline-container {
    margin-bottom: 60px;
}

.timeline-category {
    font-size: 1.8rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 40px;
    text-align: center;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 50px;
    padding-left: 50px;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 30px 30px 30px 70px;
    transition: all 0.3s ease;
}

.timeline-item:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -35px;
    top: 30px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 3px solid #1a1a2e;
}

.timeline-item.experience::before {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.timeline-item.education::before {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
}

.timeline-period {
    color: #667eea;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 8px;
}

.timeline-title {
    color: #ffffff;
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 8px;
}

.timeline-company,
.timeline-institution {
    color: #c0c0c0;
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 15px;
}

.timeline-description {
    color: #a0a0a0;
    line-height: 1.6;
    font-size: 1rem;
}

/* Tech Stack */
.techstack-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

.tech-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 25px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.tech-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.tech-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
}

.tech-card:hover::before {
    opacity: 1;
}

.tech-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.tech-image-container {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 8px;
    transition: all 0.3s ease;
}

.tech-card:hover .tech-image-container {
    background: rgba(255, 255, 255, 0.15);
    transform: scale(1.05);
}

.tech-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: brightness(0.9);
    transition: filter 0.3s ease;
}

.tech-card:hover .tech-image {
    filter: brightness(1);
}

.tech-category-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.tech-card:hover .tech-category-badge {
    opacity: 1;
}

.tech-card-content {
    text-align: left;
}

.tech-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 12px;
    background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.tech-description {
    color: #a0a0a0;
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
    transition: color 0.3s ease;
}

.tech-card:hover .tech-description {
    color: #b0b0b0;
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
    
    .about-hero {
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
    
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-item {
        padding: 20px 20px 20px 50px;
        margin-bottom: 30px;
    }
    
    .timeline-item::before {
        left: -25px;
        top: 20px;
        width: 12px;
        height: 12px;
    }
    
    .techstack-cards-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .tech-card {
        padding: 20px;
    }
    
    .tech-image-container {
        width: 45px;
        height: 45px;
    }
    
    .tech-name {
        font-size: 1.1rem;
    }
    
    .tech-description {
        font-size: 0.85rem;
    }
    
    .timeline-title {
        font-size: 1.2rem;
    }
    
    .timeline-company,
    .timeline-institution {
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
    
    .section-container {
        padding: 40px 3%;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .timeline-item {
        padding: 15px 15px 15px 40px;
    }
    
    .techstack-cards-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .tech-card {
        padding: 18px;
    }
    
    .tech-image-container {
        width: 40px;
        height: 40px;
    }
    
    .tech-name {
        font-size: 1rem;
    }
    
    .tech-description {
        font-size: 0.8rem;
    }
    
    .tech-category-badge {
        font-size: 0.7rem;
        padding: 5px 10px;
    }
}
</style>