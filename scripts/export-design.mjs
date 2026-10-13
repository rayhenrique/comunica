import { spawnSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';

const args = [
    '--yes', '-p', '@google/design.md', 'designmd', 'export', '--format', 'css-tailwind', 'DESIGN.md',
];
// Comando fixo, sem entradas do usuário; .cmd precisa do shell no Windows.
const result = process.platform === 'win32'
    ? spawnSync('npx.cmd --yes -p @google/design.md designmd export --format css-tailwind DESIGN.md', { encoding: 'utf8', shell: true })
    : spawnSync('npx', args, { encoding: 'utf8' });
if (result.status !== 0) {
    process.stderr.write(result.stderr || 'Não foi possível exportar os tokens.');
    process.exit(result.status || 1);
}
// Tokens também usados por CSS sem utilities precisam sobreviver à remoção de CSS não utilizado.
writeFileSync('resources/css/theme.generated.css', result.stdout.replace('@theme {', '@theme static {'), 'utf8');
