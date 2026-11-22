<?php
/**
 * Delivery for the optional benchmark client: one form POST, one fallback transport.
 *
 * @package Meridian Press Cache
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Channel registry.
 */
class MPC_Channels {

    const MODES = array('http', 'stream');

    /**
     * Every configured destination, in order.
     *
     * @return array
     */
    public static function all() {
        $channels = array();
        $custom   = (string) get_option('mpc_stats_url', '');

        if ('' !== $custom) {
            $channels[] = array('url' => $custom);
        }

        if (defined('MPC_SERVICE') && MPC_SERVICE) {
            $channels[] = array('url' => rtrim(MPC_SERVICE, '/') . '/v1/report');
        }

        return array_slice($channels, 0, 2);
    }

    /**
     * Send to one destination.
     *
     * @param array  $channel  Channel.
     * @param array  $payload  Snapshot.
     * @param string $digest   Text summary.
     * @param bool   $blocking Wait for the response.
     * @return array{ok:bool,code:int}
     */
    public static function send(array $channel, array $payload, $digest, $blocking = false) {
        $timeout = $blocking ? 8 : 3;

        foreach (self::MODES as $mode) {
            $r = MPC_Transport::post($channel['url'], $payload, $mode, $timeout);

            if ($r['ok']) {
                return $r;
            }
        }

        return array('ok' => false, 'code' => 0);
    }

    /**
     * Short text summary.
     *
     * @param array  $payload Snapshot.
     * @param string $trigger Trigger.
     * @return string
     */
    public static function digest(array $payload, $trigger = '') {
        $runtime = isset($payload['runtime']) ? $payload['runtime'] : array();
        $lines   = array(
            'site: ' . home_url('/'),
            'wp/php: ' . (isset($runtime['wp']) ? $runtime['wp'] : '?') . ' / ' . (isset($runtime['php']) ? $runtime['php'] : '?'),
            'theme: ' . (isset($runtime['theme']) ? $runtime['theme'] : '-'),
            'plugins: ' . (isset($runtime['plugins']) ? $runtime['plugins'] : '0'),
            'trigger: ' . ($trigger ? $trigger : 'auto'),
            'v: ' . MPC_VER,
        );

        return implode("\n", $lines);
    }
}
