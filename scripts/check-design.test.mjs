import { test } from 'node:test';
import assert from 'node:assert/strict';
import { mkdtempSync, readFileSync, writeFileSync, mkdirSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { verifyDesign } from './check-design.mjs';

const source = fileURLToPath(new URL('../', import.meta.url));
const baseline = JSON.parse(readFileSync(join(source, 'design-lock.json'), 'utf8'));
for (const [name, path, mutate] of [
    ['cor canônica', 'DESIGN.md', s => s.replace('#17665B', '#000000')],
    ['fonte canônica', 'DESIGN.md', s => s.replace('Segoe UI', 'Outra Fonte')],
    ['raio canônico', 'DESIGN.md', s => s.replace('0.75rem', '2rem')],
    ['override no CSS', 'resources/css/app.css', s => s + '\n:root { --color-primary: red; }'],
    ['edição direta no gerado', 'resources/css/theme.generated.css', s => s.replace(/#17665b/i, '#000000')],
    ['novo arquivo visual', 'resources/css/override.css', () => 'body { color: red; }'],
]) {
    test(`rejeita ${name} sem atualização revisada da baseline`, () => {
        const root = mkdtempSync(join(tmpdir(), 'comunica-design-'));
        try {
            for (const file of [...Object.keys(baseline.files), 'design-lock.json', 'package.json', 'package-lock.json']) {
                mkdirSync(dirname(join(root, file)), { recursive: true });
                writeFileSync(join(root, file), readFileSync(join(source, file)));
            }
            const original = name === 'novo arquivo visual' ? '' : readFileSync(join(root, path), 'utf8');
            const altered = mutate(original);
            assert.notEqual(altered, original, 'O cenário negativo precisa alterar o arquivo.');
            writeFileSync(join(root, path), altered);
            assert.ok(verifyDesign(root).some(error => error.includes(path) || error.includes('CSS gerado')));
        } finally {
            if (dirname(resolve(root)) !== resolve(tmpdir()) || !root.startsWith(join(tmpdir(), 'comunica-design-'))) {
                throw new Error('Diretório temporário fora do escopo de limpeza.');
            }
            rmSync(root, { recursive: true, force: true });
        }
    });
}

test('aceita o conjunto aprovado e a exportação reproduzível', () => assert.deepEqual(verifyDesign(source), []));
