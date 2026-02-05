document.addEventListener('DOMContentLoaded', () => {

    /* =========================
       AUTOCOMPLETADO POR DNI
       ========================= */
    const dniInput = document.getElementById('dni');
    if (dniInput) {
        let controller;

        dniInput.addEventListener('blur', () => {
            const dni = dniInput.value.trim();
            if (!/^\d{8}$/.test(dni)) return;

            if (controller) controller.abort();
            controller = new AbortController();

            fetch(`buscar_cliente.php?dni=${dni}`, { signal: controller.signal })
                .then(res => res.json())
                .then(data => {
                    if (!data.encontrado) return;

                    const map = {
                        nombre: data.nombre,
                        apellido: data.apellido,
                        telefono: data.telefono,
                        direccion: data.direccion
                    };

                    Object.entries(map).forEach(([id, value]) => {
                        const input = document.getElementById(id);
                        if (input && !input.value) {
                            input.value = value;
                            input.classList.add('autocompletado');
                        }
                    });
                })
                .catch(() => {});
        });
    }

    /* =========================
       ACTUALIZAR CANTIDAD (AJAX)
       ========================= */
    document.querySelectorAll('.cantidad-form').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(() => location.reload());
        });
    });

    /* =========================
       GUARDAR CLIENTE LOCAL
       ========================= */
    document.querySelector('.checkout-form')?.addEventListener('submit', () => {
        ['nombre','apellido','dni','telefono','direccion'].forEach(id => {
            const input = document.getElementById(id);
            if (input) localStorage.setItem(id, input.value);
        });
    });

    ['nombre','apellido','dni','telefono','direccion'].forEach(id => {
        const input = document.getElementById(id);
        if (input && localStorage.getItem(id)) {
            input.value = localStorage.getItem(id);
        }
    });

});
