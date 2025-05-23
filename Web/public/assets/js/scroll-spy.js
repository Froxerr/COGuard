document.addEventListener('DOMContentLoaded', function() {
    // Sadece anasayfada scroll spy'ı aktif et
    if (window.location.pathname === '/') {
        const sections = document.querySelectorAll('section[id]');
        
        window.addEventListener('scroll', function() {
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                
                if (pageYOffset >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });
            
            // Menü linklerini güncelle
            document.querySelectorAll('.navmenu a').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });

        // Menü linklerine tıklandığında smooth scroll
        document.querySelectorAll('.navmenu a[href^="/#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const hash = this.getAttribute('href').split('#')[1];
                const target = document.getElementById(hash);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }
}); 