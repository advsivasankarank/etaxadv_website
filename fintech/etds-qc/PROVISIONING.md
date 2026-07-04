# eTDSDoc User Provisioning

Phase 1 hardening disables automatic creation of a default privileged user.

For a fresh install:

1. Ensure PHP CLI is available on the server or workstation.
2. Run the provisioning utility:

```bash
php fintech/etds-qc/tools/provision_admin.php --email=admin@example.com
```

3. Enter the password when prompted.
4. The utility writes the hashed password into `storage/etds-qc/users/users.json` and clears the provisioning-required state.

To rotate or replace an existing admin email:

```bash
php fintech/etds-qc/tools/provision_admin.php --current-email=admin@etaxadv.local --email=secure-admin@example.com --name="System Administrator"
```

To provide the password non-interactively:

```bash
php fintech/etds-qc/tools/provision_admin.php --email=admin@example.com --password="Use-A-Strong-Unique-Password!"
```

The utility:

1. Refuses blank or weak passwords.
2. Uses `password_hash()` to store the password securely.
3. Never prints the password back to the terminal.
4. Updates only local runtime storage and does not change any tracked application code or workflow data.

If the older `admin@etaxadv.local` legacy admin record is still present in `users.json`, run the rotation command above to replace it with a real administrator email and a new password.

If no active users are configured, the login page will show a provisioning-required message and authentication will remain disabled until a valid user is added.
