document.getElementById('theme-toggle').addEventListener('click', () => {
    const current = localStorage.getItem('theme') || 'light';
    const next = current === 'light' ? 'dark' : 'light';

    localStorage.setItem('theme', next);
    document.documentElement.setAttribute('data-bs-theme', next);
});