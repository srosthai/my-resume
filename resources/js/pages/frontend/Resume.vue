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
    <meta name="keywords" content="resume, CV, professional experience, software developer, skills, work history" />
    <meta name="author" :content="users.name || 'Software Developer'" />
    
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
    <meta name="robots" content="index, follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="canonical" :href="$page.url" />
  </Head>

  <div class="min-h-screen bg-background text-foreground">
    <!-- Dock Navigation -->
    <DockNavigation currentRoute="/resume" />

    <!-- Music Player -->
    <MusicPlayer />

    <!-- Header/Hero Section -->
    <section class="py-12 lg:py-20 px-4 max-w-4xl mx-auto" :class="{ 'fade-in-up': isVisible }">
      <div class="flex flex-col lg:flex-row gap-8 items-center lg:items-start mt-10">
        <!-- Profile Picture -->
        <div class="flex-shrink-0">
          <Avatar class="w-32 h-32 md:w-40 md:h-40 border-4 border-primary/20">
            <AvatarImage v-if="users.image" :src="users.image.startsWith('http') ? users.image : `/${users.image}`"
              :alt="users.name" class="object-cover" />
            <AvatarFallback class="bg-primary/10 text-primary text-3xl md:text-4xl font-semibold">
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
            <p class="text-lg md:text-xl text-primary font-medium mb-3">
              {{ users.position || 'Software Developer' }}
            </p>
            <p class="text-muted-foreground leading-relaxed max-w-2xl">
              {{ users.description || 'A passionate developer with experience in creating modern web applications and solving complex problems through code.' }}
            </p>
          </div>

          <!-- Social Links -->
          <div class="flex items-center justify-center lg:justify-start gap-3 pt-4">
            <Button asChild size="sm" variant="outline">
              <a href="mailto:srosthai00@gmail.com" class="flex items-center gap-2">
                <Mail class="h-4 w-4" />
                Email
              </a>
            </Button>
            <Button asChild size="sm" variant="outline">
              <a href="https://github.com/Sovannthai" target="_blank" class="flex items-center gap-2">
                <Github class="h-4 w-4" />
                GitHub
              </a>
            </Button>
            <Button asChild size="sm" variant="outline">
              <a href="https://www.linkedin.com/in/sros-thai-b491b42ab/" target="_blank"
                class="flex items-center gap-2">
                <Linkedin class="h-4 w-4" />
                LinkedIn
              </a>
            </Button>
            <Button asChild size="sm" variant="outline">
              <Link href="/note" class="flex items-center gap-2">
              <Book class="h-4 w-4" />
              Notes
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 pb-12 space-y-12">
      <!-- Who I Am Section -->
      <section :class="{ 'fade-in-up': isVisible }">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <User class="h-5 w-5 text-primary" />
              Who I Am
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <p v-for="(statement, index) in whoIAmData" :key="index" class="text-muted-foreground">
                {{ statement }}
              </p>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Skills Section -->
      <section :class="{ 'fade-in-up': isVisible }">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Code class="h-5 w-5 text-primary" />
              Skills
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-6">
            <!-- Development Skills -->
            <div>
              <h3 class="font-semibold mb-3 flex items-center gap-2">
                <Code class="h-4 w-4" />
                Development
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="skill in skillsData.development" :key="skill.name" variant="secondary" class="px-3 py-1">
                  {{ skill.name }}
                </Badge>
              </div>
            </div>

            <!-- Tools -->
            <div>
              <h3 class="font-semibold mb-3 flex items-center gap-2">
                <Wrench class="h-4 w-4" />
                Tools
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="tool in skillsData.tools" :key="tool.name" variant="secondary" class="px-3 py-1">
                  {{ tool.name }}
                </Badge>
              </div>
            </div>

            <!-- Soft Skills -->
            <div>
              <h3 class="font-semibold mb-3 flex items-center gap-2">
                <Brain class="h-4 w-4" />
                Soft Skills
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="skill in skillsData.soft" :key="skill.name" variant="secondary" class="px-3 py-1">
                  {{ skill.name }}
                </Badge>
              </div>
            </div>

            <!-- Languages -->
            <div>
              <h3 class="font-semibold mb-3 flex items-center gap-2">
                <Languages class="h-4 w-4" />
                Languages
              </h3>
              <div class="flex flex-wrap gap-2">
                <Badge v-for="lang in languages" :key="lang.name" variant="outline" class="px-3 py-1">
                  {{ lang.name }} ({{ lang.level }})
                </Badge>
              </div>
            </div>
          </CardContent>
        </Card>
      </section>

      <!-- Work Experience Section -->
      <section :class="{ 'fade-in-up': isVisible }">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Briefcase class="h-5 w-5 text-primary" />
              Work Experience
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-6">
              <div v-for="(work, index) in workExperience" :key="index" class="relative">
                <div class="flex flex-col md:flex-row gap-4">
                  <div class="md:w-1/3">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground mb-1">
                      <Calendar class="h-4 w-4" />
                      {{ work.from }} - {{ work.to || 'Present' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                      <MapPin class="h-4 w-4" />
                      {{ work.company }}
                    </div>
                  </div>
                  <div class="md:w-2/3">
                    <h3 class="font-semibold text-lg mb-2">{{ work.position }}</h3>
                    <p class="text-muted-foreground mb-3">{{ work.description }}</p>
                    <div v-if="work.responsibilities" class="space-y-1">
                      <p class="text-sm text-muted-foreground">Key Responsibilities:</p>
                      <ul class="text-sm space-y-1 ml-4">
                        <li v-for="responsibility in work.responsibilities.split('\n')" :key="responsibility"
                          class="list-disc">
                          {{ responsibility }}
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <Separator v-if="index < workExperience.length - 1" class="mt-6" />
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
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <GraduationCap class="h-5 w-5 text-primary" />
              Education
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div v-for="(edu, index) in education" :key="index" class="space-y-2">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                  <div>
                    <h3 class="font-semibold">{{ edu.degree }}</h3>
                    <p class="text-muted-foreground">{{ edu.institution }}</p>
                  </div>
                  <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Calendar class="h-4 w-4" />
                    {{ edu.from }} - {{ edu.to }}
                  </div>
                </div>
                <p v-if="edu.description" class="text-sm text-muted-foreground">{{ edu.description }}</p>
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
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <Target class="h-5 w-5 text-primary" />
              Projects
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="grid gap-4 md:grid-cols-2">
              <div v-for="(project, index) in projects.slice(0, 4)" :key="index"
                class="border rounded-lg p-4 space-y-2">
                <div class="flex justify-between items-start">
                  <h3 class="font-semibold">{{ project.title }}</h3>
                  <Button v-if="project.link" asChild size="sm" variant="ghost">
                    <a :href="project.link" target="_blank" class="h-8 w-8 p-0">
                      <ExternalLink class="h-4 w-4" />
                    </a>
                  </Button>
                </div>
                <p class="text-sm text-muted-foreground">{{ project.description }}</p>
                <div v-if="project.tech_stack" class="flex flex-wrap gap-1">
                  <Badge v-for="tech in project.tech_stack.split(',')" :key="tech" variant="secondary" class="text-xs">
                    {{ tech.trim() }}
                  </Badge>
                </div>
              </div>
            </div>

            <!-- Show more projects link -->
            <div v-if="projects.length > 1" class="text-center mt-6">
              <Button asChild variant="outline">
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
      <footer class="text-center py-8 border-t">
        <p class="text-sm text-muted-foreground">
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

.fade-in-up {
  animation: fadeInUp 0.8s ease-out forwards;
}
</style>