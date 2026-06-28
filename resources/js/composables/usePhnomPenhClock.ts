import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const TIME_ZONE = 'Asia/Phnom_Penh';

const timeFormatter = new Intl.DateTimeFormat('en-GB', {
    timeZone: TIME_ZONE,
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
});

const dateFormatter = new Intl.DateTimeFormat('en-US', {
    timeZone: TIME_ZONE,
    weekday: 'short',
    day: '2-digit',
    month: 'short',
    year: 'numeric',
});

/**
 * Live clock pinned to Asia/Phnom_Penh.
 * Returns reactive `now` plus pre-formatted `time` (HH:MM:SS) and `date` strings.
 * Interval is cleared automatically on unmount.
 *
 * @param intervalMs tick rate; default 1000ms. Use 60000 when only the date matters.
 */
export function usePhnomPenhClock(intervalMs = 1000) {
    const now = ref(new Date());
    let timer: ReturnType<typeof setInterval> | null = null;

    const time = computed(() => {
        try {
            return timeFormatter.format(now.value);
        } catch {
            return '--:--:--';
        }
    });

    const date = computed(() => {
        try {
            return dateFormatter.format(now.value);
        } catch {
            return '';
        }
    });

    onMounted(() => {
        timer = setInterval(() => {
            now.value = new Date();
        }, intervalMs);
    });

    onBeforeUnmount(() => {
        if (timer) clearInterval(timer);
        timer = null;
    });

    return { now, time, date };
}
