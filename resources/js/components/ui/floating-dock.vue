<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'
import { Separator } from '@/components/ui/separator'
import { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip'
import { Dock, DockIcon } from '@/components/ui/dock'
import { Link } from '@inertiajs/vue3'
import ThemeToggle from '@/components/ui/ThemeToggle.vue'

interface DockItem {
  title: string
  icon: any
  href: string
  isExternal?: boolean
}

interface Props {
  items: DockItem[]
  className?: string
  mobileClassName?: string
  direction?: 'top' | 'middle' | 'bottom'
  showThemeToggle?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  className: '',
  mobileClassName: '',
  direction: 'middle',
  showThemeToggle: true
})

// Split items into navigation and social links
const navItems = computed(() => props.items.slice(0, 5))
const socialItems = computed(() => props.items.slice(5))
</script>

<template>
  <div class="flex flex-col items-center justify-center">
    <TooltipProvider>
      <!-- Desktop Dock -->
      <Dock :direction="direction" :className="className" class="hidden md:flex">
        <!-- Navigation Items -->
        <DockIcon v-for="item in navItems" :key="item.title">
          <Tooltip>
            <TooltipTrigger as-child>
              <!-- Internal Link -->
              <Link
                v-if="!item.isExternal && !item.href.startsWith('http')"
                :href="item.href"
                :aria-label="item.title"
                :class="cn(
                  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all',
                  'disabled:pointer-events-none disabled:opacity-50',
                  'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50',
                  'size-10 sm:size-11 md:size-12 hover:scale-110 transition-transform duration-200',
                  'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring'
                )"
              >
                <component :is="item.icon" class="size-3 sm:size-3.5 md:size-4" />
              </Link>
              <!-- External Link -->
              <a
                v-else
                :href="item.href"
                :aria-label="item.title"
                :target="item.isExternal || item.href.startsWith('http') ? '_blank' : undefined"
                :rel="item.isExternal || item.href.startsWith('http') ? 'noopener noreferrer' : undefined"
                :class="cn(
                  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all',
                  'disabled:pointer-events-none disabled:opacity-50',
                  'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50',
                  'size-10 sm:size-11 md:size-12 hover:scale-110 transition-transform duration-200',
                  'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring'
                )"
              >
                <component :is="item.icon" class="size-3 sm:size-3.5 md:size-4" />
              </a>
            </TooltipTrigger>
            <TooltipContent>
              <p>{{ item.title }}</p>
            </TooltipContent>
          </Tooltip>
        </DockIcon>
        
        <!-- Separator -->
        <Separator v-if="socialItems.length > 0" orientation="vertical" class="h-full" />
        
        <!-- Social Items -->
        <DockIcon v-for="item in socialItems" :key="item.title">
          <Tooltip>
            <TooltipTrigger as-child>
              <!-- Internal Link -->
              <Link
                v-if="!item.isExternal && !item.href.startsWith('http')"
                :href="item.href"
                :aria-label="item.title"
                :class="cn(
                  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all',
                  'disabled:pointer-events-none disabled:opacity-50',
                  'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50',
                  'size-10 sm:size-11 md:size-12 hover:scale-110 transition-transform duration-200',
                  'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring'
                )"
              >
                <component :is="item.icon" class="size-3 sm:size-3.5 md:size-4" />
              </Link>
              <!-- External Link -->
              <a
                v-else
                :href="item.href"
                :aria-label="item.title"
                :target="item.isExternal || item.href.startsWith('http') ? '_blank' : undefined"
                :rel="item.isExternal || item.href.startsWith('http') ? 'noopener noreferrer' : undefined"
                :class="cn(
                  'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all',
                  'disabled:pointer-events-none disabled:opacity-50',
                  'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50',
                  'size-10 sm:size-11 md:size-12 hover:scale-110 transition-transform duration-200',
                  'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring'
                )"
              >
                <component :is="item.icon" class="size-3 sm:size-3.5 md:size-4" />
              </a>
            </TooltipTrigger>
            <TooltipContent>
              <p>{{ item.title }}</p>
            </TooltipContent>
          </Tooltip>
        </DockIcon>

        <!-- Theme Toggle -->
        <Separator v-if="showThemeToggle" orientation="vertical" class="h-full" />
        <DockIcon v-if="showThemeToggle">
          <Tooltip>
            <TooltipTrigger as-child>
              <div class="flex items-center justify-center size-10 sm:size-11 md:size-12 hover:scale-110 transition-transform duration-200">
                <ThemeToggle class="size-3 sm:size-3.5 md:size-4" />
              </div>
            </TooltipTrigger>
            <TooltipContent>
              <p>Toggle Theme</p>
            </TooltipContent>
          </Tooltip>
        </DockIcon>
      </Dock>

      <!-- Mobile Dock -->
      <Dock :className="cn('md:hidden gap-1', mobileClassName)">
        <DockIcon v-for="item in items.slice(0, 5)" :key="item.title">
          <!-- Internal Link -->
          <Link
            v-if="!item.isExternal && !item.href.startsWith('http')"
            :href="item.href"
            :aria-label="item.title"
            :class="cn(
              'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all',
              'disabled:pointer-events-none disabled:opacity-50',
              'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50',
              'size-8 sm:size-9 hover:scale-105 transition-transform duration-200',
              'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring'
            )"
          >
            <component :is="item.icon" class="size-3 sm:size-3.5" />
          </Link>
          <!-- External Link -->
          <a
            v-else
            :href="item.href"
            :aria-label="item.title"
            :target="item.isExternal || item.href.startsWith('http') ? '_blank' : undefined"
            :rel="item.isExternal || item.href.startsWith('http') ? 'noopener noreferrer' : undefined"
            :class="cn(
              'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full text-sm font-medium transition-all',
              'disabled:pointer-events-none disabled:opacity-50',
              'hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50',
              'size-8 sm:size-9 hover:scale-105 transition-transform duration-200',
              'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring'
            )"
          >
            <component :is="item.icon" class="size-3 sm:size-3.5" />
          </a>
        </DockIcon>
        
        <!-- Mobile Theme Toggle -->
        <DockIcon v-if="showThemeToggle">
          <div class="flex items-center justify-center size-8 sm:size-9 hover:scale-105 transition-transform duration-200">
            <ThemeToggle class="size-3 sm:size-3.5" />
          </div>
        </DockIcon>
      </Dock>
    </TooltipProvider>
  </div>
</template>