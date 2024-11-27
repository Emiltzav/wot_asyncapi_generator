document.getElementById('codeGenerationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    // Get form data
    const topicName = document.getElementById('topicName').value;
    const messageFields = JSON.parse(document.getElementById('messageFields').value); // Parse JSON fields
    const operation = document.getElementById('operation').value;

    // Send form data to the backend using Fetch API
    fetch('/generate-code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            topicName: topicName,
            messageFields: messageFields,
            operation: operation
        })
    })
    .then(response => response.json())
    .then(data => {
        // Display the generated code
        const generatedCodeSection = document.getElementById('generatedCodeSection');
        const generatedCode = document.getElementById('generatedCode');
        
        generatedCodeSection.style.display = 'block';
        generatedCode.textContent = data.generatedCode;
    })
    .catch(error => console.error('Error:', error));
});