const dateTimeToTime = (dateTime) => {
    const timePart = dateTime.split(' ')[1];
    const [hours, minutes] = timePart.split(':');
    return `${hours}:${minutes}`;
};

const minutesToHHMMSS = (minutes) => {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:00`;
};

const timeToMinutes = (time) => {
    const [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
};

const dateTimeToMinutes = (dateTime) => {
    const timePart = dateTime.split(' ')[1];
    return timeToMinutes(timePart.substring(0, 5));
};

const timestampToHHMM = (timestamp) => {
    const [hours, minutes] = timestamp.split(':');
    return `${hours}:${minutes}`;
};

export { dateTimeToTime, minutesToHHMMSS, timeToMinutes, dateTimeToMinutes, timestampToHHMM };
