{{-- Coding Style Guidelines --}}

- Follow PSR-12 coding standards.
- Use 2 spaces for indentation.
- All controllers must end with "Handler".
- Database tables should use plural snake_case (e.g., `users`, `order_items`).
- Eloquent models should use singular StudlyCase (e.g., `User`, `OrderItem`).

{{-- Queue Numbering Rules --}}

* Queue numbering is **per service**.
* Queue code must use the service code + padded number (e.g., `GEN-001`).
* Numbering resets **daily at 00:00 WIB (GMT+7)**.
* Reset rule: consider only rows where `queues.created_at >= today() (WIB)`.
* No cron job is required; logic must be enforced at queue creation.

{{-- Admin Panel (Filament) --}}

* Create a **ServiceResource** for CRUD operations on services.
* Only **Admin** users may access the Filament panel.
* Filament panels must follow naming convention: `<Model>Resource`.

{{-- Staff Dashboard (Livewire) --}}

* Staff dashboard must allow:

  * Adding patients to queue (select service).
  * Viewing live queue list.
  * Calling a patient (broadcasting event + updating status).
  * Marking patient as **done** or **skipped**.
  * **Recalling a skipped patient**:

    * Staff may recall previously skipped patients.
    * Recall action updates status → `recalled`.
    * Recalled patients are re-broadcasted to display and patient screen.

* When a patient is called:

  1. Attempt to print the queue paper.
  2. If printer is connected (Bluetooth or USB), print immediately.
  3. If not connected, attempt reconnection.
  4. If reconnection succeeds, print queue paper.
  5. If reconnection fails, skip printing and continue with voice announcement.

{{-- Display Page --}}

* Publicly accessible, no login required.
* Must be responsive for TVs, kiosks, mobiles, and desktops.
* Must display:

  * Current serving patient (queue code + service name + counter).
  * Next patients in queue.
  * **Recalled patients** must also be shown as “Recalled” on the display page.
* Must auto-update in real-time using WebSockets.
* Must speak announcements aloud using the **SpeechSynthesis API** when a patient is called or recalled.

{{-- Events & Broadcasting --}}

* Define Laravel Events for:

  * `QueueCreated`
  * `QueueCalled`
  * `QueueSkipped`
  * `QueueRecalled`
* Events must be broadcast via WebSockets to Livewire + Echo listeners.
* Display and staff dashboard must subscribe to these events for real-time updates.

{{-- Printing Integration --}}

* Printing logic must be abstracted into a **PrinterService** class.
* The PrinterService must:

  * Detect available printer (USB or Bluetooth).
  * Handle fallback connection attempts if not already connected.
  * Expose a method `printQueueTicket($queue)` that formats and prints ticket text:

    * Clinic name (e.g., “TRI MULYO”).
    * Queue code (`GEN-001`).
    * Service name (`General Consultation`).
    * Counter number.
    * Date/time.
* If printing fails after reconnection attempts, log the error and skip printing gracefully.

{{-- Best Practices --}}

* Use **Form Request validation** for all input.
* Use **Service classes** for queue logic (numbering, reset, printing, recall).
* Use **Events and Listeners** to decouple broadcasting logic from controllers.
* Use **Repositories (optional)** for clean separation of data access.
* Implement **Policies** for role-based access on models.
* Maintain **RESTful naming conventions** for controllers, routes, and methods.
