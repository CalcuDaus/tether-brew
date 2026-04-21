

# Project Memory — tether-brew
> 253 notes | Score threshold: >40

## Safety — Never Run Destructive Commands

> Dangerous commands are actively monitored.
> Critical/high risk commands trigger error notifications in real-time.

- **NEVER** run `rm -rf`, `del /s`, `rmdir`, `format`, or any command that deletes files/directories without EXPLICIT user approval.
- **NEVER** run `DROP TABLE`, `DELETE FROM`, `TRUNCATE`, or any destructive database operation.
- **NEVER** run `git push --force`, `git reset --hard`, or any command that rewrites history.
- **NEVER** run `npm publish`, `docker rm`, `terraform destroy`, or any irreversible deployment/infrastructure command.
- **NEVER** pipe remote scripts to shell (`curl | bash`, `wget | sh`).
- **ALWAYS** ask the user before running commands that modify system state, install packages, or make network requests.
- When in doubt, **show the command first** and wait for approval.

**Stack:** JavaScript/Python · Tailwind

## Important Warnings

- **⚠️ GOTCHA: Added API key auth authentication** — - > 232 notes | Score threshold: >40
+ > 233 notes | Score threshold: 
- **⚠️ GOTCHA: Added API key auth authentication** — - > 231 notes | Score threshold: >40
+ > 232 notes | Score threshold: 
- **gotcha in agent-rules.md** — File updated (external): .brainsync/agent-rules.md

Content summary (6

## Project Standards

- Added API key auth authentication — confirmed 3x
- what-changed in brainsync_auto.md — confirmed 3x
- what-changed in brainsync_auto.md — confirmed 3x
- Added API key auth authentication — confirmed 4x
- what-changed in shared-context.json — confirmed 3x
- Updated API endpoint Fixed — ensures atomic multi-step database operations — confirmed 3x
- what-changed in 0c44c772d4b26bf390351d1f22323d9d.php — confirmed 3x
- what-changed in brainsync_auto.md — confirmed 5x

## Known Fixes

- ❌ - - Fixed null crash in Bagian — prevents null/undefined runtime crashes → ✅ problem-fix in agent-rules.md
- ❌ + - Fixed null crash in Bagian — prevents null/undefined runtime crashes → ✅ problem-fix in agent-rules.md

## Learned Patterns

- Always: what-changed in brainsync_auto.md — confirmed 3x (seen 2x)
- Always: what-changed in brainsync_auto.md — confirmed 3x (seen 3x)
- Always: what-changed in brainsync_auto.md — confirmed 3x (seen 4x)
- Agent generates new migration for every change (squash related changes)
- Agent installs packages without checking if already installed

### 📚 Core Framework Rules: [tinybirdco/tinybird-python-sdk-guidelines]
# Tinybird Python SDK Guidelines

Guidance for using the `tinybird-sdk` package to define Tinybird resources in Python.

## When to Apply

- Installing or configuring tinybird-sdk
- Defining datasources, pipes, or endpoints in Python
- Creating Tinybird clients in Python
- Using data ingestion or queries in Python
- Running tinybird dev/build/deploy commands for Python projects
- Migrating from legacy .datasource/.pipe files to Python
- Defining connections (Kafka, S3, GCS)
- Creating materialized views, copy pipes, or sink pipes

## Rule Files

- `rules/getting-started.md`
- `rules/configuration.md`
- `rules/defining-datasources.md`
- `rules/defining-endpoints.md`
- `rules/client.md`
- `rules/low-level-api.md`
- `rules/cli-commands.md`
- `rules/connections.md`
- `rules/materialized-views.md`
- `rules/copy-sink-pipes.md`
- `rules/tokens.md`

## Quick Reference

- Install: `pip install tinybird-sdk`
- Initialize: `tinybird init`
- Dev mode: `tinybird dev` (uses configured `dev_mode`, typically branch)
- Build: `tinybird build` (builds against configured dev target)
- Deploy: `tinybird deploy` (deploys to main/production)
- Preview in CI: `tinybird preview`
- Migrate: `tinybird migrate` (convert .datasource/.pipe files to Python)
- Server-side only; never expose tokens in browsers


### 📚 Core Framework Rules: [expo/expo-tailwind-setup]
# Tailwind CSS Setup for Expo with react-native-css

This guide covers setting up Tailwind CSS v4 in Expo using react-native-css and NativeWind v5 for universal styling across iOS, Android, and Web.

## Overview

This setup uses:

- **Tailwind CSS v4** - Modern CSS-first configuration
- **react-native-css** - CSS runtime for React Native
- **NativeWind v5** - Metro transformer for Tailwind in React Native
- **@tailwindcss/postcss** - PostCSS plugin for Tailwind v4

## Installation



Add resolutions for lightningcss compatibility:



- autoprefixer is not needed in Expo because of lightningcss
- postcss is included in expo by default

## Configuration Files

### Metro Config

Create or update `metro.config.js`:



### PostCSS Config

Create `postcss.config.mjs`:



### Global CSS

Create `src/global.css`:



## IMPORTANT: No Babel Config Needed

With Tailwind v4 and NativeWind v5, you do NOT need a babel.config.js for Tailwind. Remove any NativeWind babel presets if present:



## CSS Component Wrappers

Since react-native-css requires explicit CSS element wrapping, create reusable components:

### Main Components (`src/tw/index.tsx`)



### Image Component (`src/tw/image.tsx`)



### Animated Components (`src/tw/animated.tsx`)



## Usage

Import CSS-wrapped components from your tw directory:



## Custom Theme Variables

Add custom theme variables in your global.css using `@theme`:



## Platform-Specific Styles

Use platform media queries for platform-specific styling:



## Apple System Colors with CSS Variables

Create a CSS file for Apple semantic colors:



Then use in components:



## Using CSS Variables in JavaScript

Use the `useCSSVariable` hook:



## Key Differences from NativeWind v4 / Tailwind v3

1. **No babel.config.js** - Configuration is now CSS-first
2. **PostCSS plugin** - Uses `@tailwindcss/postcss` instead of `tailwindcss`
3. **CSS imports** - Use `@import "tailwindcss/..."` instead of `@tailwind` directives
4. **Theme config** - Use `@theme` in CSS instead of `tailwind.config.js`
5. **Component wrappers** - Must wrap components with `useCssElement` for className support
6. **Metro config** - Use `withNativewind` with different options (`inlineVariables: false`)

## Troubleshooting

### Styles not applying

1. Ensure you have the CSS file imported in your app entry
2. Check that components are wrapped with `useCssElement`
3. Verify Metro config has `withNativewind` applied

### Platform colors not working

1. Use `platformColor()` i...
(truncated)


### 📚 Core Framework Rules: [czlonkowski/n8n-code-python]
# Python Code Node (Beta)

Expert guidance for writing Python code in n8n Code nodes.

---

## ⚠️ Important: JavaScript First

**Recommendation**: Use **JavaScript for 95% of use cases**. Only use Python when:
- You need specific Python standard library functions
- You're significantly more comfortable with Python syntax
- You're doing data transformations better suited to Python

**Why JavaScript is preferred:**
- Full n8n helper functions ($helpers.httpRequest, etc.)
- Luxon DateTime library for advanced date/time operations
- No external library limitations
- Better n8n documentation and community support

---

## Quick Start



### Essential Rules

1. **Consider JavaScript first** - Use Python only when necessary
2. **Access data**: `_input.all()`, `_input.first()`, or `_input.item`
3. **CRITICAL**: Must return `[{"json": {...}}]` format
4. **CRITICAL**: Webhook data is under `_json["body"]` (not `_json` directly)
5. **CRITICAL LIMITATION**: **No external libraries** (no requests, pandas, numpy)
6. **Standard library only**: json, datetime, re, base64, hashlib, urllib.parse, math, random, statistics

---

## Mode Selection Guide

Same as JavaScript - choose based on your use case:

### Run Once for All Items (Recommended - Default)

**Use this mode for:** 95% of use cases

- **How it works**: Code executes **once** regardless of input count
- **Data access**: `_input.all()` or `_items` array (Native mode)
- **Best for**: Aggregation, filtering, batch processing, transformations
- **Performance**: Faster for multiple items (single execution)



### Run Once for Each Item

**Use this mode for:** Specialized cases only

- **How it works**: Code executes **separately** for each input item
- **Data access**: `_input.item` or `_item` (Native mode)
- **Best for**: Item-specific logic, independent operations, per-item validation
- **Performance**: Slower for large datasets (multiple executions)



---

## Python Modes: Beta vs Native

n8n offers two Python execution modes:

### Python (Beta) - Recommended
- **Use**: `_input`, `_json`, `_node` helper syntax
- **Best for**: Most Python use cases
- **Helpers available**: `_now`, `_today`, `_jmespath()`
- **Import**: `from datetime import datetime`



### Python (Native) (Beta)
- **Use**: `_items`, `_item` variables only
- **No helpers**: No `_input`, `_now`, etc.
- **More limited**: Standard Python only
- **Use when**: Need pure Python without n8n helpers



**Recommendation**: Use **Python (Beta)** for better n8...
(truncated)

- [JavaScript/TypeScript] Use === not == (strict equality prevents type coercion bugs)

## Available Tools (ON-DEMAND only)
- `sys_core_01(q)` — Deep search when stuck
- `sys_core_05(query)` — Full-text lookup
> Context above IS your context. Do NOT call sys_core_14() at startup.
