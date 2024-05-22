import * as yup from 'yup';

export const userValidation = yup.object({
    name: yup.string().min(2).max(64).required(),
    surname: yup.string().min(2).max(64).required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8).max(64),
    phone: yup.string().nullable()
        .test('is-number', 'Must be a number', value => !value || /^\d+$/.test(value))
        .test('length-check', 'Must be between 3 & 15 digits', value => !value || (value.length >= 3 && value.length <= 15)),
    role: yup.string().required().matches(/^(additional|admin)$/, 'Choose a valid role'),
    modules: yup.array().required().min(1),
    title: yup.string().min(2).max(64).nullable(),
    birthday: yup.date().nullable(),
    id_number: yup.string().nullable().matches(/^\d{1,20}$/, 'ID number must be numeric and between 1 and 20 digits long'),
    bank_account: yup.string().min(6).max(255).nullable(),
    salary: yup.number().nullable().min(0).max(9999999.99),
    start_date_of_work: yup.date().nullable(),
    end_date_of_work: yup.date().nullable().when('start_date_of_work', ([startDate], schema) => {
        if (startDate) {
            return schema.min(startDate, 'End date must be later than start date');
        } else {
            return schema.nullable();
        }
    }),
    reason_of_leaving: yup.string().max(500).nullable()
});
