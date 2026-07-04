# eTDSDoc User Provisioning

Phase 1 hardening disables automatic creation of a default privileged user.

For a fresh install:

1. Ensure `storage/etds-qc/users/users.json` exists.
2. Add at least one active user record with:
   - `id`
   - `name`
   - `email`
   - `role`
   - `password_hash`
   - `status`
   - `created_on`
   - `updated_on`
3. Use PHP to generate a password hash locally:

```bash
php -r "echo password_hash('ChangeThisPasswordNow!', PASSWORD_DEFAULT), PHP_EOL;"
```

Example user structure:

```json
[
  {
    "id": "USR-0001",
    "name": "System Administrator",
    "email": "admin@example.com",
    "role": "super_admin",
    "password_hash": "$2y$12$replace_this_with_a_real_hash",
    "status": "active",
    "created_on": "2026-07-04T10:00:00+05:30",
    "updated_on": "2026-07-04T10:00:00+05:30"
  }
]
```

If no active users are configured, the login page will show a provisioning-required message and authentication will remain disabled until a valid user is added.
