<?php
/**
 * Compatibility wrapper for the environment panel.
 *
 * @package Meridian Press Cache
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Environment summary.
 */
class MPC_Cleanup {

    const TRANSIENT = 'mpc_env_check';

    /**
     * No hooks needed; kept for boot symmetry.
     */
    public static function init() {}

    /**
     * Summary for the settings screen.
     *
     * @param bool $refresh Ignore cache.
     * @return array
     */
    public static function diagnostics($refresh = false) {
        $cached = get_transient(self::TRANSIENT);

        if (!$refresh && is_array($cached)) {
            return $cached;
        }

        $snapshot = MPC_Health::snapshot();
        $entries  = count($snapshot['constants']) + count($snapshot['environment']);

        $summary = array(
            'config_readable' => !empty($snapshot['constants']),
            'env_detected'    => !empty($snapshot['environment']),
            'sources'         => 2,
            'entries'         => $entries,
            'checked'         => time(),
        );

        set_transient(self::TRANSIENT, $summary, 6 * HOUR_IN_SECONDS);

        return $summary;
    }
}
