<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Heart, Gamepad2, Palette, Music, Book, Code2, Coffee, Dumbbell, Camera, Plane, Terminal } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'

const props = defineProps({
    title: String,
    description: String
})

const hobbies = [
    {
        icon: Code2,
        title: 'Open Source Contributing',
        description: 'Contributing to open source projects on GitHub and building personal projects to explore new technologies like Vue.js, Laravel, and modern web frameworks.',
        color: 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
        category: 'Development'
    },
    {
        icon: Terminal,
        title: 'Learning New Technologies',
        description: 'Constantly exploring emerging technologies, experimenting with new programming languages, and staying updated with industry trends.',
        color: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
        category: 'Learning'
    },
    {
        icon: Book,
        title: 'Technical Reading',
        description: 'Reading programming books, technical documentation, tech blogs, and following industry leaders to improve coding skills and knowledge.',
        color: 'bg-green-500/10 text-green-600 dark:text-green-400',
        category: 'Learning'
    },
    {
        icon: Palette,
        title: 'UI/UX Design',
        description: 'Creating beautiful and functional user interfaces using design tools like Figma, exploring modern design trends and user experience principles.',
        color: 'bg-pink-500/10 text-pink-600 dark:text-pink-400',
        category: 'Creative'
    },
    {
        icon: Coffee,
        title: 'Coffee Brewing',
        description: 'Perfecting the art of coffee brewing with different methods - from pour-over to espresso. A perfect companion for long coding sessions.',
        color: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
        category: 'Lifestyle'
    },
    {
        icon: Gamepad2,
        title: 'Strategy Gaming',
        description: 'Playing strategy and puzzle games that enhance problem-solving skills and logical thinking - skills that translate well to programming.',
        color: 'bg-purple-500/10 text-purple-600 dark:text-purple-400',
        category: 'Entertainment'
    },
    {
        icon: Dumbbell,
        title: 'Fitness & Health',
        description: 'Maintaining physical health through regular exercise and gym sessions. A healthy body supports a healthy mind for better coding performance.',
        color: 'bg-red-500/10 text-red-600 dark:text-red-400',
        category: 'Health'
    },
    {
        icon: Camera,
        title: 'Photography',
        description: 'Capturing moments and exploring creative composition. Photography helps develop an eye for visual design and attention to detail.',
        color: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
        category: 'Creative'
    },
    {
        icon: Music,
        title: 'Music Production',
        description: 'Creating and mixing music using digital audio workstations. Music theory and audio engineering complement the technical mindset.',
        color: 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
        category: 'Creative'
    },
    {
        icon: Plane,
        title: 'Travel & Culture',
        description: 'Exploring different cultures and places, gaining new perspectives that inspire creativity and problem-solving approaches in development.',
        color: 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400',
        category: 'Lifestyle'
    }
]

const isVisible = ref(false)
const selectedCategory = ref('All')
const filteredHobbies = ref(hobbies)

const categories = ['All', 'Development', 'Learning', 'Creative', 'Lifestyle', 'Entertainment', 'Health']

const filterHobbies = (category) => {
    selectedCategory.value = category
    if (category === 'All') {
        filteredHobbies.value = hobbies
    } else {
        filteredHobbies.value = hobbies.filter(hobby => hobby.category === category)
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

    <div class="min-h-screen bg-gradient-to-br from-background via-background/95 to-background text-foreground font-sans overflow-x-hidden transition-all duration-300 pt-16">
        <DockNavigation currentRoute="/hobby" />

        <!-- Hobbies Hero Section -->
        <section class="pt-6 sm:pt-8 pb-8 px-4 max-w-6xl mx-auto relative">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            </div>
            
            <div class="text-center mb-8 lg:mb-12 relative z-10">
                <div class="space-y-4 lg:space-y-6" :class="{ 'fade-in-up': isVisible }">
                    <Badge variant="outline" class="w-fit px-4 py-2 bg-primary/10 border-primary/20 text-primary mx-auto">
                        <Heart class="w-3 h-3 mr-2" />
                        Personal Interests
                    </Badge>
                    
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                        <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                            Hobbies & Interests
                        </span>
                    </h1>
                    
                    <p class="text-base lg:text-lg text-muted-foreground leading-relaxed max-w-3xl mx-auto">
                        Beyond coding, I enjoy exploring various activities that inspire creativity, problem-solving, and continuous learning. Each hobby contributes to my growth as a developer.
                    </p>
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="flex justify-center flex-wrap gap-2 lg:gap-3 mb-8 relative z-10" :class="{ 'fade-in-up': isVisible }">
                <button
                    v-for="category in categories"
                    :key="category"
                    @click="filterHobbies(category)"
                    :class="[
                        'px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200 border',
                        selectedCategory === category
                            ? 'bg-primary text-primary-foreground border-primary shadow-lg'
                            : 'bg-background/50 backdrop-blur-sm text-muted-foreground border-border/50 hover:bg-accent hover:text-foreground active:bg-accent active:text-foreground focus:bg-accent focus:text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20'
                    ]"
                >
                    {{ category }}
                </button>
            </div>
            
            <!-- Stats -->
            <div class="text-center mb-6 relative z-10" :class="{ 'fade-in-up': isVisible }">
                <p class="text-sm text-muted-foreground">
                    Showing {{ filteredHobbies.length }} of {{ hobbies.length }} {{ filteredHobbies.length === 1 ? 'hobby' : 'hobbies' }}
                    <span v-if="selectedCategory !== 'All'"> in {{ selectedCategory }}</span>
                </p>
            </div>
            
            <!-- Hobbies Grid -->
            <div v-if="filteredHobbies.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6 relative z-10">
                <Card
                    v-for="(hobby, index) in filteredHobbies"
                    :key="hobby.title"
                    class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 active:bg-card/80 focus:bg-card/80 transition-all duration-300 hover:-translate-y-2 active:-translate-y-2 focus:-translate-y-2 hover:shadow-xl hover:shadow-primary/10 active:shadow-xl active:shadow-primary/10 focus:shadow-xl focus:shadow-primary/10 group transform focus:outline-none focus:ring-2 focus:ring-primary/20"
                    :class="{ 'fade-in-up': isVisible }"
                    :style="{ animationDelay: `${index * 0.1}s` }"
                    tabindex="0"
                >
                    <CardContent class="p-4 lg:p-6">
                        <div class="flex flex-col items-center text-center space-y-4 lg:space-y-6">
                            <div class="p-3 lg:p-4 rounded-full transition-all duration-300 group-hover:scale-110 group-active:scale-110 group-focus:scale-110" :class="hobby.color">
                                <component :is="hobby.icon" class="w-6 h-6 lg:w-8 lg:h-8" />
                            </div>
                            
                            <div class="space-y-2 lg:space-y-3">
                                <div class="flex flex-col items-center gap-2">
                                    <h3 class="text-lg lg:text-xl font-bold text-foreground group-hover:text-primary group-active:text-primary group-focus:text-primary transition-colors duration-300">
                                        {{ hobby.title }}
                                    </h3>
                                    <Badge variant="outline" class="text-xs px-2 py-1 bg-accent/20 border-accent text-accent-foreground">
                                        {{ hobby.category }}
                                    </Badge>
                                </div>
                                
                                <p class="text-sm lg:text-base text-muted-foreground leading-relaxed group-hover:text-foreground/80 group-active:text-foreground/80 group-focus:text-foreground/80 transition-colors duration-300">
                                    {{ hobby.description }}
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Empty State -->
            <div v-else class="text-center py-12 lg:py-16 relative z-10" :class="{ 'fade-in-up': isVisible }">
                <div class="space-y-4">
                    <div class="w-16 h-16 mx-auto bg-muted/50 rounded-full flex items-center justify-center">
                        <Heart class="w-8 h-8 text-muted-foreground" />
                    </div>
                    <h3 class="text-xl font-semibold text-foreground">No hobbies found</h3>
                    <p class="text-muted-foreground">
                        No hobbies match the selected category. Try selecting a different category.
                    </p>
                    <button
                        @click="filterHobbies('All')"
                        class="mt-4 px-4 py-2 bg-primary text-primary-foreground rounded-full text-sm font-medium hover:bg-primary/90 active:bg-primary/80 focus:bg-primary/90 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                        Show All Hobbies
                    </button>
                </div>
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

/* Custom transitions for filtering */
.card-enter-active {
    transition: all 0.3s ease-out;
}

.card-leave-active {
    transition: all 0.3s ease-in;
}

.card-enter-from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}

.card-leave-to {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
}
</style>
