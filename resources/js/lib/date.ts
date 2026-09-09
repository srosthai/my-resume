/**
 * Hydration-safe date formatting.
 *
 * Uses a fixed locale and time zone so that the server (SSR) and the browser
 * render exactly the same string, avoiding hydration mismatches caused by the
 * visitor's locale/time zone.
 */
const DEFAULT_OPTIONS: Intl.DateTimeFormatOptions = {
    timeZone: 'Asia/Phnom_Penh',
    year: 'numeric',
    month: 'short',
    day: '2-digit',
};

export function formatDate(value: string | null | undefined, opts?: Intl.DateTimeFormatOptions): string {
    if (!value) {
        return '';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return new Intl.DateTimeFormat('en-US', { ...DEFAULT_OPTIONS, ...opts }).format(date);
}
