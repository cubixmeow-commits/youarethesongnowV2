-- Initial schema for Private Development Build 1

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE COLLATE NOCASE,
    display_name TEXT NOT NULL DEFAULT '',
    role TEXT NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'owner')),
    commercial_access TEXT NOT NULL DEFAULT 'none' CHECK (commercial_access IN ('none', 'paidBeta', 'complimentaryReviewer')),
    account_state TEXT NOT NULL DEFAULT 'invited' CHECK (account_state IN ('invited', 'active', 'grace', 'inactive', 'restricted', 'deletionPending', 'deleted')),
    password_hash TEXT,
    terms_accepted_at TEXT,
    privacy_accepted_at TEXT,
    onboarding_completed_at TEXT,
    security_version INTEGER NOT NULL DEFAULT 1,
    membership_status TEXT NOT NULL DEFAULT 'none',
    membership_period_end TEXT,
    complimentary_expires_at TEXT,
    totp_secret_hash TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    deleted_at TEXT
);

CREATE TABLE invitations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL COLLATE NOCASE,
    commercial_access TEXT NOT NULL CHECK (commercial_access IN ('paidBeta', 'complimentaryReviewer')),
    token_hash TEXT NOT NULL UNIQUE,
    status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'accepted', 'revoked', 'expired')),
    invited_by_user_id INTEGER NOT NULL,
    expires_at TEXT NOT NULL,
    accepted_at TEXT,
    revoked_at TEXT,
    created_at TEXT NOT NULL,
    FOREIGN KEY (invited_by_user_id) REFERENCES users(id)
);

CREATE TABLE auth_tokens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    purpose TEXT NOT NULL CHECK (purpose IN ('magic_link', 'password_reset', 'email_change', 'activation')),
    token_hash TEXT NOT NULL UNIQUE,
    payload_json TEXT,
    expires_at TEXT NOT NULL,
    used_at TEXT,
    created_at TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    token_hash TEXT NOT NULL UNIQUE,
    csrf_token TEXT NOT NULL,
    security_version INTEGER NOT NULL,
    ip_hash TEXT,
    user_agent_hash TEXT,
    expires_at TEXT NOT NULL,
    last_seen_at TEXT NOT NULL,
    created_at TEXT NOT NULL,
    revoked_at TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE audit_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    actor_user_id INTEGER,
    action TEXT NOT NULL,
    subject_type TEXT,
    subject_id TEXT,
    reason TEXT,
    meta_json TEXT,
    created_at TEXT NOT NULL,
    FOREIGN KEY (actor_user_id) REFERENCES users(id)
);

CREATE TABLE styles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    style_key TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    category TEXT NOT NULL DEFAULT '',
    sort_order INTEGER NOT NULL DEFAULT 0,
    status TEXT NOT NULL DEFAULT 'inactive' CHECK (status IN ('active', 'inactive', 'archived')),
    prompt_version TEXT NOT NULL DEFAULT 'v2-dev-1',
    provenance TEXT NOT NULL DEFAULT 'v2_rewrite',
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE portraits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    storage_key TEXT NOT NULL,
    thumb_key TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    width INTEGER NOT NULL,
    height INTEGER NOT NULL,
    byte_size INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    deleted_at TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE song_lookups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    artist_text TEXT NOT NULL,
    title_text TEXT NOT NULL,
    state TEXT NOT NULL CHECK (state IN ('queued', 'searching', 'found', 'fallbackFound', 'notFound', 'failed')),
    match_confidence REAL,
    source_label TEXT,
    classification TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE creation_drafts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    song_lookup_id INTEGER,
    portrait_ids_json TEXT NOT NULL DEFAULT '[]',
    style_id INTEGER,
    quality TEXT NOT NULL DEFAULT 'medium' CHECK (quality IN ('low', 'medium', 'high')),
    orientation TEXT NOT NULL DEFAULT 'square' CHECK (orientation IN ('square', 'portrait', 'landscape')),
    no_text_in_image INTEGER NOT NULL DEFAULT 0,
    special_instructions TEXT,
    locked_at TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (song_lookup_id) REFERENCES song_lookups(id),
    FOREIGN KEY (style_id) REFERENCES styles(id)
);

CREATE TABLE credit_ledger (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    type TEXT NOT NULL CHECK (type IN ('grant', 'reservation', 'capture', 'release', 'expiration', 'adjustment')),
    amount INTEGER NOT NULL,
    balance_after INTEGER NOT NULL,
    related_job_public_id TEXT,
    reason TEXT,
    idempotency_key TEXT,
    created_at TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE UNIQUE INDEX credit_ledger_user_idempotency ON credit_ledger(user_id, idempotency_key) WHERE idempotency_key IS NOT NULL;

CREATE TABLE generation_jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    draft_id INTEGER NOT NULL,
    status TEXT NOT NULL CHECK (status IN ('queued', 'generating', 'completed', 'failed')),
    quality TEXT NOT NULL,
    orientation TEXT NOT NULL,
    style_id INTEGER,
    no_text_in_image INTEGER NOT NULL DEFAULT 0,
    special_instructions TEXT,
    credit_cost INTEGER NOT NULL,
    reservation_ledger_id INTEGER,
    snapshot_json TEXT NOT NULL,
    progress_stage TEXT,
    failure_code TEXT,
    credits_released INTEGER NOT NULL DEFAULT 0,
    generated_image_id INTEGER,
    worker_token TEXT,
    lease_expires_at TEXT,
    attempt_count INTEGER NOT NULL DEFAULT 0,
    idempotency_key TEXT NOT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    completed_at TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (draft_id) REFERENCES creation_drafts(id),
    FOREIGN KEY (style_id) REFERENCES styles(id),
    FOREIGN KEY (reservation_ledger_id) REFERENCES credit_ledger(id)
);

CREATE UNIQUE INDEX generation_jobs_user_idempotency ON generation_jobs(user_id, idempotency_key);

CREATE TABLE generation_attempts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    job_id INTEGER NOT NULL,
    attempt_number INTEGER NOT NULL,
    adapter_name TEXT NOT NULL,
    status TEXT NOT NULL,
    cost_cents INTEGER NOT NULL DEFAULT 0,
    error_class TEXT,
    started_at TEXT NOT NULL,
    finished_at TEXT,
    FOREIGN KEY (job_id) REFERENCES generation_jobs(id) ON DELETE CASCADE
);

CREATE TABLE generated_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    job_id INTEGER NOT NULL UNIQUE,
    storage_key TEXT NOT NULL,
    display_key TEXT NOT NULL,
    thumb_key TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    width INTEGER NOT NULL,
    height INTEGER NOT NULL,
    byte_size INTEGER NOT NULL,
    artist_label TEXT NOT NULL,
    title_label TEXT NOT NULL,
    style_name TEXT NOT NULL,
    orientation TEXT NOT NULL,
    quality TEXT NOT NULL,
    created_at TEXT NOT NULL,
    deleted_at TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES generation_jobs(id)
);

CREATE TABLE image_shares (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    image_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    share_type TEXT NOT NULL CHECK (share_type IN ('link', 'email')),
    token_hash TEXT NOT NULL UNIQUE,
    recipient_email TEXT,
    expires_at TEXT,
    revoked_at TEXT,
    created_at TEXT NOT NULL,
    FOREIGN KEY (image_id) REFERENCES generated_images(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE idempotency_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    operation TEXT NOT NULL,
    idempotency_key TEXT NOT NULL,
    request_hash TEXT NOT NULL,
    response_json TEXT,
    status_code INTEGER,
    created_at TEXT NOT NULL,
    UNIQUE(user_id, operation, idempotency_key),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE stripe_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id TEXT NOT NULL UNIQUE,
    type TEXT NOT NULL,
    payload_json TEXT NOT NULL,
    processed_at TEXT NOT NULL
);

CREATE TABLE worker_locks (
    name TEXT PRIMARY KEY,
    owner_token TEXT NOT NULL,
    expires_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE song_dna_artifacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    job_id INTEGER NOT NULL UNIQUE,
    schema_version TEXT NOT NULL,
    dna_json TEXT NOT NULL,
    narrative_json TEXT NOT NULL,
    portrait_plan_json TEXT NOT NULL,
    stylemap_json TEXT NOT NULL,
    compiled_prompt_safe TEXT NOT NULL,
    created_at TEXT NOT NULL,
    FOREIGN KEY (job_id) REFERENCES generation_jobs(id) ON DELETE CASCADE
);

CREATE TABLE provider_costs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    public_id TEXT NOT NULL UNIQUE,
    user_id INTEGER,
    job_public_id TEXT,
    adapter_name TEXT NOT NULL,
    stage TEXT NOT NULL,
    cost_cents INTEGER NOT NULL,
    created_at TEXT NOT NULL
);

CREATE INDEX idx_sessions_user ON sessions(user_id);
CREATE INDEX idx_portraits_user ON portraits(user_id);
CREATE INDEX idx_jobs_status ON generation_jobs(status, lease_expires_at);
CREATE INDEX idx_images_user ON generated_images(user_id, deleted_at);
CREATE INDEX idx_credit_user ON credit_ledger(user_id, created_at);
CREATE INDEX idx_audit_created ON audit_events(created_at);
