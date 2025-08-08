Sidebar + Header sin offsets para CRMs en Vue 3
Esqueleto, reglas y checklist para no volver a romper el layout.

1) Objetivo
Definir un patrón único de layout (app shell) que:

Sea mobile-first y accesible.

Evite de raíz el bug de doble offset (gap entre sidebar y contenido).

Sea reutilizable en cualquier CRM que construyamos.

Decisión: modo canónico = Sidebar como columna real en desktop y overlay en mobile.
(Si alguna vez necesitás “sidebar fija” en desktop, usá el patrón alternativo del §7 y no mezcles).

2) Invariantes (reglas que nunca se rompen)
Una sola verdad del ancho del sidebar: --sidebar-w. Nada de repetir “280” en distintos lados.

Modos exclusivos por breakpoint:

< 1024px → overlay (sidebar fuera del flujo, sobre el contenido).

≥ 1024px → columna real (sidebar dentro del flujo, sin position: fixed).

Si el sidebar es columna, el main NO lleva margin-left.

Si el contenedor es flex, sus hijos que crecen llevan min-width: 0.

Header con Grid auto 1fr auto (izq, centro, der). Cero peleas para alinear iconos.

Botón de ícono con contrato fijo (40×40 y SVG centrado).

Z-index y scroll: el overlay del sidebar tapa al header en mobile (z-index > header) y hace body{overflow:hidden}.

3) Estructura HTML (Vue)
vue
Copy
Edit
<!-- AppShell.vue -->
<template>
  <div class="app">
    <aside class="sidebar" :class="{ 'is-open': isMobile && sidebarOpen }">
      <!-- nav -->
    </aside>

    <div class="shell">
      <header class="header">
        <button class="btn-icon" v-if="isMobile" @click="sidebarOpen = true" aria-label="Abrir menú">
          <svg>...</svg>
        </button>
        <div class="header-search"><!-- input --></div>
        <div class="header-actions">
          <button class="btn-icon"><svg><!-- bell --></svg></button>
          <button class="btn-icon"><svg><!-- settings --></svg></button>
        </div>
      </header>

      <main class="content" role="main">
        <router-view />
      </main>
    </div>

    <button v-if="isMobile && sidebarOpen"
            class="scrim"
            aria-label="Cerrar menú"
            @click="sidebarOpen = false" />
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'

const sidebarOpen = ref(false)
const isMobile = ref(true)

const mq = window.matchMedia('(min-width: 1024px)')
const update = () => { isMobile.value = !mq.matches; if (!isMobile.value) sidebarOpen.value = false }
onMounted(() => { update(); mq.addEventListener('change', update) })
onBeforeUnmount(() => mq.removeEventListener('change', update))
</script>
4) Variables y tokens mínimos
css
Copy
Edit
:root{
  --bp-desktop: 1024px;
  --sidebar-w: 280px;
  --header-h: 64px;
  --radius: 6px;
  --gap: 16px;
  --bg-app: #0b0f14;
  --bg-surface: #0f1520;
  --border: #1f2430;
  --primary: #7a5cff;
}
5) CSS canónico (mobile-first)
css
Copy
Edit
/* Reset esencial */
*{box-sizing:border-box} html,body,#app{height:100%} body{margin:0;background:var(--bg-app);color:#e6e6e6}

/* App shell */
.app{min-height:100%; display:flex}

/* Sidebar (mobile = overlay) */
.sidebar{
  position: fixed; inset:0 auto 0 0;
  width: var(--sidebar-w); max-width: 90vw;
  transform: translateX(-100%); transition: transform .2s ease;
  background: var(--bg-surface); border-right:1px solid var(--border);
  z-index: 60; /* > header */
}
.sidebar.is-open{ transform: translateX(0) }

/* Overlay */
.scrim{
  position: fixed; inset:0;
  background: rgba(0,0,0,.5); border:0; padding:0; margin:0;
  z-index: 50;
}

/* Shell (columna flexible) */
.shell{ flex:1 1 auto; min-width:0; display:flex; flex-direction:column }

/* Header: 3 zonas, sticky */
.header{
  position: sticky; top:0; z-index:40;
  height: var(--header-h);
  display:grid; grid-template-columns:auto 1fr auto; align-items:center; gap: var(--gap);
  padding: 0 var(--gap);
  background: var(--bg-surface); border-bottom:1px solid var(--border);
}
.header-search{ max-width: 640px }
@media (max-width: 767px){ .header-search{ display:none } }

/* Botón de icono consistente */
.btn-icon{
  width:40px;height:40px; display:grid; place-items:center;
  border:1px solid var(--border); border-radius: var(--radius); background:transparent;
}
.btn-icon svg{ width:16px;height:16px; display:block; flex-shrink:0 }

/* Contenido */
.content{ padding: 24px; min-width:0 }

/* --- Desktop: sidebar como COLUMNA real --- */
@media (min-width: 1024px){
  /* Sidebar entra al flujo, deja de ser overlay */
  .sidebar{
    position: relative; transform:none; z-index:auto;
    height: auto; inset: unset; /* limpia fixed */
    flex: 0 0 var(--sidebar-w); width: var(--sidebar-w);
  }
  .scrim{ display:none }       /* no hay overlay en desktop */
  .app{ display:flex }         /* ya lo es, pero explícito */
  .shell{ margin-left: 0 }     /* SIN offset extra */
  .header{ /* ancho total del shell, nada que ver con sidebar */ }
}
Por qué esto mata el bug: en desktop el sidebar es una columna real (no fixed), por lo tanto el contenido no necesita margin-left. No hay dos fuentes de separación posibles, así que no puede aparecer el gap marrón.

6) Checklist de integración (copiar a PR template)
Estructura

 Solo existe una definición de --sidebar-w.

 En desktop, la .sidebar no usa position: fixed.

 .shell y .content tienen min-width: 0.

 El header usa Grid auto 1fr auto y no hay utilidades ad-hoc para “empujar” iconos.

Responsive

 En <1024px, el sidebar abre con clase is-open, hay .scrim, y body se bloquea (overflow:hidden) mientras está abierto (agregar si lo necesitás).

 En ≥1024px, el sidebar no tiene overlay y no hay margin-left aplicado al main.

Accesibilidad

 Botón hamburguesa con aria-label.

 Overlay clickeable para cerrar y/o tecla Esc.

 (Opcional) focus-trap dentro del sidebar en mobile.

Render

 No hay reflow extraño al cruzar 1024px (probalo arrastrando la ventana).

 Las tablas hacen scroll horizontal en mobile; no rompen el ancho.

7) Patrón alternativo (solo si querés “sidebar fijo” en desktop)
Úsalo en un proyecto que lo pida; no mezclar.

css
Copy
Edit
@media (min-width: 1024px){
  .sidebar{
    position: fixed; top:0; left:0; height:100vh;
    width: var(--sidebar-w); transform:none; z-index: 30;
  }
  .shell{ margin-left: var(--sidebar-w) }  /* ÚNICO offset permitido */
}
Si elegís este patrón, eliminá cualquier flex: 0 0 var(--sidebar-w) del sidebar en desktop (ya no es una columna del flex), y no uses .scrim.

8) Pitfalls comunes (y su diagnóstico rápido)
Gap marrón entre sidebar y contenido: estás aplicando dos offsets (sidebar columna + margin-left en shell) → quitá el margin-left.

Iconos desalineados o pegados: botones sin contrato (usa .btn-icon) o SVGs con viewBox raros → normalizá el tamaño del SVG a 16×16.

Contenido que “empuja” y desborda: te falta min-width:0 en .shell o .content.

Header que “se mete” bajo el sidebar en mobile: overlay con z-index bajo → z-index del sidebar > header (60 vs 40).

Scroll del body bajo overlay: agregá/capá overflow:hidden en <body> mientras sidebarOpen.

9) Procedimiento de QA (5 minutos)
Mobile (iPhone XR/414): abrir/cerrar sidebar, probar overlay, Esc/Click, scroll lock.

Breakpoint 1024px: arrastrar ventana y verificar que no hay salto ni reflow raro.

Desktop ancho: sidebar visible como columna; no hay overlay, no hay margen lateral.

Tablas largas: que el contenedor tenga overflow:auto; -webkit-overflow-scrolling:touch.

Modales/menus: confirmar que su z-index supera al header (mapa simple: overlay 60, header 40, modales 80).

10) Snippets utilitarios (opcionales)
Bloqueo de scroll en body (cuando está abierto el overlay)

ts
Copy
Edit
watch(() => sidebarOpen.value, (open) => {
  document.documentElement.style.overflow = open && isMobile.value ? 'hidden' : ''
})
Focus-trap simple

Al abrir: sidebarRef.querySelector('[tabindex],button,a,input,select,textarea')?.focus()

Al cerrar: devolver el foco al botón hamburguesa.

11) Cómo migrar si ya tenés código “mezclado”
En el CSS de desktop, decidí: ¿columna real o fixed?

Eliminá la alternativa que no corresponda:

Si vas por columna: quitá cualquier position: fixed del sidebar y borra margin-left del shell.

Si vas por fixed: dejá fixed y mantené margin-left: var(--sidebar-w); asegurate de que el sidebar no es hijo del flex (no flex: 0 0 ...).

Definí --sidebar-w en un solo lugar.

Agregá min-width:0 en .shell y .content.

Probá el QA del §9.