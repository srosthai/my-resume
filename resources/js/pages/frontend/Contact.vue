<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import {
    Facebook,
    Linkedin,
    Instagram,
    Send,
    Github,
    Mail,
    MapPin,
    Clock,
    ArrowUpRight,
    CheckCircle,
    AlertCircle,
} from 'lucide-vue-next'
import { Skeleton } from '@/components/ui/skeleton'
import FrontendLayout from '@/layouts/FrontendLayout.vue'

const props = defineProps({
    title: { type: String, default: 'Contact' },
    description: { type: String, default: '' },
})

const isLoading = ref(true)
const isVisible = ref(false)
const showSuccessMessage = ref(false)
const showErrorMessage = ref(false)
const errorMessage = ref('')

const now = ref(new Date())
let clockTimer = null

const dateString = computed(() => {
    try {
        return new Intl.DateTimeFormat('en-US', {
            timeZone: 'Asia/Phnom_Penh',
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(now.value)
    } catch (e) {
        return ''
    }
})

const timeInPhnomPenh = computed(() => {
    try {
        return new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Phnom_Penh',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        }).format(now.value)
    } catch (e) {
        return '--:--'
    }
})

const pointer = ref({ x: 50, y: 50 })
const handlePointer = (e) => {
    pointer.value = {
        x: (e.clientX / window.innerWidth) * 100,
        y: (e.clientY / window.innerHeight) * 100,
    }
}

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
})

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
            errorMessage.value =
                errors.message || 'Failed to send message. Please try again.'
            setTimeout(() => {
                showErrorMessage.value = false
            }, 5000)
        },
    })
}

const socialLinks = [
    {
        name: 'Facebook',
        icon: Facebook,
        url: 'https://www.facebook.com/srosthai2003',
        username: 'Jo Zen',
        handle: '@srosthai2003',
    },
    {
        name: 'LinkedIn',
        icon: Linkedin,
        url: 'https://www.linkedin.com/in/sros-thai-b491b42ab/',
        username: 'SROS THAI',
        handle: 'sros-thai',
    },
    {
        name: 'Instagram',
        icon: Instagram,
        url: 'https://www.instagram.com/sovannthai887',
        username: 'Sovann Thai',
        handle: '@sovannthai887',
    },
    {
        name: 'Telegram',
        icon: Send,
        url: 'https://t.me/srosthai2003',
        username: 'srosthai',
        handle: '@srosthai2003',
    },
    {
        name: 'GitHub',
        icon: Github,
        url: 'https://github.com/srosthai',
        username: 'Sovannthai',
        handle: '@srosthai',
    },
    {
        name: 'Email',
        icon: Mail,
        url: 'mailto:srosthai00@gmail.com',
        username: 'srosthai00@gmail.com',
        handle: 'direct line',
    },
]

const openLink = (url) => {
    if (typeof window !== 'undefined') {
        window.open(url, '_blank', 'noopener,noreferrer')
    }
}

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false
        requestAnimationFrame(() => {
            isVisible.value = true
        })
    }, 400)

    clockTimer = setInterval(() => {
        now.value = new Date()
    }, 30000)

    window.addEventListener('pointermove', handlePointer, { passive: true })
})

onBeforeUnmount(() => {
    if (clockTimer) clearInterval(clockTimer)
    window.removeEventListener('pointermove', handlePointer)
})
</script>

<template>
    <FrontendLayout current-route="/contact">
        <Head>
            <title>{{ title }}</title>
            <meta name="description" :content="description" />
            <meta
                name="keywords"
                content="contact, get in touch, hire developer, collaboration, web developer contact, SROS THAI"
            />
            <meta property="og:title" :content="title" />
            <meta property="og:description" :content="description" />
            <meta property="og:type" content="website" />
            <link
                href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@400;500;600&display=swap"
                rel="stylesheet"
            />
        </Head>

        <!-- Skeleton -->
        <section
            v-if="isLoading"
            class="mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
        >
            <div class="grid w-full grid-cols-2 gap-3 sm:gap-4 md:grid-cols-12 md:gap-5">
                <Skeleton class="col-span-2 h-64 rounded-3xl md:col-span-12" />
                <Skeleton class="col-span-2 h-[34rem] rounded-3xl md:col-span-8" />
                <Skeleton class="col-span-2 h-[34rem] rounded-3xl md:col-span-4" />
                <Skeleton
                    v-for="i in 6"
                    :key="i"
                    class="col-span-2 h-28 rounded-2xl sm:col-span-1 md:col-span-4"
                />
            </div>
        </section>

        <section
            v-else
            class="relative mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-8 lg:px-10"
            :class="{ 'is-visible': isVisible }"
        >
            <!-- Ambient + grain -->
            <div
                class="pointer-events-none fixed inset-0 -z-10 overflow-hidden"
                aria-hidden="true"
            >
                <div
                    class="ambient-blob"
                    :style="{ left: pointer.x + '%', top: pointer.y + '%' }"
                ></div>
                <div class="grain-overlay"></div>
            </div>

            <!-- Top meta strip -->
            <div
                class="reveal mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2 font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground/70 sm:mb-5 sm:text-[10px] md:text-xs"
                style="--d: 0ms"
            >
                <span class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-foreground/40"></span>
                    Chapter 06
                </span>
                <span class="hidden md:inline">Contact / 2026</span>
                <span class="tabular-nums">{{ dateString }}</span>
            </div>

            <!-- HERO -->
            <article
                class="reveal relative overflow-hidden rounded-[1.5rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8 md:p-10"
                style="--d: 80ms"
            >
                <div
                    class="pointer-events-none absolute -right-24 -top-24 h-64 w-64 rotate-45 bg-gradient-to-br from-foreground/[0.04] to-transparent"
                    aria-hidden="true"
                ></div>

                <div
                    class="flex items-center justify-between font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground sm:text-[10px] md:text-xs"
                >
                    <span class="inline-flex items-center gap-2">
                        <span class="h-px w-5 bg-foreground/40 sm:w-6"></span>
                        Correspondence desk
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        </span>
                        Accepting inquiries
                    </span>
                </div>

                <h1 class="mt-5 font-serif leading-[0.9] tracking-tight sm:mt-6">
                    <span
                        class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal text-foreground"
                    >
                        Get in
                    </span>
                    <span
                        class="block text-[clamp(2.5rem,9vw,6.5rem)] font-normal italic text-foreground/80"
                    >
                        touch.
                    </span>
                </h1>

                <p
                    class="mt-5 max-w-2xl text-sm leading-relaxed text-muted-foreground sm:mt-6 sm:text-[15px] md:text-base"
                >
                    Open to collaborations, freelance work, and a good conversation about code.
                    Drop a message below, or reach out directly — whichever feels right.
                </p>
            </article>

            <!-- FORM + INFO grid -->
            <div class="mt-4 grid grid-cols-1 gap-4 sm:mt-5 sm:gap-5 md:grid-cols-12">
                <!-- Contact form card (8 cols) -->
                <article
                    class="reveal relative col-span-1 overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-8 md:col-span-8"
                    style="--d: 160ms"
                >
                    <div
                        class="flex items-center justify-between font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px] sm:tracking-[0.25em]"
                    >
                        <span>/ Send a message</span>
                        <span>{{ timeInPhnomPenh }} · ICT</span>
                    </div>

                    <h2
                        class="mt-3 font-serif text-3xl leading-tight tracking-tight text-foreground sm:text-4xl"
                    >
                        Write something nice.
                    </h2>

                    <!-- Success / Error -->
                    <div
                        v-if="showSuccessMessage"
                        class="mt-5 flex items-start gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-600 dark:text-emerald-400"
                    >
                        <CheckCircle class="mt-0.5 h-4 w-4 shrink-0" />
                        <span>Message sent — I'll reply shortly.</span>
                    </div>
                    <div
                        v-if="showErrorMessage"
                        class="mt-5 flex items-start gap-3 rounded-2xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-600 dark:text-red-400"
                    >
                        <AlertCircle class="mt-0.5 h-4 w-4 shrink-0" />
                        <span>{{ errorMessage }}</span>
                    </div>

                    <form
                        @submit.prevent="submitForm"
                        class="mt-6 space-y-5 sm:mt-8"
                    >
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div class="field">
                                <label for="name" class="field-label">
                                    <span class="field-num">01</span>
                                    Your name
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="e.g. Alex Rivera"
                                    :class="['field-input', form.errors.name ? 'field-input-error' : '']"
                                />
                                <span
                                    v-if="form.errors.name"
                                    class="field-error"
                                >
                                    {{ form.errors.name }}
                                </span>
                            </div>
                            <div class="field">
                                <label for="email" class="field-label">
                                    <span class="field-num">02</span>
                                    Email address
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    placeholder="you@domain.com"
                                    :class="['field-input', form.errors.email ? 'field-input-error' : '']"
                                />
                                <span
                                    v-if="form.errors.email"
                                    class="field-error"
                                >
                                    {{ form.errors.email }}
                                </span>
                            </div>
                        </div>

                        <div class="field">
                            <label for="subject" class="field-label">
                                <span class="field-num">03</span>
                                Subject
                            </label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                required
                                placeholder="What's this about?"
                                :class="['field-input', form.errors.subject ? 'field-input-error' : '']"
                            />
                            <span
                                v-if="form.errors.subject"
                                class="field-error"
                            >
                                {{ form.errors.subject }}
                            </span>
                        </div>

                        <div class="field">
                            <label for="message" class="field-label">
                                <span class="field-num">04</span>
                                Message
                            </label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="7"
                                required
                                placeholder="Tell me what you're working on, what you need, or just say hello…"
                                :class="['field-input field-textarea', form.errors.message ? 'field-input-error' : '']"
                            ></textarea>
                            <div class="mt-1.5 flex items-center justify-between">
                                <span
                                    v-if="form.errors.message"
                                    class="field-error"
                                >
                                    {{ form.errors.message }}
                                </span>
                                <span
                                    class="ml-auto font-mono text-[10px] uppercase tracking-[0.2em] text-muted-foreground/60"
                                >
                                    {{ form.message.length }} chars
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="cta-primary group inline-flex items-center gap-3 rounded-full bg-foreground px-6 py-3 text-sm font-medium text-background transition-all duration-300 hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span v-if="form.processing">Sending…</span>
                                <span v-else>Send message</span>
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-background/20 transition-transform duration-300 group-hover:rotate-45"
                                >
                                    <Send class="h-3.5 w-3.5" />
                                </span>
                            </button>
                            <span
                                class="font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground/60"
                            >
                                Usually replies within 24h
                            </span>
                        </div>
                    </form>
                </article>

                <!-- Side info column (4 cols) -->
                <aside class="col-span-1 space-y-4 sm:space-y-5 md:col-span-4">
                    <!-- Direct contact card -->
                    <article
                        class="reveal bento-card overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-6"
                        style="--d: 240ms"
                    >
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px] sm:tracking-[0.25em]"
                        >
                            / Direct line
                        </span>
                        <a
                            href="mailto:srosthai00@gmail.com"
                            class="mt-3 block font-serif text-2xl leading-tight tracking-tight text-foreground transition-colors hover:text-foreground/80 sm:text-[28px]"
                        >
                            srosthai00@gmail.com
                        </a>
                        <div
                            class="mt-4 space-y-2 font-mono text-[11px] uppercase tracking-[0.15em] text-muted-foreground"
                        >
                            <div class="flex items-center gap-2">
                                <MapPin class="h-3 w-3 opacity-70" />
                                Phnom Penh, Cambodia
                            </div>
                            <div class="flex items-center gap-2">
                                <Clock class="h-3 w-3 opacity-70" />
                                UTC+7 · {{ timeInPhnomPenh }} local
                            </div>
                        </div>
                    </article>

                    <!-- Availability card -->
                    <article
                        class="reveal bento-card overflow-hidden rounded-[1.25rem] border border-border/60 bg-card/60 p-5 backdrop-blur-xl sm:rounded-3xl sm:p-6"
                        style="--d: 320ms"
                    >
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px] sm:tracking-[0.25em]"
                        >
                            / Status
                        </span>
                        <div class="mt-3 space-y-3">
                            <div class="flex items-start gap-2.5">
                                <span class="relative mt-1.5 flex h-2 w-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                </span>
                                <div>
                                    <div class="font-serif text-lg leading-tight text-foreground">
                                        Available for freelance
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-muted-foreground">
                                        Open for contracts, collaborations, and short-term builds.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 border-t border-border/50 pt-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-amber-500"></span>
                                <div>
                                    <div class="font-serif text-lg leading-tight text-foreground">
                                        Full-time: selective
                                    </div>
                                    <p class="mt-1 text-xs leading-relaxed text-muted-foreground">
                                        Considering roles that fit the right team and mission.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>
                </aside>
            </div>

            <!-- FOLLOW / SOCIAL -->
            <section class="mt-10 sm:mt-14">
                <div
                    class="reveal mb-6 flex items-end justify-between gap-4 border-b border-border/50 pb-4 sm:mb-8"
                    style="--d: 0ms"
                >
                    <div>
                        <span
                            class="font-mono text-[9px] uppercase tracking-[0.25em] text-muted-foreground sm:text-[10px]"
                        >
                            Elsewhere
                        </span>
                        <h2
                            class="mt-1.5 font-serif text-3xl leading-[0.95] tracking-tight text-foreground sm:text-4xl md:text-5xl"
                        >
                            Follow along.
                        </h2>
                    </div>
                    <span
                        class="hidden font-mono text-[10px] uppercase tracking-[0.25em] text-muted-foreground/60 sm:inline-flex"
                    >
                        {{ socialLinks.length }} channels
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
                    <article
                        v-for="(social, i) in socialLinks"
                        :key="social.name"
                        class="social-card reveal group relative cursor-pointer overflow-hidden rounded-2xl border border-border/60 bg-card/60 p-5 backdrop-blur-xl"
                        :style="{ '--d': 120 + i * 70 + 'ms' }"
                        tabindex="0"
                        role="link"
                        :aria-label="`Open ${social.name}`"
                        @click="openLink(social.url)"
                        @keydown.enter="openLink(social.url)"
                        @keydown.space.prevent="openLink(social.url)"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span
                                    class="font-mono text-[9px] uppercase tracking-[0.22em] text-muted-foreground sm:text-[10px]"
                                >
                                    / {{ String(i + 1).padStart(2, '0') }}
                                </span>
                                <h3
                                    class="mt-2 font-serif text-2xl leading-tight tracking-tight text-foreground"
                                >
                                    {{ social.name }}
                                </h3>
                                <p
                                    class="mt-1 truncate font-mono text-[11px] uppercase tracking-[0.15em] text-muted-foreground"
                                >
                                    {{ social.username }}
                                </p>
                                <p
                                    class="mt-0.5 truncate font-mono text-[10px] uppercase tracking-[0.15em] text-muted-foreground/60"
                                >
                                    {{ social.handle }}
                                </p>
                            </div>
                            <span
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-border/60 text-muted-foreground transition-all duration-300 group-hover:-rotate-45 group-hover:border-foreground/60 group-hover:text-foreground"
                            >
                                <component :is="social.icon" class="h-4 w-4" />
                            </span>
                        </div>

                        <div
                            class="mt-5 flex items-center justify-between border-t border-border/50 pt-3"
                        >
                            <span
                                class="font-mono text-[10px] uppercase tracking-[0.22em] text-muted-foreground/70"
                            >
                                Open profile
                            </span>
                            <ArrowUpRight
                                class="h-3.5 w-3.5 opacity-60 transition-all duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:opacity-100"
                            />
                        </div>
                    </article>
                </div>
            </section>

            <!-- Footer -->
            <div
                class="reveal mt-10 flex flex-col items-start justify-between gap-1.5 font-mono text-[9px] uppercase tracking-[0.2em] text-muted-foreground/60 sm:mt-14 sm:flex-row sm:items-center sm:gap-2 sm:text-[10px] sm:tracking-[0.22em] md:text-xs"
                style="--d: 700ms"
            >
                <span>© {{ new Date().getFullYear() }} · Correspondence</span>
                <span>End of transmission →</span>
            </div>
        </section>
    </FrontendLayout>
</template>

<style scoped>
.reveal {
    opacity: 0;
    transform: translateY(18px);
}
.is-visible .reveal {
    animation: revealUp 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    animation-delay: var(--d, 0ms);
}
@keyframes revealUp {
    from {
        opacity: 0;
        transform: translateY(18px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Typography */
h1,
h2,
h3,
.font-serif {
    font-family: 'Instrument Serif', 'Iowan Old Style', 'Apple Garamond', Georgia, serif;
    font-feature-settings: 'ss01', 'liga';
}
.font-mono {
    font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
}

/* Ambient */
.ambient-blob {
    position: absolute;
    width: 40rem;
    height: 40rem;
    border-radius: 9999px;
    transform: translate(-50%, -50%);
    background: radial-gradient(
        closest-side,
        color-mix(in oklab, var(--color-foreground) 7%, transparent),
        transparent 70%
    );
    filter: blur(60px);
    transition:
        left 600ms cubic-bezier(0.22, 1, 0.36, 1),
        top 600ms cubic-bezier(0.22, 1, 0.36, 1);
    will-change: left, top;
}
.grain-overlay {
    position: absolute;
    inset: 0;
    opacity: 0.035;
    mix-blend-mode: overlay;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.9'/%3E%3C/svg%3E");
    pointer-events: none;
}

/* Bento card hover lift */
.bento-card {
    transition:
        transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.35s ease;
}
@media (hover: hover) and (pointer: fine) {
    .bento-card:hover {
        transform: translateY(-3px);
        border-color: color-mix(in oklab, var(--color-foreground) 20%, var(--color-border));
    }
}

/* CTA */
.cta-primary {
    box-shadow: 0 10px 30px -10px color-mix(in oklab, var(--color-foreground) 40%, transparent);
}
.cta-primary:hover {
    box-shadow: 0 14px 40px -12px color-mix(in oklab, var(--color-foreground) 55%, transparent);
}

/* Form fields */
.field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.field-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--color-muted-foreground);
}
.field-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.5rem;
    padding: 0.1rem 0.4rem;
    border-radius: 9999px;
    border: 1px solid var(--color-border);
    background: color-mix(in oklab, var(--color-muted) 40%, transparent);
    color: var(--color-foreground);
    font-size: 9px;
    font-weight: 600;
}
.field-input {
    width: 100%;
    padding: 0.85rem 1rem;
    background: color-mix(in oklab, var(--color-muted) 30%, transparent);
    border: 1px solid var(--color-border);
    border-radius: 0.875rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    letter-spacing: 0.02em;
    color: var(--color-foreground);
    transition:
        border-color 0.25s ease,
        background-color 0.25s ease,
        box-shadow 0.25s ease;
}
.field-input::placeholder {
    color: color-mix(in oklab, var(--color-muted-foreground) 70%, transparent);
    font-size: 0.75rem;
}
.field-input:focus {
    outline: none;
    border-color: color-mix(in oklab, var(--color-foreground) 45%, var(--color-border));
    background: var(--color-background);
    box-shadow: 0 0 0 3px color-mix(in oklab, var(--color-foreground) 8%, transparent);
}
.field-textarea {
    resize: vertical;
    min-height: 10rem;
    line-height: 1.6;
}
.field-input-error {
    border-color: color-mix(in oklab, #ef4444 60%, var(--color-border));
}
.field-error {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #ef4444;
}

/* Social cards */
.social-card {
    transition:
        transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
        border-color 0.35s ease,
        box-shadow 0.35s ease;
    outline: none;
}
@media (hover: hover) and (pointer: fine) {
    .social-card:hover {
        transform: translateY(-3px);
        border-color: color-mix(in oklab, var(--color-foreground) 25%, var(--color-border));
        box-shadow: 0 22px 45px -28px rgba(0, 0, 0, 0.4);
    }
}
.social-card:focus-visible {
    border-color: color-mix(in oklab, var(--color-foreground) 40%, var(--color-border));
    box-shadow: 0 0 0 2px color-mix(in oklab, var(--color-foreground) 20%, transparent);
}

@media (prefers-reduced-motion: reduce) {
    .reveal,
    .is-visible .reveal {
        opacity: 1 !important;
        transform: none !important;
        animation: none !important;
    }
    .ambient-blob {
        transition: none;
    }
}
</style>
