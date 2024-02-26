<script>
import { ref, computed } from 'vue';

// Composable function
export function useTimeSlots(appointments) {
    // Reactive state for disabled times
    const disabledTimes = ref([]);

    // Function to generate 15-minute time slots between two times
    function generateTimeSlots(startTime, endTime) {
        const start = new Date(startTime);
        const end = new Date(endTime);

        let current = new Date(start);
        const slots = [];

        while (current < end) {
            const timeString = current.toTimeString().substring(0, 5);
            slots.push(timeString);

            current = new Date(current.getTime() + 15 * 60000);
        }

        return slots;
    }

    // Function to accumulate disabled time slots for each appointment
    function calculateDisabledTimes() {
        appointments.forEach(appointment => {
            const slots = generateTimeSlots(appointment.start_time, appointment.end_time);
            disabledTimes.value.push(...slots);
        });

        // Remove duplicates if any appointment times overlap
        disabledTimes.value = [...new Set(disabledTimes.value)];
    }

    // Computed property for unique disabled times
    const uniqueDisabledTimes = computed(() => disabledTimes.value);

    // Initialize or recalculate disabled times if appointments change
    calculateDisabledTimes();

    // Expose data and methods
    return { uniqueDisabledTimes, generateTimeSlots };
}
</script>
