<script>
// Define a function to handle WhatsApp interactions
export function useWhatsapp() {
    // Function to share a value via WhatsApp
    const shareWith = (value) => {
        let whatsappUrl;
        // Check if the user agent contains "Mobi" indicating a mobile device
        const isMobile = /Mobi|Android/i.test(navigator.userAgent);

        if (isMobile) {
            // Construct WhatsApp URL for mobile devices
            whatsappUrl = `https://api.whatsapp.com/send?text=${value}`;
        } else {
            // Construct WhatsApp URL for non-mobile devices
            whatsappUrl = `https://web.whatsapp.com/send?text=${value}`;
        }

        // Open the WhatsApp URL in a new tab
        window.open(whatsappUrl, '_blank');
    };

    // Function to generate a WhatsApp link
    const sendWith = (value) => `https://wa.me/${value}`;

    // Return the shareWith and sendWith functions
    return { shareWith, sendWith };
}
</script>
