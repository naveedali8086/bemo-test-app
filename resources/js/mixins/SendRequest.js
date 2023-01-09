import {getParamValFromUrl} from "./helpers";

export default {

    methods: {

        sendRequest(url, method, always_callback, success_callback, error_callback) {

            // POST or PUT request has form data but not the "Delete" Request
            // Clear form fields before sending request
            if (this.form) {
                this.clearErrorsFromFields(this.form.errors);
            }

            let form_data = new FormData;

            if (this.form) {
                for (const key in this.form.fields) {
                    form_data.append(key, this.form.fields[key]);
                }
            }

            form_data.append('_method', method); // Required By Laravel
            form_data.append('api_token', getParamValFromUrl('token'));

            axios.post(url, form_data)
                .then((response) => {

                    if (response.data.has_err) {
                        alert(response.data.message);

                    } else {
                        // reset form in case of creating a resource but data should remain in form if the case is 'edit'
                        if (this.form && method.toUpperCase() === 'PUT') {
                            this.clearFormFields()
                        }

                        if (success_callback && typeof success_callback === 'function') {
                            success_callback(response);
                        }

                        alert(response.data.message)
                    }

                })
                .catch((error) => {

                    let response_err = error.response;

                    if (response_err) { // status code that falls out of the range of 2xx

                        if (response_err.status == 422) {
                            const errors = response_err.data.errors
                            if (this.form) {
                                // populate errors field-wise
                                for (const field_name in errors) {
                                    this.form.errors[field_name] = errors[field_name][0];
                                }
                                alert('Please fix errors in form');

                            } else {
                                // concatenating errors
                                let all_errors = '';
                                for (const field_name in errors) {
                                    all_errors += `${errors[field_name][0]}<br>`;
                                }
                                alert(all_errors);
                            }
                        } else {
                            alert(response_err.data.message);
                        }

                    } else {
                        alert(error);
                    }

                    if (error_callback && typeof error_callback === 'function') {
                        error_callback(error);
                    }

                })
                .then(() => {
                    if (always_callback && typeof always_callback === 'function') {
                        always_callback();
                    }
                });
        },

        clearErrorsFromFields() {
            const errors = this.form.errors;
            for (const attribute in errors) {
                if (!Array.isArray(errors[attribute])) {
                    errors[attribute] = '';
                }
            }
        },

        clearFormFields() {
            const form = this.form.fields;
            for (const field in form) {
                if (!Array.isArray(form[field])) {
                    form[field] = '';
                }
            }
        }

    }

}
