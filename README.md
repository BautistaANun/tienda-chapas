/* =========================
   RESET
========================= */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #ebebeb;
    color: #333;
}

/* =========================
   HEADER MERCADOLIBRE
========================= */
.header {
    background:  darkcyan;
    padding: 1.2rem 2rem;
}

.header h1 {
    text-align: center;
    font-size: 1.8rem;
    margin-bottom: 0.8rem;
}

.header nav {
    display: flex;
    justify-content: center;
    gap: 1.2rem;
    font-size: 0.95rem;
}

.header nav a {
    color: #333;
    font-weight: 500;
}

/* =========================
   CONTENEDOR PRINCIPAL
========================= */
.contenedor {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.5rem;
}


/* =========================
   FLASH
========================= */
.flash {
    background: #fff;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 6px;
    border-left: 5px solid #3483fa;
}

/* =========================
   BUSCADOR
========================= */
form {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

form input[type="text"] {
    flex: 1;
    padding: 0.7rem;
    border-radius: 4px;
    border: 1px solid #ccc;
}

form button {
    background: #3483fa;
    color: #fff;
    border: none;
    padding: 0 1.2rem;
    border-radius: 4px;
    cursor: pointer;
}

/* =========================
   ORDEN
========================= */
.orden {
    margin-bottom: 2rem;
}

.orden a {
    margin-right: 1rem;
    font-size: 0.9rem;
    color: #3483fa;
}

/* =========================
   CATEGORÍA
========================= */
h2 {
    font-size: 1.4rem;
    margin: 2.5rem 0 1.2rem;
}

/* =========================
   GRID PRODUCTOS
========================= */
.productos {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

/* =========================
   CARD PRODUCTO
========================= */
.productos > div {
    background: #fff;
    border-radius: 6px;
    padding: 1rem;
    transition: box-shadow 0.2s;
    border: 1px solid #eee;
}

.productos > div:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.producto-img {
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.8rem;
}

.producto-img img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.productos h3 {
    font-size: 1rem;
    font-weight: normal;
    margin-bottom: 0.4rem;
}

.productos p {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0.6rem;
}

.productos button {
    width: 100%;
    background: #3483fa;
    border: none;
    color: #fff;
    padding: 0.6rem;
    border-radius: 4px;
    cursor: pointer;
}

/* =========================
   PAGINACIÓN
========================= */
.paginacion {
    display: flex;
    justify-content: center;
    margin: 3rem 0;
    gap: 0.4rem;
}

.paginacion a {
    background: #fff;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.paginacion a.activa {
    background: #3483fa;
    color: #fff;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 1024px) {
    .productos {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .productos {
        grid-template-columns: repeat(2, 1fr);
    }

    .header nav {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .productos {
        grid-template-columns: 1fr;
    }
}