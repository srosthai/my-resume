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
            <!-- Woven strap -->
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

            <!-- ID card -->
            <div
                ref="card"
                class="card"
                @pointerdown="onPointerDown"
                role="button"
                :aria-label="`Drag to swing ${name}'s ID card`"
            >
                <div class="card-slot" aria-hidden="true"></div>

                <div class="card-top">
                    <span class="card-badge">{{ badge }}</span>
                    <span class="card-id">{{ label }}</span>
                </div>

                <div class="card-rule" aria-hidden="true"></div>

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

                <div class="card-foot">
                    <div class="card-barcode" aria-hidden="true"></div>
                    <span class="card-foot-text">PNH · KH</span>
                </div>

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
        color-mix(in oklab, var(--color-foreground) 6%, transparent),
        transparent 70%
    );
    filter: blur(30px);
    pointer-events: none;
    z-index: 0;
}

/* Wall pin the whole thing hangs from */
.pin {
    position: absolute;
    top: 12px;
    left: 50%;
    width: 14px;
    height: 14px;
    margin-left: -7px;
    border-radius: 50%;
    background: radial-gradient(
        circle at 32% 30%,
        #c9c9c9 0%,
        #6b6b6b 48%,
        #1c1c1c 100%
    );
    box-shadow:
        0 0 0 3px color-mix(in oklab, var(--color-foreground) 6%, transparent),
        0 2px 5px rgba(0, 0, 0, 0.45),
        inset 0 1px 1px rgba(255, 255, 255, 0.45);
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
    background: radial-gradient(circle, rgba(255, 255, 255, 0.65), transparent);
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

/* Woven fabric strap — diagonal weave + edge stitching */
.strap {
    position: absolute;
    top: 0;
    left: -15px;
    width: 30px;
    height: 138px;
    background:
        repeating-linear-gradient(
            45deg,
            rgba(255, 255, 255, 0.035) 0 1.5px,
            transparent 1.5px 3.5px
        ),
        repeating-linear-gradient(
            -45deg,
            rgba(255, 255, 255, 0.025) 0 1.5px,
            transparent 1.5px 3.5px
        ),
        linear-gradient(180deg, #0e0e0e 0%, #191919 50%, #0e0e0e 100%);
    border-left: 1px solid rgba(255, 255, 255, 0.05);
    border-right: 1px solid rgba(0, 0, 0, 0.85);
    border-radius: 0 0 2px 2px;
    box-shadow:
        inset 2px 0 5px rgba(0, 0, 0, 0.55),
        inset -2px 0 5px rgba(0, 0, 0, 0.35),
        0 3px 8px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

/* Edge stitch lines */
.strap::before,
.strap::after {
    content: '';
    position: absolute;
    top: 4px;
    bottom: 4px;
    width: 1px;
    background: repeating-linear-gradient(
        180deg,
        rgba(255, 255, 255, 0.22) 0 3px,
        transparent 3px 6px
    );
}
.strap::before {
    left: 3.5px;
}
.strap::after {
    right: 3.5px;
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
    color: rgba(255, 255, 255, 0.2);
    letter-spacing: 0.16em;
    transform: rotate(-90deg);
    white-space: nowrap;
    line-height: 1;
}

/* Brushed-metal clip threaded through the card slot */
.clip {
    position: absolute;
    top: 130px;
    left: -16px;
    width: 32px;
    height: 30px;
    z-index: 5;
    filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.35));
}

.clip-ring {
    position: absolute;
    left: 50%;
    top: 0;
    width: 18px;
    height: 12px;
    margin-left: -9px;
    border: 2px solid #3c3c3c;
    border-bottom: none;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    background: linear-gradient(180deg, #e2e2e2 0%, #8a8a8a 60%, #4a4a4a 100%);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.clip-body {
    position: absolute;
    top: 10px;
    left: 0;
    width: 32px;
    height: 18px;
    background: linear-gradient(
        180deg,
        #e6e6e6 0%,
        #b9b9b9 35%,
        #767676 70%,
        #3d3d3d 100%
    );
    border-radius: 3px;
    border: 1px solid #1f1f1f;
    box-shadow:
        inset 0 1.5px 0 rgba(255, 255, 255, 0.55),
        inset 0 -2px 3px rgba(0, 0, 0, 0.35);
}

/* Thin latch groove on the clip face */
.clip-body::after {
    content: '';
    position: absolute;
    left: 6px;
    right: 6px;
    top: 50%;
    height: 2px;
    margin-top: -1px;
    border-radius: 1px;
    background: linear-gradient(
        180deg,
        rgba(0, 0, 0, 0.45),
        rgba(255, 255, 255, 0.25)
    );
}

/* The ID card itself */
.card {
    position: absolute;
    top: 152px;
    left: 50%;
    width: 264px;
    height: 348px;
    margin-left: -132px;
    background: linear-gradient(150deg, #fcfbf9 0%, #efedea 100%);
    color: #141414;
    border-radius: 18px;
    padding: 26px 18px 16px;
    display: flex;
    flex-direction: column;
    gap: 11px;
    cursor: grab;
    box-shadow:
        0 32px 60px -24px rgba(0, 0, 0, 0.5),
        0 12px 24px -12px rgba(0, 0, 0, 0.25),
        0 2px 6px -2px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(0, 0, 0, 0.07);
    overflow: hidden;
    z-index: 4;
    touch-action: none;
}

.card:active {
    cursor: grabbing;
}

/* Punch slot the clip threads through */
.card-slot {
    position: absolute;
    top: 9px;
    left: 50%;
    width: 30px;
    height: 6px;
    margin-left: -15px;
    border-radius: 3px;
    background: #101010;
    box-shadow:
        inset 0 1.5px 2.5px rgba(0, 0, 0, 0.95),
        0 1px 0 rgba(255, 255, 255, 0.8);
}

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 9px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    flex-shrink: 0;
}

.card-badge {
    background: #141414;
    color: #f8f7f5;
    padding: 4px 10px;
    border-radius: 999px;
    font-weight: 600;
    letter-spacing: 0.24em;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.card-id {
    color: #8a8580;
    font-weight: 500;
    font-variant-numeric: tabular-nums;
}

.card-rule {
    height: 1px;
    flex-shrink: 0;
    background: linear-gradient(
        90deg,
        rgba(0, 0, 0, 0.12),
        rgba(0, 0, 0, 0.04) 60%,
        transparent
    );
}

.card-photo {
    position: relative;
    flex: 1;
    min-height: 0;
    overflow: hidden;
    border-radius: 11px;
    background: #dedcd8;
    box-shadow:
        inset 0 0 0 1px rgba(0, 0, 0, 0.07),
        inset 0 -18px 24px -18px rgba(0, 0, 0, 0.25);
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
    color: #969189;
    background: linear-gradient(135deg, #ebe9e5 0%, #c9c6c1 100%);
}

.card-info {
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}

.card-name {
    font-family: 'Instrument Serif', Georgia, serif;
    font-size: 27px;
    line-height: 1.02;
    color: #141414;
    letter-spacing: -0.01em;
}

.card-name-italic {
    font-style: italic;
    color: #4d4a46;
    font-size: 23px;
    margin-top: 1px;
}

.card-title {
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 9px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #8a8580;
    margin-top: 6px;
    font-weight: 500;
}

/* Barcode footer — replaces the old color stripe */
.card-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-shrink: 0;
    padding-top: 9px;
    border-top: 1px solid rgba(0, 0, 0, 0.07);
}

.card-barcode {
    flex: 1;
    height: 16px;
    max-width: 150px;
    background: repeating-linear-gradient(
        90deg,
        #1a1a1a 0 1.5px,
        transparent 1.5px 3px,
        #1a1a1a 3px 6px,
        transparent 6px 7.5px,
        #1a1a1a 7.5px 8.5px,
        transparent 8.5px 12px,
        #1a1a1a 12px 13px,
        transparent 13px 15.5px
    );
    opacity: 0.82;
}

.card-foot-text {
    font-family: 'JetBrains Mono', ui-monospace, monospace;
    font-size: 8px;
    letter-spacing: 0.24em;
    text-transform: uppercase;
    color: #a19c96;
    white-space: nowrap;
}

/* Soft lamination sheen */
.card-shine {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(
        118deg,
        transparent 0%,
        transparent 40%,
        rgba(255, 255, 255, 0.2) 50%,
        transparent 60%,
        transparent 100%
    );
    mix-blend-mode: overlay;
    border-radius: 18px;
}

/* Dark mode: slightly dimmed paper so the card doesn't glow */
:global(.dark) .card {
    background: linear-gradient(150deg, #f2f0ed 0%, #dcd9d5 100%);
    border-color: rgba(0, 0, 0, 0.12);
}

@media (prefers-reduced-motion: reduce) {
    .pendulum {
        transform: rotate(0) !important;
    }
}
</style>
