<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Facebook, Linkedin, Instagram, Send, Github, Mail, ExternalLink, Clock, Settings, MessageSquare, Globe, CheckCircle, AlertCircle } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Badge } from '@/components/ui/badge'
import DockNavigation from '@/components/DockNavigation.vue'
import MusicPlayer from '@/components/MusicPlayer.vue'

const props = defineProps({
    title: String,
    description: String
})

const isVisible          = ref(false)
const showSuccessMessage = ref(false)
const showErrorMessage   = ref(false)
const errorMessage       = ref('')

// Form data using Inertia's useForm
const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: ''
})

// Form submission handler
const submitForm = () => {
    form.post(route('contact.send'), {
        onSuccess: () => {
            showSuccessMessage.value = true
            showErrorMessage.value = false
            form.reset()
            setTimeout(() => {
                showSuccessMessage.value = false
            }, 5000)
        },
        onError: (errors) => {
            showErrorMessage.value = true
            showSuccessMessage.value = false
            errorMessage.value = errors.message || 'Failed to send message. Please try again.'
            setTimeout(() => {
                showErrorMessage.value = false
            }, 5000)
        }
    })
}

const socialLinks = [
    {
        name: 'Facebook',
        icon: Facebook,
        url: 'https://www.facebook.com/samo.thai.73',
        username: 'HE Sovanthai',
        color: '#1877F2'
    },
    {
        name: 'LinkedIn',
        icon: Linkedin,
        url: 'https://www.linkedin.com/in/sros-thai-b491b42ab/',
        username: 'SROS THAI',
        color: '#0A66C2'
    },
    {
        name: 'Instagram',
        icon: Instagram,
        url: 'https://www.instagram.com/sovannthai887',
        username: '@sovannthai887',
        color: '#E4405F'
    },
    {
        name: 'Telegram',
        icon: Send,
        url: 'https://t.me/srosthai2003',
        username: '@srosthai2003',
        color: '#0088CC'
    },
    {
        name: 'GitHub',
        icon: Github,
        url: 'https://github.com/srosthai',
        username: 'Sovannthai',
        color: '#333333'
    },
    {
        name: 'Email',
        icon: Mail,
        url: 'mailto:srosthai00@gmail.com',
        username: 'srosthai00@gmail.com',
        color: '#EA4335'
    }
]

const websites = [
    {
        name: 'Personal Portfolio',
        url: 'http://sovannthai.vercel.app/',
        description: 'My main portfolio website showcasing all projects and skills',
        type: 'portfolio'
    }
]

const openLink = (url) => {
    if (typeof window !== 'undefined') {
        window.open(url, '_blank', 'noopener,noreferrer')
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
        <meta name="keywords" content="contact, get in touch, hire developer, collaboration, software development services, web developer contact, professional developer, freelance developer" />
        <meta name="author" content="SROS THAI - Software Developer" />
        <meta name="language" content="en" />
        <meta name="geo.region" content="KH" />
        <meta name="geo.country" content="Cambodia" />
        <meta name="theme-color" content="#2563eb" />
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:image" content="/contact-og-image.jpg" />
        <meta property="og:url" :content="$page.url" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="Contact - Developer Portfolio" />
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta name="twitter:image" content="/contact-og-image.jpg" />
        
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
          "@type": "ContactPage",
          "name": "Contact SROS THAI - Software Developer",
          "description": "Get in touch with SROS THAI, a professional software developer specializing in web development, Vue.js, Laravel, and modern web technologies.",
          "url": "{{ $page.url }}",
          "mainEntity": {
            "@type": "Person",
            "name": "SROS THAI",
            "jobTitle": "Software Developer",
            "email": "srosthai00@gmail.com",
            "url": "http://sovannthai.vercel.app/",
            "sameAs": [
              "https://www.facebook.com/samo.thai.73",
              "https://www.linkedin.com/in/sros-thai-b491b42ab/",
              "https://www.instagram.com/sovannthai887",
              "https://github.com/srosthai",
              "https://t.me/srosthai2003"
            ],
            "contactPoint": {
              "@type": "ContactPoint",
              "email": "srosthai00@gmail.com",
              "contactType": "professional",
              "availableLanguage": ["English", "Khmer"]
            }
          }
        }
        </script>
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <DockNavigation currentRoute="/contact" />
        
        <!-- Music Player -->
        <MusicPlayer />

        <!-- Contact Hero Section -->
        <section class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto text-center relative">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            </div>
            
            <div class="space-y-6 relative z-10 mt-5" :class="{ 'fade-in-up': isVisible }">
                <div class="space-y-3">
                    <Badge variant="outline" class="w-fit px-4 py-2 bg-primary/10 border-primary/20 text-primary mx-auto">
                        <MessageSquare class="w-3 h-3 mr-2" />
                        Let's Connect
                    </Badge>
                    
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                        <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                            Get In Touch
                        </span>
                    </h1>
                </div>
                
                <p class="text-base lg:text-lg text-muted-foreground leading-relaxed max-w-3xl mx-auto">
                    Let's connect and discuss opportunities, collaborations, or just have a friendly chat about technology.
                </p>
            </div>
        </section>

        <!-- Social Media Section -->
        <section class="py-8 lg:py-12 px-4 max-w-6xl mx-auto">
            <div class="text-center mb-8 lg:mb-12" :class="{ 'fade-in-up': isVisible }">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 lg:mb-4">
                    <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                        Follow Me
                    </span>
                </h2>
                <p class="text-muted-foreground max-w-2xl mx-auto">
                    Connect with me across different platforms and stay updated with my latest work.
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                <Card
                    v-for="(social, index) in socialLinks"
                    :key="index"
                    class="bg-card/50 backdrop-blur-sm border-border/50 hover:bg-card/80 active:bg-card/90 focus:bg-card/80 transition-all duration-300 hover:-translate-y-1 active:-translate-y-2 focus:-translate-y-1 hover:shadow-lg active:shadow-xl focus:shadow-lg hover:shadow-primary/20 active:shadow-primary/30 focus:shadow-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/50 cursor-pointer group overflow-hidden"
                    :class="{ 'fade-in-up': isVisible }"
                    :style="{ animationDelay: `${index * 0.1}s` }"
                    @click="openLink(social.url)"
                    tabindex="0"
                    @keydown.enter="openLink(social.url)"
                    @keydown.space="openLink(social.url)"
                >
                    <CardContent class="flex items-center gap-4 p-4 lg:p-6">
                        <div class="flex items-center justify-center w-12 h-12 lg:w-15 lg:h-15 bg-muted/50 rounded-xl flex-shrink-0 group-hover:scale-105 group-active:scale-110 group-focus:scale-105 transition-transform">
                            <component :is="social.icon" class="w-6 h-6 lg:w-8 lg:h-8 text-foreground" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base lg:text-lg font-semibold text-foreground mb-1">{{ social.name }}</h3>
                            <p class="text-muted-foreground text-sm">{{ social.username }}</p>
                        </div>
                        <div class="text-muted-foreground group-hover:text-foreground group-active:text-foreground group-focus:text-foreground group-hover:translate-x-1 group-active:translate-x-2 group-focus:translate-x-1 transition-all">→</div>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- Websites Section -->
        <section class="py-8 lg:py-12 px-4 max-w-6xl mx-auto" hidden>
            <div class="text-center mb-8 lg:mb-12" :class="{ 'fade-in-up': isVisible }">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 lg:mb-4">
                    <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                        My Websites
                    </span>
                </h2>
                <p class="text-muted-foreground max-w-2xl mx-auto">
                    Explore my online presence and discover the projects I've built.
                </p>
            </div>
            <div class="grid grid-cols-1 gap-4 lg:gap-6">
                <Card
                    v-for="(website, index) in websites"
                    :key="index"
                    class="bg-card/50 backdrop-blur-sm border-border/50 hover:bg-card/80 active:bg-card/90 focus:bg-card/80 transition-all duration-300 hover:-translate-y-1 active:-translate-y-2 focus:-translate-y-1 hover:shadow-xl active:shadow-2xl focus:shadow-xl hover:shadow-primary/20 active:shadow-primary/30 focus:shadow-primary/20 focus:outline-none focus:ring-2 focus:ring-primary/50 cursor-pointer group"
                    :class="{ 'fade-in-up': isVisible }"
                    :style="{ animationDelay: `${index * 0.2}s` }"
                    @click="openLink(website.url)"
                    tabindex="0"
                    @keydown.enter="openLink(website.url)"
                    @keydown.space="openLink(website.url)"
                >
                    <CardContent class="flex gap-4 lg:gap-5 p-4 lg:p-6">
                        <div class="flex items-center justify-center w-12 h-12 lg:w-15 lg:h-15 bg-primary/10 rounded-xl flex-shrink-0 group-hover:bg-primary/20 group-active:bg-primary/30 group-focus:bg-primary/20 transition-colors">
                            <component :is="website.type === 'portfolio' ? ExternalLink : website.type === 'blog' ? Clock : Settings" class="w-6 h-6 lg:w-8 lg:h-8 text-primary" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg lg:text-xl font-semibold text-foreground mb-2">{{ website.name }}</h3>
                            <p class="text-sm lg:text-base text-muted-foreground leading-relaxed mb-3">{{ website.description }}</p>
                            <Badge variant="outline" class="bg-primary/5 text-primary border-primary/20">
                                <Globe class="w-3 h-3 mr-1" />
                                {{ website.url }}
                            </Badge>
                        </div>
                        <div class="text-muted-foreground group-hover:text-foreground group-active:text-foreground group-focus:text-foreground group-hover:translate-x-1 group-active:translate-x-2 group-focus:translate-x-1 group-hover:-translate-y-1 group-active:-translate-y-2 group-focus:-translate-y-1 transition-all self-start">↗</div>
                    </CardContent>
                </Card>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="py-8 lg:py-12 px-4 max-w-6xl mx-auto">
            <div class="text-center mb-8 lg:mb-12" :class="{ 'fade-in-up': isVisible }">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 lg:mb-4">
                    <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                        Send a Message
                    </span>
                </h2>
                <p class="text-muted-foreground max-w-2xl mx-auto">
                    Have a question or want to work together? I'd love to hear from you.
                </p>
            </div>
            <div class="max-w-2xl mx-auto" :class="{ 'fade-in-up': isVisible }">
                <!-- Success Message -->
                <div v-if="showSuccessMessage" class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg flex items-center text-green-800">
                    <CheckCircle class="w-5 h-5 mr-3 flex-shrink-0" />
                    <span>Message sent successfully! I'll get back to you soon.</span>
                </div>

                <!-- Error Message -->
                <div v-if="showErrorMessage" class="mb-6 p-4 bg-red-100 border border-red-300 rounded-lg flex items-center text-red-800">
                    <AlertCircle class="w-5 h-5 mr-3 flex-shrink-0" />
                    <span>{{ errorMessage }}</span>
                </div>

                <Card class="bg-card/50 backdrop-blur-sm border-border/50">
                    <CardContent class="p-6 lg:p-8">
                        <form @submit.prevent="submitForm" class="space-y-4 lg:space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                                <div class="space-y-2">
                                    <Label for="name" class="text-foreground font-medium">Full Name</Label>
                                    <Input 
                                        id="name" 
                                        v-model="form.name"
                                        type="text" 
                                        required
                                        :class="['bg-input border-border text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/20', form.errors.name ? 'border-red-500' : '']"
                                        placeholder="Enter your full name"
                                    />
                                    <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                                </div>
                                <div class="space-y-2">
                                    <Label for="email" class="text-foreground font-medium">Email Address</Label>
                                    <Input 
                                        id="email" 
                                        v-model="form.email"
                                        type="email" 
                                        required
                                        :class="['bg-input border-border text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/20', form.errors.email ? 'border-red-500' : '']"
                                        placeholder="Enter your email address"
                                    />
                                    <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="subject" class="text-foreground font-medium">Subject</Label>
                                <Input 
                                    id="subject" 
                                    v-model="form.subject"
                                    type="text" 
                                    required
                                    :class="['bg-input border-border text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/20', form.errors.subject ? 'border-red-500' : '']"
                                    placeholder="Enter the subject"
                                />
                                <span v-if="form.errors.subject" class="text-red-500 text-sm">{{ form.errors.subject }}</span>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="message" class="text-foreground font-medium">Message</Label>
                                <Textarea 
                                    id="message" 
                                    v-model="form.message"
                                    rows="6" 
                                    required
                                    :class="['bg-input border-border text-foreground placeholder:text-muted-foreground focus:border-ring focus:ring-ring/20 resize-y', form.errors.message ? 'border-red-500' : '']"
                                    placeholder="Enter your message"
                                />
                                <span v-if="form.errors.message" class="text-red-500 text-sm">{{ form.errors.message }}</span>
                            </div>
                            
                            <Button 
                                type="submit" 
                                :disabled="form.processing"
                                class="w-full bg-primary hover:bg-primary/90 active:bg-primary/80 focus:bg-primary/90 text-primary-foreground font-semibold py-3 lg:py-4 transition-all duration-300 hover:-translate-y-1 active:-translate-y-2 focus:-translate-y-1 hover:shadow-lg active:shadow-xl focus:shadow-lg hover:shadow-primary/40 active:shadow-primary/50 focus:shadow-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 focus:ring-offset-background disabled:opacity-50 disabled:cursor-not-allowed"
                                size="lg"
                            >
                                <span v-if="form.processing">Sending...</span>
                                <span v-else>Send Message</span>
                                <Send class="ml-2 w-5 h-5" />
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </section>
    </div>
</template>

<style scoped>
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
</style>