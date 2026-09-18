(function () {
    const defaults = {
        primary_color: '#b9d9c6', secondary_color: '#bdddea',
        background_color: '#f7f5ef', dark_background_color: '#182126',
        background_image_url: null
    };
    const saved = Object.assign({}, defaults, window.hasaAppearance || {});
    let previewImageUrl = saved.background_image_url;
    const hex = /^#[0-9a-f]{6}$/i;
    const safe = (value, fallback) => hex.test(value || '') ? value : fallback;
    const rgb = value => [1, 3, 5].map(index => parseInt(value.slice(index, index + 2), 16));
    const mix = (value, target, ratio) => '#' + rgb(value).map((channel, index) =>
        Math.round(channel * (1 - ratio) + target[index] * ratio).toString(16).padStart(2, '0')).join('');
    const luminance = value => {
        const [r, g, b] = rgb(value).map(channel => {
            const s = channel / 255;
            return s <= .04045 ? s / 12.92 : ((s + .055) / 1.055) ** 2.4;
        });
        return .2126 * r + .7152 * g + .0722 * b;
    };
    function apply(palette) {
        const primary = safe(palette.primary_color, defaults.primary_color);
        const secondary = safe(palette.secondary_color, defaults.secondary_color);
        const background = safe(palette.background_color, defaults.background_color);
        const darkBackground = safe(palette.dark_background_color, defaults.dark_background_color);
        const dark = document.documentElement.classList.contains('dark-mode') || document.body?.classList.contains('dark-mode');
        const primaryInk = dark ? (luminance(primary) > .35 ? mix(primary, [255, 255, 255], .08) : mix(primary, [255, 255, 255], .45)) :
            (luminance(primary) > .35 ? mix(primary, [0, 0, 0], .67) : mix(primary, [0, 0, 0], .22));
        const secondaryInk = dark ? (luminance(secondary) > .35 ? secondary : mix(secondary, [255, 255, 255], .5)) :
            (luminance(secondary) > .35 ? mix(secondary, [0, 0, 0], .67) : mix(secondary, [0, 0, 0], .2));
        const sidebarText = luminance(primary) > .36 ? '#203b38' : '#ffffff';
        // Pastel surfaces stay light even when the surrounding page uses dark mode.
        const surfaceInk = luminance(primary) > .35 ? mix(primary, [0, 0, 0], .67) : mix(primary, [255, 255, 255], .8);
        const secondarySurfaceInk = luminance(secondary) > .35 ? mix(secondary, [0, 0, 0], .67) : mix(secondary, [255, 255, 255], .8);
        const values = {
            '--theme-primary': primary, '--theme-secondary': secondary,
            '--theme-background': background, '--theme-dark-background': darkBackground,
            '--theme-primary-ink': primaryInk, '--theme-secondary-ink': secondaryInk,
            '--theme-surface-ink': surfaceInk, '--theme-secondary-surface-ink': secondarySurfaceInk,
            '--theme-sidebar-text': sidebarText,
            '--primary': primary, '--primary-light': primary, '--primary-dark': primaryInk,
            '--secondary': secondary, '--accent': secondary,
            '--sidebar-bg': primary, '--sidebar-foreground': sidebarText, '--glass-bg': primary,
            '--background': dark ? darkBackground : background,
            '--bs-primary': primary, '--bs-primary-rgb': rgb(primary).join(', '),
            '--bs-link-color': primaryInk,
            '--theme-background-image': !dark && palette.background_image_url ? `url("${palette.background_image_url}")` : 'none'
        };
        [document.documentElement, document.body].filter(Boolean).forEach(element => {
            for (const [key, value] of Object.entries(values)) element.style.setProperty(key, value);
        });
        document.querySelector('meta[name="theme-color"]')?.setAttribute('content', primary);
    }
    window.HasaTheme = { apply, saved };
    apply(saved);
    function initializeForm() {
        apply(saved);
        const form = document.getElementById('appearanceForm');
        if (!form || form.dataset.hasaThemeBound) return;
        form.dataset.hasaThemeBound = 'true';
        const getPalette = () => ({
            ...saved,
            primary_color: form.elements.primary_color.value,
            secondary_color: form.elements.secondary_color.value,
            background_color: form.elements.background_color.value,
            dark_background_color: form.elements.dark_background_color.value,
            background_image_url: previewImageUrl
        });
        form.querySelectorAll('.theme-color-input').forEach(input => input.addEventListener('input', () => apply(getPalette())));
        form.querySelectorAll('.theme-preset').forEach(button => button.addEventListener('click', () => {
            form.elements.primary_color.value = button.dataset.primary;
            form.elements.secondary_color.value = button.dataset.secondary;
            form.elements.background_color.value = button.dataset.background;
            form.elements.dark_background_color.value = button.dataset.dark;
            apply(getPalette());
        }));
        const imageInput = form.elements.background_image;
        imageInput?.addEventListener('change', () => {
            if (imageInput.files?.[0]) {
                previewImageUrl = URL.createObjectURL(imageInput.files[0]);
                apply(getPalette());
            }
        });
        form.elements.remove_background_image?.addEventListener('change', event => {
            previewImageUrl = event.target.checked ? null : saved.background_image_url;
            apply(getPalette());
        });
    }
    document.addEventListener('DOMContentLoaded', initializeForm);
    document.addEventListener('livewire:navigated', initializeForm);
    window.addEventListener('themeChange', () => apply(getCurrentPalette()));
    function getCurrentPalette() {
        const form = document.getElementById('appearanceForm');
        return form ? { ...saved, primary_color: form.elements.primary_color.value, secondary_color: form.elements.secondary_color.value,
            background_color: form.elements.background_color.value, dark_background_color: form.elements.dark_background_color.value,
            background_image_url: previewImageUrl } : saved;
    }
    new MutationObserver(() => apply(getCurrentPalette())).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
})();
