<script>
import { ref, computed } from 'vue';

export function useTimePicker(inUseHours, openingTime, closingTime) {
    const selectedStartTime = ref(null);
    const selectedEndTime = ref(null);
    // Conversion function (defined within your script)
    const minutesToHHMMSS = (minutes) => {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:00`;
    };
    // Converts the "HH:MM" format to minutes since start of the day
    const timeToMinutes = (time) => {
        const [hours, minutes] = time.split(':').map(Number);
        return hours * 60 + minutes;
    };

    // Converts "YYYY-MM-DD HH:MM:SS" to minutes since start of the day
    const dateTimeToMinutes = (dateTime) => {
        const timePart = dateTime.split(' ')[1];
        return timeToMinutes(timePart.substring(0, 5));
    };

    // Checks if a given range overlaps with any in-use hours
    const doesRangeOverlap = (start, end) => {
        return inUseHours.value.some(({ start_time, end_time }) => {
            const startTimeMinutes = dateTimeToMinutes(start_time);
            const endTimeMinutes = dateTimeToMinutes(end_time);
            return (start < endTimeMinutes && end > startTimeMinutes);
        });
    };

    const timeSlots = computed(() => {
        const slots = [];
        const inUseNumbers = new Map(); // Mapping of inUseHours range to unique numbers
        const shuffledNumbers = Array.from({ length: 83 }, (_, i) => i + 1).sort(() => Math.random() - 0.5);
        let numberIndex = 0; // Index for assigning unique numbers

        // Pre-assign unique numbers to each inUseHours range
        inUseHours.value.forEach(({ start_time, end_time }) => {
            const rangeKey = `${start_time}-${end_time}`;
            if (!inUseNumbers.has(rangeKey)) {
                inUseNumbers.set(rangeKey, shuffledNumbers[numberIndex++ % shuffledNumbers.length]);
            }
        });

        for (
            let time = openingTime.value.hours * 60 + openingTime.value.minutes;
            time <= closingTime.value.hours * 60 + closingTime.value.minutes;
            time += 15
        ) {
            const hours = Math.floor(time / 60);
            const minutes = time % 60;
            let slotSelectable = true;
            let inUseInfos = []; // Array to hold multiple inUseInfo objects
            let images = []; // Now an array to hold multiple image numbers
            let exclusiveStart = false;
            let exclusiveEnd = false;

            inUseHours.value.forEach(({ start_time, end_time, name, phone }) => {
                const startTimeMinutes = dateTimeToMinutes(start_time);
                const endTimeMinutes = dateTimeToMinutes(end_time);
                const rangeKey = `${start_time}-${end_time}`;


                if (time >= startTimeMinutes && time <= endTimeMinutes) {
                    if (time > startTimeMinutes && time < endTimeMinutes) {
                        slotSelectable = false;
                        inUseInfos.push({ name, phone, type: 'ongoing' }); // Mark as ongoing appointment
                        images.push(inUseNumbers.get(rangeKey)); // Add image number for ongoing appointment
                    }
                }


                if (time === endTimeMinutes) {
                    slotSelectable = (closingTime.value.hours * 60 + closingTime.value.minutes) !== time;
                    exclusiveStart = true;
                    inUseInfos.push({ name, phone, type: 'exclusiveStart' });
                    images.push(inUseNumbers.get(rangeKey)); // Add image number for the exclusive start
                }
                if (time === startTimeMinutes) {
                    exclusiveEnd = true;
                    inUseInfos.push({ name, phone, type: 'exclusiveEnd' });
                    images.push(inUseNumbers.get(rangeKey)); // Add image number for the exclusive end
                    slotSelectable = (openingTime.value.hours * 60 + openingTime.value.minutes) !== time;
                }
                if (exclusiveEnd && exclusiveStart) {
                    slotSelectable = false;
                }

            });

            slots.push({
                display: `${hours}:${minutes.toString().padStart(2, '0')}`,
                value: time,
                selectable: slotSelectable,
                inUseInfos: inUseInfos,
                exclusiveStart: exclusiveStart,
                exclusiveEnd: exclusiveEnd,
                images: images, // Changed to support multiple image numbers
            });
        }
        return slots;
    });

    const selectTime = (slot) => {
        // Check if the slot is selectable (not overlapping with unavailable times)
        if (!slot.selectable) return;

        // New condition: Clear the selection if the selected start time is clicked again
        if (selectedStartTime.value === slot.value || selectedEndTime.value === slot.value) {
            selectedStartTime.value = null;
            selectedEndTime.value = null;
            return; // Exit the function early
        }


        // Existing selection logic...
        if (!selectedStartTime.value || selectedEndTime.value || slot.value < selectedStartTime.value) {
            if (slot.exclusiveEnd) {
                return;
            } else {
                selectedStartTime.value = slot.value;
                selectedEndTime.value = null;
            }
        } else {
            // If a start time is already selected, and the chosen slot is later than the start time,
            // check if this would result in an overlap with unavailable hours.
            if (slot.value > selectedStartTime.value) {
                if (!doesRangeOverlap(selectedStartTime.value, slot.value)) {
                    // If there's no overlap, set this as the end time.
                    selectedEndTime.value = slot.value;
                } else {
                    // If selecting this as an end time would overlap with unavailable hours,
                    // reset and set this as the new start time instead.
                    selectedStartTime.value = slot.value;
                    selectedEndTime.value = null;
                }
            }
        }
    };


    const isSelected = (slot) => slot.value === selectedStartTime.value ? 'start' : slot.value === selectedEndTime.value ? 'end' : '';
    const isInRange = (slot) => selectedStartTime.value && selectedEndTime.value && slot.value > selectedStartTime.value && slot.value < selectedEndTime.value;

    return { selectedStartTime, selectedEndTime, timeSlots, selectTime, isSelected, isInRange };
}
</script>

