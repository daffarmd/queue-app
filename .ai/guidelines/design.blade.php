{{-- Design Guidelines --}}

# General Principles

* Always use responsive design; layouts must be mobile-first.
* Use Tailwind CSS utility classes for styling (no inline styles).
* Prefer reusable Blade components for UI elements (buttons, forms, modals).
* Follow Laravel Breeze/Jetstream component patterns where possible.

# Colors & Theme

(derived from the TRI MULYO logo)

* Primary color: `#D32F2F` (red from logo figures).
* Secondary color: `#F59E0B` (yellow/gold from rice stalk).
* Accent color: `#4CAF50` (green from flower stem).
* Neutral background: `#FFFFFF` (white from logo background).
* Text color: `#111827` (Tailwind gray-900 for readability).

# Typography

* Use Tailwind’s default font stack.
* Headings (`h1-h6`) should use `font-bold`.
* Body text should use `text-gray-700`.
* Branding text (e.g., “TRI MULYO”) can use `uppercase tracking-wide` for emphasis.

# Layout

* Use a max width of `max-w-7xl` for containers.
* Apply consistent padding (`px-4 sm:px-6 lg:px-8`).
* Use `flex` and `grid` utilities for layout, no custom CSS unless needed.
* Incorporate logo colors subtly in sections (e.g., red headers, gold highlights).

# Components

* **Buttons**: rounded, padding `px-4 py-2`.

  * Primary: red (`bg-[#D32F2F] text-white hover:bg-red-700`).
  * Secondary: gold (`bg-[#F59E0B] text-white hover:bg-yellow-600`).
  * Neutral: gray (`bg-gray-200 text-gray-900`).
* **Forms**:

  * Use `@csrf`.
  * Labels above inputs.
  * Consistent spacing `mb-4`.
  * Inputs with `border-gray-300 focus:ring-[#D32F2F] focus:border-[#D32F2F]`.
* **Alerts**:

  * Use a Blade component (`<x-alert type="success">`).
  * Success = green (`bg-green-100 text-green-800 border-green-300`).
  * Error = red (`bg-red-100 text-red-800 border-red-300`).

# Accessibility

* All interactive elements must be accessible via keyboard.
* Use semantic HTML (`<button>` instead of clickable `<div>`).
* Provide `aria-label` attributes for icons and custom components.
* Ensure sufficient color contrast between text and background (WCAG AA+).

# Branding Elements

* TRI MULYO logo must appear in the header and login screen.
* Logo red and gold should be used sparingly as accent highlights (not full backgrounds).
* Decorative motifs (rice stalk, lily flowers) may be referenced subtly in UI illustrations, but not overused.
