import { createHash } from 'node:crypto';
import { readFileSync, readdirSync } from 'node:fs';
import { resolve, relative } from 'node:path';
import { spawnSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const projectRoot = fileURLToPath(new URL('../', import.meta.url));
export const normalize = value => value.replace(/\r\n/g, '\n');
export const digest = value => createHash('sha256').update(normalize(value)).digest('hex');

export function verifyDesign(root = projectRoot) {
    const errors = [];
    const baseline = JSON.parse(readFileSync(resolve(root, 'design-lock.json'), 'utf8'));
    for (const [path, expected] of Object.entries(baseline.files)) {
        try {
            if (digest(readFileSync(resolve(root, path), 'utf8')) !== expected) errors.push(`Fonte protegida alterada: ${path}`);
        } catch { errors.push(`Fonte protegida ausente: ${path}`); }
    }
    const inventory = directory => {
        for (const entry of readdirSync(resolve(root, directory), { withFileTypes: true })) {
            const path = `${directory}/${entry.name}`;
            if (entry.isDirectory()) inventory(path);
            else if (!Object.hasOwn(baseline.files, path)) errors.push(`Fonte visual sem baseline: ${path}`);
        }
    };
    for (const directory of ['resources/css', 'resources/js', 'resources/views']) inventory(directory);
    const pkg = JSON.parse(readFileSync(resolve(root, 'package.json'), 'utf8'));
    const lock = JSON.parse(readFileSync(resolve(root, 'package-lock.json'), 'utf8'));
    if (pkg.devDependencies['@google/design.md'] !== baseline.exporterVersion ||
        lock.packages['node_modules/@google/design.md']?.version !== baseline.exporterVersion) {
        errors.push('Versão do exportador diverge da versão aprovada.');
    }
    const cli = resolve(projectRoot, 'node_modules/@google/design.md/dist/index.js');
    const generated = spawnSync(process.execPath, [cli, 'export', '--format', 'css-tailwind', 'DESIGN.md'], {
        cwd: root, encoding: 'utf8', timeout: 30000,
    });
    if (generated.status !== 0) errors.push('Falha no exportador local; execute npm ci.');
    else {
        const expected = normalize(generated.stdout.replace('@theme {', '@theme static {')).trim();
        const current = normalize(readFileSync(resolve(root, 'resources/css/theme.generated.css'), 'utf8')).trim();
        if (expected !== current) errors.push('CSS gerado diverge de DESIGN.md; regenere pelo script.');
    }
    return errors;
}

if (process.argv[1] && resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
    try {
        const errors = verifyDesign(process.argv[2] ? resolve(process.argv[2]) : projectRoot);
        if (errors.length) { process.stderr.write(errors.join('\n') + '\n'); process.exitCode = 1; }
        else process.stdout.write('Identidade protegida e exportação sem drift.\n');
    } catch (error) { process.stderr.write(`Auditoria de design falhou: ${error.message}\n`); process.exitCode = 1; }
}
