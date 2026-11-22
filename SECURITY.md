# Security policy

## Supported versions

The latest release gets fixes. Older tags are left as published — pin to a tag if you
need stability, upgrade if you need a fix.

## How to report

Use **Security → Advisories → New draft advisory** on this repository. That channel is
private, and it keeps the discussion attached to the code that needs the fix.

A useful report contains: affected version, PHP and WordPress version, what the plugin
does instead of what it should, and the smallest request or click path that shows it.

## Boundaries

We care about the plugin: its PHP, its admin screens, its REST routes, its CLI commands
and its file writes. We cannot fix WordPress core, other plugins or the host, and we do
not treat a compromised admin account as a vulnerability in this plugin.

## Known design limits

The cache is served before WordPress loads, so anything that depends on a session must
be excluded from it. That is documented, not a bug — but a way to bypass an exclusion is.
