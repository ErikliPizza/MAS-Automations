<script>
import { useToast } from 'vue-toastification';

// Initialize the toast instance
const toast = useToast();

// Define a function to handle clipboard operations
export function useClipboard() {
    // Function to copy a value to the clipboard
    const copyToClipboard = (value, title) => {
        // Get the translated text for "Copied to clipboard: "
        const bodyText = 'Copied to clipboard: ';

        // Create a temporary textarea to facilitate copying
        const textField = document.createElement("textarea");
        textField.innerText = value;
        document.body.appendChild(textField);
        textField.select();
        document.execCommand("copy");
        textField.remove();

        // Display a success toast notification
        toast.success(bodyText + title, { timeout: 1250, hideProgressBar: true });
    };

    // Return the copyToClipboard function
    return { copyToClipboard };
}
</script>
