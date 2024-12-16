// Function to load financial goals from the database
async function loadFinancialGoals() {
    try {
        const response = await fetch('fetch_finance_goals.php');
        const data = await response.json();

        if (data.status === 'success') {
            const financialGoals = data.data;
            const goalsSection = document.querySelector('#financial-goals-section');

            // Clear existing content
            goalsSection.innerHTML = `<h3>Your Financial Goals</h3><div class="goals-container"></div>`;

            // Append financial goals to the section
            const goalsContainer = goalsSection.querySelector('.goals-container');
            financialGoals.forEach(goal => {
                const percentage = Math.min(Math.max(goal.current_amount / goal.target_amount, 0), 1) * 100;
                const goalHtml = `
                    <div class="goal">
                        <p>${percentage.toFixed(1)}% ${goal.goal_name}</p>
                        <p>$ Target: $${goal.target_amount.toLocaleString()}</p>
                        <p>📅 By ${goal.target_date}</p>
                    </div>
                `;
                goalsContainer.innerHTML += goalHtml;
            });
        } else {
            console.error('Error fetching financial goals:', data.message);
        }
    } catch (error) {
        console.error('Error loading financial goals:', error);
    }
}

// Function to capture Financial Goals section and share on Facebook
async function shareFinancialGoalsOnFacebook() {
    const clientId = '1819a1835a7c8ee'; // Replace with your Imgur Client ID
    const financialGoalsElement = document.querySelector('#financial-goals-section');

    if (!financialGoalsElement) {
        console.error('Financial Goals section not found.');
        return;
    }

    // Capture Financial Goals section as an image
    const canvas = await html2canvas(financialGoalsElement, { scale: 2 });
    const imageData = canvas.toDataURL('image/png');
    const base64Image = imageData.split(',')[1]; // Extract base64 part

    // Upload the image to Imgur
    try {
        const formData = new FormData();
        formData.append('image', base64Image);

        const response = await fetch('https://api.imgur.com/3/image', {
            method: 'POST',
            headers: {
                Authorization: `Client-ID ${clientId}`,
            },
            body: formData,
        });

        const result = await response.json();

        if (result.success) {
            console.log('Image uploaded successfully:', result.data.link);

            // Open Facebook Share Dialog with the uploaded image URL
            const facebookShareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(result.data.link)}`;
            window.open(facebookShareUrl, '_blank');
        } else {
            console.error('Image upload failed:', result.data.error);
        }
    } catch (error) {
        console.error('Error uploading image:', error);
    }
}

// Load financial goals on page load
document.addEventListener('DOMContentLoaded', () => {
    loadFinancialGoals();

    // Add click event to Share button
    document.querySelector('#share-btn').addEventListener('click', shareFinancialGoalsOnFacebook);
});
