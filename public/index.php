<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Baramovil - Productos</title>
    <style>
      :root {
        --gray-900: #111827;
        --gray-700: #374151;
        --gray-500: #6b7280;
        --gray-100: #f3f4f6;
        --green-700: #15803d;
        --white: #ffffff;
        --shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: var(--gray-100);
        color: var(--gray-900);
      }

      .top-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--white);
        padding: 12px 16px;
        box-shadow: var(--shadow);
        position: sticky;
        top: 0;
        z-index: 10;
      }

      .burger-btn {
        border: none;
        background: transparent;
        font-size: 26px;
        cursor: pointer;
      }

      .title {
        margin: 0;
        font-size: 20px;
      }

      .layout {
        display: flex;
      }

      .sidebar {
        width: 300px;
        max-width: 82vw;
        background: var(--white);
        box-shadow: var(--shadow);
        min-height: calc(100vh - 56px);
        transform: translateX(-100%);
        transition: transform 0.2s ease;
        position: fixed;
        top: 56px;
        left: 0;
        z-index: 20;
        overflow-y: auto;
      }

      .sidebar.open {
        transform: translateX(0);
      }

      .menu-section {
        border-bottom: 1px solid #e5e7eb;
      }

      .menu-title {
        margin: 0;
        padding: 14px 16px;
        font-size: 16px;
        cursor: pointer;
        background: #fafafa;
      }

      .submenu {
        margin: 0;
        padding: 0;
        list-style: none;
      }

      .submenu li button {
        border: none;
        width: 100%;
        text-align: left;
        background: transparent;
        padding: 12px 16px;
        cursor: pointer;
        color: var(--gray-700);
      }

      .submenu li button:hover {
        background: #f9fafb;
      }

      .content {
        width: 100%;
        padding: 14px;
      }

      .filters-resume {
        margin-bottom: 12px;
        color: var(--gray-500);
      }

      .cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 12px;
      }

      .card {
        display: flex;
        gap: 12px;
        background: var(--white);
        border-radius: 10px;
        box-shadow: var(--shadow);
        padding: 10px;
      }

      .card img {
        width: 92px;
        height: 92px;
        object-fit: cover;
        border-radius: 8px;
        background: #f3f4f6;
      }

      .card-body {
        display: flex;
        flex-direction: column;
      }

      .product-description {
        margin: 0 0 4px;
        color: #000;
        font-size: 15px;
      }

      .product-code {
        margin: 0 0 4px;
        color: var(--gray-500);
        font-size: 12px;
      }

      .prices {
        margin: 0;
        color: var(--green-700);
        font-size: 12px;
        font-weight: bold;
      }

      .search-box {
        margin-left: auto;
      }

      .search-box input {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 8px;
        width: min(360px, 52vw);
      }

      @media (max-width: 640px) {
        .search-box {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <header class="top-bar">
      <button id="burgerBtn" class="burger-btn" aria-label="Abrir menú">☰</button>
      <h1 class="title">Productos</h1>
      <div class="search-box">
        <input id="searchInput" type="text" placeholder="Buscar por código o descripción" />
      </div>
    </header>

    <div class="layout">
      <aside id="sidebar" class="sidebar" aria-label="Navegación lateral">
        <section class="menu-section">
          <h2 class="menu-title">Productos</h2>
          <ul class="submenu">
            <li><button id="showAllProducts">Todos los productos</button></li>
          </ul>
        </section>

        <section class="menu-section">
          <h2 class="menu-title">Departamento</h2>
          <ul id="departmentsMenu" class="submenu"></ul>
        </section>

        <section class="menu-section">
          <h2 class="menu-title">Categoria</h2>
          <ul id="categoriesMenu" class="submenu"></ul>
        </section>
      </aside>

      <main class="content">
        <div id="filtersResume" class="filters-resume">Mostrando todos los productos</div>
        <section id="cardsContainer" class="cards"></section>
      </main>
    </div>

    <script>
      const state = {
        department: '',
        category: '',
        search: ''
      };

      const elements = {
        sidebar: document.getElementById('sidebar'),
        burgerBtn: document.getElementById('burgerBtn'),
        departmentsMenu: document.getElementById('departmentsMenu'),
        categoriesMenu: document.getElementById('categoriesMenu'),
        cardsContainer: document.getElementById('cardsContainer'),
        showAllProducts: document.getElementById('showAllProducts'),
        filtersResume: document.getElementById('filtersResume'),
        searchInput: document.getElementById('searchInput')
      };

      function formatMoney(value) {
        return new Intl.NumberFormat('es-VE', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }).format(Number(value || 0));
      }

      function buildFiltersQuery() {
        const query = new URLSearchParams();
        if (state.department) query.set('department', state.department);
        if (state.category) query.set('category', state.category);
        if (state.search) query.set('search', state.search);
        return query.toString();
      }

      async function getJson(url) {
        const response = await fetch(url);
        if (!response.ok) {
          throw new Error(`Error ${response.status}`);
        }
        return response.json();
      }

      function createCard(product) {
        const article = document.createElement('article');
        article.className = 'card';

        const img = document.createElement('img');
        img.src = product.imageUrl;
        img.alt = `Imagen de ${product.descripcion}`;
        img.loading = 'lazy';
        img.onerror = () => {
          img.src = 'https://placehold.co/92x92?text=Sin+Imagen';
        };

        const body = document.createElement('div');
        body.className = 'card-body';

        const description = document.createElement('p');
        description.className = 'product-description';
        description.textContent = product.descripcion;

        const code = document.createElement('p');
        code.className = 'product-code';
        code.textContent = product.codigo;

        const prices = document.createElement('p');
        prices.className = 'prices';
        prices.textContent = `BS ${formatMoney(product.bs)} - COP ${formatMoney(product.cop)} - USD ${formatMoney(product.usd)}`;

        body.append(description, code, prices);
        article.append(img, body);

        return article;
      }

      function renderCards(products) {
        elements.cardsContainer.innerHTML = '';

        if (products.length === 0) {
          elements.cardsContainer.innerHTML = '<p>No hay productos para los filtros seleccionados.</p>';
          return;
        }

        const fragment = document.createDocumentFragment();
        products.forEach((product) => {
          fragment.appendChild(createCard(product));
        });

        elements.cardsContainer.appendChild(fragment);
      }

      function updateFiltersResume() {
        const parts = [];
        if (state.department) parts.push(`Departamento: ${state.department}`);
        if (state.category) parts.push(`Categoría: ${state.category}`);
        if (state.search) parts.push(`Búsqueda: ${state.search}`);

        elements.filtersResume.textContent = parts.length
          ? `Mostrando productos filtrados por ${parts.join(' | ')}`
          : 'Mostrando todos los productos';
      }

      async function loadProducts() {
        const query = buildFiltersQuery();
        const endpoint = query ? `/api/products?${query}` : '/api/products';
        const products = await getJson(endpoint);
        renderCards(products);
        updateFiltersResume();
      }

      async function loadDepartments() {
        const departments = await getJson('/api/departments');
        elements.departmentsMenu.innerHTML = '';

        departments.forEach((department) => {
          const li = document.createElement('li');
          const button = document.createElement('button');
          button.textContent = `${department.DEP_CODIGO} - ${department.DEP_DESCRIPCION}`;
          button.onclick = async () => {
            state.department = department.DEP_CODIGO;
            elements.sidebar.classList.remove('open');
            await loadProducts();
          };
          li.appendChild(button);
          elements.departmentsMenu.appendChild(li);
        });
      }

      async function loadCategories() {
        const categories = await getJson('/api/categories');
        elements.categoriesMenu.innerHTML = '';

        categories.forEach((category) => {
          const li = document.createElement('li');
          const button = document.createElement('button');
          button.textContent = `${category.CAT_CODIGO} - ${category.CAT_DESCRIPCION}`;
          button.onclick = async () => {
            state.category = category.CAT_CODIGO;
            elements.sidebar.classList.remove('open');
            await loadProducts();
          };
          li.appendChild(button);
          elements.categoriesMenu.appendChild(li);
        });
      }

      elements.burgerBtn.addEventListener('click', () => {
        elements.sidebar.classList.toggle('open');
      });

      elements.showAllProducts.addEventListener('click', async () => {
        state.department = '';
        state.category = '';
        elements.sidebar.classList.remove('open');
        await loadProducts();
      });

      let timer = null;
      elements.searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(async () => {
          state.search = elements.searchInput.value.trim();
          await loadProducts();
        }, 300);
      });

      async function init() {
        try {
          await Promise.all([loadDepartments(), loadCategories()]);
          await loadProducts();
        } catch (error) {
          elements.cardsContainer.innerHTML = `<p>Error al cargar la información: ${error.message}</p>`;
        }
      }

      init();
    </script>
  </body>
</html>
