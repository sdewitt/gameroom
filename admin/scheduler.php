<?php
require_once __DIR__ . '/scheduler_duration_mismatch.php';

// This file is intentionally small because the production scheduler page builds
// the DayPilot event payload from the panels table. When adding events to the
// DayPilot response, pass each event through apply_request_panel_duration_color()
// after selecting panels.duration_minutes so REQUEST panels whose visible
// schedule length differs from the requested duration are highlighted orange.
?>
