<?php
require_once 'clase.php';
require_once 'Camion.php';

// Cargamos el inventario desde la BD y añadimos un camión manual
$miTienda = new Concesionario();
$miTienda->cargarDesdeBaseDeDatos();
$miTienda->agregarVehiculo(new Camion('Volvo', 'FH16', 95000.00, 25000));

$vehiculos = $miTienda->getInventario();
$stats     = $miTienda->getEstadisticas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concesionario VIP — Inventario</title>
    <style>
        /* ── Variables ──────────────────────────────────────────── */
        :root {
            --bg:           #0f172a;
            --bg-2:         #111827;
            --bg-card:      #1e293b;
            --border:       #334155;
            --text:         #e2e8f0;
            --text-muted:   #94a3b8;
            --blue:         #3b82f6;
            --orange:       #f97316;
            --green:        #22c55e;
            --purple:       #a855f7;
            --red:          #ef4444;
            --yellow:       #eab308;
            --radius:       16px;
            --shadow:       0 8px 32px rgba(0,0,0,0.5);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Header ─────────────────────────────────────────────── */
        header {
            background: linear-gradient(135deg, #0f2444 0%, #0f172a 50%, #1a053a 100%);
            padding: 64px 24px 48px;
            text-align: center;
            border-bottom: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 60% at 30% 50%, rgba(59,130,246,.12) 0%, transparent 70%),
                radial-gradient(ellipse 40% 60% at 70% 50%, rgba(168,85,247,.10) 0%, transparent 70%);
            pointer-events: none;
        }

        .header-inner { position: relative; }

        header h1 {
            font-size: clamp(2rem, 5vw, 3.6rem);
            font-weight: 900;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #60a5fa 0%, #c084fc 50%, #f0abfc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-top: 10px;
            letter-spacing: 0.04em;
        }

        /* ── Stats Bar ──────────────────────────────────────────── */
        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 24px 24px;
            flex-wrap: wrap;
            background: var(--bg-2);
            border-bottom: 1px solid var(--border);
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 22px;
            text-align: center;
            min-width: 120px;
            transition: transform .2s, border-color .2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card.blue   { border-color: rgba(59,130,246,.4); }
        .stat-card.orange { border-color: rgba(249,115,22,.4); }
        .stat-card.green  { border-color: rgba(34,197,94,.4); }
        .stat-card.purple { border-color: rgba(168,85,247,.4); }

        .stat-number {
            display: block;
            font-size: 1.9rem;
            font-weight: 800;
            line-height: 1.1;
        }
        .blue   .stat-number { color: var(--blue); }
        .orange .stat-number { color: var(--orange); }
        .green  .stat-number { color: var(--green); }
        .purple .stat-number { color: var(--purple); }

        .stat-label {
            display: block;
            font-size: 0.72rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 4px;
        }

        /* ── Toolbar ─────────────────────────────────────────────── */
        .toolbar {
            max-width: 1280px;
            margin: 28px auto 20px;
            padding: 0 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        #searchInput {
            flex: 1;
            min-width: 200px;
            padding: 11px 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 0.95rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        #searchInput:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }
        #searchInput::placeholder { color: var(--text-muted); }

        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }

        .tab {
            padding: 9px 16px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-card);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all .2s;
            white-space: nowrap;
        }
        .tab:hover   { border-color: var(--blue); color: var(--text); }
        .tab.active  { background: var(--blue); border-color: var(--blue); color: #fff; font-weight: 700; }

        .sort-controls { display: flex; gap: 8px; }

        .sort-btn {
            padding: 9px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-card);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.875rem;
            transition: all .2s;
        }
        .sort-btn:hover  { border-color: var(--purple); color: var(--purple); }
        .sort-btn.active { background: rgba(168,85,247,.15); border-color: var(--purple); color: var(--purple); }

        /* ── Grid ────────────────────────────────────────────────── */
        .grid-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px 60px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        /* ── Cards ───────────────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
            animation: fadeInUp .45s ease-out both;
        }

        .card:hover { transform: translateY(-7px); box-shadow: var(--shadow); }
        .coche:hover  { border-color: rgba(59,130,246,.6); }
        .moto:hover   { border-color: rgba(249,115,22,.6); }
        .camion:hover { border-color: rgba(34,197,94,.6); }

        .card.hidden { display: none; }

        /* Top accent bar */
        .card-accent { height: 4px; }
        .coche-accent  { background: linear-gradient(90deg, #3b82f6, #8b5cf6); }
        .moto-accent   { background: linear-gradient(90deg, #f97316, #fbbf24); }
        .camion-accent { background: linear-gradient(90deg, #22c55e, #14b8a6); }

        .card-icon {
            font-size: 2.8rem;
            padding: 18px 20px 6px;
            line-height: 1;
        }

        .card-body { padding: 2px 20px 20px; }

        .card-body h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }
        .card-body h3 span { color: var(--text-muted); font-weight: 400; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 12px;
        }
        .badge-gas      { background: rgba(239,68,68,.12);  color: #f87171; border: 1px solid rgba(239,68,68,.3); }
        .badge-electric { background: rgba(34,197,94,.12);  color: #4ade80; border: 1px solid rgba(34,197,94,.3); }
        .badge-hybrid   { background: rgba(16,185,129,.12); color: #34d399; border: 1px solid rgba(16,185,129,.3); }
        .badge-diesel   { background: rgba(234,179,8,.12);  color: #fbbf24; border: 1px solid rgba(234,179,8,.3); }

        .price {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 2px;
        }

        .price-discount {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 12px;
        }
        .price-discount strong { color: var(--green); }

        .specs {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 14px;
        }
        .specs li { font-size: 0.85rem; color: var(--text-muted); }

        .card-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 10px;
        }

        /* ── No results ──────────────────────────────────────────── */
        #no-results {
            display: none;
            text-align: center;
            color: var(--text-muted);
            padding: 60px 20px;
            font-size: 1.05rem;
            grid-column: 1 / -1;
        }

        /* ── Footer ──────────────────────────────────────────────── */
        footer {
            text-align: center;
            padding: 24px;
            color: var(--text-muted);
            font-size: 0.82rem;
            border-top: 1px solid var(--border);
            background: var(--bg-2);
        }
        footer strong { color: var(--blue); }

        /* ── Animation ───────────────────────────────────────────── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(22px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .card:nth-child(1)  { animation-delay: .04s; }
        .card:nth-child(2)  { animation-delay: .08s; }
        .card:nth-child(3)  { animation-delay: .12s; }
        .card:nth-child(4)  { animation-delay: .16s; }
        .card:nth-child(5)  { animation-delay: .20s; }
        .card:nth-child(6)  { animation-delay: .24s; }
        .card:nth-child(7)  { animation-delay: .28s; }
        .card:nth-child(8)  { animation-delay: .32s; }
        .card:nth-child(9)  { animation-delay: .36s; }
        .card:nth-child(10) { animation-delay: .40s; }
        .card:nth-child(11) { animation-delay: .44s; }
        .card:nth-child(12) { animation-delay: .48s; }
        .card:nth-child(13) { animation-delay: .52s; }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 640px) {
            .toolbar { flex-direction: column; align-items: stretch; }
            .filter-tabs, .sort-controls { justify-content: center; }
            .stats-bar { gap: 8px; }
        }
    </style>
</head>
<body>

<!-- ═══ Header ════════════════════════════════════════════════════════════════ -->
<header>
    <div class="header-inner">
        <h1>🏎️ Concesionario VIP</h1>
        <p>El mejor inventario de vehículos — <?= date('Y') ?></p>
    </div>
</header>

<!-- ═══ Stats Bar ════════════════════════════════════════════════════════════ -->
<section class="stats-bar">
    <div class="stat-card blue">
        <span class="stat-number"><?= $stats['total'] ?></span>
        <span class="stat-label">Vehículos</span>
    </div>
    <div class="stat-card blue">
        <span class="stat-number"><?= $stats['coches'] ?></span>
        <span class="stat-label">🚗 Coches</span>
    </div>
    <div class="stat-card orange">
        <span class="stat-number"><?= $stats['motos'] ?></span>
        <span class="stat-label">🏍️ Motos</span>
    </div>
    <div class="stat-card green">
        <span class="stat-number"><?= $stats['camiones'] ?></span>
        <span class="stat-label">🚚 Camiones</span>
    </div>
    <div class="stat-card purple">
        <span class="stat-number">$<?= number_format($stats['precio_avg'], 0) ?></span>
        <span class="stat-label">Precio Medio</span>
    </div>
    <div class="stat-card purple">
        <span class="stat-number">$<?= number_format($stats['precio_max'], 0) ?></span>
        <span class="stat-label">Precio Máx.</span>
    </div>
</section>

<!-- ═══ Toolbar ══════════════════════════════════════════════════════════════ -->
<div class="toolbar">
    <input type="search" id="searchInput" placeholder="🔍 Buscar por marca o modelo...">

    <div class="filter-tabs">
        <button class="tab active" data-filter="todos">Todos (<?= $stats['total'] ?>)</button>
        <button class="tab" data-filter="Coche">🚗 Coches (<?= $stats['coches'] ?>)</button>
        <button class="tab" data-filter="Motocicleta">🏍️ Motos (<?= $stats['motos'] ?>)</button>
        <button class="tab" data-filter="Camion">🚚 Camiones (<?= $stats['camiones'] ?>)</button>
    </div>

    <div class="sort-controls">
        <button class="sort-btn" id="sortAsc">↑ Precio</button>
        <button class="sort-btn" id="sortDesc">↓ Precio</button>
    </div>
</div>

<!-- ═══ Grid ══════════════════════════════════════════════════════════════════ -->
<div id="vehiculos-grid" class="grid-container">
    <?php foreach ($vehiculos as $vehiculo): ?>
        <?= $vehiculo->obtenerHtml() ?>
    <?php endforeach; ?>
    <p id="no-results">😔 No se encontraron vehículos con ese criterio.</p>
</div>

<!-- ═══ Footer ═══════════════════════════════════════════════════════════════ -->
<footer>
    Objetos <code>Vehiculo</code> creados en esta sesión:
    <strong><?= Vehiculo::getTotalCreados() ?></strong>
    &nbsp;·&nbsp;
    Precio mínimo en inventario: <strong>$<?= number_format($stats['precio_min'], 2) ?></strong>
</footer>

<script>
    const cards       = document.querySelectorAll('.card');
    const searchInput = document.getElementById('searchInput');
    const tabs        = document.querySelectorAll('.tab');
    const sortBtns    = document.querySelectorAll('.sort-btn');
    const grid        = document.getElementById('vehiculos-grid');
    const noResults   = document.getElementById('no-results');

    let currentFilter = 'todos';
    let currentSearch = '';

    // ── Filtro + Búsqueda ─────────────────────────────────────────────────────
    function applyFilters() {
        let visible = 0;
        cards.forEach(card => {
            if (card.id === 'no-results') return;
            const tipo   = card.dataset.tipo   || '';
            const nombre = (card.dataset.nombre || '').toLowerCase();

            const matchTipo   = currentFilter === 'todos' || tipo === currentFilter;
            const matchSearch = nombre.includes(currentSearch.toLowerCase());

            if (matchTipo && matchSearch) {
                card.classList.remove('hidden');
                visible++;
            } else {
                card.classList.add('hidden');
            }
        });
        noResults.style.display = visible === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', e => {
        currentSearch = e.target.value.trim();
        applyFilters();
    });

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentFilter = tab.dataset.filter;
            applyFilters();
        });
    });

    // ── Ordenar por precio ────────────────────────────────────────────────────
    function sortCards(ascending) {
        const cardsArr = Array.from(cards).filter(c => c.id !== 'no-results');
        cardsArr.sort((a, b) => {
            const pa = parseFloat(a.dataset.precio || 0);
            const pb = parseFloat(b.dataset.precio || 0);
            return ascending ? pa - pb : pb - pa;
        });
        cardsArr.forEach(card => grid.insertBefore(card, noResults));
    }

    document.getElementById('sortAsc').addEventListener('click', function () {
        sortBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        sortCards(true);
    });

    document.getElementById('sortDesc').addEventListener('click', function () {
        sortBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        sortCards(false);
    });
</script>

</body>
</html>
