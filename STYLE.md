# Guía de Estilo - Control de Gastos Cáceres

## Información General del Proyecto

- **Nombre**: Control de Gastos Cáceres
- **Framework Backend**: Laravel 11 con Filament 3
- **Framework CSS**: Tailwind CSS v4
- **Fuente Principal**: Instrument Sans (fallback: ui-sans-serif, system-ui)
- **Fuente Monoespaciada**: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas

## Paleta de Colores

### Color Principal (Primary)
```css
--color-primary: #B50E1A
```
Este es un rojo intenso que se utiliza como color principal en toda la aplicación.

### Escala de Grises
```css
--color-gray-50: var(--gray-50)
--color-gray-100: var(--gray-100)
--color-gray-200: var(--gray-200)
--color-gray-300: var(--gray-300)
--color-gray-400: var(--gray-400)
--color-gray-500: var(--gray-500)
--color-gray-600: var(--gray-600)
--color-gray-700: var(--gray-700)
--color-gray-900: var(--gray-900)
--color-gray-950: var(--gray-950)
```

### Colores Base
```css
--color-black: #000
--color-white: #fff
--color-sky-400: oklch(74.6% .16 232.661)
```

## Tipografía

### Familia de Fuentes
```css
--font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 
             'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 
             'Noto Color Emoji'

--font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 
             'Liberation Mono', 'Courier New', monospace
```

### Tamaños de Texto
```css
--text-xs: 0.75rem      /* 12px - line-height: 1.33 */
--text-sm: 0.875rem     /* 14px - line-height: 1.43 */
--text-base: 1rem       /* 16px - line-height: 1.5 */
--text-lg: 1.125rem     /* 18px - line-height: 1.56 */
--text-xl: 1.25rem      /* 20px - line-height: 1.4 */
--text-2xl: 1.5rem      /* 24px - line-height: 1.33 */
--text-3xl: 1.875rem    /* 30px - line-height: 1.2 */
```

### Pesos de Fuente
```css
--font-weight-thin: 100
--font-weight-extralight: 200
--font-weight-light: 300
--font-weight-normal: 400
--font-weight-medium: 500
--font-weight-semibold: 600
--font-weight-bold: 700
--font-weight-extrabold: 800
--font-weight-black: 900
```

### Espaciado de Letras (Letter Spacing)
```css
--tracking-tighter: -0.05em
--tracking-tight: -0.025em
```

### Altura de Línea (Line Height)
```css
--leading-loose: 2
```

## Espaciado

### Sistema de Espaciado Base
```css
--spacing: 0.25rem  /* 4px - unidad base */
```

Todos los espaciados se calculan multiplicando esta unidad base:
- `calc(var(--spacing) * 1)` = 4px
- `calc(var(--spacing) * 2)` = 8px
- `calc(var(--spacing) * 3)` = 12px
- `calc(var(--spacing) * 4)` = 16px
- etc.

## Bordes y Radios

### Border Radius
```css
--radius-md: 0.375rem   /* 6px */
--radius-lg: 0.5rem     /* 8px */
--radius-xl: 0.75rem    /* 12px */
```

### Estilo de Borde
```css
--tw-border-style: solid
```

## Breakpoints Responsivos

```css
--breakpoint-sm: 40rem    /* 640px */
--breakpoint-md: 48rem    /* 768px */
--breakpoint-lg: 64rem    /* 1024px */
--breakpoint-xl: 80rem    /* 1280px */
--breakpoint-2xl: 96rem   /* 1536px */
```

## Contenedores

```css
--container-3xs: 16rem    /* 256px */
--container-xs: 20rem     /* 320px */
--container-sm: 24rem     /* 384px */
--container-md: 28rem     /* 448px */
--container-lg: 32rem     /* 512px */
--container-xl: 36rem     /* 576px */
--container-2xl: 42rem    /* 672px */
--container-3xl: 48rem    /* 768px */
--container-4xl: 56rem    /* 896px */
--container-5xl: 64rem    /* 1024px */
--container-6xl: 72rem    /* 1152px */
--container-7xl: 80rem    /* 1280px */
```

## Transiciones y Animaciones

### Duración de Transiciones
```css
--default-transition-duration: 0.15s
--tw-duration: 75ms  /* para interacciones rápidas */
```

### Funciones de Tiempo (Easing)
```css
--ease-in: cubic-bezier(0.4, 0, 1, 1)
--ease-out: cubic-bezier(0, 0, 0.2, 1)
--ease-in-out: cubic-bezier(0.4, 0, 0.2, 1)
--default-transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1)
```

### Animaciones Predefinidas
```css
--animate-spin: spin 1s linear infinite
--animate-pulse: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite
```

## Sombras

### Sombras de Caja
```css
/* Sombra suave (equivalente a shadow-sm en Tailwind) */
box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 
            0 1px 2px -1px rgba(0, 0, 0, 0.1);
```

### Variables de Sombra
```css
--tw-shadow: 0 0 #0000
--tw-shadow-color: initial
--tw-shadow-alpha: 100%
--tw-inset-shadow: 0 0 #0000
--tw-ring-shadow: 0 0 #0000
```

## Componentes Filament

### Badges (Insignias)
```css
.fi-badge {
  min-width: 1.5rem;
  padding: calc(var(--spacing) * 1) calc(var(--spacing) * 2);
  border-radius: var(--radius-md);
  font-size: var(--text-xs);
  font-weight: var(--font-weight-medium);
  background-color: var(--gray-50);
  color: var(--gray-600);
}
```

### Botones
```css
.fi-btn {
  padding: calc(var(--spacing) * 2) calc(var(--spacing) * 3);
  border-radius: var(--radius-lg);
  font-size: var(--text-sm);
  font-weight: var(--font-weight-medium);
  gap: calc(var(--spacing) * 1.5);
  transition-duration: 75ms;
}

/* Tamaños de botones */
.fi-btn.fi-size-xs {
  padding: calc(var(--spacing) * 1.5) calc(var(--spacing) * 2);
  font-size: var(--text-xs);
}

.fi-btn.fi-size-sm {
  padding: calc(var(--spacing) * 1.5) calc(var(--spacing) * 2.5);
  font-size: var(--text-sm);
}

.fi-btn.fi-size-lg {
  padding: calc(var(--spacing) * 2.5) calc(var(--spacing) * 3.5);
  font-size: var(--text-sm);
}

.fi-btn.fi-size-xl {
  padding: calc(var(--spacing) * 3) calc(var(--spacing) * 4);
  font-size: var(--text-sm);
}
```

### Avatares
```css
.fi-avatar {
  width: calc(var(--spacing) * 8);   /* 32px */
  height: calc(var(--spacing) * 8);
  border-radius: var(--radius-md);
  object-fit: cover;
}

.fi-avatar.fi-circular {
  border-radius: 9999px;
}

.fi-avatar.fi-size-sm {
  width: calc(var(--spacing) * 6);   /* 24px */
  height: calc(var(--spacing) * 6);
}

.fi-avatar.fi-size-lg {
  width: calc(var(--spacing) * 10);  /* 40px */
  height: calc(var(--spacing) * 10);
}
```

## Modo Oscuro

El proyecto está configurado con `darkMode: false` en Filament, pero incluye estilos para modo oscuro:

```css
:root.dark {
  color-scheme: dark;
}

/* Ejemplo de uso */
.elemento:where(.dark, .dark *) {
  background-color: var(--gray-800);
  color: var(--gray-200);
}
```

## Configuración de Tailwind CSS

### Archivo de Configuración (resources/css/app.css)
```css
@import 'tailwindcss';

@theme {
  --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 
               'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 
               'Noto Color Emoji';
}
```

### Vite Configuration
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    tailwindcss(),
  ],
});
```

## Branding

### Logo
- Ubicación: `/public/logo.png`
- Altura del logo en el panel: `3rem` (48px)
- Favicon: Mismo archivo del logo

### Grupos de Navegación
```php
'Gastos'
'Categorías'
'Monedas'
'Métodos de Pago'
'Comercios'
```

## Estados Interactivos

### Hover
```css
@media (hover: hover) {
  .elemento:hover {
    /* Transición suave de 75ms */
    transition-duration: 75ms;
  }
}
```

### Focus
```css
.elemento:focus-visible {
  outline-style: none;
  --tw-ring-shadow: 0 0 0 calc(2px + var(--tw-ring-offset-width)) 
                    var(--tw-ring-color);
}
```

### Disabled
```css
.elemento:disabled {
  cursor: default;
  opacity: 0.7;
  pointer-events: none;
}
```

## Tooltips (Tippy.js)

```css
.tippy-box {
  background-color: #333;
  color: #fff;
  border-radius: 4px;
  font-size: 14px;
  line-height: 1.4;
  padding: 5px 9px;
}

.tippy-box[data-theme~=light] {
  background-color: #fff;
  color: #26323d;
  box-shadow: 0 0 20px 4px rgba(154, 161, 177, 0.15),
              0 4px 80px -8px rgba(36, 40, 47, 0.25),
              0 4px 4px -2px rgba(91, 94, 105, 0.15);
}
```

## Mejores Prácticas

1. **Usar variables CSS**: Siempre preferir `var(--spacing)`, `var(--color-primary)`, etc.
2. **Transiciones suaves**: Aplicar `transition-duration: 75ms` para interacciones
3. **Espaciado consistente**: Usar múltiplos de `--spacing` (0.25rem)
4. **Responsive**: Utilizar los breakpoints definidos
5. **Accesibilidad**: Incluir estados `:focus-visible` y `:hover`
6. **Color primario**: Usar `#B50E1A` para elementos destacados y acciones principales

## Ejemplo de Implementación

```html
<!-- Botón primario -->
<button class="fi-btn fi-color" style="
  --bg: var(--color-primary);
  --text: white;
  --hover-bg: #9a0c16;
">
  Guardar
</button>

<!-- Badge -->
<span class="fi-badge fi-color" style="
  --color-50: #fef2f2;
  --color-600: var(--color-primary);
  --text: var(--color-primary);
">
  Activo
</span>

<!-- Card con espaciado consistente -->
<div style="
  padding: calc(var(--spacing) * 4);
  border-radius: var(--radius-lg);
  background-color: white;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
">
  Contenido
</div>
```

## Dependencias de Frontend

```json
{
  "devDependencies": {
    "@tailwindcss/vite": "^4.0.0",
    "axios": "^1.11.0",
    "laravel-vite-plugin": "^2.0.0",
    "tailwindcss": "^4.0.0",
    "vite": "^7.0.7"
  }
}
```

---

**Nota**: Este documento está basado en el proyecto Control de Gastos Cáceres construido con Laravel 11, Filament 3 y Tailwind CSS v4. Para aplicar estos estilos en otro proyecto, asegúrate de incluir las mismas dependencias y configuraciones base.
