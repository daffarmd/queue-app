{{-- Database Design --}}

* `services` table:

  * `id` (PK)
  * `name` (string)
  * `code` (string, service initial)
  * `description` (nullable string)
  * `timestamps`

* `queues` table:

  * `id` (PK)
  * `service_id` (FK to services)
  * `patient_name` (string)
  * `number` (int, per-service daily counter)
  * `code` (string, formatted as `<service_code>-<number>`)
  * `counter` (string/int)
  * `status` (enum: `waiting`, `called`, `done`, `skipped`, `recalled`)
  * `called_at` (nullable timestamp)
  * `finished_at` (nullable timestamp)
  * `created_at`

* Relationships:

  * `Queue` belongs to `Service`.
  * `Service` has many `Queues`.
