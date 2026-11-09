
document.addEventListener('DOMContentLoaded', () => {
    const botones = document.querySelectorAll('.toggle-password');

    botones.forEach((boton) => {
        boton.addEventListener('click', () => {
            const contenedor = boton.closest('.password-wrapper');
            const input = contenedor.querySelector('input');

            const mostrar = input.type === 'password';

            input.type = mostrar ? 'text' : 'password';

            boton.classList.toggle('active', mostrar);
            boton.setAttribute(
                'aria-label',
                mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña'
            );
        });
    });
});
