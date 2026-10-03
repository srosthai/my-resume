# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- Recruiters, hiring managers, and freelance clients evaluating SROS THAI for full-stack work.
- Peers and readers who arrive for the notes, feeds, and project write-ups.
- They land on the home page first, usually from a CV, GitHub, or LinkedIn, and decide within seconds whether the work and the person behind it are worth a deeper look.

## Product Purpose

A personal portfolio and resume site for SROS THAI, a full-stack developer in Phnom Penh, Cambodia. It presents identity, projects, work history, writing, and a contact channel in one place. Success means a visitor understands who this is, sees credible work, and takes one of the offered next steps (projects, feeds, notes, contact).

## Positioning

Everything on the site is the developer's own: real projects, real notes with engagement (views/likes), a live local clock, and a music player. It is a working product built by the person it describes, not a template resume.

## Operating Context

- PHP 8.2 / Laravel 12 backend, Vue 3 + TypeScript + Inertia.js frontend, Tailwind CSS 4, Vite, SSR-capable.
- Public pages are read-only for visitors; a single owner account manages content through `/backend`.
- Optional server-side rendering; SEO metadata and JSON-LD ship from the blade shell.
- Contact form posts through a throttled endpoint and email (Resend).
- Dev entry: `composer dev` (Laravel + queue + Vite), app served at port 8001 in this environment.

## Capabilities and Constraints

- Pages: home, about, portfolio (+ project detail), contact, hobby, more, resume, note (+ post), feeds (+ feed detail).
- Home data: owner public profile (name, position, description, image), tech stacks, and counts (projects, tech stacks, work entries, published notes).
- Public pages must never receive raw `User` models; only `User::publicColumns()`.
- Theme support exists (light/dark via `useAppearance`, cookie + localStorage).
- Uploads go through `ImageUploadService` on the `uploads` disk.
- Registration disabled unless `AUTH_REGISTRATION_ENABLED=true`; guests get only the `public` Ziggy route group.

## Brand Commitments

- Name: SROS THAI (srosthai). Public identity and copy are factual and must stay accurate.
- The owner's own photo assets live in `public/` (`me-1.png`, `me-2.png`, `og-image.png`).
- Home visual constraint from the owner (2026-10-03, revised same day): the home page follows the composition of the owner's reference — status strip, stencil name, centered portrait, HUD spec table, credential card, data deck, techno display type — but must use the site's normal theme colors and background in both light and dark. No amber/orange world and no forced dark mode. The portrait stack uses the owner's own photos `public/me-1.png` (shown first) and `public/me-2.png` (revealed on hover/tap).

## Evidence on Hand

- Real content in the database: projects, tech stacks, work experience, education, notes, feeds, popular songs.
- Real imagery: `public/me-2.png` (self-portrait, light background), `public/og-image.png`, tech stack logos via `logo` fields.
- Contact endpoint and email template exist and are wired.
- No testimonials, metrics, or client logos exist; do not fabricate any.

## Product Principles

- Show the real work and the real person; no invented claims.
- One decisive first viewport beats a wall of sections.
- Live data (clock, counts) beats static decoration.
- Accessibility and responsiveness are part of the build, not a later pass.
- Keep public surface small, fast, and free of admin concerns.
