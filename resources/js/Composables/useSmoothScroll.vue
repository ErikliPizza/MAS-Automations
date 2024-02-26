<script>
import { onMounted, ref } from 'vue';

export function useSmoothScroll() {
    const scrollTarget = ref(null);

    const scrollToElement = () => {
        if (scrollTarget.value) {
            // Using setTimeout to ensure the DOM is updated
            setTimeout(() => {
                scrollTarget.value.scrollIntoView({ behavior: 'smooth' });
            }, 0);
        }
    };

    onMounted(scrollToElement);

    return {
        scrollTarget,
        scrollToElement,
    };
}
</script>
