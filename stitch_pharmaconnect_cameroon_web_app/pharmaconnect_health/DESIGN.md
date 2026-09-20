---
name: PharmaConnect Health
colors:
  surface: '#f9f9ff'
  surface-dim: '#cfdaf2'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f3ff'
  surface-container: '#e7eeff'
  surface-container-high: '#dee8ff'
  surface-container-highest: '#d8e3fb'
  on-surface: '#111c2d'
  on-surface-variant: '#3e4a3d'
  inverse-surface: '#263143'
  inverse-on-surface: '#ecf1ff'
  outline: '#6e7b6c'
  outline-variant: '#bdcaba'
  surface-tint: '#006e2d'
  primary: '#006b2c'
  on-primary: '#ffffff'
  primary-container: '#00873a'
  on-primary-container: '#f7fff2'
  inverse-primary: '#62df7d'
  secondary: '#006e2f'
  on-secondary: '#ffffff'
  secondary-container: '#6bff8f'
  on-secondary-container: '#007432'
  tertiary: '#a72d51'
  on-tertiary: '#ffffff'
  tertiary-container: '#c74668'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#7ffc97'
  primary-fixed-dim: '#62df7d'
  on-primary-fixed: '#002109'
  on-primary-fixed-variant: '#005320'
  secondary-fixed: '#6bff8f'
  secondary-fixed-dim: '#4ae176'
  on-secondary-fixed: '#002109'
  on-secondary-fixed-variant: '#005321'
  tertiary-fixed: '#ffd9de'
  tertiary-fixed-dim: '#ffb2bf'
  on-tertiary-fixed: '#3f0016'
  on-tertiary-fixed-variant: '#8a143c'
  background: '#f9f9ff'
  on-background: '#111c2d'
  surface-variant: '#d8e3fb'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
  headline-xl-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 28px
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
  label-lg:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 14px
  currency-display:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '700'
    lineHeight: 24px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-desktop: 2.5rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

This design system delivers a clinical, trustworthy, and welcoming interface tailored specifically for digital healthcare and pharmaceutical access in Cameroon. The aesthetic blends modern utilitarian clarity with reassuring warmth, eliminating clinical intimidation while maintaining medical precision.

### Target Audience & Tone
- **Audience:** Patients seeking prescription fulfillment, local pharmacy discovery, OTC medications, and certified pharmacists across urban and semi-urban Cameroon.
- **Emotional Response:** Reassurance, hygiene, swift reliability, and institutional trust.
- **Language & Cultural Context:** Exclusively French UI copy (`Rechercher une pharmacie`, `Ajouter au panier`, `Disponible de garde`). Financial displays strictly follow Central African CFA franc formatting with non-breaking spaces (e.g., `3 500 FCFA`).

### Aesthetic Expression
- **Design Philosophy:** Warm Clinical Minimalism. Flat, clean surfaces layered on a tinted mint canvas.
- **Visual Restraint:** Completely devoid of gradients, neon accents, or purple tones. Depth is achieved strictly through delicate, ambient foliage-tinted drop shadows and structural whitespace rather than heavy styling.

## Colors

The palette is rooted in pharmaceutical mint greens paired with grounded slate neutrals to ensure high contrast, accessibility, and an unmistakable pharmacy association.

### Primary Scale (Mint & Forest Green)
- `green-50`: `#f0fdf6` (Canvas page background)
- `green-100`: `#dcfce9` (Subtle container fill, badge background)
- `green-200`: `#bbf7d0` (Card borders, secondary button strokes, dividers)
- `green-500`: `#22c55e` (Active indicators, success states)
- `green-600`: `#16a34a` (Primary brand color, primary buttons, critical CTAs)
- `green-700`: `#15803d` (Hover states for primary actions)
- `green-900`: `#14532d` (High-contrast medical headers, dark emphasis)

### Neutral Scale (Slate)
- Canvas: `#f0fdf6`
- Surface Elevated (Cards, Modals): `#ffffff`
- Text Primary: `#1e293b` (Slate-800 — high legibility, softer than pure black)
- Text Secondary: `#475569` (Slate-600 — metadata, subtitles, prescription instructions)
- Text Muted: `#94a3b8` (Slate-400 — placeholders, disabled states)
- Border Subtle: `#e2e8f0` (Default form borders)

### Feedback & Status
- **Emergency / On-Duty Indicator ("Pharmacie de Garde"):** `#15803d` background with `#ffffff` text, or high-contrast red `#dc2626` strictly for urgent alerts or stock depletion.
- **Warning:** `#d97706` (Amber-600)
- **Info:** `#0284c7` (Sky-600)

## Typography

The typographic system utilizes **Plus Jakarta Sans** for structural headlines to deliver approachable, modern geometry, paired with **Inter** for body copy and administrative tables to maintain legibility during dense catalog browsing and prescription tracking.

### Rules & Hierarchy
- **Currency Typography:** All price instances use tabular numbers (`font-variant-numeric: tabular-nums`) with `currency-display` or `label-lg` styling. Prices must include a non-breaking space before the currency token: `12 000 FCFA`.
- **Text Color Pairing:** All headlines leverage `slate-800` (`#1e293b`) or `green-900` (`#14532d`) for primary sections. Never use low-contrast grays on lighter mint backgrounds.

## Layout & Spacing

The layout model is driven by generous whitespace, balancing density in product listings with breathing room in consultation screens.

### Grid Configuration
- **Desktop (1024px+):** 12-column grid, max content width of `1280px`, `margin-desktop` (40px), and `gutter-desktop` (24px).
- **Tablet (640px - 1023px):** 8-column grid, 24px margins, 16px gutters.
- **Mobile (<640px):** 4-column fluid grid, `margin` (16px), `gutter` (16px).

### Layout Rules
- **Viewport Canvas:** Always apply `#f0fdf6` (`green-50`) as the outer page background.
- **Section Rhythm:** Group related medical modules (e.g., pharmacy locator and inventory status) using `space-xl` (40px) between card structures on desktop, reduced to `space-lg` (24px) on mobile.

## Elevation & Depth

This design system avoids high-contrast drop shadows and heavy multi-layered borders. Visual separation relies on pure white cards sitting over the soft mint canvas (`#f0fdf6`), enhanced by diffused, natural-tinted ambient shadows.

### Shadow Scale
- **Card Base Shadow (`shadow-card`):** `0px 4px 20px -2px rgba(20, 83, 45, 0.05), 0px 2px 6px -1px rgba(20, 83, 45, 0.03)`. An organic, low-opacity shadow lightly tinted by forest green (`#14532d`) to integrate with the canvas.
- **Card Hover (`shadow-hover`):** `0px 10px 25px -4px rgba(20, 83, 45, 0.08), 0px 4px 10px -2px rgba(20, 83, 45, 0.04)`.
- **Dropdown & Flyout Elevation (`shadow-popover`):** `0px 12px 32px -4px rgba(30, 41, 59, 0.08)`. Pure neutral slate blur for overlay elements to guarantee clear layering.
- **Modal Backdrop:** `rgba(30, 41, 59, 0.4)` solid wash with no blur filter.

## Shapes

The design uses an intentional, structured hierarchy of rounded shapes to communicate softness while preserving clean architectural order.

### Shape Assignment
- **Cards & Surfaces:** Exactly `16px` border-radius (`rounded-2xl`).
- **Interactive Controls (Buttons, Inputs):** Exactly `12px` border-radius (`rounded-xl`).
- **Status Badges, Chips & Avatars:** Fully rounded pill-shape (`9999px` / `rounded-full`).
- **Checkboxes:** `4px` subtle radius (`rounded-sm`).

## Components

### Buttons
- **Primary Button:** Solid background `#16a34a`, text `#ffffff`, border-radius `12px`. Hover state: `#15803d`. Active state: `#14532d`. Focus ring: 2px offset with 2px ring `#bbf7d0`. Padding: 12px 24px (labels in `label-lg`).
- **Secondary Button:** Solid white background `#ffffff`, border 1px solid `#bbf7d0`, text `#16a34a`, border-radius `12px`. Hover state: background `#f0fdf6`, border color `#16a34a`. Active state: background `#dcfce9`.
- **Destructive Button:** White background `#ffffff`, border 1px solid `#fecaca`, text `#dc2626`. Hover state: background `#fef2f2`.

### Badges & Status Chips
- **Geometry:** Pill-shaped (`rounded-full`), height `24px` or `28px`, horizontal padding `12px`.
- **Pharmacie de Garde (On-Duty Status):** Background `#dcfce9`, text `#14532d`, with an animated or solid green dot `#16a34a`.
- **En Rupture (Out of Stock):** Background `#fee2e2`, text `#991b1b`.
- **Sur Ordonnance (Prescription Required):** Background `#e2e8f0`, text `#334155`.

### Cards
- **Product & Pharmacy Cards:** Background `#ffffff`, border-radius `16px`, padding `20px`, shadow `0px 4px 20px -2px rgba(20, 83, 45, 0.05)`. Optional subtle border: 1px solid `#f0fdf6` or transparent. On hover: translate-y `-2px` with `shadow-hover`.

### Form Inputs & Search Fields
- **Container:** Background `#ffffff`, border `1px solid #e2e8f0`, border-radius `12px`, padding `12px 16px`, typography `body-md`.
- **Focus State:** Border color `#16a34a`, box-shadow `0 0 0 3px rgba(187, 247, 208, 0.5)` (`green-200`).
- **Search Bar (Pharmacy Finder):** Include embedded `#16a34a` search icon prefix, placeholder text: "Rechercher un médicament, une pharmacie...".

### Lists & Tables
- **Pharmacy & Inventory Rows:** Clean white containers or alternating `#ffffff` and `#f0fdf6` rows separated by `1px solid #dcfce9`. Ensure currency figures right-align and use `currency-display` or `label-lg`.

### Checkboxes & Radio Buttons
- **Checkbox:** `18px x 18px`, border `1.5px solid #94a3b8`, border-radius `4px`. Checked state: background `#16a34a`, border `#16a34a`, checkmark icon in `#ffffff`.
- **Radio Button:** `20px x 20px` circular ring with `6px` centered dot `#16a34a` when selected.