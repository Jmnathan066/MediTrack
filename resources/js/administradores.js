
document.addEventListener('DOMContentLoaded', function () {
    const roleInput = document.getElementById('tipoUsuario');
    const roleButtons = document.querySelectorAll('.admin-role-option');

    const pageTitle = document.getElementById('adminPageTitle');
    const pageDescription = document.getElementById('adminPageDescription');
    const cardTitle = document.getElementById('adminCardTitle');
    const cardDescription = document.getElementById('adminCardDescription');
    const submitButton = document.getElementById('adminSubmitButton');

    const epsField = document.getElementById('epsField');
    const epsSelect = document.getElementById('idEPS');

    // Evita errores si este archivo se carga en otra vista.
    if (
        !roleInput ||
        !pageTitle ||
        !pageDescription ||
        !cardTitle ||
        !cardDescription ||
        !submitButton ||
        roleButtons.length === 0
    ) {
        return;
    }

    function seleccionarRol(rol) {
        if (!['admin', 'doctor'].includes(rol)) {
            rol = 'admin';
        }

        const esDoctor = rol === 'doctor';

        roleInput.value = rol;

        pageTitle.textContent = esDoctor
            ? 'Crear doctor'
            : 'Crear administrador';

        pageDescription.textContent = esDoctor
            ? 'Registra nuevos profesionales médicos en MediTrack EPS.'
            : 'Gestiona el acceso de nuevos administradores a MediTrack EPS.';

        cardTitle.textContent = esDoctor
            ? 'Información del doctor'
            : 'Información del administrador';

        cardDescription.textContent = esDoctor
            ? 'Completa los datos para registrar un profesional médico.'
            : 'Completa los datos para registrar una nueva cuenta administrativa.';

        submitButton.textContent = esDoctor
            ? 'Crear doctor'
            : 'Crear administrador';

        // Mostrar u ocultar el selector de EPS.
        if (epsField && epsSelect) {
            epsField.hidden = !esDoctor;
            epsSelect.required = esDoctor;
            epsSelect.disabled = !esDoctor;

            // Si cambia a administrador, se limpia la selección.
            if (!esDoctor) {
                epsSelect.value = '';
            }
        }

        roleButtons.forEach(function (button) {
            const activo = button.dataset.role === rol;

            button.classList.toggle('active', activo);
            button.setAttribute(
                'aria-pressed',
                activo ? 'true' : 'false'
            );
        });
    }

    roleButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            seleccionarRol(button.dataset.role);
        });
    });

    // Inicializa el formulario con el rol seleccionado por Laravel.
    seleccionarRol(roleInput.value);
});
