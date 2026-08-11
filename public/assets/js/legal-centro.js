(function () {
    function buildToc() {
        var prose = document.querySelector('.legal-prose');
        var list = document.getElementById('legal-toc-list');
        if (!prose || !list) return;

        var headings = prose.querySelectorAll('h2[id]');
        if (!headings.length) {
            var toc = document.getElementById('legal-toc');
            if (toc) toc.style.display = 'none';
            return;
        }

        var nav = document.createElement('div');
        nav.className = 'legal-toc-list';

        headings.forEach(function (h) {
            var a = document.createElement('a');
            a.href = '#' + h.id;
            a.textContent = h.textContent;
            a.addEventListener('click', function (e) {
                e.preventDefault();
                h.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.replaceState(null, '', '#' + h.id);
            });
            nav.appendChild(a);
        });

        list.appendChild(nav);

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var id = entry.target.getAttribute('id');
                        nav.querySelectorAll('a').forEach(function (link) {
                            link.classList.toggle('active', link.getAttribute('href') === '#' + id);
                        });
                    }
                });
            }, { rootMargin: '-20% 0px -70% 0px', threshold: 0 });
            headings.forEach(function (h) { observer.observe(h); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildToc);
    } else {
        buildToc();
    }
})();
