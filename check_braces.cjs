const fs = require('fs');
const content = fs.readFileSync('resources/views/usuario/monitoreo/modulos/infraestructura_3d.blade.php', 'utf8');

// Find the script section
const scriptStart = content.indexOf('<script>') + '<script>'.length;
const scriptEnd = content.indexOf('<\/script>', scriptStart);
const scriptContent = content.substring(scriptStart, scriptEnd);

const lines = scriptContent.split('\n');

// Track brace depth for the return object
// Find "return {" line
let returnLineIdx = -1;
for (let i = 0; i < lines.length; i++) {
    if (lines[i].trim() === 'return {') {
        returnLineIdx = i;
        break;
    }
}

if (returnLineIdx === -1) {
    console.log('Could not find "return {"');
    process.exit(1);
}

console.log('Found "return {" at script line: ' + (returnLineIdx + 1));

// Count braces from there
let depth = 0;
let inString = false;
let strChar = '';
let escaped = false;
let inLineComment = false;
let inBlockComment = false;

let closingLine = -1;

for (let li = returnLineIdx; li < lines.length; li++) {
    const line = lines[li];
    
    inLineComment = false;
    
    for (let ci = 0; ci < line.length; ci++) {
        const ch = line[ci];
        const next = ci + 1 < line.length ? line[ci+1] : '';
        
        if (inBlockComment) {
            if (ch === '*' && next === '/') {
                inBlockComment = false;
                ci++;
            }
            continue;
        }
        
        if (inLineComment) continue;
        
        if (inString) {
            if (escaped) {
                escaped = false;
                continue;
            }
            if (ch === '\\') {
                escaped = true;
                continue;
            }
            if (ch === strChar) {
                inString = false;
            }
            continue;
        }
        
        // Check for comments
        if (ch === '/' && next === '/') {
            inLineComment = true;
            break;
        }
        if (ch === '/' && next === '*') {
            inBlockComment = true;
            ci++;
            continue;
        }
        
        // Check for strings
        if (ch === '"' || ch === "'" || ch === '`') {
            inString = true;
            strChar = ch;
            continue;
        }
        
        if (ch === '{') {
            depth++;
        } else if (ch === '}') {
            depth--;
            if (depth === 0) {
                closingLine = li;
                break;
            }
        }
    }
    
    // Show status every 100 lines for long objects
    if (li === returnLineIdx || (li - returnLineIdx) % 500 === 0) {
        console.log('  At script line ' + (li + 1) + ': depth=' + depth);
    }
    
    if (closingLine !== -1) break;
}

if (closingLine !== -1) {
    console.log('\nClosing "}" found at script line: ' + (closingLine + 1));
    // Show 5 lines before and after
    for (let i = Math.max(0, closingLine - 5); i < Math.min(lines.length, closingLine + 10); i++) {
        console.log((i + 1) + ': ' + lines[i].substring(0, 100));
    }
} else {
    console.log('\nNo closing "}" found! Object not properly closed.');
    console.log('Final depth: ' + depth);
    console.log('Last 10 lines checked:');
    for (let i = Math.max(0, lines.length - 20); i < lines.length; i++) {
        console.log((i+1) + ': ' + lines[i].substring(0, 100));
    }
}
