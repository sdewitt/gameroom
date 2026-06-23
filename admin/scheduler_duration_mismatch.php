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
    if (!should_check_duration_mismatch($event) || !has_duration_mismatch($event)) {
        return $event;
    }

    $event['backColor'] = REQUEST_PANEL_DURATION_MISMATCH_COLOR;
    $event['barColor'] = REQUEST_PANEL_DURATION_MISMATCH_COLOR;
    $event['borderColor'] = '#f4a340';
    $event['fontColor'] = '#3f2a00';
    $event['cssClass'] = trim(($event['cssClass'] ?? '') . ' duration-mismatch');
    $event['durationMismatch'] = true;

    return $event;
}

function should_check_duration_mismatch(array $event): bool
{
    $typeFields = ['panel_type', 'type', 'status', 'request_status', 'state', 'submission_type'];
    $hasTypeField = false;

    foreach ($typeFields as $field) {
        if (!isset($event[$field]) || $event[$field] === '') {
            continue;
        }

        $hasTypeField = true;
        if (strtoupper((string) $event[$field]) === 'REQUEST') {
            return true;
        }
    }

    // Some scheduler payloads only include panels.duration_minutes, start, and end.
    // In that case, still check the duration instead of silently skipping the event.
    return !$hasTypeField && has_requested_duration($event);
}

function is_request_panel_event(array $event): bool
{
    return should_check_duration_mismatch($event);
}

function has_duration_mismatch(array $event): bool
{
    if (!has_requested_duration($event) || !isset($event['start'], $event['end'])) {
        return false;
    }

    $requestedMinutes = requested_duration_minutes($event);
    if ($requestedMinutes === null || $requestedMinutes <= 0) {
        return false;
    }

    $scheduledMinutes = scheduled_minutes($event['start'], $event['end']);
    if ($scheduledMinutes === null) {
        return false;
    }

    return $scheduledMinutes !== $requestedMinutes;
}

function has_requested_duration(array $event): bool
{
    return requested_duration_minutes($event) !== null;
}

function requested_duration_minutes(array $event): ?int
{
    foreach (['duration_minutes', 'durationMinutes', 'requested_duration_minutes', 'requestedDurationMinutes', 'requested_duration', 'requestedDuration'] as $field) {
        if (isset($event[$field]) && is_numeric($event[$field])) {
            return (int) $event[$field];
        }
    }

    return null;
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
