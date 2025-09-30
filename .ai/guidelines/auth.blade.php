{{-- Authentication & Authorization --}}

* Implement authentication using **Laravel Breeze (Blade + Livewire stack)**.
* Use **spatie/laravel-permission** for role-based access control.
* Roles:

  * **Admin** – manage services, counters, staff, view reports.
  * **Staff/Receptionist** – create and manage queues, call/recall patients.
  * **Doctor/Nurse** – monitor queues.
  * **Display** – public access only, read-only.
