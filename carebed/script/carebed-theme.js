(function(){
    function effectiveTheme(value){
        if(value === 'system'){
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        }
        return value === 'light' ? 'light' : 'dark';
    }
    function setIcons(theme){
        document.querySelectorAll('.icon-sun').forEach(function(icon){ icon.style.display = theme === 'light' ? 'none' : 'block'; });
        document.querySelectorAll('.icon-moon').forEach(function(icon){ icon.style.display = theme === 'light' ? 'block' : 'none'; });
    }
    window.CarebedTheme = {
        apply: function(value){
            var stored = value || localStorage.getItem('theme') || 'dark';
            var theme = effectiveTheme(stored);
            document.documentElement.setAttribute('data-theme', theme);
            document.body && document.body.setAttribute('data-theme', theme);
            localStorage.setItem('theme', stored);
            setIcons(theme);
            var select = document.getElementById('theme-select');
            if(select){ select.value = stored; }
        },
        toggle: function(){
            var current = document.documentElement.getAttribute('data-theme') || effectiveTheme(localStorage.getItem('theme') || 'dark');
            this.apply(current === 'light' ? 'dark' : 'light');
        }
    };
    function init(){
        window.CarebedTheme.apply(localStorage.getItem('theme') || 'dark');
        document.querySelectorAll('#theme-toggle').forEach(function(btn){
            if(btn.dataset.themeReady === '1') return;
            btn.dataset.themeReady = '1';
            btn.addEventListener('click', function(e){ e.preventDefault(); window.CarebedTheme.toggle(); });
        });
        var select = document.getElementById('theme-select');
        if(select && select.dataset.themeReady !== '1'){
            select.dataset.themeReady = '1';
            select.value = localStorage.getItem('theme') || 'dark';
            select.addEventListener('change', function(){ window.CarebedTheme.apply(this.value); });
        }
        document.querySelectorAll('.settings-nav-link[data-tab]').forEach(function(link){
            if(link.dataset.tabReady === '1') return;
            link.dataset.tabReady = '1';
            link.addEventListener('click', function(e){
                e.preventDefault();
                var tab = this.getAttribute('data-tab');
                document.querySelectorAll('.settings-nav-link').forEach(function(a){ a.classList.remove('active'); });
                this.classList.add('active');
                document.querySelectorAll('.settings-tab-content').forEach(function(panel){ panel.classList.remove('active'); panel.style.display='none'; });
                var target = document.getElementById('tab-' + tab);
                if(target){ target.classList.add('active'); target.style.display='block'; }
            });
        });
        var activePanel = document.querySelector('.settings-tab-content.active');
        if(activePanel){
            document.querySelectorAll('.settings-tab-content').forEach(function(panel){ if(panel !== activePanel) panel.style.display='none'; });
            activePanel.style.display='block';
        }
    }
    if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
