# Laravel Boost + Gemini CLI — Setup Flow

This guide walks you through installing **Laravel Boost** in your Ai-Agent project and connecting it to **Gemini CLI** so you can use AI-assisted Laravel development with Google’s Gemini.

---

## Prerequisites

- **Laravel 10+** (you have Laravel 12 ✓)
- **PHP 8.1+** (you have ^8.2 ✓) with common extensions: `intl`, `zip` (Filament needs these)
- **Composer**
- **Node.js v20+** (for Gemini CLI)
- **Gemini API key** (from [Google AI Studio](https://aistudio.google.com/apikey) or Vertex AI)

**Ubuntu/Debian:** If Composer reports missing PHP extensions, install them, e.g.:
```bash
sudo apt install php8.4-intl php8.4-zip
```
If `php8.4-zip` fails with "Depends: libzip4t64 ... not installable" (e.g. on Ubuntu 24.10 "questing" where libzip4t64 may be unavailable), you can either install libzip from Universe on 24.04 (`sudo apt install libzip4t64` then `php8.4-zip`), or **temporarily ignore the zip requirement** to install Boost only:
```bash
composer require laravel/boost --dev --ignore-platform-req=ext-zip
```
Your app and Filament will still need `ext-zip` for features like Excel/CSV export; install `php8.4-zip` when your distro provides it or use a different PHP source.

---

## Flow Overview

```
1. Install Gemini CLI (global)
2. Install Laravel Boost in project (Composer)
3. Run boost:install (generates MCP + guidelines)
4. Connect Gemini CLI to Laravel Boost (auto or manual)
5. Verify with /mcp list
```

---

## Step 1 — Install Gemini CLI

Install Gemini CLI globally so you can use it from any directory.

**On Linux, if you get `EACCES: permission denied`**, use a user-owned directory instead of `sudo`:

```bash
mkdir -p ~/.npm-global
npm config set prefix "$HOME/.npm-global"
echo 'export PATH="$HOME/.npm-global/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

Then install:

```bash
npm install -g @google/gemini-cli
```

**If you have write access to `/usr/local`** (or use sudo):

```bash
npm install -g @google/gemini-cli
```

**Alternative (no global install):**

```bash
npx @google/gemini-cli
```

**Requirements:** Node.js v20 or higher.

**First-time setup:** When you run `gemini` for the first time, you’ll be prompted to sign in or add an API key. Use your Google account or set `GEMINI_API_KEY` in your environment.

---

## Step 2 — Install Laravel Boost in Your Project

From your project root (`Ai-Agent`):

```bash
cd /home/muhammad-hyder-ali/Desktop/Ai-Agent
composer require laravel/boost --dev
```

This adds Laravel Boost as a dev dependency (not used in production).

---

## Step 3 — Run Boost Install (MCP + Guidelines)

Generate the MCP server config and AI guidelines:

```bash
php artisan boost:install
```

- The command may run in **interactive** mode and ask which agents/features you want.
- Select **Gemini CLI** (and any other agents you use, e.g. Cursor).
- Choose features you want, e.g.:
  - **MCP server** (required for Gemini)
  - **AI guidelines**
  - **Agent skills** (optional)

**Non-interactive example:**

```bash
php artisan boost:install --agents=gemini-cli,cursor --features=mcp_server,ai_guidelines --mcp-clients=gemini-cli,cursor
```

(Adjust `--agents` and `--mcp-clients` to match what your Boost version supports; check `php artisan boost:install --help`.)

---

## Step 4 — Connect Gemini CLI to Laravel Boost

Laravel Boost can enable Gemini automatically. If it doesn’t, use one of these:

### Option A — Let Boost do it (if supported)

If `boost:install` created Gemini config, open Gemini CLI from the **project directory** and it may already see Laravel Boost.

### Option B — Register MCP via Gemini CLI (recommended)

From your **project root** (`Ai-Agent`):

```bash
cd /home/muhammad-hyder-ali/Desktop/Ai-Agent
gemini mcp add -s project -t stdio laravel-boost php artisan boost:mcp
```

This registers the Laravel Boost MCP server for this project.

### Option C — Manual config

Create the Gemini config directory and file:

```bash
mkdir -p .gemini
```

Create or edit `.gemini/settings.json`:

```json
{
  "mcpServers": {
    "laravel-boost": {
      "command": "php",
      "args": ["artisan", "boost:mcp"]
    }
  }
}
```

**Important:** Run Gemini CLI from the project root so `php artisan boost:mcp` runs in the right directory.

---

## Step 5 — Verify Integration

1. Open a terminal in the project root:
   ```bash
   cd /home/muhammad-hyder-ali/Desktop/Ai-Agent
   ```

2. Start Gemini CLI:
   ```bash
   gemini
   ```

3. In the Gemini CLI prompt, run:
   ```
   /mcp list
   ```

You should see `laravel-boost` and its tools (e.g. Application Info, Database Query, List Routes, Search Docs, Tinker, etc.).

---

## What You Get

- **MCP tools:** Database queries, routes, config, logs, Tinker, Artisan commands, Laravel docs search, etc.
- **AI guidelines:** Laravel (and optionally Filament, Pest, etc.) best practices loaded for the agent.
- **Documentation API:** 17,000+ Laravel docs pieces available via the “Search Docs” MCP tool.

---

## Keeping Boost Updated

Update Boost’s guidelines and skills after Composer updates:

```bash
php artisan boost:update
```

You can add this to `composer.json` under `scripts.post-update-cmd` so it runs after `composer update`.

---

## Optional — Cursor

You’re using Cursor; Boost can also configure Cursor’s MCP. When you run `boost:install`, enable Cursor so it gets `.cursor/mcp.json` (or similar). Then in Cursor: **Command Palette → “Open MCP Settings”** and turn on **laravel-boost**.

---

## Troubleshooting

| Issue | What to try |
|-------|---------------------|
| `gemini: command not found` | Ensure Node v20+ and run `npm install -g @google/gemini-cli` again; check `PATH`. |
| MCP server not in `/mcp list` | Run `gemini mcp add ...` from project root (Option B) or add `.gemini/settings.json` (Option C). |
| `php artisan boost:mcp` fails | Run from project root; run `composer install` and `php artisan boost:install`. |
| API / auth errors in Gemini | Set `GEMINI_API_KEY` or complete Gemini CLI sign-in. |

---

## Quick Reference Commands

```bash
# 1. Install Gemini CLI
npm install -g @google/gemini-cli

# 2. Install Boost
cd /home/muhammad-hyder-ali/Desktop/Ai-Agent
composer require laravel/boost --dev

# 3. Install MCP + guidelines
php artisan boost:install

# 4. Register with Gemini (from project root)
gemini mcp add -s project -t stdio laravel-boost php artisan boost:mcp

# 5. Use Gemini
gemini
# Then in Gemini: /mcp list
```

After this flow, you’re set to use Laravel Boost with Gemini CLI in your Ai-Agent project.
