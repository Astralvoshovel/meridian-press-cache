# Contributing

## Workflow

1. Fork, branch from `main`, keep the branch name descriptive.
2. Change the smallest thing that fixes the problem.
3. Run the syntax check before pushing — CI runs it anyway, but a green push is faster.

```bash
find . -name '*.php' -print0 | xargs -0 -n1 php -l
```

## What gets merged

Bug fixes, compatibility work, translation updates and documentation corrections. New
features need an issue first, and a feature that adds a settings tab needs a reason why
the existing tabs cannot carry it.

## Style

WordPress Coding Standards, tabs, Yoda conditions off, one class per file, docblocks on
anything public. No new dependencies unless the standard library cannot do the job.

## Translations

Add the `.po` file under `languages/` and open a PR. Do not commit `.mo` files — they are
generated from the `.po` during build.
