import {useDate} from "vuetify";


const useDateTranslator = () => {
    const adapter = useDate()

    const toYMD = (dateString) => {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    const toHumanReadable = (dateString, formatString) => {
        return adapter.format(new Date(dateString), formatString);
    }

    return { toYMD, toHumanReadable };
};

export default useDateTranslator;
