import { spawnSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

const cli = fileURLToPath(new URL('../node_modules/@google/design.md/dist/index.js', import.meta.url));
const result = spawnSync(process.execPath, [cli, 'export', '--format', 'css-tailwind', 'DESIGN.md'], { encoding: 'utf8' });
if (result.status !== 0) {
    process.stderr.write(result.stderr || 'Não foi possível exportar os tokens.');
    process.exit(result.status || 1);
}
// Tokens também usados por CSS sem utilities precisam sobreviver à remoção de CSS não utilizado.
writeFileSync('resources/css/theme.generated.css', result.stdout.replace('@theme {', '@theme static {'), 'utf8');
