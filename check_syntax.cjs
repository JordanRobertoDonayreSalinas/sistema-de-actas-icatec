const fs = require('fs');
const c = fs.readFileSync('resources/views/usuario/monitoreo/modulos/infraestructura_3d.blade.php', 'utf8');
const s = c.indexOf('document.addEventListener');
const e = c.indexOf('<\/script>', s);
let j = c.substring(s, e);

// Replace Blade directives
j = j.replace(/@json\([^)]+\)/g, '[]');
j = j.replace(/\{\{[^}]+\}\}/g, '0');

const lines = j.split('\n');

let errorLine = -1;
// Try adding one line at a time
for (let i = 1; i <= lines.length; i++) {
    try {
        new Function(lines.slice(0, i).join('\n'));
    } catch(ex) {
        if (ex instanceof SyntaxError && errorLine === -1) {
            errorLine = i;
        }
    }
}

if (errorLine === -1) {
    console.log('No syntax errors found!');
} else {
    console.log('Error near extracted line: ' + errorLine);
    for (let i = Math.max(0, errorLine - 5); i < Math.min(lines.length, errorLine + 3); i++) {
        console.log((i + 1) + ': [' + lines[i].substring(0, 120) + ']');
    }
}
