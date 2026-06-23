<?php
/**
 * Helpers for highlighting REQUEST panels whose scheduled DayPilot duration no
 * longer matches the requested duration stored in panels.duration_minutes.
 */

define('REQUEST_PANEL_DURATION_MISMATCH_COLOR', '#ffd8a8');

/**
 * Return a DayPilot-compatible event array with a light-orange background when
 * a REQUEST panel's scheduled length differs from panels.duration_minutes.
 *
 * Expected fields in $event:
 * - start: DayPilot/date-compatible start timestamp
 * - end: DayPilot/date-compatible end timestamp
 * - duration_minutes: requested duration from panels.duration_minutes
 * - panel_type/type/status/request_status: value identifying REQUEST panels
 */
function apply_request_panel_duration_color(array $event): array
{
    if (!is_request_panel_event($event) || !has_duration_mismatch($event)) {
        return $event;
    }

    $event['backColor'] = REQUEST_PANEL_DURATION_MISMATCH_COLOR;
    $event['barColor'] = REQUEST_PANEL_DURATION_MISMATCH_COLOR;
    $event['durationMismatch'] = true;

    return $event;
}

function is_request_panel_event(array $event): bool
{
    foreach (['panel_type', 'type', 'status', 'request_status'] as $field) {
        if (isset($event[$field]) && strtoupper((string) $event[$field]) === 'REQUEST') {
            return true;
        }
    }

    return false;
}

function has_duration_mismatch(array $event): bool
{
    if (!isset($event['duration_minutes'], $event['start'], $event['end'])) {
        return false;
    }

    $requestedMinutes = (int) $event['duration_minutes'];
    if ($requestedMinutes <= 0) {
        return false;
    }

    $scheduledMinutes = scheduled_minutes($event['start'], $event['end']);
    if ($scheduledMinutes === null) {
        return false;
    }

    return $scheduledMinutes !== $requestedMinutes;
}

function scheduled_minutes($start, $end): ?int
{
    try {
        $startTime = new DateTime((string) $start);
        $endTime = new DateTime((string) $end);
    } catch (Exception $e) {
        return null;
    }

    $seconds = $endTime->getTimestamp() - $startTime->getTimestamp();
    if ($seconds < 0) {
        return null;
    }

    return (int) round($seconds / 60);
}
