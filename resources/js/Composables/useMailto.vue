<script>
export function useMailto() {
    const mailto = (value) => `mailto:${value}`;

    // Return the shareWith and sendWith functions
    return { mailto };
}
</script>
