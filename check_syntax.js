const fs = require('fs');
const c = fs.readFileSync('resources/views/usuario/monitoreo/modulos/infraestructura_3d.blade.php', 'utf8');
const s = c.indexOf('document.addEventListener');
const e = c.indexOf('</script>', s);
let j = c.substring(s, e);

// Replace Blade directives
j = j.replace(/@json\([^)]+\)/g, '[]');
j = j.replace(/\{\{[^}]+\}\}/g, '0');

const lines = j.split('\n');

// Find syntax error by binary search
let lo = 0, hi = lines.length;
while (lo < hi) {
    const mid = Math.floor((lo + hi) / 2);
    try {
        new Function(lines.slice(0, mid + 1).join('\n'));
        lo = mid + 1;
    } catch(ex) {
        if (ex instanceof SyntaxError) {
            hi = mid;
        } else {
            lo = mid + 1;
        }
    }
}

const errLine = lo;
console.log('First error around line: ' + errLine);
for (let i = Math.max(0, errLine - 5); i < Math.min(lines.length, errLine + 5); i++) {
    console.log((i + 1) + ': ' + lines[i]);
}
