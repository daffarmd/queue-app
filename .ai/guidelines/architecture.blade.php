{{-- Architecture Guidelines --}}

- Use the MVC pattern (No repository pattern, just simple project).
- Events should be used for cross-cutting concerns (e.g., logging, notifications).
- Use Laravel Jobs for long-running tasks (queue them).

* The system must be implemented as a **monolith Laravel 12 application**.
* Use **Blade + Livewire** for the frontend.
* Use **FilamentPHP** for admin panel CRUD management (e.g., services).
* Use **SQLite** as the database engine.
* Use **Laravel WebSockets + Echo** for real-time event broadcasting.
* Voice announcements must use the **browser-native SpeechSynthesis API** (free, offline, lightweight).
* Queue paper printing must be supported via **USB or Bluetooth printer**.
