<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { Button } from '@/components/ui/button'
import { ScrollArea } from '@/components/ui/scroll-area'
import {
  Mail,
  Github,
  Linkedin,
  Globe,
  MapPin,
  Calendar,
  User,
  Code,
  Briefcase,
  GraduationCap,
  Award,
  BookOpen,
  ExternalLink,
  Users,
  Clock,
  Target,
  Heart,
  Languages,
  Wrench,
  Network,
  Brain,
  Book,
  ChevronRight
} from 'lucide-vue-next'
import FrontendLayout from '@/layouts/FrontendLayout.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

const props = defineProps({
  users: {
    type: Object,
    required: true
  },
  aboutMe: {
    type: Object,
    default: () => ({})
  },
  workExperience: {
    type: Array,
    default: () => []
  },
  education: {
    type: Array,
    default: () => []
  },
  techStacks: {
    type: Array,
    default: () => []
  },
  projects: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: 'Resume'
  },
  description: {
    type: String,
    default: 'Professional Resume'
  }
})

const isVisible = ref(false)
const activeSection = ref('')

onMounted(() => {
  // Faster initial load
  requestAnimationFrame(() => {
    isVisible.value = true
  })
  
  // Intersection observer for smooth section transitions
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('section-visible')
        }
      })
    },
    { threshold: 0.1, rootMargin: '0px 0px -10% 0px' }
  )
  
  const sections = document.querySelectorAll('.observe-section')
  sections.forEach((section) => observer.observe(section))
})

const whoIAmData = [
  "Passionate about creating clean, efficient, and user-friendly web applications",
  "Committed to continuous learning and staying updated with latest technologies",
  "Strong problem-solver with excellent communication and teamwork skills"
]

const skillsData = {
  development: [
    { name: 'HTML5', icon: Code },
    { name: 'CSS3', icon: Code },
    { name: 'JavaScript', icon: Code },
    { name: 'Vue.js', icon: Code },
    { name: 'Next.js', icon: Code },
    { name: 'PHP', icon: Code },
    { name: 'Laravel', icon: Code },
    { name: 'MySQL', icon: Code }
  ],
  tools: [
    { name: 'Git', icon: Wrench },
    { name: 'Vite', icon: Wrench },
    { name: 'Postman', icon: Wrench },
    { name: 'Figma', icon: Wrench },
    { name: 'Digital Ocean', icon: Wrench },
    { name: 'Vercel', icon: Wrench },
    { name: 'Apache', icon: Wrench },
    { name: 'VS Code', icon: Wrench }
  ],
  soft: [
    { name: 'Problem Solving', icon: Brain },
    { name: 'Time Management', icon: Clock },
    { name: 'Team Collaboration', icon: Users },
    { name: 'Communication', icon: Users }
  ]
}

const languages = [
  { name: 'Khmer', level: 'Native' },
  { name: 'English', level: 'Intermediate' }
]

const volunteering = [
  {
    title: 'Community Tech Support',
    organization: 'Local Community Center',
    period: '2023 - Present',
    description: 'Providing technical support and computer literacy training to community members'
  }
]

const certifications = [
  {
    title: 'Laravel Certified Developer',
    organization: 'Laravel',
    date: '2024',
    id: 'LC-2024-001'
  },
  {
    title: 'Vue.js Developer Certification',
    organization: 'Vue School',
    date: '2023',
    id: 'VS-2023-VUE'
  }
]

const popularArticles = [
  {
    title: 'Building Modern Web Applications with Vue.js and Laravel',
    year: '2024',
    summary: 'A comprehensive guide to creating full-stack applications using Vue.js frontend and Laravel backend.',
    link: '/articles/vue-laravel-guide'
  },
  {
    title: 'Best Practices for Database Design in Laravel',
    year: '2023',
    summary: 'Essential tips and techniques for designing efficient and scalable database schemas.',
    link: '/articles/laravel-database-design'
  }
]
</script>

<template>

  <Head>
    <title>{{ title }}</title>
    <meta name="description" :content="description" />
    <meta name="keywords" content="resume, CV, professional experience, software developer, skills, work history, SROS THAI, Vue.js developer, Laravel developer, web development, Cambodia developer" />
    <meta name="author" :content="users.name || 'SROS THAI - Software Developer'" />
    <meta name="language" content="en" />
    <meta name="geo.region" content="KH" />
    <meta name="geo.country" content="Cambodia" />
    <meta name="theme-color" content="#2563eb" />
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" :content="title" />
    <meta property="og:description" :content="description" />
    <meta property="og:image" :content="users.image ? (users.image.startsWith('http') ? users.image : `${$page.url}/${users.image}`) : `${$page.url}/resume-og-image.jpg`" />
    <meta property="og:url" :content="$page.url" />
    <meta property="og:type" content="profile" />
    <meta property="og:site_name" :content="users.name + ' - Resume'" />
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" :content="title" />
    <meta name="twitter:description" :content="description" />
    <meta name="twitter:image" :content="users.image ? (users.image.startsWith('http') ? users.image : `${$page.url}/${users.image}`) : `${$page.url}/resume-og-image.jpg`" />
    
    <!-- Additional SEO Meta Tags -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="canonical" :href="$page.url" />
  </Head>

  <FrontendLayout currentRoute="/resume">
    <!-- Music Player -->
    <MusicPlayer />
    
    <div class="min-h-screen bg-background text-foreground relative">
      <!-- Subtle background pattern -->
      <div class="absolute inset-0 bg-dot-pattern opacity-[0.02] pointer-events-none"></div>

    <!-- Header/Hero Section -->
    <section class="py-16 lg:py-24 px-4 max-w-5xl mx-auto relative z-10 observe-section" :class="{ 'fade-in': isVisible }">
      <div class="flex flex-col lg:flex-row gap-12 items-center lg:items-start">
        <!-- Profile Picture -->
        <div class="flex-shrink-0">
          <Avatar class="w-32 h-32 md:w-40 md:h-40 border-2 border-border shadow-sm">
            <AvatarImage v-if="users.image" :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
              :alt="users.name" class="object-cover" />
            <AvatarFallback class="bg-muted text-foreground text-2xl md:text-3xl font-medium">
              {{ users.name?.charAt(0) }}
            </AvatarFallback>
          </Avatar>
        </div>

        <!-- Profile Info -->
        <div class="flex-1 text-center lg:text-left space-y-4">
          <div>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-foreground mb-2">
              {{ users.name || 'Your Name' }}
            </h1>
            <p class="text-lg md:text-xl text-muted-foreground font-medium mb-3">
              {{ users.position || 'Software Developer' }}
            </p>
            <p class="text-muted-foreground leading-relaxed max-w-2xl">
              {{ users.description || 'A passionate developer with experience in creating modern web applications and solving complex problems through code.' }}
            </p>
          </div>

          <!-- Social Links -->
          <div class="flex items-center justify-center lg:justify-start gap-3 pt-4">
            <Button asChild size="sm" variant="ghost" class="hover:bg-accent transition-colors">
              <a href="mailto:srosthai00@gmail.com" class="flex items-center gap-2">
                <Mail class="h-4 w-4" />
                <span class="hidden sm:inline">Email</span>
              </a>
            </Button>
            <Button asChild size="sm" variant="ghost" class="hover:bg-accent transition-colors">
              <a href="https://github.com/srosthai" target="_blank" class="flex items-center gap-2">
                <Github class="h-4 w-4" />
                <span class="hidden sm:inline">GitHub</span>
              </a>
            </Button>
            <Button asChild size="sm" variant="ghost" class="hover:bg-accent transition-colors">
              <a href="https://www.linkedin.com/in/sros-thai-b491b42ab/" target="_blank"
                class="flex items-center gap-2">
                <Linkedin class="h-4 w-4" />
                <span class="hidden sm:inline">LinkedIn</span>
              </a>
            </Button>
            <Button asChild size="sm" variant="ghost" class="hover:bg-accent transition-colors">
              <Link href="/note" class="flex items-center gap-2">
              <Book class="h-4 w-4" />
              <span class="hidden sm:inline">Notes</span>
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 pb-16 space-y-12 relative z-10">
      <!-- Who I Am Section -->
      <section class="observe-section">
        <Card class="border-border/50 shadow-none hover:border-border transition-colors">
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
              <User class="h-4 w-4 text-muted-foreground" />
              Who I Am
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <p v-for="(statement, index) in whoIAmData" :key="index" class="text-muted-foreground text-sm leading-relaxed">
                {{ statement }}
              </p>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Skills Section -->
      <section class="observe-section">
        <Card class="border-border/50 shadow-none hover:border-border transition-colors">
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
              <Code class="h-4 w-4 text-muted-foreground" />
              Skills
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- Development Skills -->
            <div>
              <h3 class="font-medium mb-3 text-sm text-muted-foreground">
                Development
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="skill in skillsData.development" :key="skill.name" variant="outline" class="px-3 py-1 text-xs font-normal hover:bg-accent transition-colors cursor-default">
                  {{ skill.name }}
                </Badge>
              </div>
            </div>

            <!-- Tools -->
            <div>
              <h3 class="font-medium mb-3 text-sm text-muted-foreground">
                Tools
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="tool in skillsData.tools" :key="tool.name" variant="outline" class="px-3 py-1 text-xs font-normal hover:bg-accent transition-colors cursor-default">
                  {{ tool.name }}
                </Badge>
              </div>
            </div>

            <!-- Soft Skills -->
            <div>
              <h3 class="font-medium mb-3 text-sm text-muted-foreground">
                Soft Skills
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="skill in skillsData.soft" :key="skill.name" variant="outline" class="px-3 py-1 text-xs font-normal hover:bg-accent transition-colors cursor-default">
                  {{ skill.name }}
                </Badge>
              </div>
            </div>

            <!-- Languages -->
            <div>
              <h3 class="font-medium mb-3 text-sm text-muted-foreground">
                Languages
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="lang in languages" :key="lang.name" variant="outline" class="px-3 py-1 text-xs font-normal hover:bg-accent transition-colors cursor-default">
                  {{ lang.name }} · {{ lang.level }}
                </Badge>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Work Experience Section -->
      <section class="observe-section">
        <Card class="border-border/50 shadow-none hover:border-border transition-colors">
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
              <Briefcase class="h-4 w-4 text-muted-foreground" />
              Work Experience
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-6">
              <div v-for="(work, index) in workExperience" :key="index" class="relative pl-6 pb-6 border-l-2 border-border last:border-0">
                <div class="absolute left-0 w-2 h-2 bg-border rounded-full -translate-x-[5px]"></div>
                <div class="space-y-2">
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                    <h3 class="font-medium text-foreground">{{ work.position }}</h3>
                    <span class="text-xs text-muted-foreground">
                      {{ work.from }} - {{ work.to || 'Present' }}
                    </span>
                  </div>
                  <p class="text-sm text-muted-foreground">{{ work.company }}</p>
                  <p class="text-sm text-muted-foreground leading-relaxed">{{ work.description }}</p>
                  <div v-if="work.responsibilities" class="mt-3">
                    <ul class="text-xs space-y-1 text-muted-foreground">
                      <li v-for="responsibility in work.responsibilities.split('\n')" :key="responsibility"
                        class="flex items-start gap-2">
                        <ChevronRight class="h-3 w-3 mt-0.5 flex-shrink-0" />
                        <span>{{ responsibility }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Volunteering Section -->
      <section :class="{ 'fade-in-up': isVisible }" hidden>
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Heart class="h-5 w-5 text-primary" />
              Volunteering
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div v-for="(volunteer, index) in volunteering" :key="index" class="space-y-2">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                  <h3 class="font-semibold">{{ volunteer.title }}</h3>
                  <span class="text-sm text-muted-foreground">{{ volunteer.organization }}</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                  <Calendar class="h-4 w-4" />
                  {{ volunteer.period }}
                </div>
                <p class="text-muted-foreground">{{ volunteer.description }}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Education Section -->
      <section class="observe-section">
        <Card class="border-border/50 shadow-none hover:border-border transition-colors">
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
              <GraduationCap class="h-4 w-4 text-muted-foreground" />
              Education
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div v-for="(edu, index) in education" :key="index" class="pb-4 border-b border-border last:border-0 last:pb-0">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                  <div>
                    <h3 class="font-medium text-foreground">{{ edu.degree }}</h3>
                    <p class="text-sm text-muted-foreground">{{ edu.institution }}</p>
                  </div>
                  <span class="text-xs text-muted-foreground">
                    {{ edu.from }} - {{ edu.to }}
                  </span>
                </div>
                <p v-if="edu.description" class="text-sm text-muted-foreground mt-2 leading-relaxed">{{ edu.description }}</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Certifications Section -->
      <section :class="{ 'fade-in-up': isVisible }" hidden>
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Award class="h-5 w-5 text-primary" />
              Certifications
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div v-for="(cert, index) in certifications" :key="index" class="flex justify-between items-start">
                <div class="space-y-1">
                  <h3 class="font-semibold">{{ cert.title }}</h3>
                  <p class="text-sm text-muted-foreground">{{ cert.organization }}</p>
                  <p class="text-xs text-muted-foreground">ID: {{ cert.id }}</p>
                </div>
                <Badge variant="outline">{{ cert.date }}</Badge>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Popular Articles Section -->
      <section :class="{ 'fade-in-up': isVisible }" hidden>
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <BookOpen class="h-5 w-5 text-primary" />
              Popular Articles
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div v-for="(article, index) in popularArticles" :key="index" class="space-y-2">
                <div class="flex justify-between items-start">
                  <div class="flex-1">
                    <h3 class="font-semibold">{{ article.title }}</h3>
                    <p class="text-sm text-muted-foreground mb-2">{{ article.summary }}</p>
                    <Button asChild size="sm" variant="link" class="p-0 h-auto">
                      <a :href="article.link" class="flex items-center gap-1">
                        Continue reading
                        <ExternalLink class="h-3 w-3" />
                      </a>
                    </Button>
                  </div>
                  <Badge variant="outline">{{ article.year }}</Badge>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Projects Section -->
      <section class="observe-section">
        <Card class="border-border/50 shadow-none hover:border-border transition-colors">
          <CardHeader class="pb-3">
            <CardTitle class="flex items-center gap-2 text-lg font-medium">
              <Target class="h-4 w-4 text-muted-foreground" />
              Projects
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid gap-4 md:grid-cols-2">
              <div v-for="(project, index) in projects.slice(0, 4)" :key="index"
                class="border border-border rounded-lg p-4 space-y-3 hover:bg-accent/50 transition-colors">
                <div class="flex justify-between items-start">
                  <h3 class="font-medium text-sm text-foreground">{{ project.title }}</h3>
                  <Button v-if="project.link" asChild size="icon" variant="ghost" class="h-6 w-6">
                    <a :href="project.link" target="_blank">
                      <ExternalLink class="h-3 w-3" />
                    </a>
                  </Button>
                </div>
                <p class="text-xs text-muted-foreground leading-relaxed">{{ project.description }}</p>
                <div v-if="project.tech_stack" class="flex flex-wrap gap-1">
                  <span v-for="tech in project.tech_stack.split(',')" :key="tech" class="text-xs px-2 py-0.5 bg-muted rounded-md">
                    {{ tech.trim() }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Show more projects link -->
            <div v-if="projects.length > 1" class="mt-4 pt-4 border-t border-border">
              <Button asChild variant="ghost" size="sm" class="w-full hover:bg-accent">
                <a href="/portfolio" class="flex items-center justify-center gap-2 text-xs">
                  View All Projects
                  <ChevronRight class="h-3 w-3" />
                </a>
              </Button>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Footer -->
      <footer class="text-center py-8 border-t border-border mt-12">
        <p class="text-xs text-muted-foreground">
          © {{ new Date().getFullYear() }} {{ users.name || 'Your Name' }}. All rights reserved.
        </p>
      </footer>
    </div>
    </div>
  </FrontendLayout>
</template>

<style scoped>
/* Optimized animations with will-change for better performance */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in {
  animation: fadeIn 0.5s ease-out forwards;
}

/* Section visibility transition */
.observe-section {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.5s ease-out, transform 0.5s ease-out;
  will-change: opacity, transform;
}

.section-visible {
  opacity: 1;
  transform: translateY(0);
}

/* Subtle dot pattern background */
.bg-dot-pattern {
  background-image: radial-gradient(circle, currentColor 1px, transparent 1px);
  background-size: 20px 20px;
}

/* Optimized transitions */
.transition-colors {
  transition-property: color, background-color, border-color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Custom scrollbar - minimal design */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: hsl(var(--border));
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: hsl(var(--muted-foreground) / 0.3);
}

/* Performance optimizations */
* {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}

/* GPU acceleration for smooth scrolling */
.observe-section {
  transform: translateZ(0);
  backface-visibility: hidden;
}
</style>