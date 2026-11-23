# Realtime updates

The student, teacher, staff, DOS, accountant, and secretary portals listen for private `system.updates` WebSocket events. Changes to core academic, attendance, fee, timetable, and account models publish an update signal after the database transaction commits. The signal contains no student or record data. Open pages update automatically unless a form has unsaved edits; in that case, the page offers a refresh action to protect the draft.

## Production setup

Configure these values in the server environment (do not commit secrets):

- `BROADCAST_CONNECTION=reverb`
- `REVERB_APP_ID`, `REVERB_APP_KEY`, and `REVERB_APP_SECRET` with unique generated values
- `REVERB_SERVER_HOST=0.0.0.0` and `REVERB_SERVER_PORT=8080` for the WebSocket server
- `REVERB_HOST=127.0.0.1` and `REVERB_PORT=8080` for Laravel's connection to Reverb
- `REVERB_SCHEME=http` for a local reverse-proxy connection, or `https` when the PHP app connects directly over TLS
- `REVERB_ALLOWED_ORIGINS` with the public portal hostname(s), comma-separated
- `VITE_REVERB_APP_KEY`, `VITE_REVERB_HOST`, `VITE_REVERB_PORT`, and `VITE_REVERB_SCHEME` for the browser-facing WebSocket endpoint

The public Vite values must be present when building the frontend. In production, expose Reverb through a TLS-enabled reverse proxy and set the browser scheme to `https` (WSS). Keep the app secret server-side.

After deployment, install dependencies, build assets, clear Laravel's config cache, and run `php artisan reverb:start` under a process manager such as Supervisor so the WebSocket server restarts if it exits. No queue worker is required for these change signals because they are broadcast synchronously after commit.
