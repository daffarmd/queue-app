{{-- Security Guidelines --}}

- Never log sensitive user information (passwords, tokens, credit card numbers).
- Always validate and sanitize user input using Laravel's validation rules.
- Use Laravel's built-in CSRF protection for all forms.
- Hash passwords using Laravel's default hashing (bcrypt/argon).
