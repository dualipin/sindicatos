function floatingElementScroll() {
    const floatingElements = document.getElementById('floatingElements');

    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (floatingElements) {
        if (scrollTop > 10) {
            floatingElements.classList.remove('d-none');
        } else {
            floatingElements.classList.add('d-none');
        }
    }
}

addEventListener('DOMContentLoaded', (e) => {
    floatingElementScroll();
})
addEventListener('scroll', floatingElementScroll);