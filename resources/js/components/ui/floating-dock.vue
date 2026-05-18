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
  isTheme?: boolean
}

interface Props {
  items: DockItem[]
  currentRoute?: string
  className?: string
  mobileClassName?: string
  direction?: 'top' | 'middle' | 'bottom'
  showThemeToggle?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  currentRoute: '/',
  className: '',
  mobileClassName: '',
  direction: 'middle',
  showThemeToggle: true
})

// Split items into navigation and social links
const navItems = computed(() => props.items.slice(0, 5))
const socialItems = computed(() => props.items.slice(5))

const isActive = (href: string) => {
  if (href.startsWith('#')) return false
  const current = props.currentRoute || '/'
  if (href === '/') return current === '/'
  return current === href || current.startsWith(`${href}/`)
}

const navLinkClasses = (href: string, mobile = false) => {
  const active = isActive(href)

  if (mobile) {
    return cn(
      'group inline-flex h-11 items-center justify-center gap-2 rounded-full text-sm font-semibold transition-all duration-300',
      'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70',
      active
        ? 'min-w-[7.25rem] bg-white/14 px-4 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.12),0_10px_28px_-18px_rgba(255,255,255,0.75)]'
        : 'w-11 text-white/55 hover:bg-white/8 hover:text-white'
    )
  }

  return cn(
    'group inline-flex h-11 items-center justify-center gap-2 rounded-full text-sm font-semibold transition-all duration-300',
    'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring',
    active
      ? 'bg-foreground px-4 text-background shadow-[0_14px_34px_-22px_rgba(0,0,0,0.75)]'
      : 'w-11 text-muted-foreground hover:bg-accent hover:text-accent-foreground'
  )
}
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
              <ThemeToggle
                v-if="item.isTheme"
                class="size-4 shrink-0 text-current"
                :button-class="navLinkClasses(item.href)"
              />
              <!-- Internal Link -->
              <Link
                v-else-if="!item.isExternal && !item.href.startsWith('http')"
                :href="item.href"
                :aria-label="item.title"
                :aria-current="isActive(item.href) ? 'page' : undefined"
                :class="navLinkClasses(item.href)"
              >
                <component :is="item.icon" class="size-4 shrink-0" />
                <span v-if="isActive(item.href)">{{ item.title }}</span>
              </Link>
              <!-- External Link -->
              <a
                v-else
                :href="item.href"
                :aria-label="item.title"
                :target="item.isExternal || item.href.startsWith('http') ? '_blank' : undefined"
                :rel="item.isExternal || item.href.startsWith('http') ? 'noopener noreferrer' : undefined"
                :class="navLinkClasses(item.href)"
              >
                <component :is="item.icon" class="size-4 shrink-0" />
                <span v-if="isActive(item.href)">{{ item.title }}</span>
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
              <ThemeToggle
                v-if="item.isTheme"
                class="size-4 shrink-0 text-current"
                :button-class="navLinkClasses(item.href)"
              />
              <!-- Internal Link -->
              <Link
                v-else-if="!item.isExternal && !item.href.startsWith('http')"
                :href="item.href"
                :aria-label="item.title"
                :aria-current="isActive(item.href) ? 'page' : undefined"
                :class="navLinkClasses(item.href)"
              >
                <component :is="item.icon" class="size-4 shrink-0" />
                <span v-if="isActive(item.href)">{{ item.title }}</span>
              </Link>
              <!-- External Link -->
              <a
                v-else
                :href="item.href"
                :aria-label="item.title"
                :target="item.isExternal || item.href.startsWith('http') ? '_blank' : undefined"
                :rel="item.isExternal || item.href.startsWith('http') ? 'noopener noreferrer' : undefined"
                :class="navLinkClasses(item.href)"
              >
                <component :is="item.icon" class="size-4 shrink-0" />
                <span v-if="isActive(item.href)">{{ item.title }}</span>
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
      <Dock
        :className="
          cn(
            'md:hidden h-[4.25rem] gap-1.5 border-white/10 !bg-neutral-950 px-3 shadow-[0_22px_70px_-32px_rgba(0,0,0,0.85)] supports-[backdrop-filter]:!bg-neutral-950/95',
            mobileClassName,
          )
        "
      >
        <DockIcon v-for="item in items.slice(0, 5)" :key="item.title">
          <ThemeToggle
            v-if="item.isTheme"
            class="size-5 shrink-0 text-current"
            :button-class="navLinkClasses(item.href, true)"
          />
          <!-- Internal Link -->
          <Link
            v-else-if="!item.isExternal && !item.href.startsWith('http')"
            :href="item.href"
            :aria-label="item.title"
            :aria-current="isActive(item.href) ? 'page' : undefined"
            :class="navLinkClasses(item.href, true)"
          >
            <component :is="item.icon" class="size-5 shrink-0" />
            <span v-if="isActive(item.href)" class="leading-none">{{ item.title }}</span>
          </Link>
          <!-- External Link -->
          <a
            v-else
            :href="item.href"
            :aria-label="item.title"
            :target="item.isExternal || item.href.startsWith('http') ? '_blank' : undefined"
            :rel="item.isExternal || item.href.startsWith('http') ? 'noopener noreferrer' : undefined"
            :class="navLinkClasses(item.href, true)"
          >
            <component :is="item.icon" class="size-5 shrink-0" />
            <span v-if="isActive(item.href)" class="leading-none">{{ item.title }}</span>
          </a>
        </DockIcon>
      </Dock>
    </TooltipProvider>
  </div>
</template>
