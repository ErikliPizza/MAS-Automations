<script>
// Import the useClipboard composable
import { useClipboard } from "@/Composables/useClipboard.vue";

// Destructure the copyToClipboard function from the useClipboard composable
const { copyToClipboard } = useClipboard();

// Define a function that adds a contact
export function useContact() {
    // Function to add a contact based on user agent
    const callNumber = (value) => {
        // Check if the user agent contains "Mobi" indicating a mobile device
        const isMobile = /Mobi|Android/i.test(navigator.userAgent);

        if (isMobile) {
            // Construct the URL with the contact number
            const contactUrl = `tel:${value}`;
            // Open the default contact application
            window.open(contactUrl, '_blank');
        } else {
            // Copy the value to clipboard
            copyToClipboard(value, value);
        }
    };

    // This function generates a VCard (contact card) in the form of a .vcf file and triggers its download.
    const addContact = (name, number, email = null) => {
        let emailField = "";
        if (email !== null) {
            emailField = `EMAIL;TYPE=:${email}\r\n`
        }
        // Create the VCard data string.
        const vCardData = `BEGIN:VCARD\r\n
VERSION:3.0\r\n
FN:${name}\r\n
TEL;TYPE=:${number}\r\n
${emailField}
END:VCARD`;

        // Create a Blob containing the VCard data.
        const blob = new Blob([vCardData], { type: 'text/vcard' });

        // Create a URL for the Blob.
        const url = window.URL.createObjectURL(blob);

        // Create a hidden 'a' element for downloading the VCard.
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.setAttribute('download', name + '.vcf');

        // Append the 'a' element to the document body.
        document.body.appendChild(a);

        // Trigger a click event on the 'a' element to initiate the download.
        a.click();

        // Revoke the URL to release resources.
        window.URL.revokeObjectURL(url);
    };


    // Return the addToContact function
    return { callNumber, addContact };
}
</script>
