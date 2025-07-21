<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { Button } from '@/components/ui/button'
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
  Book
} from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'
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

onMounted(() => {
  setTimeout(() => {
    isVisible.value = true
  }, 100)
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
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Person",
      "name": "{{ users.name || 'SROS THAI' }}",
      "jobTitle": "{{ users.position || 'Software Developer' }}",
      "description": "{{ users.description || 'Professional software developer specializing in Vue.js, Laravel, and modern web technologies. Experienced in creating scalable web applications and solving complex technical challenges.' }}",
      "url": "{{ $page.url }}",
      "email": "srosthai00@gmail.com",
      "image": "{{ users.image ? (users.image.startsWith('http') ? users.image : $page.url + '/' + users.image) : $page.url + '/resume-og-image.jpg' }}",
      "sameAs": [
        "https://github.com/Sovannthai",
        "https://www.linkedin.com/in/sros-thai-b491b42ab/",
        "https://www.facebook.com/samo.thai.73",
        "https://www.instagram.com/sovannthai887",
        "https://t.me/srosthai2003"
      ],
      "knowsAbout": [
        "Vue.js", "Laravel", "JavaScript", "PHP", "MySQL", "HTML5", "CSS3", "Web Development", "Software Development"
      ],
      "alumniOf": {
        "@type": "Organization",
        "name": "{{ education && education[0] ? education[0].institution : 'Educational Institution' }}"
      },
      "workLocation": {
        "@type": "Place",
        "address": {
          "@type": "PostalAddress",
          "addressCountry": "KH",
          "addressRegion": "Cambodia"
        }
      },
      "hasOccupation": {
        "@type": "Occupation",
        "name": "Software Developer",
        "occupationLocation": {
          "@type": "Country",
          "name": "Cambodia"
        },
        "skills": "Vue.js, Laravel, JavaScript, PHP, MySQL, Web Development"
      }
    }
    </script>
  </Head>

  <div class="min-h-screen bg-gradient-to-br from-background via-background to-muted/20 text-foreground relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-grid-pattern opacity-5 pointer-events-none"></div>
    
    <!-- Dock Navigation -->
    <DockNavigation currentRoute="/resume" />

    <!-- Music Player -->
    <MusicPlayer />

    <!-- Header/Hero Section -->
    <section class="py-12 lg:py-20 px-4 max-w-5xl mx-auto relative z-10" :class="{ 'fade-in-up': isVisible }">
      <div class="flex flex-col lg:flex-row gap-12 items-center lg:items-start mt-10">
        <!-- Profile Picture -->
        <div class="flex-shrink-0 relative group">
          <div class="absolute -inset-1 bg-gradient-to-r from-primary/50 to-primary/30 rounded-full blur opacity-60 group-hover:opacity-100 transition duration-300"></div>
          <Avatar class="relative w-36 h-36 md:w-44 md:h-44 border-4 border-background shadow-2xl">
            <AvatarImage v-if="users.image" :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
              :alt="users.name" class="object-cover" />
            <AvatarFallback class="bg-gradient-to-br from-primary/20 to-primary/10 text-primary text-3xl md:text-4xl font-semibold">
              {{ users.name?.charAt(0) }}
            </AvatarFallback>
          </Avatar>
        </div>

        <!-- Profile Info -->
        <div class="flex-1 text-center lg:text-left space-y-6">
          <div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold bg-gradient-to-r from-foreground to-muted-foreground bg-clip-text text-transparent mb-3">
              {{ users.name || 'Your Name' }}
            </h1>
            <p class="text-xl md:text-2xl text-primary font-medium mb-4 tracking-wide">
              {{ users.position || 'Software Developer' }}
            </p>
            <p class="text-muted-foreground leading-relaxed max-w-2xl text-lg">
              {{ users.description || 'A passionate developer with experience in creating modern web applications and solving complex problems through code.' }}
            </p>
          </div>

          <!-- Social Links -->
          <div class="flex items-center justify-center lg:justify-start gap-4 pt-6">
            <Button asChild size="default" variant="outline" class="hover:bg-primary hover:text-primary-foreground transition-all duration-300 hover:scale-105">
              <a href="mailto:srosthai00@gmail.com" class="flex items-center gap-2">
                <Mail class="h-4 w-4" />
                Email
              </a>
            </Button>
            <Button asChild size="default" variant="outline" class="hover:bg-primary hover:text-primary-foreground transition-all duration-300 hover:scale-105">
              <a href="https://github.com/Sovannthai" target="_blank" class="flex items-center gap-2">
                <Github class="h-4 w-4" />
                GitHub
              </a>
            </Button>
            <Button asChild size="default" variant="outline" class="hover:bg-primary hover:text-primary-foreground transition-all duration-300 hover:scale-105">
              <a href="https://www.linkedin.com/in/sros-thai-b491b42ab/" target="_blank"
                class="flex items-center gap-2">
                <Linkedin class="h-4 w-4" />
                LinkedIn
              </a>
            </Button>
            <Button asChild size="default" variant="outline" class="hover:bg-primary hover:text-primary-foreground transition-all duration-300 hover:scale-105">
              <Link href="/note" class="flex items-center gap-2">
              <Book class="h-4 w-4" />
              Notes
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 pb-12 space-y-16 relative z-10">
      <!-- Who I Am Section -->
      <section :class="{ 'fade-in-up': isVisible }">
        <Card class="border-0 bg-card/60 backdrop-blur-sm hover:bg-card/80 transition-all duration-300 hover:shadow-xl">
          <CardHeader>
            <CardTitle class="flex items-center gap-3 text-2xl">
              <div class="p-2 rounded-full bg-primary/10">
                <User class="h-6 w-6 text-primary" />
              </div>
              Who I Am
            </CardTitle>
          </CardHeader>
          <CardContent class="pt-2">
            <div class="space-y-4">
              <p v-for="(statement, index) in whoIAmData" :key="index" class="text-muted-foreground text-lg leading-relaxed">
                {{ statement }}
              </p>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Skills Section -->
      <section :class="{ 'fade-in-up': isVisible }">
        <Card class="border-0 bg-card/60 backdrop-blur-sm hover:bg-card/80 transition-all duration-300 hover:shadow-xl">
          <CardHeader>
            <CardTitle class="flex items-center gap-3 text-2xl">
              <div class="p-2 rounded-full bg-primary/10">
                <Code class="h-6 w-6 text-primary" />
              </div>
              Skills
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-8 pt-2">
            <!-- Development Skills -->
            <div>
              <h3 class="font-semibold mb-4 flex items-center gap-2 text-lg">
                <Code class="h-5 w-5 text-primary" />
                Development
              </h3>
              <div class="flex flex-wrap gap-3">
                <Badge v-for="skill in skillsData.development" :key="skill.name" variant="secondary" class="px-4 py-2 text-sm hover:bg-primary hover:text-primary-foreground transition-all duration-300 cursor-default">
                  {{ skill.name }}
                </Badge>
              </div>
            </div>

            <!-- Tools -->
            <div>
              <h3 class="font-semibold mb-4 flex items-center gap-2 text-lg">
                <Wrench class="h-5 w-5 text-primary" />
                Tools
              </h3>
              <div class="flex flex-wrap gap-3">
                <Badge v-for="tool in skillsData.tools" :key="tool.name" variant="secondary" class="px-4 py-2 text-sm hover:bg-primary hover:text-primary-foreground transition-all duration-300 cursor-default">
                  {{ tool.name }}
                </Badge>
              </div>
            </div>

            <!-- Soft Skills -->
            <div>
              <h3 class="font-semibold mb-4 flex items-center gap-2 text-lg">
                <Brain class="h-5 w-5 text-primary" />
                Soft Skills
              </h3>
              <div class="flex flex-wrap gap-3">
                <Badge v-for="skill in skillsData.soft" :key="skill.name" variant="secondary" class="px-4 py-2 text-sm hover:bg-primary hover:text-primary-foreground transition-all duration-300 cursor-default">
                  {{ skill.name }}
                </Badge>
              </div>
            </div>

            <!-- Languages -->
            <div>
              <h3 class="font-semibold mb-4 flex items-center gap-2 text-lg">
                <Languages class="h-5 w-5 text-primary" />
                Languages
              </h3>
              <div class="flex flex-wrap gap-3">
                <Badge v-for="lang in languages" :key="lang.name" variant="outline" class="px-4 py-2 text-sm hover:bg-primary hover:text-primary-foreground transition-all duration-300 cursor-default">
                  {{ lang.name }} ({{ lang.level }})
                </Badge>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Work Experience Section -->
      <section :class="{ 'fade-in-up': isVisible }">
        <Card class="border-0 bg-card/60 backdrop-blur-sm hover:bg-card/80 transition-all duration-300 hover:shadow-xl">
          <CardHeader>
            <CardTitle class="flex items-center gap-3 text-2xl">
              <div class="p-2 rounded-full bg-primary/10">
                <Briefcase class="h-6 w-6 text-primary" />
              </div>
              Work Experience
            </CardTitle>
          </CardHeader>
          <CardContent class="pt-2">
            <div class="space-y-8">
              <div v-for="(work, index) in workExperience" :key="index" class="relative p-6 rounded-xl border border-border/50 hover:border-primary/20 transition-all duration-300 hover:bg-muted/20">
                <div class="flex flex-col md:flex-row gap-6">
                  <div class="md:w-1/3">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground mb-2">
                      <Calendar class="h-4 w-4" />
                      {{ work.from }} - {{ work.to || 'Present' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-primary font-medium">
                      <MapPin class="h-4 w-4" />
                      {{ work.company }}
                    </div>
                  </div>
                  <div class="md:w-2/3">
                    <h3 class="font-semibold text-xl mb-3 text-foreground">{{ work.position }}</h3>
                    <p class="text-muted-foreground mb-4 text-lg leading-relaxed">{{ work.description }}</p>
                    <div v-if="work.responsibilities" class="space-y-2">
                      <p class="text-sm font-medium text-foreground">Key Responsibilities:</p>
                      <ul class="text-sm space-y-2 ml-4">
                        <li v-for="responsibility in work.responsibilities.split('\n')" :key="responsibility"
                          class="list-disc text-muted-foreground leading-relaxed">
                          {{ responsibility }}
                        </li>
                      </ul>
                    </div>
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
      <section :class="{ 'fade-in-up': isVisible }">
        <Card class="border-0 bg-card/60 backdrop-blur-sm hover:bg-card/80 transition-all duration-300 hover:shadow-xl">
          <CardHeader>
            <CardTitle class="flex items-center gap-3 text-2xl">
              <div class="p-2 rounded-full bg-primary/10">
                <GraduationCap class="h-6 w-6 text-primary" />
              </div>
              Education
            </CardTitle>
          </CardHeader>
          <CardContent class="pt-2">
            <div class="space-y-6">
              <div v-for="(edu, index) in education" :key="index" class="p-6 rounded-xl border border-border/50 hover:border-primary/20 transition-all duration-300 hover:bg-muted/20">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div>
                    <h3 class="font-semibold text-xl mb-2">{{ edu.degree }}</h3>
                    <p class="text-primary font-medium">{{ edu.institution }}</p>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Calendar class="h-4 w-4" />
                    {{ edu.from }} - {{ edu.to }}
                  </div>
                </div>
                <p v-if="edu.description" class="text-muted-foreground mt-3 leading-relaxed">{{ edu.description }}</p>
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
      <section :class="{ 'fade-in-up': isVisible }">
        <Card class="border-0 bg-card/60 backdrop-blur-sm hover:bg-card/80 transition-all duration-300 hover:shadow-xl">
          <CardHeader>
            <CardTitle class="flex items-center gap-3 text-2xl">
              <div class="p-2 rounded-full bg-primary/10">
                <Target class="h-6 w-6 text-primary" />
              </div>
              Projects
            </CardTitle>
          </CardHeader>
          <CardContent class="pt-2">
            <div class="grid gap-6 md:grid-cols-2">
              <div v-for="(project, index) in projects.slice(0, 4)" :key="index"
                class="border border-border/50 rounded-xl p-6 space-y-4 hover:border-primary/20 transition-all duration-300 hover:bg-muted/20 hover:shadow-lg group">
                <div class="flex justify-between items-start">
                  <h3 class="font-semibold text-lg group-hover:text-primary transition-colors duration-300">{{ project.title }}</h3>
                  <Button v-if="project.link" asChild size="sm" variant="ghost" class="hover:bg-primary hover:text-primary-foreground">
                    <a :href="project.link" target="_blank" class="h-8 w-8 p-0">
                      <ExternalLink class="h-4 w-4" />
                    </a>
                  </Button>
                </div>
                <p class="text-muted-foreground leading-relaxed">{{ project.description }}</p>
                <div v-if="project.tech_stack" class="flex flex-wrap gap-2">
                  <Badge v-for="tech in project.tech_stack.split(',')" :key="tech" variant="secondary" class="text-xs px-2 py-1 hover:bg-primary hover:text-primary-foreground transition-all duration-300">
                    {{ tech.trim() }}
                  </Badge>
                </div>
              </div>
            </div>

            <!-- Show more projects link -->
            <div v-if="projects.length > 1" class="text-center mt-8">
              <Button asChild variant="outline" class="hover:bg-primary hover:text-primary-foreground transition-all duration-300 hover:scale-105">
                <a href="/portfolio" class="flex items-center gap-2">
                  View All Projects
                  <ExternalLink class="h-4 w-4" />
                </a>
              </Button>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Footer -->
      <footer class="text-center py-12 border-t border-border/50 mt-16">
        <p class="text-muted-foreground">
          © {{ new Date().getFullYear() }} {{ users.name || 'Your Name' }}. All rights reserved.
        </p>
      </footer>
    </div>
  </div>
</template>

<style scoped>
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
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

.fade-in-up {
  animation: fadeInUp 0.8s ease-out forwards;
}

.bg-grid-pattern {
  background-image: 
    linear-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 50px 50px;
}

.group:hover .group-hover\:animate-float {
  animation: float 3s ease-in-out infinite;
}

/* Smooth transitions for all interactive elements */
.transition-all {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Glass morphism effect */
.backdrop-blur-sm {
  backdrop-filter: blur(8px);
}

/* Gradient text */
.bg-clip-text {
  background-clip: text;
  -webkit-background-clip: text;
}

.text-transparent {
  color: transparent;
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: hsl(var(--background));
}

::-webkit-scrollbar-thumb {
  background: hsl(var(--muted-foreground));
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: hsl(var(--primary));
}
</style>