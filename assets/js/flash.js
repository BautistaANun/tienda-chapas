document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.flash');

    if (!flash) return;

    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-10px)';
    }, 3000);

    setTimeout(() => {
        flash.remove();
    }, 3500);
});

