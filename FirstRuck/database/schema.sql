PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    session_key TEXT NOT NULL UNIQUE,
    answers_json TEXT NOT NULL,
    generated_profile_json TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS trails (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    source TEXT NOT NULL,
    source_ref TEXT,
    is_demo INTEGER NOT NULL DEFAULT 1,
    distance_km REAL NOT NULL,
    elevation_gain_m INTEGER NOT NULL,
    max_grade_percent REAL NOT NULL,
    surface TEXT NOT NULL,
    route_type TEXT NOT NULL,
    shade_level TEXT NOT NULL,
    facilities_json TEXT NOT NULL DEFAULT '[]',
    summary TEXT NOT NULL,
    geometry_json TEXT NOT NULL DEFAULT '[]',
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recommendation_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    recommendation_json TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE CASCADE
);

INSERT OR IGNORE INTO trails (
    id, name, source, source_ref, is_demo, distance_km, elevation_gain_m,
    max_grade_percent, surface, route_type, shade_level, facilities_json,
    summary, geometry_json
) VALUES
    ('demo-harbor-loop', 'Harbor bluff loop', 'First Ruck demo', NULL, 1, 3.2, 42, 5.0, 'compacted', 'loop', 'partial', '["parking","water"]', 'A forgiving loop with an open middle mile and clear turnaround options.', '[[9,72],[22,56],[40,61],[55,38],[73,43],[88,22]]'),
    ('demo-oak-canyon', 'Oak canyon path', 'First Ruck demo', NULL, 1, 4.1, 96, 8.0, 'trail', 'out-and-back', 'high', '["parking","toilets"]', 'A shaded dirt route with a steady climb and an easy early turnaround.', '[[8,67],[25,62],[32,44],[49,51],[66,30],[87,36]]'),
    ('demo-river-greenway', 'River greenway', 'First Ruck demo', NULL, 1, 2.8, 18, 2.0, 'paved', 'out-and-back', 'low', '["parking","water","toilets"]', 'A mostly level path for learning pace and pack fit without technical terrain.', '[[7,62],[25,57],[39,63],[55,49],[72,52],[91,31]]'),
    ('demo-mesa-rise', 'Mesa rise circuit', 'First Ruck demo', NULL, 1, 5.0, 154, 11.0, 'gravel', 'loop', 'low', '["parking"]', 'A longer rolling circuit for later weeks when hills feel controlled.', '[[6,73],[19,52],[38,59],[53,29],[70,41],[91,18]]');

