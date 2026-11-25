/**
 * Display formatting shared by pages (Indonesian locale).
 *
 * Usage: formatFileSize(52_340) → "51 KB", formatDate('2026-11-24T08:15:00+00:00') → "24 Nov 2026"
 */

export function formatFileSize(bytes) {
    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${Math.round(bytes / 1024)} KB`;
    }

    return `${(bytes / 1024 / 1024).toLocaleString('id-ID', { maximumFractionDigits: 1 })} MB`;
}

export function formatDate(isoDate) {
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(isoDate));
}

/** A template variable as it is written in Word, e.g. `nama` → `${nama}`. */
export function placeholderFor(variable) {
    return `\${${variable}}`;
}

/**
 * A calendar date without time (`YYYY-MM-DD`, e.g. a birth date), read as a local date so it never
 * shifts a day because of the time zone. `formatCalendarDate('1990-05-01')` → "1 Mei 1990".
 */
export function formatCalendarDate(date) {
    const [year, month, day] = date.split('-').map(Number);

    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(year, month - 1, day));
}

/** Date and time in the viewer's time zone, e.g. `formatDateTime('2026-11-25T03:15:00+00:00')` → "25 Nov 2026 10.15". */
export function formatDateTime(isoDate) {
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(isoDate));
}
