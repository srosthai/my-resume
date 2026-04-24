<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
    name: { type: String, default: 'Portfolio' },
    position: { type: String, default: '' },
    image: { type: String, default: '' },
    badge: { type: String, default: 'STAFF' },
    label: { type: String, default: 'ID · 2026' },
    strapText: { type: String, default: 'PORTFOLIO · 2026 · PORTFOLIO · 2026' },
})

const root = ref(null)
const card = ref(null)

const angle = ref(0)
let angularVelocity = 0
let rafId = null
let lastTs = 0
let dragging = false
let dragOffsetAngle = 0
let prevPointerTs = 0

let pivotX = 0
let pivotY = 0

const reducedMotion = ref(false)

const GRAVITY = 0.000085
const DAMPING_PER_MS = 0.9985
const MAX_ANGLE = Math.PI * 0.9
const MAX_VEL = 0.02

const updatePivot = () => {
    if (!root.value) return
    const rect = root.value.getBoundingClientRect()
    pivotX = rect.left + rect.width / 2
    pivotY = rect.top + 20
}

const pointerAngle = (cx, cy) => Math.atan2(cx - pivotX, cy - pivotY)

const loop = (ts) => {
    const dt = lastTs ? Math.min(32, ts - lastTs) : 16
    lastTs = ts

    if (!dragging) {
        const acc = -GRAVITY * Math.sin(angle.value)
        angularVelocity += acc * dt
        angularVelocity *= Math.pow(DAMPING_PER_MS, dt)
        if (angularVelocity > MAX_VEL) angularVelocity = MAX_VEL
        if (angularVelocity < -MAX_VEL) angularVelocity = -MAX_VEL
        angle.value += angularVelocity * dt

        if (angle.value > MAX_ANGLE) {
            angle.value = MAX_ANGLE
            angularVelocity *= -0.4
        } else if (angle.value < -MAX_ANGLE) {
            angle.value = -MAX_ANGLE
            angularVelocity *= -0.4
        }
    }

    rafId = requestAnimationFrame(loop)
}

const onPointerDown = (e) => {
    if (reducedMotion.value) return
    updatePivot()
    dragging = true
    angularVelocity = 0
    dragOffsetAngle = pointerAngle(e.clientX, e.clientY) - angle.value
    prevPointerTs = performance.now()
    try {
        e.target.setPointerCapture?.(e.pointerId)
    } catch (_) {
        // ignore
    }
    e.preventDefault()
}

const onPointerMove = (e) => {
    if (!dragging) return
    const rawAngle = pointerAngle(e.clientX, e.clientY) - dragOffsetAngle
    const clamped = Math.max(-MAX_ANGLE, Math.min(MAX_ANGLE, rawAngle))
    const now = performance.now()
    const dt = Math.max(1, now - prevPointerTs)
    angularVelocity = (clamped - angle.value) / dt
    angle.value = clamped
    prevPointerTs = now
}

const onPointerUp = () => {
    dragging = false
}

onMounted(() => {
    reducedMotion.value = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches || false
    updatePivot()

    if (!reducedMotion.value) {
        angularVelocity = 0.0018
        rafId = requestAnimationFrame(loop)
    }

    window.addEventListener('resize', updatePivot, { passive: true })
    window.addEventListener('scroll', updatePivot, { passive: true })
    window.addEventListener('pointermove', onPointerMove, { passive: true })
    window.addEventListener('pointerup', onPointerUp, { passive: true })
    window.addEventListener('pointercancel', onPointerUp, { passive: true })
})

onBeforeUnmount(() => {
    if (rafId) cancelAnimationFrame(rafId)
    window.removeEventListener('resize', updatePivot)
    window.removeEventListener('scroll', updatePivot)
    window.removeEventListener('pointermove', onPointerMove)
    window.removeEventListener('pointerup', onPointerUp)
    window.removeEventListener('pointercancel', onPointerUp)
})

const rotateStyle = computed(() => ({
    transform: `rotate(${angle.value}rad)`,
}))

const firstName = computed(() => (props.name || '').trim().split(/\s+/)[0] || 'Name')
const lastName = computed(() =>
    (props.name || '').trim().split(/\s+/).slice(1).join(' '),
)

const imageSrc = computed(() => {
    if (!props.image) return ''
    return props.image.startsWith('http') ? props.image : `/${props.image}`
})
</script>

<template>
    <div ref="root" class="lanyard-root">
        <div class="ambient"></div>

        <!-- Pin at top center -->
        <div class="pin" aria-hidden="true"></div>

        <!-- Pendulum pivots at its origin (top-center of root) -->
        <div class="pendulum" :style="rotateStyle">
            <!-- Strap -->
            <div class="strap" aria-hidden="true">
                <div class="strap-texture">
                    <span v-for="i in 6" :key="i" class="strap-text">
                        {{ strapText }}
                    </span>
                </div>
            </div>

            <!-- Metal clip -->
            <div class="clip" aria-hidden="true">
                <div class="clip-ring"></div>
                <div class="clip-body"></div>
            </div>

            <!-- Hole through the card top -->
            <div class="card-hole" aria-hidden="true"></div>

            <!-- ID card -->
            <div
                ref="card"
                class="card"
                @pointerdown="onPointerDown"
                role="button"
                :aria-label="`Drag to swing ${name}'s ID card`"
            >
                <div class="card-top">
                    <span class="card-badge">{{ badge }}</span>
                    <span class="card-id">{{ label }}</span>
                </div>
                <div class="card-photo">
                    <img
                        v-if="imageSrc"
                        :src="imageSrc"
                        :alt="name"
                        draggable="false"
                    />
                    <div v-else class="card-photo-fallback">
                        {{ firstName.charAt(0) }}
                    </div>
                </div>
                <div class="card-info">
                    <div class="card-name">
                        <div>{{ firstName }}</div>
                        <div v-if="lastName" class="card-name-italic">{{ lastName }}</div>
                    </div>
                    <div class="card-title">{{ position }}</div>
                </div>
                <div class="card-stripe"></div>
                <div class="card-shine"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.lanyard-root {
    position: relative;
    width: 100%;
    min-height: 34rem;
    aspect-ratio: 3 / 4.2;
    user-select: none;
    -webkit-user-select: none;
    touch-action: none;
    overflow: visible;
}

.ambient {
    position: absolute;
    left: 50%;
    top: 55%;
    width: 80%;
    height: 55%;
    transform: translate(-50%, -50%);
    background: radial-gradient(
        ellipse at center,
        color-mix(in oklab, var(--color-foreground) 8%, transparent),
        transparent 70%
    );
    filter: blur(30px);
    pointer-events: none;
    z-index: 0;
}

.pin {
    position: absolute;
    top: 12px;
    left: 50%;
    width: 16px;
    height: 16px;
    margin-left: -8px;
    border-radius: 50%;
    background: radial-gradient(
        circle at 30% 30%,
        #b8b8b8 0%,
        #606060 45%,
        #1a1a1a 100%
    );
    box-shadow:
        0 2px 4px rgba(0, 0, 0, 0.5),
        inset 0 -1px 1.5px rgba(255, 255, 255, 0.25),
        inset 0 1px 1px rgba(255, 255, 255, 0.4);
    z-index: 6;
}

.pin::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 50%;
    width: 4px;
    height: 4px;
    margin: -2px 0 0 -2px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.6), transparent);
}

.pendulum {
    position: absolute;
    top: 20px;
    left: 50%;
    width: 0;
    height: 0;
    transform-origin: 0 0;
    will-change: transform;
    z-index: 2;
}

/* Strap hangs from pivot down to clip */
.strap {
    position: absolute;
    top: 0;
    left: -13px;
    width: 26px;
    height: 140px;
    background: linear-gradient(
        180deg,
        #0a0a0a 0%,
        #141414 50%,
        #0a0a0a 100%
    );
    border-left: 1px solid rgba(255, 255, 255, 0.04);
    border-right: 1px solid rgba(0, 0, 0, 0.8);
    box-shadow:
        inset 2px 0 4px rgba(0, 0, 0, 0.5),
        0 2px 6px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.strap-texture {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: center;
}

.strap-text {
    display: block;
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 7.5px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.18);
    letter-spacing: 0.15em;
    transform: rotate(-90deg);
    white-space: nowrap;
    line-height: 1;
}

/* Metal clip at bottom of strap */
.clip {
    position: absolute;
    top: 135px;
    left: -17px;
    width: 34px;
    height: 24px;
    z-index: 3;
}

.clip-ring {
    position: absolute;
    left: 50%;
    top: 0;
    width: 20px;
    height: 12px;
    margin-left: -10px;
    border: 1.5px solid #2a2a2a;
    border-bottom: none;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    background: linear-gradient(
        180deg,
        #d4d4d4 0%,
        #7a7a7a 55%,
        #3a3a3a 100%
    );
}

.clip-body {
    position: absolute;
    top: 10px;
    left: 0;
    width: 34px;
    height: 14px;
    background: linear-gradient(
        180deg,
        #cfcfcf 0%,
        #6a6a6a 50%,
        #2a2a2a 100%
    );
    border-radius: 2px;
    border: 1px solid #141414;
    box-shadow:
        inset 0 1px 0 rgba(255, 255, 255, 0.3),
        0 1px 2px rgba(0, 0, 0, 0.4);
}

.card-hole {
    position: absolute;
    top: 151px;
    left: 50%;
    width: 28px;
    height: 5px;
    margin-left: -14px;
    border-radius: 2px;
    background: #0a0a0a;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.9);
    z-index: 5;
}

/* The ID card itself */
.card {
    position: absolute;
    top: 160px;
    left: 50%;
    width: 260px;
    height: 340px;
    margin-left: -130px;
    background:
        linear-gradient(140deg, #fafafa 0%, #e8e8e8 100%);
    color: #111;
    border-radius: 16px;
    padding: 16px 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    cursor: grab;
    box-shadow:
        0 28px 50px -20px rgba(0, 0, 0, 0.55),
        0 6px 12px -2px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(0, 0, 0, 0.08);
    overflow: hidden;
    z-index: 4;
    touch-action: none;
}

.card:active {
    cursor: grabbing;
}

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 10px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    flex-shrink: 0;
}

.card-badge {
    background: #111;
    color: #f8f8f8;
    padding: 4px 9px;
    border-radius: 4px;
    font-weight: 700;
    letter-spacing: 0.22em;
}

.card-id {
    color: #666;
    font-weight: 600;
}

.card-photo {
    position: relative;
    flex: 1;
    min-height: 0;
    overflow: hidden;
    border-radius: 9px;
    background: #d4d4d4;
}

.card-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 25%;
    pointer-events: none;
    -webkit-user-drag: none;
}

.card-photo-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 88px;
    color: #888;
    background: linear-gradient(135deg, #e5e5e5 0%, #bfbfbf 100%);
}

.card-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex-shrink: 0;
}

.card-name {
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 28px;
    line-height: 1;
    color: #111;
    letter-spacing: -0.01em;
}

.card-name-italic {
    font-style: italic;
    opacity: 0.8;
    font-size: 24px;
    margin-top: 2px;
}

.card-title {
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 9.5px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: #666;
    margin-top: 5px;
    font-weight: 500;
}

.card-stripe {
    position: absolute;
    bottom: 9px;
    left: 16px;
    right: 16px;
    height: 4px;
    border-radius: 2px;
    background: linear-gradient(
        90deg,
        #111 0%,
        #111 40%,
        transparent 40%,
        transparent 50%,
        #f43f5e 50%,
        #eab308 100%
    );
    opacity: 0.85;
}

/* Subtle highlight strip — creates a lamination feel */
.card-shine {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(
        115deg,
        transparent 0%,
        transparent 38%,
        rgba(255, 255, 255, 0.25) 48%,
        transparent 58%,
        transparent 100%
    );
    mix-blend-mode: overlay;
    border-radius: 16px;
}

/* Dark mode: keep the card readable, bit of contrast shift */
:global(.dark) .card {
    background: linear-gradient(140deg, #f0f0f0 0%, #d6d6d6 100%);
}

@media (prefers-reduced-motion: reduce) {
    .pendulum {
        transform: rotate(0) !important;
    }
}
</style>
