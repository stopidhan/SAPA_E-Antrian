const fs = require('fs');
let content = fs.readFileSync('resources/views/Pages/StaffOperatorLoket/Index.blade.php', 'utf8');

content = content.replace(/body:\s*JSON\.stringify\(\{\}\)/g, function(match, offset) {
    let beforeSnippet = content.substring(offset - 200, offset);
    if (beforeSnippet.includes('/selesai/')) {
        return "body: JSON.stringify({ category: this.serviceCategory, description: this.serviceDescription })";
    }
    if (beforeSnippet.includes('/panggil/') || beforeSnippet.includes('/layani/')) {
        return "body: JSON.stringify({ counter_id: this.counterId })";
    }
    return match;
});

fs.writeFileSync('resources/views/Pages/StaffOperatorLoket/Index.blade.php', content);
