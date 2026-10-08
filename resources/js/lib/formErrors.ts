/**
 * The first message for a field, including nested keys such as "gallery.0".
 */
export function fieldError(errors: Partial<Record<string, string>>, key: string): string | undefined {
    if (errors[key]) {
        return errors[key];
    }

    const nestedKey = Object.keys(errors).find((name) => name.startsWith(`${key}.`));

    return nestedKey ? errors[nestedKey] : undefined;
}

/**
 * Unique validation sentences for a toast. Keeps the list short enough to read.
 */
export function validationMessages(errors: Partial<Record<string, string>>): string[] {
    const unique = [...new Set(Object.values(errors).filter((message): message is string => Boolean(message)))];

    if (unique.length === 0) {
        return ['Please check the form and try again.'];
    }

    if (unique.length <= 3) {
        return unique;
    }

    return [...unique.slice(0, 3), 'Other fields need attention too. They are marked on the form.'];
}
