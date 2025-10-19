@php($hidden = $hiddenGroups ?? [])
@if(!empty($hidden))
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const hidden = @json($hidden);
    const groupSelectors = [
        '.fi-sidebar-group',
        '[data-sidebar] .fi-sidebar-group',
        '.fi-topbar-nav-group',
        '[data-top-navigation] .fi-topbar-nav-group'
    ];
    const itemSelectors = [
        '.fi-sidebar-item',
        '[data-sidebar] .fi-sidebar-item',
        '.fi-topbar-item',
        '[data-top-navigation] .fi-topbar-item'
    ];
    const getText = el => el?.textContent?.trim();

    const prune = () => {
        groupSelectors.forEach(sel => document.querySelectorAll(sel).forEach(group => {
            const label = group.querySelector('.fi-sidebar-group-label, .fi-topbar-group-label, [data-group-label]');
            const txt = getText(label);
            if (txt && hidden.includes(txt)) {
                group.remove();
            }
        }));
        // Remove orphaned items still referencing hidden group via attribute
        itemSelectors.forEach(sel => document.querySelectorAll(sel).forEach(item => {
            const attr = item.getAttribute('data-group') || item.getAttribute('data-group-label');
            if (attr && hidden.includes(attr.trim())) {
                item.remove();
            }
        }));
        // Generic fallback: aria-label pattern
        document.querySelectorAll('[aria-label]').forEach(el => {
            const al = el.getAttribute('aria-label');
            if (al && hidden.includes(al.trim())) {
                const container = el.closest('.fi-sidebar-item, .fi-topbar-item, li');
                if (container) container.remove();
            }
        });
    };
    prune();
    new MutationObserver(() => prune()).observe(document.body, { childList: true, subtree: true });
});
</script>
@endif