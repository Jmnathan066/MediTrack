
/* =========================================
   MENÚ DEL USUARIO
========================================= */

function toggleMenu() {
    const menu = document.getElementById('menu');
    const button = document.getElementById('profileButton');

    if (!menu) return;

    menu.classList.toggle('hidden');

    if (button) {
        button.setAttribute(
            'aria-expanded',
            String(!menu.classList.contains('hidden'))
        );
    }
}

window.toggleMenu = toggleMenu;


/* =========================================
   SUBMENÚ DE MEDICAMENTOS
========================================= */

function toggleMedicamentos() {
    const submenu = document.getElementById('submenuMedicamentos');

    if (submenu) {
        submenu.classList.toggle('hidden');
    }
}

window.toggleMedicamentos = toggleMedicamentos;


/* =========================================
   SIDEBAR: CONTRAER Y EXPANDIR
========================================= */

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarButton = document.getElementById('sidebarToggle');

    if (!sidebar) return;

    const isCollapsed = sidebar.classList.toggle('collapsed');

    document.querySelectorAll('.main-content, .content').forEach(content => {
        content.classList.toggle('expanded', isCollapsed);
    });

    // Ocultar cualquier tooltip al cambiar el estado.
    hideSidebarTooltip();

    try {
        localStorage.setItem('sidebarCollapsed', String(isCollapsed));
    } catch (error) {
        console.warn('No se pudo guardar el estado del menú.');
    }

    if (sidebarButton) {
        sidebarButton.setAttribute(
            'aria-expanded',
            String(!isCollapsed)
        );
    }
}

window.toggleSidebar = toggleSidebar;


/* =========================================
   INICIALIZAR EL ESTADO DEL SIDEBAR
========================================= */

function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarButton = document.getElementById('sidebarToggle');

    if (!sidebar) return;

    let isCollapsed = false;

    try {
        isCollapsed =
            localStorage.getItem('sidebarCollapsed') === 'true';
    } catch (error) {
        // Si el almacenamiento no está disponible,
        // el menú comienza expandido.
    }

    sidebar.classList.toggle('collapsed', isCollapsed);

    document.querySelectorAll('.main-content, .content').forEach(content => {
        content.classList.toggle('expanded', isCollapsed);
    });

    if (sidebarButton) {
        sidebarButton.setAttribute(
            'aria-expanded',
            String(!isCollapsed)
        );
    }
}


/* =========================================
   TOOLTIP DEL SIDEBAR CONTRAÍDO
========================================= */

let sidebarTooltipElement = null;

function hideSidebarTooltip() {
    if (sidebarTooltipElement) {
        sidebarTooltipElement.classList.remove('visible');
        sidebarTooltipElement.textContent = '';
    }
}

function initializeSidebarTooltips() {
    const sidebar = document.getElementById('sidebar');

    if (!sidebar) return;

    // Evitar registrar los mismos eventos más de una vez.
    if (sidebar.dataset.tooltipsConfigured === 'true') return;

    sidebar.dataset.tooltipsConfigured = 'true';

    // Crear el tooltip fuera del sidebar para que no se recorte.
    sidebarTooltipElement = document.createElement('div');
    sidebarTooltipElement.className = 'sidebar-tooltip';
    sidebarTooltipElement.setAttribute('role', 'tooltip');
    sidebarTooltipElement.setAttribute('aria-hidden', 'true');

    document.body.appendChild(sidebarTooltipElement);

    function showSidebarTooltip(item) {
        // Mostrar los nombres solo si el sidebar está contraído.
        if (!sidebar.classList.contains('collapsed')) {
            hideSidebarTooltip();
            return;
        }

        // Obtener el texto directamente del elemento Blade.
        const textElement = item.querySelector('.menu-text');

        if (!textElement) {
            hideSidebarTooltip();
            return;
        }

        const label = textElement.textContent.trim();

        if (!label) {
            hideSidebarTooltip();
            return;
        }

        const sidebarRect = sidebar.getBoundingClientRect();
        const itemRect = item.getBoundingClientRect();

        sidebarTooltipElement.textContent = label;
        sidebarTooltipElement.setAttribute('aria-hidden', 'false');

        // Posición inicial para medir las dimensiones.
        sidebarTooltipElement.style.left = '0px';
        sidebarTooltipElement.style.top = '0px';

        sidebarTooltipElement.classList.add('visible');

        const tooltipRect =
            sidebarTooltipElement.getBoundingClientRect();

        // Colocar el recuadro a la derecha del sidebar.
        let left = sidebarRect.right + 12;

        // Evitar que el tooltip salga por el borde derecho.
        const maxLeft = window.innerWidth - tooltipRect.width - 8;

        left = Math.min(left, maxLeft);
        left = Math.max(8, left);

        // Centrar el tooltip con respecto a la opción.
        let top = itemRect.top + itemRect.height / 2;

        // Mantener el recuadro dentro de la pantalla verticalmente.
        const halfHeight = tooltipRect.height / 2;

        top = Math.max(
            halfHeight + 8,
            Math.min(
                top,
                window.innerHeight - halfHeight - 8
            )
        );

        sidebarTooltipElement.style.left = `${left}px`;
        sidebarTooltipElement.style.top = `${top}px`;
    }

    // Registrar los eventos de todas las opciones existentes.
    sidebar.querySelectorAll('.menu-item, .menu-title').forEach(item => {
        item.addEventListener('mouseenter', () => {
            showSidebarTooltip(item);
        });

        item.addEventListener('mouseleave', () => {
            hideSidebarTooltip();
        });

        // También permite mostrar el nombre con navegación por teclado.
        item.addEventListener('focus', () => {
            showSidebarTooltip(item);
        });

        item.addEventListener('blur', () => {
            hideSidebarTooltip();
        });
    });

    // Ocultar el recuadro al salir del menú.
    sidebar.addEventListener('mouseleave', hideSidebarTooltip);

    // Ocultarlo cuando se desplaza el sidebar.
    sidebar.addEventListener('scroll', hideSidebarTooltip);

    // Ocultarlo al cambiar el tamaño o desplazarse por la página.
    window.addEventListener('resize', hideSidebarTooltip);
    window.addEventListener('scroll', hideSidebarTooltip, true);
}


/* =========================================
   MODO CLARO / OSCURO
========================================= */

function applyTheme(isDark) {
    document.documentElement.classList.toggle('dark-mode', isDark);

    const toggle = document.getElementById('themeToggle');
    const label = document.getElementById('themeLabel');

    if (toggle) {
        toggle.setAttribute('aria-checked', String(isDark));
    }

    if (label) {
        label.textContent = isDark ? 'Modo claro' : 'Modo oscuro';
    }
}

function toggleTheme() {
    const isDark =
        !document.documentElement.classList.contains('dark-mode');

    applyTheme(isDark);

    try {
        localStorage.setItem(
            'meditrack-theme',
            isDark ? 'dark' : 'light'
        );
    } catch (error) {
        console.warn('No se pudo guardar la preferencia del tema.');
    }
}

window.toggleTheme = toggleTheme;


/* =========================================
   CARGAR EL TEMA GUARDADO
========================================= */

(function initializeTheme() {
    let savedTheme = 'light';

    try {
        savedTheme =
            localStorage.getItem('meditrack-theme') || 'light';
    } catch (error) {
        // Si el almacenamiento no está disponible,
        // se utiliza el tema claro.
    }

    applyTheme(savedTheme === 'dark');
})();


/* =========================================
   INICIALIZACIÓN GENERAL
========================================= */

function initializePartials() {
    initializeSidebar();
    initializeSidebarTooltips();

    const sidebarButton = document.getElementById('sidebarToggle');
    const themeButton = document.getElementById('themeToggle');

    // Configurar el botón del sidebar una sola vez.
    if (
        sidebarButton &&
        sidebarButton.dataset.sidebarConfigured !== 'true'
    ) {
        sidebarButton.dataset.sidebarConfigured = 'true';
        sidebarButton.addEventListener('click', toggleSidebar);
    }

    // Configurar el botón del tema una sola vez.
    if (
        themeButton &&
        themeButton.dataset.themeConfigured !== 'true'
    ) {
        themeButton.dataset.themeConfigured = 'true';
        themeButton.addEventListener('click', toggleTheme);
    }
}


/* =========================================
   ARRANCAR CUANDO EL DOCUMENTO ESTÉ LISTO
========================================= */

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        initializePartials
    );
} else {
    initializePartials();
}
