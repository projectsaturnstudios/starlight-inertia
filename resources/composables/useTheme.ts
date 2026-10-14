export type Theme = 'auto' | 'light' | 'dark';

const KEY = 'theme';
const media = () => window.matchMedia('(prefers-color-scheme: dark)');

export function storedTheme(): Theme {
    try {
        const value = localStorage.getItem(KEY);
        return value === 'light' || value === 'dark' ? value : 'auto';
    } catch {
        return 'auto';
    }
}

export function resolve(theme: Theme): 'light' | 'dark' {
    return theme === 'auto' ? (media().matches ? 'dark' : 'light') : theme;
}

export function applyTheme(theme: Theme = storedTheme()): void {
    document.documentElement.dataset.theme = resolve(theme);
}

export function setTheme(theme: Theme): void {
    try {
        if (theme === 'auto') {
            localStorage.removeItem(KEY);
        } else {
            localStorage.setItem(KEY, theme);
        }
    } catch {
        // Storage blocked: the choice lasts for this page only.
    }
    applyTheme(theme);
}

media().addEventListener('change', () => applyTheme());

export function useTheme() {
    return { storedTheme, resolve, applyTheme, setTheme };
}
