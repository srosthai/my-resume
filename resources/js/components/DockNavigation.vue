<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { 
  Home, 
  User, 
  Briefcase, 
  Mail, 
  Heart, 
  MoreHorizontal,
  Github,
  Linkedin,
  Phone,
  Command,
  Send
} from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'
import { Dock, DockIcon } from '@/components/magicui'
import ThemeToggle from '@/components/ThemeToggle.vue'
import { cn } from '@/lib/utils'

interface Props {
  currentRoute?: string
}

const props = withDefaults(defineProps<Props>(), {
  currentRoute: '/'
})

const navigationItems = [
  { href: '/', icon: Home, label: 'Home' },
  { href: '/about', icon: User, label: 'About' },
  { href: '/portfolio', icon: Briefcase, label: 'Projects' },
  { href: '/contact', icon: Phone, label: 'Contact' },
  { href: '/hobby', icon: Command, label: 'Hobbies' },
  { href: '/more', icon: MoreHorizontal, label: 'More' },
]

const socialLinks = [
  {
    name: 'GitHub',
    url: 'https://github.com/Sovannthai',
    icon: Github,
  },
  {
    name: 'LinkedIn',
    url: 'https://www.linkedin.com/in/sros-thai-b491b42ab/',
    icon: Linkedin,
  },
  {
    name: 'Telegram',
    url: 'https://t.me/HE_Sovanthai',
    icon: Send,
  },
]

const isActive = (href: string) => {
  return props.currentRoute === href
}
</script>

<template>
  <div class="fixed top-3 sm:top-6 left-1/2 transform -translate-x-1/2 z-50 max-w-[calc(100vw-1rem)] sm:max-w-[calc(100vw-2rem)] w-auto overflow-x-auto dock-container">
    <TooltipProvider>
      <Dock direction="middle" class="bg-background/80 backdrop-blur-xl border-border/30 shadow-xl px-1 md:px-2">
        <!-- Navigation Items -->
        <DockIcon 
          v-for="item in navigationItems" 
          :key="item.label"
          class="h-10 w-10 sm:h-12 sm:w-12 border-0"
        >
          <Tooltip>
            <TooltipTrigger asChild>
              <Button
                asChild
                :variant="isActive(item.href) ? 'default' : 'ghost'"
                size="icon"
                :class="cn(
                  'h-8 w-8 sm:h-10 sm:w-10 rounded-full transition-all duration-200',
                  isActive(item.href) 
                    ? 'bg-primary text-primary-foreground shadow-lg scale-110' 
                    : 'hover:bg-accent hover:scale-105 active:bg-accent active:scale-102 focus:bg-accent focus:scale-105 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background'
                )"
              >
                <Link :href="item.href" :aria-label="item.label">
                  <component :is="item.icon" class="h-3 w-3 sm:h-4 sm:w-4" />
                </Link>
              </Button>
            </TooltipTrigger>
            <TooltipContent>
              <p>{{ item.label }}</p>
            </TooltipContent>
          </Tooltip>
        </DockIcon>

        <!-- Separator -->
        <Separator orientation="vertical" class="h-6 sm:h-8 mx-0.5 sm:mx-1 hidden sm:block" />

        <!-- Social Links -->
        <DockIcon 
          v-for="social in socialLinks" 
          :key="social.name"
          class="h-10 w-10 sm:h-12 sm:w-12 border-0 hidden sm:block"
        >
          <Tooltip>
            <TooltipTrigger asChild>
              <Button
                asChild
                variant="ghost"
                size="icon"
                class="h-8 w-8 sm:h-10 sm:w-10 rounded-full hover:bg-accent hover:scale-105 active:bg-accent active:scale-102 focus:bg-accent focus:scale-105 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background transition-all duration-200"
              >
                <a 
                  :href="social.url" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  :aria-label="social.name"
                >
                  <component :is="social.icon" class="h-3 w-3 sm:h-4 sm:w-4" />
                </a>
              </Button>
            </TooltipTrigger>
            <TooltipContent>
              <p>{{ social.name }}</p>
            </TooltipContent>
          </Tooltip>
        </DockIcon>

        <!-- Separator -->
        <Separator orientation="vertical" class="h-6 sm:h-8 mx-0.5 sm:mx-1 hidden sm:block" />

        <!-- Theme Toggle -->
        <DockIcon class="h-10 w-10 sm:h-12 sm:w-12 border-0">
          <Tooltip>
            <TooltipTrigger asChild>
              <ThemeToggle class="h-8 w-8 sm:h-10 sm:w-10 rounded-full hover:scale-105 active:scale-102 focus:scale-105 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:ring-offset-2 focus:ring-offset-background transition-all duration-200" />
            </TooltipTrigger>
            <TooltipContent>
              <p>Toggle Theme</p>
            </TooltipContent>
          </Tooltip>
        </DockIcon>
      </Dock>
    </TooltipProvider>
  </div>
</template>