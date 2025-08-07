# Sistema de Diseño BAMBU Dashboard

## Sistema Tipográfico

### Jerarquía de Texto
- **Page Title**: `text-2xl font-bold` (32px, 700)
- **Section Title**: `text-lg font-semibold` (18px, 600)  
- **Card Title**: `text-sm font-medium uppercase tracking-wider` (14px, 500)
- **Primary Values**: `text-3xl font-bold tracking-tight` (30px, 700)
- **Secondary Values**: `text-lg font-bold` (18px, 700)
- **Body Text**: `text-sm font-medium` (14px, 500)
- **Secondary Text**: `text-sm` (14px, 400)
- **Metadata/Labels**: `text-xs` (12px, 400)
- **Table Headers**: `text-xs font-semibold uppercase tracking-wider` (12px, 600)

### Colores de Texto
- **Primary**: `text-white`
- **Secondary**: `text-slate-400` 
- **Tertiary**: `text-slate-500`
- **Success**: `text-green-400`
- **Warning**: `text-orange-400`
- **Error**: `text-red-400`
- **Info**: `text-blue-400`

## Sistema de Espaciado

### Padding/Margin
- **Cards**: `p-6` (24px)
- **Small Elements**: `p-4` (16px)  
- **Buttons**: `px-3 py-1.5` o `px-4 py-2`
- **Table Cells**: `px-6 py-4`
- **Badges**: `px-3 py-1.5`

### Gaps
- **Page Sections**: `gap-6` (24px)
- **Card Grid**: `gap-4` (16px)
- **Element Groups**: `gap-3` (12px)
- **Inline Elements**: `gap-2` (8px)

### Margins
- **Section Separation**: `mb-6` (24px)
- **Element Separation**: `mb-4` (16px)
- **Text Elements**: `mb-3`, `mt-1`

## Alturas y Proporciones

### Elementos Fijos
- **Metric Cards**: `h-[120px]`
- **Table Rows**: `h-16` (64px)
- **Buttons**: `h-10` o `h-8`
- **Chart Areas**: `h-64` (256px)
- **Mini Charts**: `h-40` (160px)

### Contenedores
- **Border Radius**: `rounded-lg` (8px)
- **Borders**: `border border-slate-700`
- **Hover States**: `hover:border-slate-600`

## Estados y Colores

### Backgrounds
- **Cards**: `bg-slate-800`
- **Hover States**: `bg-slate-900/30`
- **Chart Areas**: `bg-slate-900/50`
- **Overlays**: `bg-slate-900/80`

### Status Colors
- **Success/Entregado**: `bg-green-500/20 text-green-400 border-green-500/30`
- **Info/En camino**: `bg-blue-500/20 text-blue-400 border-blue-500/30`
- **Warning/Preparando**: `bg-orange-500/20 text-orange-400 border-orange-500/30`
- **Pending**: `bg-amber-500/20 text-amber-400 border-amber-500/30`
- **Error/Cancelado**: `bg-red-500/20 text-red-400 border-red-500/30`

### Icon Colors
- **Ventas**: `text-green-400 bg-green-500/20`
- **Pedidos**: `text-blue-400 bg-blue-500/20`  
- **Clientes**: `text-purple-400 bg-purple-500/20`
- **Entregas**: `text-orange-400 bg-orange-500/20`

## Patrones de Componentes

### Metric Cards
```vue
<div class="bg-slate-800 rounded-lg border border-slate-700 p-6 h-[120px]">
  <!-- Icon + Title Header -->
  <!-- Large Value -->
  <!-- Trend Badge -->
</div>
```

### Section Headers
```vue
<div class="flex items-center justify-between mb-6">
  <div>
    <h2 class="text-lg font-semibold text-white">Title</h2>
    <p class="text-sm text-slate-400 mt-1">Subtitle</p>
  </div>
  <action-element />
</div>
```

### Table Headers
```vue
<th class="text-left px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">
  Header
</th>
```

### Status Badges
```vue
<span class="px-3 py-1.5 text-xs font-semibold rounded-full border bg-{color}-500/20 text-{color}-400 border-{color}-500/30">
  Status
</span>
```

## Transiciones

### Duración Estándar
- **Hover Effects**: `duration-200`
- **Color Changes**: `transition-colors`
- **All Properties**: `transition-all`

### Estados Hover
- **Cards**: `hover:border-slate-600`
- **Rows**: `hover:bg-slate-900/30`
- **Buttons**: `hover:bg-slate-800 hover:text-white`

## Consistencia Visual

### Reglas de Oro
1. **Usar siempre las alturas fijas especificadas**
2. **Mantener el padding de 24px (`p-6`) en cards principales**
3. **Aplicar tracking-wider en texto uppercase**
4. **Usar opacidad /20, /30, /50 para transparencias**
5. **Mantener font-weights específicos por jerarquía**
6. **Espaciar secciones con gap-6, elementos con gap-4**

### Anti-patrones a Evitar
- ❌ Mezclar `px-4` y `px-6` en la misma sección
- ❌ Usar `text-base` cuando debería ser `text-sm` 
- ❌ Alturas automáticas en cards principales
- ❌ Colores custom fuera del sistema definido
- ❌ Inconsistencia en border-radius (usar siempre `rounded-lg`)