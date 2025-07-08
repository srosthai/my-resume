<script setup>
import { ref, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Construction, Wrench, Clock } from 'lucide-vue-next'
import DockNavigation from '@/components/DockNavigation.vue'

const props = defineProps({
    title: String,
    description: String
})

const isVisible = ref(false)

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
        <DockNavigation currentRoute="/more" />

        <!-- More Content Section -->
        <section class="min-h-screen flex items-center justify-center px-4 max-w-6xl mx-auto relative">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            </div>
            
            <div class="text-center max-w-4xl space-y-8 lg:space-y-12 relative z-10">
                <div class="space-y-4 lg:space-y-6" :class="{ 'fade-in-up': isVisible }">
                    <Badge variant="outline" class="w-fit px-4 py-2 bg-primary/10 border-primary/20 text-primary mx-auto">
                        <Clock class="w-3 h-3 mr-2" />
                        Coming Soon
                    </Badge>
                    
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                        <span class="bg-gradient-to-br from-foreground via-foreground/90 to-foreground/70 bg-clip-text text-transparent">
                            More Features
                        </span>
                    </h1>
                    
                    <p class="text-base lg:text-lg text-muted-foreground leading-relaxed">
                        This section is coming soon. Stay tuned for more exciting content and features!
                    </p>
                </div>
                
                <!-- Coming Soon Cards Grid -->
                <div class="grid grid-cols-1 gap-4 lg:gap-6" :class="{ 'fade-in-up': isVisible }">
                    <!-- Main Coming Soon Card -->
                    <div>
                        <Card class="bg-card/50 backdrop-blur-sm border border-border/50 hover:bg-card/80 active:bg-card/80 focus:bg-card/80 transition-all duration-300 hover:-translate-y-1 active:-translate-y-1 focus:-translate-y-1 hover:shadow-xl active:shadow-xl focus:shadow-xl hover:shadow-primary/10 active:shadow-primary/10 focus:shadow-primary/10 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background">
                            <CardContent class="p-6 lg:p-8 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 lg:w-20 lg:h-20 bg-primary/10 rounded-full mb-6 lg:mb-8">
                                    <Construction class="w-8 h-8 lg:w-10 lg:h-10 text-primary" />
                                </div>
                                
                                <CardTitle class="text-2xl lg:text-3xl font-bold mb-3 lg:mb-4 text-foreground">
                                    Under Construction
                                </CardTitle>
                                
                                <CardDescription class="text-base lg:text-lg text-muted-foreground leading-relaxed mb-6 lg:mb-8">
                                    We're working hard to bring you amazing new features. Check back soon!
                                </CardDescription>
                                
                                <div class="flex items-center justify-center gap-4 lg:gap-6 text-sm text-muted-foreground">
                                    <div class="flex items-center gap-2">
                                        <Wrench class="w-4 h-4" />
                                        <span>In Development</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Clock class="w-4 h-4" />
                                        <span>Coming @2025</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
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
</style>